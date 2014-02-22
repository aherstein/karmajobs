<?php

class SearchResultsController extends BaseController
{
    private function fuzzyDate($timestamp)
    {
        if (preg_match("/[-\/:]/", $timestamp))
        {
            $timestamp = strtotime($timestamp);
        }

        if ($timestamp > time()) // All future dates
        {
            return date('m/d/y', $timestamp);
        }
        elseif ($timestamp >= mktime(0, 0, 0)) // Today
        {
            return date('G', time() - $timestamp) . " hours ago";
        }
        elseif ($timestamp >= mktime(0, 0, 0) - 86400) // Yesterday
        {
            return 'Yesterday';
        }
        elseif ($timestamp >= mktime(0, 0, 0) - 86400 * 7) // Within 7 days
        {
            return date('l', $timestamp);
        }
        elseif ($timestamp >= mktime(0, 0, 0, 1, 1)) // Within 1 year
        {
            return date('F j', $timestamp);
        }
        else // Older than 1 year
        {
            return date('m/d/Y', $timestamp);
        }
    }


    /**
     * @Deprecated
     */
    public function getAllSearchResults()
    {
        $sortOrder = Input::get('sort') != "" ? Input::get('sort') : "desc";

        // If job posting id is set, get that (i.e. user has clicked on a search result)
        if (Input::get('id') != "")
        {
            $selectedJobPosting = JobPosting::findOrFail(Input::get('id'));
            $selectedJobPosting->created_time = $this->fuzzyDate($selectedJobPosting->created_time);
        }
        else
        {
            $selectedJobPosting = new JobPosting();
        }

        $jobPostings = JobPosting::orderBy('created_time', 'desc')->get();

        foreach ($jobPostings as $jobPosting)
        {
            $jobPosting->created_time = $this->fuzzyDate($jobPosting->created_time);
        }

        return View::make('search.layout', array(
            'jobPostings'        => $jobPostings,
            'selectedJobPosting' => $selectedJobPosting
        ));
    }


    public function getSearchResults()
    {
        // Get variables from search form
        $keyword = strtolower(Input::get('keyword'));
        $filter = Input::get('filter') != "" ? Input::get('filter') : "jobs";
        $city = strtolower(Input::get('city'));
        $distance = Input::get('distance');
        $sort = Input::get('sort') != "" ? Input::get('sort') : "desc";

        // If job posting id is set, get that (i.e. user has clicked on a search result)
        if (Input::get('id') != "")
        {
            $selectedJobPosting = JobPosting::findOrFail(Input::get('id'));
            $selectedJobPosting->created_time = $this->fuzzyDate($selectedJobPosting->created_time);
        }
        else
        {
            $selectedJobPosting = new JobPosting(); // Set blank job posting for template
        }

        if ($filter == "jobs") $filter = "selftext"; // Jobs filter searches selftext field in JobPosting model

        if ($keyword == "" && $city == "") // No search
        {
            $jobPostings = JobPosting::orderBy('created_time', $sort)->get();
        }
        else // User searched for job postings
        {
            $jobPostings = JobPosting::
                whereRaw("lower($filter) LIKE '%$keyword%'")
                ->orderBy('created_time', $sort)
                ->get();
        }

        foreach ($jobPostings as $jobPosting)
        {
            $jobPosting->created_time = $this->fuzzyDate($jobPosting->created_time);
        }

        // Return the view. We need to pass back all the search criteria variables for the job posting links.
        return View::make('search.layout', array(
            'jobPostings'        => $jobPostings,
            'selectedJobPosting' => $selectedJobPosting,
            'keyword'            => $keyword,
            'filter'             => $filter,
            'city'               => $city,
            'distance'           => $distance,
            'sort'               => $sort
        ));
    }

}

?>