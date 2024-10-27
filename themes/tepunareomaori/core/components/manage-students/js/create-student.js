jQuery(document).ready(function ($) {
    var current_fs, next_fs, previous_fs; // fieldsets
    var left, opacity, scale; // fieldset properties which we will animate
    var animating; // flag to prevent quick multi-click glitches

    //student Details
    var selectedPasswordMode = ''; // 'text' or 'picture'
    var selectedPasswordValue = '';
    var school_id, school_name, studentFirstName, studentLastName, studentPassword;
    var selectedClassroomId = '';
    var selectedPicture = '';
    var classroom_selection_confirmed = false;
    var picture_selection_confirmed = false;


    // Function to handle tab switching
    function switchTab(activeButton, showDiv) {
        // Remove active-tab class from all buttons
        $('.manage-students button').removeClass('active-tab');
        activeButton.addClass('active-tab');
        $('#all-students, #create-student, #bulk-create-student').hide();
        showDiv.fadeIn();
    }

    $('.manage-students button.all-students').addClass('active-tab');

    // Click event for all-students button
    $('.all-students').click(function () {
        switchTab($(this), $('#all-students'));
    });

    // Click event for create-student button
    $('.create-student').click(function () {
        switchTab($(this), $('#create-student'));
    });

    // Click event for create-student button
    $('.bulk-create-student').click(function () {
        switchTab($(this), $('#bulk-create-student'));
    });

    //curriculum level select
    $("#password_type").select2();
    $('#password_type').one('select2:open', function (e) {
        $('input.select2-search__field').prop('placeholder', MST_data.choose_password_type);
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

    /* Manage student Classrooms on Create student page */
    $('fieldset#classrooms_setup').find('.toggle-classroom-student').on('click', function () {
        var $this = $(this);

        $this.toggleClass('active');
        $this.closest('tr.classroom').toggleClass('selected');

        if ($this.hasClass('active')) {
            $this.attr('data-bp-tooltip', MST_data.remove_classroom);
            $this.find('.bp-screen-reader-text').text(MST_data.remove_classroom);
        } else {
            $this.attr('data-bp-tooltip', MST_data.assign_classroom);
            $this.find('.bp-screen-reader-text').text(MST_data.assign_classroom);
        }

        $('#confirm-classroom-selection').fadeIn();
    });


    $(document).on('click', '.toggle-classroom-student', function () {
        var $btn = $(this);
        var $checkbox = $btn.find('input.cb-value');
        var $parentRow = $btn.closest('tr.classroom');
        var isActive = $btn.hasClass('active');

        // Deselect all other classrooms
        $('.toggle-classroom-student.active').removeClass('active').find('input.cb-value').prop('checked', false);
        $('tr.classroom.selected').removeClass('selected');

        // Select the clicked classroom if it was not already active
        if (!isActive) {
            $btn.addClass('active');
            $checkbox.prop('checked', true);
            $parentRow.addClass('selected');
        } else {
            $parentRow.removeClass('selected');
        }

        // Show or hide the confirm button based on selection
        var anySelected = $('.toggle-classroom-student.active').length > 0;
        $('#confirm-classroom-selection').toggle(anySelected);
    });


    $('#confirm-classroom-selection').on('click', function () {
        selectedClassroomId = $('tr.classroom.selected').attr('id');

        if (!selectedClassroomId) {
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>' + MST_data.no_classroom_selected + '</div>',
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
                    '<div>' + MST_data.classroom_selected + '</div>',
                    'success',
                    null,
                    true,
                ]
            );

        }
    });


    $('#confirm-picture-password').on('click', function () {
        if (selectedPicture === '') {
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>No picture selected</div>',
                    'error',
                    null,
                    true,
                ]
            );
        } else {
            picture_selection_confirmed = true;
            $(document).trigger(
                'bb_trigger_toast_message',
                [
                    '',
                    '<div>Picture password has been set successfully</div>',
                    'success',
                    null,
                    true,
                ]
            );
        }

        checkNextButtonState();
    });

    $('fieldset#student_credentials').find('#student_password').on('change', function () {
        checkNextButtonState();
    });

    function resetForm() {
        $('form#multistepsform').trigger("reset");
        $('fieldset#classrooms_setup').find('tr.classroom').removeClass('selected');
        selectedPicture = '';
        $('fieldset#classrooms_setup').find('tr.classroom .toggle-classroom-student').removeClass('active');
        $('fieldset#student_credentials').find('#password_type').val('').trigger('change');
        $('fieldset#student_credentials').find('#student_password').val('').attr('placeholder', '*****************');
        $('fieldset#student_credentials').find('#password_strength').text('');
    }

    function resetToFirstFieldsetGlobal() {
        // Hide all fieldsets
        $('fieldset').hide();

        // Show the first fieldset and style it correctly
        $('#student_details').show();
        $('#student_details').css({
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

    $('.picture-password .images-grid .picture-container img').on('click', function () {
        var $container = $(this).closest('.picture-container');
        var imageUrl = $(this).attr('src');

        if ($container.hasClass('selected')) {
            $container.removeClass('selected');
            selectedPicture = '';
            picture_selection_confirmed = false;
            $('#confirm-picture-password').fadeOut();
        } else {
            $('.picture-password .images-grid .picture-container').removeClass('selected');
            $container.addClass('selected');
            selectedPicture = imageUrl;
            $('#confirm-picture-password').fadeIn();
        }

        //console.log('selectedPicture : ', selectedPicture); // Log the selected picture URL (if any)

        checkNextButtonState();
    });

    $('.images-grid').on('click', '.picture-container', function () {
        var $container = $(this);
        var $img = $container.find('img');
        var imageUrl = $img.attr('src');

        if ($container.hasClass('selected')) {
            // If the clicked container is already selected, deselect it
            $container.removeClass('selected');
            $img.css({
                'filter': 'brightness(0.9)',
                'transform': 'scale(1)',
                'box-shadow': 'none',
                'border': 'none'
            });
            $container.find('.bb-icon-thumbtack-star').css('display', 'none');
            selectedPicture = ''; // Clear the selected picture variable
        } else {
            // Deselect any previously selected container
            $('.images-grid').find('.picture-container').removeClass('selected');
            $('.images-grid').find('img').css({
                'filter': 'brightness(0.9)',
                'transform': 'scale(1)',
                'box-shadow': 'none',
                'border': 'none'
            });
            $('.images-grid').find('.bb-icon-thumbtack-star').css('display', 'none');

            // Select the clicked container
            $container.addClass('selected');
            $img.css({
                'filter': 'brightness(1.2) saturate(1.5) drop-shadow(2px 4px 6px black)',
                'transform': 'scale(1.1)',
                'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)',
                'border': '1px solid #fff'
            });
            $container.find('.bb-icon-thumbtack-star').css('display', 'flex');
            selectedPicture = imageUrl; // Store the selected picture URL
        }

        //console.log('selectedPicture : ', selectedPicture); // Log the selected picture URL (if any)
    });

    $('fieldset#student_credentials').on('change', '#password_type', function () {
        var selectedType = $(this).val();
        var $textPasswordInput = $('fieldset#student_credentials').find('#student_password');
        if (selectedType === 'text') {
            $('.text-password').fadeIn();
            $('.picture-password').hide();
            $('#confirm-picture-password').hide();
            selectedPicture = '';

        } else if (selectedType === 'picture') {
            $('.text-password').hide();
            $('.picture-password').fadeIn();
            $('#student_password').val('');
            $('fieldset#student_credentials').find('#password_strength').text('');
            $textPasswordInput.removeAttr('required');
            checkSelectedPicture();
        } else {
            $('.text-password').fadeOut();
            $('.picture-password').fadeOut();
            $('#confirm-picture-password').hide();
            selectedPicture = '';
        }
        selectedPasswordMode = selectedType;
        checkNextButtonState();
    })

    function checkSelectedPicture() {
        var $nextButton = $('fieldset#student_credentials').find('input.next');
        var selectedType = $('#password_type').val();
        var $textPasswordInput = $('fieldset#student_credentials').find('#student_password');

        if (selectedType === 'picture' && selectedPicture == '') {
            $nextButton.addClass('disabled');
            $('#confirm-picture-password').hide();
        }
        else if (selectedType == '') {
            $nextButton.addClass('disabled');
            $('#confirm-picture-password').hide();
        }
        else if (selectedType === 'text' && $textPasswordInput.val() == '') {
            $nextButton.addClass('disabled');
            $('#confirm-picture-password').hide();
        }
        else {
            $nextButton.removeClass('disabled');
        }
    }

    function checkNextButtonState() {
        var selectedType = $('#password_type').val();
        var $textPasswordInput = $('fieldset#student_credentials').find('#student_password');
        var $nextButton = $('fieldset#student_credentials').find('input.next');

        if (selectedType === 'text') {
            // If the selected type is text, check if the password input is filled
            if ($textPasswordInput.val() === '') {
                $nextButton.addClass('disabled');
            } else {
                $nextButton.removeClass('disabled');
            }
        } else if (selectedType === 'picture') {
            // If the selected type is picture, check if the picture is selected and confirmed
            if (selectedPicture === '' || !picture_selection_confirmed) {
                $nextButton.addClass('disabled');
            } else {
                $nextButton.removeClass('disabled');
            }
        }
    }

    $(".fieldset-footer .next").click(function () {
        if (animating) return false;

        current_fs = $(this).closest('fieldset');
        next_fs = current_fs.next();

        // Validate fields
        if (!validateFields(current_fs)) {
            return false;
        }

        // student details
        if (current_fs.attr('id') === 'student_details') {
            var studentDetails = $(this).closest('fieldset#student_details');
            school_id = $(studentDetails).find('#student_school').data('school_id');
            school_name = $(studentDetails).find('#student_school').text();
            studentFirstName = $(studentDetails).find('#student_firstname').val();
            studentLastName = $(studentDetails).find('#student_lastname').val();
            studentPassword = $('fieldset#student_credentials').find('#student_password');
            studentPasswordValue = $(studentPassword).val();

            checkSelectedPicture();

            $('fieldset#student_credentials').on('click', '#generate_student_password', function () {
                var newPassword = randomStudentPassword();

                // Ensure studentPassword is correctly referencing the password input field
                var studentPassword = $('#student_password');

                // Check if studentPassword is a valid jQuery object and has length > 0
                if (studentPassword.length > 0) {
                    studentPassword.val(newPassword).trigger('input');
                    validatePasswordStrength(newPassword);
                } else {
                    console.error("Password input field not found.");
                }
            });

            $('#student_password').on('input change', function () {
                var password = $(this).val();
                validatePasswordStrength(password);
            });

            function randomStudentPassword() {
                var prefix = "ks";
                var numbers = "0123456789";
                var randomDigits = "";

                for (var i = 0; i < 4; i++) {
                    var randomIndex = Math.floor(Math.random() * numbers.length);
                    randomDigits += numbers[randomIndex];
                }

                return prefix + randomDigits;
            }

            function validatePasswordStrength(password) {
                var strength = getPasswordStrength(password);
                var $passwordStrength = $('#password_strength');
                var isValidFormat = validatePasswordFormat(password);
                var $nextButton = $('fieldset#student_credentials').find('input.next');
                if (strength >= 2 && isValidFormat) {
                    $passwordStrength.text(MST_data.strong_student_password).css('color', 'green');
                    $nextButton.removeClass('disabled');
                } else if (!isValidFormat) {
                    $nextButton.addClass('disabled');
                    $passwordStrength.text(MST_data.weak_student_password).css('color', 'red');
                } else {
                    $nextButton.addClass('disabled');
                    $passwordStrength.text(MST_data.weak_student_password).css('color', 'red');
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

            function validatePasswordFormat(password) {
                // Regex to check the format: 'ks' followed by 4 or more digits
                var regex = /^ks\d{4,}$/;
                return regex.test(password);
            }

            $('#student_form').on('submit', function (event) {
                var password = $('#student_password').val();
                var strength = getPasswordStrength(password);
                var isValidFormat = validatePasswordFormat(password);

                if (strength < 4 || !isValidFormat) {
                    event.preventDefault();
                    alert('Please enter a valid and strong password. It should start with "ks" followed by 4 digits and meet strength criteria.');
                }
            });


        }

        checkSelectedPicture();

        // student credentials
        if (current_fs.attr('id') === 'student_credentials') {
            var studentCredentials = $(this).closest('fieldset#student_credentials');
            studentEmail = $(studentCredentials).find('#student_email').val();
            studentPassword = $(studentCredentials).find('#student_password').val();
            checkSelectedPicture();

            if (selectedPasswordMode === 'text') {
                selectedPasswordValue = $('#student_password').val();
            } else if (selectedPasswordMode === 'picture') {
                selectedPasswordValue = selectedPicture; // Implement this function
            }

            checkNextButtonState();

            /* console.log('studentFirstName : ' + studentFirstName);
            console.log('studentLastName : ' + studentLastName);
            console.log('selectedPasswordMode : ' + selectedPasswordMode);
            console.log('selectedPasswordValue : ' + selectedPasswordValue); */
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

        checkSelectedPicture();
    });

    $("#submit-create-student").click(function (e) {
        current_fs = $(this).closest('fieldset');
        var security = $(this).data('security');

        /* console.log('studentFirstName : ' + studentFirstName);
        console.log('studentLastName : ' + studentLastName);
        console.log('selectedPasswordMode : ' + selectedPasswordMode);
        console.log('selectedPasswordValue : ' + selectedPasswordValue);
        console.log('selectedClassroomId : ' + selectedClassroomId); */

        if (current_fs.attr('id') === 'classrooms_setup') {
            e.preventDefault();

            // Check for student first name
            if (!studentFirstName) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MST_data.missing_student_first_name + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if studentFirstName is missing
            }

            // Check for student last name
            if (!studentLastName) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MST_data.missing_student_last_name + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if studentLastName is missing
            }

            // Check for student password based on the selected password mode
            if (selectedPasswordMode === 'text') {
                if (!selectedPasswordValue) {
                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + MST_data.missing_student_password + '</div>',
                            'error',
                            null,
                            true,
                        ]
                    );
                    return; // Stop further execution if password is missing
                }
            } else if (selectedPasswordMode === 'picture') {
                // Check for unconfirmed picture password selection
                if ($('.picture-password .images-grid .picture-container.selected').length === 0 || !picture_selection_confirmed) {
                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>unconfirmed picture selection</div>',
                            'error',
                            null,
                            true,
                        ]
                    );
                    return; // Stop further execution if picture password is not confirmed
                }
            }

            // Check for selected classroom
            if (!selectedClassroomId || !classroom_selection_confirmed) {
                $(document).trigger(
                    'bb_trigger_toast_message',
                    [
                        '',
                        '<div>' + MST_data.no_classroom_selected + '</div>',
                        'error',
                        null,
                        true,
                    ]
                );
                return; // Stop further execution if no classroom is selected or confirmed
            }

            // if everything is fine, we can start creating our student

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
            fieldset_footer.find('#submit-create-student').hide();
            fieldset_footer.find('input.previous').hide();
            current_fs.find('.classrooms-notice').hide();
            current_fs.find('#confirm-classroom-selection').hide();
            title.text(MST_data.creating_student_header);
            subtitle.text(MST_data.student_being_created_message);

            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'create_student',
                    security: security,
                    payload: 'create_student_payload',
                    school_id: school_id,
                    school_name: school_name,
                    studentFirstName: studentFirstName,
                    studentLastName: studentLastName,
                    selectedPasswordMode: selectedPasswordMode,
                    selectedPasswordValue: selectedPasswordValue,
                    selectedClassroomId: selectedClassroomId,
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
                            title.addClass('error').text(MST_data.error_creating_classrrom);
                            subtitle.addClass('error').text(result.data);
                            fieldset_notice.text(result.data).removeClass('success').addClass('error');
                            current_fs.find('input.previous').fadeIn();
                            current_fs.find('input.back').fadeIn();
                            fieldset_notice.fadeIn();

                            fieldset_footer.find('.previous').on('click', function () {
                                fieldset_notice.hide();
                                title.removeClass('error').text(MST_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                fieldset_footer.find('#submit-create-student').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                            })
                            fieldset_footer.find('.back').on('click', function () {
                                resetToFirstFieldset();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MST_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                fieldset_footer.find('#submit-create-student').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                            })
                        } else {

                            // Handle success : student Created
                            var student_name = result.student_name;
                            var student_link = result.student_link;

                            // Change h2 inner text
                            title.text(MST_data.student_created_title);
                            subtitle.text(MST_data.student_created_subtitle);

                            // Create new button and append it after the h2 element
                            var newButton = '<a href="' + student_link + '" class="button new_student_link" style="width: fit-content; margin: auto;"\
                            target = "_blank" rel = "noopener noreferrer" > '
                                + student_name + '<span class="bb-icon-l bb-icon-external-link"></span></a>';

                            fieldset_notice.text(result.student_created_success_message).addClass('success');
                            fieldset_notice.after(newButton);
                            fieldset_notice.fadeIn();

                            $("#create-new-student").fadeIn()

                            // Attach event to the new "Create New student" button
                            $("#create-new-student").click(function () {
                                // Reset form or redirect to student creation page
                                resetForm();
                                resetToFirstFieldset();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MST_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(MST_data.classrooms_setup_subtitle);
                                //subtitle.text(MST_data.student_being_created_message); // MST_data.classrooms_setup_subtitle
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                $('.no-classrooms-list').hide();
                                fieldset_footer.find('#submit-create-student').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                                current_fs.find('.new_student_link').fadeOut();
                                $(this).hide();
                            });

                            $('.manage-students button.all-students').on('click', function () {
                                // Trigger click event on the "Create New student" button
                                resetForm();
                                resetToFirstFieldsetGlobal();
                                fieldset_notice.hide();
                                title.removeClass('error').text(MST_data.classrooms_setup_title);
                                subtitle.removeClass('error').text(MST_data.classrooms_setup_subtitle);
                                //subtitle.text(MST_data.student_being_created_message); // MST_data.classrooms_setup_subtitle
                                current_fs.find('.classrooms-list').fadeIn();
                                current_fs.find('.classrooms-notice').fadeIn();
                                current_fs.find('#confirm-classroom-selection').fadeIn();
                                fieldset_footer.find('#submit-create-student').fadeIn();
                                current_fs.find('input.previous').fadeOut();
                                current_fs.find('input.back').fadeOut();
                                current_fs.find('.new_student_link').fadeOut();
                                $("#create-new-student").hide();

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

                    title.addClass('error').text(MST_data.error_creating_classrrom);
                    subtitle.addClass('error').text(result.responseText);
                    fieldset_notice.text(MST_data.choose_different_student_name).removeClass('success').addClass('error');
                    current_fs.find('input.previous').fadeIn();
                    current_fs.find('input.back').fadeIn();
                    fieldset_notice.fadeIn();

                    fieldset_footer.find('.previous').on('click', function () {
                        fieldset_notice.hide();
                        title.removeClass('error').text(MST_data.classrooms_setup_title);
                        subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                        current_fs.find('.classrooms-list').fadeIn();
                        current_fs.find('.classrooms-notice').fadeIn();
                        current_fs.find('#confirm-classroom-selection').fadeIn();
                        fieldset_footer.find('#submit-create-student').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                    })
                    fieldset_footer.find('.back').on('click', function () {
                        resetToFirstFieldset();
                        fieldset_notice.hide();
                        title.removeClass('error').text(MST_data.classrooms_setup_title);
                        subtitle.removeClass('error').text(result.classrooms_setup_subtitle);
                        current_fs.find('.classrooms-list').fadeIn();
                        current_fs.find('.classrooms-notice').fadeIn();
                        current_fs.find('#confirm-classroom-selection').fadeIn();
                        fieldset_footer.find('#submit-create-student').fadeIn();
                        current_fs.find('input.previous').fadeOut();
                        current_fs.find('input.back').fadeOut();
                    })
                },
            });

        }
    });


});
