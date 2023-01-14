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

            $table->enum('field_type', \App\Enums\Process\ProcessField::variants());

            $table->string('alias');
            $table->string('title');
            $table->integer('order');
            $table->smallInteger('is_active');

            $table->text('description')->nullable();
            $table->string('icon')->nullable();

            $table->boolean('required')->default(true);
            $table->text('default_value')->nullable();

            $table->integer('process_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('process_field_types');
    }
}
