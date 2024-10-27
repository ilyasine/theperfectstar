<?php 

global $ld_active ;

$ld_active = false;

if (!defined('TPRM_THEME_PATH')) {
    define('TPRM_THEME_PATH', get_theme_root() . '/tepunareomaori/');
}

if (!defined('TPRM_IMG_ABS_PATH')) {
    define('TPRM_IMG_ABS_PATH', TPRM_THEME_PATH . 'assets/images/');
}

if (!defined('TPRM_IMG_PATH')) {
    define('TPRM_IMG_PATH', get_stylesheet_directory_uri() . '/assets/images/');
}

if (!defined('TPRM_PLACEHOLDER_IMG')) {
    define('TPRM_PLACEHOLDER_IMG', TPRM_IMG_PATH . 'tprm-placeholder.jpg');
}

if (!defined('TPRM_CSS_PATH')) {
    define('TPRM_CSS_PATH', get_stylesheet_directory_uri() . '/assets/css/');
}

if (!defined('TPRM_JS_PATH')) {
    define('TPRM_JS_PATH', get_stylesheet_directory_uri() . '/assets/js/');
}

if (!defined('TPRM_DEP')) {
    define('TPRM_DEP', TPRM_THEME_PATH . 'core/dependencies/');
}

if (!defined('TPRM_COMPONENT')) {
    define('TPRM_COMPONENT', TPRM_THEME_PATH . 'core/components/');
}

if (!defined('TPRM_THEME_VERSION')) {
    define('TPRM_THEME_VERSION', '3.0.0');
}

if ( ! defined( 'THEME_HOOK_PREFIX' ) ) {
    define( 'THEME_HOOK_PREFIX', 'TPRM_theme_' );
}

if ( ! defined( 'TPRM_SUPPORT' ) ) {
    define('TPRM_SUPPORT', sprintf( __('Please get in touch with <a href="mailto:%s">tepunareomaori support</a> or contact your School Leader', 'tprm-theme'), 'kiaora@tepunareomaori.co.nz' ) );
}

if ( ! defined( 'TPRM_icon' ) ) {
    define('TPRM_icon', file_get_contents( TPRM_IMG_ABS_PATH . 'TPRM_icon.php'));
}

if ( ! defined( 'student_main_page' ) ) {
    define('student_main_page', home_url('/members/me/my-course/'));
}

if ( ! defined( 'manager_main_page' ) && function_exists('get_last_user_school')) {
    define('manager_main_page', bp_get_group_permalink(groups_get_group(get_last_user_school())));
}

if ( ! defined( 'library_main_page' ) ) {
    define('library_main_page', home_url('/library/'));
}

if ( ! defined( 'libraries_manager_main_page' ) ) {
    define('libraries_manager_main_page', home_url('/libraries-dashboard/'));
}

// if learndash is active
if( in_array('sfwd-lms/sfwd_lms.php', apply_filters('active_plugins', get_option('active_plugins'))) || defined( 'LEARNDASH_VERSION' )){
    $ld_active = true;

    define('TPRM_LD_PATH',  WP_PLUGIN_DIR . '/sfwd-lms/');

    define('TPRM_LD_TEMPLATE_PATH',  WP_PLUGIN_DIR . '/sfwd-lms/themes/ld30/templates/');

    //sfwd-lms/themes/ld30/templates/modules/alert.php
}

