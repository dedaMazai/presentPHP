<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMortgageMinMaxPropertyPriceToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal(column: 'mortgage_min_property_price', total: 16)->nullable();
            $table->decimal(column: 'mortgage_max_property_price', total: 16)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('mortgage_min_property_price');
            $table->dropColumn('mortgage_max_property_price');
        });
    }
}
