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
            $table->dropColumn('guide');
            $table->dropColumn('guide_for_sign');
            $table->dropColumn('guide_renew_sign');
            $table->dropColumn('guide_renew_alarm');

            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('document_id')->nullable();
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
            $table->string('guide')->nullable();
            $table->string('guide_for_sign')->nullable();
            $table->string('guide_renew_sign')->nullable();
            $table->string('guide_renew_alarm')->nullable();

            $table->dropColumn('name');
            $table->dropColumn('code');
            $table->dropColumn('document_id');
        });
    }
};
