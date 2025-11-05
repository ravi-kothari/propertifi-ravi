<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Models\UserPreferences;
use App\Models\UserLeads;
use App\Models\LeadFeedback;
use App\Jobs\DistributeLeadToLowerTiers;
use App\Services\LeadScoringService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadDistributionService
{
    protected LeadScoringService $scoringService;

    public function __construct()
    {
        $this->scoringService = new LeadScoringService();
    }
    /**
     * Find matching property managers for a lead based on preferences.
     *
     * @param Lead $lead
     * @param int $limit Maximum number of PMs to match
     * @param bool $respectExclusivity Whether to enforce exclusivity hours
     * @return \Illuminate\Support\Collection
     */
    public function findMatchingPropertyManagers(Lead $lead, $limit = 5, $respectExclusivity = true)
    {
        $query = UserPreferences::active()
            ->with(['user', 'tier']);

        // Filter by property type
        if ($lead->property_type) {
            $query->where(function ($q) use ($lead) {
                $q->whereJsonContains('property_types', $lead->property_type)
                  ->orWhereNull('property_types');
            });
        }

        // Filter by number of units
        if ($lead->number_of_units) {
            $query->where(function ($q) use ($lead) {
                $q->where(function ($subQ) use ($lead) {
                    $subQ->where('min_units', '<=', $lead->number_of_units)
                         ->where('max_units', '>=', $lead->number_of_units);
                })
                ->orWhere(function ($subQ) {
                    $subQ->whereNull('min_units')
                         ->whereNull('max_units');
                });
            });
        }

        // Filter by zip code (exact match or service radius)
        if ($lead->zipcode) {
            $query->where(function ($q) use ($lead) {
                // Exact zip code match
                $q->whereJsonContains('zip_codes', $lead->zipcode)
                  // Or has service radius (would need geocoding for actual radius calculation)
                  ->orWhere('service_radius_miles', '>', 0)
                  // Or no zip restriction
                  ->orWhereNull('zip_codes');
            });
        }

        // Get matching PMs and calculate match scores
        $matches = $query->get()->map(function ($preference) use ($lead) {
            $baseScore = $this->calculateMatchScore($preference, $lead);
            $feedbackAdjustment = $this->calculateFeedbackAdjustment($preference->user_id, $lead);
            $finalScore = $baseScore + $feedbackAdjustment;

            return [
                'user_id' => $preference->user_id,
                'preference' => $preference,
                'user' => $preference->user,
                'tier' => $preference->tier,
                'score' => max(0, min(100, $finalScore)), // Clamp between 0-100
                'base_score' => $baseScore,
                'feedback_adjustment' => $feedbackAdjustment,
            ];
        });

        // Sort by tier priority (higher tier first) and then by match score
        // Balanced formula: tier priority gets weight but doesn't completely override match quality
        $matches = $matches->sortByDesc(function ($match) {
            $tierPriority = $match['tier'] ? $match['tier']->priority ?? 0 : 0;
            $matchScore = $match['score'];

            // Improved formula: (priority * 100) + (score * 10)
            // This gives tiers advantage but allows good matches in lower tiers to compete
            return ($tierPriority * 100) + ($matchScore * 10);
        })
        // Secondary sort by last_lead_received_at for fair distribution within same tier/score
        ->sortBy(function ($match) {
            return $match['user']->last_lead_received_at ?? now()->subYears(10);
        });

        // Apply exclusivity hours filter if enabled
        if ($respectExclusivity && $lead->created_at) {
            $matches = $this->applyExclusivityFilter($matches, $lead);
        }

        return $matches->take($limit);
    }

    /**
     * Apply exclusivity hours filter to matches.
     * Higher tier subscribers get exclusive access to new leads for a certain period.
     *
     * @param \Illuminate\Support\Collection $matches
     * @param Lead $lead
     * @return \Illuminate\Support\Collection
     */
    protected function applyExclusivityFilter($matches, Lead $lead)
    {
        $leadAgeHours = $lead->created_at->diffInHours(now());

        // Find the highest tier with exclusivity hours
        $highestExclusivityTier = $matches->max(function ($match) {
            return $match['tier'] ? $match['tier']->exclusivity_hours ?? 0 : 0;
        });

        if ($highestExclusivityTier > 0 && $leadAgeHours < $highestExclusivityTier) {
            // Filter to only include tiers with exclusivity hours > 0
            // This ensures only paying tiers with exclusivity get new leads during the exclusivity window
            return $matches->filter(function ($match) use ($leadAgeHours) {
                $tierExclusivity = $match['tier'] ? $match['tier']->exclusivity_hours ?? 0 : 0;
                // Only include tiers where exclusivity hours > 0 (have exclusivity)
                // AND the lead is within their exclusivity period
                return $tierExclusivity > 0 && $leadAgeHours < $tierExclusivity;
            });
        }

        return $matches;
    }

    /**
     * Calculate feedback-based adjustment to match score.
     * This adjusts the score based on historical PM feedback for similar leads.
     *
     * @param int $pmId
     * @param Lead $lead
     * @return int Adjustment from -20 to +20
     */
    protected function calculateFeedbackAdjustment($pmId, Lead $lead)
    {
        $adjustment = 0;

        // Get recent feedback from this PM (last 3 months)
        $recentFeedback = LeadFeedback::forPM($pmId)
            ->where('created_at', '>=', now()->subMonths(3))
            ->get();

        if ($recentFeedback->isEmpty()) {
            return 0; // No feedback history
        }

        // Check property type rejection history
        if ($lead->property_type) {
            $propertyTypeRejections = $recentFeedback->filter(function ($feedback) use ($lead) {
                return $feedback->lead->property_type === $lead->property_type
                    && in_array($feedback->feedback_type, ['rejected', 'not_interested'])
                    && $feedback->rejection_reason === 'wrong_property_type';
            })->count();

            if ($propertyTypeRejections >= 3) {
                $adjustment -= 15; // Significant penalty for consistent rejections
            }
        }

        // Check zip code/location rejection history
        if ($lead->zipcode) {
            $locationRejections = $recentFeedback->filter(function ($feedback) use ($lead) {
                return $feedback->lead->zipcode === $lead->zipcode
                    && in_array($feedback->feedback_type, ['rejected', 'not_interested'])
                    && $feedback->rejection_reason === 'wrong_location';
            })->count();

            if ($locationRejections >= 2) {
                $adjustment -= 10; // Penalty for location rejections
            }
        }

        // Reward for accepting similar leads
        $similarAcceptances = $recentFeedback->filter(function ($feedback) use ($lead) {
            return $feedback->lead->property_type === $lead->property_type
                && $feedback->feedback_type === 'accepted';
        })->count();

        if ($similarAcceptances >= 3) {
            $adjustment += 10; // Boost for accepting similar leads
        }

        // Penalize for spam/low quality feedback
        $spamCount = $recentFeedback->whereIn('feedback_type', ['spam', 'low_quality'])->count();
        if ($spamCount >= 2) {
            $adjustment -= 5;
        }

        // Average quality rating bonus/penalty
        $avgQualityRating = $recentFeedback->whereNotNull('quality_rating')->avg('quality_rating');
        if ($avgQualityRating !== null) {
            // Quality ratings are 1-5, adjust score based on deviation from 3 (neutral)
            $qualityAdjustment = ($avgQualityRating - 3) * 2; // -4 to +4 range
            $adjustment += $qualityAdjustment;
        }

        return max(-20, min(20, $adjustment)); // Clamp between -20 and +20
    }

    /**
     * Calculate match score between a lead and property manager preferences using AI.
     *
     * @param UserPreferences $preference
     * @param Lead $lead
     * @return int Score from 0-100
     */
    protected function calculateMatchScore(UserPreferences $preference, Lead $lead)
    {
        // Use AI Lead Scoring Service for intelligent scoring
        $user = User::find($preference->user_id);
        $aiScore = $this->scoringService->scoreLead($lead, $user);

        // Return AI-calculated score
        return $aiScore['score'];

        // Legacy scoring code below (kept for fallback)
        $score = 0;

        // Property type match (30 points)
        if ($lead->property_type && $preference->property_types) {
            if (in_array($lead->property_type, $preference->property_types)) {
                $score += 30;
            }
        } else {
            $score += 10; // Partial points if no preference set
        }

        // Units match (25 points)
        if ($lead->number_of_units) {
            if ($preference->min_units && $preference->max_units) {
                if ($lead->number_of_units >= $preference->min_units &&
                    $lead->number_of_units <= $preference->max_units) {
                    $score += 25;
                }
            } else {
                $score += 10; // Partial points if no preference set
            }
        }

        // Location matching (35 points max)
        // Priority: Geospatial distance > Zip code match > No restriction
        if ($lead->latitude && $lead->longitude && $preference->latitude && $preference->longitude && $preference->service_radius_miles > 0) {
            // Geospatial distance matching (most accurate)
            $distance = $this->calculateDistance(
                $lead->latitude,
                $lead->longitude,
                $preference->latitude,
                $preference->longitude
            );

            if ($distance <= $preference->service_radius_miles) {
                // Score based on how close to center (closer = better)
                $distanceRatio = $distance / $preference->service_radius_miles;
                $score += (int) (35 * (1 - ($distanceRatio * 0.5))); // 35-17.5 points based on distance
            } else {
                // Outside service radius
                $score += 0;
            }
        } elseif ($lead->zipcode && $preference->hasZipCode($lead->zipcode)) {
            // Exact zip code match (fallback if no geospatial data)
            $score += 35;
        } elseif ($preference->service_radius_miles > 0) {
            $score += 15; // Partial points for service radius without distance calc
        } elseif (!$preference->hasZipCodes()) {
            $score += 10; // Partial points if no zip restriction
        }

        // Price range match (10 points)
        if ($lead->price) {
            // Could add price range preferences in future
            $score += 5;
        }

        return min($score, 100); // Cap at 100
    }

    /**
     * Distribute a lead to matched property managers.
     *
     * @param Lead $lead
     * @param array $options
     * @return array Distribution results
     */
    public function distributeLead(Lead $lead, array $options = [])
    {
        $limit = $options['limit'] ?? 5;
        $autoNotify = $options['auto_notify'] ?? true;
        $respectExclusivity = $options['respect_exclusivity'] ?? true;

        DB::beginTransaction();

        try {
            // Find matching PMs
            $matches = $this->findMatchingPropertyManagers($lead, $limit, $respectExclusivity);

            if ($matches->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No matching property managers found.',
                    'distributed_count' => 0,
                ];
            }

            $distributed = [];

            foreach ($matches as $match) {
                // Check if already distributed to this PM
                $existing = UserLeads::where('lead_id', $lead->id)
                    ->where('pm_id', $match['user_id'])
                    ->first();

                if ($existing) {
                    continue; // Skip if already distributed
                }

                // Get AI score for this PM
                $user = User::find($match['user_id']);
                $aiScore = $this->scoringService->scoreLead($lead, $user);

                // Create distribution record
                $userLead = UserLeads::create([
                    'user_id' => $match['user_id'],
                    'lead_id' => $lead->id,
                    'pm_id' => $match['user_id'],
                    'match_score' => $match['score'],
                    'status' => 'pending',
                    'distributed_at' => now(),
                ]);

                // Update user's last_lead_received_at for fair distribution
                User::where('id', $match['user_id'])->update([
                    'last_lead_received_at' => now()
                ]);

                $distributed[] = [
                    'user_lead_id' => $userLead->id,
                    'pm_id' => $match['user_id'],
                    'pm_name' => $match['user']->name ?? 'N/A',
                    'pm_email' => $match['user']->email ?? 'N/A',
                    'match_score' => $match['score'],
                    'tier' => $match['tier']->name ?? 'N/A',
                ];

                // Fire WebSocket event with AI score (broadcasts to PM's private channel)
                event(new \App\Events\LeadDistributed($userLead, $aiScore));

                // Send email notification if enabled
                if ($autoNotify && $match['preference']->email_notifications) {
                    // TODO: Send email notification
                }
            }

            // Update lead distribution stats
            $lead->markAsDistributed();

            // Schedule delayed distribution to lower tiers if exclusivity is being enforced
            if ($respectExclusivity && $lead->created_at) {
                $this->scheduleDelayedDistribution($lead, $matches, $options);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Lead distributed to {$matches->count()} property manager(s).",
                'distributed_count' => count($distributed),
                'distributions' => $distributed,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lead distribution failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Lead distribution failed.',
                'error' => $e->getMessage(),
                'distributed_count' => 0,
            ];
        }
    }

    /**
     * Schedule delayed distribution to lower tiers after exclusivity period.
     *
     * @param Lead $lead
     * @param \Illuminate\Support\Collection $highTierMatches
     * @param array $options
     * @return void
     */
    protected function scheduleDelayedDistribution(Lead $lead, $highTierMatches, array $options)
    {
        // Find the highest exclusivity hours among distributed tiers
        $highestExclusivity = $highTierMatches->max(function ($match) {
            return $match['tier'] ? $match['tier']->exclusivity_hours ?? 0 : 0;
        });

        if ($highestExclusivity > 0) {
            // Calculate when exclusivity expires
            $exclusivityExpiresAt = $lead->created_at->addHours($highestExclusivity);
            $delayInMinutes = now()->diffInMinutes($exclusivityExpiresAt, false);

            // Only schedule if exclusivity hasn't expired yet
            if ($delayInMinutes > 0) {
                DistributeLeadToLowerTiers::dispatch($lead->id, $options)
                    ->delay(now()->addMinutes($delayInMinutes));

                Log::info("Scheduled delayed distribution for lead {$lead->id} to lower tiers in {$delayInMinutes} minutes");
            }
        }
    }

    /**
     * Calculate distance between two points using Haversine formula.
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in miles
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 3959; // Earth's radius in miles

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get distribution statistics for a lead.
     *
     * @param Lead $lead
     * @return array
     */
    public function getDistributionStats(Lead $lead)
    {
        $distributions = UserLeads::where('lead_id', $lead->id)
            ->with(['propertyManager', 'responses'])
            ->get();

        return [
            'total_distributed' => $distributions->count(),
            'pending' => $distributions->where('status', 'pending')->count(),
            'viewed' => $distributions->where('status', 'viewed')->count(),
            'responded' => $distributions->where('status', 'responded')->count(),
            'distributions' => $distributions->map(function ($dist) {
                return [
                    'pm_name' => $dist->propertyManager->name ?? 'N/A',
                    'pm_email' => $dist->propertyManager->email ?? 'N/A',
                    'match_score' => $dist->match_score,
                    'status' => $dist->status,
                    'distributed_at' => $dist->distributed_at,
                    'viewed_at' => $dist->viewed_at,
                    'response_count' => $dist->responses->count(),
                ];
            }),
        ];
    }
}
