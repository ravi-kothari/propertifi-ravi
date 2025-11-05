<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StateProfileController extends Controller
{
    /**
     * Display a listing of state profiles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $profiles = StateProfile::withCount('lawContents')
                ->orderBy('name')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $profiles,
                'message' => 'State profiles retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve state profiles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created state profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'state_code' => 'required|string|size:2|unique:state_profiles,state_code',
                'name' => 'required|string|max:100',
                'abbreviation' => 'required|string|size:2',
                'overview' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Auto-generate slug from name if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($request->name);
            }

            $profile = StateProfile::create($data);

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'data' => $profile,
                'message' => 'State profile created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create state profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified state profile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $profile = StateProfile::withCount('lawContents')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $profile,
                'message' => 'State profile retrieved successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'State profile not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve state profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified state profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $profile = StateProfile::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'state_code' => [
                    'required',
                    'string',
                    'size:2',
                    Rule::unique('state_profiles')->ignore($profile->id)
                ],
                'name' => 'required|string|max:100',
                'abbreviation' => 'required|string|size:2',
                'overview' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Auto-generate slug from name if not provided
            if (empty($data['slug']) && $request->has('name')) {
                $data['slug'] = Str::slug($request->name);
            }

            $profile->update($data);

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'data' => $profile,
                'message' => 'State profile updated successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'State profile not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update state profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified state profile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $profile = StateProfile::findOrFail($id);
            $profile->delete();

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'State profile deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'State profile not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete state profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
