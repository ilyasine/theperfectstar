

<?php 

/* manage teachers */
add_action('bp_init', 'setup_teachers_group_nav' );
function setup_teachers_group_nav(){
    global $bp; 
    $user_access = false;
    $group_link = '';
	
    if( bp_is_active('groups') && !empty($bp->groups->current_group) ){

		$group_type = bp_groups_get_group_type( $bp->groups->current_group->id );
		
        $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
        $user_access = $bp->groups->current_group->user_has_access;
	
		if ( is_tprm_manager() ) {
			bp_core_new_subnav_item( array( 
				'name' =>  __( 'Teachers', 'tprm-theme' ),
				'slug' => 'teachers',
				'parent_url' => $group_link, 
				'parent_slug' => $bp->groups->current_group->slug,
				'screen_function' => 'teachers_bp_group', 
				'position' => 50, 
				'user_has_access' => $user_access, 
				//'site_admin_only' => true,
				'item_css_id' => 'teachers' 
			));
		}
    }

}

function teachers_bp_group() {

    add_action('bp_template_title', 'teachers_screen_title');
    add_action('bp_template_content', 'teachers_screen_content');

    $templates = array('groups/single/plugins.php', 'plugin-template.php');
    if (strstr(locate_template($templates), 'groups/single/plugins.php')) {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
    } else {
        bp_core_load_template(apply_filters('bp_core_template_plugin', 'plugin-template'));
    }

}

function teachers_screen_title() {
	_e( 'Teachers', 'tprm-theme' );
}

function teachers_screen_content() {

	/* if(is_school(bp_get_current_group_id())){
		get_school_teachers_from_classrooms_and_move_them_to_school(bp_get_current_group_id()); //TODO remove after looping trough all schools
		require_once MTE_TEMPLATE_DIR . 'create-teacher.php' ;
		require_once MTE_TEMPLATE_DIR . 'school-teachers-list.php' ;
		
	}else{
		require_once MTE_TEMPLATE_DIR . 'classroom-teachers-list.php' ;
	}	 */
	/* require_once MTE_TEMPLATE_DIR . 'create-teacher.php' ;
	require_once MTE_TEMPLATE_DIR . 'teachers-list.php' ; */
	$is_school = is_school(bp_get_current_group_id()) ? true : false;
    //if ($is_school) get_school_teachers_from_classrooms_and_move_them_to_school(bp_get_current_group_id()); //TODO remove after looping trough all schools
	if ($is_school) require_once MTE_TEMPLATE_DIR . 'create-teacher.php' ;
			
	require_once MTE_TEMPLATE_DIR . 'teachers-list.php';

}

function teachers_groups_forbidden($names){
	
	array_push( $names , 'teachers' );

	return $names;
}

function teachers_count( $count, $nav_item, $displayed_nav ) {

	if ( $nav_item->slug == 'teachers' ) {
		$count = true;
	}

    return $count;

}

add_filter('bp_nouveau_nav_has_count', 'teachers_count', 99, 3 );

add_filter('groups_forbidden_names', 'teachers_groups_forbidden' , 1);

add_filter('bp_nouveau_get_nav_count', 'teachers_count_int', 99, 3);

function teachers_count_int($count, $nav_item, $displayed_nav) {
    if ($nav_item->slug == 'teachers') {
        $group_id = bp_get_current_group_id();
        $group_members = groups_get_group_members(array(
            'group_id' => $group_id,
            'group_role' => array('admin', 'mod'),
        ));

        $teacher_count = 0;

        foreach ($group_members['members'] as $member) {
            $user_id = $member->ID;
            $user = get_userdata($user_id);
            
            // Check if user roles include 'school_staff' role
            if ($user && in_array('school_staff', $user->roles)) {
                $teacher_count++;
            }
        }

        //$group_type = bp_groups_get_group_type($group_id);

        if (is_school($group_id)) {
            $group_members = groups_get_group_members(array(
                'group_id' => $group_id,
                'group_role' => array('member'),
            ));

            foreach ($group_members['members'] as $member) {
                $user_id = $member->ID;
                $user = get_userdata($user_id);
                
                // Check if user roles include 'school_staff' role
                if ($user && in_array('school_staff', $user->roles)) {
                    $teacher_count++;
                }
            }
        }

        $count = $teacher_count;
    }

    return $count;
}
