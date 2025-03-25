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
        if (!Schema::hasColumn('transaction_logs', 'psb_order_id')) {
            Schema::table('transaction_logs', function (Blueprint $table) {
                $table->string('psb_order_id')->nullable();
            });
        }

        if (!Schema::hasColumn('transaction_logs', 'qr_id')) {
            Schema::table('transaction_logs', function (Blueprint $table) {
                $table->string('qr_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropColumn('psb_order_id');
            $table->dropColumn('qr_id');
        });
    }
};
