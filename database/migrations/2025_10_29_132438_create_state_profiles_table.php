<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('state_profiles', function (Blueprint $table) {
            $table->id();
            $table->char('state_code', 2)->unique();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('overview')->nullable();
            $table->char('abbreviation', 2);
            $table->json('meta_data')->nullable(); // For additional state-specific data
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance and lookups
            $table->index('state_code');
            $table->index('slug');
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
        Schema::dropIfExists('state_profiles');
    }
}
