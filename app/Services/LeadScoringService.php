<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Models\UserPreferences;
use App\Models\UserLeads;
use Illuminate\Support\Facades\DB;

/**
 * AI-Powered Lead Scoring Service
 *
 * Scores leads 0-100 based on multiple factors:
 * - Property type match
 * - Location proximity
 * - Unit count alignment
 * - Historical PM performance
 * - Lead freshness
 * - Lead source quality
 */
class LeadScoringService
{
    // Scoring weights (total = 100)
    const WEIGHT_PROPERTY_TYPE = 25;
    const WEIGHT_LOCATION = 20;
    const WEIGHT_UNITS = 15;
    const WEIGHT_HISTORICAL = 20;
    const WEIGHT_FRESHNESS = 10;
    const WEIGHT_SOURCE = 10;

    /**
     * Calculate AI score for a lead-PM match
     *
     * @param Lead $lead
     * @param User $pm
     * @return array ['score' => int, 'breakdown' => array, 'tier' => string]
     */
    public function scoreLead(Lead $lead, User $pm): array
    {
        $preferences = UserPreferences::where('user_id', $pm->id)->first();

        if (!$preferences) {
            return [
                'score' => 0,
                'breakdown' => [],
                'tier' => 'no_match',
                'reasons' => ['No preferences configured']
            ];
        }

        // Calculate individual scores
        $propertyTypeScore = $this->scorePropertyType($lead, $preferences);
        $locationScore = $this->scoreLocation($lead, $preferences);
        $unitsScore = $this->scoreUnits($lead, $preferences);
        $historicalScore = $this->scoreHistoricalPerformance($lead, $pm);
        $freshnessScore = $this->scoreFreshness($lead);
        $sourceScore = $this->scoreSource($lead);

        // Calculate weighted total
        $totalScore = round(
            ($propertyTypeScore * self::WEIGHT_PROPERTY_TYPE / 100) +
            ($locationScore * self::WEIGHT_LOCATION / 100) +
            ($unitsScore * self::WEIGHT_UNITS / 100) +
            ($historicalScore * self::WEIGHT_HISTORICAL / 100) +
            ($freshnessScore * self::WEIGHT_FRESHNESS / 100) +
            ($sourceScore * self::WEIGHT_SOURCE / 100)
        );

        return [
            'score' => $totalScore,
            'breakdown' => [
                'property_type' => $propertyTypeScore,
                'location' => $locationScore,
                'units' => $unitsScore,
                'historical' => $historicalScore,
                'freshness' => $freshnessScore,
                'source' => $sourceScore,
            ],
            'tier' => $this->getScoreTier($totalScore),
            'badge' => $this->getScoreBadge($totalScore),
            'reasons' => $this->getScoreReasons($totalScore, [
                'property_type' => $propertyTypeScore,
                'location' => $locationScore,
                'units' => $unitsScore,
            ]),
        ];
    }

    /**
     * Score property type match (0-100)
     */
    protected function scorePropertyType(Lead $lead, UserPreferences $preferences): int
    {
        $preferredTypes = $preferences->property_types ?? [];

        if (empty($preferredTypes)) {
            return 50; // Neutral score if no preference set
        }

        $leadType = strtolower($lead->property_type);

        // Exact match
        if (in_array($leadType, $preferredTypes)) {
            return 100;
        }

        // Partial matches (e.g., multi-family could match residential)
        $partialMatches = [
            'multi_family' => ['residential'],
            'apartment' => ['residential'],
            'condo' => ['residential'],
            'office' => ['commercial'],
            'retail' => ['commercial'],
            'warehouse' => ['industrial'],
        ];

        foreach ($partialMatches as $leadVariant => $preferredVariants) {
            if (str_contains($leadType, $leadVariant)) {
                foreach ($preferredVariants as $variant) {
                    if (in_array($variant, $preferredTypes)) {
                        return 70; // Good match
                    }
                }
            }
        }

        return 30; // Poor match but not zero
    }

    /**
     * Score location proximity (0-100)
     */
    protected function scoreLocation(Lead $lead, UserPreferences $preferences): int
    {
        $preferredZipCodes = $preferences->zip_codes ?? [];
        $serviceRadiusMiles = $preferences->service_radius_miles ?? 25;

        if (empty($preferredZipCodes)) {
            return 50; // Neutral if no preference
        }

        $leadZip = $lead->zipcode;

        // Exact ZIP match
        if (in_array($leadZip, $preferredZipCodes)) {
            return 100;
        }

        // Check if within service radius (simplified distance check)
        // In production, use proper geocoding and distance calculation
        if ($this->isWithinRadius($leadZip, $preferredZipCodes, $serviceRadiusMiles)) {
            return 80; // Good proximity
        }

        // Check if in same city/area (first 3 digits of ZIP)
        $leadZipPrefix = substr($leadZip, 0, 3);
        foreach ($preferredZipCodes as $prefZip) {
            if (substr($prefZip, 0, 3) === $leadZipPrefix) {
                return 60; // Same area
            }
        }

        return 20; // Outside service area
    }

    /**
     * Score unit count alignment (0-100)
     */
    protected function scoreUnits(Lead $lead, UserPreferences $preferences): int
    {
        $leadUnits = $lead->number_of_units ?? 0;
        $minUnits = $preferences->min_units;
        $maxUnits = $preferences->max_units;

        // If no preference set, give neutral score
        if ($minUnits === null && $maxUnits === null) {
            return 50;
        }

        // Within range
        if (($minUnits === null || $leadUnits >= $minUnits) &&
            ($maxUnits === null || $leadUnits <= $maxUnits)) {
            return 100;
        }

        // Close to range (within 20%)
        if ($minUnits !== null && $leadUnits < $minUnits) {
            $diff = ($minUnits - $leadUnits) / $minUnits;
            if ($diff <= 0.2) return 70;
        }

        if ($maxUnits !== null && $leadUnits > $maxUnits) {
            $diff = ($leadUnits - $maxUnits) / $maxUnits;
            if ($diff <= 0.2) return 70;
        }

        return 30; // Outside range
    }

