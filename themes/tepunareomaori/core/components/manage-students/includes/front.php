<?php 


add_action('students_credentials', 'students_list' );
add_action('bp_init', 'setup_students_credentials_group_nav' );
add_filter('bp_nouveau_nav_has_count', 'students_count', 99, 3 );
add_filter('bp_nouveau_get_nav_count', 'students_count_int', 99, 3 );
add_filter('groups_forbidden_names', 'students_credentials_groups_forbidden' , 1);
add_filter('bp_get_search_default_text', 'students_search_form_text', 10, 1);
add_filter('bp_core_get_component_search_query_arg', 'students_search_query_arg', 10, 1);
add_filter('bp_nouveau_feedback_messages', 'students_credentials_feedback_message');

function students_credentials_feedback_message($feedback_messages){
	$students_credentials_feedback_message = array(
		'students-credentials-loading'             => array(
			'type'    => 'loading',
			'message' => __( 'Requesting the students credentials. Please wait.', 'tprm-theme' ),
		),
	);
	
	$feedback_messages = array_merge($feedback_messages, $students_credentials_feedback_message);

	return $feedback_messages;
}


function students_credentials_groups_forbidden($names){
	
	array_push( $names , 'students' );

	return $names;
}

/*
* ************** Start students Callback functions  **************
 */

 /**
 * Change students search form text
 *
 * @since V2
 * @param string $default_text to change.
 * @return string $default_text
 */

function students_search_form_text($default_text){
	if( function_exists('is_students_page') && is_students_page() ){ 
		$default_text = __( 'Search Student&hellip;', 'tprm-theme' );
	}
	if( function_exists('is_teachers_page') && is_teachers_page() ){ 
		$default_text = __( 'Search Teacher&hellip;', 'tprm-theme' );
	}

	return $default_text;
}

 /**
 * Update students search query arg
 *
 * @since V2
 * @param string $query_arg to update
 * @return string $query_arg
 */

function students_search_query_arg($query_arg){
	if( strpos($_SERVER['REQUEST_URI'], "students") !== false ){ 
		$query_arg = 'group_members_search';
		/* $query_arg = 'members_search'; */
	}

	return $query_arg;
}

/**
 * Create new Group tab for Student Credentials
 *
 * @since V2
 */

function setup_students_credentials_group_nav(){
    global $bp; 
    $user_access = false;
    $group_link = '';
	
    if( bp_is_active('groups') && !empty($bp->groups->current_group) ){

		$group_type = bp_groups_get_group_type( $bp->groups->current_group->id );

		$is_school = is_school($bp->groups->current_group->id) ? true : false;

		$user_access = $is_school ? is_tprm_manager() : $bp->groups->current_group->user_has_access;
		
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        //$user_access = $bp->groups->current_group->user_has_access;
	
		if ( is_tprm_leader() ) {
			bp_core_new_subnav_item( array( 
				'name' =>  __( 'Students', 'tprm-theme' ),
				'slug' => 'students',
				'parent_url' => $group_link, 
				'parent_slug' => $bp->groups->current_group->slug,
				'screen_function' => 'students_bp_group', 
				'position' => 60, 
				'user_has_access' => $user_access, 
				//'site_admin_only' => true,
				'item_css_id' => 'students' 
			));
		}
    }

}


function students_count( $count, $nav_item, $displayed_nav ) {

	if ( $nav_item->slug == 'students' ) {
		$count = true;
	}

    return $count;

}


function students_count_int( $count, $nav_item, $displayed_nav ) {

	if ( $nav_item->slug == 'students' ) {

		$group_members = groups_get_group_members(array(
			'group_id' => bp_get_current_group_id()	
		));

		$count = $group_members['count'];

	}

    return $count;

}

