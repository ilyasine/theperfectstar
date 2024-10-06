jQuery(document).ready(function ($) {
    
    // open related courses popup
    window.BBLMS.switchLdGridList = function () { };

    $('#course-dir-list #courses-list #see-related-courses').magnificPopup({
        type: 'inline',
        fixedContentPos: false,
        //midClick: true,
        callbacks: {
            open: function() {
                // Add a class to the body to prevent scrolling
                $('body').addClass('noscroll');                        
            },
            close: function() {
                // Remove the class from the body
                $('body').removeClass('noscroll');
            }
        },
    });

    // Variable to keep track of the previously selected element
    var $prevSelected = null;

    $('.ld-item-name').on('click', function (e) {
        //e.preventDefault(); // Prevent the default action of the anchor

        var related_course = $(this).data('related_course');
        var notices = $('.see-related-courses-head-notices');
        var courseName =  $(this).data('course_name');

        //console.log('related_course : ' + related_course)
        
        // Check if the clicked element is marked as completed
        var $statusIcon = $(this).find('.ld-status-icon');
        var isSelected = $statusIcon.hasClass('ld-status-complete');
        
        // Toggle the completion status of the clicked element
        $statusIcon.toggleClass('ld-status-complete', !isSelected);
        $statusIcon.toggleClass('ld-status-incomplete', isSelected);
        
        // Toggle the checkmark icon based on the completion status
        if (!isSelected) {
            $statusIcon.append('<span class="ld-icon ld-icon-checkmark"></span>');
            
            // Remove completed status from the previously selected element
            if ($prevSelected !== null) {
                $prevSelected.find('.ld-status-icon').removeClass('ld-status-complete').addClass('ld-status-incomplete');
                $prevSelected.find('.ld-icon-checkmark').remove();
                $prevSelected.removeClass('ld-primary-color');
            }
            
            // Update the previously selected element
            $prevSelected = $(this);
            $(this).addClass('ld-primary-color');
            // selected
            notices.html(manage_courses_data.course_selected + '<strong>' + courseName + '</strong>');
        } else {
            $statusIcon.find('.ld-icon-checkmark').remove();
            // If the current element was marked as completed, clear the previously selected element
            $prevSelected = null;
            $(this).removeClass('ld-primary-color');
            // no select
            notices.text(manage_courses_data.no_course_selected);
        }

        // Update the data-selected_course_id attribute of #confirm_selected_course
        $('#confirm_selected_course').attr('data-selected_course_id', isSelected ? '' : related_course);
    });

    //confirm selected course
    $('.confirm_selected_course').on('click', function (e) {
        e.preventDefault();

        var button = $(this);
        var security = button.data('security');
        var current_course_id = button.data('current_course_id');
        var ld_group_id = button.data('group');
        var selected_course_id = $(this).attr('data-selected_course_id');

        if (selected_course_id) { // Check if selected_course_id has a value
            NProgress.start();
            NProgress.set(0.4);

            var interval = setInterval(function () {
                NProgress.inc();
            }, 1000);
            clearInterval(interval);

            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'replace_course',
                    security: security,
                    payload: 'replace_course',
                    current_course_id: current_course_id,
                    selected_course_id: selected_course_id,
                    ld_group_id: ld_group_id
                },
                type: 'post',
                dataType: 'json',
                success: function (result, textstatus) {
                    if (result) {
                        console.log(result);

                        $(document).trigger(
                            'bb_trigger_toast_message',
                            [
                                '',
                                '<div>' + result.success_msg +  '</div>',
                                'success',
                                null,
                                false,
                            ]
                        );

                        NProgress.done();

                        // Reload the page after 3 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function (result) {
                    console.log(result);
                    console.log('fail');
                },
            });
        } else {          
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>' + manage_courses_data.no_course_selected_alert  + '</div>', 
                    'warning',
                    null,
                    true,
                ]
            );
        }
    });

    //Courses order
    $("#courses-list").dragsort({
        dragSelector: ".bb-course-item-wrap",

        dragEnd: function() {
            // Get the new order of course items after sorting
            var totalRequests = $("#courses-list .bb-course-item-wrap").length;
            var completedRequests = 0;

            $("#courses-list .bb-course-item-wrap").map(function(index) {
                var courseId = $(this).data('course-id');
                var group_id = $(this).data('group-id');
                var security = $(this).data('security');
                var courseindex = index + 1; // Index starts from 0, so add 1 to start from 1
                
                // Update the position meta data for each course item
                $.ajax({
                    url: ajaxurl, // WordPress AJAX handler URL
                    type: 'POST',
                    data: {
                        action: 'update_course_position',
                        course_id: courseId,
                        group_id: group_id,
                        new_position: courseindex,
                        security: security,
                        payload: 'update_course_position',
                    },
                    success: function (response) {
                        // Handle success response if needed
                        completedRequests++;
                        
                        //console.log(response.data);
                        //console.log("Course position: " + courseindex + " updated for course ID: " + courseId);
                        if (completedRequests === totalRequests) {
                            // All requests completed, trigger the toast message
                            $(document).trigger(
                                'bb_trigger_toast_message',
                                [
                                    '',
                                    '<div>' + response.data +  '</div>',
                                    'success',
                                    null,
                                    true,
                                ]
                            );

                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response if needed
                        /* console.error("Error updating course position for course ID: " + courseId);
                        console.error(error); */
                        $(document).trigger(
                            'bb_trigger_toast_message',
                            [
                                '',
                                '<div>' + error +  '</div>',
                                'success',
                                null,
                                true,
                            ]
                        );
                    }
                });
                return courseId;
            }).get();
        }
    });

    $("#courses-list").on('click', '.change-course.button', function(e) {
        e.preventDefault();
        // Your code to handle the click event
    });
    $('.change-course.button').on('click', function (e) {
        e.stopPropagation();
    });
    
    
});


