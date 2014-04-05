<?php

class HistoricalJobPostingsController extends BaseController
{
    public function update($subredditTitle) // PUT
    {
        $startTime = time();
        Log::info("[" . get_class($this) . "] Starting run.");

        $returnArray = array();
//        $subreddits = Subreddit::where("title", $subredditTitle); // Retrieve specified row in subreddits table
        $subreddits = Subreddit::all();

        foreach ($subreddits as $subreddit)
        {
            if ($subreddit->title != $subredditTitle)
            {
                continue;
            }

            $lastPostId = $subreddit->last_post_id; // Get last post id from database

            for ($i = 0; $i < MAX_HISTORICAL_POSTS / REDDIT_API_MAX_POSTS; $i++) // Fetch 100 at a time until global limit is reached
            {
                $currBatch = $i * REDDIT_API_MAX_POSTS;
                $nextBatch = $currBatch + REDDIT_API_MAX_POSTS;
                Log::info("Fetching job postings for: " . $subreddit->title . " (batch $currBatch to $nextBatch)");

                try
                {
                    $jobPostings = RedditApi::getJobPostings($subreddit, true, $lastPostId);
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

                foreach ($jobPostings as $jobPosting)
                {
                    try
                    {
                        $jobPosting->save();
                    }
                    catch (Illuminate\Database\QueryException $e)
                    {
                        if (str_contains($e, "Unique violation")) // Duplicate job posting
                        {
                            Log::warning("Duplicate job posting: " . $jobPosting->title . " (" . $jobPosting->reddit_post_id . ")");
                            $lastPostId = $jobPosting->reddit_post_id;
                            continue;
                        }
                    }

                    $lastPostId = $jobPosting->reddit_post_id;

                    Log::info("Stored: " . $jobPosting->title);
                    array_push($returnArray, array('subreddit' => $subreddit->title, 'title' => $jobPosting->title));
                }
            }
        }

        Log::info("[" . get_class($this) . "] Finished run.");
        $endTime = time();

        $took = $endTime - $startTime;

        return Response::json(array(
                'success' => true,
                'error'   => false,
//                'jobpostings' => $returnArray
                'num'     => count($returnArray),
                'took'    => $took
            ),
            200
        );

    }

}

?>