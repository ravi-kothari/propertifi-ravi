<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserLeads;
use App\Models\LeadResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PmDashboardController extends Controller
{
    /**
     * Display the authenticated PM's dashboard data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        $pm = Auth::user();

        // Get PM's preferences and tier information
        $preferences = $pm->preferences()->with('tier')->first();

        // Lead statistics
        $totalLeadsAssigned = UserLeads::where('pm_id', $pm->id)->count();

        $leadsByStatus = UserLeads::where('pm_id', $pm->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Recent leads (last 7 days)
        $recentLeadsCount = UserLeads::where('pm_id', $pm->id)
            ->where('distributed_at', '>=', now()->subDays(7))
            ->count();

        // This month's leads
        $thisMonthLeadsCount = UserLeads::where('pm_id', $pm->id)
            ->whereYear('distributed_at', now()->year)
            ->whereMonth('distributed_at', now()->month)
            ->count();

        // Response rate
        $totalResponses = LeadResponse::whereHas('userLead', function ($query) use ($pm) {
            $query->where('pm_id', $pm->id);
        })->count();

        $responseRate = $totalLeadsAssigned > 0
            ? round(($totalResponses / $totalLeadsAssigned) * 100, 2)
            : 0;

        // Tier and subscription info
        $subscriptionInfo = [
            'tier_name' => $preferences && $preferences->tier ? $preferences->tier->name : 'free',
            'tier_title' => $preferences && $preferences->tier ? $preferences->tier->title : 'Free Tier',
            'monthly_price' => $preferences && $preferences->tier ? $preferences->tier->price : 0,
            'lead_cap' => $preferences && $preferences->tier ? $preferences->tier->lead_cap : 3,
            'leads_used_this_month' => $thisMonthLeadsCount,
            'leads_remaining' => $preferences && $preferences->tier
                ? max(0, $preferences->tier->lead_cap - $thisMonthLeadsCount)
                : max(0, 3 - $thisMonthLeadsCount),
            'exclusivity_hours' => $preferences && $preferences->tier ? $preferences->tier->exclusivity_hours : 0,
            'is_active' => $preferences ? $preferences->is_active : false,
        ];

        // Credit info (if applicable)
        $creditInfo = [
            'credits_available' => $pm->credits ?? 0,
            'credits_used' => 0, // This would come from a credits_used table if tracking usage
        ];

        // Recent activity
        $recentLeads = UserLeads::where('pm_id', $pm->id)
            ->with(['lead' => function ($query) {
                $query->select('id', 'property_type', 'city', 'state', 'quality_score', 'created_at');
            }])
            ->orderBy('distributed_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($userLead) {
                return [
                    'id' => $userLead->id,
                    'lead_id' => $userLead->lead_id,
                    'property_type' => $userLead->lead->property_type ?? 'N/A',
                    'location' => ($userLead->lead->city ?? '') . ', ' . ($userLead->lead->state ?? ''),
                    'quality_score' => $userLead->lead->quality_score ?? 0,
                    'match_score' => $userLead->match_score,
                    'status' => $userLead->status,
                    'distributed_at' => $userLead->distributed_at,
                    'viewed_at' => $userLead->viewed_at,
                ];
            });

        $stats = [
            'user' => [
                'id' => $pm->id,
                'name' => $pm->name,
                'email' => $pm->email,
                'company_name' => $pm->company_name,
            ],
            'overview' => [
                'total_leads_assigned' => $totalLeadsAssigned,
                'recent_leads_7_days' => $recentLeadsCount,
                'this_month_leads' => $thisMonthLeadsCount,
                'response_rate_percentage' => $responseRate,
                'total_responses' => $totalResponses,
            ],
            'leads_by_status' => [
                'pending' => $leadsByStatus['pending'] ?? 0,
                'viewed' => $leadsByStatus['viewed'] ?? 0,
                'responded' => $leadsByStatus['responded'] ?? 0,
                'total' => $totalLeadsAssigned,
            ],
            'subscription_info' => $subscriptionInfo,
            'credit_info' => $creditInfo,
            'recent_activity' => $recentLeads,
            'preferences' => $preferences ? [
                'property_types' => $preferences->property_types,
                'min_units' => $preferences->min_units,
                'max_units' => $preferences->max_units,
                'zip_codes' => $preferences->zip_codes,
                'service_radius_miles' => $preferences->service_radius_miles,
                'email_notifications' => $preferences->email_notifications,
                'sms_notifications' => $preferences->sms_notifications,
            ] : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
