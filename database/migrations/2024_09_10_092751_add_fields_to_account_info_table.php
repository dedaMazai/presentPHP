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
            $table->string('address_id')->nullable();
            $table->string('service_seller_id')->nullable();
            $table->string('address_number')->nullable();
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
            $table->dropColumn('address_id');
            $table->dropColumn('service_seller_id');
            $table->dropColumn('address_number');
        });
    }
};
