<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceDocumentTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_templates', function (Blueprint $table) {
            // Add state and category relationships
            $table->char('state_code', 2)->nullable()->after('description');
            $table->string('category_slug', 120)->nullable()->after('state_code');

            // Add file metadata
            $table->decimal('file_size_mb', 8, 2)->nullable()->after('file_path');

            // Add download tracking
            $table->integer('download_count')->default(0)->after('file_size_mb');

            // Add tags for filtering
            $table->json('tags')->nullable()->after('download_count');

            // Change status to is_active boolean
            $table->dropColumn('status');
            $table->boolean('is_active')->default(true)->after('requires_signup');

            // Add indexes for filtering
            $table->index(['state_code', 'category_slug', 'is_active']);
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
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropIndex(['state_code', 'category_slug', 'is_active']);
            $table->dropIndex(['is_active']);

            $table->string('status')->default('draft');
            $table->dropColumn([
                'state_code',
                'category_slug',
                'file_size_mb',
                'download_count',
                'tags',
                'is_active'
            ]);
        });
    }
}
