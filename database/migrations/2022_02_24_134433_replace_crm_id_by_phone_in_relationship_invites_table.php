<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceCrmIdByPhoneInRelationshipInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relationship_invites', function (Blueprint $table) {
            $table->dropColumn('crm_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->dateTime('birth_date');
        });

        Schema::table('relationships', function (Blueprint $table) {
            $table->string('joint_owner_id')->nullable();
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
            $table->string('crm_id');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('phone');
            $table->dropColumn('birth_date');
            $table->dropColumn('joint_owner_id');
        });
    }
}
