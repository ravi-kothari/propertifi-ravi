<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Question::query()->with('dependsOnQuestion:id,question_text');

        // Filter by step if provided
        if ($request->has('step')) {
            $query->forStep($request->step);
        }

        // Filter by active status if provided
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Order by step and order_in_step
        $questions = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string|max:255',
            'input_type' => 'required|in:text,number,select,multiselect,radio,checkbox,textarea,date,email,phone,file',
            'options' => 'nullable|array',
            'help_text' => 'nullable|string',
            'placeholder' => 'nullable|string|max:255',
            'step' => 'required|integer|min:1|max:5',
            'order_in_step' => 'required|integer|min:0',
            'is_required' => 'boolean',
            'validation_rules' => 'nullable|array',
            'depends_on_question_id' => 'nullable|exists:questions,id',
            'depends_on_answer' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $question = Question::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Question created successfully',
                'data' => $question,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create question',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $question = Question::with(['dependsOnQuestion', 'dependentQuestions'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $question,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Question not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question_text' => 'sometimes|required|string|max:255',
            'input_type' => 'sometimes|required|in:text,number,select,multiselect,radio,checkbox,textarea,date,email,phone,file',
            'options' => 'nullable|array',
            'help_text' => 'nullable|string',
            'placeholder' => 'nullable|string|max:255',
            'step' => 'sometimes|required|integer|min:1|max:5',
            'order_in_step' => 'sometimes|required|integer|min:0',
            'is_required' => 'boolean',
            'validation_rules' => 'nullable|array',
            'depends_on_question_id' => 'nullable|exists:questions,id',
            'depends_on_answer' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $question = Question::findOrFail($id);

            // Prevent circular dependencies
            if ($request->has('depends_on_question_id') && $request->depends_on_question_id == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'A question cannot depend on itself',
                ], 422);
            }

            $question->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'data' => $question,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update question',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);

            // Check if any questions depend on this one
            $dependentCount = $question->dependentQuestions()->count();
            if ($dependentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete question. {$dependentCount} other question(s) depend on it.",
                ], 422);
            }

            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete question',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder questions within a step
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'step' => 'required|integer|min:1|max:5',
            'question_orders' => 'required|array',
            'question_orders.*.id' => 'required|exists:questions,id',
            'question_orders.*.order_in_step' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            foreach ($request->question_orders as $order) {
                Question::where('id', $order['id'])
                    ->where('step', $request->step)
                    ->update(['order_in_step' => $order['order_in_step']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Questions reordered successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder questions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
