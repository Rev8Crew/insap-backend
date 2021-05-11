<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('Record name');
            $table->string('description')->comment('Record description')->default('');
            $table->integer('order')->comment('Record order')->default(0 );
            $table->smallInteger('is_active')->comment('Is record active')->default(\App\helpers\IsActiveHelper::ACTIVE_ACTIVE);

            $table->integer('record_data_id')->default(0);
            $table->integer('user_id')->default(0);

            //$table->foreign('record_data_id')->references('id')->on('record_data')->onDelete('cascade');
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('records');
    }
}
