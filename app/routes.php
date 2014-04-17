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

Route::get('/', 'SearchResultsController@getAllJobPostings');
Route::get('search', 'SearchResultsController@getSearchResults');

// Ajax
Route::get('/ajax/result-detail', array(
    'as'   => 'search.ajax.result-detail',
    'uses' => 'SearchResultsController@resultDetail'
));

Route::group(array('prefix' => 'api', 'before' => 'auth.basic'), function ()
{
    Route::resource('subreddit', 'SubredditController');
    Route::resource('jobpostings', 'GetJobPostingsController');
    Route::resource('fetchjobpostings', 'FetchJobPostingsController');
    Route::resource('updatejobpostings', 'UpdateJobPostingsController');
    Route::resource('historical', 'HistoricalJobPostingsController');
});

//Route::get('test', function ()
//{
//    $job = new JobPosting();
//    $job->title = "[discussion] great job!";
////    $job->subreddit->title = "cscareerquestions";
//
//    echo "<pre>";
//    print_r(Classifier::classify($job));
//    echo "</pre>";
//});

Route::get('info', function ()
{
    phpinfo();
});