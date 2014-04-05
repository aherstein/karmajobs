<?php
/**
 * This class makes calls to the Reddit API and returns respective models.
 * It DOES NOT make any pulls or insertions into the database – that is left to the controllers.
 */

//define("MAX_POSTS_TO_FETCH", 25);
define("DATE_FORMAT", "Y-m-d H:i:s");

class RedditApi
{

    /**
     * Checks for null values and returns a non-null value if null.
     */
    protected static function validate($field)
    {
        $type = gettype($field);

        switch ($type)
        {
            case "boolean":
                return is_null($field) ? false : $field;
            case "integer":
                return is_null($field) ? 0 : $field;
            case "float":
                return is_null($field) ? 0.0 : $field;
            case "string":
                return is_null($field) ? "" : $field;
            default:
                return 0;
        }
    }


    public static function getSubreddit($subreddit)
    {
        $result = Curl::get("http://api.reddit.com/r/$subreddit/about");

        $subredditObj = new Subreddit;
        $subredditObj->reddit_subreddit_id = $result['data']['name'];
        $subredditObj->title = $result['data']['display_name'];
        $subredditObj->last_post_id = "";
        $subredditObj->url = $result['data']['url'];
        $subredditObj->description = $result['data']['title'];

        return $subredditObj;
    }


    public static function getJobPostings($subreddit, $historical = false, $lastPostId = "")
    {
        $limit = REDDIT_API_MAX_POSTS;

        if ($historical)
        {
            $result = Curl::get("http://api.reddit.com/r/" . $subreddit->title . "?after=" . $lastPostId . "&limit=$limit&count=0");
        }
        else
        {
            $result = Curl::get("http://api.reddit.com/r/" . $subreddit->title . "?before=" . $subreddit->last_post_id . "&limit=$limit&count=0");
        }

        $postsArray = $result['data']['children']; // This is the path to the array of posts.

        $returnArray = array();
        foreach ($postsArray as $post)
        {
//          $fullname = $post['kind'] . "_" . $post['data']['id'];
            $post = $post['data']; // All the data we are after are in the data sub-array.

            if ($post['stickied'] == true) continue; // We don't want sticky posts, as they mess up the last post id (i.e. the post used to remenber where to start the pull next)

            $jobPostingObj = new JobPosting;
            $jobPostingObj->title = RedditApi::validate($post['title']);
            $jobPostingObj->selftext = RedditApi::validate($post['selftext']);
            $jobPostingObj->selftext_html = RedditApi::validate($post['selftext_html']);
            $jobPostingObj->is_self = $post['is_self'] == 1 ? true : false;
            $jobPostingObj->reddit_post_id = RedditApi::validate($post['name']);
            $jobPostingObj->clicked = RedditApi::validate($post['clicked']);
            $jobPostingObj->author = RedditApi::validate($post['author']);
            $jobPostingObj->score = RedditApi::validate($post['score']);
            $jobPostingObj->subreddit_id = RedditApi::validate($subreddit->id);
            $jobPostingObj->created_time = RedditApi::validate(date(DATE_FORMAT, $post['created']));
            $jobPostingObj->created_utc = RedditApi::validate(date(DATE_FORMAT, $post['created_utc']));
            $jobPostingObj->edited_time = RedditApi::validate(date(DATE_FORMAT, $post['edited']));
            $jobPostingObj->num_up_votes = RedditApi::validate($post['ups']);
            $jobPostingObj->num_down_votes = RedditApi::validate($post['downs']);
            $jobPostingObj->num_likes = RedditApi::validate($post['likes']);
            $jobPostingObj->num_comments = RedditApi::validate($post['num_comments']);
            $jobPostingObj->permalink = RedditApi::validate($post['permalink']);
            $jobPostingObj->domain = RedditApi::validate($post['domain']);

            // Classify data
            $classifiedData = Classifier::classify($post);
            $jobPostingObj->category_id = RedditApi::validate($classifiedData['category_id']);
            $jobPostingObj->location = RedditApi::validate($classifiedData['location']);
            $jobPostingObj->city = RedditApi::validate($classifiedData['city']);
            $jobPostingObj->state = RedditApi::validate($classifiedData['state']);
            $jobPostingObj->lat = RedditApi::validate($classifiedData['lat']);
            $jobPostingObj->long = RedditApi::validate($classifiedData['long']);

            $returnArray[] = $jobPostingObj; // Add the job posting to the return array.

        }

        return $returnArray;
    }


    /**
     * This function is used for the update job postings controller. It pulls old posts already processed so the
     * controller can check for removed items.
     * @param $subreddit
     * @param int $limit
     * @return array
     */
    public static function getJobPostingsForUpdate($subreddit, $limit = REDDIT_API_MAX_POSTS)
    {
        $result = Curl::get("http://api.reddit.com/r/" . $subreddit->title . "?after=" . $subreddit->last_post_id . "&limit=$limit&count=0");
        $postsArray = $result['data']['children']; // This is the path to the array of posts.

        $returnArray = array();
        foreach ($postsArray as $post)
        {
//          $fullname = $post['kind'] . "_" . $post['data']['id'];
            $post = $post['data']; // All the data we are after are in the data sub-array.

            if ($post['stickied'] == true) continue; // We don't want sticky posts, as they mess up the last post id (i.e. the post used to remenber where to start the pull next)

            $jobPostingObj = new JobPosting;
            $jobPostingObj->title = RedditApi::validate($post['title']);
            $jobPostingObj->selftext = RedditApi::validate($post['selftext']);
            $jobPostingObj->selftext_html = RedditApi::validate($post['selftext_html']);
            $jobPostingObj->is_self = $post['is_self'] == 1 ? true : false;
            $jobPostingObj->reddit_post_id = RedditApi::validate($post['name']);
            $jobPostingObj->clicked = RedditApi::validate($post['clicked']);
            $jobPostingObj->author = RedditApi::validate($post['author']);
            $jobPostingObj->score = RedditApi::validate($post['score']);
            $jobPostingObj->subreddit_id = RedditApi::validate($subreddit->id);
            $jobPostingObj->created_time = RedditApi::validate(date(DATE_FORMAT, $post['created']));
            $jobPostingObj->created_utc = RedditApi::validate(date(DATE_FORMAT, $post['created_utc']));
            $jobPostingObj->edited_time = RedditApi::validate(date(DATE_FORMAT, $post['edited']));
            $jobPostingObj->num_up_votes = RedditApi::validate($post['ups']);
            $jobPostingObj->num_down_votes = RedditApi::validate($post['downs']);
            $jobPostingObj->num_likes = RedditApi::validate($post['likes']);
            $jobPostingObj->num_comments = RedditApi::validate($post['num_comments']);
            $jobPostingObj->permalink = RedditApi::validate($post['permalink']);
            $jobPostingObj->domain = RedditApi::validate($post['domain']);

            $returnArray[] = $jobPostingObj; // Add the job posting to the return array.

        }

        return $returnArray;
    }
} 