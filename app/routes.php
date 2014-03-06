<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Route::get('/', function ()
//{
//    return View::make('hello');
//});

Route::get('subreddits', function ()
{
    $subreddits = Subreddit::all(); // Retrieve all rows in subreddits table

    return View::make('subreddits')->with('subreddits', $subreddits);
});

Route::get('insertsubreddits', function ()
{
    $subreddits = array();

    foreach ($subreddits as $subreddit)
    {
        $subredditObj = RedditApi::getSubreddit($subreddit);
        $subredditObj->save();
    }

    $subreddits = Subreddit::all(); // Retrieve all rows in subreddits table

    return View::make('subreddits')->with('subreddits', $subreddits);
});

Route::get('jobpostings', function ()
{
    $jobposting = new JobPosting;
    $jobposting->title = 'test';
    $jobposting->save();

    $jobpostings = JobPosting::all(); // Retrieve all rows in jobpostings table

    return View::make('jobpostings')->with('jobpostings', $jobpostings);
});

Route::get('getjobpostingsdebug', function ()
{
    $subreddits = Subreddit::all(); // Retrieve all rows in subreddits table
    foreach ($subreddits as $subreddit)
    {
        echo $subreddit->title;
        $jobPostings = RedditApi::getJobPostings($subreddit);

        echo "<pre>"; print_r($jobPostings); echo "</pre>";
    }

//    return View::make('jobpostings')->with('jobpostings', $jobpostings);
});

Route::get('getjobpostings', 'GetJobPostingsController@getJobPostings');

////////////////////////////////////////////////////////////////////////////////

Route::get('/', 'SearchResultsController@getAllJobPostings');
Route::get('search', 'SearchResultsController@getSearchResults');