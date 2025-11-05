<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalculatorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calculator_logs', function (Blueprint $table) {
            $table->id();
            $table->string('calculator_type'); // roi, mortgage, rent_vs_buy
            $table->json('input_data'); // User inputs
            $table->json('result_data'); // Calculated results
            $table->unsignedBigInteger('user_id')->nullable(); // If logged in
            $table->string('session_id')->nullable(); // Anonymous tracking
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index('calculator_type');
            $table->index('created_at');
            $table->index(['user_id', 'calculator_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calculator_logs');
    }
}
