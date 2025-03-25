<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->default(false);
            $table->string('place');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->foreignId('image_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('news_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('url')->nullable();
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
        Schema::dropIfExists('ads');
    }
}
