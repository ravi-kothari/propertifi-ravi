<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeospatialColumnsForLeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add latitude and longitude to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('zipcode')
                ->comment('Latitude for geospatial matching');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude')
                ->comment('Longitude for geospatial matching');
            $table->index(['latitude', 'longitude']); // Spatial index for performance
        });

        // Add latitude and longitude to user_preferences table for PM service areas
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('zip_codes')
                ->comment('Center latitude for service radius');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude')
                ->comment('Center longitude for service radius');
            $table->index(['latitude', 'longitude']); // Spatial index for performance
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
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
}
