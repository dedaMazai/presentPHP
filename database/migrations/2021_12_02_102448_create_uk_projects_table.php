<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUkProjectsTable
 */
class CreateUkProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->morphs('articlable');
        });

        Schema::create('uk_projects', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_published')->default(true);
            $table->string('name');
            $table->string('crm_1c_id');
            $table->foreignId('image_id')->constrained('images')->nullOnDelete();
            $table->text('description');
            $table->string('postcode');
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
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->dropMorphs('articlable');
        });

        Schema::dropIfExists('uk_projects');
    }
}
