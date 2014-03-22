@section('top-bar')
<div id="top-bar">
    <div id="top-status">
        <a href="/" id="karma-header"><img src="img/karmajobs.png"/> </a>

        <div class="status-text">{{$countJobs}} jobs</div>
        <div class="status-text">{{$countJobSeekers}} job seekers</div>
        <div class="status-text">{{$countDiscussions}} job discussions</div>
    </div>
    {{--
    <div id="top-links">
        <a href="#" id="welcome-link">Hi <strong>solidwhetstone</strong></a>
        <a href="#" id="mail-link"></a>
        <a href="#" id="profile-link">edit profile</a>
        <a href="#" id="logoff-link">log off</a>
        <a href="#" id="settings-link"></a>
    </div>
    --}}
</div>
@stop