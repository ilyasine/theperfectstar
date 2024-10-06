jQuery(document).ready(function ($) {

    setTimeout(() => {
        $('.buddypress-wrap .subnav-filters').css('visibility', 'visible').hide().fadeIn();
    }, 2000);

    // Function to handle tab switching
    function switchTab(activeButton, showDiv) {
        // Remove active-tab class from all buttons
        $('.manage-classrooms button').removeClass('active-tab');
        activeButton.addClass('active-tab');
        $('#groups-dir-list, #create-classroom, #duplicate-structure').hide();
        showDiv.fadeIn();
    }

    $('.manage-classrooms button.all-classrooms').addClass('active-tab');

    // Click event for all-classrooms button
    $('.all-classrooms').click(function () {
        switchTab($(this), $('#groups-dir-list'));
    });

    // Click event for create-classroom button
    $('.create-classroom').click(function () {
        switchTab($(this), $('#create-classroom'));
    });

    // Click event for duplicate-structure button
    $('.duplicate-structure').click(function () {
        switchTab($(this), $('#duplicate-structure'));
    });

    // Apply select ui
    //schools filter
    $("#school-order-by").select2();
    $('#school-order-by').one('select2:open', function (e) {
        $('input.select2-search__field').prop('placeholder', TPRM_bp_groups_data.all_schools);
    });

    $("#groups-order-by").select2();

    // curriculum filter
    $("#group-type-order-by").select2();
    $('#group-type-order-by').one('select2:open', function (e) {
        $('input.select2-search__field').prop('placeholder', TPRM_bp_groups_data.all_curriculums);
    });

    // year filter
    $("#year-order-by").select2();
    $('#year-order-by').one('select2:open', function (e) {
        $('input.select2-search__field').prop('placeholder', TPRM_bp_groups_data.this_year_i18);
    });

    //initialize ajax query always with groups of the current year

    if ($('#buddypress [data-bp-filter="group_subgroups"]').length) {
        bp.Nouveau.objectRequest({
            object: 'group_subgroups',
            scope: 'personal',
            filter: 'active',
            page: 1,
            extras: false,
            group_year: TPRM_bp_groups_data.this_year,
        }).done(function (response) {
            //console.log(response.data.scopes)
            var count = response.data.scopes.all;
            $('#subgroups-groups-li').find('span.count').text(count)
        });
    }

    if ($('#buddypress [data-bp-filter="groups"]').length) {
        bp.Nouveau.objectRequest(
            {
                object: 'groups',
                scope: 'personal',
                filter: 'active',
                page: 1,
                extras: false,
                group_year: TPRM_bp_groups_data.this_year,
            }
        );
    }

    // AJAX call when the school filter dropdown changes
    $('#buddypress').on('change', '[data-bp-school-filter]', function () {

        var selectedSchool = $(this).val();

        NProgress.start();
        NProgress.set(0.4);

        // Increment 
        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);

        /*  bp.Nouveau.objectRequest(
             {
                 object: 'groups',
                 scope: 'personal',
                 filter: 'active',
                 page: 1,
                 extras: false,
                 group_school: selectedSchool
             }
         ); */

        bp.Nouveau.objectRequest({
            object: 'groups',
            scope: 'personal',
            filter: 'active',
            page: 1,
            extras: false,
            group_year: selectedSchool
        }).done(function (response) {
            var count = response.data.scopes.all;
            $('#subgroups-groups-li').find('span.count').text(count);
            NProgress.done();
            clearInterval(interval);
        });
    });

    // AJAX call when the year filter dropdown changes
    $('#buddypress').on('change', '[data-bp-year-filter]', function () {
        var selectedYear = $(this).val();

        NProgress.start();
        NProgress.set(0.4);

        // Increment 
        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);

        bp.Nouveau.objectRequest({
            object: 'groups',
            scope: 'personal',
            filter: 'active',
            page: 1,
            extras: false,
            group_year: selectedYear
        }).done(function (response) {
            var count = response.data.scopes.all;
            $('#subgroups-groups-li').find('span.count').text(count);
            NProgress.done();
            clearInterval(interval);
        });
    });

    // AJAX call when the year filter dropdown changes
    /* $('#buddypress').on('change', '[data-bp-year-filter]', function () {
        var selectedYear = $(this).val();
        bp.Nouveau.objectRequest({
            object: 'groups',
            scope: 'personal',
            filter: 'active',
            page: 1,
            extras: false,
            group_year: selectedYear
        }).done(function (response) {
            var count = response.data.scopes.all;
            $('#subgroups-groups-li').find('span.count').text(count)
        });    
    }); */

    $(document).on('change', '#buddypress [data-bp-group-type-filter]', function () {
        var group_type = $(this).val();

        NProgress.start();
        NProgress.set(0.4);

        // Increment 
        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);

        bp.Nouveau.objectRequest({
            object: 'groups',
            scope: 'personal',
            filter: 'active',
            page: 1,
            extras: false,
            group_type: group_type
        }).done(function (response) {
            var count = response.data.scopes.all;
            $('#subgroups-groups-li').find('span.count').text(count);
            NProgress.done();
            clearInterval(interval);
        });
    });


    $(document).on('click', '[data-bp-pagination="grpage"].bp-pagination a.page-numbers, .ld-pages a', function (e) {

        NProgress.start();
        NProgress.set(0.4);

        // Increment 
        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);

        $(document).ajaxStop(function () {
            //console.log('ajax call is finished')
            NProgress.done();
            clearInterval(interval);
        });

    });

    $('.disconnect-classroom-button').hover(
        function () {
            // Hover in: Change the icon to bb-icon-file-export with ease-in-out transition
            $(this).find('i').removeClass('bb-icon-sign-in').addClass('bb-icon-sign-out').css('transition', 'all .6s ease-in-out');
        },
        function () {
            // Hover out: Change the icon back to bb-icon-file-import with ease-in-out transition
            $(this).find('i').removeClass('bb-icon-sign-out').addClass('bb-icon-sign-in').css('transition', 'all .6s ease-in-out');
        }
    ).on('click', function (e) {
        e.preventDefault();
        var button = $(this);
        var nonce = $(this).data('security');

        NProgress.start();
        NProgress.set(0.4);

        // Increment 
        var interval = setInterval(function () {
            NProgress.inc();
        }, 1000);

        // Step 1: Send Member IDs
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'send_students_ids',
                payload: 'disconnect_classroom',
                security: nonce,
                send_members: true, // Add a flag to indicate sending member IDs
            },
            success: function (response) {
                // Check if sending member IDs was successful
                if (response.success) {
                    var memberIds = response.data.students_ids;

                    // Step 2: Process Logout
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: ajaxurl,
                        headers: {
                            pragma: "no-cache",
                            "cache-control": "no-cache"
                        },
                        data: {
                            action: 'TPRM_ajax_logout_group',
                            payload: 'disconnect_classroom',
                            security: nonce,
                            user_ids: memberIds, // Include the member IDs for logout
                        },
                        success: function (logoutResponse) {
                            // Check if logout was successful
                            if (logoutResponse.success) {

                                button.text(TPRM_bp_groups_data.disconnected_classroom);
                                NProgress.done();
                                clearInterval(interval);

                                // Step 3: Send Success Message
                                jQuery(document).trigger(
                                    'bb_trigger_toast_message',
                                    [
                                        '',
                                        '<div>' + logoutResponse.data.disconnected_message + '</div>',
                                        'success',
                                        null,
                                        true
                                    ]
                                );

                            } else {
                                // Handle logout failure
                                console.error('Logout failed:', logoutResponse.data);
                            }
                        },
                        error: function (error) {
                            // Handle logout error
                            console.error('Logout error:', error);
                        }
                    });
                } else {
                    // Handle sending member IDs failure
                    console.error('Sending member IDs failed:', response.data);
                }
            },
            error: function (error) {
                // Handle sending member IDs error
                console.error('Sending member IDs error:', error);
            }
        });
    });

    //Copy Classroom Access Code
    $(document).on('click', '.classroom_code .bb-icon-copy', function () {
        var targetElement = $(this).siblings('.classroom_code_text');
        var textToCopy;

        if (targetElement.is('input')) {
            textToCopy = targetElement.val();
        } else {
            textToCopy = targetElement.text().trim();
        }

        // Create a textarea element to hold the text to be copied
        var textarea = $('<textarea>').val(textToCopy).css({
            position: 'fixed',
            opacity: 0
        }).appendTo('body');

        // Select the text and copy it to the clipboard
        textarea[0].select();
        document.execCommand('copy');

        // Remove the temporary textarea
        textarea.remove();

        // Feedback to the user
        jQuery(document).trigger(
            'bb_trigger_toast_message',
            [
                '',
                '<div>' + TPRM_bp_groups_data.classroomcopyfeedback + '</div>',
                'success',
                null,
                true
            ]
        );
    });
    //Copy school Access Code
    $(document).on('click', '.school_code .bb-icon-copy', function () {
        var targetElement = $(this).siblings('.school_code_text');
        var textToCopy;

        if (targetElement.is('input')) {
            textToCopy = targetElement.val();
        } else {
            textToCopy = targetElement.text().trim();
        }

        // Create a textarea element to hold the text to be copied
        var textarea = $('<textarea>').val(textToCopy).css({
            position: 'fixed',
            opacity: 0
        }).appendTo('body');

        // Select the text and copy it to the clipboard
        textarea[0].select();
        document.execCommand('copy');

        // Remove the temporary textarea
        textarea.remove();

        // Feedback to the user
        jQuery(document).trigger(
            'bb_trigger_toast_message',
            [
                '',
                '<div>' + TPRM_bp_groups_data.schoolcopyfeedback + '</div>',
                'success',
                null,
                true
            ]
        );
    });

    //Delete classroom
    $(document).on('click', '.classroom_actions .delete-classroom', function (e) {
        e.preventDefault();

        var $this = $(this);
        //var classroom_id = $this.parents('li.item-entry').data('bp-item-id');
        var classroom_name = $this.parents('li.item-entry').find('.classroom_name > a').text();

        //open confirmation popup
        $.magnificPopup.open({
            items: {
                src: $this.attr('href'),
                type: 'inline'
            },
            fixedContentPos: true,
            fixedBgPos: true,
            closeBtnInside: true,
            closeOnBgClick: false,
            closeOnContentClick: false,
            removalDelay: 300,
            mainClass: 'mfp-fade',
            callbacks: {
                open: function () {

                    $('.confirm_delete_classroom').on('click', function (e) {
                        e.preventDefault();

                        var button = $(this);
                        var security = button.data('security');
                        var classroom_id = button.data('group');

                        if (classroom_id) { // Check if selected_course_id has a value
                            NProgress.start();
                            NProgress.set(0.4);

                            var interval = setInterval(function () {
                                NProgress.inc();
                            }, 1000);
                            clearInterval(interval);

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'delete_classroom',
                                    security: security,
                                    payload: 'delete_classroom_payload',
                                    classroom_id: classroom_id,
                                    classroom_name: classroom_name,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result, textstatus) {
                                    if (result) {
                                        console.log(result)
                                        $(document).trigger(
                                            'bb_trigger_toast_message',
                                            [
                                                '',
                                                '<div>' + result.data.message + '</div>',
                                                'success',
                                                null,
                                                true,
                                            ]
                                        );

                                        NProgress.done();

                                        $.magnificPopup.close();

                                        //after confirm update the UI
                                        bp.Nouveau.objectRequest({
                                            object: 'groups',
                                            scope: 'personal',
                                            filter: 'active',
                                            page: 1,
                                            extras: false,
                                        }).done(function (response) {
                                            var count = response.data.scopes.all;
                                            $('#subgroups-groups-li').find('span.count').text(count)
                                        });

                                    }
                                },
                                error: function (result) {
                                    $(document).trigger(
                                        'bb_trigger_toast_message',
                                        [
                                            '',
                                            '<div>' + result.data.message + '</div>',
                                            'error',
                                            null,
                                            true,
                                        ]
                                    );
                                },
                            });
                        } else {
                            $(document).trigger(
                                'bb_trigger_toast_message',
                                [
                                    '',
                                    '<div>' + result.data.message + '</div>',
                                    'error',
                                    null,
                                    true,
                                ]
                            );
                        }
                    });

                    $('#cancel_delete_classroom').on('click', function (e) {
                        e.preventDefault();
                        $.magnificPopup.close();
                        NProgress.done();
                    })
                },
            }
        });

    });



});
