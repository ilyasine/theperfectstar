<?php 

add_action('content_courses', 'content_list' );
add_action('bp_init', 'setup_content_courses_group_nav' );
//add_filter('bp_nouveau_nav_has_count', 'content_count', 99, 3 );
//add_filter('bp_nouveau_get_nav_count', 'content_count_int', 99, 3 );
add_filter('groups_forbidden_names', 'content_courses_groups_forbidden' , 1);
add_filter('bp_get_search_default_text', 'content_search_form_text', 10, 1);
add_filter('bp_nouveau_feedback_messages', 'content_courses_feedback_message');

function content_courses_feedback_message($feedback_messages){
	$content_courses_feedback_message = array(
		'content-courses-loading'             => array(
			'type'    => 'loading',
			'message' => __( 'Requesting All courses. Please wait.', 'tprm-theme' ),
		),
	);
	
	$feedback_messages = array_merge($feedback_messages, $content_courses_feedback_message);

	return $feedback_messages;
}


function content_courses_groups_forbidden($names){
	
	array_push( $names , 'content' );

	return $names;
}

/*
* ************** Start content Callback functions  **************
 */

 /**
 * Change content search form text
 *
 * @since V2
 * @param string $default_text to change.
 * @return string $default_text
 */

function content_search_form_text($default_text){
	if( function_exists('is_content_page') && is_content_page() ){ 
		$default_text = __( 'Search Course&hellip;', 'tprm-theme' );
	}

	return $default_text;
}


/**
 * Create new Group tab for Student courses
 *
 * @since V2
 */

function setup_content_courses_group_nav(){
    global $bp; 
    $user_access = false;
    $group_link = '';
	
    if( bp_is_active('groups') && !empty($bp->groups->current_group) ){

		$group_type = bp_groups_get_group_type( $bp->groups->current_group->id );

		$is_school = is_school($bp->groups->current_group->id) ? true : false;

		$user_access = $is_school ? is_TPRM_manager() : $bp->groups->current_group->user_has_access;
		
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        //$user_access = $bp->groups->current_group->user_has_access;
	
		if ( is_TPRM_leader() && !$is_school ) {
			bp_core_new_subnav_item( array( 
				'name' =>  __( 'Content', 'tprm-theme' ),
				'slug' => 'content',
				'parent_url' => $group_link, 
				'parent_slug' => $bp->groups->current_group->slug,
				'screen_function' => 'content_bp_group', 
				'position' => 0, 
				'user_has_access' => $user_access, 
				//'site_admin_only' => true,
				'item_css_id' => 'content' 
			));
		}
    }

}


function content_count( $count, $nav_item, $displayed_nav ) {

	if ( $nav_item->slug == 'content' ) {
		$count = true;
	}

    return $count;

}


function content_count_int( $count, $nav_item, $displayed_nav ) {

	if ( $nav_item->slug == 'content' ) {

		$group_members = groups_get_group_members(array(
			'group_id' => bp_get_current_group_id()	
		));

		$count = $group_members['count'];

	}

    return $count;

}

function content_bp_group() {

    add_action('bp_template_title', 'content_courses_screen_title');
    add_action('bp_template_content', 'content_courses_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }

}

function content_courses_screen_title() {
	_e( 'Student courses', 'tprm-theme' );
}

function content_courses_screen_content() {
	?>
		<!-- <div id="bp-ajax-loader"><?php bp_nouveau_user_feedback('content-courses-loading');?></div> -->
	<?php
	do_action('content_courses');
}


/**
 * Student All courses content
 *
 * @since V2
 */

function content_list(){

	$attr = array(
        'post_status' => 'publish',    // Only show published courses
        'order' => 'DESC',             // Order courses in descending order
        //'orderby' => 'ID',             // Order by ID
        //'show_thumbnail' => 'false',    // Show thumbnails
        'show_content' => 'true',      // Show content excerpt
        'course_grid' => false,       // Display courses in a grid layout
        'progress_bar' => 'true',      // Show progress bar (if applicable)
        //'category_name' => 'featured', // Filter by a specific category slug, e.g., 'featured'
    );

	$course_content = ld_course_list($attr);

    if ( !empty($course_content) ) {
        echo $course_content;
    } else {
        _e('No courses available.');
    }
}
