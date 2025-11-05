<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerDashboardController extends Controller
{
    /**
     * Display the authenticated owner's dashboard data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $owner = Auth::user();

        // Load all dashboard data
        $leads = $owner->leads()->latest()->get();
        $bookmarks = $owner->bookmarks()->with('bookmarkable')->get();
        $savedCalculations = $owner->savedCalculations()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'owner' => [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                ],
                'leads' => $leads,
                'bookmarks' => $bookmarks,
                'saved_calculations' => $savedCalculations,
                'statistics' => [
                    'total_leads' => $leads->count(),
                    'total_bookmarks' => $bookmarks->count(),
                    'total_calculations' => $savedCalculations->count(),
                ],
            ],
        ]);
    }
}
