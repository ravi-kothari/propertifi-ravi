<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Boolean flag to indicate if the PM has been verified by admin
            $table->boolean('is_verified')->default(false);

            // JSON field to store verification documents (URLs/paths to uploaded docs)
            $table->json('verification_documents')->nullable();

            // Timestamp when the PM was verified
            $table->timestamp('verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_verified',
                'verification_documents',
                'verified_at'
            ]);
        });
    }
}
