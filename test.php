<?php


/**
 * Send a POST request using cURL
 * @param string $url to request
 * @param array $post values to send
 * @param array $options for cURL
 * @return string
 */
function curlPost($url, array $post = NULL, array $options = array())
{
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POSTFIELDS => http_build_query($post)
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}



/**
 * Send a GET request using cURL
 * @param string $url to request
 * @param array $get values to send
 * @param array $options for cURL
 * @return string
 */
function curlGet($url, array $get = NULL, array $options = array())
{
    $defaults = array(
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 4
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if( ! $result = curl_exec($ch))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}



function getPosts($sub, $before = "", $limit = 100)
{
    return json_decode(curlGet("http://api.reddit.com/r/$sub?before=$before&limit=$limit&count=0"), true)['data']['children'];
}


//$posts =  getPosts("chicago");
//echo "<pre>"; print_r($posts); echo "</pre>";

$mostRecentId = "t3_1xlr1i"; // Get mostRecentId from database here

$posts =  getPosts("funny", $mostRecentId);
$mostRecentId = "";
foreach ($posts as $post)
{
    $fullname = $post['kind'] . "_" . $post['data']['id'];
    $timestamp = $post['data']['created'];

    $formattedTime = date("Y-m-d h:i a", $timestamp);

    echo "<p>$fullname ($formattedTime)</p>"; // insert mostRecentId into database here

    if ($mostRecentId == "")
    {
        $mostRecentId = $fullname;
    }
//    echo "<pre>"; print_r($post); echo "</pre>";
}
echo "<p>Finished run. ". count($posts) ." posts pulled; most recent id: $mostRecentId</p>";


?>