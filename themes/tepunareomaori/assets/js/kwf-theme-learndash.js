jQuery(document).ready(function ($) {


    // toggle sidebar
    function setupToggleSidebar() {
        var dynamicStyle = $("<style>").attr("type", "text/css").appendTo("head");
        var sidebarWrapper = $(".lms-topic-sidebar-wrapper");

        var isSidebarClosed = localStorage.getItem("isSidebarClosed") === "true";

        dynamicStyle.text('.toggle-sidebar { left: ' + (isSidebarClosed ? '0' : '370px') + '; }');
        $(".toggle-sidebar").text(sidebarWrapper.hasClass("lms-topic-sidebar-close") ? " ▷" : " ◁");

        if (isSidebarClosed) {
            sidebarWrapper.addClass("lms-topic-sidebar-close");
        } else {
            sidebarWrapper.removeClass("lms-topic-sidebar-close");
        }

        function updateToggleIcons() {
            $(".toggle-sidebar").text(sidebarWrapper.hasClass("lms-topic-sidebar-close") ? " ▷" : " ◁");
            dynamicStyle.text('.toggle-sidebar { left: ' + (sidebarWrapper.hasClass("lms-topic-sidebar-close") ? '0' : '370px') + '; }');
            // Store the state in localStorage
            localStorage.setItem("isSidebarClosed", sidebarWrapper.hasClass("lms-topic-sidebar-close"));
        }

        function toggleSidebar() {
            sidebarWrapper.toggleClass("lms-topic-sidebar-close");
            updateToggleIcons();
        }

        $("#learndash-page-content").append('<div class="toggle-sidebar" style="position: fixed; top: 22%; cursor: pointer; background-color: #00A79D; color: #fff; padding: 5px; border-bottom-right-radius: 5px; border-top-right-radius: 5px; font-size: 13px; z-index: 999;">' +
            (sidebarWrapper.hasClass('lms-topic-sidebar-close') ? '▷' : '◁') +
            '</div>');


        $(".toggle-sidebar").click(function () {
            toggleSidebar();
        });
        $(".course-toggle-view").click(function () {
            toggleSidebar();
        });

        $(window).resize(function () {
            $(".toggle-sidebar").toggle($(window).width() > 767);
        });
    }



    /* Lesson header */

    $("#lessonDropdown").select2();
    $('#lessonDropdown').one('select2:open', function (e) {
        $('input.select2-search__field').prop('placeholder', TPRM_data.select_lesson);
    });
    //TPRM_data.select_lesson
    var $dropdown = $('#lessonDropdown');
    var $lessonsContent = $('#lessonsContent').children();

    $dropdown.on('change', function () {
        var selectedValue = $(this).val();

        // Update the URL without reloading the page
        if (selectedValue) {
            window.history.pushState({}, '', selectedValue);

            // Set the new URL and reload the page
            window.location.href = selectedValue;

            // Hide all lessons content
            $lessonsContent.hide();

            // Display the selected lesson's content
            var lessonId = $(this).find('option:selected').data('lesson-id');
            var $selectedLessonContent = $('#lessonContent_' + lessonId);
            if ($selectedLessonContent.length) {
                $selectedLessonContent.show();

                // Remove or comment out this block to stop redirection
                /*
                var $firstTopic = $selectedLessonContent.find('.bb-type-list .lms-topic-item a').first();
                if ($firstTopic.length) {
                    // Update URL with the first topic's URL
                    window.location.href = $firstTopic.attr('href');
                }
                */
            }
        }
    });

    // On page load, check the current URL and display the corresponding lesson content
    var currentUrl = window.location.href;
    $dropdown.find('option').each(function () {
        if (currentUrl.startsWith($(this).val())) { // Check if current URL matches the base value
            $dropdown.val($(this).val()); // Retain the selected value in the dropdown
            var lessonId = $(this).data('lesson-id');
            var $selectedLessonContent = $('#lessonContent_' + lessonId);
            if ($selectedLessonContent.length) {
                $selectedLessonContent.show();
            }
        }
    });


});