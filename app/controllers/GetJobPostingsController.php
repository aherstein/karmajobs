<?php

class GetJobPostingsController extends BaseController
{
    public function index()
    {
        $limit = Request::get('limit');

        $jobpostings = JobPosting::all()->take($limit);

        return $jobpostings;
    }
} 