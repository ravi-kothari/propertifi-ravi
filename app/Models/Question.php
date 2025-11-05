<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text',
        'input_type',
        'options',
        'help_text',
        'placeholder',
        'step',
        'order_in_step',
        'is_required',
        'validation_rules',
        'depends_on_question_id',
        'depends_on_answer',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent question that this question depends on
     */
    public function dependsOnQuestion()
    {
        return $this->belongsTo(Question::class, 'depends_on_question_id');
    }

    /**
     * Get the child questions that depend on this question
     */
    public function dependentQuestions()
    {
        return $this->hasMany(Question::class, 'depends_on_question_id');
    }

    /**
     * Scope to get only active questions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get questions for a specific step
     */
    public function scopeForStep($query, $step)
    {
        return $query->where('step', $step);
    }

    /**
     * Scope to order questions by step and order_in_step
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step')->orderBy('order_in_step');
    }

    /**
     * Get all questions grouped by step
     */
    public static function getAllByStep()
    {
        return self::active()
            ->ordered()
            ->get()
            ->groupBy('step');
    }

    /**
     * Check if this question should be displayed based on parent question answer
     */
    public function shouldDisplay($answers)
    {
        if (!$this->depends_on_question_id) {
            return true;
        }

        $parentQuestion = $this->dependsOnQuestion;
        if (!$parentQuestion) {
            return true;
        }

        $parentAnswer = $answers[$parentQuestion->id] ?? null;

        if ($this->depends_on_answer === null) {
            return !empty($parentAnswer);
        }

        return $parentAnswer == $this->depends_on_answer;
    }
}
