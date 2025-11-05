<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Temporarily removed foreign key constraints until pricings and tiers tables are created
            $table->unsignedBigInteger('pricing_id')->nullable();
            $table->unsignedBigInteger('tier_id')->nullable();
            $table->string('status')->default('active');
            $table->integer('timings')->nullable()->comment('Default delivery speed');
            $table->json('property_types')->nullable()->comment('Array of preferred property types');
            $table->integer('min_units')->nullable()->comment('Minimum units preference');
            $table->integer('max_units')->nullable()->comment('Maximum units preference');
            $table->integer('preferred_delivery_speed')->nullable()->comment('Custom delivery speed override');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_preferences');
    }
}
