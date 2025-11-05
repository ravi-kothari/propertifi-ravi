<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Tier name (e.g., Basic, Premium, Enterprise)');
            $table->string('title')->comment('Display title for tier');
            $table->decimal('price', 10, 2)->default(0)->comment('Monthly subscription price');
            $table->integer('timings')->default(0)->comment('Legacy field - timing preferences');
            $table->integer('priority')->default(0)->comment('Priority level for lead distribution (higher = first priority)');
            $table->integer('lead_cap')->default(10)->comment('Maximum leads per month for this tier');
            $table->integer('exclusivity_hours')->default(0)->comment('Hours of exclusive access to leads before other tiers');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->timestamps();

            // Add indexes for performance
            $table->index('status');
            $table->index('priority');
        });

        // Insert default tiers
        DB::table('tiers')->insert([
            [
                'name' => 'free',
                'title' => 'Free Tier',
                'price' => 0.00,
                'priority' => 1,
                'lead_cap' => 3,
                'exclusivity_hours' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'basic',
                'title' => 'Basic Plan',
                'price' => 49.99,
                'priority' => 2,
                'lead_cap' => 10,
                'exclusivity_hours' => 0,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'premium',
                'title' => 'Premium Plan',
                'price' => 149.99,
                'priority' => 3,
                'lead_cap' => 30,
                'exclusivity_hours' => 24,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'enterprise',
                'title' => 'Enterprise Plan',
                'price' => 499.99,
                'priority' => 4,
                'lead_cap' => 100,
                'exclusivity_hours' => 48,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tiers');
    }
}
