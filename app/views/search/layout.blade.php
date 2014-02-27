@include('search.sections.top-bar')
@include('search.sections.side-menu')
@include('search.sections.results')
@include('search.sections.result-detail')
@include('search.sections.previous-search')
@include('search.sections.footer')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="cleartype" content="on">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>KarmaJobs</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="js/jquery.mousewheel.min.js" type="application/javascript"></script>
    <script src="js/jquery.mCustomScrollbar.min.js" type="application/javascript"></script>
    <script src="js/icheck.min.js" type="application/javascript"></script>
    <script src="js/main.js" type="application/javascript"></script>
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="css/minimal/minimal.css">
    <link rel="stylesheet" href="css/minimal/grey.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/styles.css">

    <script>
        jQuery(document).ready(function(){
            setupSelects();
            $('#karma-rank').iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal',
                cursor: true
            });

            jQuery('#search-toggle').on("click", function(event){
                jQuery('#side-menu').toggleClass('expanded')
            });

            jQuery('#previous-toggle').on("click", function(event){
                jQuery('#previous-search').toggleClass('expanded')
            });

            jQuery('.result-listing a').on("click", function(event){
                jQuery('#result-detail').addClass('activated')
            });

            jQuery('#back').on("click", function(event){
                jQuery('#result-detail').removeClass('activated')
            });
        });
        jQuery(window).load(function(){
            jQuery("#results-list").mCustomScrollbar({
                scrollInertia:0
            });
            jQuery("#post-text").mCustomScrollbar({
                scrollInertia:0
            });
        });


        // Side Menu Column
        $("#filter").val("{{$filter}}");


        // Results Column
        $("#days").val("{{$days}}");

        $(":checkbox[id='karma-rank']").click(function () {
            $("#sort-controls-form").submit();
        });

        $("#days").change(function () {
            window.location.href = "/search?keyword={{$keyword}}&filter={{$filter}}&city={{$city}}&distance={{$distance}}&days=" + $("#days").val();
        });
    </script>
</head>
<body>
    @yield('top-bar')
    <div id="middle">
        @yield('side-menu')
        @yield('results')
        @yield('result-detail')
        @yield('previous-search')
    </div>
    @yield('footer')
</body>
</html>

