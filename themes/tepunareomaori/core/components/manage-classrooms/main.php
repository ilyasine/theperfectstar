<?php 

/* Define Constants */

define('MCL_URL_PATH', get_stylesheet_directory_uri() . '/core/components/manage-classrooms/' );

define('MCL_DIR', TPRM_COMPONENT . 'manage-classrooms/' );

define('MCL_JS_DIR', MCL_URL_PATH . 'js/' );
define('MCL_CSS_DIR', MCL_URL_PATH . 'css/' );
define('MCL_INCLUDES_DIR', MCL_DIR . 'includes/' );

add_action('wp_enqueue_scripts', 'manage_classrooms_scripts');

/* Enqueue Scripts and styles for subgroups */

// Enqueue picture password scripts and styles
function manage_classrooms_scripts() {
	if(  function_exists('bp_is_group_subgroups') && bp_is_group_subgroups() || function_exists('bp_is_groups_directory') && bp_is_groups_directory() ){	
        wp_enqueue_script("jquery-effects-core");

        global $bp;
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        $students_tab = $group_link . 'students';
        
       //Enqueue create classroom
        wp_enqueue_script('create-classroom-script', MCL_JS_DIR . 'create-classroom.js', array( 'jquery', 'jquery-effects-core' , 'jquery-ui-tabs' , 'tprm-bp-groups-js' ), TPRM_THEME_VERSION, true );
        wp_enqueue_style('create-classroom-style', TPRM_CSS_PATH .'create-classroom.css' );

        //Enqueue manage classroom
        wp_enqueue_style('manage-classrooms-style', TPRM_CSS_PATH .'manage-classrooms.css' );        
        wp_enqueue_script('manage-classroom-script', MCL_JS_DIR . 'manage-classroom.js', array( 'jquery', 'jquery-effects-core' , 'jquery-ui-tabs' , 'tprm-bp-groups-js' ), TPRM_THEME_VERSION, true );

       //Duplicate Structure
        wp_enqueue_script('duplicate-structure-script', MCL_JS_DIR . 'duplicate-structure.js', array( 'tprm-nprogress-script','jquery' ), TPRM_THEME_VERSION, true );
        wp_enqueue_style('duplicate-structure-style', TPRM_CSS_PATH .'duplicate-structure.css' );

        $creating_classrrom_header = __('CREATING CLASSROOM', 'tprm-theme');
        $classroom_being_created_message = __('The classroom is being created...', 'tprm-theme');
        $this_year = get_option('school_year');
        
        $MCL_data = array(
            'add_teacher' => __('Add this teacher to the Classroom', 'tprm-theme'),
            'remove_teacher' => __('Remove this teacher from the Classroom', 'tprm-theme'),
            'no_teacher_selected' => __('You did not select any teacher to assign to the Classroom. Please select at least one teacher and confirm your selection by pressing the ❝ <strong>Confirm Teacher Selection</strong> ❞ button', 'tprm-theme'),
            'teacher_selected' => __('The selected teachers have been successfully assigned to the Classroom', 'tprm-theme'),
            'missing_school_id' => __('School ID is missing', 'tprm-theme'),
            'missing_classroom_name' => __('Classroom name is missing. Please provide a valid classroom name', 'tprm-theme'),
            'missing_group_type' => __('Curriculum is not generated! Classroom level is missing. Please select a valid classroom level', 'tprm-theme'),
            'unconfirmed_selection' => __('You have selected one or more teachers but have not yet confirmed your choices. Please click the ❝ <strong>Confirm Teacher Selection</strong> ❞ button to finalize your selection', 'tprm-theme'),
            'choose_classroom_level' => __('Choose a Level for the classroom', 'tprm-theme'),
            'choose_classroom_level_to_generate_curriculum' => esc_attr__('Choose a classroom Level to see the Generated Curriculum', 'tprm-theme'),
        
            // creating class
            'creating_classroom_header' => __('CREATING CLASSROOM', 'tprm-theme'),
            'error_creating_classroom' => __('ERROR', 'tprm-theme'),
            'classroom_being_created_message' => __('The classroom is being created...', 'tprm-theme'),
        
            // error
            'choose_different_classroom_name' => __('Please go back and choose a different Classroom Name', 'tprm-theme'),
        
            // success
            'classroom_created_title' => __('Classroom created', 'tprm-theme'),
            'classroom_created_subtitle' => __('Done', 'tprm-theme'),
            'classroom_created_body' => __('The classroom CE1 has been created successfully. You can access it via the following button', 'tprm-theme'),
            'create_new_classroom' => __('Create New Classroom', 'tprm-theme'),
        
            'teachers_setup_title' => __('Teachers Setup.', 'tprm-theme'),
            'teachers_setup_subtitle' => __('Add a Teacher(s) to the Classroom:', 'tprm-theme'),
        
            'previous_btn' => __('⮘ Previous', 'tprm-theme'),
            'back_btn' => __('⮘ Back', 'tprm-theme'),
        
            // promote
            'promote_this_student' => sprintf(esc_attr__('Promote this student to %s', 'tprm-theme'), $this_year),
            'promote_all_students' => sprintf(esc_attr__('Promote All students to %s', 'tprm-theme'), $this_year),
            'demote_this_student' => sprintf(esc_attr__('Demote this student to %s', 'tprm-theme'), $this_year),
            'demote_all_students' => sprintf(esc_attr__('Demote All students to %s', 'tprm-theme'), $this_year),
            'choose_classroom' => __('Choose a classroom', 'tprm-theme'),
            'error_promoting_students' => __('Promoting Students Failed, Classroom or Students are not valid', 'tprm-theme'),
            'no_students_selected' => __('No students selected or missing classroom ID', 'tprm-theme'),
            'promoting_students_in_progress' => __('Promoting Students in Progress...', 'tprm-theme'),
            'promoting_students_success' => __('Students promoted successfully.', 'tprm-theme'),
            'no_classroom_selected_to_promote' => sprintf(
                __('Please select a classroom to promote students from or create new student(s) from <a target="_blank" href="%s">here</a>.', 'tprm-theme'),
                $students_tab
            ),
            'error_occurred' => __('An error occurred. Please try again', 'tprm-theme'),
        
            // assign
            'choose_classroom' => __('Choose a classroom', 'tprm-theme'),
            'error_assigning_students' => __('Assigning Students Failed, Classroom or Students are not valid', 'tprm-theme'),
            'no_students_selected' => __('No students selected or missing classroom ID', 'tprm-theme'),
            'assigning_students_in_progress' => __('Assigning Students in Progress...', 'tprm-theme'),
            'assigning_students_success' => __('Students assigned successfully.', 'tprm-theme'),
            'no_classroom_selected_to_assign' => sprintf(
                __('Please select a classroom to assign students from or create new student(s) from <a target="_blank" href="%s">here</a>.', 'tprm-theme'),
                $students_tab
            ),
        
            // duplicate structure
            'error_duplicating_structure' => __('ERROR', 'tprm-theme'),
            'duplicating_structure_in_progress' => __('Creating Classrooms in Progress...', 'tprm-theme'),
            'duplicating_structure_success' => __('Classrooms have been created successfully.', 'tprm-theme'),
        );
      
          
        // Localize the script with translated strings
        wp_localize_script( 'create-classroom-script', 'MCL_data', $MCL_data );
        wp_localize_script( 'duplicate-structure-script', 'MCL_data', $MCL_data );
        
	}
}


// Load Manage Classes component Hooks

$includes_files = array('school', 'classroom', 'admin', 'create', 'duplicate-structure', 'assign');

foreach($includes_files as $includes_file){
    require_once MCL_INCLUDES_DIR . $includes_file . '.php';
}
