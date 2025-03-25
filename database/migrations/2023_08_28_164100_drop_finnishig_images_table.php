<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('finnishig_images');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('finnishig_images', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->default(false);
            $table->string('finishing_id');
            $table->string('description')->nullable();
            $table->bigInteger('image_id');
            $table->foreign('image_id')->references('id')->on('images');
        });
    }
};
