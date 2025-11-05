<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\UserLeads;
use App\Services\LeadScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadScoringController extends Controller
{
    protected LeadScoringService $scoringService;

    public function __construct()
    {
        $this->scoringService = new LeadScoringService();
    }

    /**
     * Get AI score for a specific lead
     *
     * GET /api/v1/leads/{leadId}/score
     */
    public function getLeadScore(Request $request, $leadId)
    {
        $user = $request->user();
        $lead = Lead::findOrFail($leadId);

        $score = $this->scoringService->scoreLead($lead, $user);

        return response()->json([
            'lead_id' => $lead->id,
            'score' => $score['score'],
            'tier' => $score['tier'],
            'badge' => $score['badge'],
            'reasons' => $score['reasons'],
            'breakdown' => $score['breakdown'],
        ]);
    }

    /**
     * Get AI scores for all leads of current PM
     *
     * GET /api/v1/leads/scores
     */
    public function getMyLeadScores(Request $request)
    {
        $user = $request->user();

        // Get all leads for this PM
        $userLeads = UserLeads::where('user_id', $user->id)
            ->with('lead')
            ->get();

        $scoredLeads = [];

        foreach ($userLeads as $userLead) {
            if ($userLead->lead) {
                $score = $this->scoringService->scoreLead($userLead->lead, $user);

                $scoredLeads[] = [
                    'id' => $userLead->lead->id,
                    'user_lead_id' => $userLead->id,
                    'property_type' => $userLead->lead->property_type,
                    'address' => $userLead->lead->address,
                    'city' => $userLead->lead->city,
                    'state' => $userLead->lead->state,
                    'zipcode' => $userLead->lead->zipcode,
                    'number_of_units' => $userLead->lead->number_of_units,
                    'status' => $userLead->status,
                    'score' => $score['score'],
                    'tier' => $score['tier'],
                    'badge' => $score['badge'],
                    'reasons' => $score['reasons'],
                    'distributed_at' => $userLead->distributed_at,
                ];
            }
        }

        // Sort by score descending
        usort($scoredLeads, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return response()->json([
            'leads' => $scoredLeads,
            'total' => count($scoredLeads),
        ]);
    }

    /**
     * Get market insights for PM
     *
     * GET /api/v1/market-insights
     */
    public function getMarketInsights(Request $request)
    {
        $user = $request->user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return response()->json([
                'message' => 'Please set your preferences first'
            ], 400);
        }

        // Get leads from last 30 days
        $recentLeads = Lead::where('created_at', '>=', now()->subDays(30))->get();

        // Property type trends
        $propertyTypeTrends = $recentLeads->groupBy('property_type')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(5);

        // Hot ZIP codes in service area
        $zipCodes = $preferences->zip_codes ?? [];
        $hotZipCodes = $recentLeads
            ->whereIn('zipcode', $zipCodes)
            ->groupBy('zipcode')
            ->map(fn($group) => [
                'count' => $group->count(),
                'avg_units' => $group->avg('number_of_units'),
            ])
            ->sortByDesc('count')
            ->take(5);

        // Average lead quality by property type
        $avgScoresByType = [];
        foreach ($propertyTypeTrends as $type => $count) {
            $typeLeads = $recentLeads->where('property_type', $type);
            $scores = [];

            foreach ($typeLeads as $lead) {
                $score = $this->scoringService->scoreLead($lead, $user);
                $scores[] = $score['score'];
            }

            $avgScoresByType[$type] = [
                'count' => $count,
                'avg_score' => count($scores) > 0 ? round(array_sum($scores) / count($scores)) : 0,
            ];
        }

        // PM performance metrics
        $myLeads = UserLeads::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $performance = [
            'leads_received' => $myLeads->count(),
            'leads_won' => $myLeads->whereIn('status', ['won', 'closed'])->count(),
            'conversion_rate' => $myLeads->count() > 0
                ? round(($myLeads->whereIn('status', ['won', 'closed'])->count() / $myLeads->count()) * 100)
                : 0,
            'avg_response_time_minutes' => $myLeads->whereNotNull('viewed_at')->avg(function($userLead) {
                return $userLead->viewed_at->diffInMinutes($userLead->distributed_at);
            }),
        ];

        // Market comparison (anonymized average of similar PMs)
        $similarPMs = DB::table('user_preferences')
            ->where('tier_id', $preferences->tier_id)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id');

        $marketAvg = UserLeads::whereIn('user_id', $similarPMs)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $marketComparison = [
            'your_conversion_rate' => $performance['conversion_rate'],
            'market_avg_conversion_rate' => $marketAvg->count() > 0
                ? round(($marketAvg->whereIn('status', ['won', 'closed'])->count() / $marketAvg->count()) * 100)
                : 0,
            'your_response_time' => round($performance['avg_response_time_minutes'] ?? 0),
            'market_avg_response_time' => round($marketAvg->whereNotNull('viewed_at')->avg(function($userLead) {
                return $userLead->viewed_at->diffInMinutes($userLead->distributed_at);
            }) ?? 0),
        ];

        return response()->json([
            'period' => 'Last 30 days',
            'propertyTypeTrends' => $propertyTypeTrends,
            'hotZipCodes' => $hotZipCodes,
            'avgScoresByType' => $avgScoresByType,
            'yourPerformance' => $performance,
            'marketComparison' => $marketComparison,
            'insights' => $this->generateInsights($performance, $marketComparison, $avgScoresByType),
        ]);
    }

    /**
     * Generate actionable insights
     */
    protected function generateInsights($performance, $marketComparison, $avgScores): array
    {
        $insights = [];

        // Conversion rate insights
        if ($marketComparison['your_conversion_rate'] > $marketComparison['market_avg_conversion_rate']) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Great Performance!',
                'message' => "Your conversion rate is {$marketComparison['your_conversion_rate']}%, which is " .
                    round($marketComparison['your_conversion_rate'] - $marketComparison['market_avg_conversion_rate']) .
                    "% above market average.",
            ];
        } else if ($marketComparison['your_conversion_rate'] < $marketComparison['market_avg_conversion_rate']) {
            $insights[] = [
                'type' => 'improvement',
                'title' => 'Opportunity for Growth',
                'message' => "Consider improving your response time and lead follow-up strategies to boost conversions.",
            ];
        }

        // Response time insights
        if ($marketComparison['your_response_time'] < $marketComparison['market_avg_response_time']) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Fast Responder!',
                'message' => "Your response time is faster than market average. Keep it up!",
            ];
        }

        // Property type recommendations
        $topScoringType = collect($avgScores)->sortByDesc('avg_score')->keys()->first();
        if ($topScoringType) {
            $insights[] = [
                'type' => 'recommendation',
                'title' => 'Focus Area',
                'message' => "'{$topScoringType}' properties have the highest match scores for your preferences. Consider prioritizing these leads.",
            ];
        }

        return $insights;
    }
}
