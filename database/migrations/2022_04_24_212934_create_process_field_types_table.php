<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessFieldTypesTable extends Migration
{
    public function up()
    {
        Schema::create('process_field_types', function (Blueprint $table) {
            $table->id();

            $table->string('field_type')->comment('Example: text, number, dateTime, etc...');
            $table->string('alias');

            $table->string('title');
            $table->text('description');
            $table->string('icon');

            $table->integer('order');

            $table->smallInteger('is_active');

            $table->integer('process_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('process_field_types');
    }
}
