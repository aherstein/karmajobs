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

//Route::get('/', array('as' => 'home', 'uses' => 'SearchResultsController@getSearchResults'));
Route::get('/', array('as' => 'home', 'uses' => 'PageController@home'));
Route::get('search/{keyword?}/{category?}/{location?}', array('as' => 'search', 'uses' => 'SearchResultsController@getSearchResults'));
Route::post('post', 'SearchResultsController@post');
Route::post('postOptions', 'SearchResultsController@postOptions');

// Pages
Route::get('about', array('as' => 'about', 'uses' => 'PageController@about'));
Route::get('whynoads', array('as' => 'whynoads', 'uses' => 'PageController@whyNoAds'));
Route::get('contact', array('as' => 'contact', 'uses' => 'PageController@contact'));

// Ajax
Route::get('/ajax/result-detail/{id}/{keyword?}/{category?}/{location?}', array('as' => 'search.ajax.result-detail', 'uses' => 'SearchResultsController@resultDetail'));

// Sitemap
Route::get('sitemap.xml', array('as' => 'sitemap', 'uses' => 'SiteMapController@getSiteMap'));

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