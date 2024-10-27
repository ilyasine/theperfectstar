jQuery(document).ready(function ($) {

    // Attach a click event to the login button
    $(document).on('click', '#wp-submit', function (e) {

        var submitValue = $(this).val();

        // If the action is "lostpassword" Or update Password, don't run the custom logic
        if (submitValue === 'Request reset link' || submitValue === 'Save') {
            return; // Exit the function, allow normal behavior
        }

        e.preventDefault();
        // Disable the login button
        $(this).prop('disabled', true);

        // Get the username and password from the login form
        var username = $('#user_login').val();
        var password = $('#user_pass').val();

        // Use Ajax to perform the login
        $.ajax({
            type: 'POST',
            url: ajaxurl, // WordPress Ajax URL
            data: {
                action: 'fingerprint_login_ajax', // Custom Ajax action
                username: username, // Username field
                password: password // Password field
            },
            success: function (response) {
                //console.log('Response:', response);
                if (response && response.success) {
                    // Authentication was successful, hide the preloader
            
                    // Check if the template content is available in the response
                    if (response.data && response.data.template) {
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
                                
                        $(document).ready(function () {
                            // Fetch dynamic redirect URL
                            $.ajax({
                                type: 'GET',
                                url: ajaxurl,
                                data: {
                                    action: 'get_dynamic_redirect_url'
                                },
                                success: function (redirectResponse) {
                                    if (redirectResponse && redirectResponse.success && redirectResponse.data && redirectResponse.data.redirect_url) {
                                        // Redirect the user after the loader completes

                                        window.location.href = redirectResponse.data.redirect_url;
                                       
                              
                                    } else {
                                        console.error('Failed to fetch dynamic redirect URL.');
                                    }
                                },
                                error: function (error) {
                                   // console.error('Ajax error:', error);
                                }
                            });
                        });
                    } else {
                        console.error('Template content is missing in the response.');
                    }
                } else {
                    // Handle failed logins

                    if (response.data === 'Session limit reached') {
                        //console.log(response)
                        // If session limit is reached, trigger the popup
                        $('.login_session').magnificPopup({
                            type: 'inline',
                            fixedContentPos: true,
                            fixedBgPos: true,
                            closeBtnInside: true,
                            closeOnBgClick: false,
                            closeOnContentClick: false,
                            removalDelay: 300,
                            mainClass: 'mfp-move-horizontal',
                        }).click();
                    } else {
                        // Display Notice error
                        $('.login form').prepend('<div id="login_error" class="notice notice-error">' + response.data + '</div>');
                        // remove lost password link from the error message
                        $('.login form #login_error a').remove();
                        // Re-enable the login button
                        $('#wp-submit').prop('disabled', false);
                    }
                }
            },
            error: function (error) {
                // Handle Ajax errors
                console.error('Ajax error:', error);
            }
        });
    });


    $('#cancel-btn').on('click', function () { 
        $('#wp-submit').prop('disabled', false);
    });
    // Handle click on "Continue" button in the popup
    $('#continue-btn').on('click', function () {
        // Submit the login form programmatically
        // Get username and password
        var username = $('#user_login').val();
        var password = $('#user_pass').val();
        var security = $(this).data('security');
    
        // Perform AJAX login
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'destroy_sessions_and_login',
                username: username,
                password: password,
                security: security 
            },
            success: function (response) {
                // Handle login response
                //console.log(response)
                if (response && response.success) {
                    
                    // Check if the template content is available in the response
                    if (response.data && response.data.template) {
                        // Apply fadeIn effect to the template
                        $.magnificPopup.instance.close();
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
                                
                        $(document).ready(function () {
                            // Fetch dynamic redirect URL
                            $.ajax({
                                type: 'GET',
                                url: ajaxurl,
                                data: {
                                    action: 'get_dynamic_redirect_url'
                                },
                                success: function (redirectResponse) {
                                    if (redirectResponse && redirectResponse.success && redirectResponse.data && redirectResponse.data.redirect_url) {
                                        // Redirect the user after the loader completes

                                        window.location.href = redirectResponse.data.redirect_url;
                                        
                                
                                    } else {
                                        console.error('Failed to fetch dynamic redirect URL.');
                                    }
                                },
                                error: function (error) {
                                    // console.error('Ajax error:', error);
                                }
                            });
                        });
                    } else {
                        console.error('Template content is missing in the response.');
                    }
                } else {
                    $('.login form').prepend('<div id="login_error" class="notice notice-error">' + response.data + '</div>');
                    // remove lost password link from the error message
                    $('.login form #login_error a').remove();
                    // Re-enable the login button
                    $('#wp-submit').prop('disabled', false);  

                    $.magnificPopup.close();
                }
            },
            error: function (error) {
                // Handle AJAX errors
                console.error('Ajax error:', error);
            }
        });
    });

});
