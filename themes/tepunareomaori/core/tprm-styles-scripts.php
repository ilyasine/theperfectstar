<?php 

 /**
 * tepunareomaori
 * Ce fichier intégre les fichiers des styles et scripts
 * @author : Yassine Idrissi
 * 
 * @since V2
 */

 add_action('wp_enqueue_scripts', 'TPRM_public_scripts' , 99999);
 add_action('login_enqueue_scripts', 'TPRM_public_scripts' , 99999);
 add_action('admin_enqueue_scripts', 'TPRM_admin_scripts' );
 add_action('wp_enqueue_scripts', 'dashboard_scripts_styles' , 99999 );
 add_action('wp_enqueue_scripts', 'students_credentials_styles' );
 //add_action('wp_enqueue_scripts', 'teacher_resources_styles' );
 add_action('wp_enqueue_scripts', 'final_course_quiz' );

 add_action('wp_enqueue_scripts', 'schools_archive' );
 

function TPRM_public_scripts() {

  wp_enqueue_style('styles',get_stylesheet_uri());
  wp_enqueue_style('kwf-global-style', TPRM_CSS_PATH .'global.css' );
  wp_enqueue_style('tprm-theme-style', TPRM_CSS_PATH .'tprm-theme.css', '', TPRM_THEME_VERSION );

  /* Enqueue Nprogress*/
  wp_enqueue_style('kwf-nprogress-style', TPRM_CSS_PATH .'nprogress.css', '', TPRM_THEME_VERSION );
  wp_enqueue_script('kwf-nprogress-script', TPRM_JS_PATH . 'nprogress.js', array( 'jquery' ), TPRM_THEME_VERSION, true );
  if(!is_login_page() ){
    wp_enqueue_script('kwf-global-script', TPRM_JS_PATH . 'global.js', array( 'kwf-nprogress-script', 'jquery' ), TPRM_THEME_VERSION, true );
  }

  /* Enqueue driverjs*/
 /*  wp_enqueue_style('kwf-driver-style', TPRM_CSS_PATH .'driver.css', '', TPRM_THEME_VERSION );
  wp_enqueue_script('kwf-driver-script', TPRM_JS_PATH . 'driver.js', TPRM_THEME_VERSION, true );
  wp_enqueue_script('kwf-guide-script', TPRM_JS_PATH . 'guide.js', array( 'kwf-driver-script' ), TPRM_THEME_VERSION, true );
  $i18_string = array(
    'press_here' => __('Press Here', 'tprm-theme' ),
    'manage_school_description' => __('To learn how to manage your School', 'tprm-theme' ),
    'manage_teacher_description' => __('To learn how to manage your students’ accounts and courses', 'tprm-theme' ),
    'start_course' => __('Press Here to Start', 'tprm-theme' ),
    'continue_course' => __('Press Here to Continue', 'tprm-theme' ),
  );

  wp_localize_script( 'kwf-guide-script', 'i18_string', $i18_string ); */

  $TPRM_data = array(
    'ajaxurl'  => admin_url( 'admin-ajax.php' ),
    'select_school_i18' => __('Select School', 'tprm-theme' ),
  );

  wp_localize_script( 'kwf-global-script', 'TPRM_data', $TPRM_data );
  
  /* if(function_exists('bp_is_groups_directory') && bp_is_groups_directory()) { */ // groups archive page
    if(function_exists('bp_is_groups_component') && bp_is_groups_component()) { // single group

      wp_enqueue_script( 'kwf-bp-groups-js', TPRM_JS_PATH . 'bp-groups.js', array( 'jquery' ), TPRM_THEME_VERSION, true );

    $TPRM_bp_groups_data = array(
      'all_schools'     => __('All Schools', 'tprm-theme' ),
      'all_curriculums' => __('All Curriculums', 'tprm-theme' ),
      'this_year_i18' => __('Current Year', 'tprm-theme' ),
      'credentials_copied' => __('Credentials copied to clipboard !', 'tprm-theme' ),   
      'disconnected_classroom' => __('Classroom Disconnected', 'tprm-theme' ),
      'disconnect_classroom' => __( 'Disconnect Classroom', 'tprm-theme' ),
      'classroomcopyfeedback' => __( 'Classroom code copied to clipboard !', 'tprm-theme' ), 
      'schoolcopyfeedback' => __( 'School code copied to clipboard !', 'tprm-theme' ), 
      'ajaxurl'  => admin_url( 'admin-ajax.php' ),
      'this_year' => get_option( 'school_year' ),
    );

    if( function_exists('bp_is_group_subgroups') && bp_is_group_subgroups() || function_exists('bp_is_groups_directory') && bp_is_groups_directory()){
      $TPRM_bp_groups_data['this_school'] = bp_get_current_group_id();
    }

    // Localize the script with new data
    wp_localize_script( 'kwf-bp-groups-js', 'TPRM_bp_groups_data', $TPRM_bp_groups_data );

  }

    $TPRM_css = '';

    if(( (function_exists('bp_is_groups_component') && bp_is_groups_component()) || (function_exists('bp_is_user') && bp_is_user())) ){
      wp_enqueue_style('kwf-group-style', TPRM_CSS_PATH .'kwf-group.css', '', TPRM_THEME_VERSION );   
    }

    if( is_reporting() ){
      $TPRM_css .= 'input#reporting-group-selector__submit {
        color: #333 !important;
        border: 1px solid #979797;
        background-color: white;
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, white), color-stop(100%, #dcdcdc));
        background: -webkit-linear-gradient(top, white 0%, #dcdcdc 100%);
        background: -moz-linear-gradient(top, white 0%, #dcdcdc 100%);
        background: -ms-linear-gradient(top, white 0%, #dcdcdc 100%);
        background: -o-linear-gradient(top, white 0%, #dcdcdc 100%);
        background: linear-gradient(to bottom, white 0%, #dcdcdc 100%)
        }        
        input#reporting-group-selector__submit:hover {
            color: #fff;
            border-color: #9e9e9e;
            background-color: #9e9e9e;
            box-shadow: none;
        }      
        input#reporting-group-selector__submit:focus {
            box-shadow: 0 0 0 3px rgba(108, 117, 125, .2);
            box-shadow: none;
        }     
      ';
    }

    if ( ! is_TPRM_admin() ) {
      $TPRM_css .= '.group-button.leave-group {
        pointer-events: none !important;
        }';	
      /* $TPRM_css .= '.member-type-tepunareomaori {
        display: none !important;
        }';	 */
    }
    if ( ! current_user_can('director') && ! is_TPRM_admin() ) {
      $TPRM_css .= ' a.bp-parent-group-title {
        pointer-events: none !important;
        }';	
    }

    if ( ! is_active_member() && is_user_logged_in() ) {
      $TPRM_css .= '.header-aside .user-wrap.user-wrap-container li.menupop.parent {
          display: none !important;
      }';
      $TPRM_css .= '.header-aside .user-wrap.user-wrap-container .sub-menu-inner li a.user-link {
          pointer-events: none;
      }';
      
    }
 

  /* handle active btn on front page based on subscription status*/

  if ( ! is_active_member() ) {
      $TPRM_css .= '#kwf-not-activated {
          display: inline-block;
          margin: -40px auto 100px auto;
      }';
      $TPRM_css .= '#kwf-activated {
          display: none;
      }';
  }else{
      $TPRM_css .= '#kwf-activated {
          display: inline-block;
          margin: -40px auto 100px auto;
      }';
      $TPRM_css .= '#kwf-not-activated {
          display: none;
      }';
  }
      
    wp_add_inline_style('tprm-theme-style', $TPRM_css);

  	// LearnDash.
	if ( class_exists( 'SFWD_LMS' ) ) {
		wp_enqueue_style( 'tprm-theme-learndash', TPRM_CSS_PATH .'learndash.css', '', TPRM_THEME_VERSION );
    // Just load on lessons, topics, quizzes & course.
		if (is_learning()) {
			wp_enqueue_script( 'tprm-theme-learndash-js', TPRM_JS_PATH . 'tprm-theme-learndash.js', array( 'jquery' ), TPRM_THEME_VERSION, true );
		}
    $TPRM_data = array(
      'ajaxurl'  => admin_url( 'admin-ajax.php' ),
      'select_lesson' => __('Select a Lesson', 'tprm-theme' ),
    );
  
    wp_localize_script( 'tprm-theme-learndash-js', 'TPRM_data', $TPRM_data );
	}

  // Forums.
	if ( class_exists( 'bbPress' ) ) {
		wp_enqueue_style( 'tprm-theme-forums', TPRM_CSS_PATH . '/bbpress.css', '', buddyboss_theme()->version() );
	}

	// WooCommerce.
	/* if ( function_exists( 'WC' ) ) {
		wp_enqueue_style( 'tprm-theme-woocommerce', TPRM_CSS_PATH .'/woocommerce.css', '', buddyboss_theme()->version() );
	} */

  /**
   * replace login with back to dasboard if user is logged in
   *
   * @since V2
   */

    if ( is_user_logged_in() ) {
      $css = '#kwf-login { display: none; }';
      $css .= '#kwf-dashboard { display: inline-block; margin-top: -20px; }';
    }else{
      $css = '#kwf-dashboard { display: none; }';
      $css .= '#kwf-login { display: inline-block; margin-bottom: -20px;}';
    }

    /* Hide signup button */
    $css .= '.bb-header-buttons .button.signup { display: none; }';
  
    wp_add_inline_style('kwf-global-style', $css);

}

