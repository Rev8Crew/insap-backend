<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Project name');
            $table->string('description')->comment('Project description')->default('');
            $table->text('image')->comment('Project image')->nullable();
            $table->integer('order')->comment('Project order')->default(0 );
            $table->smallInteger('is_active')->comment('Is project active')->default(\App\helpers\IsActiveHelper::ACTIVE_ACTIVE);

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
    }
}
