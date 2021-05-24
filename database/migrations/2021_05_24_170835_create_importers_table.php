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

            $table->integer('importer_script_id')->index()->nullable();
            $table->integer('appliance_id')->index()->nullable();
            $table->integer('user_id')->index()->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('importers');
    }
}
