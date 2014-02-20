<?php

class JobPosting extends Eloquent
{

    public function phone()
    {
        return $this->hasOne('Subreddit');
    }
}

?>