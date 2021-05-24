<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImporterEventsTable extends Migration
{
    public function up()
    {
        Schema::create('importer_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->smallInteger('event')->nullable();
            $table->smallInteger('type')->nullable();

            $table->string('event_class')->nullable();

            $table->integer('appliance_id')->index()->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('importer_events');
    }
}
