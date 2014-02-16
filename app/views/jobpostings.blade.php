@extends('layout')

@section('content')
    @foreach($jobpostings as $jobposting)
        <p><a href="{{$jobposting->permalink}}">{{$jobposting->title}}</a></p>
    @endforeach
@stop