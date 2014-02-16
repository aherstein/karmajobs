<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedUpdatedFields extends Migration
{
    public function up()
    {
        Schema::table('job_postings', function ($table)
        {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        Schema::table('categories', function ($table)
        {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        Schema::table('subreddits', function ($table)
        {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });

        Schema::table('job_posting_views', function ($table)
        {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }


    public function down()
    {
        Schema::table('job_postings', function ($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('categories', function ($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('subreddits', function ($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });

        Schema::table('job_posting_views', function ($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