    /**
     * Score based on PM's historical performance with similar leads (0-100)
     */
    protected function scoreHistoricalPerformance(Lead $lead, User $pm): int
    {
        // Get PM's past performance with similar property types
        $similarLeads = UserLeads::where('user_id', $pm->id)
            ->where('property_type', $lead->property_type)
            ->get();

        if ($similarLeads->isEmpty()) {
            return 50; // No history = neutral
        }

        // Calculate conversion rate
        $total = $similarLeads->count();
        $won = $similarLeads->whereIn('status', ['won', 'closed'])->count();
        $conversionRate = $total > 0 ? ($won / $total) : 0;

        // Calculate response time average
        $avgResponseMinutes = $similarLeads->whereNotNull('viewed_at')
            ->avg(function($userLead) {
                if ($userLead->viewed_at && $userLead->distributed_at) {
                    return $userLead->viewed_at->diffInMinutes($userLead->distributed_at);
                }
                return null;
            });

        // Score based on conversion rate (60%) and response time (40%)
        $conversionScore = $conversionRate * 100;

        // Fast response = high score (under 60 min = 100, over 240 min = 0)
        $responseScore = 100;
        if ($avgResponseMinutes) {
            $responseScore = max(0, min(100, 100 - (($avgResponseMinutes - 60) / 180 * 100)));
        }

        return round(($conversionScore * 0.6) + ($responseScore * 0.4));
    }

    /**
     * Score lead freshness (0-100)
     * Newer leads score higher
     */
    protected function scoreFreshness(Lead $lead): int
    {
        $ageHours = $lead->created_at->diffInHours(now());

        // Less than 1 hour = 100
        if ($ageHours < 1) return 100;

        // 1-6 hours = 90-80
        if ($ageHours < 6) return 100 - ($ageHours * 3);

        // 6-24 hours = 80-60
        if ($ageHours < 24) return 80 - (($ageHours - 6) * 1.1);

        // 24-48 hours = 60-40
        if ($ageHours < 48) return 60 - (($ageHours - 24) * 0.8);

        // 48-72 hours = 40-20
        if ($ageHours < 72) return 40 - (($ageHours - 48) * 0.8);

        // Over 72 hours = 20-0
        if ($ageHours < 168) return max(0, 20 - (($ageHours - 72) * 0.2));

        return 0; // Very old lead
    }

    /**
     * Score lead source quality (0-100)
     */
    protected function scoreSource(Lead $lead): int
    {
        $sourceQuality = [
            'website' => 90,
            'referral' => 100,
            'direct' => 85,
            'partner' => 80,
            'organic' => 75,
            'paid' => 70,
            'test' => 0,
        ];

        $source = strtolower($lead->source ?? 'unknown');

        return $sourceQuality[$source] ?? 50; // Default to 50 if unknown
    }

    /**
     * Determine score tier
     */
    protected function getScoreTier(int $score): string
    {
        if ($score >= 80) return 'excellent';
        if ($score >= 65) return 'good';
        if ($score >= 50) return 'fair';
        if ($score >= 35) return 'poor';
        return 'very_poor';
    }

    /**
     * Get visual badge for score
     */
    protected function getScoreBadge(int $score): array
    {
        if ($score >= 80) {
            return [
                'text' => '🔥 High Value',
                'color' => 'red',
                'priority' => 'high'
            ];
        }
        if ($score >= 65) {
            return [
                'text' => '⭐ Great Match',
                'color' => 'green',
                'priority' => 'medium'
            ];
        }
        if ($score >= 50) {
            return [
                'text' => '✓ Good Fit',
                'color' => 'blue',
                'priority' => 'normal'
            ];
        }
        return [
            'text' => 'Standard',
            'color' => 'gray',
            'priority' => 'low'
        ];
    }

    /**
     * Get human-readable reasons for score
     */
    protected function getScoreReasons(int $totalScore, array $breakdown): array
    {
        $reasons = [];

        if ($breakdown['property_type'] >= 80) {
            $reasons[] = 'Matches your preferred property types';
        }
        if ($breakdown['location'] >= 80) {
            $reasons[] = 'In your target service area';
        }
        if ($breakdown['units'] >= 80) {
            $reasons[] = 'Unit count fits your criteria';
        }

        if ($totalScore >= 80) {
            $reasons[] = 'Highly recommended based on your preferences';
        }

        return $reasons;
    }

    /**
     * Simplified radius check
     * In production, use proper geocoding service
     */
    protected function isWithinRadius(string $leadZip, array $preferredZips, int $radiusMiles): bool
    {
        // Simplified: check if ZIP codes are close numerically
        // In production, use actual lat/long distance calculation
        $leadZipNum = (int)$leadZip;

        foreach ($preferredZips as $prefZip) {
            $prefZipNum = (int)$prefZip;
            $diff = abs($leadZipNum - $prefZipNum);

            // Rough approximation: 100 ZIP difference ≈ 50 miles
            if ($diff <= ($radiusMiles * 2)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Batch score multiple leads for a PM
     */
    public function scoreLeadsForPM(array $leads, User $pm): array
    {
        $scores = [];

        foreach ($leads as $lead) {
            $scores[$lead->id] = $this->scoreLead($lead, $pm);
        }

        // Sort by score descending
        uasort($scores, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return $scores;
    }
}
