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
        Schema::create('ban_ip', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->dateTime('block_time');
            $table->dateTime('unlock_time');
            $table->bigInteger('attempts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ban_ip');
    }
};
