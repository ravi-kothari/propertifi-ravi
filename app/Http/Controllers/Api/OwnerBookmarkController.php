<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OwnerBookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerBookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookmarks = Auth::user()->bookmarks()->with('bookmarkable')->get();
        return response()->json($bookmarks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'bookmarkable_id' => 'required|integer',
            'bookmarkable_type' => 'required|string|in:StateLawContent,DocumentTemplate',
        ]);

        $bookmark = Auth::user()->bookmarks()->create($request->all());

        return response()->json($bookmark, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OwnerBookmark  $bookmark
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnerBookmark $bookmark)
    {
        if ($bookmark->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bookmark->delete();

        return response()->json(null, 204);
    }
}
