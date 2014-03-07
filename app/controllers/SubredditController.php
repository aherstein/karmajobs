<?php

class SubredditController extends BaseController
{
    public function index()
    {
        $subreddits = Subreddit::all();

        return $subreddits;
    }


    public function store()
    {
        $subreddit = Request::get('subreddit');
        $subredditObj = RedditApi::getSubreddit($subreddit);
        $subredditObj->save();

        App::error(function (Exception $exception)
        {
            return Response::json(array(
                    'success' => false,
                    'error'   => $exception
                ),
                200
            );
        });

        return Response::json(array(
                'success'   => true,
                'error'     => false,
                'subreddit' => $subredditObj->toArray()
            ),
            200
        );
    }
} 