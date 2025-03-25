<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDefaultLoanOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('default_loan_offers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('default_loan_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_info_id')->constrained('bank_info')->cascadeOnDelete();
            $table->decimal('loan_rate', 16);
            $table->integer('loan_period');
            $table->decimal('monthly_payment', 16);
            $table->text('description');
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }
}
