<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobPostingsTable extends Migration
{
	public function up()
	{
        Schema::create('job_postings', function($table)
        {
            $table->increments('job_id');
            $table->integer('category_id');
            $table->string('title');
            $table->string('selftext');
            $table->string('selftext_html');
            $table->boolean('is_self');
            $table->string('reddit_post_id');
            $table->boolean('clicked');
            $table->string('author');
            $table->integer('score');
            $table->integer('subreddit_id');
            $table->dateTime('created_time');
            $table->dateTime('created_utc');
            $table->dateTime('edited_time');
            $table->integer('num_up_votes');
            $table->integer('num_down_votes');
            $table->integer('num_likes');
            $table->integer('num_comments');
            $table->string('location');
            $table->string('permalink');
            $table->string('domain');
            $table->string('city');
            $table->string('state');
            $table->float('lat');
            $table->float('long');
        });
	}

	public function down()
	{
        Schema::drop('job_postings');
	}

}
