@section('results')
<div id="results" class="main-column">
    <h2>{{sizeof($jobPostings)}} Results</h2>

    <div id="sort-controls" class="clearfix">
        <form id="sort-controls-form" method="post" action="/postOptions">
        <input type="checkbox" name="karmaRank" id="karma-rank" @if ($karmaRank == "on") checked @endif>rank by
            karma
            <select id="days">
                <option value="1"
                @if ($days == "1") selected @endif>past day</option>
                <option value="3"
                @if ($days == "3") selected @endif>past 3 days</option>
                <option value="7"
                @if ($days == "7") selected @endif>past 7 days</option>
                <option value="30"
                @if ($days == "30") selected @endif>past 30 days</option>
            </select>
            <input type="hidden" name="keyword" value="{{$keyword}}">
            <input type="hidden" name="category" value="{{$category}}">
            <input type="hidden" name="location" value="{{$location}}">
            <input type="hidden" name="distance" value="{{$distance}}">
            {{--<input type="hidden" name="sort" value="{{$sort}}">--}}
            <input type="hidden" name="days" value="{{$days}}">
            <input type="hidden" name="id" value="{{$id}}">
        </form>
    </div>
    <div id="results-list">
        @foreach($jobPostings as $jobPosting)
        <div class="result-listing">
            @if ($jobPosting->id == $id) <b> @endif
                <a href="#{{$jobPosting->id}}" id="link{{$jobPosting->id}}" name="link">{{$jobPosting->title}}</a>
                {{--href="/search?keyword={{$keyword}}&category={{$category}}&location={{$location}}&distance={{$distance}}&days={{$days}}&karmaRank={{$karmaRank}}&id={{$jobPosting->id}}"--}}
                @if ($jobPosting->id == $id) </b> @endif
            <div class="time">{{$jobPosting->created_time}} in
                <a href="http://reddit.com/r/{{$jobPosting->subreddit_title}}" target="_blank">{{$jobPosting->subreddit_title}}</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@stop