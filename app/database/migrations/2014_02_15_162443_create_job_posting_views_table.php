<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobPostingViewsTable extends Migration
{


    public function up()
    {
        Schema::create('job_posting_views', function ($table)
        {
            $table->integer('job_id');
            $table->string('ip_address');
            $table->dateTime('timestamp');
        });
    }


    public function down()
    {
        Schema::drop('job_posting_views');
    }

}
