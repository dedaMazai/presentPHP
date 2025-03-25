<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class ChangeFieldsInProjectsTable
 */
class ChangeFieldsInProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('office_work_hours');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('map_image_id');
            $table->string('crm_address_id')->nullable();
            $table->string('color');
            $table->text('description')->nullable();
            $table->text('office_work_hours')->nullable();
            $table->json('property_type_params')->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('office_work_hours');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('map_image_id')->nullable()->constrained('images')->nullOnDelete();
            $table->dropColumn('crm_address_id');
            $table->dropColumn('color');
            $table->dropColumn('description');
            $table->json('office_work_hours')->nullable();
            $table->dropColumn('property_type_params');
        });
    }
}
