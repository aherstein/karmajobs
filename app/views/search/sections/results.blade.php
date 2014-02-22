@section('results')
<div id="results"  class="main-column">
    <h2>143 Results</h2>
    <div id="sort-controls" class="clearfix">
        <input type="checkbox" name="karma-rank" id="karma-rank">rank by karma
        <select id="sort-drop"><option>newest</option><option>oldest</option></select>
    </div>
    <div id="results-list">
        @foreach($jobPostings as $jobPosting)
        <div class="result-listing">
            <a href="/search?keyword={{$keyword}}&filter={{$filter}}&city={{$city}}&distance={{$distance}}&sort={{$sort}}&id={{$jobPosting->id}}">{{$jobPosting->title}}</a>
            <div class="time">{{$jobPosting->created_time}}</div>
        </div>
        @endforeach
    </div>
</div>
@stop