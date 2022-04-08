<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportersTable extends Migration
{
    public function up()
    {
        Schema::create('importers', function (Blueprint $table) {
            $table->bigIncrements('id');

            //
            $table->string('name')->nullable();
            $table->string('description')->nullable();

            $table->integer('appliance_id')->index()->nullable();
            $table->integer('plugin_id')->index()->nullable();

            $table->integer('is_active');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('importers');
    }
}
