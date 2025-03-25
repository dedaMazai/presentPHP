<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuildsColumnsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('build_android_url')->nullable();
            $table->string('build_ios_url')->nullable();
            $table->string('refill_account_acquiring')->nullable();
            $table->string('claim_payment_acquiring')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('build_android_url');
            $table->dropColumn('build_ios_url');
            $table->dropColumn('refill_account_acquiring');
            $table->dropColumn('claim_payment_acquiring');
        });
    }
}
