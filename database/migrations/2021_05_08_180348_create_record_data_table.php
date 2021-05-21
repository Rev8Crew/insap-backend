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
            $table->string('description')->comment('Record data description')->default('');
            $table->integer('order')->comment('Record data order')->default(0 );
            $table->smallInteger('is_active')->comment('Is record data active')->default(\App\helpers\IsActiveHelper::ACTIVE_ACTIVE);

            $table->integer('project_id')->default(0);
            $table->integer('image_id')->comment('Record data image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('record_data');
    }
}
