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
                if ($this[0].id == "days") window.location.href = "{{URL::route('search', $searchParams);}}?karmaRank={{$karmaRank}}&days=" + $("#days").val();
                if ($this[0].id == "days") window.location.href = "{{URL::route('search', $searchParams);}}?karmaRank={{$karmaRank}}&days=" + $("#days").val();

                // Hide location search box if category is discussion
                if ($this[0].id == "filter") {
                    if ($this.val() == "6") $('#location').hide();
                    else $('#location').show();
                }
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

        // Hide location search box if category is discussion
        @if ($category == 6)
            $('#location').hide()
            @endif

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