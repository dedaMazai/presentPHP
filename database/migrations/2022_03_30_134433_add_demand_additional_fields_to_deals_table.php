<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDemandAdditionalFieldsToDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('demand_status');
            $table->string('demand_booking_type');
            $table->timestamp('initial_begin_date');
            $table->timestamp('initial_end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('demand_status');
            $table->dropColumn('demand_booking_type');
            $table->dropColumn('initial_begin_date');
            $table->dropColumn('initial_end_date');
        });
    }
}
