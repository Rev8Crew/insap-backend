<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatedProcessTable extends Migration
{
    public function up()
    {
        Schema::create('related_process', static function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('child_id')->nullable();

            $table->foreign('parent_id')->references('id')->on('processes');
            $table->foreign('child_id')->references('id')->on('processes');

            $table->timestamps();
        });
    }
}
