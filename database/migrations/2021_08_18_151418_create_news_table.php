<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->default(false);
            $table->string('type');
            $table->string('title');
            $table->string('tag')->nullable();
            $table->text('url')->nullable();
            $table->foreignId('preview_image_id')->nullable()->constrained('images')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('news_content_item', function (Blueprint $table) {
            $table->foreignId('news_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_item_id')->constrained('content_items')->cascadeOnDelete();
            $table->integer('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_content_item');
        Schema::dropIfExists('news');
    }
}
