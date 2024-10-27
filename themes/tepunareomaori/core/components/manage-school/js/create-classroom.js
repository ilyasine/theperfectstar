jQuery(document).ready(function ($) {
    var current_fs, next_fs, previous_fs; // fieldsets
    var left, opacity, scale; // fieldset properties which we will animate
    var animating; // flag to prevent quick multi-click glitches

    //Classroom Details
    var school_id, school_name, classroomName;
    var TeacherIds = [];
    var teacher_selection_confirmed = false;

    function validateFields(fieldset) {
        var isValid = true;
        fieldset.find('input, select').each(function () {
            if (!this.checkValidity()) {
                isValid = false;
                $(this).addClass('invalid');
                $(this)[0].reportValidity(); // Triggers the browser's built-in validation popup
            } else {
                $(this).removeClass('invalid');
            }
        });
        return isValid;
    }

    $('fieldset#teachers_setup').find('.toggle-classroom-teacher').on('click', function () {
        var $this = $(this);

        $this.toggleClass('active');
        $this.closest('li.teacher').toggleClass('selected');

        if ($this.hasClass('active')) {
            $this.attr('data-bp-tooltip', MCL_data.remove_teacher);
            $this.find('.bp-screen-reader-text').text(MCL_data.remove_teacher);
        } else {
            $this.attr('data-bp-tooltip', MCL_data.add_teacher);
            $this.find('.bp-screen-reader-text').text(MCL_data.add_teacher);
        }

        $('#confirm-teacher-selection').fadeIn();
    });

    $('#confirm-teacher-selection').on('click', function () {
        TeacherIds = [];

        $('li.teacher.selected').each(function () {
            TeacherIds.push($(this).attr('id'));
        });

        if (TeacherIds.length === 0) {
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>' + MCL_data.no_teacher_selected + '</div>',
                    'error',
                    null,
                    true,
                ]
            );
        } else {
            teacher_selection_confirmed = true;
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>' + MCL_data.teacher_selected + '</div>',
                    'success',
                    null,
                    true,
                ]
            );
        }


    });

    function resetForm() {
        $('form#multistepsform').trigger("reset");
        $('fieldset#teachers_setup').find('li.teacher').removeClass('selected');
        $('fieldset#teachers_setup').find('li.teacher .toggle-classroom-teacher').removeClass('active');
    }

    function resetToFirstFieldsetGlobal() {
        // Hide all fieldsets
        $('fieldset').hide();

        // Reset any form state here (input values, selection states, etc.)

        // Show the first fieldset and style it correctly
        $('#classroom_details').show();
        $('#classroom_details').css({
            'display': 'flex',
            'left': '0%',
            'opacity': 1,
            'transform': 'scale(1)',
            'position': 'absolute'
        });

        $('fieldset').css({
            'transform': 'scale(1)',
            'opacity': 1,
            'left': '0%'
        });
    }

    function resetToFirstFieldset() {
        if (animating) return false;
        animating = true;

        var current_fs = $('fieldset:visible');
        var first_fs = $('fieldset').first();

        // Reset progressbar 
        $("#progressbar li").removeClass("active");
        $("#progressbar li").first().addClass("active");

        // Reset styles for all fieldsets
        $('fieldset').css({
            'transform': 'scale(1)',
            'opacity': 1,
            'left': '0%'
        });

        // Show the first fieldset with animation 
        first_fs.show();
        first_fs.css('display', 'flex');
        current_fs.animate({ opacity: 0 }, {
            step: function (now, mx) {
                // As the opacity of current_fs reduces to 0 - stored in "now" 
                // 1. Scale first_fs from 80% to 100% 
                var scale = 0.8 + (1 - now) * 0.2;
                // 2. Take current_fs to the right(50%) - from 0% 
                var left = ((1 - now) * 50) + "%";
                // 3. Increase opacity of first_fs to 1 as it moves in 
                var opacity = 1 - now;
                current_fs.css({ 'left': left });
                first_fs.css({ 'transform': 'scale(' + scale + ')', 'opacity': opacity });
            },
            duration: 800,
            complete: function () {
                current_fs.hide();
                animating = false;
            },
            easing: 'easeInOutBack'
        });

    }


    $(".fieldset-footer .next").click(function () {
        if (animating) return false;

        current_fs = $(this).closest('fieldset');
        next_fs = current_fs.next();

        // Validate fields
        if (!validateFields(current_fs)) {
            return false;
        }

        // Classroom details
        if (current_fs.attr('id') === 'classroom_details') {
            var classroomDetails = $(this).closest('fieldset#classroom_details');
            school_id = $(classroomDetails).find('#classroom_school').data('school_id');
            school_name = $(classroomDetails).find('#classroom_school').text();
            classroomName = $(classroomDetails).find('#classroom_name').val();
        }


        animating = true;

        // Activate next step on progressbar using the index of next_fs
        $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

        // Show the next fieldset
        next_fs.show();
        next_fs.css('display', 'flex');
        // Hide the current fieldset with style
        current_fs.animate({ opacity: 0 }, {
            step: function (now, mx) {
                // As the opacity of current_fs reduces to 0 - stored in "now"
                // 1. Scale current_fs down to 80%
                scale = 1 - (1 - now) * 0.2;
                // 2. Bring next_fs from the right(50%)
                left = (now * 50) + "%";
                // 3. Increase opacity of next_fs to 1 as it moves in
                opacity = 1 - now;
                current_fs.css({ 'transform': 'scale(' + scale + ')', 'position': 'absolute' });
                //current_fs.css({ 'position': 'relative' });
                next_fs.css({ 'left': left, 'opacity': opacity });
            },
            duration: 800,
            complete: function () {
                current_fs.hide();
                animating = false;
            },
            easing: 'easeInOutBack'
        });
    });

    $(".fieldset-footer .previous").click(function () {
        if (animating) return false;
        animating = true;

        current_fs = $(this).closest('fieldset');
        previous_fs = current_fs.prev();

        // De-activate current step on progressbar
        $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

        // Show the previous fieldset
        previous_fs.show();
        previous_fs.css('display', 'flex');
        // Hide the current fieldset with style
        current_fs.animate({ opacity: 0 }, {
            step: function (now, mx) {
                // As the opacity of current_fs reduces to 0 - stored in "now"
                // 1. Scale previous_fs from 80% to 100%
                scale = 0.8 + (1 - now) * 0.2;
                // 2. Take current_fs to the right(50%) - from 0%
                left = ((1 - now) * 50) + "%";
                // 3. Increase opacity of previous_fs to 1 as it moves in
                opacity = 1 - now;
                current_fs.css({ 'left': left });
                previous_fs.css({ 'transform': 'scale(' + scale + ')', 'opacity': opacity });
            },
            duration: 800,
            complete: function () {
                current_fs.hide();
                animating = false;
            },
            easing: 'easeInOutBack'
        });
    });

    $("#submit-create-classroom").click(function (e) {
        current_fs = $(this).closest('fieldset');
        var security = $(this).data('security');

        if (current_fs.attr('id') === 'teachers_setup') {
            e.preventDefault();

            if (!classroomName) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MCL_data.missing_classroom_name + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if classroomName is missing
            }

            // Check for unconfirmed teachers
            if ($('li.teacher.selected').length > 0 && !teacher_selection_confirmed) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MCL_data.unconfirmed_selection + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if teachers are not confirmed
            }

            // Check for nonselected teachers
            if ($('li.teacher').length > 0 && !teacher_selection_confirmed) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MCL_data.no_teacher_selected + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if teachers are not confirmed
            }

            // if everything is fine, we can start creating our classroom

            title = current_fs.find('.fieldset-header .fs-title');
            subtitle = current_fs.find('.fieldset-header .fs-subtitle');
            fieldset_body = current_fs.find('.fieldset-body');
            fieldset_footer = current_fs.find('.fieldset-footer');
            fieldset_notice = fieldset_body.find('.notice');

            //Creating
            $('.tprm-preloader').fadeIn();
            current_fs.find('.teachers-list').hide();
            $('.no-teachers-list').hide();
            fieldset_footer.find('#submit-create-classroom').hide();
            fieldset_footer.find('input.previous').hide();
            current_fs.find('.teachers-notice').hide();
            current_fs.find('#confirm-teacher-selection').hide();
            title.text(MCL_data.creating_classrrom_header);
            subtitle.text(MCL_data.classroom_being_created_message);

            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'create_classroom',
                    security: security,
                    payload: 'create_classroom_payload',
                    school_id: school_id,
                    school_name: school_name,
                    classroomName: classroomName,
                    TeacherIds: TeacherIds,
                },
                type: 'post',
                dataType: 'json',
                success: function (result, textstatus) {
                    console.log(result);
                    if (result) {
                        $('.tprm-preloader').hide();
                        // Created
                        //NProgress.done();

                        if (result.success === false) {
                            //Error
                            title.addClass('error').text(MCL_data.error_creating_classrrom);
                            subtitle.addClass('error').text(result.data);
                            fieldset_notice.text(result.data + ' ' + MCL_data.choose_different_classroom_name).removeClass('success').addClass('error');
                            current_fs.find('input.previous').fadeIn();
                            current_fs.find('input.back').fadeIn();
                            fieldset_notice.fadeIn();

                            fieldset_footer.find('.previous').on('click', function () {
                                fieldset_notice.hide();
                                title.removeClass('error').text(MCL_data.teachers_setup_title);
                                subtitle.removeClass('error').text(result.teachers_setup_subtitle);
                                current_fs.find('.teachers-list').fadeIn();
                                current_fs.find('.teachers-notice').fadeIn();
                                current_fs.find('#confirm-teacher-selection').fadeIn();
                                fieldset_footer.find('#submit-create-classroom').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                            })
                            fieldset_footer.find('.back').on('click', function () {
                                resetToFirstFieldset();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MCL_data.teachers_setup_title);
                                subtitle.removeClass('error').text(result.teachers_setup_subtitle);
                                current_fs.find('.teachers-list').fadeIn();
                                current_fs.find('.teachers-notice').fadeIn();
                                current_fs.find('#confirm-teacher-selection').fadeIn();
                                fieldset_footer.find('#submit-create-classroom').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                            })
                        } else {

                            // Handle success : Classroom Created
                            var classroom_name = result.classroom_name;
                            var classroom_link = result.classroom_link;

                            // Change h2 inner text
                            title.text(MCL_data.classroom_created_title);
                            subtitle.text(MCL_data.classroom_created_subtitle);

                            // Create new button and append it after the h2 element
                            var newButton = '<a href="' + classroom_link + '" class="button new_classroom_link" style="width: fit-content; margin: auto;"\
                            target = "_blank" rel = "noopener noreferrer" > '
                                + classroom_name + '<span class="bb-icon-l bb-icon-external-link"></span></a>';

                            fieldset_notice.text(result.classroom_created_success_message).addClass('success');
                            fieldset_notice.after(newButton);
                            fieldset_notice.fadeIn();

                            $("#create-new-classroom").fadeIn()

                            // Attach event to the new "Create New Classroom" button
                            $("#create-new-classroom").click(function () {
                                // Reset form or redirect to classroom creation page
                                resetForm();
                                resetToFirstFieldset();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MCL_data.teachers_setup_title);
                                subtitle.removeClass('error').text(MCL_data.teachers_setup_subtitle);
                                //subtitle.text(MCL_data.classroom_being_created_message); // MCL_data.teachers_setup_subtitle
                                current_fs.find('.teachers-list').fadeIn();
                                current_fs.find('.teachers-notice').fadeIn();
                                current_fs.find('#confirm-teacher-selection').fadeIn();
                                $('.no-teachers-list').fadeIn();
                                fieldset_footer.find('#submit-create-classroom').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                                current_fs.find('.new_classroom_link').fadeOut();
                                $(this).hide();
                            });

                            $('.manage-classrooms button.all-classrooms').on('click', function () {
                                // Trigger click event on the "Create New Classroom" button
                                resetForm();
                                resetToFirstFieldsetGlobal();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MCL_data.teachers_setup_title);
                                subtitle.removeClass('error').text(MCL_data.teachers_setup_subtitle);
                                //subtitle.text(MCL_data.classroom_being_created_message); // MCL_data.teachers_setup_subtitle
                                current_fs.find('.teachers-list').fadeIn();
                                current_fs.find('.teachers-notice').fadeIn();
                                current_fs.find('#confirm-teacher-selection').fadeIn();
                                fieldset_footer.find('#submit-create-classroom').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                                current_fs.find('.new_classroom_link').fadeOut();
                                $("#create-new-classroom").hide();

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
                            });

                        }

                    }
                },
                error: function (result) {
                    console.log(result);
                    console.log('fail');

                    $('.tprm-preloader').hide();
                    //NProgress.done();

                    title.addClass('error').text(MCL_data.error_creating_classrrom);
                    subtitle.addClass('error').text(result.responseText);
                    fieldset_notice.text(MCL_data.choose_different_classroom_name).removeClass('success').addClass('error');
                    current_fs.find('input.previous').fadeIn();
                    current_fs.find('input.back').fadeIn();
                    fieldset_notice.fadeIn();

                    fieldset_footer.find('.previous').on('click', function () {
                        fieldset_notice.hide();
                        title.removeClass('error').text(MCL_data.teachers_setup_title);
                        subtitle.removeClass('error').text(result.teachers_setup_subtitle);
                        current_fs.find('.teachers-list').fadeIn();
                        current_fs.find('.teachers-notice').fadeIn();
                        current_fs.find('#confirm-teacher-selection').fadeIn();
                        fieldset_footer.find('#submit-create-classroom').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                    })
                    fieldset_footer.find('.back').on('click', function () {
                        resetToFirstFieldset();
                        fieldset_notice.hide();
                        title.removeClass('error').text(MCL_data.teachers_setup_title);
                        subtitle.removeClass('error').text(result.teachers_setup_subtitle);
                        current_fs.find('.teachers-list').fadeIn();
                        current_fs.find('.teachers-notice').fadeIn();
                        current_fs.find('#confirm-teacher-selection').fadeIn();
                        fieldset_footer.find('#submit-create-classroom').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                    })
                },
            });

        }
    });
});
