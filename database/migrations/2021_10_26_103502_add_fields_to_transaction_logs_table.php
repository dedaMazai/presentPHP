<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddFieldsToTransactionLogsTable
 */
class AddFieldsToTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->string('remote_order_id')->nullable();
            $table->string('qr_id')->nullable();
            $table->string('status');
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->string('payment_method_type')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropColumn('remote_order_id');
            $table->dropColumn('qr_id');
            $table->dropColumn('status');
            $table->dropColumn('project_id');
            $table->string('payment_method_type')->nullable()->change();
        });
    }
}
