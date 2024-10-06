jQuery(document).ready(function ($) {

    //Edit Teacher 

    $(".edit-teacher-details-content-body").tabs();

    //Delete teacher
    $(document).on('click', '.teacher_actions .delete-teacher', function (e) {
        e.preventDefault();

        var $this = $(this);

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

                    $('.confirm_delete_teacher').on('click', function (e) {
                        e.preventDefault();

                        var button = $(this);
                        var security = button.data('security');
                        var teacher_id = button.data('teacher_id');
                        var teacher_name = button.data('teacher_name');

                        if (teacher_id) {
                            NProgress.start();
                            NProgress.set(0.4);

                            var interval = setInterval(function () {
                                NProgress.inc();
                            }, 1000);
                            clearInterval(interval);

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'delete_teacher',
                                    security: security,
                                    payload: 'delete_teacher_payload',
                                    teacher_id: teacher_id,
                                    teacher_name: teacher_name,
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

                                        // Update the UI or refresh as needed
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 2000);

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
                            console.log('Teacher ID is missing');
                        }
                    });


                    $('#cancel_delete_teacher').on('click', function (e) {
                        e.preventDefault();
                        $.magnificPopup.close();
                        NProgress.done();
                    })
                },
            }
        });

    });


    //Delete teacher
    $(document).on('click', '.teacher_actions .edit-teacher-details-btn', function (e) {
        e.preventDefault();

        var $this = $(this);

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

                    $('.confirm_edit_teacher').on('click', function (e) {
                        e.preventDefault();

                        var button = $(this);
                        var security = button.data('security');
                        var teacher_id = button.data('teacher_id');
                        var teacher_name = button.data('teacher_name');

                        var first_name = $('#first-name').val();
                        var last_name = $('#last-name').val();
                        var email = $('#email').val();
                        var password = $('#password').val();

                        if (teacher_id) {
                            NProgress.start();
                            NProgress.set(0.4);

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'edit_teacher',
                                    security: security,
                                    teacher_id: teacher_id,
                                    teacher_name: teacher_name,
                                    first_name: first_name,
                                    last_name: last_name,
                                    email: email,
                                    password: password,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result, textstatus) {
                                    if (result.success) {
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
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 2000);
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
                                },
                                error: function (result) {
                                    $(document).trigger(
                                        'bb_trigger_toast_message',
                                        [
                                            '',
                                            '<div>' + result.responseText + '</div>',
                                            'error',
                                            null,
                                            true,
                                        ]
                                    );
                                },
                            });
                        }
                    });

                    $('#cancel_edit_teacher').on('click', function (e) {
                        e.preventDefault();
                        $.magnificPopup.close();
                        NProgress.done();
                    })
                },
            }
        });

    });


    $('.copy-button').click(function () {
        var targetElement = $(this).siblings('.copy-target');
        var copyfeedback = $(this).data('feedback');
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

        // feedback to the user
        $(document).trigger(
            'bb_trigger_toast_message',
            [
                '',
                '<div>' + copyfeedback + '</div>',
                'success',
                null,
                true
            ]
        );

    });


    $(document).on('click mouseenter mouseleave', '.toggle-classroom-teacher', function (event) {
        var $btn = $(this);
        var $tr = $btn.closest('tr.classroom');
        var $prevTr = $tr.prev('tr.classroom');
        var isActive = $btn.hasClass('active'); // Current state before any action

        if (event.type === 'click') {
            // Toggle active state after the click
            var willBeActive = !isActive;
            $btn.toggleClass('active', willBeActive);
            $tr.toggleClass('selected', willBeActive);
            $prevTr.toggleClass('previous-selected', willBeActive);

            if (willBeActive) {
                $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
            } else {
                $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
            }

            // Remove 'previous-selected' from any row that is no longer followed by a selected row
            $tr.next('tr.classroom').each(function () {
                if (!$(this).hasClass('selected')) {
                    $(this).prev('tr.classroom').removeClass('previous-selected');
                }
            });

        } else if (event.type === 'mouseenter' || event.type === 'mouseleave') {
            // Update tooltip text based on the current state
            if (isActive) {
                $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
            } else {
                $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
            }
        }
    });



    // Handle initial loading state for previous siblings
    $('.manage-teacher-classrooms').find('tr.classroom.selected').each(function () {
        $(this).prev('tr.classroom').addClass('previous-selected');
    });

    // Manage teacher classrooms
    $(document).on('click', '.teacher_actions .manage-teacher-classrooms-btn', function (e) {
        e.preventDefault();

        var $this = $(this);
        var teacher_id = $this.data('teacher_id');

        // Open confirmation popup
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
                    var initialSelectedClassrooms = [];
                    var $table = $('#manage-teacher-classrooms-' + teacher_id);

                    // Store the initial state of selected classrooms
                    $table.find('tr.classroom.selected').each(function () {
                        initialSelectedClassrooms.push($(this).attr('id'));
                    });

                    // Handle "Select All" button clicks
                    /* $table.find('th.classroom_action .toggle-classroom-teacher-all').on('click', function () {
                       //var $this = $(this);
                        var allSelected = $table.find('.toggle-classroom-teacher').length === $table.find('.toggle-classroom-teacher.active').length;
                        console.log('clicked');
                        var $allbtn = $(this);
                        var $checkbox = $allbtn.find('input.cb-value');
                        var $alltr = $allbtn.closest('tr.classroom');
                        var isallActive = $allbtn.hasClass('active');

                        if (isallActive) {
                            $allbtn.removeClass('active');
                            $alltr.removeClass('selected');
                            $allbtn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                            $allbtn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
                        } else {
                            $allbtn.addClass('active');
                            $alltr.addClass('selected');
                            $allbtn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                            $allbtn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
                        }
                        $table.find('.toggle-classroom-teacher').each(function () {
                            var $btn = $(this);
                            var $tr = $btn.closest('tr.classroom');
                            var isActive = $btn.hasClass('active');
                            
                            if (isActive ) {
                                $btn.removeClass('active');
                                $tr.removeClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
                            } else {
                                $btn.addClass('active');
                                $tr.addClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
                            }
                        });

                        $table.on('click', '.toggle-classroom-teacher', function () {
                            console.log('clicked');
                            var $btn = $(this);
                            var $checkbox = $btn.find('input.cb-value');
                            var $tr = $btn.closest('tr.classroom');
                            var isActive = $btn.hasClass('active');
    
                            if (isActive) {
                                $btn.removeClass('active');
                                $tr.removeClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
                            } else {
                                $btn.addClass('active');
                                $tr.addClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
                            }
                        });
                        

                    
                    });

                    $table.on('click', '.toggle-classroom-teacher', function () {
                        var $btn = $(this);
                        var $checkbox = $btn.find('input.cb-value');
                        var $tr = $btn.closest('tr.classroom');
                        var isActive = $btn.hasClass('active');

                        if (isActive) {
                            $btn.removeClass('active');
                            $tr.removeClass('selected');
                            $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                            $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
                        } else {
                            $btn.addClass('active');
                            $tr.addClass('selected');
                            $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                            $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
                        }
                    }); */

                    $table.find('th.classroom_action .toggle-classroom-teacher-all').off('click').on('click', function () {
                        var $allbtn = $(this);
                        var isAllActive = $allbtn.hasClass('active');

                        // Toggle state for "Select All" button
                        if (isAllActive) {
                            $allbtn.removeClass('active');
                            $allbtn.closest('tr.classroom').removeClass('selected');
                            $allbtn.attr('data-bp-tooltip', MTE_data.assign_all_classrooms);
                            $allbtn.find('.bp-screen-reader-text').text(MTE_data.assign_all_classrooms);
                        } else {
                            $allbtn.addClass('active');
                            $allbtn.closest('tr.classroom').addClass('selected');
                            $allbtn.attr('data-bp-tooltip', MTE_data.remove_all_classrooms);
                            $allbtn.find('.bp-screen-reader-text').text(MTE_data.remove_all_classrooms);
                        }

                        // Toggle state for individual buttons based on "Select All" state
                        var newState = !$allbtn.hasClass('active');
                        $table.find('.toggle-classroom-teacher').each(function () {
                            var $btn = $(this);
                            var $tr = $btn.closest('tr.classroom');

                            if (newState) {
                                $btn.removeClass('active');
                                $tr.removeClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
                            } else {
                                $btn.addClass('active');
                                $tr.addClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
                            }
                        });
                    });

                    // Unified click handler for individual classroom buttons
                    $table.on('click', '.toggle-classroom-teacher', function () {
                        var $btn = $(this);
                        var $tr = $btn.closest('tr.classroom');
                        var isActive = $btn.hasClass('active');

                        // Toggle state for individual button
                        if (isActive) {
                            $btn.removeClass('active');
                            $tr.removeClass('selected');
                            $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                            $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
                        } else {
                            $btn.addClass('active');
                            $tr.addClass('selected');
                            $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                            $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
                        }

                        // Update the "Select All" button based on the current state of individual buttons
                        var allSelectedNow = $table.find('.toggle-classroom-teacher').length === $table.find('.toggle-classroom-teacher.active').length;
                        var $selectAllBtn = $table.find('th.classroom_action .toggle-classroom-teacher-all');

                        if (allSelectedNow) {
                            $selectAllBtn.addClass('active');
                            $selectAllBtn.closest('tr.classroom').addClass('selected');
                            $selectAllBtn.attr('data-bp-tooltip', MTE_data.remove_all_classrooms);
                            $selectAllBtn.find('.bp-screen-reader-text').text(MTE_data.remove_all_classrooms);
                        } else {
                            $selectAllBtn.removeClass('active');
                            $selectAllBtn.closest('tr.classroom').removeClass('selected');
                            $selectAllBtn.attr('data-bp-tooltip', MTE_data.assign_all_classrooms);
                            $selectAllBtn.find('.bp-screen-reader-text').text(MTE_data.assign_all_classrooms);
                        }
                    });


                    // Confirm classroom selection
                    $('.confirm_teacher_classrooms').off('click').on('click', function (e) {
                        e.preventDefault();

                        var finalSelectedClassrooms = [];
                        var AddedClassrooms = [];
                        var RemovedClassrooms = [];

                        // Get the final state of selected classrooms
                        $table.find('tr.classroom.selected').each(function () {
                            finalSelectedClassrooms.push($(this).attr('id'));
                        });

                        // Include initially selected classrooms in AddedClassrooms
                        AddedClassrooms = [...initialSelectedClassrooms];

                        // Determine which classrooms were added (new selections)
                        finalSelectedClassrooms.forEach(function (classroomId) {
                            if (!initialSelectedClassrooms.includes(classroomId)) {
                                AddedClassrooms.push(classroomId);
                            }
                        });

                        // Determine which classrooms were removed
                        initialSelectedClassrooms.forEach(function (classroomId) {
                            if (!finalSelectedClassrooms.includes(classroomId)) {
                                RemovedClassrooms.push(classroomId);
                            }
                        });

                        // Remove overlapping elements from AddedClassrooms
                        AddedClassrooms = AddedClassrooms.filter(function (classroomId) {
                            return !RemovedClassrooms.includes(classroomId);
                        });

                        // Additional AJAX or operations can be handled here
                        var button = $(this);
                        var security = button.data('security');
                        var teacher_id = button.data('teacher_id');
                        var teacher_name = button.data('teacher_name');

                        /* console.log("Added Classrooms:", AddedClassrooms);
                        console.log("Removed Classrooms:", RemovedClassrooms);
                        console.log("teacher_id:", teacher_id);
                        console.log("teacher_name:", teacher_name); */

                        if (teacher_id) {
                            NProgress.start();
                            NProgress.set(0.4);

                            var interval = setInterval(function () {
                                NProgress.inc();
                            }, 1000);
                            clearInterval(interval);

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'manage_teacher_classrooms',
                                    security: security,
                                    payload: 'manage_teacher_classrooms_payload',
                                    teacher_id: teacher_id,
                                    teacher_name: teacher_name,
                                    AddedClassrooms: AddedClassrooms,
                                    RemovedClassrooms: RemovedClassrooms,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result, textstatus) {
                                    if (result) {
                                        //console.log(result);
                                        $(document).trigger(
                                            'bb_trigger_toast_message',
                                            [
                                                '',
                                                '<div>' + result.teacher_classrooms_updated_success_message + '</div>',
                                                'success',
                                                null,
                                                true,
                                            ]
                                        );

                                        NProgress.done();
                                        $.magnificPopup.close();

                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1000);
                                    }
                                },
                                error: function (result) {
                                    console.log(result);
                                    $(document).trigger(
                                        'bb_trigger_toast_message',
                                        [
                                            '',
                                            '<div>' + result + '</div>',
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
                                    '<div>' + result + '</div>',
                                    'error',
                                    null,
                                    true,
                                ]
                            );
                        }
                    });

                    $('#cancel_teacher_classrooms').off('click').on('click', function (e) {
                        e.preventDefault();
                        var $allToggle = $table.find('th.classroom_action .toggle-classroom-teacher.all');
                        $allToggle.removeClass('active');
                        $allToggle.attr('data-bp-tooltip', MTE_data.assign_all_classrooms);
                        $allToggle.find('.bp-screen-reader-text').text(MTE_data.assign_all_classrooms);
                        $table.find('.toggle-classroom-teacher').each(function () {
                            var $btn = $(this);
                            var $tr = $btn.closest('tr.classroom');
                            if (!initialSelectedClassrooms.includes($tr.attr('id'))) {
                                $btn.removeClass('active');
                                $tr.removeClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.assign_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
                            } else {
                                $btn.addClass('active');
                                $tr.addClass('selected');
                                $btn.attr('data-bp-tooltip', MTE_data.remove_classroom);
                                $btn.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
                            }
                        });
                        $.magnificPopup.close();
                        NProgress.done();
                    });
                },
            }
        });
    });

});
