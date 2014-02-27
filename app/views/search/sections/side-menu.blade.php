@section('side-menu')
<script>$("#filter").change("{{$filter}}");</script>
<div id="side-menu" class="main-column">
    <a id="search-toggle" href="#"></a>

    <div id="search">
        <h3>search parameters</h3>

        <form>
            <div class="input-wrap">
                <input class="text-input" name="keyword" value="{{$keyword}}" placeholder="keyword" type="text"/>
                <select name="filter" id="filter">
                    <option value="selftext">jobs</option>
                    <option value="title">titles</option>
                </select>
            </div>
            <div class="input-wrap">
                <input class="text-input" name="city" value="{{$city}}" placeholder="city or zip" type="text"/>
                <select name="distance" id="distance">
                    <option value="25">25 mi.</option>
                    <option value="50">50 mi.</option>
                </select>
            </div>
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