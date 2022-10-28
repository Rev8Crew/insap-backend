<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordDataTable extends Migration
{
    public function up()
    {
        Schema::create('record_data', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('Record data name');
            $table->text('description')->comment('Record data description')->nullable();
            $table->integer('order')->comment('Record data order')->default(0 );
            $table->smallInteger('is_active')->comment('Is record data active');

            $table->integer('project_id')->index()->default(0);
            $table->integer('image_id')->comment('Record data image')->index()->nullable();
            $table->integer('creator_user_id')->nullable();

            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('record_data');
    }
}
