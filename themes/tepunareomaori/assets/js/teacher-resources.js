jQuery(function ($) {
    var page = 2; // Start from page 2 since page 1 is already loaded
    var loading = false;
    var course_id = $('.ld-item-list').data('shortcode_instance').course_id;

    $(window).scroll(function () {

        if (loading) return;

        // Remove all occurrences of ld-section-heading except the first one
        $('.ld-section-heading:not(:first)').remove();

        $('body.resources .ld-pagination').html('<div class="tprm-loading"></div>');

        // Reset the margin top of ld-item-list
        $('.ld-item-list').css('marginTop', '0');

        var windowHeight = $(window).height();
        var documentHeight = $(document).height();
        var scrollPosition = $(window).scrollTop();
        var triggerHeight = documentHeight - windowHeight - 100; //

        if (scrollPosition >= triggerHeight) {
            loadMore(course_id);
        }

    });

    function loadMore(course_id) {
        loading = true;

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'load_more_course_content',
                page: page,
                course_id: course_id,
            },
            type: 'post',

            success: function (response) {

                loading = false;
                // Hide pagination if there is no more content
                if ($.trim(response) !== '') {
                    // Remove existing loader
                    $('body.resources .ld-pagination').remove();

                    // Append the new content to the container
                    $('body.resources .ld-item-list-items').append(response);
                    page++;

                    // feedback to the user
                    jQuery(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + teacher_resources.scroll_down + '</div>',
                            'info',
                            null,
                            true
                        ]
                    );

                } else {
                    // Hide loading spinner if there is no more content to load
                    $('body.resources .ld-pagination').remove();

                }

            }
        });
    }
});
