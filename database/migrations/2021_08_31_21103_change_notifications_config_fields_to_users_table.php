<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ChangeNotificationsConfigFieldsToUsersTable
 */
class ChangeNotificationsConfigFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('enabled_notifications');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->jsonb('enabled_notifications')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('enabled_notifications');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('enabled_notifications')->default('[]');
        });
    }
}
