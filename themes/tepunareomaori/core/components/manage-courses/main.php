<?php 

/* Define Constants */

define('MC_URL_PATH', get_stylesheet_directory_uri() . '/core/components/manage-courses/' );

define('MC_PATH', TPRM_COMPONENT . 'manage-courses/' );

define('MC_JS_PATH', MC_URL_PATH . 'js/' );

define('MC_INCLUDES_DIR', MC_PATH . 'includes/' );


/* Enqueue Scripts and styles */

add_action('wp_enqueue_scripts', 'manage_courses_front' );

function manage_courses_front(){
    // group default page (courses) or courses profile page 
    if( is_group_courses_page() || is_group_content_page() ){//
        wp_enqueue_script('kwf-manage-courses-script', MC_JS_PATH . 'front.js' , array('jquery'), TPRM_THEME_VERSION, true);
        wp_enqueue_script('jquery-dragsort.min', TPRM_JS_PATH . 'dragsort.min.js' , array('jquery'), TPRM_THEME_VERSION, true);
        wp_enqueue_style('kwf-manage-courses-style', TPRM_CSS_PATH .'manage-courses.css' );
        $translation_array = array(
          'course_selected' => __('You have selected the following Course : ', 'tprm-theme' ),
          'no_course_selected' => __('No Course has been Selected', 'tprm-theme' ),
          'no_course_selected_alert' => __('No Course has been Selected, Please <strong>Select a course before confirm</strong> or <strong>Cancel</strong> to close', 'tprm-theme' ),
          'unlock_order' => __('Unlock Order', 'tprm-theme' ),
          'lock_order' => __('Lock Order', 'tprm-theme' ),
          'order_locked_notice' => __('Courses order is <strong>disabled</strong>. To enable it, please press the <strong>Lock Order</strong> button.', 'tprm-theme'),
          'order_unlocked_notice' => __('Courses order is <strong>enabled</strong>. You can drag and drop any course to any position at which you would like the course to appear inside this classroom. Once you are done, Kindly press the <strong>Lock Order</strong> button', 'tprm-theme'),
          'order_locked_notice_title' => __('<strong>Order Locked</strong>', 'tprm-theme'),
          'order_unlocked_notice_title' => __('<strong>Order Unlocked</strong>', 'tprm-theme'),
          'order_locked_info' => __('The course order is currently <strong>disabled</strong>. To enable it, please press the <strong>Unlock Order</strong> button to start ordering the courses.', 'tprm-theme'),
          'order_unlocked_info' => __('The course order is currently <strong>enabled</strong>. Once you\'ve finished ordering the courses, please press the <strong>Lock Order</strong> button.', 'tprm-theme'),
        );
        // Localize the script with translated strings
        wp_localize_script( 'kwf-manage-courses-script', 'manage_courses_data', $translation_array );
    }
    // courses profile page 
    if( function_exists('bp_is_my_profile') && bp_is_my_profile() && function_exists('bb_learndash_profile_courses_slug') && strpos($_SERVER['REQUEST_URI'], bb_learndash_profile_courses_slug() ) !== false ){
        wp_enqueue_script('profile-courses-script', MC_JS_PATH . 'profile-courses.js' , array('jquery'), TPRM_THEME_VERSION, true);
        $translation_array = array(
          'my_archive' => __('My Archive', 'tprm-theme' ),        
        );
        // Localize the script with translated strings
        wp_localize_script( 'profile-courses-script', 'profile_courses', $translation_array ); 
    }
}

add_action('admin_enqueue_scripts', 'manage_courses_admin' );

function manage_courses_admin($hook){
    if ( 'edit.php' !== $hook ) {
      return;
    }
    wp_enqueue_script('kwf-manage-courses-script', MC_JS_PATH . 'admin.js' , array('jquery'), TPRM_THEME_VERSION, true);
   
    $manage_courses_data = array(
      'nonce' => wp_create_nonce( 'manage_courses_bulk_edit_nonce' ),
    );
    // Localize the script with translated strings
    wp_localize_script( 'kwf-manage-courses-script', 'manage_courses_data', $manage_courses_data );
    
}

// Load Manage Classes component Hooks

$includes_files = array('front', 'all-content', 'admin');

foreach($includes_files as $includes_file){
    require_once MC_INCLUDES_DIR . $includes_file . '.php';
}