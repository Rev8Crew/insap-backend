<?php

use App\helpers\IsActiveHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImporterEventsTable extends Migration
{
    public function up()
    {
        Schema::create('importer_events', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('name for event')->nullable();
            $table->smallInteger('event')->comment('list of events')->nullable();

            $table->smallInteger('order')->nullable();

            $table->integer('is_active')->default(IsActiveHelper::ACTIVE_ACTIVE);

            $table->string('interpreter_class')->comment('Interpreter like PHP, python, go ...')->nullable();
            $table->integer('importer_id')->index()->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('importer_events');
    }
}
