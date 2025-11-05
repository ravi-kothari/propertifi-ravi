<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceStateLawContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('state_law_contents', function (Blueprint $table) {
            // Add SEO fields
            $table->string('meta_description', 160)->nullable()->after('summary');
            $table->boolean('is_published')->default(false)->after('status');
            $table->timestamp('last_updated_at')->nullable()->after('is_published');

            // Add slug for URLs
            $table->string('slug', 200)->nullable()->after('title');

            // Update status column to be boolean
            $table->dropColumn('status');

            // Add composite unique index
            $table->unique(['state_code', 'topic_slug', 'slug'], 'state_topic_slug_unique');

            // Add index for published content
            $table->index(['state_code', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('state_law_contents', function (Blueprint $table) {
            $table->dropIndex(['state_code', 'is_published']);
            $table->dropUnique('state_topic_slug_unique');

            $table->string('status')->default('draft');
            $table->dropColumn(['meta_description', 'is_published', 'last_updated_at', 'slug']);
        });
    }
}
