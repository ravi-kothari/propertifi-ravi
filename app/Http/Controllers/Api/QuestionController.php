<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Get all questions grouped by step
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $questions = Question::active()
                ->ordered()
                ->with('dependsOnQuestion:id,question_text')
                ->get()
                ->groupBy('step')
                ->map(function ($stepQuestions) {
                    return $stepQuestions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'question_text' => $question->question_text,
                            'input_type' => $question->input_type,
                            'options' => $question->options,
                            'help_text' => $question->help_text,
                            'placeholder' => $question->placeholder,
                            'step' => $question->step,
                            'order_in_step' => $question->order_in_step,
                            'is_required' => $question->is_required,
                            'validation_rules' => $question->validation_rules,
                            'depends_on_question_id' => $question->depends_on_question_id,
                            'depends_on_answer' => $question->depends_on_answer,
                            'parent_question' => $question->dependsOnQuestion ? [
                                'id' => $question->dependsOnQuestion->id,
                                'question_text' => $question->dependsOnQuestion->question_text,
                            ] : null,
                        ];
                    })->values();
                });

            return response()->json([
                'success' => true,
                'data' => $questions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch questions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get questions for a specific step
     *
     * @param int $step
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByStep($step)
    {
        try {
            $questions = Question::active()
                ->forStep($step)
                ->ordered()
                ->with('dependsOnQuestion:id,question_text')
                ->get()
                ->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'input_type' => $question->input_type,
                        'options' => $question->options,
                        'help_text' => $question->help_text,
                        'placeholder' => $question->placeholder,
                        'step' => $question->step,
                        'order_in_step' => $question->order_in_step,
                        'is_required' => $question->is_required,
                        'validation_rules' => $question->validation_rules,
                        'depends_on_question_id' => $question->depends_on_question_id,
                        'depends_on_answer' => $question->depends_on_answer,
                        'parent_question' => $question->dependsOnQuestion ? [
                            'id' => $question->dependsOnQuestion->id,
                            'question_text' => $question->dependsOnQuestion->question_text,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $questions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch questions for step ' . $step,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
