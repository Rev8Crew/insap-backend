<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('path')->nullable();

            $table->string('name')->nullable();
            $table->string('mime')->nullable();
            $table->integer('is_active')->default(\App\helpers\IsActiveHelper::ACTIVE_ACTIVE);

            $table->integer('user_id')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
}
