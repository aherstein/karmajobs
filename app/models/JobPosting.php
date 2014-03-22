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


    public function scopeJobs($query)
    {
        return $query->where('category_id', '=', 2);
    }


    public function scopeJobSeekers($query)
    {
        return $query->where('category_id', '=', 3);
    }


    public function scopeNonProfits($query)
    {
        return $query->where('category_id', '=', 4);
    }


    public function scopeInternships($query)
    {
        return $query->where('category_id', '=', 5);
    }


    public function scopeDiscussions($query)
    {
        return $query->where('category_id', '=', 6);
    }


    public function scopeCryptoJobs($query)
    {
        return $query->where('category_id', '=', 7);
    }

}

?>