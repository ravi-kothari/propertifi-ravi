<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadResponse;
use App\Models\UserLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LeadResponseController extends Controller
{
    /**
     * Submit a response to a lead.
     *
     * POST /api/v2/leads/{leadId}/responses
     */
    public function submitResponse(Request $request, $leadId)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'response_type' => 'required|in:contact_info,availability,price_quote,decline',
            'message' => 'required|string|min:10|max:1000',
            'contact_info' => 'nullable|array',
            'contact_info.phone' => 'nullable|string|min:10',
            'contact_info.email' => 'nullable|email',
            'contact_info.preferred_time' => 'nullable|string',
            'contact_info.notes' => 'nullable|string|max:500',
            'availability' => 'nullable|array',
            'availability.date' => 'nullable|string',
            'availability.time' => 'nullable|string',
            'availability.location' => 'nullable|string|max:200',
            'availability.notes' => 'nullable|string|max:500',
            'price_quote' => 'nullable|array',
            'price_quote.amount' => 'nullable|numeric|min:0',
            'price_quote.frequency' => 'nullable|in:monthly,yearly,one-time',
            'price_quote.details' => 'nullable|string|min:10|max:1000',
            'price_quote.includes' => 'nullable|array',
            'price_quote.valid_until' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify lead exists
        $lead = Lead::findOrFail($leadId);

        // Get authenticated user (PM)
        $pmId = Auth::id();

        // Find the UserLead relationship
        $userLead = UserLeads::where('lead_id', $leadId)
            ->where('user_id', $pmId)
            ->first();

        if (!$userLead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lead.',
            ], 403);
        }

        // Transform frontend nested structure to backend flat structure
        $responseData = [
            'user_lead_id' => $userLead->id,
            'pm_id' => $pmId,
            'lead_id' => $leadId,
            'response_type' => $request->response_type,
            'message' => $request->message,
            'responded_at' => now(),
        ];

        // Handle contact_info type
        if ($request->response_type === 'contact_info' && $request->has('contact_info')) {
            $contactInfo = $request->contact_info;
            $responseData['contact_phone'] = $contactInfo['phone'] ?? null;
            $responseData['contact_email'] = $contactInfo['email'] ?? null;
            $responseData['notes'] = $contactInfo['notes'] ?? null;
        }

        // Handle availability type
        if ($request->response_type === 'availability' && $request->has('availability')) {
            $availability = $request->availability;
            // Store as JSON in the availability field
            $responseData['availability'] = json_encode([
                'date' => $availability['date'] ?? null,
                'time' => $availability['time'] ?? null,
                'location' => $availability['location'] ?? null,
                'notes' => $availability['notes'] ?? null,
            ]);
        }

        // Handle price_quote type
        if ($request->response_type === 'price_quote' && $request->has('price_quote')) {
            $priceQuote = $request->price_quote;
            $responseData['quoted_price'] = $priceQuote['amount'] ?? null;
            // Store full quote details in notes
            $quoteDetails = [
                'frequency' => $priceQuote['frequency'] ?? null,
                'details' => $priceQuote['details'] ?? null,
                'includes' => $priceQuote['includes'] ?? [],
                'valid_until' => $priceQuote['valid_until'] ?? null,
            ];
            $responseData['notes'] = json_encode($quoteDetails);
        }

        // Create the response
        $leadResponse = LeadResponse::create($responseData);

        // Update UserLead status to 'responded'
        $userLead->update([
            'status' => 'responded',
        ]);

        // Transform response back to frontend format
        $transformedResponse = $this->transformResponseForFrontend($leadResponse);

        return response()->json([
            'success' => true,
            'message' => 'Response submitted successfully',
            'data' => $transformedResponse,
        ], 201);
    }

    /**
     * Get all responses for a lead.
     *
     * GET /api/v2/leads/{leadId}/responses
     */
    public function getResponses($leadId)
    {
        // Verify lead exists
        $lead = Lead::findOrFail($leadId);

        // Get authenticated user
        $pmId = Auth::id();

        // Verify user has access to this lead
        $userLead = UserLeads::where('lead_id', $leadId)
            ->where('user_id', $pmId)
            ->first();

        if (!$userLead) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lead.',
            ], 403);
        }

        // Get all responses for this lead by this PM
        $responses = LeadResponse::where('lead_id', $leadId)
            ->where('user_id', $pmId)
            ->with('propertyManager:id,name,email,company_name')
            ->orderBy('responded_at', 'desc')
            ->get();

        // Transform responses to frontend format
        $transformedResponses = $responses->map(function ($response) {
            return $this->transformResponseForFrontend($response);
        });

        return response()->json([
            'success' => true,
            'data' => $transformedResponses,
        ]);
    }

    /**
     * Transform backend LeadResponse to frontend format.
     *
     * @param LeadResponse $response
     * @return array
     */
    private function transformResponseForFrontend(LeadResponse $response)
    {
        $transformed = [
            'id' => $response->id,
            'lead_id' => $response->lead_id,
            'property_manager_id' => $response->pm_id,
            'response_type' => $response->response_type,
            'message' => $response->message,
            'created_at' => $response->responded_at?->toISOString() ?? $response->created_at->toISOString(),
            'updated_at' => $response->updated_at->toISOString(),
        ];

        // Transform contact_info
        if ($response->response_type === 'contact_info') {
            $transformed['contact_info'] = [
                'phone' => $response->contact_phone,
                'email' => $response->contact_email,
                'preferred_time' => null, // Not stored separately in backend
                'notes' => $response->notes,
            ];
        }

        // Transform availability
        if ($response->response_type === 'availability' && $response->availability) {
            $availability = is_string($response->availability)
                ? json_decode($response->availability, true)
                : $response->availability;

            $transformed['availability'] = $availability;
        }

        // Transform price_quote
        if ($response->response_type === 'price_quote') {
            $quoteDetails = $response->notes
                ? (is_string($response->notes) ? json_decode($response->notes, true) : $response->notes)
                : [];

            $transformed['price_quote'] = [
                'amount' => $response->quoted_price ? (float) $response->quoted_price : null,
                'frequency' => $quoteDetails['frequency'] ?? null,
                'details' => $quoteDetails['details'] ?? null,
                'includes' => $quoteDetails['includes'] ?? [],
                'valid_until' => $quoteDetails['valid_until'] ?? null,
            ];
        }

        return $transformed;
    }
}
