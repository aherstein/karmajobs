<?php

/**
 * Created by PhpStorm.
 * User: aherstein
 * Date: 3/21/14
 * Time: 9:41 PM
 */
class Curl
{
    /**
     * Send a POST request using cURL
     * @param string $url to request
     * @param array $post values to send
     * @param array $options for cURL
     * @return array
     */
    public static function post($url, array $post = null, array $options = array())
    {
        $defaults = array(
            CURLOPT_POST           => 1,
            CURLOPT_HEADER         => 0,
            CURLOPT_URL            => $url,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_TIMEOUT => 400,
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
    public static function get($url)
    {
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 400
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
} 