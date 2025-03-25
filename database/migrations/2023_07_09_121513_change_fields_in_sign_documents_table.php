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
        Schema::table('sign_documents', function (Blueprint $table) {
            $table->string('guide')->nullable()->change();
            $table->string('guide_for_sign')->nullable()->change();
            $table->string('guide_renew_sign')->nullable()->change();
            $table->string('guide_renew_alarm')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_documents', function (Blueprint $table) {
            $table->string('guide')->nullable(false)->change();
            $table->string('guide_for_sign')->nullable(false)->change();
            $table->string('guide_renew_sign')->nullable(false)->change();
            $table->string('guide_renew_alarm')->nullable(false)->change();
        });
    }
};
