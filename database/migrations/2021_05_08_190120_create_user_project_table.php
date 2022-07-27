<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProjectTable extends Migration
{
    public function up()
    {
        Schema::create('user_project', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('project_id')->index();
            $table->integer('user_id')->index();

            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_project');
    }
}
