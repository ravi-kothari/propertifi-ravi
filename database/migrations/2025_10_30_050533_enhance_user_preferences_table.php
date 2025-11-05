<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceUserPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            // Add lead notifications preference
            $table->boolean('email_notifications')->default(true)->after('preferred_delivery_speed');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');

            // Add zip codes preference as JSON
            $table->json('zip_codes')->nullable()->after('sms_notifications')->comment('Array of preferred zip codes');

            // Add geographic radius preference
            $table->integer('service_radius_miles')->nullable()->after('zip_codes')->comment('Service area radius in miles');

            // Change status to is_active boolean
            $table->dropColumn('status');
            $table->boolean('is_active')->default(true)->after('tier_id');

            // Add indexes
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_active']);

            $table->string('status')->default('active');
            $table->dropColumn([
                'email_notifications',
                'sms_notifications',
                'zip_codes',
                'service_radius_miles',
                'is_active'
            ]);
        });
    }
}
