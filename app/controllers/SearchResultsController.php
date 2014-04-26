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
            Log::info("No ZIP code found  for $zipcode in cache, calling API");
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
            Log::info("ZIP code $zipcode found in cache, retrieving from database.");
        }

        return $zipcodeObj->city;
    }


    private function setPreviousSearchesCookie($keyword, $category, $location)
    {
        //Generate cookie string
        $search = $keyword . ":" . $category . ":" . $location;

        // Set/get previous searches list
        if (!isset($_COOKIE['previousSearches3']))
        {
            if ($search != "::") setcookie("previousSearches3", $search, time() + 60 * 60 * 24 * 30, "/");
            $previousSearches = array();
            if ($search != "::") array_push($previousSearches, $search);
        }
        else
        {
            if ($search != "::") setcookie("previousSearches3", $_COOKIE['previousSearches3'] . "," . $search, time() + 60 * 60 * 24 * 30, "/");
            $previousSearches = explode(",", $_COOKIE['previousSearches3']); // Split searches by ,
            if ($search != "::") array_push($previousSearches, $search);
            $previousSearches = array_unique(array_reverse($previousSearches));

        }

        // Split search items (keyword, category, location) by :
        $previousSearchesExpanded = array();
        foreach ($previousSearches as $previousSearch)
        {
            $previousSearchArray = explode(":", $previousSearch);
            array_push($previousSearchesExpanded, $this->normalizeParameters($previousSearchArray[0], $previousSearchArray[1], $previousSearchArray[2]));
        }

        return $previousSearchesExpanded;
    }


    private function setViewedJobPostingsCookie($id)
    {
        if (!isset($_COOKIE['viewedJobPostings']))
        {
            if ($id != "") setcookie("viewedJobPostings", $id, time() + 60 * 60 * 24 * 30, "/");
        }
        else
        {
            if ($id != "") setcookie("viewedJobPostings", $_COOKIE['viewedJobPostings'] . "," . $id, time() + 60 * 60 * 24 * 30, "/");
        }
    }


    private function getViewedJobPostingsCookie()
    {
        return isset($_COOKIE['viewedJobPostings']) ? explode(",", $_COOKIE['viewedJobPostings']) : array();
    }


    private function getCategoryIdFromName($category)
    {
        $category = ucwords(str_replace("-", " ", $category));

        $categories = Category::all();

        foreach ($categories as $categoryObj)
        {
            if ($categoryObj->title == $category) return $categoryObj->id;
        }

        return 2; // Default to Job category
    }


    private function normalizeParameters($keyword, $category, $location)
    {
        // Clean up by removing unwanted characters
        $keyword = preg_replace("[^ 0-9a-zA-Z]", " ", $keyword);
        $location = preg_replace("[^ 0-9a-zA-Z]", " ", $location);

        // Remove commas and colons
        $keyword = str_replace(",", " ", $keyword);
        $keyword = str_replace(":", " ", $keyword);
        $location = str_replace(",", " ", $location);
        $location = str_replace(":", " ", $location);

        // Remove multiple adjacent spaces
        while (strstr($keyword, "  "))
        {
            $keyword = str_replace("  ", " ", $keyword);
        }
        while (strstr($location, "  "))
        {
            $location = str_replace("  ", " ", $location);
        }

        // Replace single spaces with a URL friendly dash
        $keyword = str_replace(" ", "-", $keyword);
        $category = str_replace(" ", "-", $category);
        $location = str_replace(" ", "-", $location);

        return array(
            'keyword'  => $keyword,
            'category' => $category,
            'location' => $location
        );
    }


    private function title($keyword, $category, $location, $selectedJobPosting = false)
    {
        $prefix = "";
        // Replace dashes with spaces
        $keyword = ucwords(str_replace("-", " ", $keyword));
        $category = ucwords(str_replace("-", " ", $category));
        $location = ucwords(str_replace("-", " ", $location));

        if ($keyword == "" && $category == "" && $location == "" && $selectedJobPosting != false)
        {
            return preg_replace("/\\[.*\\]/", "", $selectedJobPosting->title); // Remove tag from title
        }

        if ($selectedJobPosting == false || $selectedJobPosting->title == "")
        {
            if (strtolower($category) == "discussion")
            {
                return "$prefix $keyword $category";
            }
            else if (strtolower($location) == "everywhere")
            {
                return "$prefix $keyword $category everywhere";
            }
            else
            {
                return "$prefix $keyword $category in $location";
            }
        }
        else
        {
            $jobPostingTitle = preg_replace("/\\[.*\\]/", "", $selectedJobPosting->title); // Remove tag from title
            if (strtolower($category) == "discussion")
            {
                return "$prefix $keyword $category – " . $jobPostingTitle;;
            }
            else if (strtolower($location) == "everywhere")
            {
                return "$prefix $keyword $category everywhere – " . $jobPostingTitle;
            }
            else
            {
                return "$prefix $keyword $category in $location – " . $jobPostingTitle;
            }
        }
    }


    private function renderSearchResults($keyword, $category, $location, $distance, $sort, $days, $karmaRank, $id, $searchParams = array())
    {
        // If job posting id is set, get that (i.e. user has clicked on a search result)
        if ($id != "")
        {
            $selectedJobPosting = JobPosting::findOrFail($id);
            $selectedJobPosting->created_time = $this->fuzzyDate($selectedJobPosting->created_time);

            //Insert into job postings views table
            try
            {
                $jobPostingView = new JobPostingView;
                $jobPostingView->job_id = $id;
                $jobPostingView->ip_address = $_SERVER['REMOTE_ADDR'];
                $jobPostingView->save();
            }
            catch (Illuminate\Database\QueryException $e)
            {
                Log::info("Duplicate job posting view: " . $jobPostingView);
            }
        }
        else
        {
            $selectedJobPosting = new JobPosting(); // Set blank job posting for template
        }

        // Set filter (category) portion of WHERE clause
        if ($category != 0)
        {
            $whereFilter = "  AND category_id = $category";
        }
        else
        {
            $whereFilter = "";
        }

        // Search options
        if ($keyword == "" && $location == "") // No search
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

            $previousSearches = $this->setPreviousSearchesCookie($searchParams['keyword'], $searchParams['category'], $searchParams['location']);
        }
        else // User searched for job postings
        {
            // Generate where portion of query – keyword amd days
            $where = "(lower(job_postings.title) LIKE '%$keyword%' OR lower(selftext) LIKE '%$keyword%') AND category_id = '$category' AND now() - created_time < INTERVAL '$days days' $whereFilter";

            // Process city field
            $cityWhere = "";
            if ($location != "") // City field is set
            {
                if (strlen($location) == 5 && preg_match('/^[0-9]{5}$/', $location)) // Is a 5 digit zip code
                {
                    $location = strtolower($this->getCityByZip($location));
                }
                $cityWhere = "AND (lower(job_postings.title) LIKE '%$location%' OR lower(selftext) LIKE '%$location%' OR lower(subreddits.title) LIKE '%$location%' OR lower(subreddits.description) LIKE '%$location%')";
            }
            $where .= $cityWhere; // Append city where clause to the main where clause

            // Rank by karma
            if ($karmaRank == "on")
            {
                $jobPostings = Subreddit::join('job_postings', 'job_postings.subreddit_id', '=', 'subreddits.id') // Join the subreddits table so we can search on subreddit title
                    ->whereRaw($where)
                    ->orderBy('num_up_votes', "desc")
                    ->get();
            }
            else
            {
                $jobPostings = Subreddit::join('job_postings', 'subreddits.id', '=', 'job_postings.subreddit_id') // Join the subreddits table so we can search on subreddit title
                    ->whereRaw($where)
                    ->orderBy('created_time', $sort)
                    ->get();
            }

            // If no results are returned, increade the day range
            if (sizeof($jobPostings) == 0 && $days < 30)
            {
                return $this->renderSearchResults($keyword, $category, $location, "", $sort, 30, $karmaRank, $id, $searchParams);
            }

            $previousSearches = $this->setPreviousSearchesCookie($searchParams['keyword'], $searchParams['category'], $searchParams['location']);

            // Open first job posting
            if ($id == "")
            {
                foreach ($jobPostings as $jobPosting)
                {
                    $id = $jobPosting->id;
                    $selectedJobPosting = JobPosting::findOrFail($jobPosting->id);
                    $selectedJobPosting->created_time = $this->fuzzyDate($selectedJobPosting->created_time);
                    $this->setViewedJobPostingsCookie($id);
                    break;
                }
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
        $countJobs = number_format(JobPosting::jobs()->count());
        $countJobSeekers = number_format(JobPosting::jobSeekers()->count());
        $countDiscussions = number_format(JobPosting::discussions()->count());


        // Since we are using the join method for the query (I think), we can't call the subreddit object for the job postings, so we create the field here
        foreach ($jobPostings as $j)
        {
            $j->subreddit_title = str_replace("/", "", str_replace("/r/", "", $j->url));
        }

        // Return the view. We need to pass back all the search criteria variables for the job posting links.
        return View::make('search.layout', array(
            'jobPostings'        => $jobPostings,
            'selectedJobPosting' => $selectedJobPosting,
            'searchParams'      => $searchParams,
            'keyword'            => $keyword,
            'category'          => $category,
            'location'          => $location,
            'distance'           => $distance,
            'sort'               => $sort,
            'days'               => $days,
            'karmaRank'          => $karmaRank,
            'id'                 => $id,
            'previousSearches'   => $previousSearches,
            'viewedJobPostings' => $this->getViewedJobPostingsCookie(),
            'categories'         => $categories,
            'countJobs'          => $countJobs,
            'countJobSeekers'    => $countJobSeekers,
            'countDiscussions'   => $countDiscussions,
            'title'             => $this->title($searchParams['keyword'], $searchParams['category'], $searchParams['location'], $selectedJobPosting)
        ));
    }


    public function getSearchResults($keyword = "", $category = "", $location = "")
    {
        // Get variables from search form
//        $keyword = strtolower(Input::get('keyword'));
//        $category = Input::get('filter') != "" ? Input::get('filter') : 0;
//        $location = strtolower(Input::get('city'));
//        $distance = Input::get('distance');
        $sort = Input::get('sort') != "" ? Input::get('sort') : "desc";
        $days = Input::get('days') != "" ? Input::get('days') : 7;
        $karmaRank = Input::get('karmaRank');
        $id = Input::get('id');

//        if ($this->checkForOldPreviousSearchesCookie()) return Redirect::to(URL::route('home')); // Check for old version of previous searched cookie that causes error if parsed

        // If no search and previous search cookie has been set, show the last entered search
        if (isset($_COOKIE['previousSearches3']) && $keyword == "" && $location == "")
        {
            $previousSearches = array_unique(array_reverse(explode(",", $_COOKIE['previousSearches3'])));
            $previousSearch = explode(":", $previousSearches[0]);

            $keyword = $previousSearch[0];
            $category = $previousSearch[1];
            $location = $previousSearch[2];

        }

        // Replace dashes with spaces
        $keyword = str_replace("-", " ", $keyword);
        $category = str_replace("-", " ", $category);
        $location = str_replace("-", " ", $location);

        $searchParams = $this->normalizeParameters($keyword, $category, $location);

        // Get category ID from name
        $category = $this->getCategoryIdFromName($category);

        // Process default values "all" and "everywhere"
        if ($keyword == "all") $keyword = "";
        if ($location == "everywhere") $location = "";

        return $this->renderSearchResults($keyword, $category, $location, "", $sort, $days, $karmaRank, $id, $searchParams);

    }


    // Ajax functions
    public function resultDetail($id, $keyword = "", $category = "", $location = "")
    {
        $selectedJobPosting = JobPosting::findOrFail($id);
        $selectedJobPosting->created_time = $this->fuzzyDate($selectedJobPosting->created_time);

        $this->setViewedJobPostingsCookie($id);

        return View::make('search.ajax.result-detail', array(
            'selectedJobPosting' => $selectedJobPosting,
            'title'              => $this->title($keyword, $category, $location, $selectedJobPosting)
        ));
    }


    // Pretty URL post method
    public function post()
    {
        // Get variables from search form
        $keyword = Input::get("keyword") != "" ? strtolower(Input::get("keyword", "all")) : "all";
        $category = Input::get("category", 2);
        $location = Input::get("location") != "" ? strtolower(Input::get("location", "everywhere")) : "everywhere";
//      $distance = Input::get('distance');

        // Get category name from database and add it to the URL
        $categoryObj = Category::findOrFail($category);
        $category = strtolower($categoryObj->title);

        $searchParams = $this->normalizeParameters($keyword, $category, $location);

        $url = URL::route('search', $searchParams);

        return Redirect::to($url);
    }


    // Pretty URL post method with search options (karma rank and time frame)
    public function postOptions()
    {
        // Get variables from search form
        $keyword = Input::get("keyword") != "" ? strtolower(Input::get("keyword", "all")) : "all";
        $category = Input::get("category", 2);
        $location = Input::get("location") != "" ? strtolower(Input::get("location", "everywhere")) : "everywhere";
//      $distance = Input::get('distance');
        $karmaRank = Input::get('karmaRank');
        $days = Input::get('days');
        $id = Input::get('id');

        // Get category name from database and add it to the URL
        $categoryObj = Category::findOrFail($category);
        $category = strtolower($categoryObj->title);

        $searchParams = $this->normalizeParameters($keyword, $category, $location);

        $url = URL::route('search', $searchParams) . "/?karmaRank=$karmaRank&days=$days";

        return Redirect::to($url);
    }

}

?>