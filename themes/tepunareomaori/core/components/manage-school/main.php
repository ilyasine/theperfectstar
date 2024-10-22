<?php 

/* Define Constants */

define('MSC_URL_PATH', get_stylesheet_directory_uri() . '/core/components/manage-school/' );

define('MSC_DIR', TPRM_COMPONENT . 'manage-school/' );

define('MSC_JS_DIR', MSC_URL_PATH . 'js/' );
define('MSC_CSS_DIR', MSC_URL_PATH . 'css/' );
define('MSC_INCLUDES_DIR', MSC_DIR . 'includes/' );
define('MSC_TEMPLATE_DIR', MSC_DIR . 'templates/' );

//add_action('wp_enqueue_scripts', 'manage_school_scripts');

/* Enqueue Scripts and styles for subgroups */

// Enqueue picture password scripts and styles
function manage_school_scripts() {
	//if(  function_exists('bp_is_group_subgroups') && bp_is_group_subgroups() || function_exists('bp_is_groups_directory') && bp_is_groups_directory() ){	
        
       //Enqueue create school
        wp_enqueue_script('create-school-script', MSC_JS_DIR . 'create-school.js', array( 'jquery' ), TPRM_THEME_VERSION, true );
        wp_enqueue_style('create-school-style', TPRM_CSS_PATH .'create-school.css' );
        
	//}
}


// Load Manage School component Hooks
$includes_files = array('form-submission', 'create-school', 'create-admin');

foreach($includes_files as $includes_file){
    require_once MSC_INCLUDES_DIR . $includes_file . '.php';
}
