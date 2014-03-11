<?php

class FetchJobPostingsController extends BaseController
{
    public function index()
    {
        Log::info("[" . get_class($this) . "] Starting run.");

        $returnArray = array();
        $subreddits = Subreddit::all(); // Retrieve all rows in subreddits table
        foreach ($subreddits as $subreddit)
        {
            Log::info("Fetching job postings for: " . $subreddit->title);
            $jobPostings = RedditApi::getJobPostings($subreddit);
            $lastPostId = "";

            foreach ($jobPostings as $jobPosting)
            {
                array_push($returnArray, array('subreddit' => $subreddit->title, 'title' => $jobPosting->title));
                $jobPosting->save();

                Log::info("Stored: " . $jobPosting->title);

                if ($lastPostId == "")
                {
                    // The reddit API returns posts in reverse chrono order – we want to make sure we only insert the first post ID in this loop
                    $lastPostId = $jobPosting->reddit_post_id;
                    $subreddit->last_post_id = $lastPostId;
                    $subreddit->save();
                    Log::info("Updated subreddit " . $subreddit->title . " with last post id: $lastPostId");
                }
            }
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