<?php

class JobPosting extends Eloquent
{

    public function subreddit()
    {
        return $this->belongsTo('Subreddit');
    }

}

?>