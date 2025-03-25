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
        if (!Schema::hasColumn('settings', 'refill_account_acquiring')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('refill_account_acquiring')->default('psb');
            });
        }

        if (!Schema::hasColumn('settings', 'claim_payment_acquiring')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('claim_payment_acquiring')->default('psb');
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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('refill_account_acquiring');
            $table->dropColumn('claim_payment_acquiring');
        });
    }
};
