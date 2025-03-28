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
        Schema::table('pay_booking_time', function (Blueprint $table) {
            $table->string('payment_url')->nullable();
            $table->string('contract_id')->nullable();
            $table->string('status')->nullable();
            $table->string('order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_booking_time', function (Blueprint $table) {
            $table->dropColumn('payment_url');
            $table->dropColumn('contract_id');
            $table->dropColumn('status');
            $table->dropColumn('order_id');
        });
    }
};
