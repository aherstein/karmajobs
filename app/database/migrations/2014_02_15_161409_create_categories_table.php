<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{

    public function up()
    {
        Schema::create('categories', function ($table)
        {
            $table->increments('category_id');
            $table->string('title');
        });
    }

    public function down()
    {
        Schema::drop('categories');
    }

}
