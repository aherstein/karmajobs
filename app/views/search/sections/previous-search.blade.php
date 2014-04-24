@section('previous-search')
<div id="previous-search" class="main-column">
    <a id="previous-toggle" href="#"></a>

    <h2>previous searches</h2>

    <div class="searches">
        @foreach($previousSearches as $previousSearch)
        <a href="{{URL::route('search', $previousSearch);}}">{{str_replace("-", " ", $previousSearch['keyword'])}} {{str_replace("-", " ", $previousSearch['category'])}} in
            {{str_replace("-", " ", $previousSearch['location'])}}</a>
        @endforeach
    </div>
</div>
@stop