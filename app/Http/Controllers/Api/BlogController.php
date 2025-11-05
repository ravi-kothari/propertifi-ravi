<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blogs;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Get latest published blog posts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(Request $request)
    {
        try {
            // Get limit from query parameter, default to 6
            $limit = $request->input('limit', 6);

            // Validate limit is a positive integer
            if (!is_numeric($limit) || $limit < 1) {
                $limit = 6;
            }

            // Get latest published blogs
            $blogs = Blogs::where('status', 1)
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();

            // Transform data to match Next.js requirements
            $blogsData = $blogs->map(function ($blog) {
                return $this->transformBlogPost($blog, false);
            });

            return response()->json([
                'success' => true,
                'data' => $blogsData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog posts'
            ], 500);
        }
    }

    /**
     * Get blog posts with pagination and filtering
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 12);
            $category = $request->input('category');

            // Build query
            $query = Blogs::where('status', 1);

            // Filter by category if provided
            if ($category) {
                $query->where('blog_category_id', $category);
            }

            // Paginate results
            $blogs = $query->orderBy('id', 'DESC')->paginate($perPage);

            // Transform data
            $blogsData = $blogs->getCollection()->map(function ($blog) {
                return $this->transformBlogPost($blog, false);
            });

            return response()->json([
                'success' => true,
                'data' => $blogsData,
                'meta' => [
                    'total' => $blogs->total(),
                    'current_page' => $blogs->currentPage(),
                    'per_page' => $blogs->perPage(),
                    'last_page' => $blogs->lastPage()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog posts'
            ], 500);
        }
    }

    /**
     * Get single blog post by slug
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        try {
            // Find blog by slug
            $blog = Blogs::where('slug', $slug)
                ->where('status', 1)
                ->first();

            if (!$blog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blog post not found'
                ], 404);
            }

            // Get related posts (same category, exclude current)
            $relatedPosts = Blogs::where('status', 1)
                ->where('blog_category_id', $blog->blog_category_id)
                ->where('id', '!=', $blog->id)
                ->orderBy('id', 'DESC')
                ->limit(3)
                ->get()
                ->map(function ($relatedBlog) {
                    return $this->transformBlogPost($relatedBlog, false);
                });

            // Transform main blog post with full content
            $blogData = $this->transformBlogPost($blog, true);

            // Add related posts
            $blogData['related'] = $relatedPosts;

            return response()->json([
                'success' => true,
                'data' => $blogData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blog post'
            ], 500);
        }
    }

    /**
     * Search blog posts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $perPage = $request->input('per_page', 12);

            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'meta' => [
                        'total' => 0,
                        'current_page' => 1,
                        'per_page' => $perPage,
                        'last_page' => 1
                    ]
                ], 200);
            }

            // Search in title, heading, and description
            $blogs = Blogs::where('status', 1)
                ->where(function ($q) use ($query) {
                    $q->where('heading', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%');
                })
                ->orderBy('id', 'DESC')
                ->paginate($perPage);

            // Transform data
            $blogsData = $blogs->getCollection()->map(function ($blog) {
                return $this->transformBlogPost($blog, false);
            });

            return response()->json([
                'success' => true,
                'data' => $blogsData,
                'meta' => [
                    'total' => $blogs->total(),
                    'current_page' => $blogs->currentPage(),
                    'per_page' => $blogs->perPage(),
                    'last_page' => $blogs->lastPage()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search blog posts'
            ], 500);
        }
    }

    /**
     * Transform blog post data
     *
     * @param Blogs $blog
     * @param bool $includeContent
     * @return array
     */
    private function transformBlogPost($blog, $includeContent = false)
    {
        // Handle featured image
        $featuredImageUrl = null;
        if ($blog->banner && $blog->banner != '') {
            $featuredImageUrl = url('public/img/blogs/' . $blog->banner);
        }

        // Calculate read time (average reading speed: 200 words per minute)
        $wordCount = str_word_count(strip_tags($blog->description ?? ''));
        $readTime = max(1, ceil($wordCount / 200));

        // Get category name if available
        $categoryName = 'Uncategorized';
        if ($blog->blog_category_id) {
            $category = BlogCategory::find($blog->blog_category_id);
            if ($category) {
                $categoryName = $category->name ?? 'Uncategorized';
            }
        }

        // Prepare excerpt (truncate description to 160 characters)
        $excerpt = $blog->description ?? '';
        if (strlen($excerpt) > 160) {
            $excerpt = substr(strip_tags($excerpt), 0, 160) . '...';
        } else {
            $excerpt = strip_tags($excerpt);
        }

        $data = [
            'id' => $blog->id,
            'slug' => $blog->slug,
            'title' => $blog->heading ?? '',
            'excerpt' => $excerpt,
            'featured_image_url' => $featuredImageUrl,
            'category' => $categoryName,
            'published_at' => $blog->post_date ? date('c', strtotime($blog->post_date)) : $blog->created_at->toIso8601String(),
            'read_time' => $readTime
        ];

        // Add full content if requested
        if ($includeContent) {
            $data['content'] = $blog->description ?? '';

            // Add author data (placeholder for now)
            $data['author'] = [
                'name' => 'Propertifi Team',
                'avatar_url' => null
            ];

            // Add tags (placeholder - add tags column to blogs table if needed)
            $data['tags'] = [];
        }

        return $data;
    }
}
