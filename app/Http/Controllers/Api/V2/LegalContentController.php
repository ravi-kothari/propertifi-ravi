<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\StateProfile;
use App\Models\LegalTopic;
use App\Models\StateLawContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LegalContentController extends Controller
{
    /**
     * Get all states with law content counts.
     *
     * GET /api/v2/legal/states
     */
    public function getStates()
    {
        $states = Cache::remember('legal_states', 3600, function () {
            return StateProfile::active()
                ->with(['lawContents' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->get()
                ->map(function ($state) {
                    return [
                        'state_code' => $state->state_code,
                        'name' => $state->name,
                        'slug' => $state->slug,
                        'overview' => $state->overview,
                        'law_count' => $state->lawContents->count(),
                    ];
                })
                ->filter(function ($state) {
                    return $state['law_count'] > 0;
                })
                ->values();
        });

        return response()->json([
            'success' => true,
            'data' => $states,
        ]);
    }

    /**
     * Get all legal topics.
     *
     * GET /api/v2/legal/topics
     */
    public function getTopics()
    {
        $topics = Cache::remember('legal_topics', 3600, function () {
            return LegalTopic::active()
                ->ordered()
                ->get()
                ->map(function ($topic) {
                    return [
                        'slug' => $topic->slug,
                        'name' => $topic->name,
                        'description' => $topic->description,
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'data' => $topics,
        ]);
    }

    /**
     * Get all law topics for a specific state.
     *
     * GET /api/v2/legal/states/{state}/laws
     */
    public function getStateLaws($stateSlug)
    {
        $state = StateProfile::where('slug', $stateSlug)
            ->orWhere('state_code', strtoupper($stateSlug))
            ->firstOrFail();

        $cacheKey = "legal_state_laws_{$state->state_code}";

        $laws = Cache::remember($cacheKey, 3600, function () use ($state) {
            return StateLawContent::where('state_code', $state->state_code)
                ->where('is_published', true)
                ->with('legalTopic')
                ->get()
                ->groupBy('topic_slug')
                ->map(function ($topicLaws, $topicSlug) {
                    $topic = $topicLaws->first()->legalTopic;

                    return [
                        'topic_slug' => $topicSlug,
                        'topic_name' => $topic ? $topic->name : $topicSlug,
                        'laws' => $topicLaws->map(function ($law) {
                            return [
                                'slug' => $law->slug,
                                'title' => $law->title,
                                'summary' => $law->summary,
                                'last_updated' => $law->last_updated_at?->format('Y-m-d'),
                            ];
                        })->values(),
                    ];
                })
                ->values();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'state' => [
                    'code' => $state->state_code,
                    'name' => $state->name,
                    'slug' => $state->slug,
                ],
                'laws_by_topic' => $laws,
            ],
        ]);
    }

    /**
     * Get specific law content for a state and topic.
     *
     * GET /api/v2/legal/states/{state}/laws/{topic}
     */
    public function getStateLawByTopic($stateSlug, $topicSlug)
    {
        $state = StateProfile::where('slug', $stateSlug)
            ->orWhere('state_code', strtoupper($stateSlug))
            ->firstOrFail();

        $cacheKey = "legal_law_{$state->state_code}_{$topicSlug}";

        $lawContent = Cache::remember($cacheKey, 3600, function () use ($state, $topicSlug) {
            return StateLawContent::where('state_code', $state->state_code)
                ->where('topic_slug', $topicSlug)
                ->where('is_published', true)
                ->with(['legalTopic', 'stateProfile'])
                ->firstOrFail();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'state' => [
                    'code' => $lawContent->stateProfile->state_code,
                    'name' => $lawContent->stateProfile->name,
                    'slug' => $lawContent->stateProfile->slug,
                ],
                'topic' => [
                    'slug' => $lawContent->legalTopic->slug,
                    'name' => $lawContent->legalTopic->name,
                ],
                'law' => [
                    'slug' => $lawContent->slug,
                    'title' => $lawContent->title,
                    'summary' => $lawContent->summary,
                    'content' => $lawContent->content,
                    'meta_description' => $lawContent->meta_description,
                    'official_link' => $lawContent->official_link,
                    'last_updated' => $lawContent->last_updated_at?->format('Y-m-d'),
                ],
            ],
        ]);
    }

    /**
     * Search law content across all states.
     *
     * GET /api/v2/legal/search?q={query}
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query) || strlen($query) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 3 characters.',
            ], 400);
        }

        $results = StateLawContent::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('summary', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['stateProfile', 'legalTopic'])
            ->limit(50)
            ->get()
            ->map(function ($law) {
                return [
                    'state_code' => $law->state_code,
                    'state_name' => $law->stateProfile->name,
                    'topic_slug' => $law->topic_slug,
                    'topic_name' => $law->legalTopic->name,
                    'slug' => $law->slug,
                    'title' => $law->title,
                    'summary' => $law->summary,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count(),
        ]);
    }
}
