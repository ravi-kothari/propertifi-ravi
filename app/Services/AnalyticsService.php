<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\UserLeads;
use App\Models\LeadResponse;
use App\Models\TemplateDownload;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get lead distribution statistics for a date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getLeadStats($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();
        $distributedLeads = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->where('distribution_count', '>', 0)
            ->count();

        $leadsByStatus = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $leadsByPropertyType = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->select('property_type', DB::raw('count(*) as count'))
            ->groupBy('property_type')
            ->orderByDesc('count')
            ->get();

        $averageQualityScore = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('quality_score')
            ->avg('quality_score');

        return [
            'total_leads' => $totalLeads,
            'distributed_leads' => $distributedLeads,
            'distribution_rate' => $totalLeads > 0 ? round(($distributedLeads / $totalLeads) * 100, 2) : 0,
            'leads_by_status' => $leadsByStatus,
            'leads_by_property_type' => $leadsByPropertyType,
            'average_quality_score' => round($averageQualityScore ?? 0, 2),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get property manager performance metrics.
     *
     * @param int $pmId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getPropertyManagerPerformance($pmId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $leadsReceived = UserLeads::where('pm_id', $pmId)
            ->whereBetween('distributed_at', [$startDate, $endDate])
            ->count();

        $leadsViewed = UserLeads::where('pm_id', $pmId)
            ->whereBetween('distributed_at', [$startDate, $endDate])
            ->whereNotNull('viewed_at')
            ->count();

        $responses = LeadResponse::where('pm_id', $pmId)
            ->whereBetween('responded_at', [$startDate, $endDate])
            ->get();

        $responsesByType = $responses->groupBy('response_type')
            ->map(function ($group) {
                return $group->count();
            });

        $averageResponseTime = UserLeads::where('pm_id', $pmId)
            ->whereBetween('distributed_at', [$startDate, $endDate])
            ->whereNotNull('viewed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, distributed_at, viewed_at)) as avg_hours')
            ->value('avg_hours');

        $averageMatchScore = UserLeads::where('pm_id', $pmId)
            ->whereBetween('distributed_at', [$startDate, $endDate])
            ->avg('match_score');

        return [
            'pm_id' => $pmId,
            'leads_received' => $leadsReceived,
            'leads_viewed' => $leadsViewed,
            'view_rate' => $leadsReceived > 0 ? round(($leadsViewed / $leadsReceived) * 100, 2) : 0,
            'total_responses' => $responses->count(),
            'response_rate' => $leadsReceived > 0 ? round(($responses->count() / $leadsReceived) * 100, 2) : 0,
            'responses_by_type' => $responsesByType,
            'positive_responses' => $responses->filter(fn($r) => $r->isPositive())->count(),
            'average_response_time_hours' => round($averageResponseTime ?? 0, 2),
            'average_match_score' => round($averageMatchScore ?? 0, 2),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get conversion funnel statistics.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getConversionFunnel($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $totalLeads = Lead::whereBetween('created_at', [$startDate, $endDate])->count();

        $distributedLeads = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->where('distribution_count', '>', 0)
            ->count();

        $viewedLeads = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->where('viewed_count', '>', 0)
            ->count();

        $respondedLeads = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('responses')
            ->count();

        $positiveResponses = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('responses', function ($query) {
                $query->whereIn('response_type', ['interested', 'contact_requested']);
            })
            ->count();

        return [
            'funnel' => [
                [
                    'stage' => 'Total Leads',
                    'count' => $totalLeads,
                    'percentage' => 100,
                ],
                [
                    'stage' => 'Distributed',
                    'count' => $distributedLeads,
                    'percentage' => $totalLeads > 0 ? round(($distributedLeads / $totalLeads) * 100, 2) : 0,
                ],
                [
                    'stage' => 'Viewed',
                    'count' => $viewedLeads,
                    'percentage' => $totalLeads > 0 ? round(($viewedLeads / $totalLeads) * 100, 2) : 0,
                ],
                [
                    'stage' => 'Responded',
                    'count' => $respondedLeads,
                    'percentage' => $totalLeads > 0 ? round(($respondedLeads / $totalLeads) * 100, 2) : 0,
                ],
                [
                    'stage' => 'Positive Response',
                    'count' => $positiveResponses,
                    'percentage' => $totalLeads > 0 ? round(($positiveResponses / $totalLeads) * 100, 2) : 0,
                ],
            ],
            'conversion_rates' => [
                'lead_to_distributed' => $totalLeads > 0 ? round(($distributedLeads / $totalLeads) * 100, 2) : 0,
                'distributed_to_viewed' => $distributedLeads > 0 ? round(($viewedLeads / $distributedLeads) * 100, 2) : 0,
                'viewed_to_responded' => $viewedLeads > 0 ? round(($respondedLeads / $viewedLeads) * 100, 2) : 0,
                'responded_to_positive' => $respondedLeads > 0 ? round(($positiveResponses / $respondedLeads) * 100, 2) : 0,
            ],
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get template download statistics.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getTemplateStats($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $totalDownloads = TemplateDownload::whereBetween('downloaded_at', [$startDate, $endDate])->count();

        $uniqueUsers = TemplateDownload::whereBetween('downloaded_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->whereNotNull('user_id')
            ->count('user_id');

        $topTemplates = TemplateDownload::whereBetween('downloaded_at', [$startDate, $endDate])
            ->select('template_id', DB::raw('count(*) as downloads'))
            ->groupBy('template_id')
            ->orderByDesc('downloads')
            ->limit(10)
            ->with('template:id,title,category_slug,is_free')
            ->get();

        $downloadsByCategory = TemplateDownload::whereBetween('downloaded_at', [$startDate, $endDate])
            ->join('document_templates', 'template_downloads.template_id', '=', 'document_templates.id')
            ->join('document_categories', 'document_templates.category_slug', '=', 'document_categories.slug')
            ->select('document_categories.name as category', DB::raw('count(*) as downloads'))
            ->groupBy('document_categories.name')
            ->orderByDesc('downloads')
            ->get();

        $freeVsPaid = TemplateDownload::whereBetween('downloaded_at', [$startDate, $endDate])
            ->join('document_templates', 'template_downloads.template_id', '=', 'document_templates.id')
            ->select('document_templates.is_free', DB::raw('count(*) as downloads'))
            ->groupBy('document_templates.is_free')
            ->get()
            ->pluck('downloads', 'is_free');

        return [
            'total_downloads' => $totalDownloads,
            'unique_users' => $uniqueUsers,
            'top_templates' => $topTemplates,
            'downloads_by_category' => $downloadsByCategory,
            'free_vs_paid' => [
                'free' => $freeVsPaid[1] ?? 0,
                'paid' => $freeVsPaid[0] ?? 0,
            ],
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get dashboard overview statistics.
     *
     * @return array
     */
    public function getDashboardOverview()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'leads' => [
                'today' => Lead::whereDate('created_at', $today)->count(),
                'this_month' => Lead::where('created_at', '>=', $thisMonth)->count(),
                'total' => Lead::count(),
                'pending' => Lead::where('status', 'pending')->count(),
            ],
            'distributions' => [
                'today' => UserLeads::whereDate('distributed_at', $today)->count(),
                'this_month' => UserLeads::where('distributed_at', '>=', $thisMonth)->count(),
                'total' => UserLeads::count(),
            ],
            'responses' => [
                'today' => LeadResponse::whereDate('responded_at', $today)->count(),
                'this_month' => LeadResponse::where('responded_at', '>=', $thisMonth)->count(),
                'total' => LeadResponse::count(),
                'positive_rate' => $this->getPositiveResponseRate(),
            ],
            'templates' => [
                'total_downloads' => TemplateDownload::count(),
                'downloads_today' => TemplateDownload::whereDate('downloaded_at', $today)->count(),
                'downloads_this_month' => TemplateDownload::where('downloaded_at', '>=', $thisMonth)->count(),
            ],
            'property_managers' => [
                'total_active' => User::where('role', 'pm')->where('is_active', true)->count(),
                'with_preferences' => User::where('role', 'pm')
                    ->whereHas('userPreferences', function ($q) {
                        $q->where('is_active', true);
                    })->count(),
            ],
        ];
    }

    /**
     * Get top performing property managers.
     *
     * @param int $limit
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getTopPerformingPMs($limit = 10, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        return User::where('role', 'pm')
            ->select('users.*')
            ->withCount([
                'userLeads as leads_received' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('distributed_at', [$startDate, $endDate]);
                },
                'leadResponses as responses_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('responded_at', [$startDate, $endDate]);
                },
            ])
            ->having('leads_received', '>', 0)
            ->orderByDesc('responses_count')
            ->limit($limit)
            ->get()
            ->map(function ($pm) use ($startDate, $endDate) {
                $responseRate = $pm->leads_received > 0
                    ? round(($pm->responses_count / $pm->leads_received) * 100, 2)
                    : 0;

                return [
                    'pm_id' => $pm->id,
                    'name' => $pm->name,
                    'email' => $pm->email,
                    'leads_received' => $pm->leads_received,
                    'responses_count' => $pm->responses_count,
                    'response_rate' => $responseRate,
                ];
            });
    }

    /**
     * Calculate positive response rate.
     *
     * @return float
     */
    protected function getPositiveResponseRate()
    {
        $totalResponses = LeadResponse::count();

        if ($totalResponses === 0) {
            return 0;
        }

        $positiveResponses = LeadResponse::whereIn('response_type', ['interested', 'contact_requested'])->count();

        return round(($positiveResponses / $totalResponses) * 100, 2);
    }

    /**
     * Get time-series data for leads.
     *
     * @param string $period (daily, weekly, monthly)
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getLeadTimeSeries($period = 'daily', $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        $dateFormat = match($period) {
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%U',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $leads = Lead::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, count(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $distributions = UserLeads::whereBetween('distributed_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(distributed_at, '{$dateFormat}') as period, count(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $responses = LeadResponse::whereBetween('responded_at', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(responded_at, '{$dateFormat}') as period, count(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'period_type' => $period,
            'leads' => $leads,
            'distributions' => $distributions,
            'responses' => $responses,
            'date_range' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }
}
