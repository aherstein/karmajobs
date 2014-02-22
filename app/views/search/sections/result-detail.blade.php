@section('result-detail')
<div id="result-detail"  class="main-column">
    @if ($selectedJobPosting->title != "")
    <a id="star-link"></a>
    <div id="post-text">
        <a id="back"></a>
        <h2>{{$selectedJobPosting->title}}</h2><span class="time">{{$selectedJobPosting->created_time}}</span>
        <div id="post-bar">submitted by: <a href="http://reddit.com/u/{{$selectedJobPosting->author}}">{{$selectedJobPosting->author}}</a>  in reddit.com/r/{{$selectedJobPosting->subreddit->title}}</div>
        <p>{{htmlspecialchars_decode($selectedJobPosting->selftext_html)}}</p>
    </div>
    <a href="http://reddit.com{{$selectedJobPosting->permalink}}" id="post-text-link">View Post on Reddit</a>
    @endif
</div>
@stop