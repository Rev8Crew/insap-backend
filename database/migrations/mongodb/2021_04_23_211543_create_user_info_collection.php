<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;

class CreateUserInfoCollection extends Migration
{
    /**
     *
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $collection) {
            $collection->index('user_id');
        });
    }

    /**
     *
     */
    public function down()
    {
        Schema::connection('mongodb')->drop('user_infos');
    }
}
