<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');

            // Response Data
            $table->text('answer');
            $table->string('answer_type')->default('text'); // text, multiple_choice, yes_no, number

            // Metadata
            $table->integer('display_order')->default(0);

            $table->timestamps();

            // Indexes for performance
            $table->index('lead_id');
            $table->index('question_id');
            $table->index(['lead_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('responses');
    }
}
