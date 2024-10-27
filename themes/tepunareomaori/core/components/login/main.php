<?php 

/* Define Constants */

define('LC_URL_PATH', get_stylesheet_directory_uri() . '/core/components/login/' );

define('LC_DIR', TPRM_COMPONENT . 'login/' );

define('LC_JS_DIR', LC_URL_PATH . 'js/' );
define('LC_CSS_DIR', LC_URL_PATH . 'css/' );
define('LC_INCLUDES_DIR', LC_DIR . 'includes/' );

add_action('login_enqueue_scripts', 'loading_login_script', 9999);
add_action('wp_enqueue_scripts', 'picture_password_scripts', 9999);

/* Enqueue Scripts and styles */

function loading_login_script() {
    // Enqueue the custom JavaScript file on the login page
      $ajaxurl = admin_url('admin-ajax.php');
      ob_start();
      include TPRM_THEME_PATH . 'template-parts/preloader.php';
      $preloader = ob_get_clean();
      
      wp_enqueue_style('tprm-nprogress-style', TPRM_CSS_PATH .'nprogress.css', '', TPRM_THEME_VERSION );
      wp_enqueue_style('tprm-login-style', TPRM_CSS_PATH .'tprm-login.css' );
      wp_enqueue_script('tprm-nprogress-script', TPRM_JS_PATH . 'nprogress.js', array('jquery' ), TPRM_THEME_VERSION, true );
      wp_enqueue_script('loading-login-script', LC_JS_DIR . 'login-loader.js', array( 'tprm-nprogress-script','jquery' ), TPRM_THEME_VERSION, true );     
      wp_enqueue_script('login-page-script', LC_JS_DIR . 'login-page.js', array( 'tprm-nprogress-script','jquery' ), TPRM_THEME_VERSION, true );
      wp_add_inline_script('loading-login-script', 'var ajaxurl = ' . wp_json_encode($ajaxurl) . ';', 'before');
      wp_add_inline_script('login-page-script', 'var preloader = ' . wp_json_encode($preloader) . ';', 'before');

      $LC_data = array(
        'login_picture_password' => __('Log In with Picture Password', 'tprm-theme' ),
        'empty_classroom_code' => __('Please Enter a Classroom Code.', 'tprm-theme' ),
        'login_btn_label' => __('Log In', 'tprm-theme' ),
        'login_text_password' => __('Log In with Regular Password', 'tprm-theme' ),
        'username_empty' => __('<strong>Error : </strong> The username field is empty.', 'tprm-theme' ),
        'password_empty' => __('<strong>Error : </strong> The password field is empty.', 'tprm-theme' ),
        'picture_password_empty' => __('<strong>Error : </strong> No Picture has been selected.' , 'tprm-theme' ),
        'picture_password_login_nonce' => wp_create_nonce( 'picture_password_login_nonce' ),
      );
      
    // Localize the script with translated strings
    wp_localize_script( 'login-page-script', 'LC_data', $LC_data );
}


// Enqueue picture password scripts and styles
function picture_password_scripts() {
	if( is_page('group-account-access') ){	
      wp_enqueue_script('picture-password-script', LC_JS_DIR . 'picture-password.js', array( 'tprm-nprogress-script','jquery' ), TPRM_THEME_VERSION, true );
    	//wp_enqueue_style('picture-password-style', LC_CSS_DIR .'picture-password.css' );
	}
}

// Load Login component Hooks

$includes_files = array('active-session', 'backend', 'front', 'picture-password', 'login');

foreach($includes_files as $includes_file){
  require_once LC_INCLUDES_DIR . $includes_file . '.php';
}
