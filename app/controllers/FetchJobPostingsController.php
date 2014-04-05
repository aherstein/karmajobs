<?php

class FetchJobPostingsController extends BaseController
{
    public function index()
    {
        $startTime = time();
        Log::info("[" . get_class($this) . "] Starting run.");

        $returnArray = array();
        $subreddits = Subreddit::all(); // Retrieve all rows in subreddits table
        foreach ($subreddits as $subreddit)
        {
            Log::info("Fetching job postings for: " . $subreddit->title);

            try
            {
                $jobPostings = RedditApi::getJobPostings($subreddit);
            }
            catch (ErrorException $e)
            {
                return Response::json(array(
                        'success' => false,
                        'error'   => $e->getMessage()
                    ),
                    500
                );
            }
            $lastPostId = "";

            foreach ($jobPostings as $jobPosting)
            {
                if ($lastPostId == "")
                {
                    // The reddit API returns posts in reverse chrono order – we want to make sure we only insert the first post ID in this loop
                    $lastPostId = $jobPosting->reddit_post_id;
                    $subreddit->last_post_id = $lastPostId;
                    $subreddit->save();
                    Log::info("Updated subreddit " . $subreddit->title . " with last post id: $lastPostId");
                }

                try
                {
                    $jobPosting->save();
                }
                catch (Illuminate\Database\QueryException $e)
                {
                    if (str_contains($e, "Unique violation")) // Duplicate job posting
                    {
                        Log::warning("Duplicate job posting: " . $jobPosting->title . " (" . $jobPosting->reddit_post_id . ")");
                        continue;
                    }
                }

                Log::info("Stored: " . $jobPosting->title);
                array_push($returnArray, array('subreddit' => $subreddit->title, 'title' => $jobPosting->title));

            }
        }

        Log::info("[" . get_class($this) . "] Finished run.");
        $endTime = time();

        $took = $endTime - $startTime;

        return Response::json(array(
                'success'     => true,
                'error'       => false,
//                'jobpostings' => $returnArray
                'num'  => count($returnArray),
                'took' => $took
            ),
            200
        );

    }

}

?>