function students_bp_group() {

    add_action('bp_template_title', 'students_credentials_screen_title');
    add_action('bp_template_content', 'students_credentials_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }

}

function students_credentials_screen_title() {
	_e( 'Student Credentials', 'tprm-theme' );
}

function students_credentials_screen_content() {
	?>
		<!-- <div id="bp-ajax-loader"><?php bp_nouveau_user_feedback('students-credentials-loading');?></div> -->
	<?php
	do_action('students_credentials');
}


/**
 * Student Credentials content
 *
 * @since V2
 */

function students_list(){

	global $wpdb, $TPRM_ajax_nonce ;

	$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );
	
	// create students_credentials table where we are supposed to insert user_logins and passwords
	$std_cred_tbl = $wpdb->prefix . "students_credentials";
	if( $wpdb->get_var( "SHOW TABLES LIKE '". esc_sql($std_cred_tbl) ."'" ) != esc_sql($std_cred_tbl) ) {
		$sql = "CREATE TABLE $std_cred_tbl ( username varchar(100) primary key, stdcred varchar(100), first_name varchar(100), last_name varchar(100) )";
		$wpdb->query($sql);
	}

	$group_id = bp_get_current_group_id();
	$is_school = is_school(bp_get_current_group_id()) ? true : false;

	$group_members = array();
	$classrooms = array();

	$std_names = array();
	$std_emails = array();
	$std_usernames = array();
	$std_passwords = array();
	$std_classroms = array();
	$std_status = array();
	$status_colors =  array();
	$status_bg_colors =  array();
	$school_id = '';
	
	if ($is_school) {
		
		// Get school group members directly
		$school_group_members = groups_get_group_members(array(
			'group_id'            => $group_id, // Use the school group ID instead
			'exclude_admins_mods' => true,
			'exclude_banned'      => true,
			'exclude'             => false,
			'group_role'          => array('member'),
			'search_terms'        => false,
			//'type'              => 'first_joined',
		));


		if (isset($school_group_members['members'])) {
			// Iterate over the school group members and add them to the group_members array
			foreach ($school_group_members['members'] as $school_group_member) {
				$group_members[] = $school_group_member;
			}
		}

	} else {
		//Classroom
		// Get just the members of the current group
		$group_members = groups_get_group_members(array(
			'group_id'            => $group_id,
			//'per_page'            => 10,
			//'page'                => false,
			'exclude_admins_mods' => true,
			'exclude_banned'      => true,
			'exclude'             => false,
			'group_role'          => array('member'),
			'search_terms'        => false,
			//'type'                => 'first_joined',
		));
	}
	
	$cred_row_class = 'std-alternate';
	$members_id = array();
	
	if ($is_school) {
		$school_id = bp_get_current_group_id();
		$students_credentials = array_reverse($group_members);
	} else {
		$students_credentials = $group_members["members"];
		$school_id = bp_get_parent_group_id(bp_get_current_group_id());
	}

	$active_count = 0;
	$inactive_count = 0;
	$total_count = 0;
	$activation_rate = '';

	if ($students_credentials) {
		foreach ($students_credentials as $student) {
			$id = $student->ID;
			$user = get_userdata($id);
			if(in_array('suspended', (array)$user->roles)) continue; //skip suspended students from the count		
			if (is_active_student($id)) {
				$active_count++;
			} else {
				$inactive_count++;
			}
		}
		$total_count = $active_count + $inactive_count;

		$activation_rate = number_format(($active_count/$total_count) * 100, 2);
	}

	if ($is_school){
		//require_once MST_TEMPLATE_DIR . 'create-student.php';	
		//get_school_students_from_classrooms_and_move_them_to_school(bp_get_current_group_id()); //TODO remove after looping trough all schools
		/* echo '<pre>';
		var_dump( get_school_students($school_id) );
		echo '</pre>'; */
		//$year = get_option('school_year');
		/* $year = get_previous_year();
		echo '<pre>';
		var_dump( get_students_without_classroom_for_year($school_id, $year) );
		echo '</pre>'; */
	}
	require_once MST_TEMPLATE_DIR . 'create-student.php';	
	require_once MST_TEMPLATE_DIR . 'students-list.php';

}

