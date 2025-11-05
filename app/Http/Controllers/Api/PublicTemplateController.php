<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PublicTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = DocumentTemplate::where('status', 'published')->latest()->get();
        return response()->json($templates);
    }

    /**
     * Download the specified resource.
     *
     * @param  \App\Models\DocumentTemplate  $template
     * @return \Illuminate\Http\Response
     */
    public function download(DocumentTemplate $template)
    {
        if ($template->status !== 'published') {
            abort(404);
        }

        if ($template->requires_signup && !Auth::check()) {
            return response()->json(['message' => 'Authentication required.'], 401);
        }

        return Storage::disk('templates')->download($template->file_path, $template->title);
    }
}