function TPRM_admin_scripts() { 
  wp_enqueue_style('kwf-admin-style', TPRM_CSS_PATH .'/admin-style.css' );
}

function dashboard_scripts_styles(){
  if( is_page_template('dashboard.php') ) {  
    wp_dequeue_style('buddyboss-theme-elementor');
    wp_enqueue_style('kwf-dash-style', TPRM_CSS_PATH .'/dashboard.css' );
  }
}

function students_credentials_styles(){
  if( strpos($_SERVER['REQUEST_URI'], "students-credentials") !== false ){ 
    wp_enqueue_style('kwf-students-credentials-style', TPRM_CSS_PATH .'/students_credentials.css' );
		wp_enqueue_script('TPRM_stdcred', TPRM_JS_PATH . 'students-credentials.js' , array(), TPRM_THEME_VERSION, true);
    wp_enqueue_script('TPRM_jspdf', TPRM_JS_PATH . 'jspdf.umd.min.js' );
		wp_enqueue_script('TPRM_jspdf_autotable', TPRM_JS_PATH . 'jspdf.plugin.autotable.js');
		wp_enqueue_script('TPRM_jspdf_nunito_font', TPRM_JS_PATH . 'Nunito-Regular-normal.js');
  
    $translation_array = array(
      'credentials_copied' => __('Credentials copied to clipboard !', 'tprm-theme' ),
      'success_print' => __('You have successfully generated the students credentials file for this classroom, please check out your download folder.', 'tprm-theme' ),
    );

    // Localize the script with new data
    wp_localize_script( 'TPRM_stdcred', 'TPRM_stdcred', $translation_array );
  }
}

