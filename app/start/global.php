<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

    app_path() . '/commands',
    app_path() . '/controllers',
    app_path() . '/models',
    app_path() . '/database/seeds',
    app_path() . '/lib',

));

// Global constants
define("REDDIT_API_MAX_POSTS", 100);
define("MAX_HISTORICAL_POSTS", 1000);
define("LOCATION_AUTH_ID", "57df26a1-1cd4-4edc-857d-c66d5a504c6f");
define("LOCATION_AUTH_TOKEN", "qbE%2B9oHsvWFk0P0H9qkyB4adQl3Junk2dCUQEW%2BtJONW3ifFo%2FAknAGbCCg%2B0ynXz%2BNIawZVoyrkBJML6DwM5w%3D%3D");

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path() . '/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function (Exception $exception, $code)
{
    Log::error($exception);
});

App::missing(function ($exception)
{
	// Get category counts
	$countJobs = number_format(JobPosting::jobs()->count());
	$countJobSeekers = number_format(JobPosting::jobSeekers()->count());
	$countDiscussions = number_format(JobPosting::discussions()->count());

	// Get categories from database
	$categories = Category::all();

	$params = array(

		'searchParams'     => array('keyword' => "", 'category' => "", 'location' => ""),
		'keyword'          => "",
		'category'         => "",
		'location'         => "",
		'days'             => 7,
		'karmaRank'        => "off",
		'id'               => 0,
		'categories'       => $categories,
		'countJobs'        => $countJobs,
		'countJobSeekers'  => $countJobSeekers,
		'countDiscussions' => $countDiscussions
	);

	return Response::view('error.missing', $params, 404);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function ()
{
    return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path() . '/filters.php';
