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
    <title>{{$title}}</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="js/jquery.mousewheel.min.js" type="application/javascript"></script>
    <script src="js/jquery.mCustomScrollbar.min.js" type="application/javascript"></script>
    <script src="js/icheck.min.js" type="application/javascript"></script>
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="css/minimal/minimal.css">
    <link rel="stylesheet" href="css/minimal/grey.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="/img/favicon.png">

    <script>
        // Iterate over each select element to create custom element
        function setupSelects() {
            jQuery('select').each(function () {

                // Cache the number of options
                var $this = $(this),
                    numberOfOptions = $(this).children('option').length;

                // Hides the select element
                $this.addClass('s-hidden');

                // Wrap the select element in a div
                $this.wrap('<div class="select"></div>');

                // Insert a styled div to sit over the top of the hidden select element
                $this.after('<div class="styledSelect"></div>');

                // Cache the styled div
                var $styledSelect = $this.next('div.styledSelect');

                // Show the selected option in the styled div
                for (var i = 0; i < numberOfOptions; i++) {
                    if ($this[0][i].selected) $styledSelect.text($this.children('option').eq(i).text());
                }

                // Insert an unordered list after the styled div and also cache the list
                var $list = $('<ul />', {
                    'class': 'options'
                }).insertAfter($styledSelect);

                // Insert a list item into the unordered list for each select option
                for (var i = 0; i < numberOfOptions; i++) {
                    $('<li />', {
                        text: $this.children('option').eq(i).text(),
                        rel: $this.children('option').eq(i).val()
                    }).appendTo($list);
                }

                // Cache the list items
                var $listItems = $list.children('li');

                // Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
                $styledSelect.click(function (e) {
                    e.stopPropagation();
                    if (jQuery('div.styledSelect.active').length) {
                        $('div.styledSelect.active').each(function () {
                            $(this).removeClass('active').next('ul.options').hide();
                        });
                    } else {
                        $(this).toggleClass('active').next('ul.options').toggle();
                    }


                });

                // Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
                // Updates the select element to have the value of the equivalent option
                $listItems.click(function (e) {
                    e.stopPropagation();
                    $styledSelect.text($(this).text()).removeClass('active');
                    $this.val($(this).attr('rel'));
                    $list.hide();
                    /* alert($this.val()); /* Uncomment this for demonstration! */

                    /**
                     * Workaround for inability to use jQuery events with this styling function
                     */
                    if ($this[0].id == "days") window.location.href = "/search?keyword={{$keyword}}&filter={{$filter}}&city={{$city}}&distance={{$distance}}&karmaRank={{$karmaRank}}&days=" + $("#days").val();
                });

                // Hides the unordered list when clicking outside of it
                $(document).click(function () {
                    $styledSelect.removeClass('active');
                    $list.hide();
                });

            });
        }
    </script>

    <script>
        jQuery(document).ready(function () {
            // Load post ID if exists
            var id = document.location.hash;
            if (id != "") {
                getResultsDetail(id.slice(1));
            }

            // Custom scrollbars
            jQuery("#results-list").mCustomScrollbar({
                scrollInertia: 0
            });
            jQuery("#post-text").mCustomScrollbar({
                scrollInertia: 0
            });

            setupSelects();
            $('#karma-rank').iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal',
                cursor: true
            });

            jQuery('#search-toggle').on("click", function (event) {
                jQuery('#side-menu').toggleClass('expanded')
            });

            jQuery('#previous-toggle').on("click", function (event) {
                jQuery('#previous-search').toggleClass('expanded')
            });

            jQuery('.result-listing a').on("click", function (event) {
                jQuery('#result-detail').addClass('activated')
            });

            jQuery('#back').on("click", function (event) {
                jQuery('#result-detail').removeClass('activated')
            });

            $('#karma-rank').on('ifChecked', function (event) {
                $("#sort-controls-form").submit();
            });
            $('#karma-rank').on('ifUnchecked', function (event) {
                $("#sort-controls-form").submit();
            });

        });
    </script>

    <script>
        function getResultsDetail(id) {
            $('#result-detail').load('/ajax/result-detail?id=' + id); // Replace result detail with content via ajax
            $('#result-detail').scrollTop(0); // Scroll to the top

            // Unbold all other links
            arr = document.getElementsByName('link');
            for (var i = 0; i < arr.length; i++) {
                var obj = document.getElementsByName('link').item(i)
                obj.style.fontWeight = 'normal';
            }

            // Bold the current link
            document.getElementById('link' + id).style.fontWeight = 'bold';
        }
    </script>

    <script>
        window.onhashchange = function () {
            var id = document.location.hash;
            getResultsDetail(id.slice(1));
        }

    </script>
</head>
<body>
@include('includes.google-analytics')
@yield('top-bar')
<div id="middle">
    @yield('side-menu')
    @yield('results')
    <div id="result-detail" class="main-column">
        @yield('result-detail')
    </div>
    @yield('previous-search')
</div>
@yield('footer')
</body>
</html>

