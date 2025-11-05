<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = DocumentTemplate::latest()->paginate(20);
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.templates.create');
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
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx',
        ]);

        $path = $request->file('file')->store('', 'templates');

        DocumentTemplate::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'is_free' => $request->filled('is_free'),
            'requires_signup' => $request->filled('requires_signup'),
            'status' => $request->status ?? 'draft',
        ]);

        return redirect()->route('admin.templates.index')
                        ->with('success','Document Template created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentTemplate $template)
    {
        return view('admin.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentTemplate $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentTemplate $template)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'sometimes|file|mimes:pdf,doc,docx',
        ]);

        $path = $template->file_path;
        if ($request->hasFile('file')) {
            Storage::disk('templates')->delete($template->file_path);
            $path = $request->file('file')->store('', 'templates');
        }

        $template->update([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'is_free' => $request->filled('is_free'),
            'requires_signup' => $request->filled('requires_signup'),
            'status' => $request->status ?? 'draft',
        ]);

        return redirect()->route('admin.templates.index')
                        ->with('success','Document Template updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentTemplate $template)
    {
        Storage::disk('templates')->delete($template->file_path);
        $template->delete();

        return redirect()->route('admin.templates.index')
                        ->with('success','Document Template deleted successfully');
    }
}
