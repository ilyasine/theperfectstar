<?php 

/* limit active logins  */

/* function user_reached_limit( $user_id ) {
    // If bypassed.
    //if ( $user_id == '2023' ) {
        //return false;
    //}
    $user = get_userdata( $user_id );
    if ( in_array( 'administrator', $user->roles ) ) {
        return false;
    }
    // Sessions token instance.
    $manager = WP_Session_Tokens::get_instance( $user_id );
    // Count sessions.
    $count = count( $manager->get_all() );
    // Check if sessions are more than one.
    $reached = $count >= 1;

    return $reached;
}
 */

 function user_reached_limit( $user_id ) {
    // Get user data
    $user = get_userdata( $user_id );

    // Check if user data is valid and has roles
    if ( $user && isset( $user->roles ) ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            return false;
        }
    } else {
        // Handle the case where user data is not found or roles are not set
        return false; // or handle it as per your logic
    }

    // Sessions token instance.
    $manager = WP_Session_Tokens::get_instance( $user_id );

    // Count sessions.
    $count = count( $manager->get_all() );

    // Check if sessions are more than one.
    $reached = $count >= 1;

    return $reached;
}


add_action('wp_ajax_destroy_sessions_and_login', 'destroy_sessions_and_login_callback');
add_action('wp_ajax_nopriv_destroy_sessions_and_login', 'destroy_sessions_and_login_callback');

function destroy_sessions_and_login_callback() {
    // Security check.
    check_ajax_referer('destroy_sessions_and_login', 'security');

    // Get username and password from AJAX request.
    $username = isset($_POST['username']) ? sanitize_user( $_POST['username'] ) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    ob_start();
    include TPRM_THEME_PATH . 'template-parts/preloader.php';
    $template_content = ob_get_clean();
    
    // Authenticate user.
    $user = wp_authenticate($username, $password);

    // Check if authentication failed.
    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    }

    // Get user ID.
    $user_id = $user->ID;

    // Get user data.
    $user_data = get_userdata($user_id);

    // Check if user is an administrator or tprm-admin.
    if (!in_array('administrator', $user_data->roles)) {
        // Destroy other sessions.
        $manager = WP_Session_Tokens::get_instance($user_id);
        $manager->destroy_all();
    }

    // Log in the user.
    wp_set_auth_cookie($user_id);

    // Login success, send back the loading template.
    wp_send_json_success(array('template' => $template_content));
}


add_filter('bp_core_change_privacy_policy_link_on_private_network', 'display_login_session_page', 10, 2);

 /**
 * Display login session page
 *
 * @since V2
 */

 function display_login_session_page($link, $privacy_policy_url) {

    $TPRM_ajax_nonce = wp_create_nonce( "destroy_sessions_and_login" );

    $html_template = '
    <a class="login_session popup-modal-login popup-first" style="display:none;" href="%s">%s</a>
    <div id="login_session" class="mfp-hide login-popup bb-modal">
        <h1 class="alert-title">%s</h1>
        <div class="login-session-content">
            <div class="header-container">
                <span class="bb-icon-l bb-icon-exclamation"></span>
                <span class="bb-icon-l bb-icon-desktop"></span>
            </div>
            <p>%s</p>
            <p>%s</p>
            <p>%s</p>
            <div class="button-container">
                <button type="button" class="button mfp-close" id="cancel-btn">%s</button>
                <button type="button" data-security="' . esc_attr($TPRM_ajax_nonce) . '" class="button" id="continue-btn">%s</button>
            </div>
        </div>
    </div>';

    $href = '#login_session';
    $link_text = __('Your account is logged in on another device.', 'tprm-theme');
    $alert_title = __('Your account is logged in on another device.', 'tprm-theme');
    $popup_content_1 = __('Uh oh! Looks like someone else is already using this account from another device.', 'tprm-theme');
    $popup_content_2 = __('If you continue working on this device, your account will be logged out from other devices.', 'tprm-theme');
    $popup_content_3 = __('Do you want to continue working on this device?', 'tprm-theme');
    $cancel_button_text = __('Cancel', 'tprm-theme');
    $continue_button_text = __('Continue', 'tprm-theme');

    $login_session_output = sprintf(
        $html_template,
        $href,
        $link_text,
        $alert_title,
        $popup_content_1,
        $popup_content_2,
        $popup_content_3,
        $cancel_button_text,
        $continue_button_text
    );

    $link .= $login_session_output;   

    return $link;
}