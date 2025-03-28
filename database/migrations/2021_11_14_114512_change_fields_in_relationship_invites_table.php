<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ChangeFieldsInRelationshipInvitesTable
 */
class ChangeFieldsInRelationshipInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relationship_invites', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('phone');
            $table->dropColumn('code');
        });

        Schema::table('relationship_invites', function (Blueprint $table) {
            $table->string('crm_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relationship_invites', function (Blueprint $table) {
            $table->string('name');
            $table->string('phone');
            $table->string('code')->unique();
        });

        Schema::table('relationship_invites', function (Blueprint $table) {
            $table->dropColumn('crm_id');
        });
    }
}
