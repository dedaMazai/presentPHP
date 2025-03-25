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
        Schema::create('sign_documents', function (Blueprint $table) {
            $table->id();
            $table->string('guide');
            $table->string('guide_for_sign');
            $table->string('guide_renew_sign');
            $table->string('guide_renew_alarm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sign_documents');
    }
};
