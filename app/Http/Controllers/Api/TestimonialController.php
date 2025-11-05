<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonials;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Get published testimonials
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function published(Request $request)
    {
        try {
            // Get limit from query parameter, default to 10
            $limit = $request->input('limit', 10);

            // Validate limit is a positive integer
            if (!is_numeric($limit) || $limit < 1) {
                $limit = 10;
            }

            // Get published testimonials
            $testimonials = Testimonials::where('status', 1)
                ->orderBy('id', 'DESC')
                ->limit($limit)
                ->get();

            // Transform data to match Next.js requirements
            $testimonialsData = $testimonials->map(function ($testimonial) {
                // Handle image path - convert to absolute URL
                $imageUrl = null;
                if ($testimonial->banner && $testimonial->banner != '') {
                    $imageUrl = url('public/img/testimonials/' . $testimonial->banner);
                }

                return [
                    'id' => $testimonial->id,
                    'name' => $testimonial->name ?? 'Anonymous',
                    'quote' => $testimonial->description ?? '',
                    'rating' => $testimonial->rating ?? 5,
                    'image_url' => $imageUrl,
                    'location' => $testimonial->location ?? 'Property Owner',
                    'created_at' => $testimonial->created_at ? $testimonial->created_at->toIso8601String() : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $testimonialsData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch testimonials'
            ], 500);
        }
    }
}
