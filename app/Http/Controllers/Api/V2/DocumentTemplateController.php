<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DocumentTemplateController extends Controller
{
    /**
     * Get all active templates with filtering.
     *
     * GET /api/v2/templates
     */
    public function getTemplates(Request $request)
    {
        $state = $request->input('state');
        $category = $request->input('category');
        $search = $request->input('search');
        $isFree = $request->input('is_free');
        $perPage = $request->input('per_page', 15);

        $cacheKey = 'templates_' . md5(serialize([
            'state' => $state,
            'category' => $category,
            'search' => $search,
            'is_free' => $isFree,
            'per_page' => $perPage,
            'page' => $request->input('page', 1),
        ]));

        $templates = Cache::remember($cacheKey, 3600, function () use ($state, $category, $search, $isFree, $perPage) {
            $query = DocumentTemplate::active()
                ->with(['stateProfile', 'documentCategory']);

            // Filter by state
            if ($state) {
                $query->byState($state);
            }

            // Filter by category
            if ($category) {
                $query->byCategory($category);
            }

            // Filter by free status
            if ($isFree !== null) {
                if (filter_var($isFree, FILTER_VALIDATE_BOOLEAN)) {
                    $query->free();
                }
            }

            // Search by title or description
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            return $query->ordered()->paginate($perPage);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'templates' => $templates->map(function ($template) {
                    return [
                        'id' => $template->id,
                        'title' => $template->title,
                        'description' => $template->description,
                        'state_code' => $template->state_code,
                        'state_name' => $template->stateProfile ? $template->stateProfile->name : null,
                        'category_slug' => $template->category_slug,
                        'category_name' => $template->documentCategory ? $template->documentCategory->name : null,
                        'file_size_mb' => $template->file_size_mb,
                        'download_count' => $template->download_count,
                        'is_free' => $template->is_free,
                        'requires_signup' => $template->requires_signup,
                        'tags' => $template->tags,
                    ];
                }),
                'pagination' => [
                    'current_page' => $templates->currentPage(),
                    'per_page' => $templates->perPage(),
                    'total' => $templates->total(),
                    'last_page' => $templates->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get single template details.
     *
     * GET /api/v2/templates/{id}
     */
    public function getTemplate($id)
    {
        $cacheKey = "template_{$id}";

        $template = Cache::remember($cacheKey, 3600, function () use ($id) {
            return DocumentTemplate::active()
                ->with(['stateProfile', 'documentCategory'])
                ->findOrFail($id);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $template->id,
                'title' => $template->title,
                'description' => $template->description,
                'state' => $template->stateProfile ? [
                    'code' => $template->stateProfile->state_code,
                    'name' => $template->stateProfile->name,
                    'slug' => $template->stateProfile->slug,
                ] : null,
                'category' => $template->documentCategory ? [
                    'slug' => $template->documentCategory->slug,
                    'name' => $template->documentCategory->name,
                    'description' => $template->documentCategory->description,
                ] : null,
                'file_size_mb' => $template->file_size_mb,
                'download_count' => $template->download_count,
                'is_free' => $template->is_free,
                'requires_signup' => $template->requires_signup,
                'tags' => $template->tags,
            ],
        ]);
    }

    /**
     * Track download and return file path.
     *
     * POST /api/v2/templates/{id}/download
     */
    public function download(Request $request, $id)
    {
        // Template is already loaded and validated by middleware
        $template = $request->input('template');

        // Record the download with full tracking
        $userId = Auth::id();
        $download = $template->recordDownload($userId, $request);

        // Clear cache for this template
        Cache::forget("template_{$id}");
        Cache::forget('templates_list');

        // In production, generate a signed URL that expires
        return response()->json([
            'success' => true,
            'data' => [
                'download_id' => $download->id,
                'file_path' => $template->file_path,
                'file_name' => basename($template->file_path),
                'file_size_mb' => $template->file_size_mb,
                'title' => $template->title,
                'download_count' => $template->download_count,
                'message' => 'Download recorded successfully.',
            ],
        ]);
    }

    /**
     * Get all active categories with template counts.
     *
     * GET /api/v2/templates/categories
     */
    public function getCategories()
    {
        $categories = Cache::remember('template_categories', 3600, function () {
            return DocumentCategory::active()
                ->ordered()
                ->with(['documentTemplates' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->get()
                ->map(function ($category) {
                    return [
                        'slug' => $category->slug,
                        'name' => $category->name,
                        'description' => $category->description,
                        'template_count' => $category->documentTemplates->count(),
                    ];
                })
                ->filter(function ($category) {
                    return $category['template_count'] > 0;
                })
                ->values();
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
