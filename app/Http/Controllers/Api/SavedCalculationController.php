<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedCalculationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $calculations = Auth::user()->savedCalculations;
        return response()->json($calculations);
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
            'calculator_type' => 'required|string|in:roi,mortgage,rent_vs_buy',
            'input_data' => 'required|array',
            'result_data' => 'required|array',
        ]);

        $calculation = Auth::user()->savedCalculations()->create($request->all());

        return response()->json($calculation, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SavedCalculation  $savedCalculation
     * @return \Illuminate\Http\Response
     */
    public function destroy(SavedCalculation $savedCalculation)
    {
        if ($savedCalculation->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $savedCalculation->delete();

        return response()->json(null, 204);
    }
}
