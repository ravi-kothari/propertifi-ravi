<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Leads;
use App\Mail\LeadSubmitMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    /**
     * Store a new lead from the multi-step form
     *
     * @param StoreLeadRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreLeadRequest $request)
    {
        try {
            // Generate unique confirmation number
            $confirmationNumber = 'LEAD-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

            // Prepare lead data
            $leadData = [
                'name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->street_address,
                'city' => $request->city,
                'state' => $request->state,
                'zipcode' => $request->zip_code,
                'property_type' => $request->property_type,
                'number_of_units' => $request->number_of_units,
                'square_footage' => $request->square_footage,
                'additional_services' => $request->additional_services ? json_encode($request->additional_services) : null,
                'preferred_contact' => $request->preferred_contact,
                'source' => $request->source ?? 'get-started-form',
                'confirmation_number' => $confirmationNumber,
                'status' => 1
            ];

            // Create lead
            $lead = Leads::create($leadData);

            // Generate unique ID (legacy format)
            $uniqueID = 'L' . str_pad($lead->id, 3, "0", STR_PAD_LEFT);
            $lead->update(['unique_id' => $uniqueID]);

            // TODO: Implement manager matching logic
            // For now, return a placeholder count
            $matchedManagersCount = $this->getMatchedManagersCount($request);

            // Send confirmation email
            if ($request->email && filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $emailData = [
                        'userData' => $lead,
                        'subject' => 'Thank you for reaching out!',
                        'confirmation_number' => $confirmationNumber
                    ];

                    Mail::to([strtolower(trim($request->email))])
                        ->bcc([
                            strtolower(env('BCC')),
                            strtolower(env('BCC2')),
                            strtolower(env('BCC3')),
                            strtolower(env('BCC4')),
                            strtolower(env('BCC5'))
                        ])
                        ->send(new LeadSubmitMail($emailData));
                } catch (\Exception $e) {
                    // Log email error but don't fail the request
                    \Log::error('Failed to send lead confirmation email: ' . $e->getMessage());
                }
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Your request has been submitted successfully',
                'lead_id' => $lead->id,
                'data' => [
                    'id' => $lead->id,
                    'confirmation_number' => $confirmationNumber,
                    'matched_managers_count' => $matchedManagersCount
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit lead. Please try again.'
            ], 500);
        }
    }

    /**
     * Get count of matched property managers
     * TODO: Implement actual matching logic based on:
     * - Property location (city, state, zipcode)
     * - Property type
     * - Services needed
     * - Manager availability and capacity
     *
     * @param StoreLeadRequest $request
     * @return int
     */
    private function getMatchedManagersCount($request)
    {
        // Placeholder implementation
        // In production, this should query the users/agents table
        // with filters for location, property type, services, etc.

        // For now, return a random number between 3-8 for demonstration
        return rand(3, 8);
    }
}
