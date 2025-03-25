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
        Schema::create('fiscal_receipts', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->string('receipt_id');
            $table->string('operation_id');
            $table->string('operation_type');
            $table->string('receipt_type');
            $table->integer('receipt_status_code');
            $table->string('receipt_status');
            $table->string('orig_receipt_id')->nullable();
            $table->timestamp('timestamp');
            $table->string('group_code');
            $table->string('daemon_code');
            $table->string('device_code');
            $table->string('ofd_receipt_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fiscal_receipts');
    }
};
