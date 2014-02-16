<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameIdFields extends Migration
{

    public function up()
    {
        Schema::table('job_postings', function($table)
        {
            $table->renameColumn('job_id', 'id');
        });

        Schema::table('subreddits', function($table)
        {
            $table->renameColumn('subreddit_id', 'id');
        });

        Schema::table('categories', function($table)
        {
            $table->renameColumn('category_id', 'id');
        });
    }

    public function down()
    {
        Schema::table('job_postings', function($table)
        {
            $table->renameColumn('id', 'job_id');
        });

        Schema::table('subreddits', function($table)
        {
            $table->renameColumn('id', 'subreddit_id');
        });

        Schema::table('categories', function($table)
        {
            $table->renameColumn('id', 'category_id');
        });
    }

}
