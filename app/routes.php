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

Route::group(array('prefix' => 'api', 'before' => 'auth.basic'), function ()
{
    Route::resource('subreddit', 'SubredditController');
    Route::resource('jobpostings', 'GetJobPostingsController');
    Route::resource('fetchjobpostings', 'FetchJobPostingsController');
    Route::resource('updatejobpostings', 'UpdateJobPostingsController');
});

Route::get('info', function ()
{
    phpinfo();
});