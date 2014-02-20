<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeJobpostingsSelftextToLongtest extends Migration
{

    public function up()
    {
        DB::update('alter table job_postings alter column selftext type text');
        DB::update('alter table job_postings alter column selftext_html type text');
    }


    public function down()
    {
        DB::update('alter table job_postings alter column selftext type varchar(255)');
        DB::update('alter table job_postings alter column selftext_html type varchar(255)');
    }

}
