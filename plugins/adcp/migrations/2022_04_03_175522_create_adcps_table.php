<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdcpsTable extends Migration
{
    public function up()
    {
        Schema::create('adcps', static function (Blueprint $table) {
            $table->id();

            $table->bigInteger('step_id')->index()->nullable();
            $table->integer('expedition_number')->index()->nullable();

            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('distance')->nullable();
            $table->double('speed')->index()->nullable();
            $table->double('max_depth')->nullable();

            $table->json('depths')->nullable();

            $table->dateTime('date')->index();
            $table->integer('record_id')->index()->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adcps');
    }
}
