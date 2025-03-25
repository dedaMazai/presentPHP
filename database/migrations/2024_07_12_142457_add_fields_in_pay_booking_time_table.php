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
            $table->string('contract_number')->nullable();
            $table->string('fiscalization_complete')->default(false);
            $table->timestamp('order_creation_time')->nullable();
            $table->string('email')->nullable();
            $table->json('register_do_log')->nullable();
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
            $table->dropColumn('contract_number');
            $table->dropColumn('fiscalization_complete');
            $table->dropColumn('order_creation_time');
            $table->dropColumn('email');
            $table->dropColumn('register_do_log');
        });
    }
};
