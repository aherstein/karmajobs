<?php

class SubredditController extends BaseController
{
    public function index() // GET
    {
        $subreddits = Subreddit::all();

        $subredditsArray = Array();

        foreach ($subreddits as $subredditObj)
        {
            array_push($subredditsArray, $subredditObj->title);
        }

        return json_encode($subredditsArray);
    }


    public function update($subreddit) // PUT
    {
        try
        {
//            $subreddit = Request::get('s');
            $subredditObj = RedditApi::getSubreddit($subreddit);
            $subredditObj->save();
        }
        catch (\Illuminate\Database\QueryException $e)
        {
            if (stristr($e->getMessage(), "Unique violation:"))
            {
                return Response::json(array(
                        'success' => false,
                        'error'   => $subredditObj->title . " already exists.",
                    ),
                    500
                );
            }
            else
            {
                return Response::json(array(
                        'success' => false,
                        'error'   => $e->getMessage(),
                    ),
                    500
                );
            }
        }
        catch (Exception $e)
        {
            return Response::json(array(
                    'success' => false,
                    'error'   => $e->getMessage(),
                ),
                500
            );
        }

        return Response::json(array(
                'success'   => true,
                'error'     => false,
//                'subreddit' => $subredditObj->toArray()
                'subreddit' => $subredditObj->title
            ),
            200
        );
    }


    public function destroy($subreddit) // DELETE
    {
        try
        {
//            $subreddit = Request::get('s');
            Subreddit::where('title', '=', $subreddit)->delete();
        }
        catch (Exception $e)
        {
            return Response::json(array(
                    'success' => false,
                    'error'   => $e->getMessage(),
                ),
                500
            );
        }

        return Response::json(array(
                'success' => true,
                'error'   => false
            ),
            200
        );
    }

} 