<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lead;
use App\Models\UserLeads;
use App\Models\LeadResponse;
use App\Models\DocumentTemplate;
use App\Models\TemplateDownload;
use App\Models\CalculatorLog;
use App\Models\StateLawContent;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    /**
     * Get comprehensive admin dashboard statistics.
     *
     * GET /api/admin/dashboard
     */
    public function dashboard(Request $request)
    {
        // Cache for 5 minutes to improve performance
        $cacheKey = 'admin_dashboard_stats';

        $stats = Cache::remember($cacheKey, 300, function () {
            return $this->gatherDashboardStats();
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Gather all dashboard statistics.
     */
    private function gatherDashboardStats()
    {
        return [
            'overview' => $this->getOverviewStats(),
            'lead_stats' => $this->getLeadStats(),
            'user_stats' => $this->getUserStats(),
            'revenue_stats' => $this->getRevenueStats(),
            'engagement_stats' => $this->getEngagementStats(),
            'recent_activity' => $this->getRecentActivity(),
            'growth_trends' => $this->getGrowthTrends(),
        ];
    }

    /**
     * Get overview statistics.
     */
    private function getOverviewStats()
    {
        $totalPMs = User::where('type', User::TYPE_PM)->count();
        $verifiedPMs = User::where('type', User::TYPE_PM)->where('is_verified', true)->count();
        $totalLeads = Lead::count();
        $distributedLeads = Lead::where('distribution_count', '>', 0)->count();

        return [
            'total_users' => User::whereIn('type', ['owner', 'pm'])->count(),
            'active_users' => User::whereIn('type', ['owner', 'pm'])->where('status', 1)->count(),
            'total_property_managers' => $totalPMs,
            'verified_property_managers' => $verifiedPMs,
            'total_owners' => User::where('type', User::TYPE_OWNER)->count(),
            'total_leads' => $totalLeads,
            'distributed_leads' => $distributedLeads,
            'pending_leads' => Lead::where('status', 'new')->count(),
        ];
    }

    /**
     * Get lead statistics.
     */
    private function getLeadStats()
    {
        $totalLeads = Lead::count();
        $distributedLeads = Lead::where('distribution_count', '>', 0)->count();

        // This month's leads
        $thisMonthLeads = Lead::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Last month's leads for comparison
        $lastMonthLeads = Lead::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $monthGrowth = $lastMonthLeads > 0
            ? round((($thisMonthLeads - $lastMonthLeads) / $lastMonthLeads) * 100, 2)
            : 0;

        // Average quality score
        $avgQualityScore = Lead::avg('quality_score') ?? 0;

        // Calculate rates
        $totalDistributions = UserLeads::count();
        $viewedLeads = UserLeads::whereNotNull('viewed_at')->count();
        $respondedLeads = UserLeads::where('status', 'responded')->count();

        return [
            'total_this_month' => $thisMonthLeads,
            'total_last_month' => $lastMonthLeads,
            'growth_percentage' => $monthGrowth,
            'average_quality_score' => round($avgQualityScore, 2),
            'distribution_rate' => $totalLeads > 0
                ? round(($distributedLeads / $totalLeads) * 100, 2)
                : 0,
            'view_rate' => $totalDistributions > 0
                ? round(($viewedLeads / $totalDistributions) * 100, 2)
                : 0,
            'response_rate' => $totalDistributions > 0
                ? round(($respondedLeads / $totalDistributions) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get user statistics.
     */
    private function getUserStats()
    {
        $totalPMs = User::where('type', User::TYPE_PM)->count();
        $verifiedPMs = User::where('type', User::TYPE_PM)->where('is_verified', true)->count();
        $pendingVerification = User::where('type', User::TYPE_PM)->where('is_verified', false)->count();

        // PMs by tier
        $pmsByTier = DB::table('users')
            ->leftJoin('user_preferences', 'users.id', '=', 'user_preferences.user_id')
            ->leftJoin('tiers', 'user_preferences.tier_id', '=', 'tiers.id')
            ->where('users.type', User::TYPE_PM)
            ->select('tiers.title as tier_name', DB::raw('count(*) as count'))
            ->groupBy('tiers.title')
            ->get()
            ->map(function ($item) {
                return [
                    'tier_name' => $item->tier_name ?? 'No Tier',
                    'count' => $item->count,
                ];
            })
            ->toArray();

        // New users this month
        $newPMsThisMonth = User::where('type', User::TYPE_PM)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $newOwnersThisMonth = User::where('type', User::TYPE_OWNER)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            'new_this_month' => $newPMsThisMonth + $newOwnersThisMonth,
            'pending_verification' => $pendingVerification,
            'pm_by_tier' => $pmsByTier,
        ];
    }

    /**
     * Get revenue statistics (placeholder for Stripe integration).
     */
    private function getRevenueStats()
    {
        // Calculate MRR from active subscriptions
        $mrr = DB::table('users')
            ->join('user_preferences', 'users.id', '=', 'user_preferences.user_id')
            ->join('tiers', 'user_preferences.tier_id', '=', 'tiers.id')
            ->where('user_preferences.is_active', true)
            ->sum('tiers.price');

        $activeSubscriptions = DB::table('user_preferences')
            ->where('is_active', true)
            ->count();

        $totalUsers = User::whereIn('type', ['pm', 'owner'])->count();
        $arpu = $totalUsers > 0 ? round($mrr / $totalUsers, 2) : 0;

        return [
            'mrr' => round($mrr, 2),
            'arr' => round($mrr * 12, 2),
            'active_subscriptions' => $activeSubscriptions,
            'average_revenue_per_user' => $arpu,
        ];
    }

    /**
     * Get engagement statistics.
     */
    private function getEngagementStats()
    {
        // Template downloads this month
        $templateDownloadsThisMonth = TemplateDownload::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Calculator usage this month
        $calculatorUsageThisMonth = CalculatorLog::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            'average_lead_view_time' => 0, // Placeholder - would need tracking
            'average_response_time_hours' => 0, // Placeholder - would need tracking
            'template_downloads_this_month' => $templateDownloadsThisMonth,
            'calculator_uses_this_month' => $calculatorUsageThisMonth,
        ];
    }

    /**
     * Get recent activity.
     */
    private function getRecentActivity()
    {
        $recentLeads = Lead::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'property_type' => $lead->property_type ?? 'N/A',
                    'state' => $lead->state ?? 'N/A',
                    'created_at' => $lead->created_at->toISOString(),
                ];
            });

        $recentUsers = User::whereIn('type', ['pm', 'owner'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => $user->type,
                    'created_at' => $user->created_at->toISOString(),
                ];
            });

        return [
            'recent_leads' => $recentLeads,
            'recent_users' => $recentUsers,
        ];
    }

    /**
     * Get growth trends (last 6 months).
     */
    private function getGrowthTrends()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');

            $leads = Lead::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $users = User::whereIn('type', ['pm', 'owner'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $months[] = [
                'month' => $monthLabel,
                'leads' => $leads,
                'users' => $users,
            ];
        }

        return $months;
    }

    /**
     * Clear the dashboard cache.
     *
     * POST /api/admin/dashboard/clear-cache
     */
    public function clearCache()
    {
        Cache::forget('admin_dashboard_stats');

        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache cleared successfully.',
        ]);
    }
}
