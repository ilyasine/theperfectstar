jQuery(document).ready(function ($) {

    //Assign course to group
    $(document).on('click', '.assign-course-to-group-btn', function (e) {
        e.preventDefault();

        var button = $(this);
        var security = button.data('security');
        var ld_group_id = button.data('ld-group-id');
        var course_id = button.data('course-id');
        var course_container = button.closest('.ld_course_grid')

        if (course_id) {
            NProgress.start();
            NProgress.set(0.4);
            var interval = setInterval(function () { NProgress.inc(); }, 1000);
            clearInterval(interval);          

            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'assign_course_to_group',
                    security: security,
                    payload: 'assign_course_to_group',
                    course_id: course_id,
                    ld_group_id: ld_group_id,
                },
                type: 'post',
                dataType: 'json',
                success: function (result, textstatus) {
                    console.log(result);
                    console.log(textstatus);
                    course_container.fadeOut();
                    if (result) {
                        $(document).trigger(
                            'bb_trigger_toast_message',
                            ['', '<div>' + result.success_msg + '</div>', 'success', null, true]
                        );                     
                    } else {
                        $(document).trigger(
                            'bb_trigger_toast_message',
                            ['', '<div>' + result.success_msg + '</div>', 'success', null, true]
                        );
                    }
                    NProgress.done();
                },
                error: function (result) {
                    console.log(result);
                    console.log('fail');
                    NProgress.done();
                }
            });
        } else {
            $(document).trigger(
                'bb_trigger_toast_message',
                ['', '<div>' + manage_courses_data.no_course_selected_alert + '</div>', 'warning', null, true]
            );
        }    

    })
    
    // open related courses popup
    window.BBLMS.switchLdGridList = function () { };

    $('.ld-course-list-content').addClass('list-view');

    // 
    $('.ld-item-list-items .ld-item-list-item.learndash-complete').last().next().find('.ld-item-list-item-preview').css('background-color', '#D6FCF4');

    var parent_course = null;

    /* $('#course-dir-list #courses-list #see-related-courses').magnificPopup({
        type: 'inline',
        fixedContentPos: true,
        fixedBgPos: true,
        closeBtnInside: true,
        closeOnBgClick: false,
        closeOnContentClick: false,
        removalDelay: 300,
        mainClass: 'mfp-move-horizontal related-courses',
        callbacks: {         
            open: function () {
                var mp = $.magnificPopup.instance,
                    t = $(mp.currItem);         
                parent_course = t.attr('src').replace('#see-related-courses-', '');
                
                
            }
        }
    }); */

    var $prevSelected = null;

    $('#course-dir-list #courses-list #see-related-courses').magnificPopup({
        type: 'inline',
        fixedContentPos: true,
        fixedBgPos: true,
        closeBtnInside: true,
        closeOnBgClick: false,
        closeOnContentClick: false,
        removalDelay: 300,
        mainClass: 'mfp-move-horizontal related-courses',
        callbacks: {         
            open: function () {
                var mp = $.magnificPopup.instance,
                    t = $(mp.currItem);         
                parent_course = t.attr('src').replace('#see-related-courses-', '');
                
                // Now, bind the click events inside the popup after it opens
                var $popupContent = $('#see-related-courses-' + parent_course);
    
                // Apply click event on '.ld-item-name'
                $popupContent.find('.ld-item-name').off('click').on('click', function (e) {
                    e.preventDefault();
                    var related_course = $(this).data('related_course');
                    var courseName = $(this).data('course_name');
                    var notices = $popupContent.find('.see-related-courses-head-notices');
                    var $statusIcon = $(this).find('.ld-status-icon');
                    var isSelected = $statusIcon.hasClass('ld-status-complete');
                    
                    // Toggle completion status
                    $statusIcon.toggleClass('ld-status-complete', !isSelected);
                    $statusIcon.toggleClass('ld-status-incomplete', isSelected);
                    
                    // Toggle the checkmark icon based on completion status
                    if (!isSelected) {
                        $statusIcon.append('<span class="ld-icon ld-icon-checkmark"></span>');
                        if ($prevSelected !== null && parent_course == parent_course) {
                            $prevSelected.find('.ld-status-icon').removeClass('ld-status-complete').addClass('ld-status-incomplete');
                            $prevSelected.find('.ld-icon-checkmark').remove();
                            $prevSelected.removeClass('ld-primary-color');
                            notices.text(manage_courses_data.no_course_selected);
                        }
                        $prevSelected = $(this);
                        $(this).addClass('ld-primary-color');
                        notices.html(manage_courses_data.course_selected + '<strong>' + courseName + '</strong>');
                    } else {
                        $statusIcon.find('.ld-icon-checkmark').remove();
                        $prevSelected = null;
                        $(this).removeClass('ld-primary-color');
                        notices.text(manage_courses_data.no_course_selected);
                    }
    
                    // Update the data-selected_course_id attribute
                    $('#confirm_selected_course').attr('data-selected_course_id', isSelected ? '' : related_course);
                });
    
                // Apply click event on '.confirm_selected_course'
                $popupContent.find('.confirm_selected_course').off('click').on('click', function (e) {
                    e.preventDefault();
    
                    var button = $(this);
                    var security = button.data('security');
                    var current_course_id = button.data('current_course_id');
                    var ld_group_id = button.data('group');
                    var course_position = button.data('position');
                    var selected_course_id = button.attr('data-selected_course_id');
    
                    if (selected_course_id) {
                        NProgress.start();
                        NProgress.set(0.4);
                        var interval = setInterval(function () { NProgress.inc(); }, 1000);
                        clearInterval(interval);
    
                        $.ajax({
                            url: ajaxurl,
                            data: {
                                action: 'replace_course',
                                security: security,
                                payload: 'replace_course',
                                current_course_id: current_course_id,
                                selected_course_id: selected_course_id,
                                ld_group_id: ld_group_id,
                                course_position: course_position
                            },
                            type: 'post',
                            dataType: 'json',
                            success: function (result, textstatus) {
                                if (result) {
                                    $(document).trigger(
                                        'bb_trigger_toast_message',
                                        ['', '<div>' + result.success_msg + '</div>', 'success', null, false]
                                    );
                                    NProgress.done();
                                    $.magnificPopup.close();
                                    location.reload();
                                }
                            },
                            error: function (result) {
                                console.log(result);
                                console.log('fail');
                            }
                        });
                    } else {
                        $(document).trigger(
                            'bb_trigger_toast_message',
                            ['', '<div>' + manage_courses_data.no_course_selected_alert + '</div>', 'warning', null, true]
                        );
                    }
                });
            }
        }
    });
    

    // Click event handler for opening the second popup
    $(document).on('click', '.preview-course', function (e) {
        e.preventDefault();
        var courseId = $(this).data('course-id');
        var popupId = '#preview-course-' + courseId;
        var security = $(this).data('security');

        $.magnificPopup.open({
            items: {
                src: popupId,
                type: 'inline'
            }
        });

        // Update the close button mfp-src to back to the previous popup
        $(popupId).find('.mfp-close').attr('data-mfp-src', '#see-related-courses-' + parent_course)
        
        // Show loader before the ajax request is proccessed
        $(popupId).find('.popup-scroll').html('<div class="kwf-loading"></div>');

        // Fetch course video using AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_course_preview',
                course_id: courseId,
                security: security // Add nonce for security
            },
            success: function(response) {
                // Replace loader with fetched content
                $(popupId).find('.popup-scroll').html(response);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(error);
            }
        });
    });  
    

    /* Course Order */

    var dragsortEnabled = false; // Initially, dragsort is disabled
    var toggleOrderBtn= $('.courses-order-container #toggle-order');
    var toggleOrderBtnText = toggleOrderBtn.children('.toggle-order-text');
    var lockIcon = $('.courses-order-container #toggle-order span#lock-icon');
    var toggle_order_notice = $('.courses-order-container .order-notice');
    
    toggleOrderBtnText.text(manage_courses_data.unlock_order);
    toggleOrderBtn.click(function () {
        // Toggle bar for course order
        $('#courses-list').toggleClass('order-enabled');
        $('.bb-course-item-wrap').toggleClass('order-enabled');
        if (dragsortEnabled) {
            // If dragsort is enabled, disable it       
            toggleOrderBtnText.text(manage_courses_data.unlock_order);          
            toggle_order_notice.html(manage_courses_data.order_locked_info);
            lockIcon.removeClass('bb-icon-lock-alt-open').addClass('bb-icon-lock-alt');
            $("#courses-list").dragsort("destroy");
            $("#courses-list .bb-course-item-wrap .bb-cover-list-item").css('pointer-events', 'all');
            dragsortEnabled = false;
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    manage_courses_data.order_locked_notice_title,
                    '<div>' + manage_courses_data.order_locked_notice  + '</div>', 
                    'info',
                    null,
                    true,
                ]
            );
        } else {
            // If dragsort is disabled, enable it
            toggleOrderBtnText.text(manage_courses_data.lock_order);
            toggle_order_notice.html(manage_courses_data.order_unlocked_info);
            lockIcon.removeClass('bb-icon-lock-alt').addClass('bb-icon-lock-alt-open');
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    manage_courses_data.order_unlocked_notice_title,
                    '<div>' + manage_courses_data.order_unlocked_notice  + '</div>', 
                    'info',
                    null,
                    false
                ]
            );
            setTimeout(() => {
                $(' .item-list:last-child').removeClass( 'pull-animation' ).addClass( 'close-item' ).delay( 500 ).remove();
            }, 15000);
            //Process Courses order
            $("#courses-list .bb-course-item-wrap .bb-cover-list-item").css('pointer-events', 'none');
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
                            url: ajaxurl,
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

                                if (completedRequests === totalRequests) {                         
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
            dragsortEnabled = true;
        }
    });
  
});


