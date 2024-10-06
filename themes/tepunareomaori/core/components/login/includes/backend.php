<?php 

add_action('wp_ajax_get_dynamic_redirect_url', 'get_dynamic_redirect_url');
add_action('wp_ajax_nopriv_get_dynamic_redirect_url', 'get_dynamic_redirect_url');
add_action('wp_ajax_fingerprint_login_ajax', 'preloader_login_ajax_handler');
add_action('wp_ajax_nopriv_fingerprint_login_ajax', 'preloader_login_ajax_handler');


/**
 * @since V2
 *
 * Add an AJAX endpoint to get the dynamic redirect URL
 */ 

 function get_dynamic_redirect_url() {
    $user = wp_get_current_user();
    $redirect_url = redirect_after_login('', '', $user);
    wp_send_json_success(array('redirect_url' => $redirect_url));
}
/**
 * @since V2
 *
 * Handle the AJAX login callback 
 */ 

function preloader_login_ajax_handler() {

    // Get username and password from AJAX request.
    $username = isset($_POST['username']) ? sanitize_user( $_POST['username'] ) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    ob_start();
    include TPRM_THEME_PATH . 'template-parts/preloader.php';
    $template_content = ob_get_clean();
    
    // Authenticate user.
    $user = wp_authenticate($username, $password);

    if ( user_reached_limit( $user->ID ) ) {
        wp_send_json_error( 'Session limit reached');
    }

    // Check if authentication failed.
    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    }

    // Get user ID.
    $user_id = $user->ID;

    $user_data = get_userdata($user_id);

    if (!in_array('administrator', $user_data->roles)) {
        // Destroy other sessions.
        $manager = WP_Session_Tokens::get_instance($user_id);
        $manager->destroy_all();
    }

    // Log in the user.
    wp_set_auth_cookie($user_id);

    // Login success, send back the loading template
    wp_send_json_success(array('template' => $template_content));
}