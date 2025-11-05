<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StateLawContent;
use App\Models\StateProfile;
use App\Models\LegalTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class StateLawContentController extends Controller
{
    /**
     * Display a listing of state law contents.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = StateLawContent::with(['stateProfile', 'legalTopic']);

            // Filter by state_code if provided
            if ($request->has('state_code')) {
                $query->where('state_code', strtoupper($request->state_code));
            }

            // Filter by topic_slug if provided
            if ($request->has('topic_slug')) {
                $query->where('topic_slug', $request->topic_slug);
            }

            $contents = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $contents,
                'message' => 'State law contents retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve state law contents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created state law content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'state_code' => 'required|string|size:2|exists:state_profiles,state_code',
                'topic_slug' => 'required|string|exists:legal_topics,slug',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'summary' => 'nullable|string',
                'meta_description' => 'nullable|string|max:160',
                'is_published' => 'nullable|boolean',
                'official_link' => 'nullable|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $content = StateLawContent::create($request->all());

            // Clear cache
            Cache::flush();

            // Load relationships
            $content->load(['stateProfile', 'legalTopic']);

            return response()->json([
                'success' => true,
                'data' => $content,
                'message' => 'State law content created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create state law content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified state law content.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $content = StateLawContent::with(['stateProfile', 'legalTopic'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $content,
                'message' => 'State law content retrieved successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'State law content not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve state law content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified state law content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $content = StateLawContent::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'state_code' => 'required|string|size:2|exists:state_profiles,state_code',
                'topic_slug' => 'required|string|exists:legal_topics,slug',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'summary' => 'nullable|string',
                'meta_description' => 'nullable|string|max:160',
                'is_published' => 'nullable|boolean',
                'official_link' => 'nullable|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $content->update($request->all());

            // Clear cache
            Cache::flush();

            // Load relationships
            $content->load(['stateProfile', 'legalTopic']);

            return response()->json([
                'success' => true,
                'data' => $content,
                'message' => 'State law content updated successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'State law content not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update state law content',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified state law content.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $content = StateLawContent::findOrFail($id);
            $content->delete();

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'State law content deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'State law content not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete state law content',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