function teacher_resources_styles(){
  if( function_exists('bp_is_group') && bp_is_group() ){
    wp_enqueue_style('kwf-teacher-resources-style', TPRM_CSS_PATH .'/teacher_resources.css' );
    wp_enqueue_script('kwf-teacher-resources-script', TPRM_JS_PATH . 'teacher-resources.js' , array(), TPRM_THEME_VERSION, true);

    $translation_array = array(
      'scroll_down' => __('Please Scroll down to view the remaining resources.', 'tprm-theme' ), 
    );

    // Localize the script with translated strings
    wp_localize_script( 'kwf-teacher-resources-script', 'teacher_resources', $translation_array );
  }
}

function final_course_quiz(){
  if( function_exists('bp_is_group') && bp_is_group() || is_learning() ){
      wp_enqueue_script('TPRM_final_course_quiz', TPRM_JS_PATH . 'final-course-quiz.js' , array('jquery'), TPRM_THEME_VERSION, true);//
      wp_enqueue_style('kwf-final-course-quiz-style', TPRM_CSS_PATH .'final_quiz.css' );     
  }
}



function schools_archive(){
  if( is_page('schools') ){
      $ajaxurl = admin_url('admin-ajax.php');
      wp_enqueue_script('jquery-ui-tabs');
      wp_enqueue_script('schools-kwf-script', TPRM_JS_PATH . 'schools-stats.js' , array( 'jquery', 'jquery-ui-tabs' ), TPRM_THEME_VERSION, true);//
      wp_enqueue_style('kwf-schools-archive-style', TPRM_CSS_PATH .'schools_archive.css' );
      wp_add_inline_script('schools-kwf-script', 'var ajaxurl = ' . wp_json_encode($ajaxurl) . ';', 'before');
  }
}


