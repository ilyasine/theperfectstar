jQuery(document).ready(function ($) {

    // hard reload
    $('#kwf-refresh').on("click", function () {

        $('.TPRM_refresh').magnificPopup({
            type: 'inline',
            fixedContentPos: true,
            fixedBgPos: true,
            closeBtnInside: true,
            closeOnBgClick: false,
            closeOnContentClick: false,
            removalDelay: 300,
            mainClass: 'mfp-move-horizontal',
        }).click();

    });

    if ($('.popup-troubleshooting-dismiss').length) {
        $('.popup-troubleshooting-dismiss').click(
            function (e) {
                e.preventDefault();
                $.magnificPopup.close();
            }
        );
    }

    /*     var loginDiv = jQuery('#loginform');
        var loginDivv = jQuery('.user-pass-wrap');
    
        loginDiv.prepend('<div class="login-tabs"><a href="#" class="login-tab active" data-target="login-tab-content">Login With Cred</a><a href="#" class="group-class-tab" data-target="group-class-tab-content">Picture Password</a></div>');
    
        // Create the custom content for the "Picture Password" tab
        jQuery('#group-class-tab-content').html(`
            <div class="picture-password-content">
                <input type="text" name="username" id="username" class="custom-input-field" style="height:55px; background-color:lightgray;color:black !important;border-radius:100px;" placeholder="Username">
                <div id="image-gallery" class="image-gallery"></div>
                <div id="group-class-message" class="group-class-message"></div>
                <input type="submit" value="Login" id="picture-password-submit" class="custom-submit-button" style="width:100%; height:55px;background-color: #00a79d !important;">
            </div>
        `);
    
        //jQuery('.privacy-policy-page-link').before('<div class="custom-login-content" style="margin-top:20px;"><center>Or<h3 style="margin-bottom:10px;">Group Class Code</h3></center><input type="text" name="classroom_code" id="classroom_code" class="custom-input-field" placeholder="Class Code"><input type="submit" value="Submit" id="classroom_code_submit" class="custom-submit-button" style="width:100%;"><div id="group-class-message" ></div></div>');
        jQuery('.custom-login-content').before(jQuery('#group-class-tab-content'));
        var styleElement = document.createElement('style');
        styleElement.innerHTML = '#classroom_code_submit:disabled {\
            color: #a7aaad !important;\
            background: #f6f7f7 !important;\
            border-color: #dcdcde !important;\
            box-shadow: none !important;\
            text-shadow: none !important;\
            cursor: default;\
        }';
        document.head.appendChild(styleElement); */

    function is_picture_password_mode() {
        return $('#login').hasClass('picture-mode');
    }

    var login_text_password = LC_data.login_text_password;
    var login_picture_password = LC_data.login_picture_password;
    var selectedPicture = '';

    // Create the tab structure and insert it after the form
    var tabsHTML = `
    <div id="login-tabs">
        <ul>
            <li id="text-password-tab"><a href="#text-password" class="active">${login_text_password}</a></li>
            <li id="picture-password-tab"><a href="#picture-password">${login_picture_password}</a></li>
        </ul>
    </div>
    <div id="picture-password-fields" style="display: none;">
        <div class="picture_password" style="max-height: 300px; overflow-y: scroll; overflow-x: hidden; padding: 20px; padding-right: 10px; margin-bottom: 16px;">
            <div class="kwf-preloader-login" style="display: none;">
                 ${preloader}
            </div>
            <div class="images-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;"></div>
            <div id="group-class-message" class="group-class-message"></div>
        </div>
    </div>`;

    var originalSubmitButton = $('#wp-submit');
    var originalUsernameField = $('#loginform').find('p').first().clone();

    // Function to replace submit button
    function replaceSubmitButton(newButton) {
        $('#wp-submit, #picture-submit').remove();
        $('p.submit').prepend(newButton);
    }

    // Insert the tabs just after the form
    $('#loginform').before(tabsHTML);

    $('p.submit').find('input');

    // Initially hide the picture password fields
    $('#picture-password-fields').hide();

    // Hide the active tab's <li> initially
    $('#text-password-tab').hide(); // Since "Login With Credentials" is the default active

    // Handle tab clicks
    $('#login-tabs ul li a').click(function (e) {
        e.preventDefault();

        $('.notice-error').hide();

        // Show all tabs first
        $('#login-tabs ul li').show();

        // Remove 'active' class from all tabs
        $('#login-tabs ul li a').removeClass('active');

        // Add 'active' class to the clicked tab
        $(this).addClass('active');

        // Hide the <li> of the newly active tab
        $(this).closest('li').hide();

        // Toggle between the text password fields and picture password fields
        var selectedTab = $(this).attr('href');
        var submitButton = $('p.submit').find('input[type="submit"]');

        if (selectedTab === "#text-password") {
            // Text Password Mode
            $('#loginform p:not(".submit, .forgetmenot"), #loginform .user-pass-wrap').show();
            $('#picture-password-fields').hide();
            $('#login').removeClass('picture-mode');
            $('.login form#loginform').find('#login_error.notice.picture-mode');
            replaceSubmitButton(originalSubmitButton.clone());
        } else {
            // Picture Password Mode
            $('#loginform .user-pass-wrap').hide();
            $('#loginform .user-pass-wrap').before($('#picture-password-fields'));
            /* $('#loginform p:not(".submit, .forgetmenot"), #loginform .user-pass-wrap').hide(); */
            $('#picture-password-fields').show();
            $('#login').addClass('picture-mode');
            // Create and append the picture submit button
            var pictureSubmitButton = $('<button>', {
                id: 'picture-submit',
                type: 'button',
                class: 'button button-primary button-large',
                text: LC_data.login_btn_label,
                security: LC_data.picture_password_login_nonce,
            });

            // Check if the #login_error element with the class .picture-mode exists in the form
            if ($('#login_error.notice.picture-mode').length === 0) {
                // If it doesn't exist, prepend it to the form
                $('.login form#loginform').prepend('<div id="login_error" class="notice picture-mode notice-error"></div>');
            }

            // Hide the element (whether it was newly added or already existed)
            $('#login_error.notice.picture-mode').hide();

            replaceSubmitButton(pictureSubmitButton);
        }

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

        console.log('selectedPicture : ', selectedPicture); // Log the selected picture URL (if any)
    });


    // Handle the Picture Password tab click
    $('#picture-password-tab').click(function (event) {
        event.preventDefault();
        $('#user_pass').val('');
        // Load images via AJAX when the tab is activated
        if (!$('.images-grid .picture-container img').length) {
            $('#picture-password-fields .picture_password .kwf-preloader-login').show();
            $('#picture-password-fields .picture_password').css('overflow', 'hidden');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'fetch_all_images',
                    //security: security,
                },
                dataType: 'json',
                success: function (response) {
                    if (response.html) {
                        $('.images-grid').html(response.html);
                        $('#picture-password-fields .picture_password .kwf-preloader-login').fadeOut();
                        $('#picture-password-fields .picture_password').css('overflow', 'auto');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }
    });

    /* $(document).on('click', '#picture-submit', function(event) {
        if (is_picture_password_mode()) {  
            event.preventDefault(); // Prevent normal form submission
            $('#login_error').hide();
            var username = $('#user_login').val();
            var security = $(this).attr('security');

            console.log(username);
            console.log(security);

            if (username === '') {           
                $('#login_error.notice.picture-mode').show();
                $('#login_error.notice.picture-mode').html(LC_data.username_empty);
                return;
            }

            if (!selectedPicture) {
                $('#login_error.notice.picture-mode').show();
                $('#login_error.notice.picture-mode').html(LC_data.picture_password_empty);
                return;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'validate_picture_password',
                    security: security,
                    username: username,
                    selectedPicture: selectedPicture
                },
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    NProgress.done();
                    if (response.success) {
                        window.location.href = response.redirect_url;

                    } else {
                        $('#login_error.notice.picture-mode').show();
                        $('#login_error.notice.picture-mode').html(response.data.message);
                        messageDiv.append("<p style='color:red;margin-top:10px 0px;'>" + response.message + "</p>");
                        if (response.message.includes('Maximum attempts reached')) {
                            // Disable image selection
                            $('#image-gallery .gallery-image').off('click');
                            $('#picture-password-submit').prop('disabled', true);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    $('#login_error.notice.picture-mode').show();
                    $('#login_error.notice.picture-mode').html(error);
                }
            });
        }
        
    }); */

    $(document).on('click', '#picture-submit', function (event) {
        if (is_picture_password_mode()) {
            event.preventDefault(); // Prevent normal form submission
            $('#login_error').hide();
            $('.notice-error').hide();
            var username = $('#user_login').val();
            var security = $(this).attr('security');

            if (username === '') {
                $('#login_error.notice.picture-mode').show();
                $('#login_error.notice.picture-mode').html(LC_data.username_empty);
                return;
            }

            if (!selectedPicture) {
                $('#login_error.notice.picture-mode').show();
                $('#login_error.notice.picture-mode').html(LC_data.picture_password_empty);
                return;
            }

            $(this).prop('disabled', true);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'validate_picture_password',
                    security: security,
                    username: username,
                    selectedPicture: selectedPicture
                },
                dataType: 'json',
                success: function (response) {
                    console.log(response)

                    if (response.success) {
                        // Apply fadeIn effect to the template

                        var template = $(response.data.template);
                        template.hide().fadeIn();

                        // Prepend the content of the preloader to the body
                        $('body.login').prepend(template);

                        // Start and finish NProgress when the document is ready
                        NProgress.start();
                        NProgress.set(0.4);

                        var interval = setInterval(function () {
                            NProgress.inc();
                        }, 1000);
                        clearInterval(interval);
                        setTimeout(function () {
                            window.location.href = response.data.redirect_url;
                        }, 2000);
                    } else {
                        $('#login_error.notice.picture-mode').hide();
                        $('#picture-submit').prop('disabled', false);
                        $('#login_error.notice.picture-mode').show();
                        $('#login_error.notice.picture-mode').html(response.data.message);
                    }


                },
                error: function (xhr, status, error) {
                    console.error(error);

                    $('#picture-submit').prop('disabled', false);
                }
            });
        }
    });


    $('#classroom_code_submit').click(function (event) {

        event.preventDefault();
        var classCodeValue = $('#classroom_code').val();
        var security = $(this).data('security');
        if (classCodeValue == '') {
            // classroom-code-notice
            $('#login_error.notice.classroom-code-notice').show();
            $('#login_error.notice.classroom-code-notice').html(LC_data.empty_classroom_code);
            return;
        }
        $("#classroom_code_submit").prop('disabled', true);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'check_classroom_code',
                security: security,
                classCodeValue: classCodeValue,
            },
            dataType: 'json',
            success: function (response) {
                if (response.success == true) {
                    var template = $(response.template);
                    template.hide().fadeIn();
                    $('body.login').prepend(template);
                    NProgress.start();
                    NProgress.set(0.4);

                    var interval = setInterval(function () {
                        NProgress.inc();
                    }, 1000);
                    clearInterval(interval);
                    console.log(response.group_account_access_url);
                    var url = window.location.origin + '/group-account-access/?classroom_code=' + encodeURIComponent(classCodeValue);
                    url = response.group_account_access_url;
                    $(document).ready(function () {
                        window.location.href = url;
                        console.log(url);
                    })
                } else {
                    $('#login_error.notice.classroom-code-notice').html(response.message);
                    $("#classroom_code_submit").prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

});


