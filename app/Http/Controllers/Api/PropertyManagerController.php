<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PropertyManagerController extends Controller
{
    /**
     * Get list of property managers for Next.js frontend
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::where('type', 'User')
            ->where('status', 1);

        // Filter by state if provided
        if($request->has('state') && $request->state != '') {
            $query->where('state', $request->state);
        }

        // Filter by city if provided
        if($request->has('city') && $request->city != '') {
            $query->where('city', $request->city);
        }

        // Filter by verified status if provided
        if($request->has('verified')) {
            $query->where('is_verified', $request->verified == 'true' ? 1 : 0);
        }

        // Search by name or company
        if($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Prioritize verified PMs if sorting by name
        if($sortBy == 'name') {
            $query->orderBy('is_verified', 'desc')
                  ->orderBy('name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $managers = $query->select([
            'id',
            'name',
            'email',
            'company_name',
            'photo',
            'city',
            'state',
            'slug',
            'is_verified',
            'verified_at',
            'about',
            'website',
            'featured'
        ])->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $managers
        ]);
    }

    /**
     * Get single PM details by slug
     *
     * @param  string  $state
     * @param  string  $city
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($state, $city, $slug)
    {
        $manager = User::where('slug', $slug)
            ->where('state', $state)
            ->where('city', $city)
            ->where('type', 'User')
            ->where('status', 1)
            ->select([
                'id',
                'name',
                'email',
                'company_name',
                'photo',
                'city',
                'state',
                'slug',
                'is_verified',
                'verified_at',
                'about',
                'website',
                'p_contact_name',
                'p_contact_no',
                'p_contact_email',
                'address',
                'zipcode',
                'single_family',
                'multi_family',
                'association_property',
                'commercial_property',
                'units'
            ])
            ->first();

        if(!$manager) {
            return response()->json([
                'success' => false,
                'message' => 'Property manager not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $manager
        ]);
    }

    /**
     * Get PM details by ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById($id)
    {
        $manager = User::where('id', $id)
            ->where('type', 'User')
            ->where('status', 1)
            ->select([
                'id',
                'name',
                'email',
                'company_name',
                'photo',
                'city',
                'state',
                'slug',
                'is_verified',
                'verified_at',
                'about',
                'website',
                'featured'
            ])
            ->first();

        if(!$manager) {
            return response()->json([
                'success' => false,
                'message' => 'Property manager not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $manager
        ]);
    }
}
