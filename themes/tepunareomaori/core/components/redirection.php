<?php 

add_action('template_redirect', 'TPRM_subscription_redirect');
add_action('shutdown', 'TPRM_reset_wpml_capabilities' );
add_action('wp_logout', 'TPRM_homepage_logout_redirect');

/**
 * 
 * @global function check if we are in a public page
 * 
 * @since V2
 */

 function TPRM_is_public() {
	$public_pages = array(
		'contact',
		'te-reo-tuatahi',
		'organisations',
		'schools-explore',
		'about',
		'privacy-policy',
		'schools',
		'ece',	
        'esol',
        'terms-of-service',
        'group-account-access'
	);
    /* 
    https://anita.local
    https://anita.local/about
    https://anita.local/cart
    https://anita.local/checkout
    https://anita.local/contact
    https://anita.local/privacy-policy/
    https://anita.local/schools
    https://anita.local/ece
    https://anita.local/esol/
    https://anita.local/terms-of-service
    /te-reo-tuatahi/
    /product/
    /product-category/
    https://anita.local/product/schools-sign-up/
    https://anita.local/schools-explore/
    https://anita.local/organisations/
    https://anita.local/?ff_landing=43
    https://anita.local/?ff_landing=41
    */

	if ( is_front_page() || is_page( $public_pages ) ) {
		return true;
	}

	return false;
}


/**
 * 
 * @global function check if we are in subscription page
 * 
 * @since V2
 */

 function is_subscription(){

	if ( is_page('subscription') )	{  
		return true; 
	}
	return false;
 }


/**
 * 
 * @global function check if we are in the reporting page
 * 
 * @since V2
 */

function is_reporting(){

	if ( is_page('reports') || is_page('resultats') )	{  
		return true; 
	}
	return false;
}

/**
 * 
 * @global function check if we are in the library page
 * 
 * @since V3
 */

function is_library_page(){

	if ( is_page('library') )	{  
		return true; 
	}
	return false;
}


/**
 * 
 * @global function check if we are in the library page
 * 
 * @since V3
 */

function is_library_manage_page(){

	if ( is_page('manage-libraries') || is_page('libraries-sales_overview')  )	{  
		return true; 
	}
	return false;
}
/**
 * 
 * @global function check if we are in the library page
 * 
 * @since V3
 */

function is_library_dashboard_page(){

	if ( is_page('libraries-dashboard') )	{  
		return true; 
	}
	return false;
}

/**
 * 
 * @global function check if the current page is a students page
 * 
 * @since V3
 */

function is_students_page(){
    if( function_exists('bp_is_group') && bp_is_group() && strpos($_SERVER['REQUEST_URI'], "students") !== false ){  
		return true; 
	}
	return false; 
}

/**
 * 
 * @global function check if the current page is a teachers page
 * 
 * @since V3
 */

function is_teachers_page(){
    if( function_exists('bp_is_group') && bp_is_group() && strpos($_SERVER['REQUEST_URI'], "teachers") !== false ){  
		return true; 
	}
	return false; 
}

/**
 * 
 * @global function check if the current page is a courses page
 * 
 * @since V3
 */

function is_global_courses_page(){ 
    if( function_exists('bp_is_my_profile') && bp_is_my_profile() && 
       ( function_exists('bb_learndash_profile_courses_slug') && strpos($_SERVER['REQUEST_URI'], bb_learndash_profile_courses_slug() ) !== false
        || strpos($_SERVER['REQUEST_URI'], 'additional-content' ) !== false  
        || strpos($_SERVER['REQUEST_URI'], 'exam' ) !== false ) ){  
		return true; 
	}
	return false; 
}
/**
 * 
 * @global function check if the current page is a courses page
 * 
 * @since V3
 */

function is_courses_page(){ 
    if( function_exists('bp_is_my_profile') && bp_is_my_profile() && 
        function_exists('bb_learndash_profile_courses_slug') && strpos($_SERVER['REQUEST_URI'], bb_learndash_profile_courses_slug() ) !== false ){  
		return true; 
	}
	return false; 
}

