<?php

class JobPosting extends Eloquent
{
    protected $softDelete = true;


    public function subreddit()
    {
        return $this->belongsTo('Subreddit');
    }


    public function category()
    {
        return $this->belongsTo('Category');
    }

}

?>