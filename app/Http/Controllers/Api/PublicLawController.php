<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StateLawContent;
use Illuminate\Http\Request;

class PublicLawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $laws = StateLawContent::where('status', 'published')->latest()->get();
        return response()->json($laws);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $state_slug
     * @return \Illuminate\Http\Response
     */
    public function show($state_slug)
    {
        $law = StateLawContent::where('state_code', $state_slug)
                                ->where('status', 'published')
                                ->firstOrFail();
        return response()->json($law);
    }
}
