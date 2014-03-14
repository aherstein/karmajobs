@section('side-menu')
<div id="side-menu" class="main-column">
    <a id="search-toggle" href="#"></a>

    <div id="search">
        <h3>search parameters</h3>

        <form action="/search">
        <div class="input-wrap">
                <input class="text-input" name="keyword" value="{{$keyword}}" placeholder="keyword" type="text"/>
                <select name="filter" id="filter">
                    <option value="1"
                    @if ($filter == "1") selected @endif>Jobs/Job Seekers</option>
                    <option value="2"
                    @if ($filter == "2") selected @endif>Jobs</option>
                    <option value="3"
                    @if ($filter == "3") selected @endif>Job Seekers</option>
                    <option value="4"
                    @if ($filter == "4") selected @endif>Non Profit</option>
                    <option value="5"
                    @if ($filter == "5") selected @endif>Internships</option>
                    <option value="6"
                    @if ($filter == "6") selected @endif>Job Discussion</option>
                    <option value="7"
                    @if ($filter == "7") selected @endif>Crypto Currency Jobs</option>
                    <option value="0"
                    @if ($filter == "0") selected @endif>default (for dev purposes only)</option>
                </select>
            </div>
            <div class="input-wrap">
                <input class="text-input" name="city" value="{{$city}}" placeholder="city or zip" type="text"/>
                <select name="distance" id="distance">
                    <option value="25"
                    @if ($distance == "25") selected @endif>25 mi.</option>
                    <option value="50"
                    @if ($distance == "50") selected @endif>50 mi.</option>
                </select>
            </div>
            <input type="hidden" name="days" value="{{$days}}">
            <input type="hidden" name="karmaRank" value="{{$karmaRank}}">
            <button type="submit" value="search" id="submit">search</button>
        </form>
    </div>
    {{--
    <div id="post-links" class="clearfix">
        <h3>post on karmajobs</h3>
        <a href="#">i am hiring</a>
        <a href="#">i am for hire</a>
    </div>
    <div id="later-link">
        <h3>check out later</h3>
        <a href="#">show favorites</a>
    </div>
    --}}
</div>
@stop