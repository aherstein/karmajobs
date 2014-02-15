<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeys extends Migration
{
    public function up()
    {
        Schema::table('job_postings', function ($table)
        {
            $table->foreign('category_id')->references('category_id')->on('categories');
            $table->foreign('subreddit_id')->references('subreddit_id')->on('subreddits');
        });

        Schema::table('job_posting_views', function ($table)
        {
            $table->primary(array('job_id', 'ip_address', 'timestamp'));
            $table->foreign('job_id')->references('job_id')->on('job_postings');
        });
    }

    public function down()
    {
        Schema::table('job_postings', function ($table)
        {
            $table->dropForeign('job_postings_category_id_foreign');
            $table->dropForeign('job_postings_subreddit_id_foreign');
        });

        Schema::table('job_posting_views', function ($table)
        {
            $table->dropPrimary('job_posting_views_pkey');
            $table->dropForeign('job_posting_views_job_id_foreign');
        });

    }

}
