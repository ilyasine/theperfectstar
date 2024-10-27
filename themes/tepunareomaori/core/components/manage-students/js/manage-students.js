/* manage student */

jQuery(document).ready(function ($) {

    //View Picture Password
    $('.password-toggle #view-picture-password').on('click', function () {
        var $button = $(this);
        var $img = $button.siblings('.picture-password');

        if ($img.length) {
            // Get the current image source and the data attribute for the password image
            var currentSrc = $img.attr('src');
            var passwordSrc = $img.data('password-src');
            var placeholderSrc = $img.data('placeholder-src') || $img.attr('src');

            // Toggle the image source based on the current source
            if (currentSrc === placeholderSrc) {
                $img.attr('src', passwordSrc);
                $button.find('.button-text').text('Click to hide the password');
            } else {
                $img.attr('src', placeholderSrc);
                $button.find('.button-text').text('Click to view the password');
            }
        }
    });

    //Edit student 
    $(".edit-student-details-content-body").tabs();

    //Delete student
    $(document).on('click', '.student_actions .delete-student', function (e) {
        e.preventDefault();

        var $this = $(this);
        var student_id = $this.data('student_id');
        var student_suspend_container = $('#student-delete-content-' + student_id);

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

                    $(student_suspend_container).on('click', '.confirm_delete_student', function (e) {
                        e.preventDefault();
                        var button = $(this);
                        var bodyparagraphs = $(student_suspend_container).find('.student-delete-content-body').find('p');
                        var title = $(student_suspend_container).find('.student-delete-content-title').find('.title-text');
                        var security = button.data('security');
                        var student_name = button.data('student_name');
                        var school_id = button.data('school_id');
                        var classroom_id = button.data('classroom_id');
                        var original_title = title.text();
                        var footer_container = $(student_suspend_container).find('.student-delete-content-footer');

                        if (student_id) {
                            //process suspend
                            $('.tprm-preloader').show();
                            bodyparagraphs.hide();
                            $(title).text(MST_data.student_being_suspended_message);
                            footer_container.find('button').addClass('disabled');
                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'suspend_student',
                                    security: security,
                                    payload: 'suspend_student_payload',
                                    student_id: student_id,
                                    classroom_id: classroom_id,
                                    school_id: school_id,
                                    student_name: student_name,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result, textstatus) {
                                    if (result) {
                                        console.log(result);
                                        $('.tprm-preloader').hide();
                                        footer_container.find('button').removeClass('disabled');
                                        bodyparagraphs.show();
                                        $(title).text(result.data.message);

                                        setTimeout(() => {
                                            $.magnificPopup.close();
                                        }, 1000);

                                        setTimeout(() => {
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
                                        }, 2000);

                                        setTimeout(() => {
                                            window.location.reload();
                                            $(title).text(original_title);
                                        }, 3000);

                                    }
                                },
                                error: function (result) {
                                    $('.tprm-preloader').hide();
                                    bodyparagraphs.show();
                                    $(title).text(original_title);
                                    footer_container.find('button').removeClass('disabled');
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
                            console.log(result)
                            /* $(document).trigger(
                                'bb_trigger_toast_message',
                                [
                                    '',
                                    '<div>' + result.data.message +  '</div>',
                                    'error',
                                    null,
                                    true,
                                ]
                            ); */
                        }
                    });

                    $('#cancel_delete_student').on('click', function (e) {
                        e.preventDefault();
                        $.magnificPopup.close();
                    })
                },
            }
        });

    });

    //Edit student
    $(document).on('click', '.student_actions .edit-student-details-btn', function (e) {
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

                    $('.confirm_edit_student').on('click', function (e) {
                        e.preventDefault();

                        var button = $(this);
                        var security = button.data('security');
                        var student_id = button.data('student_id');
                        var student_name = button.data('student_name');

                        var first_name = $('#first-name-' + student_id).val();
                        var last_name = $('#last-name-' + student_id).val();
                        var email = $('#email-' + student_id).val();
                        var password = $('#password-' + student_id).val();
                        var picture_password_url = $('#picture-password-url-' + student_id).val(); // Fetch picture password URL

                        if (student_id) {
                            NProgress.start();
                            NProgress.set(0.4);

                            var interval = setInterval(function () {
                                NProgress.inc();
                            }, 1000);
                            clearInterval(interval);

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'edit_student',
                                    security: security,
                                    student_id: student_id,
                                    student_name: student_name,
                                    first_name: first_name,
                                    last_name: last_name,
                                    email: email,
                                    password: password,
                                    picture_password_url: picture_password_url,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result) {
                                    if (result.success) {
                                        $(document).trigger(
                                            'bb_trigger_toast_message',
                                            ['', '<div>' + result.data.message + '</div>', 'success', null, true]
                                        );

                                        NProgress.done();
                                        $.magnificPopup.close();

                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 2000);

                                    } else {
                                        $(document).trigger(
                                            'bb_trigger_toast_message',
                                            ['', '<div>' + result.data.message + '</div>', 'error', null, true]
                                        );
                                    }
                                },
                                error: function (result) {
                                    $(document).trigger(
                                        'bb_trigger_toast_message',
                                        ['', '<div>' + result.responseJSON.data.message + '</div>', 'error', null, true]
                                    );
                                },
                            });
                        } else {
                            console.log('No student ID provided');
                        }
                    });

                    // Function to fetch all images for the picture password gallery
                    // Fetch all images function
                    function fetchAllImages() {

                        $.ajax({
                            url: ajaxurl,
                            data: {
                                action: 'fetch_all_images'
                            },
                            type: 'get',
                            dataType: 'json',
                            success: function (response) {
                                if (response.html) {
                                    $('#image-gallery').html(response.html);
                                }
                            },
                            error: function () {
                                console.error('Failed to load images');
                            }
                        });
                    }

                    fetchAllImages();

                    // Event listener for picture password selection
                    $('.images-grid').on('click', '.picture-container', function () {
                        var $container = $(this);

                        if ($container.hasClass('selected')) {




                            var $img = $container.find('img');
                            var imgSrc = $img.attr('src');
                            // Find the correct hidden input field by using the data-student-id attribute
                            var studentId = $('input.picture-password-url').data('student-id');


                            $('#picture-password-url-' + studentId).val(imgSrc); // Set hidden input with selected image URL

                        }
                    });



                    $('input[name="password_type"]').change(function () {
                        if ($('#picture-password-radio').is(':checked')) {
                            $('#text-password-section').hide();
                            $('#picture-password-section').show();
                            // Update password type to 'picture' in the backend (AJAX can be added here)
                        } else {
                            $('#text-password-section').show();
                            $('#picture-password-section').hide();
                            // Update password type to 'text' in the backend (AJAX can be added here)
                        }
                    });

                    // Load current state on page load based on the checked radio button
                    if ($('#picture-password-radio').is(':checked')) {
                        $('#text-password-section').hide();
                        $('#picture-password-section').show();
                    } else {
                        $('#text-password-section').show();
                        $('#picture-password-section').hide();
                    }


                    $('#cancel_edit_student').on('click', function (e) {
                        e.preventDefault();
                        $.magnificPopup.close();
                        NProgress.done();
                    })
                },
            }
        });

    });


    $('.images-grid').on({
        mouseover: function () {
            $(this).css({
                'filter': 'brightness(1.1)',
                'transform': 'scale(1.1)',
                'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)',
                'transition': '.3s ease-in-out'
            });
        },
        mouseout: function () {
            if (!$(this).closest('.picture-container').hasClass('selected')) {
                $(this).css({
                    'filter': 'brightness(0.9)',
                    'transform': 'scale(1)',
                    'box-shadow': 'none'
                });
            }
        }
    }, '.picture-container img');

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

        // console.log('selectedPicture : ', selectedPicture); // Log the selected picture URL (if any)
    });
    // Handle initial loading state for previous siblings
    $('.manage-student-classrooms').find('tr.classroom.selected').each(function () {
        $(this).prev('tr.classroom').addClass('previous-selected');
    });

    // Manage student classrooms
    $(document).on('click', '.student_actions .manage-student-classrooms-btn', function (e) {
        e.preventDefault();

        var $this = $(this);
        var student_id = $this.data('student_id');

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
                    var initialSelectedClassroom = null;  // Track only one selected classroom
                    var studentClassroom = null;  // Track only one selected classroom
                    var $table = $('#manage-student-classrooms-' + student_id);

                    // Store the initial state of the selected classroom
                    initialSelectedClassroom = $table.find('tr.classroom.selected').attr('id');
                    studentClassroom = $table.find('tr.classroom').data('student-classroom');
                    classroomTable = $table.find('.school-classroom-table');


                    $table.on('click', '.toggle-classroom-student', function () {
                        var $btn = $(this);
                        var $checkbox = $btn.find('input.cb-value');
                        var $parentRow = $btn.closest('tr.classroom');
                        var isActive = $btn.hasClass('active');

                        // Deselect all other classrooms
                        $table.find('.toggle-classroom-student.active').removeClass('active').find('input.cb-value').prop('checked', false);
                        $table.find('tr.classroom.selected').removeClass('selected');

                        /*  if ($parentRow.attr('id') === studentClassroom) {
                             $btn.addClass('active');
                             $parentRow.addClass('selected');
                         } */

                        // Select the clicked classroom if it was not already active
                        if (!isActive) {
                            $btn.addClass('active');
                            $checkbox.prop('checked', true);
                            $parentRow.addClass('selected');
                        } else {
                            $parentRow.removeClass('selected');
                        }

                    });

                    // Confirm classroom selection
                    $('.confirm_student_classrooms').off('click').on('click', function (e) {
                        e.preventDefault();

                        var finalSelectedClassroom = null;
                        var AddedClassroom = null;
                        var RemovedClassroom = null;

                        // Get the final state of the selected classroom
                        finalSelectedClassroom = $table.find('tr.classroom.selected').attr('id');

                        // Determine if a classroom was added or removed
                        if (finalSelectedClassroom && finalSelectedClassroom !== initialSelectedClassroom) {
                            AddedClassroom = finalSelectedClassroom;
                            RemovedClassroom = initialSelectedClassroom;
                        } else if (!finalSelectedClassroom && initialSelectedClassroom) {
                            RemovedClassroom = initialSelectedClassroom;
                        }

                        // Additional AJAX or operations can be handled here
                        var button = $(this);
                        var security = button.data('security');
                        var student_id = button.data('student_id');
                        var student_name = button.data('student_name');
                        var title = $table.find('.manage-student-classrooms-content-title').find('.title-text');
                        var original_title = title.text();
                        var footer_container = $table.find('.manage-student-classrooms-content-footer');

                        /*console.log(" initialSelectedClassroom: ", initialSelectedClassroom);
                        console.log("Added Classroom: ", AddedClassroom);
                        console.log("Removed Classroom: ", RemovedClassroom);
                        console.log("student_id: ", student_id);
                        console.log("student_name: ", student_name);*/

                        if (student_id) {
                            NProgress.start();
                            NProgress.set(0.4);

                            $('.tprm-preloader').show();
                            classroomTable.css('visibility', 'collapse');
                            $(title).text(MST_data.updating_student_classrooms_message);
                            footer_container.find('button').addClass('disabled');

                            var interval = setInterval(function () {
                                NProgress.inc();
                            }, 1000);
                            clearInterval(interval);

                            $.ajax({
                                url: ajaxurl,
                                data: {
                                    action: 'manage_student_classrooms',
                                    security: security,
                                    payload: 'manage_student_classrooms_payload',
                                    student_id: student_id,
                                    student_name: student_name,
                                    AddedClassroom: AddedClassroom,
                                    RemovedClassroom: RemovedClassroom,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (result, textstatus) {
                                    if (result) {
                                        $('.tprm-preloader').hide();
                                        classroomTable.css('visibility', 'visible');

                                        setTimeout(() => {
                                            $.magnificPopup.close();
                                        }, 1000);

                                        $(document).trigger(
                                            'bb_trigger_toast_message',
                                            [
                                                '',
                                                '<div>' + result.student_classrooms_updated_success_message + '</div>',
                                                'success',
                                                null,
                                                true,
                                            ]
                                        );

                                        NProgress.done();

                                        setTimeout(() => {
                                            window.location.reload();
                                            $(title).text(original_title);
                                        }, 4000);
                                    }
                                },
                                error: function (result) {
                                    console.log(result);
                                    $('.tprm-preloader').hide();
                                    classroomTable.css('visibility', 'visible');
                                    $(title).text(original_title);
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
                            $('.tprm-preloader').hide();
                            classroomTable.css('visibility', 'visible');
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

                    // Cancel classroom selection
                    $table.find('#cancel_student_classrooms').off('click').on('click', function (e) {
                        e.preventDefault();
                        $table.find('.toggle-classroom-student').each(function () {
                            var $btn = $(this);
                            var $tr = $btn.closest('tr.classroom');
                            /* console.log(" $tr.attr('id') ", $tr.attr('id'));
                            console.log("initialSelectedClassroom ", initialSelectedClassroom);
                            console.log("initialSelectedClassroom ", initialSelectedClassroom);
                            console.log('studentClassroom : ', studentClassroom); */
                            /*  if ($tr.attr('id') === initialSelectedClassroom || $tr.attr('id') === studentClassroom) {
                                 $btn.addClass('active');
                                 $tr.addClass('selected');
                             } else {
                                 $btn.removeClass('active');
                                 $tr.removeClass('selected');
                             } */
                        });
                        $.magnificPopup.close();
                        NProgress.done();
                    });
                },
            }
        });
    });

    //Activate

    $('.license-paste i').on('click', function (e) {
        // Check if the Clipboard API is supported in the browser
        var $input = $(this).siblings('input.licenseinpt');
        if (navigator.clipboard) {
            navigator.clipboard.readText()
                .then(clipboardText => {
                    // Paste the clipboard content into the input field
                    $input.val(clipboardText);
                })
                .catch(error => {
                    console.error('Failed to read clipboard contents: ', error);
                });
        } else {
            // If Clipboard API is not supported, fallback to the traditional method
            $input.select();
            document.execCommand('paste');
        }
    });

    //Activate the account
    $('form.kwfFormSCLicense').on('submit', function (e) {
        e.preventDefault();
        let lic_activate = $(this).find('[type="submit"]').val();

        let code = $(this).find('.licenseinpt').val();
        if (code == '' || code.length <= 0) {
            $(this).find('[type="submit"]').val(lic_activate);
            return;
        }
        var submitButton = $(this).find('.licensebtn');
        submitButton.addClass('loading');
        let prID = $(this).find('[name="prId"]').val();
        let uID = $(this).find('[name="uId"]').val();
        var security = $(this).data('security');
        let formThis = this;
        NProgress.start();
        NProgress.set(0.4);
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                code: code,
                prid: prID,
                user_id: uID,
                action: 'license_buy',
                security: security,
                is_students_page: true,
            },
            success: function (response) {
                submitButton.removeClass('loading');
                if (!response.success) {
                    NProgress.done();
                    $(formThis).find('[type="submit"]').val(lic_activate);
                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + response.data.msg + '</div>',
                            'error',
                            null,
                            true,
                        ]
                    );
                    return;
                }
                if (response.success) {
                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + response.data.msg + '</div>',
                            'success',
                            null,
                            true,
                        ]
                    );
                    $(formThis).closest('.column-tprm-student-status-tprm-inactive').replaceWith(
                        `<div class="column-tprm-student-status-tprm-active" id="tprm-active">
                            <a class="activated">
                              <span>                                                        
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" id="check">
                                  <path id="test" d="M4.76499011,6.7673683 L8.2641848,3.26100386 C8.61147835,2.91299871 9.15190114,2.91299871 9.49919469,3.26100386 
                                  C9.51164115,3.27347582 9.52370806,3.28637357 9.53537662,3.29967699 C9.83511755,3.64141434 9.81891834,4.17816549 
                                  9.49919469,4.49854425 L5.18121271,8.82537365 C4.94885368,9.05820878 4.58112654,9.05820878 4.34876751,8.82537365 
                                  L2.50080531,6.97362503 C2.48835885,6.96115307 2.47629194,6.94825532 2.46462338,6.93495189 
                                  C2.16488245,6.59321455 2.18108166,6.0564634 2.50080531,5.73608464 C2.84809886,5.3880795 3.38852165,5.3880795 
                                  3.7358152,5.73608464 L4.76499011,6.7673683 Z"></path>
                                </svg>                                                    
                              </span>
                              <ul>
                                <li>${MST_data.active}</li>
                              </ul>
                            </a>
                        </div>`
                    );
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
                $(formThis).find('[type="submit"]').val(lic_activate);
                submitButton.removeClass('loading');
            }
        });
    });

    //Activate Paid School
    $('.activate').on('click touch', function (e) {
        var self = $(this);
        if (self.attr('disabled')) return; // Prevent multiple clicks on the same button
        var studentID = self.data('studentid');
        var schoolID = self.data('schoolid');
        var security = self.data('security');
        var school_seats_stats = $('.account-stats-container').find('#school_seats h2');
        var active_count_stats = $('.account-stats-container').find('#active_count h2');
        var inactive_count_stats = $('.account-stats-container').find('#inactive_count h2');
        var total_count_stats = $('.account-stats-container').find('#total_count h2');
        var activation_rate_stats = $('.account-stats-container').find('#activation_rate h2 span');
        var school_seats_stats_val = $(school_seats_stats).text();
        var active_count_stats_val = $(active_count_stats).text();
        var inactive_count_stats_val = $(inactive_count_stats).text();
        var total_count_stats_val = $(total_count_stats).text();

        if (!self.hasClass('loading') && studentID && schoolID && security) {
            $('.activate').attr('disabled', true);
            self.addClass('loading').attr('disabled', true);
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'activate_student',
                    security: security,
                    payload: 'activate_student_payload',
                    schoolID: schoolID,
                    studentID: studentID,
                },
                type: 'post',
                dataType: 'json',
                success: function (response, textstatus) {
                    //console.log(response);
                    self.addClass('done');
                    if (response.success) {
                        var new_active_count_stats_val = parseInt(active_count_stats_val) + 1;
                        var new_activation_rate_stats_val = parseFloat((new_active_count_stats_val / total_count_stats_val) * 100).toFixed(2);
                        active_count_stats.text(new_active_count_stats_val);
                        inactive_count_stats.text(parseInt(inactive_count_stats_val) - 1);
                        school_seats_stats.text(parseInt(school_seats_stats_val) - 1);
                        activation_rate_stats.text(new_activation_rate_stats_val);
                        $(document).trigger(
                            'bb_trigger_toast_message',
                            [
                                '',
                                '<div>' + response.data.student_activated_success_message + '</div>',
                                'success',
                                null,
                                true,
                            ]
                        );
                        //Student Activated, Re-enable all activate buttons except the activated one
                        $('.activate').not(self).attr('disabled', false);
                    } else {
                        self.removeClass('loading done');
                        $(document).trigger(
                            'bb_trigger_toast_message',
                            [
                                '',
                                '<div>' + response.data.msg + '</div>',
                                'error',
                                null,
                                true,
                            ]
                        );
                        $('.activate').attr('disabled', false);
                    }
                    // Re-enable all activate buttons


                },
                error: function (response) {
                    self.removeClass('loading done');
                    $(document).trigger(
                        'bb_trigger_toast_message',
                        [
                            '',
                            '<div>' + response.data.student_activation_error_message + '</div>',
                            'error',
                            null,
                            true,
                        ]
                    );
                    // Re-enable all activate buttons
                    $('.activate').attr('disabled', false);
                },

            });
        }
    });


});
