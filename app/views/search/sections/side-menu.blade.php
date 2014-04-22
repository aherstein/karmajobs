@section('side-menu')
<div id="side-menu" class="main-column">
    <a id="search-toggle" href="#"></a>

    <div id="search">
        <h3>search parameters</h3>

        <form action="/post" method="post">
        <div class="input-wrap">
                <input class="text-input" name="keyword" value="{{$keyword}}" placeholder="keyword" type="text"/>
                <select name="category" id="filter">
                    @foreach($categories as $c)
                    <option value="{{$c->id}}"
                    @if ($category == "$c->id") selected @endif>{{$c->title}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-wrap">
                <input class="text-input-long" name="location" id="location" value="{{$location}}" placeholder="city or zip" type="text"/>
                {{--
                <select name="distance" id="distance">
                    <option value="25"
                    @if ($distance == "25") selected @endif>25 mi.</option>
                    <option value="50"
                    @if ($distance == "50") selected @endif>50 mi.</option>
                </select>
                --}}
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