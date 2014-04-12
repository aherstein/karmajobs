<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTimestampColFromJobPostingViews extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_posting_views', function ($table)
        {
            $table->dropPrimary('job_posting_views_pkey');
            $table->dropColumn('timestamp');
            $table->primary(array('job_id', 'ip_address', 'created_at'));

        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_posting_views', function ($table)
        {
            $table->dropPrimary('job_posting_views_pkey');
            $table->dateTime('timestamp');
            $table->primary(array('job_id', 'ip_address', 'timestamp'));
        });

    }

}
