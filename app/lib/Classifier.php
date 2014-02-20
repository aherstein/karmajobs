<?php

/**
 * Created by PhpStorm.
 * User: aherstein
 * Date: 2/19/14
 * Time: 9:45 PM
 */
class Classifier
{

    protected static function getSubredditIdFromDatabase($category)
    {
        $categoryObj = Category::where('title', '=', $category)->get();
        return $categoryObj->id;
    }

    public static function Classify($post)
    {
        //TODO Write method body

        $returnArray = array(
            'category_id' => 0,
            'location'    => "",
            'city'        => "",
            'state'       => "",
            'lat'         => 0.0,
            'long'        => 0.0
        );

        return $returnArray;
    }
} 