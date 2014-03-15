<?php

class GetJobPostingsController extends BaseController
{
    public function index()
    {
        $jobpostings = JobPosting::all();

        return $jobpostings;
    }
} 