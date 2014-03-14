<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertCategories extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $c = new Category();
        $c->title = "Jobs/Job Seekers";
        $c->save();

        $c = new Category();
        $c->title = "Jobs";
        $c->save();

        $c = new Category();
        $c->title = "Job Seekers";
        $c->save();

        $c = new Category();
        $c->title = "Non Profit";
        $c->save();

        $c = new Category();
        $c->title = "Internships";
        $c->save();

        $c = new Category();
        $c->title = "Job Discussion";
        $c->save();

        $c = new Category();
        $c->title = "Crypto Currency Jobs";
        $c->save();
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
