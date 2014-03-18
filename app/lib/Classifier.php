<?php

/**
 * Created by PhpStorm.
 * User: aherstein
 * Date: 2/19/14
 * Time: 9:45 PM
 */
class Classifier
{
    /*
        [DEFAULT] => 0
        [JOBS_JOB_SEEKERS] => 1
        [JOBS] => 2
        [JOB_SEEKERS] => 3
        [NON_PROFIT] => 4
        [INTERNSHIPS] => 5
        [JOB_DISCUSSION] => 6
        [CRYPTO_CURRENCY_JOBS] => 7
    */

    static $categories = array();


    protected static function getSubredditIdFromDatabase($category)
    {
        $categoryObj = Category::where('title', '=', $category)->get();

        return $categoryObj->id;
    }


    protected static function getCategories()
    {
        $categoryObjects = Category::all();

        foreach ($categoryObjects as $categoryObj)
        {
            // Convert title to enum format
            $title = strtoupper($categoryObj->title);
            $title = str_replace(" ", "_", $title);
            $title = str_replace("/", "_", $title);

            Classifier::$categories[$title] = $categoryObj->id;
        }
    }


    //@formatter:off
    protected static function classifyCategory($post)
    {
        //TODO Store this info in the database
        $combinationSubreddits = array("albertajobs","ArkansasJobs","atljobs","ausjobs","austinjobs","BaltimoreForHire","baltimorejobs","bigdatajobs","boisejobs","bostonjobs","boulderjobs","bristoljobs","buffalojobs","CalgaryJobs","cciejobs","charlestonjobs","charlottejobs","chicagojobs","ChinaJobs","CincinnatiJobs","CLEClassifieds","columbusclassifieds","ctjobs","dcjobs","DEjobs","denverjobs","designjobs","detroitjobs","dfwjobs","dsmjobs","edmontonJobs","EdmontonJobs","empleos_AR","forhire","geologycareers","gisjobs","grjobs","hfxjobs","HIJobs","houstonjobs","indyjobs","irejobs","JacksonvilleJobs","jobb","jobbit","jobsinOC","JobsPhilippines","KCjobs","kentuckianajobs","lajobs","london_forhire","longislandjobs","Louisianajobs","lvjobs","MexiJobs","mnjobs","montrealjobs","nashvillejobs","NetworkingJobs","norjobs","nycjobs","NYCjobs","okjobs","olyjobs","omahajobs","orlandojobs","Ottawa_jobs","ottawajobs","parkrangers","PDXEmployment","PDXjobs","PerthJobs","philadelphiajobs","PhillyJobs","phxjobs","pittsburghjobs","PortlandJobConnection","portlandjobs","PuertoRicoJobs","rijobs","ritforhire","SacJobs","SanAntonioJobs","sandiegojobs","scienceforhire","sdjobs","seajobs","seattlejobs","sfbayjobs","shovelbum","siouxfallsjobs","soflojobs","spacecoastjobs","SpokaneJobs","STLjobs","sysadminjobs","tampajobs","tcjobs","tesoljobs","ThaiJobs","TOjobexchange","TorontoJobPostings","torontoJobs","trianglejobs","tucsonjobs","ukjobs","utahjobs","vancouverjobs","wiscojobs","youngjobs");
        $jobsSubreddits = array("jobopenings");
        $nonprofitSubreddits = array("Nonprofit_Jobs");
        $internshipsSubreddits = array("Internships");
        $discussionSubreddits = array("AskHR","careerguidance","careeropportunities","cscareerquestions","DreamcareerHelp","entrepreneur","freelance","GetEmployed","HowsYourJob","InterviewFauxYou","jobnetworking","Jobs","jobsearchhacks","jobsecrets","resumes","retailmanagement","talesfromthejob","thisismyjob","work");

        // Lowercase all subreddits arrays
        for ($i = 0; $i < count($combinationSubreddits); $i++) $combinationSubreddits[$i] = strtolower($combinationSubreddits[$i]);
        for ($i = 0; $i < count($jobsSubreddits); $i++) $jobsSubreddits[$i] = strtolower($jobsSubreddits[$i]);
        for ($i = 0; $i < count($nonprofitSubreddits); $i++) $nonprofitSubreddits[$i] = strtolower($nonprofitSubreddits[$i]);
        for ($i = 0; $i < count($internshipsSubreddits); $i++) $internshipsSubreddits[$i] = strtolower($internshipsSubreddits[$i]);
        for ($i = 0; $i < count($discussionSubreddits); $i++) $discussionSubreddits[$i] = strtolower($discussionSubreddits[$i]);


        if (stristr($post['title'], "for hire") !== false) return Classifier::$categories['JOB_SEEKERS'];
        if (stristr($post['title'], "hiring") !== false) return Classifier::$categories['JOBS'];
        if (stristr($post['title'], "discussion") !== false) return Classifier::$categories['JOB_DISCUSSION'];
        if (in_array(strtolower($post['subreddit']), $jobsSubreddits)) return Classifier::$categories['JOBS'];
        if (in_array(strtolower($post['subreddit']), $nonprofitSubreddits)) return Classifier::$categories['NON_PROFIT'];
        if (in_array(strtolower($post['subreddit']), $nonprofitSubreddits)) return Classifier::$categories['NON_PROFIT'];
        if (in_array(strtolower($post['subreddit']), $internshipsSubreddits)) return Classifier::$categories['INTERNSHIPS'];
        if (in_array(strtolower($post['subreddit']), $discussionSubreddits)) return Classifier::$categories['JOB_DISCUSSION'];

        return 0;
    }


    public static function classify($post)
    {
        if (count(Classifier::$categories == 0)) Classifier::getCategories(); // Only need to get the categories once

        //TODO Write method body

        $returnArray = array(
            'category_id' => Classifier::classifyCategory($post),
            'location'    => "",
            'city'        => "",
            'state'       => "",
            'lat'         => 0.0,
            'long'        => 0.0
        );

        return $returnArray;
    }
} 