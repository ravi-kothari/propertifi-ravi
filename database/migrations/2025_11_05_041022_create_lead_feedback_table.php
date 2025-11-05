<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_lead_id')->constrained('user_leads')->onDelete('cascade')
                ->comment('Reference to the user_leads distribution record');
            $table->foreignId('pm_id')->constrained('users')->onDelete('cascade')
                ->comment('Property manager who provided feedback');
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade')
                ->comment('Lead this feedback is about');
            $table->enum('feedback_type', ['accepted', 'rejected', 'spam', 'unresponsive', 'not_interested', 'low_quality'])
                ->comment('Type of feedback provided by PM');
            $table->text('feedback_notes')->nullable()
                ->comment('Optional notes from PM about why they accepted/rejected');
            $table->string('rejection_reason')->nullable()
                ->comment('Structured rejection reason (e.g., wrong_location, wrong_property_type, price_too_high)');
            $table->integer('quality_rating')->nullable()
                ->comment('PM rating of lead quality (1-5 stars)');
            $table->timestamps();

            // Indexes for performance
            $table->index(['pm_id', 'feedback_type']);
            $table->index(['lead_id', 'feedback_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_feedback');
    }
}
