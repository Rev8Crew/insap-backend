<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessesTable extends Migration
{
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->bigIncrements('id');

            //
            $table->string('name')->nullable();
            $table->string('description')->nullable();

            $table->integer('type'); // Import | Export
            $table->string('interpreter')->comment('Interpreter like PHP, python, go ...')->nullable();
            $table->json('options')->nullable();

            $table->integer('appliance_id')->index()->nullable();
            $table->integer('plugin_id')->index()->nullable();
            $table->integer('user_id')->index()->nullable();

            $table->integer('is_active');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('processes');
    }
}
