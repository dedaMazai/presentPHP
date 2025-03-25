<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ChangeFieldsInFiscalReceiptsTable
 */
class ChangeFieldsInFiscalReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fiscal_receipts', function (Blueprint $table) {
            $table->string('order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fiscal_receipts', function (Blueprint $table) {
            $table->int('order_id');
        });
    }
}
