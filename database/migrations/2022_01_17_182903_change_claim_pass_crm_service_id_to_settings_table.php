<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeClaimPassCrmServiceIdToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('claim_pass_crm_service_id', 'claim_pass_car_crm_service_id');
            $table->string('claim_pass_human_crm_service_id')->nullable();
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
            $table->dropColumn('claim_pass_human_crm_service_id');
            $table->renameColumn('claim_pass_car_crm_service_id', 'claim_pass_crm_service_id');
        });
    }
}
