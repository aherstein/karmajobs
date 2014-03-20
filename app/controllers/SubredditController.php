<?php

class SubredditController extends BaseController
{
    public function index() // GET
    {
        $subreddits = Subreddit::all();

        return $subreddits;
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
                'subreddit' => $subredditObj->toArray()
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