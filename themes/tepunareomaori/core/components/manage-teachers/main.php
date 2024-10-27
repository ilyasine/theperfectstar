<?php 

/* Define Constants */

define('MTE_URL_PATH', get_stylesheet_directory_uri() . '/core/components/manage-teachers/' );

define('MTE_DIR', TPRM_COMPONENT . 'manage-teachers/' );

define('MTE_JS_DIR', MTE_URL_PATH . 'js/' );
define('MTE_CSS_DIR', MTE_URL_PATH . 'css/' );
define('MTE_INCLUDES_DIR', MTE_DIR . 'includes/' );
define('MTE_TEMPLATE_DIR', MTE_DIR . 'templates/' );

add_action('wp_enqueue_scripts', 'manage_teachers_scripts');

/* Enqueue Scripts and styles for subgroups */

// Enqueue picture password scripts and styles
function manage_teachers_scripts() {
	if( function_exists('is_teachers_page') && is_teachers_page() ){	
        wp_enqueue_script("jquery-effects-core");
        //wp_enqueue_style('jquery-ui-style', TPRM_CSS_PATH .'jquery-ui.css' );
        //manage-teachers global style
        wp_enqueue_style('manage-teachers-style', TPRM_CSS_PATH .'manage-teachers.css' );
       //Enqueue create teacher
        wp_enqueue_script('create-teacher-script', MTE_JS_DIR . 'create-teacher.js', array( 'jquery', 'jquery-effects-core' , 'jquery-ui-tabs' , 'tprm-bp-groups-js' ), TPRM_THEME_VERSION, true );
        wp_enqueue_script('manage-teacher-script', MTE_JS_DIR . 'manage-teacher.js', array( 'jquery', 'jquery-effects-core' , 'jquery-ui-tabs' , 'tprm-bp-groups-js' ), TPRM_THEME_VERSION, true );
        wp_enqueue_style('create-teacher-style', TPRM_CSS_PATH .'create-teacher.css' );

        $creating_classrrom_header = __('CREATING Teacher', 'tprm-theme');
        $teacher_being_created_message = __('The Teacher is being created...', 'tprm-theme');
        
        $MTE_data = array(
            'assign_classroom' => __('Set as teacher for this Classroom', 'tprm-theme' ),
            'assign_all_classrooms' => __('Set as teacher for All Classrooms', 'tprm-theme' ),
            'remove_classroom' => __('Remove the teacher from this classroom', 'tprm-theme' ),
            'remove_all_classrooms' => __('Remove the teacher from All Classrooms', 'tprm-theme' ),
            'no_classroom_selected' => __('You did not select any classroom to assign to the teacher. Please select at least one teacher and confirm your selection by pressing the ❝ <strong>Confirm Teacher Selection</strong> ❞ button', 'tprm-theme' ),
            'classroom_selected' => __('The selected classrooms have been successfully assigned to the teacher', 'tprm-theme' ),
            'missing_school_id' => __('School ID is missing', 'tprm-theme' ),
            'missing_teacher_first_name' => __('Teacher First name is missing. Please provide a valid Teacher First name', 'tprm-theme' ),
            'missing_teacher_last_name' => __('Teacher Last name is missing. Please provide a valid Teacher First name', 'tprm-theme' ),
            'missing_teacher_email' => __('Teacher Email is missing. Please provide a valid Teacher Email', 'tprm-theme' ),
            'teacher_email_placeholder' => esc_attr__( 'Ex. teacher@tepunareomaori.com', 'tprm-theme' ),
            'missing_teacher_password' => __('Teacher Password is missing. Please Generate a valid Password', 'tprm-theme' ),
            'weak_teacher_password' => __('Weak password. Please Generate a stronger Password', 'tprm-theme' ),
            'strong_teacher_password' => __('Strong password', 'tprm-theme' ),
        
            'unconfirmed_selection' => __('You have selected one or more classrooms but have not yet confirmed your choices. Please click the ❝ <strong>Confirm Classroom Selection</strong> ❞ button to finalize your selection', 'tprm-theme' ),          
            //creating teacher
            'creating_teacher_header' => __('CREATING Teacher', 'tprm-theme'),
            'error_creating_teacher' => __('ERROR', 'tprm-theme'),
            'teacher_being_created_message' => __('The teacher is being created...', 'tprm-theme'),
            //error
            'choose_different_teacher_name' => __('Please Back and Choose a different teacher Name', 'tprm-theme'),
            //success
            'teacher_created_title' => __('Teacher created', 'tprm-theme'),
            'teacher_created_subtitle' => __('Done', 'tprm-theme'),
            'teacher_created_body' => __('The teacher CE1 has been created successfully. You can access it via the following button', 'tprm-theme'),
            'create_new_teacher' => __('Create New teacher', 'tprm-theme'),

            'classrooms_setup_title' => __('Classrooms Setup.', 'tprm-theme'),
            'classrooms_setup_subtitle' => __('Assign Classroom(s) to the teacher:', 'tprm-theme'),

            'previous_btn' => __('⮘ Previous', 'tprm-theme'),
            'back_btn' => __('⮘ Back', 'tprm-theme'),

          );
          // Localize the script with translated strings
          wp_localize_script( 'manage-teacher-script', 'MTE_data', $MTE_data );  
          wp_localize_script( 'create-teacher-script', 'MTE_data', $MTE_data );  
        
	    }
}


// Load Manage Classes component Hooks

$includes_files = array('front', 'create', 'functions', 'manage-classrooms');

foreach($includes_files as $includes_file){
    require_once MTE_INCLUDES_DIR . $includes_file . '.php';
}
