<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_lead_id')->constrained('user_leads')->onDelete('cascade');
            $table->foreignId('pm_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->enum('response_type', ['interested', 'not_interested', 'need_more_info', 'contact_requested'])->default('interested');
            $table->text('message')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->json('availability')->nullable(); // PM's availability for viewing/meeting
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('responded_at');
            $table->timestamps();

            $table->index(['user_lead_id', 'responded_at']);
            $table->index('pm_id');
            $table->index('lead_id');
            $table->index('response_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_responses');
    }
}
