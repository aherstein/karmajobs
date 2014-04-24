<?php

class PageController extends BaseController
{
    private function data($title)
    {
        // Get category counts
        $countJobs = number_format(JobPosting::jobs()->count());
        $countJobSeekers = number_format(JobPosting::jobSeekers()->count());
        $countDiscussions = number_format(JobPosting::discussions()->count());

        // Get categories from database
        $categories = Category::all();

        return array(

            'searchParams'     => array('keyword' => "", 'category' => "", 'location => ""),
            'keyword'          => "",
            'category'         => "",
            'location'         => "",
            'days'             => 7,
            'karmaRank'        => "off",
            'categories'       => $categories,
            'countJobs'        => $countJobs,
            'countJobSeekers'  => $countJobSeekers,
            'countDiscussions' => $countDiscussions,
            'title'            => "KarmaJobs - " . $title
        );
    }


    function about()
    {
        return View::make('page.layout', $this->data("About"))->nest('page', 'page.about');
    }


    function whyNoAds()
    {
        return View::make('page.layout', $this->data("Why No Ads?"))->nest('page', 'page.whynoads');
    }


    function contact()
    {
        return View::make('page.layout', $this->data("Contact"))->nest('page', 'page.contact');
    }
} 