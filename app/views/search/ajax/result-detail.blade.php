@include('search.sections.result-detail')
<script>document.title = "{{$title}}"</script>
<script>$("#post-text").mCustomScrollbar({scrollInertia: 0});</script>
@yield('result-detail')