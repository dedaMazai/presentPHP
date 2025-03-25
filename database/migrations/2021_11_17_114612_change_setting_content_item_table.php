<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ChangeSettingContentItemTable
 */
class ChangeSettingContentItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('setting_content_item');

        Schema::create('settings_content_item', function (Blueprint $table) {
            $table->foreignId('settings_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('settings_content_item');

        Schema::create('setting_content_item', function (Blueprint $table) {
            $table->foreignId('setting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_item_id')->constrained('content_items')->cascadeOnDelete();
            $table->integer('order')->nullable();
        });
    }
}
