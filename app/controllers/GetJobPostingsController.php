<?php
class GetJobPostingsController extends BaseController
{
    /**
     * Send a GET request using cURL
     * @param string $url to request
     * @param array $get values to send
     * @param array $options for cURL
     * @return string
     */
    public function curlGet($url)
    {
        $defaults = array(
            CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 4
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($defaults));
        if( ! $result = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }



    public function getPosts($sub, $before = "", $limit = 100)
    {
        $url = "http://api.reddit.com/r/$sub?before=$before&limit=$limit&count=0";

        $defaults = array(
            CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 4
        );

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        if( ! $result = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true)['data']['children']; // TODO Figure out why data is not coming back from that call.

//        return json_decode($this->curlGet("http://api.reddit.com/r/$sub?before=$before&limit=$limit&count=0"), true)['data']['children'];
    }

////////////////////////////////////////////////////////////////////////////////

    public function getJobPostings()
    {
        $results = array();

        $subreddits = Subreddit::all();

        foreach( $subreddits as $subreddit)
        {
            $mostRecentId = ""; // TODO Get mostRecentId from database here

            $posts =  $this->getPosts($subreddit, $mostRecentId, 4);
            $mostRecentId = "";
            foreach ($posts as $post)
            {
                $fullname = $post['kind'] . "_" . $post['data']['id'];
                $timestamp = $post['data']['created'];

                $formattedTime = date("Y-m-d h:i a", $timestamp);

                array_push($results, array('subreddit' => $subreddit, 'fullname' => $fullname, 'time' => $formattedTime)); // TODO insert mostRecentId into database here

                if ($mostRecentId == "")
                {
                    $mostRecentId = $fullname;
                }
            }
        }
//        echo "<p>Finished run. ". count($posts) ." posts pulled; most recent id: $mostRecentId</p>";

        return View::make('getjobpostings.results', array('results' => $results));
    }

}
?>