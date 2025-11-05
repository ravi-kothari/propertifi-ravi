<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStateLawContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('state_law_contents', function (Blueprint $table) {
            $table->id();
            $table->string('state_code', 2)->index();
            $table->string('topic_slug')->index();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('official_link')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('state_law_contents');
    }
}
