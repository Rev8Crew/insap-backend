<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePluginsTable extends Migration
{
    public function up()
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('slug')->unique();

            $table->json('settings')->nullable();

            $table->string('service_class')->nullable();

            $table->integer('is_active');
            //

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plugins');
    }
}