/**
 * 
 * @global function check if the current page is student additional content page
 * 
 * @since V3
 */

function is_additional_content_page(){ 
    if( function_exists('bp_is_my_profile') && bp_is_my_profile() &&       
        strpos($_SERVER['REQUEST_URI'], 'additional-content' ) !== false  ){  
        return true;
    }
    return false; 
}
/**
 * 
 * @global function check if the current page is the student exam page
 * 
 * @since V3
 */

function is_exam_page(){ 
    if( function_exists('bp_is_my_profile') && bp_is_my_profile() &&       
      strpos($_SERVER['REQUEST_URI'], 'exam' ) !== false  ){  
		return true; 
	}
	return false; 
}

/**
 * 
 * @global function check if the current page is a is the onboarding course page for teacher
 * 
 * @since V3
 */

function is_onboarding_course(){ 
    if( is_singular('sfwd-courses') && (strpos($_SERVER['REQUEST_URI'], 'teacher-support' ) !== false || strpos($_SERVER['REQUEST_URI'], 'support-pour-lenseignant' ) !== false) ){  
		return true; 
	}
	return false; 
}

/**
 * 
 * @global function check if the current page is a is the academy course page for school admin
 * 
 * @since V3
 */

function is_academy_course(){ 
    if( is_singular('sfwd-courses') && (strpos($_SERVER['REQUEST_URI'], 'support-pour-ladministrateur' ) !== false || strpos($_SERVER['REQUEST_URI'], 'support-for-the-administrator' ) !== false ) ){
		return true;
	}
	return false;
}

/**
 * 
 * @global function check if the current page is a group courses page
 * 
 * @since V3
 */

function is_group_courses_page(){ 
    if( function_exists('bp_is_group') && bp_is_group() && strpos($_SERVER['REQUEST_URI'], "courses") !== false ){  
		return true; 
	}
	return false; 
}


/**
 * 
 * @global function check if the current page is a group content page
 * 
 * @since V3
 */

function is_group_content_page(){ 
    if( function_exists('bp_is_group') && bp_is_group() 
        && strpos($_SERVER['REQUEST_URI'], 'teachers' ) == false               
        && strpos($_SERVER['REQUEST_URI'], 'students' ) == false
        && strpos($_SERVER['REQUEST_URI'], 'members' ) == false 
        && strpos($_SERVER['REQUEST_URI'], 'courses' ) == false 
        && strpos($_SERVER['REQUEST_URI'], 'admin' ) == false  ){  
		return true; 
	}
	return false; 
}


/**
 * Redirects the current user based on their membership status, profile, and access rights.
 *
 * Handles various redirections based on subscription status, membership, admin roles, and specific pages such as library and library manager.
 * 
 * @since V2 Initial version with membership and subscription redirection.
 * @since V3 Added redirection for library and library manager roles.
 * @since V3 Update redirection for student and teacher roles.
 * 
 * @return void
 */

