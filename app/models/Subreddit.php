<?php

class Subreddit extends Eloquent
{
    public function jobPostings()
    {
        return $this->has_many('JobPosting');
    }
}

?>