<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCategories extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $category = Category::find(1);
        $category->delete();

        $category = Category::find(6);
        $category->title = "Discussion";
        $category->save();

        $category = Category::find(7);
        $category->title = "Crypto Jobs";
        $category->save();
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
