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
        Schema::table('account_info', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('balance')->nullable();
            $table->string('services_debt')->nullable();
            $table->string('not_paid_months')->nullable();
            $table->string('is_meter_enter_period_active')->nullable();
            $table->string('project_crm_1c_id')->nullable();
            $table->string('meter_enter_period_start')->nullable();
            $table->string('meter_enter_period_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_info', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('balance');
            $table->dropColumn('services_debt');
            $table->dropColumn('not_paid_months');
            $table->dropColumn('is_meter_enter_period_active');
            $table->dropColumn('project_crm_1c_id');
            $table->dropColumn('meter_enter_period_start');
            $table->dropColumn('meter_enter_period_end');
        });
    }
};
