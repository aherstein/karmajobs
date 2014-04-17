@section('result-detail')
@if ($selectedJobPosting->title != "")
{{--<a id="star-link"></a>--}}
<div id="post-text">
    <a id="back" href="javascript:$('#result-detail').removeClass('activated');"></a>

    <h2>{{$selectedJobPosting->title}}</h2><span class="time">{{$selectedJobPosting->created_time}}</span>

    <div id="post-bar">submitted by: <a href="http://reddit.com/u/{{$selectedJobPosting->author}}">{{$selectedJobPosting->author}}</a>
        in <a href="http://reddit.com/r/{{$selectedJobPosting->subreddit->title}}" target="_blank">{{$selectedJobPosting->subreddit->title}}</a>
    </div>
    @if ($selectedJobPosting->is_self)
    <p>{{htmlspecialchars_decode($selectedJobPosting->selftext_html)}}</p>
    @else
    <p>Job posting is a link to {{$selectedJobPosting->domain}}.</p>
    @endif

    <a href="http://reddit.com{{$selectedJobPosting->permalink}}" id="post-text-link" target="_blank">View Post on Reddit</a>
</div>

@endif
@stop