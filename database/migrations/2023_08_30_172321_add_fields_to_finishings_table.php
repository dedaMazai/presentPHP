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
        Schema::table('finishings', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('catalog_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finishings', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('catalog_url');
        });
    }
};
