<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplianceProjectTable extends Migration
{
    public function up()
    {
        Schema::create('appliance_project', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('appliance_id');
            $table->unsignedInteger('project_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appliance_project');
    }
}
