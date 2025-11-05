<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_text');
            $table->enum('input_type', ['text', 'number', 'select', 'multiselect', 'radio', 'checkbox', 'textarea', 'date', 'email', 'phone', 'file']);
            $table->json('options')->nullable()->comment('Options for select, multiselect, radio, checkbox types');
            $table->text('help_text')->nullable();
            $table->string('placeholder')->nullable();
            $table->integer('step')->default(1)->comment('Which step of the form (1-5)');
            $table->integer('order_in_step')->default(0)->comment('Display order within the step');
            $table->boolean('is_required')->default(false);
            $table->json('validation_rules')->nullable()->comment('JSON array of validation rules');
            $table->unsignedBigInteger('depends_on_question_id')->nullable()->comment('Show this question only if parent question has specific answer');
            $table->string('depends_on_answer')->nullable()->comment('The answer value that triggers showing this question');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('depends_on_question_id')->references('id')->on('questions')->onDelete('set null');
            $table->index(['step', 'order_in_step']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
