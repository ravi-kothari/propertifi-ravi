<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToUserLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_leads', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('user_leads', 'pm_id')) {
                $table->unsignedBigInteger('pm_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('user_leads', 'lead_unique_id')) {
                $table->string('lead_unique_id')->nullable()->after('lead_id');
            }
            if (!Schema::hasColumn('user_leads', 'property_type')) {
                $table->string('property_type')->nullable()->after('lead_unique_id');
            }
            if (!Schema::hasColumn('user_leads', 'lead_date')) {
                $table->timestamp('lead_date')->nullable()->after('property_type');
            }
            if (!Schema::hasColumn('user_leads', 'price')) {
                $table->decimal('price', 12, 2)->nullable()->after('lead_date');
            }
            if (!Schema::hasColumn('user_leads', 'tier_id')) {
                $table->unsignedBigInteger('tier_id')->nullable()->after('price');
            }
            if (!Schema::hasColumn('user_leads', 'delivery_speed_preference')) {
                $table->integer('delivery_speed_preference')->default(0)->after('tier_id');
            }
            if (!Schema::hasColumn('user_leads', 'location_match')) {
                $table->integer('location_match')->default(0)->after('delivery_speed_preference');
            }
            if (!Schema::hasColumn('user_leads', 'category_match')) {
                $table->integer('category_match')->default(0)->after('location_match');
            }
            if (!Schema::hasColumn('user_leads', 'distribution_fairness')) {
                $table->integer('distribution_fairness')->default(0)->after('category_match');
            }
            if (!Schema::hasColumn('user_leads', 'tier_preference')) {
                $table->integer('tier_preference')->default(0)->after('distribution_fairness');
            }
            if (!Schema::hasColumn('user_leads', 'total_score')) {
                $table->integer('total_score')->default(0)->after('tier_preference');
            }
            if (!Schema::hasColumn('user_leads', 'match_score')) {
                $table->integer('match_score')->default(0)->after('total_score');
            }
            if (!Schema::hasColumn('user_leads', 'distributed_at')) {
                $table->timestamp('distributed_at')->nullable()->after('match_score');
            }
            if (!Schema::hasColumn('user_leads', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('distributed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_leads', function (Blueprint $table) {
            $table->dropColumn([
                'pm_id',
                'lead_unique_id',
                'property_type',
                'lead_date',
                'price',
                'tier_id',
                'delivery_speed_preference',
                'location_match',
                'category_match',
                'distribution_fairness',
                'tier_preference',
                'total_score',
                'match_score',
                'distributed_at',
                'viewed_at',
            ]);
        });
    }
}
