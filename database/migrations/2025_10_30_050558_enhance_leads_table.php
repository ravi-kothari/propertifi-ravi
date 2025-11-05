<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Add lead quality score
            $table->integer('quality_score')->nullable()->after('status')->comment('Automated lead quality score 0-100');

            // Add distribution tracking
            $table->integer('distribution_count')->default(0)->after('quality_score')->comment('Number of times distributed');
            $table->timestamp('last_distributed_at')->nullable()->after('distribution_count');

            // Add response tracking
            $table->integer('viewed_count')->default(0)->after('last_distributed_at')->comment('Times viewed by property managers');
            $table->timestamp('first_viewed_at')->nullable()->after('viewed_count');

            // Add property details
            $table->integer('year_built')->nullable()->after('square_footage');
            $table->json('amenities')->nullable()->after('year_built')->comment('Property amenities');

            // Add utm tracking
            $table->string('utm_source')->nullable()->after('source');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');

            // Add indexes for reporting
            $table->index('quality_score');
            $table->index('distribution_count');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['quality_score']);
            $table->dropIndex(['distribution_count']);
            $table->dropIndex(['status', 'created_at']);

            $table->dropColumn([
                'quality_score',
                'distribution_count',
                'last_distributed_at',
                'viewed_count',
                'first_viewed_at',
                'year_built',
                'amenities',
                'utm_source',
                'utm_medium',
                'utm_campaign'
            ]);
        });
    }
}
