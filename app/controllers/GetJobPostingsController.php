<?php
class GetJobPostingsController extends BaseController
{
    public function getJobPostings()
    {
        $results = array();

        $subreddits = Subreddit::all();

        foreach( $subreddits as $subreddit)
        {
            $lastPostId = $subreddit->last_post_id; // Get lastPostId from database

            $posts =  RedditApi::getPosts($subreddit, $lastPostId);
            $lastPostId = "";
            foreach ($posts as $post)
            {
                $fullname = $post['kind'] . "_" . $post['data']['id'];
                $timestamp = $post['data']['created'];

                $formattedTime = date("Y-m-d h:i a", $timestamp);

                array_push($results, array('subreddit' => $subreddit, 'fullname' => $fullname, 'time' => $formattedTime)); // TODO insert lastPostId into database here

                if ($lastPostId == "")
                {
                    $lastPostId = $fullname;
                }
            }
        }
//        echo "<p>Finished run. ". count($posts) ." posts pulled; most recent id: $mostRecentId</p>";

        return View::make('getjobpostings.results', array('results' => $results));
    }

}
?>