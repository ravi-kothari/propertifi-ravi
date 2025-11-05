<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class LegalTopicController extends Controller
{
    /**
     * Display a listing of legal topics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $topics = LegalTopic::withCount('stateLawContents')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $topics,
                'message' => 'Legal topics retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve legal topics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created legal topic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $topic = LegalTopic::create($request->all());

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'data' => $topic,
                'message' => 'Legal topic created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create legal topic',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified legal topic.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $topic = LegalTopic::withCount('stateLawContents')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $topic,
                'message' => 'Legal topic retrieved successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Legal topic not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve legal topic',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified legal topic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $topic = LegalTopic::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $topic->update($request->all());

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'data' => $topic,
                'message' => 'Legal topic updated successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Legal topic not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update legal topic',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified legal topic.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $topic = LegalTopic::findOrFail($id);
            $topic->delete();

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Legal topic deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Legal topic not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete legal topic',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
