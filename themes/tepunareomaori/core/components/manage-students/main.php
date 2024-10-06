<?php 

/* Define Constants */

define('MST_URL_PATH', get_stylesheet_directory_uri() . '/core/components/manage-students/' );

define('MST_DIR', TPRM_COMPONENT . 'manage-students/' );

define('MST_JS_DIR', MST_URL_PATH . 'js/' );
define('MST_CSS_DIR', MST_URL_PATH . 'css/' );
define('MST_INCLUDES_DIR', MST_DIR . 'includes/' );
define('MST_TEMPLATE_DIR', MST_DIR . 'templates/' );

add_action('wp_enqueue_scripts', 'manage_students_scripts');

/* Enqueue Scripts and styles for subgroups */

// Enqueue picture password scripts and styles
function manage_students_scripts() {

    if( function_exists('is_students_page') && is_students_page() ){ 
            
        wp_enqueue_style('manage-students-style', TPRM_CSS_PATH .'manage-students.css' );
        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('manage-students-script', MST_JS_DIR . 'manage-students.js', array( 'jquery', 'jquery-effects-core' , 'jquery-ui-tabs' , 'kwf-bp-groups-js' ), TPRM_THEME_VERSION, true );

        /* include styles and script for creating students only in school */
        if( is_school(bp_get_current_group_id()) ){
            wp_enqueue_script('create-student-script', MST_JS_DIR . 'create-student.js', array( 'jquery', 'jquery-effects-core' , 'jquery-ui-tabs' , 'kwf-bp-groups-js' ), TPRM_THEME_VERSION, true );       
            wp_enqueue_style('create-student-style', TPRM_CSS_PATH .'create-student.css' );
            if( is_TPRM_manager() ) :
                wp_enqueue_script('bulk-create-student-script', MST_JS_DIR . 'bulk-create-student.js', array( 'jquery', 'jquery-effects-core' , 'jquery-ui-tabs' , 'kwf-bp-groups-js' ), TPRM_THEME_VERSION, true );       
                wp_enqueue_style('bulk-create-student-style', TPRM_CSS_PATH .'bulk-create-student.css' );
            endif;
        }

        wp_enqueue_script('TPRM_stdcred', MST_JS_DIR . 'students-credentials.js' , array('jquery'), TPRM_THEME_VERSION, true);
        wp_enqueue_script('TPRM_jspdf', TPRM_JS_PATH . 'jspdf.umd.min.js' );
        wp_enqueue_script('TPRM_jspdf_autotable', TPRM_JS_PATH . 'jspdf.plugin.autotable.js');
        wp_enqueue_script('TPRM_jspdf_nunito_font', TPRM_JS_PATH . 'Nunito-Regular-normal.js');

        $MST_data = array(
            'credentials_copied' => __('Credentials copied to clipboard !', 'tprm-theme' ),
            'select_classroom' => __('Select a classroom', 'tprm-theme' ),
            'choose_password_type' => __('Choose a password type', 'tprm-theme' ),
            'success_print' => __('You have successfully generated the students credentials file for this classroom, please check out your download folder.', 'tprm-theme' ),
            'assign_classroom' => __('Assign the student to this Classroom', 'tprm-theme' ),
            'remove_classroom' => __('Remove the student from this classroom', 'tprm-theme' ),
            'no_classroom_selected' => __('You did not select any classroom to assign to the student. Please select one Classroom and confirm your selection by pressing the ❝ <strong>Confirm Classroom Selection</strong> ❞ button', 'tprm-theme' ),
            'no_classroom_selected_for_import' => __('You did not select any classroom where to import the students.', 'tprm-theme' ),
            'no_file_selected_to_import' => __('Please select a file to import before proceeding.', 'tprm-theme'),
            'classroom_selected' => __('The selected classroom has been successfully assigned to the student', 'tprm-theme' ),
            'missing_school_id' => __('School ID is missing', 'tprm-theme' ),
            'missing_student_first_name' => __('Student First name is missing. Please provide a valid Student First name', 'tprm-theme' ),
            'missing_student_last_name' => __('Student Last name is missing. Please provide a valid Student Last name', 'tprm-theme' ),         
            'missing_student_password' => __('Student Password is missing. Please Generate a valid Password or choose a Picture Password', 'tprm-theme' ),
            'weak_student_password' => __('Weak password. Password must start with ks followed by 4 digits or more. Please Generate a stronger Password', 'tprm-theme' ),
            'strong_student_password' => __('Strong password', 'tprm-theme' ),    
            'unconfirmed_selection' => __('You have selected one or more classrooms but have not yet confirmed your choices. Please click the ❝ <strong>Confirm Classroom Selection</strong> ❞ button to finalize your selection', 'tprm-theme' ),          
            'no_picture_selected' => __('You have choosen Picture Password Type and You did not select any picture. Please select one Picture and confirm your selection by pressing the ❝ <strong>Confirm Picture Selection</strong> ❞ button to finalize your selection ', 'tprm-theme' ),          
            'file_uploaded' => __('You have Uploaded the file : ', 'tprm-theme' ),
            //creating student
            'creating_student_header' => __('Creating Student', 'tprm-theme'),
            'error_creating_student' => __('ERROR', 'tprm-theme'),
            'student_being_created_message' => __('The Student is being created...', 'tprm-theme'),
            //suspending student
            'student_being_suspended_message' => __('The Student is being suspended...', 'tprm-theme'),
            //updating student classrooms
            'updating_student_classrooms_message' => __('Updating Classrooms for', 'tprm-theme'),
            //bulk creating students
            'creating_students_header' => __('Creating Students', 'tprm-theme'),
            'submiting_students_file' => __('Submiting Students File', 'tprm-theme'),
            'error_creating_student_from_file' => __('ERROR', 'tprm-theme'),
            'students_being_created_message' => __('The Students are being created...', 'tprm-theme'),
            'uploading_analyzing_excel_file' => __('Uploading and analyzing Excel File...', 'tprm-theme'),
            'submitting_file' => __('Submitting File', 'tprm-theme'),
            //error
            'choose_different_student_name' => __('Please Back and Choose a different student Name', 'tprm-theme'),
            //bulk error
            'error_submiting_students_file' => __('There is an error with your Excel File, Please ensure the format is correct and upload the file again', 'tprm-theme'),
            //success
            'student_created_title' => __('Student created', 'tprm-theme'),
            'student_created_subtitle' => __('Done', 'tprm-theme'),
            'student_created_body' => __('The Student has been created successfully. You can access it via the following button', 'tprm-theme'),
            'create_new_student' => __('Create New student', 'tprm-theme'),
            //bulk success
            'bulk_student_created_subtitle' => __('Done', 'tprm-theme'),
            'bulk_student_created_body' => __('The Students have been created successfully. You can see them by pressing the following button', 'tprm-theme'),
            'bulk_student_created_title' => __('Students created', 'tprm-theme'),
            'bulk_create_new_student' => __('Submit New Students File', 'tprm-theme'),
            'processed_students' => __('Students have been Processed', 'tprm-theme'),
            'skipped_students' => __('Students Ignored', 'tprm-theme'),
            'active' => __('Active', 'tprm-theme'),

            'classrooms_setup_title' => __('Classroom Setup.', 'tprm-theme'),
            'classrooms_setup_subtitle' => __('Assign Classroom to the student:', 'tprm-theme'),       

            'previous_btn' => __('⮘ Previous', 'tprm-theme'),
            'back_btn' => __('⮘ Back', 'tprm-theme'),

        );
        // Localize the script with translated strings
        wp_localize_script( 'manage-students-script', 'MST_data', $MST_data );
        wp_localize_script( 'create-student-script', 'MST_data', $MST_data );
        wp_localize_script( 'bulk-create-student-script', 'MST_data', $MST_data );
        wp_localize_script( 'TPRM_stdcred', 'TPRM_stdcred', $MST_data );
        
	}

}

// Load Manage Classes component Hooks

$includes_files = array('front', 'activate', 'create', 'assign', 'bulk-import', 'functions', 'students-credentials');

foreach($includes_files as $includes_file){
    require_once MST_INCLUDES_DIR . $includes_file . '.php';
}
