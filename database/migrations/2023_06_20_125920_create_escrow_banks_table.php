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
        Schema::create('escrow_banks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('escrow_bank_id');
            $table->foreign('escrow_bank_id')->references('id')->on('banks');
            $table->jsonb('letterofbank_ids')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escrow_banks');
    }
};
