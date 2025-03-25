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
        Schema::create('pay_booking_time', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('crm_id');
            $table->string('customer_id');
            $table->timestamp('end_date');
            $table->timestamp('time_to_pay');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_booking_time');
    }
};
