<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ChangeFieldsInSettingsTable
 */
class ChangeFieldsInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('office');
            $table->dropColumn('phone');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('main_office_address')->nullable();
            $table->string('main_office_phone')->nullable();
            $table->text('main_office_work_hours')->nullable();
            $table->string('main_office_email')->nullable();
            $table->float('main_office_lat')->nullable();
            $table->float('main_office_long')->nullable();
            $table->string('phone')->nullable();
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
            $table->dropColumn('main_office_address');
            $table->dropColumn('main_office_phone');
            $table->dropColumn('main_office_work_hours');
            $table->dropColumn('main_office_email');
            $table->dropColumn('main_office_lat');
            $table->dropColumn('main_office_long');
            $table->dropColumn('phone');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->text('office');
            $table->string('phone');
        });
    }
}
