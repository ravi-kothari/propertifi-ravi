<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\UserLeads;
use App\Services\LeadDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadDistributionController extends Controller
{
    protected $distributionService;

    public function __construct(LeadDistributionService $distributionService)
    {
        $this->distributionService = $distributionService;
    }

    /**
     * Find matching property managers for a lead.
     *
     * GET /api/v2/leads/{id}/matches
     */
    public function findMatches($id, Request $request)
    {
        $lead = Lead::findOrFail($id);

        $limit = $request->input('limit', 5);
        $matches = $this->distributionService->findMatchingPropertyManagers($lead, $limit);

        return response()->json([
            'success' => true,
            'data' => [
                'lead_id' => $lead->id,
                'total_matches' => $matches->count(),
                'matches' => $matches->map(function ($match) {
                    return [
                        'pm_id' => $match['user_id'],
                        'pm_name' => $match['user']->name ?? 'N/A',
                        'pm_email' => $match['user']->email ?? 'N/A',
                        'tier' => $match['tier']->name ?? 'N/A',
                        'tier_priority' => $match['tier']->priority ?? 0,
                        'match_score' => $match['score'],
                        'preferences' => [
                            'property_types' => $match['preference']->property_types,
                            'min_units' => $match['preference']->min_units,
                            'max_units' => $match['preference']->max_units,
                            'zip_codes' => $match['preference']->zip_codes,
                            'service_radius_miles' => $match['preference']->service_radius_miles,
                        ],
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * Distribute a lead to property managers.
     *
     * POST /api/v2/leads/{id}/distribute
     */
    public function distribute($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:20',
            'auto_notify' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $lead = Lead::findOrFail($id);

        // Check if lead is already distributed
        if ($lead->distribution_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Lead has already been distributed. Use re-distribute endpoint to distribute again.',
            ], 400);
        }

        $options = [
            'limit' => $request->input('limit', 5),
            'auto_notify' => $request->input('auto_notify', true),
        ];

        $result = $this->distributionService->distributeLead($lead, $options);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Re-distribute a lead to additional property managers.
     *
     * POST /api/v2/leads/{id}/redistribute
     */
    public function redistribute($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:20',
            'auto_notify' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $lead = Lead::findOrFail($id);

        $options = [
            'limit' => $request->input('limit', 5),
            'auto_notify' => $request->input('auto_notify', true),
        ];

        $result = $this->distributionService->distributeLead($lead, $options);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get distribution statistics for a lead.
     *
     * GET /api/v2/leads/{id}/distribution-stats
     */
    public function getStats($id)
    {
        $lead = Lead::findOrFail($id);

        $stats = $this->distributionService->getDistributionStats($lead);

        return response()->json([
            'success' => true,
            'data' => [
                'lead_id' => $lead->id,
                'lead_status' => $lead->status,
                'quality_score' => $lead->quality_score,
                'distribution_count' => $lead->distribution_count,
                'last_distributed_at' => $lead->last_distributed_at,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get leads distributed to a specific property manager.
     *
     * GET /api/v2/property-managers/{pmId}/leads
     */
    public function getPropertyManagerLeads($pmId, Request $request)
    {
        $status = $request->input('status'); // pending, viewed, responded
        $perPage = $request->input('per_page', 15);

        $query = UserLeads::where('pm_id', $pmId)
            ->with(['lead', 'responses']);

        if ($status) {
            $query->where('status', $status);
        }

        $userLeads = $query->orderBy('distributed_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'leads' => $userLeads->map(function ($userLead) {
                    return [
                        'user_lead_id' => $userLead->id,
                        'lead_id' => $userLead->lead_id,
                        'match_score' => $userLead->match_score,
                        'status' => $userLead->status,
                        'distributed_at' => $userLead->distributed_at,
                        'viewed_at' => $userLead->viewed_at,
                        'response_count' => $userLead->responses->count(),
                        'lead' => [
                            'property_type' => $userLead->lead->property_type,
                            'number_of_units' => $userLead->lead->number_of_units,
                            'address' => $userLead->lead->address,
                            'city' => $userLead->lead->city,
                            'state' => $userLead->lead->state,
                            'zipcode' => $userLead->lead->zipcode,
                            'price' => $userLead->lead->price,
                            'quality_score' => $userLead->lead->quality_score,
                        ],
                    ];
                }),
                'pagination' => [
                    'current_page' => $userLeads->currentPage(),
                    'per_page' => $userLeads->perPage(),
                    'total' => $userLeads->total(),
                    'last_page' => $userLeads->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Mark a lead as viewed by a property manager.
     *
     * POST /api/v2/property-managers/{pmId}/leads/{leadId}/view
     */
    public function markAsViewed($pmId, $leadId)
    {
        $userLead = UserLeads::where('pm_id', $pmId)
            ->where('lead_id', $leadId)
            ->firstOrFail();

        if (!$userLead->viewed_at) {
            $userLead->update([
                'status' => 'viewed',
                'viewed_at' => now(),
            ]);

            // Update lead viewed count
            $userLead->lead->markAsViewed();
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead marked as viewed.',
            'data' => [
                'viewed_at' => $userLead->viewed_at,
                'status' => $userLead->status,
            ],
        ]);
    }
}
