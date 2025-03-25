<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateContentItemsTable
 */
class CreateContentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('text')->nullable();
            $table->foreignId('image_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('video_url')->nullable();
            $table->timestamps();
        });

        Schema::create('article_content_item', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_item_id')->constrained('content_items')->cascadeOnDelete();
            $table->integer('order')->nullable();
        });

        Schema::create('content_item_image', function (Blueprint $table) {
            $table->foreignId('content_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('image_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_item_image');
        Schema::dropIfExists('article_content_item');
        Schema::dropIfExists('content_items');
    }
}
