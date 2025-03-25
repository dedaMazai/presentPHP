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
        Schema::table('documents_name', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->bigInteger('object_type_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents_name', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('object_type_code');
        });
    }
};
