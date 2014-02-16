<?php

define("MAX_POSTS_TO_FETCH", 100);

class RedditApi
{
    /**
     * Send a POST request using cURL
     * @param string $url to request
     * @param array $post values to send
     * @param array $options for cURL
     * @return array
     */
    protected static function curlPost($url, array $post = null, array $options = array())
    {
        $defaults = array(
            CURLOPT_POST           => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_URL            => $url,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_POSTFIELDS     => http_build_query($post)
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if (!$result = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true);
    }


    /**
     * Send a GET request using cURL
     * @param string $url to request
     * @param array $get values to send
     * @param array $options for cURL
     * @return array
     */
    protected static function curlGet($url)
    {
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 4
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        if (!$result = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true);
    }


    public static function getSubReddit($subreddit)
    {
        $result = RedditApi::curlGet("http://api.reddit.com/r/$subreddit/about");

        $subredditObj = new Subreddit;
        $subredditObj->reddit_subreddit_id = $result['data']['name'];
        $subredditObj->title = $result['data']['display_name'];
        $subredditObj->last_post_id = "";
        $subredditObj->url = $result['data']['url'];

        return $subredditObj;
    }


    public static function getPosts($subreddit, $before = "", $limit = MAX_POSTS_TO_FETCH)
    {
        // TODO Write method body.
    }
} 