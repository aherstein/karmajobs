@section('previous-search')
<div id="previous-search"  class="main-column">
    <a id="previous-toggle" href="#"></a>
    <h2>previous searches</h2>
    <div class="searches">
        @foreach($previousSearches as $previousSearch)
        <a href="/search?keyword={{$previousSearch}}">{{$previousSearch}}</a>
        @endforeach
    </div>
</div>
@stop