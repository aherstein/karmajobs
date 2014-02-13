@extends('layout')

@section('content')
    @foreach($subreddits as $subreddit)
        <p><a href="http://reddit.com{{$subreddit->url}}">{{$subreddit->title}}</a></p>
    @endforeach
@stop