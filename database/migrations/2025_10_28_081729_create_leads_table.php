<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Basic Contact Information
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Property Address
            $table->string('address');
            $table->string('city');
            $table->string('state', 2)->index();
            $table->string('zipcode', 10)->index();

            // Property Details
            $table->string('property_type')->nullable();
            $table->integer('number_of_units')->nullable();
            $table->integer('square_footage')->nullable();

            // Financial Information
            $table->decimal('price', 12, 2)->nullable();
            $table->string('price_range')->nullable();

            // Lead Details
            $table->string('category')->nullable();
            $table->text('additional_services')->nullable();
            $table->string('preferred_contact')->default('email');
            $table->string('source')->default('website');

            // Lead Management
            $table->string('status')->default('new')->index();
            $table->string('unique_id')->unique();
            $table->string('confirmation_number')->nullable()->unique();

            // Dynamic Questions (stored as JSON)
            $table->json('questions')->nullable();

            // Foreign Keys (to be added in Phase 2)
            // $table->foreignId('owner_id')->nullable()->constrained('owners')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('created_at');
            $table->index(['state', 'zipcode']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
