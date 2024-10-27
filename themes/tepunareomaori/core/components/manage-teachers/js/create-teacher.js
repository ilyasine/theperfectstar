jQuery(document).ready(function ($) {
    var current_fs, next_fs, previous_fs; // fieldsets
    var left, opacity, scale; // fieldset properties which we will animate
    var animating; // flag to prevent quick multi-click glitches

    //teacher Details
    var school_id, school_name, TeacherFirstName, TeacherLastName, TeacherEmail, TeacherPassword, TeacherPasswordValue;
    var ClassroomIds = [];
    var classroom_selection_confirmed = false;

    // Function to handle tab switching
    function switchTab(activeButton, showDiv) {
        // Remove active-tab class from all buttons
        $('.manage-teachers button').removeClass('active-tab');
        activeButton.addClass('active-tab');
        $('#all-teachers, #create-teacher').hide();
        showDiv.fadeIn();
    }

    $('.manage-teachers button.all-teachers').addClass('active-tab');

    // Click event for all-teachers button
    $('.all-teachers').click(function () {
        switchTab($(this), $('#all-teachers'));
    });

    // Click event for create-teacher button
    $('.create-teacher').click(function () {
        switchTab($(this), $('#create-teacher'));
    });

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

    /* Manage Teacher Classrooms on Create Teacher page */
    /*  $('fieldset#classrooms_setup').find('.toggle-classroom-teacher').on('click', function () {
         var $this = $(this);
     
         $this.toggleClass('active');
         $this.closest('tr.classroom').toggleClass('selected');
     
         if ($this.hasClass('active')) {
             $this.attr('data-bp-tooltip', MTE_data.remove_classroom);
             $this.find('.bp-screen-reader-text').text(MTE_data.remove_classroom);
         } else {
             $this.attr('data-bp-tooltip', MTE_data.assign_classroom);
             $this.find('.bp-screen-reader-text').text(MTE_data.assign_classroom);
         }
     
         $('#confirm-classroom-selection').fadeIn();
     }); */

    $(document).on('click mouseenter mouseleave', '.toggle-classroom-teacher', function (event) {
        var $btn = $(this);
        var $tr = $btn.closest('tr.classroom');
        var $prevTr = $tr.prev('tr.classroom');
        var isActive = $btn.hasClass('active'); // Determine if it should be active after the click

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

            $('#confirm-classroom-selection').fadeIn();

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

    // Add the event listener for the "Select All" button
    $('th.classroom_action .toggle-classroom-teacher-all').on('click', function () {
        var $this = $(this);
        var allSelected = $('fieldset#classrooms_setup').find('.toggle-classroom-teacher').length === $('fieldset#classrooms_setup').find('.toggle-classroom-teacher.active').length;
        // console.log( ' all' ,  $('fieldset#classrooms_setup').find('.toggle-classroom-teacher').length );
        //console.log( ' active only' , $('fieldset#classrooms_setup').find('.toggle-classroom-teacher.active').length);
        $('fieldset#classrooms_setup').find('.toggle-classroom-teacher').each(function () {
            var $btn = $(this);
            if (allSelected) {
                // Deselect all
                $btn.removeClass('active');
                $btn.closest('tr.classroom').removeClass('selected');
                $btn.attr('data-bp-tooltip', MTE_data.assign_all_classroom);
                $btn.find('.bp-screen-reader-text').text(MTE_data.assign_all_classroom);
            } else {
                // Select all
                $btn.addClass('active');
                $btn.closest('tr.classroom').addClass('selected');
                $btn.attr('data-bp-tooltip', MTE_data.remove_all_classrooms);
                $btn.find('.bp-screen-reader-text').text(MTE_data.remove_all_classrooms);
            }
        });

        // Toggle the state of the "Select All" button itself
        if (!allSelected) {
            $this.removeClass('active');
            $this.attr('data-bp-tooltip', MTE_data.assign_all_classroom);
            $this.find('.bp-screen-reader-text').text(MTE_data.assign_all_classroom);
        } else {
            $this.addClass('active');
            $this.attr('data-bp-tooltip', MTE_data.remove_classroom_all);
            $this.find('.bp-screen-reader-text').text(MTE_data.remove_classroom_all);
        }

        $table.find('#confirm-classroom-selection').fadeIn();
    });


    $('#confirm-classroom-selection').on('click', function () {
        ClassroomIds = [];

        $('tr.classroom.selected').each(function () {
            ClassroomIds.push($(this).attr('id'));
        });

        if (ClassroomIds.length === 0) {
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>' + MTE_data.no_classroom_selected + '</div>',
                    'error',
                    null,
                    true,
                ]
            );
        } else {
            classroom_selection_confirmed = true;
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>' + MTE_data.classroom_selected + '</div>',
                    'success',
                    null,
                    true,
                ]
            );
        }

    });

    function resetForm() {
        $('form#multistepsform').trigger("reset");
        $('fieldset#classrooms_setup').find('tr.classroom').removeClass('selected');
        $('fieldset#classrooms_setup').find('tr.classroom .toggle-teacher-classroom').removeClass('selected');
        $('fieldset#teacher_credentials').find('#teacher_email').val('').attr('placeholder', MTE_data.teacher_email_placeholder);
        $('fieldset#teacher_credentialsp').find('#teacher_password').val('').attr('placeholder', '*****************');
        $('fieldset#teacher_credentialsp').find('#password_strength').text('');
    }

    function resetToFirstFieldsetGlobal() {
        // Hide all fieldsets
        $('fieldset').hide();

        // Show the first fieldset and style it correctly
        $('#teacher_details').show();
        $('#teacher_details').css({
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

        // teacher details
        if (current_fs.attr('id') === 'teacher_details') {
            var teacherDetails = $(this).closest('fieldset#teacher_details');
            school_id = $(teacherDetails).find('#teacher_school').data('school_id');
            school_name = $(teacherDetails).find('#teacher_school').text();
            TeacherFirstName = $(teacherDetails).find('#teacher_firstname').val();
            TeacherLastName = $(teacherDetails).find('#teacher_lastname').val();
            TeacherPassword = $('fieldset#teacher_credentials').find('#teacher_password');
            TeacherPasswordValue = $(TeacherPassword).val();

            $('fieldset#teacher_credentials').on('click', '#generate_teacher_password', function () {
                var newPassword = randomPassword(10);
                TeacherPassword.val(newPassword).trigger('change');
                TeacherPasswordValue = newPassword;
                validatePasswordStrength(newPassword);
            });

            $('#teacher_password').on('input', function () {
                var password = $(this).val();
                validatePasswordStrength(password);
            });

            function randomPassword(length) {
                var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
                var password = "";
                for (var i = 0; i < length; i++) {
                    var randomIndex = Math.floor(Math.random() * chars.length);
                    password += chars[randomIndex];
                }
                return password;
            }

            function validatePasswordStrength(password) {
                var strength = getPasswordStrength(password);
                var $passwordStrength = $('#password_strength');

                if (strength >= 4) {
                    $passwordStrength.text(MTE_data.strong_teacher_password).css('color', 'green');
                } else {
                    $passwordStrength.text(MTE_data.weak_teacher_password).css('color', 'red');
                }
            }

            function getPasswordStrength(password) {
                var strength = 0;
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                return strength;
            }

            $('#teacher_form').on('submit', function (event) {
                var password = $('#TeacherPassword').val();
                var strength = getPasswordStrength(password);

                if (strength < 4) {
                    event.preventDefault();
                    alert('Please generate a stronger password.');
                }
            });



        }

        // teacher credentials
        if (current_fs.attr('id') === 'teacher_credentials') {
            var teacherCredentials = $(this).closest('fieldset#teacher_credentials');
            TeacherEmail = $(teacherCredentials).find('#teacher_email').val();
            TeacherPassword = $(teacherCredentials).find('#teacher_password').val();
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

    $("#submit-create-teacher").click(function (e) {
        current_fs = $(this).closest('fieldset');
        var security = $(this).data('security');

        //console.log(school_id, school_name, TeacherFirstName, TeacherLastName, TeacherEmail, TeacherPassword, ClassroomIds);

        if (current_fs.attr('id') === 'classrooms_setup') {
            e.preventDefault();

            if (!TeacherFirstName) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MTE_data.missing_teacher_first_name + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if teacherName is missing
            }

            if (!TeacherLastName) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MTE_data.missing_teacher_last_name + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if teacherName is missing
            }

            if (!TeacherEmail) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MTE_data.missing_teacher_email + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if email is missing
            }

            if (!TeacherPassword) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MTE_data.missing_teacher_password + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if password is missing
            }

            // Check for unconfirmed teachers
            if ($('tr.classroom.selected').length > 0 && !classroom_selection_confirmed) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MTE_data.unconfirmed_selection + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if teachers are not confirmed
            }

            // Check for nonselected teachers
            if ($('tr.classroom').length > 0 && !classroom_selection_confirmed) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MTE_data.no_classroom_selected + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if teachers are not confirmed
            }

            // if everything is fine, we can start creating our teacher

            NProgress.start();
            NProgress.set(0.4);

            var interval = setInterval(function () {
                NProgress.inc();
            }, 1000);
            clearInterval(interval);

            title = current_fs.find('.fieldset-header .fs-title');
            subtitle = current_fs.find('.fieldset-header .fs-subtitle');
            fieldset_body = current_fs.find('.fieldset-body');
            fieldset_footer = current_fs.find('.fieldset-footer');
            fieldset_notice = fieldset_body.find('.notice')

            //Creating
            $('.tprm-preloader').fadeIn();
            current_fs.find('.classrooms-list').hide();
            $('.no-classrooms-list').hide();
            fieldset_footer.find('#submit-create-teacher').hide();
            fieldset_footer.find('input.previous').hide();
            current_fs.find('.classrooms-notice').hide();
            current_fs.find('#confirm-classroom-selection').hide();
            title.text(MTE_data.creating_classrrom_header);
            subtitle.text(MTE_data.teacher_being_created_message);

            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'create_teacher',
                    security: security,
                    payload: 'create_teacher_payload',
                    school_id: school_id,
                    school_name: school_name,
                    TeacherFirstName: TeacherFirstName,
                    TeacherLastName: TeacherLastName,
                    TeacherEmail: TeacherEmail,
                    TeacherPassword: TeacherPassword,
                    ClassroomIds: ClassroomIds,
                },
                type: 'post',
                dataType: 'json',
                success: function (result, textstatus) {
                    if (result) {
                        $('.tprm-preloader').hide();
                        // Created
                        NProgress.done();

                        if (result.success === false) {
                            //Error
                            title.addClass('error').text(MTE_data.error_creating_classrrom);
                            subtitle.addClass('error').text(result.data);
                            fieldset_notice.text(result.data).removeClass('success').addClass('error');
                            current_fs.find('input.previous').fadeIn();
                            current_fs.find('input.back').fadeIn();
                            fieldset_notice.fadeIn();

                            fieldset_footer.find('.previous').on('click', function () {
                                fieldset_notice.hide();
                                title.removeClass('error').text(MTE_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                fieldset_footer.find('#submit-create-teacher').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                            })
                            fieldset_footer.find('.back').on('click', function () {
                                resetToFirstFieldset();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MTE_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                fieldset_footer.find('#submit-create-teacher').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                            })
                        } else {

                            // Handle success : teacher Created
                            var teacher_name = result.teacher_name;
                            var teacher_link = result.teacher_link;

                            // Change h2 inner text
                            title.text(MTE_data.teacher_created_title);
                            subtitle.text(MTE_data.teacher_created_subtitle);

                            // Create new button and append it after the h2 element
                            var newButton = '<a href="' + teacher_link + '" class="button new_teacher_link" style="width: fit-content; margin: auto;"\
                            target = "_blank" rel = "noopener noreferrer" > '
                                + teacher_name + '<span class="bb-icon-l bb-icon-external-link"></span></a>';

                            fieldset_notice.text(result.teacher_created_success_message).addClass('success');
                            fieldset_notice.after(newButton);
                            fieldset_notice.fadeIn();

                            $("#create-new-teacher").fadeIn()

                            // Attach event to the new "Create New teacher" button
                            $("#create-new-teacher").click(function () {
                                // Reset form or redirect to teacher creation page
                                resetForm();
                                resetToFirstFieldset();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MTE_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(MTE_data.classrooms_setup_subtitle);
                                //subtitle.text(MTE_data.teacher_being_created_message); // MTE_data.classrooms_setup_subtitle
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                $('.no-classrooms-list').hide();
                                fieldset_footer.find('#submit-create-teacher').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                                current_fs.find('.new_teacher_link').fadeOut();
                                $(this).hide();
                            });

                            $('.manage-teachers button.all-teachers').on('click', function () {
                                // Trigger click event on the "Create New teacher" button
                                resetForm();
                                resetToFirstFieldsetGlobal();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MTE_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(MTE_data.classrooms_setup_subtitle);
                                //subtitle.text(MTE_data.teacher_being_created_message); // MTE_data.classrooms_setup_subtitle
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                fieldset_footer.find('#submit-create-teacher').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                                current_fs.find('.new_teacher_link').fadeOut();
                                $("#create-new-teacher").hide();

                                /*  bp.Nouveau.objectRequest({
                                     object: 'groups',
                                     scope: 'personal',
                                     filter: 'active',
                                     page: 1,
                                     extras: false,
                                     //group_year: selectedYear
                                 }).done(function (response) {
                                     var count = response.data.scopes.all;
                                     $('#subgroups-groups-li').find('span.count').text(count)
                                 }); */
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            });

                        }

                    }
                },
                error: function (result) {
                    console.log(result);
                    console.log('fail');

                    $('.tprm-preloader').hide();
                    NProgress.done();

                    title.addClass('error').text(MTE_data.error_creating_classrrom);
                    subtitle.addClass('error').text(result.responseText);
                    fieldset_notice.text(MTE_data.choose_different_teacher_name).removeClass('success').addClass('error');
                    current_fs.find('input.previous').fadeIn();
                    current_fs.find('input.back').fadeIn();
                    fieldset_notice.fadeIn();

                    fieldset_footer.find('.previous').on('click', function () {
                        fieldset_notice.hide();
                        title.removeClass('error').text(MTE_data.classrooms_setup_title);
                        subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                        current_fs.find('.classrooms-list').fadeIn();
                        current_fs.find('.classrooms-notice').fadeIn();
                        current_fs.find('#confirm-classroom-selection').fadeIn();
                        fieldset_footer.find('#submit-create-teacher').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                    })
                    fieldset_footer.find('.back').on('click', function () {
                        resetToFirstFieldset();
                        fieldset_notice.hide();
                        title.removeClass('error').text(MTE_data.classrooms_setup_title);
                        subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                        current_fs.find('.classrooms-list').fadeIn();
                        current_fs.find('.classrooms-notice').fadeIn();
                        current_fs.find('#confirm-classroom-selection').fadeIn();
                        fieldset_footer.find('#submit-create-teacher').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                    })
                },
            });

        }
    });


});
