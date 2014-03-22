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


    private function getCityByZip($zipcode)
    {
        // Check to make sure we have a valid 5 digit zip code
        if (!(strlen($zipcode) == 5 && preg_match('/^[0-9]{5}$/', $zipcode))) // Is not a 5 digit zip code
        {
            return $zipcode;
        }

        $zipcodeObj = ZipCode::find($zipcode); // Search the database for the zip code

        if ($zipcodeObj == null) // If not found, call the SmartyStreets API and store that result inthe database
        {
            Log::info("No zipcode found in cache, calling API");
            $smartyStreets = Curl::get("https://api.smartystreets.com/zipcode/?auth-id=" . LOCATION_AUTH_ID . "&auth-token=" . LOCATION_AUTH_TOKEN . "&zipcode=$zipcode");

            if (isset($smartyStreets[0]['status'])) // Error
            {
                Log::info($smartyStreets[0]['reason']);

                return $smartyStreets[0]['reason'];
            }

            // Store the results from the API call into the database for caching
            $zipcodeObj = new ZipCode();
            $zipcodeObj->zip = $smartyStreets[0]['zipcodes'][0]['zipcode'];
            $zipcodeObj->city = $smartyStreets[0]['city_states'][0]['city'];
            $zipcodeObj->state_abbreviation = $smartyStreets[0]['city_states'][0]['state_abbreviation'];
            $zipcodeObj->state = $smartyStreets[0]['city_states'][0]['state'];
            $zipcodeObj->lat = $smartyStreets[0]['zipcodes'][0]['latitude'];
            $zipcodeObj->long = $smartyStreets[0]['zipcodes'][0]['longitude'];
            $zipcodeObj->save();
        }
        else
        {
            Log::info("Zipcode found in cache, retrieving from database.");
        }

        return $zipcodeObj->city;
    }


    public function getAllJobPostings()
    {
        $sort = Input::get('sort') != "" ? Input::get('sort') : "desc";
        $days = Input::get('days') != "" ? Input::get('days') : 1;
        $karmaRank = Input::get('karmaRank');
        $id = Input::get('id');

        // Get newest job posting
//        $selectedJobPostings = DB::table('job_postings')->order_by('created_time', 'desc')->first();
//        $selectedJobPostings[0]->created_time = $this->fuzzyDate($selectedJobPostings[0]->created_time);

        // Apply days filter
        $where = "now() - created_time < INTERVAL '$days days'";

        // Rank by karma
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

        // Get category counts
        $countJobs = JobPosting::jobs()->count();
        $countJobSeekers = JobPosting::jobSeekers()->count();
        $countDiscussions = JobPosting::discussions()->count();

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
            'categories'       => $categories,
            'countJobs'        => $countJobs,
            'countJobSeekers'  => $countJobSeekers,
            'countDiscussions' => $countDiscussions
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

        // Set filter (category) portion of WHERE clause
        if ($filter != 0)
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
            // Generate where portion of query – keyword amd days
            $where = "(lower(title) LIKE '%$keyword%' OR lower(selftext) LIKE '%$keyword%') AND category_id = '$filter' AND now() - created_time < INTERVAL '$days days' $whereFilter";

            // Process city field
            $cityWhere = "";
            if ($city != "") // City field is set
            {
                if (strlen($city) == 5 && preg_match('/^[0-9]{5}$/', $city)) // Is a 5 digit zip code
                {
                    $city = strtolower($this->getCityByZip($city));
                }
                $cityWhere = "AND (lower(title) LIKE '%$city%' OR lower(selftext) LIKE '%$city%')";
            }
            $where .= $cityWhere;

            // Rank by karma
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
                if ($keyword != "")
                {
                    setcookie("previousSearches", $_COOKIE['previousSearches'] . "," . $keyword, time() + 60 * 60 * 24 * 30, "/");
                }
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

        // Get category counts
        $countJobs = JobPosting::jobs()->count();
        $countJobSeekers = JobPosting::jobSeekers()->count();
        $countDiscussions = JobPosting::discussions()->count();

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
            'categories'       => $categories,
            'countJobs'        => $countJobs,
            'countJobSeekers'  => $countJobSeekers,
            'countDiscussions' => $countDiscussions
        ));
    }

}

?>