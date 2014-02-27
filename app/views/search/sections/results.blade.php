@section('results')
<div id="results" class="main-column">
    <h2>{{sizeof($jobPostings)}} Results</h2>

    <div id="sort-controls" class="clearfix">
        <form id="sort-controls-form">
            @if ($karmaRank == "on")
            <input type="checkbox" name="karmaRank" id="karma-rank" checked>rank by karma
            @else
            <input type="checkbox" name="karmaRank" id="karma-rank">rank by karma
            @endif
            <select id="days">
                <option value="1">past day</option>
                <option value="3">past 3 days</option>
                <option value="7">past 7 days</option>
                <option value="30">past 30 days</option>
            </select>
            <input type="hidden" name="keyword" value="{{$keyword}}">
            <input type="hidden" name="filter" value="{{$filter}}">
            <input type="hidden" name="city" value="{{$city}}">
            <input type="hidden" name="distance" value="{{$distance}}">
            {{--<input type="hidden" name="sort" value="{{$sort}}">--}}
            <input type="hidden" name="days" value="{{$days}}">
            <input type="hidden" name="id" value="{{$id}}">
        </form>
    </div>
    <div id="results-list">
        @foreach($jobPostings as $jobPosting)
        <div class="result-listing">
            <a href="/search?keyword={{$keyword}}&filter={{$filter}}&city={{$city}}&distance={{$distance}}&days={{$days}}&id={{$jobPosting->id}}">{{$jobPosting->title}}</a>

            <div class="time">{{$jobPosting->created_time}}</div>
        </div>
        @endforeach
    </div>
</div>
@stop