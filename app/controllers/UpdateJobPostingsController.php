<?php

define("MAX_POSTS_TO_UPDATE", 10);

class UpdateJobPostingsController extends BaseController
{
    public function index()
    {
        Log::info("[" . get_class($this) . "] Starting run.");

        $returnArray = array();
        $subreddits = Subreddit::all(); // Retrieve all rows in subreddits table
        foreach ($subreddits as $subreddit)
        {
            Log::info("Getting updated job postings for: " . $subreddit->title);
            echo $subreddit->title . "<br>";
            $jobPostingsReddit = RedditApi::getJobPostingsForUpdate($subreddit, MAX_POSTS_TO_UPDATE - 1);
            $jobPostingsKarmaJobs = JobPosting::
                whereRaw("subreddit_id = " . $subreddit->id)
                ->orderBy('created_time', "DESC")
                ->take(MAX_POSTS_TO_UPDATE)
                ->get();

            $redditPostIds = array();
            $karmaJobsPostIds = array();

            array_push($redditPostIds, $subreddit->last_post_id); // Add last post id to the reddits array because we know we have that one in the database already.

            // Get all post IDs from reddit
            foreach ($jobPostingsReddit as $jobPosting)
            {
//                array_push($returnArray, array('subreddit' => $subreddit->title, 'title' => $jobPosting->title));
//                $jobPosting->save();
                array_push($redditPostIds, $jobPosting->reddit_post_id);
                echo $jobPosting->created_time . "<br>";
            }

            echo "<br>" . "<br>";

            // Get all post IDs from KarmaJobs database
            foreach ($jobPostingsKarmaJobs as $jobPosting)
            {
//                array_push($returnArray, array('subreddit' => $subreddit->title, 'title' => $jobPosting->title));
//                $jobPosting->save();
                array_push($karmaJobsPostIds, $jobPosting->reddit_post_id);
                echo $jobPosting->created_time . "<br>";
            }

            // Get the difference between the two posts.
            $diffPosts = array_diff($karmaJobsPostIds, $redditPostIds);

            //TODO Delete different posts

            echo "<pre>";
            print_r($redditPostIds);
            print_r($karmaJobsPostIds);
            print_r($diffPosts);
            echo "</pre>";

            return;
        }

        Log::info("[" . get_class($this) . "] Finished run.");

        return Response::json(array(
                'success'     => true,
                'error'       => false,
                'jobpostings' => $returnArray
            ),
            200
        );

    }

}

?>