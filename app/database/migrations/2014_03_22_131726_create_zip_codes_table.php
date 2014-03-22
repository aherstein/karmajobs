<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZipCodesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zip_codes', function ($table)
        {
            $table->string('zip');
            $table->string('city');
            $table->string('state_abbreviation');
            $table->string('state');
            $table->float('lat');
            $table->float('long');
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->softDeletes();
            $table->primary('zip');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('zip_codes');
    }

}
