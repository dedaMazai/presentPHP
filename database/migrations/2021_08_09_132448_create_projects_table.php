<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateProjectsTable
 */
class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('project_types')->restrictOnDelete();
            $table->boolean('is_published')->default(true);
            $table->string('name');
            $table->foreignId('showcase_image_id')->constrained('images')->nullOnDelete();
            $table->foreignId('main_image_id')->constrained('images')->nullOnDelete();
            $table->string('metro')->nullable();
            $table->string('crm_id');
            $table->string('mortgage_calculator_id');
            $table->foreignId('map_image_id')->constrained('images')->nullOnDelete();
            $table->float('lat');
            $table->float('long');
            $table->json('map_facts')->default('[]');
            $table->string('office_phone')->nullable();
            $table->string('office_address')->nullable();
            $table->float('office_lat')->nullable();
            $table->float('office_long')->nullable();
            $table->json('office_work_hours')->nullable();
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
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_types');
    }
}
