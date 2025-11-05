<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLeadController extends Controller
{
    /**
     * Update the status of a user lead.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserLead  $userLead
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, UserLead $userLead)
    {
        if ($userLead->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:new,contacted,closed',
        ]);

        $userLead->update(['status' => $request->status]);

        return response()->json($userLead);
    }

    /**
     * Update the notes for a user lead.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserLead  $userLead
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotes(Request $request, UserLead $userLead)
    {
        if ($userLead->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $userLead->update(['notes' => $request->notes]);

        return response()->json($userLead);
    }
}
