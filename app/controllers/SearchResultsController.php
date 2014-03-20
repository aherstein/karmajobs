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


    public function getAllJobPostings()
    {
        $sort = Input::get('sort') != "" ? Input::get('sort') : "desc";
        $days = Input::get('days') != "" ? Input::get('days') : 1;
        $karmaRank = Input::get('karmaRank');
        $id = Input::get('id');

        $where = "now() - created_time < INTERVAL '$days days'";

        if ($karmaRank == "on")
        {
            $jobPostings = JobPosting::
                whereRaw($where)
                ->orderBy('num_up_votes', "desc")
                ->get();
        }
        else
        {
            $jobPostings = JobPosting::
                whereRaw($where)
                ->orderBy('created_time', $sort)
                ->get();
        }

        // Set/get previous searches list
        if (!isset($_COOKIE['previousSearches']))
        {
            setcookie("previousSearches", "", time() + 60 * 60 * 24 * 30, "/");
            $previousSearches = array();
        }
        else
        {
            $previousSearches = array_unique(array_reverse(explode(",", $_COOKIE['previousSearches'])));
        }

        // Apply fuzzy dates
        foreach ($jobPostings as $jobPosting)
        {
            $jobPosting->created_time = $this->fuzzyDate($jobPosting->created_time);
        }

        // Get categories from database
        $categories = Category::all();

        return View::make('search.layout', array(
            'jobPostings'        => $jobPostings,
            'selectedJobPosting' => new JobPosting(),
            'keyword'            => "",
            'filter'             => "",
            'city'               => "",
            'distance'           => "",
            'sort'               => $sort,
            'days'               => $days,
            'karmaRank'          => $karmaRank,
            'id'                 => $id,
            'previousSearches' => $previousSearches,
            'categories'       => $categories
        ));

    }


    public function getSearchResults()
    {
        // Get variables from search form
        $keyword = strtolower(Input::get('keyword'));
        $filter = Input::get('filter') != "" ? Input::get('filter') : 0;
        $city = strtolower(Input::get('city'));
        $distance = Input::get('distance');
        $sort = Input::get('sort') != "" ? Input::get('sort') : "desc";
        $days = Input::get('days') != "" ? Input::get('days') : 1;
        $karmaRank = Input::get('karmaRank');
        $id = Input::get('id');

        // If job posting id is set, get that (i.e. user has clicked on a search result)
        if ($id != "")
        {
            $selectedJobPosting = JobPosting::findOrFail($id);
            $selectedJobPosting->created_time = $this->fuzzyDate($selectedJobPosting->created_time);
        }
        else
        {
            $selectedJobPosting = new JobPosting(); // Set blank job posting for template
        }

        // Set filter portion of WHERE clause
        if ($filter == 1) // Jobs/Job Postings combined category
        {
            $whereFilter = "  AND (category_id = 2 OR category_id = 3)";
        }
        elseif ($filter != 0)
        {
            $whereFilter = "  AND category_id = $filter";
        }
        else
        {
            $whereFilter = "";
        }

        // Search options
        if ($keyword == "" && $city == "") // No search
        {
            $where = "now() - created_time < INTERVAL '$days days' $whereFilter";

            if ($karmaRank == "on")
            {
                $jobPostings = JobPosting::
                    whereRaw($where)
                    ->orderBy('num_up_votes', "desc")
                    ->get();
            }
            else
            {
                $jobPostings = JobPosting::
                    whereRaw($where)
                    ->orderBy('created_time', $sort)
                    ->get();
            }

            // Set/get previous searches list
            if (!isset($_COOKIE['previousSearches']))
            {
                setcookie("previousSearches", $keyword, time() + 60 * 60 * 24 * 30, "/");
                $previousSearches = array();
            }
            else
            {
                $previousSearches = array_unique(array_reverse(explode(",", $_COOKIE['previousSearches'])));
            }
        }
        else // User searched for job postings
        {
            $where = "(lower(title) LIKE '%$keyword%' OR lower(selftext) LIKE '%$keyword%') AND category_id = '$filter' AND now() - created_time < INTERVAL '$days days' $whereFilter";

            if ($karmaRank == "on")
            {
                $jobPostings = JobPosting::
                    whereRaw($where)
                    ->orderBy('num_up_votes', "desc")
                    ->get();
            }
            else
            {
                $jobPostings = JobPosting::
                    whereRaw($where)
                    ->orderBy('created_time', $sort)
                    ->get();
            }

            // Set/get previous searches list
            if (!isset($_COOKIE['previousSearches']))
            {
                setcookie("previousSearches", $keyword, time() + 60 * 60 * 24 * 30, "/");
                $previousSearches = array();
            }
            else
            {
                setcookie("previousSearches", $_COOKIE['previousSearches'] . "," . $keyword, time() + 60 * 60 * 24 * 30, "/");
                $previousSearches = array_unique(array_reverse(explode(",", $_COOKIE['previousSearches'])));
            }

        }

        // Apply fuzzy dates
        foreach ($jobPostings as $jobPosting)
        {
            $jobPosting->created_time = $this->fuzzyDate($jobPosting->created_time);
        }

        // Get categories from database
        $categories = Category::all();

        // Return the view. We need to pass back all the search criteria variables for the job posting links.
        return View::make('search.layout', array(
            'jobPostings'        => $jobPostings,
            'selectedJobPosting' => $selectedJobPosting,
            'keyword'            => $keyword,
            'filter'             => $filter,
            'city'               => $city,
            'distance'           => $distance,
            'sort'             => $sort,
            'days'             => $days,
            'karmaRank'        => $karmaRank,
            'id'               => $id,
            'previousSearches' => $previousSearches,
            'categories'       => $categories
        ));
    }

}

?>