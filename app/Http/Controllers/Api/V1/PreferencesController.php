<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PreferencesController extends Controller
{
    /**
     * Get current user's preferences
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Get user preferences
        $preferences = UserPreferences::where('user_id', $user->id)->first();

        // If no preferences exist, create default ones
        if (!$preferences) {
            $preferences = UserPreferences::create([
                'user_id' => $user->id,
                'is_active' => true,
                'email_notifications' => true,
                'sms_notifications' => false,
            ]);
        }

        // Get tier information
        $tier = $preferences->tier;

        return response()->json([
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile ?? '',
                'company_name' => $user->company_name ?? '',
                'bio' => $user->about ?? '',
            ],
            'leadCriteria' => [
                'property_types' => $preferences->property_types ?? [],
                'zip_codes' => $preferences->zip_codes ?? [],
                'min_units' => $preferences->min_units,
                'max_units' => $preferences->max_units,
                'service_radius_miles' => $preferences->service_radius_miles ?? 25,
                'latitude' => $preferences->latitude,
                'longitude' => $preferences->longitude,
            ],
            'notifications' => [
                'email_notifications' => $preferences->email_notifications,
                'sms_notifications' => $preferences->sms_notifications,
            ],
            'subscription' => [
                'tier_id' => $preferences->tier_id,
                'tier_name' => $tier->name ?? 'free',
                'exclusivity_hours' => $tier->exclusivity_hours ?? 0,
            ],
        ]);
    }

    /**
     * Update user preferences
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validation rules
        $validator = Validator::make($request->all(), [
            // Profile
            'profile.name' => 'sometimes|string|max:255',
            'profile.phone' => 'sometimes|string|nullable|max:20',
            'profile.company_name' => 'sometimes|string|nullable|max:255',
            'profile.bio' => 'sometimes|string|nullable|max:1000',

            // Lead Criteria
            'leadCriteria.property_types' => 'sometimes|array',
            'leadCriteria.property_types.*' => 'string|in:residential,commercial,industrial,mixed_use,land',
            'leadCriteria.zip_codes' => 'sometimes|array',
            'leadCriteria.zip_codes.*' => 'string|regex:/^\d{5}$/',
            'leadCriteria.min_units' => 'sometimes|nullable|integer|min:0',
            'leadCriteria.max_units' => 'sometimes|nullable|integer|min:0',
            'leadCriteria.service_radius_miles' => 'sometimes|integer|min:0|max:500',
            'leadCriteria.latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'leadCriteria.longitude' => 'sometimes|nullable|numeric|between:-180,180',

            // Notifications
            'notifications.email_notifications' => 'sometimes|boolean',
            'notifications.sms_notifications' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Custom validation: min_units should be less than max_units
        if ($request->has('leadCriteria.min_units') && $request->has('leadCriteria.max_units')) {
            if ($request->input('leadCriteria.min_units') > $request->input('leadCriteria.max_units')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['leadCriteria.min_units' => ['Min units cannot be greater than max units']]
                ], 400);
            }
        }

        // Update user profile
        if ($request->has('profile')) {
            $profileData = [];
            if ($request->has('profile.name')) {
                $profileData['name'] = $request->input('profile.name');
            }
            if ($request->has('profile.phone')) {
                $profileData['mobile'] = $request->input('profile.phone');
            }
            if ($request->has('profile.company_name')) {
                $profileData['company_name'] = $request->input('profile.company_name');
            }
            if ($request->has('profile.bio')) {
                $profileData['about'] = $request->input('profile.bio');
            }

            if (!empty($profileData)) {
                $user->update($profileData);
            }
        }

        // Get or create preferences
        $preferences = UserPreferences::firstOrCreate(
            ['user_id' => $user->id],
            [
                'is_active' => true,
                'email_notifications' => true,
                'sms_notifications' => false,
            ]
        );

        // Update lead criteria
        if ($request->has('leadCriteria')) {
            $criteriaData = [];

            if ($request->has('leadCriteria.property_types')) {
                $criteriaData['property_types'] = $request->input('leadCriteria.property_types');
            }
            if ($request->has('leadCriteria.zip_codes')) {
                $criteriaData['zip_codes'] = $request->input('leadCriteria.zip_codes');
            }
            if ($request->has('leadCriteria.min_units')) {
                $criteriaData['min_units'] = $request->input('leadCriteria.min_units');
            }
            if ($request->has('leadCriteria.max_units')) {
                $criteriaData['max_units'] = $request->input('leadCriteria.max_units');
            }
            if ($request->has('leadCriteria.service_radius_miles')) {
                $criteriaData['service_radius_miles'] = $request->input('leadCriteria.service_radius_miles');
            }
            if ($request->has('leadCriteria.latitude')) {
                $criteriaData['latitude'] = $request->input('leadCriteria.latitude');
            }
            if ($request->has('leadCriteria.longitude')) {
                $criteriaData['longitude'] = $request->input('leadCriteria.longitude');
            }

            if (!empty($criteriaData)) {
                $preferences->update($criteriaData);
            }
        }

        // Update notifications
        if ($request->has('notifications')) {
            $notificationData = [];

            if ($request->has('notifications.email_notifications')) {
                $notificationData['email_notifications'] = $request->input('notifications.email_notifications');
            }
            if ($request->has('notifications.sms_notifications')) {
                $notificationData['sms_notifications'] = $request->input('notifications.sms_notifications');
            }

            if (!empty($notificationData)) {
                $preferences->update($notificationData);
            }
        }

        // Refresh and return updated data
        $user->refresh();
        $preferences->refresh();
        $tier = $preferences->tier;

        return response()->json([
            'message' => 'Preferences updated successfully',
            'data' => [
                'profile' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->mobile ?? '',
                    'company_name' => $user->company_name ?? '',
                    'bio' => $user->about ?? '',
                ],
                'leadCriteria' => [
                    'property_types' => $preferences->property_types ?? [],
                    'zip_codes' => $preferences->zip_codes ?? [],
                    'min_units' => $preferences->min_units,
                    'max_units' => $preferences->max_units,
                    'service_radius_miles' => $preferences->service_radius_miles ?? 25,
                    'latitude' => $preferences->latitude,
                    'longitude' => $preferences->longitude,
                ],
                'notifications' => [
                    'email_notifications' => $preferences->email_notifications,
                    'sms_notifications' => $preferences->sms_notifications,
                ],
                'subscription' => [
                    'tier_id' => $preferences->tier_id,
                    'tier_name' => $tier->name ?? 'free',
                    'exclusivity_hours' => $tier->exclusivity_hours ?? 0,
                ],
            ]
        ]);
    }
}
