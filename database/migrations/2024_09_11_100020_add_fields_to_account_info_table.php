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
            $table->string('project_name')->nullable();
            $table->string('project_id')->nullable();
            $table->string('uk_emergency_claim_phone')->nullable();
            $table->string('classifier_uk_id')->nullable();
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
            $table->dropColumn('project_name');
            $table->dropColumn('project_id');
            $table->dropColumn('uk_emergency_claim_phone');
            $table->dropColumn('classifier_uk_id');
        });
    }
};
