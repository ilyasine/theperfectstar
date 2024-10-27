/* manage Classroom */

jQuery(document).ready(function ($) {

    /* 
    *  Start Manage student classrooms
    */
    $(document).on('click', '.classroom_actions .manage-classroom-btn', function (e) {
        e.preventDefault();

        var $this = $(this);
        var classroom_id = $this.data('classroom_id');

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

                    $('.classroom-manage-content-body').tabs();
                    var $table = $('#manage-classroom-' + classroom_id);

                    manageStudentsTab(classroom_id);

                    $table.find('#cancel_manage_classroom').on('click', function (e) {

                        $.magnificPopup.close();
                    })


                },
            }
        });

    });

    $(document).on('click mouseenter mouseleave', '.toggle-classroom-student', function (event) {
        var $btn = $(this);
        var $li = $btn.closest('li.student');
        var $prevTr = $li.prev('li.student');
        var isActive = $btn.hasClass('active'); // Determine if it should be active after the click

        if (event.type === 'click') {
            // Toggle active state
            $btn.toggleClass('active', isActive);
            $li.toggleClass('selected', isActive);
            $prevTr.toggleClass('previous-selected', isActive);

            if (isActive) {
                $btn.attr('data-bp-tooltip', MCL_data.remove_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.remove_classroom);
            } else {
                $btn.attr('data-bp-tooltip', MCL_data.assign_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.assign_classroom);
            }

            // Remove 'previous-selected' from any row that is no longer followed by a selected row
            $li.next('li.student').each(function () {
                if (!$(this).hasClass('selected')) {
                    $(this).prev('li.student').removeClass('previous-selected');
                }
            });
        } else if (event.type === 'mouseenter' || event.type === 'mouseleave') {
            if (isActive) {
                $btn.attr('data-bp-tooltip', MCL_data.remove_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.remove_classroom);
            } else {
                $btn.attr('data-bp-tooltip', MCL_data.assign_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.assign_classroom);
            }
        }
    });

    // Handle initial loading state for previous siblings
    $('.manage-student-classrooms').find('li.student.selected').each(function () {
        $(this).prev('li.student').addClass('previous-selected');
    });


    $(document).on('click mouseenter mouseleave', '.toggle-classroom-student', function (event) {
        var $btn = $(this);
        var $tr = $btn.closest('tr.classroom');
        var $prevTr = $tr.prev('tr.classroom');
        var isActive = $btn.hasClass('active'); // Determine if it should be active after the click

        if (event.type === 'click') {
            // Toggle active state
            $btn.toggleClass('active', isActive);
            $tr.toggleClass('selected', isActive);
            $prevTr.toggleClass('previous-selected', isActive);

            if (isActive) {
                $btn.attr('data-bp-tooltip', MCL_data.remove_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.remove_classroom);
            } else {
                $btn.attr('data-bp-tooltip', MCL_data.assign_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.assign_classroom);
            }

            // Remove 'previous-selected' from any row that is no longer followed by a selected row
            $tr.next('tr.classroom').each(function () {
                if (!$(this).hasClass('selected')) {
                    $(this).prev('tr.classroom').removeClass('previous-selected');
                }
            });
        } else if (event.type === 'mouseenter' || event.type === 'mouseleave') {
            if (isActive) {
                $btn.attr('data-bp-tooltip', MCL_data.remove_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.remove_classroom);
            } else {
                $btn.attr('data-bp-tooltip', MCL_data.assign_classroom);
                $btn.find('.bp-screen-reader-text').text(MCL_data.assign_classroom);
            }
        }
    });

    /* 
    *  End Manage student classrooms
    */


    /*
    *  Start Promote Student
    */

    $(document).on('click', '.classroom_actions .promote-students-btn', function (e) {
        e.preventDefault();

        var $this = $(this);
        var classroom_id = $this.data('classroom_id');

        //open confirmation popup
        $.magnificPopup.open({
            items: {
                src: $this.attr('href'),
                type: 'inline',
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

                    //promote-students-dropdown                
                    /* $('#promote-students-dropdown-' + classroom_id).select2();
                     $('#promote-students-dropdown').one('select2:open', function(e) {
                        $('input.select2-search__field').prop('placeholder', MCL_data.choose_classroom);
                    }); */

                    var $list = $('#promote-students-' + classroom_id);

                    var $studentsList = $list.find('.students-list');
                    var $addedStudentsList = $list.find('.added-students-list');

                    // Save original studentsList when open the popup
                    var originalClassroom = $('#promote-students-dropdown-' + classroom_id).val();
                    var originalStudentsListHtml = $studentsList.html();
                    var originalAddedStudentsListHtml = $addedStudentsList.html();

                    // Click event for toggle button
                    $studentsList.add($addedStudentsList).off('click').on('click', '.toggle-classroom-student', function () {
                        var $this = $(this);
                        var $studentItem = $this.closest('li.student');

                        // Store original position if not already stored
                        if (!$this.data('original-position')) {
                            var originalIndex = $studentItem.index();
                            $this.data('original-position', originalIndex);
                        }

                        $this.toggleClass('selected');
                        $studentItem.toggleClass('selected');

                        if ($this.hasClass('selected')) {
                            // Move student from students-list to added-students-list
                            $this.attr('data-bp-tooltip', MCL_data.remove_student);
                            $this.find('.bp-screen-reader-text').text(MCL_data.remove_student);
                            $studentItem.appendTo($addedStudentsList);
                            // Add 'promoted' class to the student item
                            $studentItem.addClass('promoted');
                        } else {
                            // Move student from added-students-list to students-list
                            $this.attr('data-bp-tooltip', MCL_data.add_student);
                            $this.find('.bp-screen-reader-text').text(MCL_data.add_student);

                            // Restore original position
                            var originalIndex = $this.data('original-position');
                            var $allStudents = $studentsList.children('li.student');

                            // Move the item to the original position or to the end if index is out of bounds
                            if (originalIndex !== undefined && originalIndex < $allStudents.length) {
                                $studentItem.insertBefore($allStudents.eq(originalIndex));
                            } else {
                                $studentsList.append($studentItem); // If index is out of bounds, append to end
                            }

                            $this.removeData('original-position');
                            // Add 'demoted' class to the student item
                            $studentItem.addClass('demoted');
                        }

                        // Show or hide the confirm button based on selection
                        var anySelected = $studentsList.find('.toggle-classroom-student.selected').length > 0;
                        $('#confirm-student-selection').toggle(anySelected);
                    });

                    $('#promote-students-dropdown-' + classroom_id).on('change', function () {
                        var classroomId = $(this).val();
                        var $preloader = $studentsList.find('.tprm-preloader');
                        $preloader.show();
                        $studentsList.addClass('loading');

                        if (classroomId) {
                            // Collect student IDs currently in the added students list
                            var addedStudentIds = $addedStudentsList.find('li.student').map(function () {
                                return $(this).attr('id');
                            }).get();

                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'load_previous_students',
                                    classroom_id: classroomId
                                },
                                success: function (response) {
                                    // Clear existing students and the preloader
                                    $studentsList.find('li:not(.tprm-preloader)').remove();

                                    // Parse response to get student data
                                    var newStudents = $(response).filter('li.student');

                                    // Filter out students that are already in the added students list
                                    var filteredStudents = newStudents.filter(function () {
                                        var studentId = $(this).attr('id');
                                        return addedStudentIds.indexOf(studentId) === -1;
                                    });

                                    // Append the filtered students
                                    $studentsList.append(filteredStudents);

                                    // Hide the preloader and remove loading class
                                    $preloader.hide();
                                    $studentsList.removeClass('loading');
                                },
                                error: function () {
                                    // Handle errors
                                    $studentsList.removeClass('loading');
                                    $studentsList.html('<li class="nostudent">' + MCL_data.error_occurred + '</li>');
                                }
                            });
                        } else {
                            $studentsList.html('<li class="nostudent">' + MCL_data.no_classroom_selected_to_promote + '</li>');
                            $studentsList.removeClass('loading');
                        }
                    });


                    $list.find('.confirm_promote_students').on('click', function (e) {
                        e.preventDefault();

                        var button = $(this);
                        var security = button.data('security');
                        var studentIds = [];
                        $addedStudentsList.find('.student').each(function () {
                            var studentId = $(this).attr('id');
                            if (studentId) {
                                studentIds.push(studentId);
                            }
                        });

                        var studentIdsToPromote = [];
                        var studentIdsToDemote = [];

                        // Collect promoted students (in addedStudentsList)
                        $addedStudentsList.find('.student.promoted').each(function () {
                            var studentId = $(this).attr('id');
                            if (studentId) {
                                studentIdsToPromote.push(studentId);
                            }
                        });

                        // Collect demoted students (in studentsList)
                        $studentsList.find('.student.demoted').each(function () {
                            var studentId = $(this).attr('id');
                            if (studentId) {
                                studentIdsToDemote.push(studentId);
                            }
                        });

                        var title_content = $list.find('.promote-students-content-title_text');
                        var title_content_original_text = $list.find('.promote-students-content-title_text').text();
                        var footer_content = $list.find('.promote-students-content-footer');
                        var preloader = $list.find('.tprm-preloader-promote');
                        var body_content = $list.find('.promote-students-content-body');
                        var students_container = $(body_content).find('.students-container');
                        var added_students_container = $(body_content).find('.added-students-container');

                        /* console.log(classroom_id);
                        console.log(studentIds);
                        console.log(security);

                        console.log('studentIdsToPromote :' + studentIdsToPromote);
                        console.log('studentIdsToDemote :' + studentIdsToDemote); */


                        $(footer_content).hide();
                        $(students_container).hide();
                        $(added_students_container).hide();
                        $(preloader).show();
                        $(title_content).text(MCL_data.promoting_students_in_progress);

                        if (studentIds.length > 0 && classroom_id) {

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'promote_students',
                                    security: security,
                                    payload: 'promote_students_payload',
                                    student_ids: studentIds, // Pass array of student IDs
                                    classroom_id: classroom_id, // Pass classroom ID
                                    student_ids_to_promote: studentIdsToPromote,
                                    student_ids_to_demote: studentIdsToDemote,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result) {

                                    $(footer_content).show();
                                    $(students_container).show();
                                    $(added_students_container).show();
                                    $(preloader).hide();
                                    $(title_content).text(MCL_data.promoting_students_success);

                                    $('#close_promote_students').show();

                                    $('#close_promote_students').on('click', function (e) {
                                        $.magnificPopup.close();
                                        $(title_content).text(title_content_original_text);
                                        setTimeout(() => {
                                            bp.Nouveau.objectRequest({
                                                object: 'groups',
                                                scope: 'personal',
                                                filter: 'active',
                                                page: 1,
                                                extras: false,
                                                //group_year: selectedYear
                                            }).done(function (response) {
                                                var count = response.data.scopes.all;
                                                $('#subgroups-groups-li').find('span.count').text(count)
                                            });
                                            $(document).trigger(
                                                'bb_trigger_toast_message',
                                                [
                                                    '',
                                                    '<div>' + MCL_data.promoting_students_success + '</div>',
                                                    'success',
                                                    null,
                                                    true,
                                                ]
                                            );
                                        }, 2000);
                                    })

                                },
                                error: function (result) {
                                    console.log(result);
                                    $studentsList.html(originalStudentsListHtml);  // Restore the original students list                                      
                                    $addedStudentsList.html(originalAddedStudentsListHtml); // Clear the added students list
                                    $('#promote-students-dropdown-' + classroom_id).val(originalClassroom);
                                    $(footer_content).show();
                                    $(students_container).show();
                                    $(added_students_container).show();
                                    $(preloader).hide();
                                    $(title_content).text(title_content_original_text);
                                    //$.magnificPopup.close();
                                    $(document).trigger(
                                        'bb_trigger_toast_message',
                                        [
                                            '',
                                            '<div>' + MCL_data.error_promoting_students + '</div>',
                                            'error',
                                            null,
                                            true,
                                        ]
                                    );
                                },
                            });
                        } else {
                            $studentsList.html(originalStudentsListHtml);  // Restore the original students list                                      
                            $addedStudentsList.html(originalAddedStudentsListHtml); // Clear the added students list
                            $('#promote-students-dropdown-' + classroom_id).val(originalClassroom);
                            $(footer_content).show();
                            $(students_container).show();
                            $(added_students_container).show();
                            $(preloader).hide();
                            $(title_content).text(title_content_original_text);
                            //$.magnificPopup.close();
                            $(document).trigger(
                                'bb_trigger_toast_message',
                                [
                                    '',
                                    '<div>' + MCL_data.no_students_selected + '</div>',
                                    'error',
                                    null,
                                    true,
                                ]
                            );
                        }
                    });

                    $list.find('#cancel_promote_students').on('click', function (e) {
                        e.preventDefault();
                        $studentsList.html(originalStudentsListHtml);  // Restore the original students list                                      
                        $addedStudentsList.html(originalAddedStudentsListHtml); // Clear the added students list
                        $('#promote-students-dropdown-' + classroom_id).val(originalClassroom);
                        $.magnificPopup.close();
                    })
                },
            }
        });

    });

    /* 
    *  End Promote Student
    */



    /*
    *  Start Assign Student
    */

    function manageStudentsTab(classroom_id) {




        // Save the original state of the lists when the popup is opened
        var $teacherList = $('#manage-classroom-' + classroom_id);
        var $teachersList = $teacherList.find('.teachers-list');
        var $addedTeachersList = $teacherList.find('.added-teachers-list');

        // Store the original HTML of both lists for reverting later
        var originalTeachersListHtml = $teachersList.html();
        var originalAddedTeachersListHtml = $addedTeachersList.html();

        // Click event for toggle button
        function bindToggleEvent() {
            $teachersList.add($addedTeachersList).off('click').on('click', '.toggle-classroom-teacher', function () {
                var $this = $(this);
                var $teacherItem = $this.closest('li.teacher');

                // Store the original position of the item if it's not already stored
                if (!$this.data('original-position')) {
                    var originalIndex = $teacherItem.index();
                    $this.data('original-position', originalIndex);
                }

                $this.toggleClass('selected');
                $teacherItem.toggleClass('selected');

                if ($this.hasClass('selected')) {
                    // Move teacher from teachers-list to added-teachers-list
                    $this.attr('data-bp-tooltip', MCL_data.remove_teacher);
                    $this.find('.bp-screen-reader-text').text(MCL_data.remove_teacher);
                    $teacherItem.appendTo($addedTeachersList);
                    $teacherItem.addClass('assigned');
                } else {
                    // Move teacher from added-teachers-list back to teachers-list
                    $this.attr('data-bp-tooltip', MCL_data.add_teacher);
                    $this.find('.bp-screen-reader-text').text(MCL_data.add_teacher);

                    // Restore original position in the list
                    var originalIndex = $this.data('original-position');
                    var $allTeachers = $teachersList.children('li.teacher');

                    if (originalIndex !== undefined && originalIndex < $allTeachers.length) {
                        $teacherItem.insertBefore($allTeachers.eq(originalIndex));
                    } else {
                        $teachersList.append($teacherItem);
                    }

                    $this.removeData('original-position');
                    $teacherItem.addClass('removed');
                }

                // Show or hide the confirm button based on selection
                var anySelected = $teachersList.find('.toggle-classroom-teacher.selected').length > 0;
                $('#confirm-teacher-selection').toggle(anySelected);
            });
        }

        // Initial binding of the toggle event
        bindToggleEvent();


        $teacherList.find('#revert_assign_teachers').on('click', function (e) {
            e.preventDefault();

            // Restore the original teachers list
            $teachersList.html(originalTeachersListHtml);
            // Restore the original added teachers list
            $addedTeachersList.html(originalAddedTeachersListHtml);

            // Hide confirm button
            $('#confirm-teacher-selection').hide();

            // Re-bind the click event for toggling teacher selection after restoring the lists
            bindToggleEvent();
        });
        var $list = $('#manage-classroom-' + classroom_id);

        var $studentsList = $list.find('.students-list');
        var $addedStudentsList = $list.find('.added-students-list');

        // Save original studentsList when open the popup
        var originalClassroom = $('#assign-students-dropdown-' + classroom_id).val();
        var originalStudentsListHtml = $studentsList.html();
        var originalAddedStudentsListHtml = $addedStudentsList.html();

        // Click event for toggle button
        $studentsList.add($addedStudentsList).off('click').on('click', '.toggle-classroom-student', function () {
            var $this = $(this);
            var $studentItem = $this.closest('li.student');

            // Store original position if not already stored
            if (!$this.data('original-position')) {
                var originalIndex = $studentItem.index();
                $this.data('original-position', originalIndex);
            }

            $this.toggleClass('selected');
            $studentItem.toggleClass('selected');

            if ($this.hasClass('selected')) {
                // Move student from students-list to added-students-list
                $this.attr('data-bp-tooltip', MCL_data.remove_student);
                $this.find('.bp-screen-reader-text').text(MCL_data.remove_student);
                $studentItem.appendTo($addedStudentsList);
                // Add 'assigned' class to the student item
                $studentItem.addClass('assigned');
            } else {
                // Move student from added-students-list to students-list
                $this.attr('data-bp-tooltip', MCL_data.add_student);
                $this.find('.bp-screen-reader-text').text(MCL_data.add_student);

                // Restore original position
                var originalIndex = $this.data('original-position');
                var $allStudents = $studentsList.children('li.student');

                // Move the item to the original position or to the end if index is out of bounds
                if (originalIndex !== undefined && originalIndex < $allStudents.length) {
                    $studentItem.insertBefore($allStudents.eq(originalIndex));
                } else {
                    $studentsList.append($studentItem); // If index is out of bounds, append to end
                }

                $this.removeData('original-position');
                // Add 'removed' class to the student item
                $studentItem.addClass('removed');
            }

            // Show or hide the confirm button based on selection
            var anySelected = $studentsList.find('.toggle-classroom-student.selected').length > 0;
            $('#confirm-student-selection').toggle(anySelected);
        });

        $('#assign-students-dropdown-' + classroom_id).on('change', function () {
            var classroomId = $(this).val();
            var current_classroom_name = $(this).data('current-classroom-name');
            var school_id = $(this).data('school_id');
            var this_classroom_year = $(this).data('this_classroom_year');
            var $preloader = $studentsList.find('.tprm-preloader');
            $preloader.show();
            $studentsList.addClass('loading');

            if (classroomId) {
                // Collect student IDs currently in the added students list
                var addedStudentIds = $addedStudentsList.find('li.student').map(function () {
                    return $(this).attr('id');
                }).get();

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'load_this_year_students',
                        classroom_id: classroomId,
                        school_id: school_id,
                        this_classroom_year: this_classroom_year,
                        current_classroom_name: current_classroom_name
                    },
                    success: function (response) {
                        console.log(school_id);
                        console.log(this_classroom_year);
                        console.log(classroom_id);
                        // Clear existing students and the preloader
                        $studentsList.find('li:not(.tprm-preloader)').remove();

                        // Parse response to get student data
                        var newStudents = $(response).filter('li.student');

                        // Filter out students that are already in the added students list
                        var filteredStudents = newStudents.filter(function () {
                            var studentId = $(this).attr('id');
                            return addedStudentIds.indexOf(studentId) === -1;
                        });

                        // Append the filtered students
                        $studentsList.append(filteredStudents);

                        // Hide the preloader and remove loading class
                        $preloader.hide();
                        $studentsList.removeClass('loading');
                    },
                    error: function () {
                        // Handle errors
                        $studentsList.removeClass('loading');
                        $studentsList.html('<li class="nostudent">' + MCL_data.error_occurred + '</li>');
                    }
                });
            } else {
                $studentsList.html('<li class="nostudent">' + MCL_data.no_classroom_selected_to_promote + '</li>');
                $studentsList.removeClass('loading');
            }
        });
        $teacherList.find('.confirm_assign_teachers').on('click', function (e) {
            e.preventDefault();

            var button = $(this);
            var security = button.data('security');

            var classroom_id = button.data('group');
            var teacherIdsToAssign = [];

            // Collect assigned teachers (in addedTeachersList)
            $addedTeachersList.find('.teacher.assigned').each(function () {
                var teacherId = $(this).attr('id');
                if (teacherId) {
                    teacherIdsToAssign.push(teacherId);
                }
            });

            var title_content = $list.find('.classroom-manage-content-title_text');
            var title_content_original_text = $list.find('.classroom-manage-content-title_text').text();
            var footer_content = $list.find('.assign-teachers-footer');
            var preloader = $list.find('.tprm-preloader-assign');
            var body_content = $list.find('.outer-teachers-container');
            var teachers_container = $(body_content).find('.teachers-container');
            var added_teachers_container = $(body_content).find('.added-teachers-container');

            console.log('teacherIdsToAssign :' + teacherIdsToAssign);

            $(footer_content).hide();
            $(teachers_container).hide();
            $(added_teachers_container).hide();
            $(preloader).show();
            $(title_content).text(MCL_data.assigning_teachers_in_progress);

            if (classroom_id) {
                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'assign_teachers',
                        security: security,
                        payload: 'assign_teachers_payload',
                        classroom_id: classroom_id,
                        teacher_ids_to_assign: teacherIdsToAssign,
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (result) {
                        console.log(result);
                        $(footer_content).show();
                        $(teachers_container).show();
                        $(added_teachers_container).show();
                        $(preloader).hide();
                        $(title_content).text(MCL_data.assigning_teachers_success);

                        $('#close_assign_teachers').show();
                        $('#revert_assign_teachers').hide();

                        $('#close_assign_teachers').on('click', function (e) {
                            $.magnificPopup.close();
                            $(title_content).text(title_content_original_text);
                            setTimeout(() => {
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
                                $(document).trigger(
                                    'bb_trigger_toast_message',
                                    [
                                        '',
                                        '<div>' + MCL_data.assigning_teachers_success + '</div>',
                                        'success',
                                        null,
                                        true,
                                    ]
                                );
                            }, 2000);
                        })
                    },
                    error: function (result) {
                        console.log('Error: ', result);
                    }
                });
            }
        });


        $list.find('.confirm_assign_students').on('click', function (e) {
            e.preventDefault();

            var button = $(this);
            var security = button.data('security');
            var studentIdsToAssign = [];
            var studentIdsToRemove = [];

            // Collect assigned students (in addedStudentsList)
            $addedStudentsList.find('.student.assigned').each(function () {
                var studentId = $(this).attr('id');
                if (studentId) {
                    studentIdsToAssign.push(studentId);
                }
            });

            // Collect removed students (in studentsList)
            $studentsList.find('.student.removed').each(function () {
                var studentId = $(this).attr('id');
                if (studentId) {
                    studentIdsToRemove.push(studentId);
                }
            });

            var title_content = $list.find('.classroom-manage-content-title_text');
            var title_content_original_text = $list.find('.classroom-manage-content-title_text').text();
            var footer_content = $list.find('.assign-students-footer');
            var preloader = $list.find('.tprm-preloader-assign');
            var body_content = $list.find('.outer-students-container');
            var students_container = $(body_content).find('.students-container');
            var added_students_container = $(body_content).find('.added-students-container');

            console.log('studentIdsToAssign :' + studentIdsToAssign);
            console.log('studentIdsToRemove :' + studentIdsToRemove);

            $(footer_content).hide();
            $(students_container).hide();
            $(added_students_container).hide();
            $(preloader).show();
            $(title_content).text(MCL_data.assigning_students_in_progress);

            if (classroom_id) {

                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'assign_students',
                        security: security,
                        payload: 'assign_students_payload',
                        classroom_id: classroom_id,
                        student_ids_to_assign: studentIdsToAssign,
                        student_ids_to_remove: studentIdsToRemove,
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (result) {
                        console.log(result);
                        $(footer_content).show();
                        $(students_container).show();
                        $(added_students_container).show();
                        $(preloader).hide();
                        $(title_content).text(MCL_data.assigning_students_success);

                        $('#close_assign_students').show();
                        $('#revert_assign_students').hide();

                        $('#close_assign_students').on('click', function (e) {
                            $.magnificPopup.close();
                            $(title_content).text(title_content_original_text);
                            setTimeout(() => {
                                bp.Nouveau.objectRequest({
                                    object: 'groups',
                                    scope: 'personal',
                                    filter: 'active',
                                    page: 1,
                                    extras: false,
                                    //group_year: selectedYear
                                }).done(function (response) {
                                    var count = response.data.scopes.all;
                                    $('#subgroups-groups-li').find('span.count').text(count)
                                });
                                $(document).trigger(
                                    'bb_trigger_toast_message',
                                    [
                                        '',
                                        '<div>' + MCL_data.assigning_students_success + '</div>',
                                        'success',
                                        null,
                                        true,
                                    ]
                                );
                            }, 2000);
                        })

                    },
                    error: function (result) {
                        console.log(result);
                        $studentsList.html(originalStudentsListHtml);  // Restore the original students list                                      
                        $addedStudentsList.html(originalAddedStudentsListHtml); // Clear the added students list
                        $('#assign-students-dropdown-' + classroom_id).val('');
                        $(footer_content).show();
                        $(students_container).show();
                        $(added_students_container).show();
                        $(preloader).hide();
                        $(title_content).text(title_content_original_text);
                        $.magnificPopup.close();
                        $(document).trigger(
                            'bb_trigger_toast_message',
                            [
                                '',
                                '<div>' + MCL_data.error_assigning_students + '</div>',
                                'error',
                                null,
                                true,
                            ]
                        );
                    },
                });
            } else {
                $studentsList.html(originalStudentsListHtml);  // Restore the original students list                                      
                $addedStudentsList.html(originalAddedStudentsListHtml); // Clear the added students list
                $('#assign-students-dropdown-' + classroom_id).val('');
                $(footer_content).show();
                $(students_container).show();
                $(added_students_container).show();
                $(preloader).hide();
                $(title_content).text(title_content_original_text);
                //$.magnificPopup.close();
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MCL_data.no_students_selected + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
            }
        });

        $list.find('#revert_assign_students').on('click', function (e) {
            e.preventDefault();
            $studentsList.html(originalStudentsListHtml);  // Restore the original students list                                      
            $addedStudentsList.html(originalAddedStudentsListHtml); // Clear the added students list
            $('#assign-students-dropdown-' + classroom_id).val('');
            //$.magnificPopup.close();
        })

    }

    /* 
    *  End Assign Student
    */


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

});
