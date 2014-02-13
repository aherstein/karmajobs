<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubredditsTable extends Migration
{
	public function up()
	{
        Schema::create('subreddits', function($table)
        {
            $table->increments('subreddit_id');
            $table->string('reddit_subreddit_id');
            $table->string('title');
            $table->string('last_post_id');
            $table->string('url');
        });
	}

	public function down()
	{
        Schema::drop('subreddits');
	}
}
