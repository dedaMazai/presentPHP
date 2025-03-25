<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('banks', function (Blueprint $table) {
            $table->string('bank_id')->nullable()->change();
            $table->bigInteger('image_id')->nullable()->change();
        });

        DB::table('banks')->insert
        ([
            'name' => 'Без банка',
            'bank_id' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->string('bank_id')->change();
            $table->bigInteger('image_id')->change();
        });

        DB::table('banks')->where('name', '=', 'Без банка')->delete();
    }
};
