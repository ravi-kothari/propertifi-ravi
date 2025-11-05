<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StateLawContent;
use Illuminate\Http\Request;

class StateLawController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $laws = StateLawContent::latest()->paginate(20);
        return view('admin.laws.index', compact('laws'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.laws.create');
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
            'state_code' => 'required|string|max:2',
            'topic_slug' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string',
        ]);

        StateLawContent::create($request->all());

        return redirect()->route('admin.laws.index')
                        ->with('success','State Law Content created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StateLawContent  $stateLawContent
     * @return \Illuminate\Http\Response
     */
    public function show(StateLawContent $law)
    {
        return view('admin.laws.show', compact('law'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StateLawContent  $stateLawContent
     * @return \Illuminate\Http\Response
     */
    public function edit(StateLawContent $law)
    {
        return view('admin.laws.edit', compact('law'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StateLawContent  $stateLawContent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StateLawContent $law)
    {
        $request->validate([
            'state_code' => 'required|string|max:2',
            'topic_slug' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|string',
        ]);

        $law->update($request->all());

        return redirect()->route('admin.laws.index')
                        ->with('success','State Law Content updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StateLawContent  $stateLawContent
     * @return \Illuminate\Http\Response
     */
    public function destroy(StateLawContent $law)
    {
        $law->delete();

        return redirect()->route('admin.laws.index')
                        ->with('success','State Law Content deleted successfully');
    }
}
