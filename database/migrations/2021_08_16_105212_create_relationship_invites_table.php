<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateRelationshipInvitesTable
 */
class CreateRelationshipInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relationship_invites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_number');
            $table->string('role');
            $table->string('name');
            $table->string('phone');
            $table->string('code')->unique();
            $table->dateTime('accepted_at')->nullable();
            $table->unsignedBigInteger('accepted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('relationship_invites');
    }
}
