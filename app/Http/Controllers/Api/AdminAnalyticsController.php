<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lead;
use App\Models\UserLeads;
use App\Models\TemplateDownload;
use App\Models\CalculatorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminAnalyticsController extends Controller
{
    /**
     * Get lead analytics with filtering.
     *
     * GET /api/admin/analytics/leads
     */
    public function leadAnalytics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'state' => 'nullable|string',
            'property_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Lead::query();

        // Apply filters
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->state) {
            $query->where('state', $request->state);
        }
        if ($request->property_type) {
            $query->where('property_type', $request->property_type);
        }

        $analytics = [
            'summary' => [
                'total_leads' => $query->count(),
                'average_quality_score' => round($query->avg('quality_score') ?? 0, 2),
                'total_distributed' => $query->where('distribution_count', '>', 0)->count(),
                'total_undistributed' => $query->where('distribution_count', 0)->count(),
            ],
            'by_state' => (clone $query)->select('state', DB::raw('count(*) as count'))
                ->groupBy('state')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'by_property_type' => (clone $query)->select('property_type', DB::raw('count(*) as count'))
                ->groupBy('property_type')
                ->orderBy('count', 'desc')
                ->get(),
            'by_source' => (clone $query)->select('source', DB::raw('count(*) as count'))
                ->whereNotNull('source')
                ->groupBy('source')
                ->orderBy('count', 'desc')
                ->get(),
            'quality_distribution' => [
                'high' => (clone $query)->where('quality_score', '>=', 70)->count(),
                'medium' => (clone $query)->whereBetween('quality_score', [40, 69])->count(),
                'low' => (clone $query)->where('quality_score', '<', 40)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'filters_applied' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'state' => $request->state,
                'property_type' => $request->property_type,
            ],
        ]);
    }

    /**
     * Get property manager performance analytics.
     *
     * GET /api/admin/analytics/property-managers
     */
    public function propertyManagerAnalytics(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        $pmAnalytics = User::where('type', User::TYPE_PM)
            ->leftJoin('user_leads', 'users.id', '=', 'user_leads.pm_id')
            ->leftJoin('user_preferences', 'users.id', '=', 'user_preferences.user_id')
            ->leftJoin('tiers', 'user_preferences.tier_id', '=', 'tiers.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.company_name',
                'users.is_verified',
                'tiers.name as tier_name',
                DB::raw('COUNT(DISTINCT user_leads.id) as total_leads'),
                DB::raw('COUNT(DISTINCT CASE WHEN user_leads.viewed_at IS NOT NULL THEN user_leads.id END) as viewed_leads'),
                DB::raw('COUNT(DISTINCT CASE WHEN user_leads.status = "responded" THEN user_leads.id END) as responded_leads'),
                DB::raw('ROUND(AVG(user_leads.match_score), 2) as avg_match_score')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.company_name', 'users.is_verified', 'tiers.name')
            ->orderBy('total_leads', 'desc')
            ->paginate($perPage);

        // Calculate rates for each PM
        $pmAnalytics->getCollection()->transform(function ($pm) {
            $pm->view_rate = $pm->total_leads > 0
                ? round(($pm->viewed_leads / $pm->total_leads) * 100, 2)
                : 0;
            $pm->response_rate = $pm->total_leads > 0
                ? round(($pm->responded_leads / $pm->total_leads) * 100, 2)
                : 0;
            return $pm;
        });

        return response()->json([
            'success' => true,
            'data' => $pmAnalytics,
        ]);
    }

    /**
     * Get template download analytics.
     *
     * GET /api/admin/analytics/templates
     */
    public function templateAnalytics(Request $request)
    {
        $popularTemplates = DB::table('template_downloads')
            ->join('document_templates', 'template_downloads.template_id', '=', 'document_templates.id')
            ->select(
                'document_templates.id',
                'document_templates.title',
                'document_templates.state_code',
                'document_templates.category_slug',
                DB::raw('COUNT(*) as download_count'),
                DB::raw('COUNT(DISTINCT template_downloads.user_id) as unique_downloaders')
            )
            ->groupBy('document_templates.id', 'document_templates.title', 'document_templates.state_code', 'document_templates.category_slug')
            ->orderBy('download_count', 'desc')
            ->limit(20)
            ->get();

        $downloadsByState = DB::table('template_downloads')
            ->join('document_templates', 'template_downloads.template_id', '=', 'document_templates.id')
            ->select('document_templates.state_code', DB::raw('COUNT(*) as count'))
            ->whereNotNull('document_templates.state_code')
            ->groupBy('document_templates.state_code')
            ->orderBy('count', 'desc')
            ->get();

        $downloadsByCategory = DB::table('template_downloads')
            ->join('document_templates', 'template_downloads.template_id', '=', 'document_templates.id')
            ->select('document_templates.category_slug', DB::raw('COUNT(*) as count'))
            ->whereNotNull('document_templates.category_slug')
            ->groupBy('document_templates.category_slug')
            ->orderBy('count', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'popular_templates' => $popularTemplates,
                'downloads_by_state' => $downloadsByState,
                'downloads_by_category' => $downloadsByCategory,
                'total_downloads' => TemplateDownload::count(),
                'unique_users' => TemplateDownload::distinct('user_id')->count('user_id'),
            ],
        ]);
    }

    /**
     * Get calculator usage analytics.
     *
     * GET /api/admin/analytics/calculators
     */
    public function calculatorAnalytics(Request $request)
    {
        $usageByType = CalculatorLog::select('calculator_type', DB::raw('COUNT(*) as count'))
            ->groupBy('calculator_type')
            ->get();

        $dailyUsage = CalculatorLog::whereDate('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $authenticatedVsGuest = [
            'authenticated' => CalculatorLog::whereNotNull('user_id')->count(),
            'guest' => CalculatorLog::whereNull('user_id')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'usage_by_type' => $usageByType,
                'daily_usage_last_30_days' => $dailyUsage,
                'authenticated_vs_guest' => $authenticatedVsGuest,
                'total_usage' => CalculatorLog::count(),
                'unique_users' => CalculatorLog::whereNotNull('user_id')
                    ->distinct('user_id')
                    ->count('user_id'),
            ],
        ]);
    }

    /**
     * Get revenue analytics by tier.
     *
     * GET /api/admin/analytics/revenue
     */
    public function revenueAnalytics(Request $request)
    {
        $activeSubscriptions = DB::table('users')
            ->join('user_preferences', 'users.id', '=', 'user_preferences.user_id')
            ->join('tiers', 'user_preferences.tier_id', '=', 'tiers.id')
            ->where('user_preferences.is_active', true)
            ->select(
                'tiers.id',
                'tiers.name',
                'tiers.title',
                'tiers.price',
                DB::raw('COUNT(*) as subscriber_count'),
                DB::raw('SUM(tiers.price) as monthly_revenue')
            )
            ->groupBy('tiers.id', 'tiers.name', 'tiers.title', 'tiers.price')
            ->orderBy('tiers.priority', 'desc')
            ->get();

        $totalMRR = $activeSubscriptions->sum('monthly_revenue');
        $totalARR = $totalMRR * 12;

        // Subscription growth (last 6 months)
        $subscriptionGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = DB::table('user_preferences')
                ->where('is_active', true)
                ->whereYear('created_at', '<=', $date->year)
                ->whereMonth('created_at', '<=', $date->month)
                ->count();

            $subscriptionGrowth[] = [
                'month' => $date->format('M Y'),
                'active_subscriptions' => $count,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'mrr' => round($totalMRR, 2),
                'arr' => round($totalARR, 2),
                'subscriptions_by_tier' => $activeSubscriptions,
                'subscription_growth' => $subscriptionGrowth,
                'average_revenue_per_user' => $activeSubscriptions->sum('subscriber_count') > 0
                    ? round($totalMRR / $activeSubscriptions->sum('subscriber_count'), 2)
                    : 0,
            ],
        ]);
    }

    /**
     * Export analytics data as CSV.
     *
     * GET /api/admin/analytics/export
     */
    public function exportAnalytics(Request $request)
    {
        $type = $request->input('type', 'leads'); // leads, users, templates, calculators

        // This would generate a CSV file - placeholder implementation
        return response()->json([
            'success' => true,
            'message' => "Export for {$type} analytics initiated. Download link will be sent to your email.",
        ]);
    }
}
