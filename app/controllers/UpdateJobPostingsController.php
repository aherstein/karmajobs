<?php

//define("MAX_POSTS_TO_UPDATE", 25);

class UpdateJobPostingsController extends BaseController
{
	public function index()
	{
		set_time_limit(0);

		Log::info("[" . get_class($this) . "] Starting run.");

		$returnArray = array();
		$subreddits = Subreddit::all(); // Retrieve all rows in subreddits table
		foreach ($subreddits as $subreddit)
		{
			Log::info("Getting updated job postings for: " . $subreddit->title);
//            echo $subreddit->title . "<br>";
			try
			{
				$jobPostingsReddit = RedditApi::getJobPostingsForUpdate($subreddit, REDDIT_API_MAX_POSTS - 1);
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

			$jobPostingsKarmaJobs = JobPosting::
				where('subreddit_id', $subreddit->id)
				->orderBy('created_time', "DESC")
				->take(REDDIT_API_MAX_POSTS)
				->get();

			$redditPostIds = array();
			$karmaJobsPostIds = array();

			if (count($jobPostingsReddit) == 0) // The last post id was deleted from reddit, so we need to reset it so that the fetch job will repull the posts.
			{
				Log::info("Subreddit " . $subreddit->title . "last post id was deleted from reddit. Need to reset.");
				$subreddit->last_post_id = "";
				$subreddit->save();
				continue;
			}
			array_push($redditPostIds, $subreddit->last_post_id); // Add last post id to the reddits array because we know we have that one in the database already.

			// Get all post IDs from reddit
			foreach ($jobPostingsReddit as $jobPosting)
			{
				array_push($redditPostIds, $jobPosting->reddit_post_id);
			}

			// Get all post IDs from KarmaJobs database
			foreach ($jobPostingsKarmaJobs as $jobPosting)
			{
				array_push($karmaJobsPostIds, $jobPosting->reddit_post_id);
			}

			// Get the difference between the two posts.
			$diffPosts = array_diff($karmaJobsPostIds, $redditPostIds);

			// Delete posts
			foreach ($diffPosts as $postToDelete)
			{
				JobPosting::where('reddit_post_id', $postToDelete)->delete();
				array_push($returnArray, $postToDelete);
				Log::info("Deleted " . $postToDelete);
			}

//            echo "<pre>";
//            print_r($redditPostIds);
//            print_r($karmaJobsPostIds);
//            print_r($diffPosts);
//            echo "</pre>";
		}

		Log::info("[" . get_class($this) . "] Finished run.");

		return Response::json(array(
				'success'            => true,
				'error'              => false,
				'deletedjobpostings' => $returnArray
			),
			200
		);

	}

}

?>