function TPRM_subscription_redirect() {
    $manager_main_page = '';
    if (function_exists('bp_get_group_permalink')) {
        $manager_main_page = bp_get_group_permalink(groups_get_group(get_last_user_school()));
    }
    
    $library_main_page = home_url('/library/');
    $student_main_page = home_url('/members/me/my-course/');
    $libraries_manager_main_page = home_url('/libraries-dashboard/');
    if (!function_exists('is_subscription') || !function_exists('TPRM_is_ecom')) {
        return;
    }

    if (is_subscription() && !is_user_logged_in()) {
        auth_redirect();
    }

    if (is_user_logged_in()) {
        // Redirect if the user is logged in and on the subscription page
        if (is_subscription()) {
            if (is_tprm_admin() || TPRM_is_public() ) {
                return;
            }

            // Define the main page constants based on the user's role
            if (is_school_principal() || is_teacher() || is_school_leader()) {
                wp_redirect($manager_main_page);
                exit();
            }

            if (is_library()) {
                wp_redirect($library_main_page);
                exit();
            }

            if (is_libraries_manager()) {
                wp_redirect($libraries_manager_main_page);
                exit();
            }

            if (is_active_student(get_current_user_id())) {
                wp_redirect($student_main_page);
                exit();
            }
        }
        if (is_page('dashboard')) {
            // Define the main page constants based on the user's role
            if (is_school_principal() || is_teacher() || is_school_leader()) {
                wp_redirect($manager_main_page);
                exit();
            }

            if (is_library()) {
                wp_redirect($library_main_page);
                exit();
            }

            if (is_libraries_manager()) {
                wp_redirect($libraries_manager_main_page);
                exit();
            }

            if (is_active_student(get_current_user_id())) {
                wp_redirect($student_main_page);
                exit();
            }
        }

        // Other redirects based on different conditions
        if (!is_tprm_admin() && !is_active_member() && !is_active_student(get_current_user_id()) && !is_subscription() && !TPRM_is_public() && !TPRM_is_ecom() && !is_tprm_leader()) {
            wp_redirect(home_url('/subscription/'));
            exit();
        }

        if (is_library() && !is_library_page() && !TPRM_is_public() && !TPRM_is_ecom()) {
            wp_redirect(home_url('/library/'));
            exit();
        }

        if (is_libraries_manager() && !is_library_manage_page() && !is_library_dashboard_page() && !TPRM_is_public() && !TPRM_is_ecom()) {
            wp_redirect(home_url('/libraries-dashboard/'));
            exit();
        }
    }
}
  
/* 
* *** redirection and wpml hooks ***
*/

/**
 * reset wpml capabilities
 *
 * @since V2
 */

 function TPRM_reset_wpml_capabilities() {
    if ( function_exists( 'icl_enable_capabilities' ) ) {
        icl_enable_capabilities();
    }
}

/**
 * redirect to homepage after logout
 *
 * @since V2
 */

 function TPRM_homepage_logout_redirect() {
    wp_redirect(home_url()); // Redirect to the home page
    exit;
}

// Add h5p capabilities to administrators
/* $admin_role = get_role('administrator');

if ($admin_role) {
    $admin_role->add_cap('manage_h5p_libraries');
    $admin_role->add_cap('edit_others_h5p_contents');
    $admin_role->add_cap('edit_h5p_contents');
    $admin_role->add_cap('view_h5p_contents');
    $admin_role->add_cap('view_others_h5p_contents');
    $admin_role->add_cap('view_h5p_results');
}
 */



/* 
* Update last user school
*/

add_action('wp_ajax_update_last_user_school', 'update_last_user_school');

function update_last_user_school() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'update_last_user_school_payload' && !empty($_POST['selectedUrl'])
    && !empty($_POST['selectedText']) && !empty($_POST['avatarUrl']) && is_user_logged_in() ) {

        //check_ajax_referer('update_last_user_school_nonce', 'security');
      
        $selectedText = sanitize_text_field(($_POST['selectedText']));
        $avatarUrl = esc_url_raw(($_POST['avatarUrl']));
        $selectedUrl = esc_url_raw(($_POST['selectedUrl']));
        $user_id = get_current_user_id();
        update_user_meta($user_id, 'selected_school_label', $selectedText);
        update_user_meta($user_id, 'selected_school_avatar', $avatarUrl);
        update_user_meta($user_id, 'selected_school_url', $selectedUrl);

        $avatar =  get_user_meta($user_id, 'selected_school_avatar');
        wp_send_json_success($avatar);
        wp_die();
    } else {
        wp_send_json_error();
        error_log('some data is missing or user not logged in');
        wp_die();
    }
}


function add_courses_label_to_body_class($classes) {

    if (is_academy_course()) {
        $classes[] = 'academy_course';
    }
    if (is_onboarding_course()) {
        $classes[] = 'onboarding_course';
    }
    
    return $classes;
}
add_filter('body_class', 'add_courses_label_to_body_class');