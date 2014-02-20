<?php
class GetJobPostingsController extends BaseController
{
    public function getJobPostings()
    {
        $subreddits = Subreddit::all(); // Retrieve all rows in subreddits table
        foreach ($subreddits as $subreddit)
        {
            echo "<p>Getting job postings for: ".$subreddit->title."</p>"; //TODO Remove HTML
            $jobPostings = RedditApi::getJobPostings($subreddit);
            $lastPostId = "";

            foreach ($jobPostings as $jobPosting)
            {
                $jobPosting->save();

                echo "<p>Stored: ".$jobPosting->title."</p>"; //TODO Remove HTML

                if ($lastPostId == "")
                {
                    // The reddit API returns posts in reverse chrono order – we want to make sure we only insert the first post ID in this loop
                    $lastPostId = $jobPosting->reddit_post_id;
                    $subreddit->last_post_id = $lastPostId;
                    $subreddit->save();
                    echo "<p>Updated subreddit ".$subreddit->title." with last post id: $lastPostId.</p>"; //TODO Remove HTML
                }
            }
        }

//      return View::make('getjobpostings.results', array('results' => $results));
    }

}
?>