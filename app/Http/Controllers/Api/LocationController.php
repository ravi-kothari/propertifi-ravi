<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\Cities;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    /**
     * Get list of states with property managers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStates()
    {
        try {
            // Get all active US states
            $states = State::where('country_id', 231)
                ->where('status', 1)
                ->orderBy('state', 'ASC')
                ->get();

            // Transform data to match Next.js requirements
            $statesData = $states->map(function ($state) {
                // Generate slug from state name
                $slug = Str::slug($state->state);

                return [
                    'id' => $state->id,
                    'name' => $state->state,
                    'code' => $state->abbreviation,
                    'slug' => $slug
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $statesData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch states'
            ], 500);
        }
    }

    /**
     * Get cities by state code
     *
     * @param string $stateCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCitiesByState($stateCode)
    {
        try {
            // Find state by code (abbreviation)
            $state = State::where('country_id', 231)
                ->where('abbreviation', strtoupper($stateCode))
                ->where('status', 1)
                ->first();

            if (!$state) {
                return response()->json([
                    'success' => false,
                    'message' => 'State not found'
                ], 404);
            }

            // Get all cities for this state
            $cities = Cities::where('state_id', $state->id)
                ->where('status', 1)
                ->orderBy('city', 'ASC')
                ->get();

            // Transform data and add manager count
            $citiesData = [];
            foreach ($cities as $city) {
                // Count property managers in this city
                $managerCount = User::where('city', $city->id)
                    ->where('status', 1)
                    ->where('type', 'Agent')
                    ->count();

                // Only include cities with property managers
                if ($managerCount > 0) {
                    $citiesData[] = [
                        'id' => $city->id,
                        'name' => $city->city,
                        'slug' => $city->slug ?? Str::slug($city->city),
                        'state_code' => $state->abbreviation,
                        'manager_count' => $managerCount
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $citiesData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities'
            ], 500);
        }
    }
}
