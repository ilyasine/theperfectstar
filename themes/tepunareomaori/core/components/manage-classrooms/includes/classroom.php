<?php 




/*
* ************** Classes  **************
 */

add_action('bp_actions', 'TPRM_remove_group_tabs' );
add_action('bp_actions', 'TPRM_remove_profile_settings_general_tab' );
add_action('groups_setup_nav', 'TPRM_custom_disable_group_members' );
add_filter('bp_nouveau_get_groups_directory_nav_items', 'TPRM_hide_all_groups_create');
add_filter('hide_groups_search', 'TPRM_hide_groups_search_for_non_admin', 1);
add_filter('hide_header_forum', '__return_true');
add_action('bp_template_redirect', 'hide_all_members_directory' );
add_filter('wp_get_nav_menu_items', 'hide_menu_items_based_subscription', 10, 3 );
add_filter('bp_nouveau_get_nav_count', 'exclude_tprm_admins_from_members_count', 99, 3);
add_filter('bp_after_has_groups_parse_args', 'exclude_parent_groups_from_groups_loop');
add_filter('bp_nouveau_get_nav_count', 'exclude_parent_groups_from_groups_count', 99, 3);
add_action('wp_ajax_send_students_ids', 'send_students_ids');
add_action('wp_ajax_tprm_ajax_logout_group', 'TPRM_ajax_logout_group');

/**
 *  Remove edit Tab
 *
 * @since V2
 */

function TPRM_remove_profile_settings_general_tab() {

	if( bp_is_active( 'xprofile' ) && ! is_tprm_admin() ) :	

		bp_core_remove_subnav_item( 'settings', 'general' );
		bp_core_remove_subnav_item( 'profile', 'edit' );

	endif;

}

/**
 * Hide menu items based on membership and profile
 *
 * @since V2
 * @return Array
 */

function hide_menu_items_based_subscription( $items, $menu, $args ) {

	// handle roles
	if ( is_user_logged_in() ) {
		// Get the current user
		$current_user = wp_get_current_user();

		// Check if the user has a specific role (e.g., teacher or director)
		if ( in_array( 'school_staff', $current_user->roles ) || in_array( 'school_principal', $current_user->roles ) ) {
			// Hide items for teachers and directors
			if ( $menu->term_id === 344 ) { // english menu
				$items_to_unset = array( 16195, 17830 ); // courses + student textbook

				foreach ( $items as $key => $item ) {
					// Check if the object is a nav_menu_item and the item ID should be unset
					if ( in_array( $item->ID, $items_to_unset ) ) {
						unset( $items[ $key ] );
					}
				}
			}
			if ( $menu->term_id === 345 ) { // french menu
				$items_to_unset = array( 16208, 17831 ); // cours + Manuel de l’élève

				foreach ( $items as $key => $item ) {
					// Check if the object is a nav_menu_item and the item ID should be unset
					if ( in_array( $item->ID, $items_to_unset ) ) {
						unset( $items[ $key ] );
					}
				}
			}
		} elseif ( in_array( 'school_student', $current_user->roles ) || in_array( 'tprm-student', $current_user->roles ) ) {

			// Hide items for students
			if ( $menu->term_id === 344 ) { // english menu
				$items_to_unset = array( 16202, 16204, 56154 ); // classrom + reports + need help

				foreach ( $items as $key => $item ) {
					// Check if the object is a nav_menu_item and the item ID should be unset
					if ( in_array( $item->ID, $items_to_unset ) ) {
						unset( $items[ $key ] );
					}
				}
			}
			if ( $menu->term_id === 345 ) { // french menu
				$items_to_unset = array( 16223, 16219, 56153 ); // Classes + Résultats + Support

				foreach ( $items as $key => $item ) {
					// Check if the object is a nav_menu_item and the item ID should be unset
					if ( in_array( $item->ID, $items_to_unset ) ) {
						unset( $items[ $key ] );
					}
				}
			}
		}
	}

	// handle subscription
	if ( ! is_tprm_admin() && ! is_active_member() && is_user_logged_in() ) {
		if ( $menu->term_id === 344 || $menu->term_id === 345 ) {
			foreach ( $items as $key => $item ) {		
				unset( $items[$key] );
			}
		}
	}
	
	return $items;
}

/**
 * Remove Send Invites & Manage Group tab
 *
 * @since V2
 */

function TPRM_remove_group_tabs() {  

    $slug = bp_get_current_group_slug();
    bp_core_remove_subnav_item( $slug, 'invite' );
    
    if ( ! bp_is_group() || is_super_admin() || is_tprm_admin() ) {
        return;
    }

    bp_core_remove_subnav_item( $slug, 'admin' );

    // Add the admin subnav slug you want to hide in the
    // following array
    $hide_tabs = array(
        'group-avatar' => 1,
        'delete-group' => 1,
        'edit-details' => 1,
        'group-settings' => 1,
        'group-cover-image' => 1,
        'manage-members' => 1,
        'Documentation' => 1,
        'courses' => 1,
    );
    $parent_nav_slug = bp_get_current_group_slug() . '_manage';
    // Remove the nav items
    foreach ( array_keys( $hide_tabs ) as $tab ) {
        // Since 2.6, You just need to add the 'groups' parameter at the end of the bp_core_remove_subnav_item
        bp_core_remove_subnav_item( $parent_nav_slug, $tab, 'groups' );
    }
    // You may want to be sure the user can't access
    if ( ! empty( $hide_tabs[ bp_action_variable( 0 ) ] ) ) {
        bp_core_add_message( 'Sorry, but this part is restricted to super admins!', 'error' );
        bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) );
    }
		
} 


/**
 * @since V2
 *
 * @param array $nav_items to edit
 * @return array $nav_items 
 */ 

function TPRM_hide_all_groups_create($nav_items) {

	if( ! is_tprm_admin() && is_user_logged_in() ){

		echo '<style>.buddypress-wrap .bp-subnavs .component-navigation li#groups-personal a .count {display: inline-block !important;}</style>';
		echo '<style>.buddypress-wrap .bp-subnavs .component-navigation li#groups-personal a:after {display: none !important;}</style>';

		if (isset($nav_items['create'])) {
			unset($nav_items['create']);
		}
		if (isset($nav_items['all'])) {
			unset($nav_items['all']);
		}
		if (isset($nav_items['personal'])) {
			$nav_items['personal']['text'] = __('My Classrooms', 'tprm-theme' ) ;
		}
	}

	return $nav_items;
}


/**
 *  Hide groups search for non admins
 *
 * @since V2
 * @return bool Returns false on failure, True on success.
 */ 

function TPRM_hide_groups_search_for_non_admin($hide){

	if( ! is_tprm_manager() ) $hide = true;
	
	return $hide;
}


/**
 * @since V2
 *
 * @param int $count      The group members count.
 * @param object $nav_item The component the navigation is attached to.
 * @param object $displayed_nav The component the navigation is attached to.
 * @return int the group members count
 */ 

function exclude_tprm_admins_from_members_count( $count, $nav_item, $displayed_nav ) {

    if ('groups' === $displayed_nav && ('members' === $nav_item->slug || 'all-members' === $nav_item->slug)) {
        // Exclude KWF admins count
        $admins_count = get_tepunareomaori_admins_count();
        $count = BP_Groups_Group::get_total_member_count(bp_get_current_group_id()) - $admins_count;
    }

    return $count;
}



/**
 * Enable grid view for classes inside school group
 *
 * @since V2
 * @return int The total tepunareomaori admins count.
 */

function get_tepunareomaori_admins_count() {

	$group_members = groups_get_group_members( array(
		'group_id'            => bp_get_current_group_id(),
		'per_page'            => false,
		'page'                => false,
		'exclude_admins_mods' => false,
		'exclude_banned'      => true,
		'exclude'             => false,
		'group_role'          => array('admin'),
		'search_terms'        => false,
		'type'                => 'first_joined') );
		;	

    $count = 0;
    foreach ( $group_members['members'] as $member ) {
        $user = get_userdata( $member->ID );
		if ( in_array( 'administrator', (array) $user->roles ) || 
		bp_get_member_type( $member->ID ) == 'tepunareomaori' ) {
			$count++;
		}
		
    }
    return $count;
}

 /**
 * Hide Group members for everyone except Site Admin, Group admin and group mods
 *
 * @since V2
 */
	
function TPRM_custom_disable_group_members() {

	if ( ! bp_is_group() ) {
		return;
	}
	$group_id = bp_get_current_group_id();
	$user_id  = get_current_user_id();

	/* if ( is_super_admin() || groups_is_user_admin( $user_id, $group_id ) || groups_is_user_mod( $user_id, $group_id ) ) {
		return;
	} */

    if( ! is_tprm_admin() ){
        bp_core_remove_subnav_item( groups_get_current_group()->slug, 'members' );
    }
	

    // remove messages tab
	bp_core_remove_subnav_item( groups_get_current_group()->slug, 'messages' );

}

/**
 * Hide Members Directory from everyone.
 * 
 * @since V2
 */
function hide_all_members_directory() {
    if ( bp_is_members_directory() && ! is_tprm_admin()) {
        bp_do_404();
        load_template( get_404_template() );
        exit( 0 );
    }
}


/**
 * Get the parent groups of the current user
 *
 * @since V2
 * @return array
 */

/* function get_parent_groups() {
    // Get the current user ID
    $user_id = get_current_user_id();

    // Get the groups of the current user
    $groups = groups_get_user_groups($user_id);

    // Initialize an empty array for the parent groups
    $parent_groups = array();

    // Loop through the groups
    foreach ($groups['groups'] as $group_id) {
        // Get the group object
        $group = groups_get_group($group_id);

        // Check if the group has a parent
        if ($group->parent_id) {
            // Add the parent group ID to the array
            $parent_groups[] = $group->parent_id;
        }
    }

    // Remove any duplicate values from the array
    $parent_groups = array_unique($parent_groups);

    // Return the parent groups array
    return $parent_groups;
} */


function get_schools(){
    $schools = array();
    $args = array(
        'show_hidden' => true,
        'per_page' => -1,
        'parent_id' => 0,
        'fields' => 'ids',
    );

    //TODO for directors and it admin

    // TODO : add user_id => bp_loggedin_user_id() to args array to get school 

    $parent_groups = array();
    
    if( is_tprm_admin()){       
        $schools = groups_get_groups($args)["groups"];
    }else{
        $groups = groups_get_user_groups(bp_loggedin_user_id());
        // Loop through the groups
        foreach ($groups['groups'] as $group_id) {
            // Get the group object
            $group = groups_get_group($group_id);

            // Check if the group has a parent
            if ($group->parent_id) {
                // Add the parent group ID to the array
                $parent_groups[] = $group->parent_id;
            }
        }
        $schools = array_unique( $parent_groups);
    }

    return $schools;
}

function get_parent_groups() {
    // Get the current user ID
    $user_id = get_current_user_id();

    // Initialize an empty array for the parent groups
    $parent_groups = array();

    // Check if the user is an admin
    if (current_user_can('administrator')) {
        // Get all groups
        $all_groups = groups_get_groups(array('show_hidden' => true,  ));

        // Loop through all groups
        foreach ($all_groups['groups'] as $group_id) {
            // Check if the group has a parent
            $group = groups_get_group($group_id);

            // Check if the group has a parent
            if ($group->parent_id) {
                // Add the parent group ID to the array
                $parent_groups[] = $group->parent_id;
            }
        }
    } else {
        // Get the groups of the current user
        $groups = groups_get_user_groups($user_id);

        // Loop through the groups
        foreach ($groups['groups'] as $group_id) {
            // Get the group object
            $group = groups_get_group($group_id);

            // Check if the group has a parent
            if ($group->parent_id) {
                // Add the parent group ID to the array
                $parent_groups[] = $group->parent_id;
            }
        }
    }

    // Remove any duplicate values from the array
    $parent_groups = array_unique($parent_groups);

    // Return the parent groups array
    return $parent_groups;
}


/**
 * Get the year groups of the current user
 *
 * @since V3
 * @return array
 */

function get_groups_year() {
    // Get the current user ID
    $user_id = get_current_user_id();

    // Get the groups of the current user
    $groups = groups_get_user_groups($user_id);

    // Initialize an empty array for the parent groups
    $groups_year = array();

    // Loop through the groups
    foreach ($groups['groups'] as $group_id) {

		//Skip Parent group
		if (in_array($group_id, get_schools())) {
            continue;
        }
		
		$group_year = groups_get_groupmeta($group_id, 'ecole_year');

		$groups_year[] = $group_year;

    }

    // Remove any duplicate values from the array
    $groups_year = array_unique($groups_year);

    // Return the parent groups array
    return $groups_year;
}

function get_groups_with_year($year) {
    // Initialize an empty array to store group IDs
    $group_ids = array();

    // Get all group IDs
    $user_id = get_current_user_id();

    // Get the groups of the current user
    $groups = groups_get_user_groups($user_id);

    // Loop through all group IDs
    foreach ($groups['groups'] as $group_id) {
        // Check if the group has the meta 'ecole_year' equal to the selected year
        $group_year = groups_get_groupmeta($group_id, 'ecole_year');

        //Skip Parent group
		if (in_array($group_id, get_schools())) {
            continue;
        }
        if ($group_year == $year) {
            // Add group ID to the array
            $group_ids[] = $group_id;
        }
    }

    return $group_ids;
}

add_filter( 'bp_nouveau_feedback_messages', 'modify_groups_loop_none_message' );

function modify_groups_loop_none_message( $feedback_messages ) {
    // Check if the message we want to modify exists
    if ( function_exists('bp_is_group_subgroups') && isset( $feedback_messages['groups-loop-none']) && isset($feedback_messages['directory-groups-loading'])  ) {
        // no classrooms found
        if(bp_is_group_subgroups()) : 
            $school_id = bp_get_current_group_id();
            $previous_year = get_previous_year();
            $TPRM_school_year = school_implementation_year($school_id);
            $classrooms_for_previous_year = get_school_classrooms_for_year($school_id, $previous_year);
            if ($TPRM_school_year > 1 && !empty($classrooms_for_previous_year)) {
                if(is_tprm_manager()){
                    $feedback_messages['groups-loop-none']['message'] = __(
                        'Sorry, no classrooms were found for this year. Please press the ❝<strong>Duplicate Previous Year Structure</strong>❞ button bellow to duplicate all classrooms from the previous year with one click.', 
                        'tprm-theme'
                    );
                }else{
                    $feedback_messages['groups-loop-none']['message'] = __(
                        'Sorry, no classrooms were found for this year. Please contact your School Leader to create Classrooms', 
                        'tprm-theme'
                    );
                }
                
            } else {
                if(is_tprm_manager()){
                    $feedback_messages['groups-loop-none']['message'] = __(
                        'Sorry, no classrooms were found for this year, and this is your first year on tepunareomaori. Please press the ❝<strong>Create Classroom</strong>❞ button above to start creating your classrooms.', 
                        'tprm-theme'
                    );
                }else{
                    $feedback_messages['groups-loop-none']['message'] = __(
                        'Sorry, no classrooms were found for this year. Please contact your School Leader to create Classrooms', 
                        'tprm-theme'
                    );
                }
            }
            
        endif;
        // loading classrooms
        $feedback_messages['directory-groups-loading']['message'] = __( 'Loading classrooms of the school. Please wait.', 'tprm-theme' );

        //no teachers
        if( function_exists('bp_is_group') && bp_is_group() && strpos($_SERVER['REQUEST_URI'], "teachers") !== false ){
            $feedback_messages['group-members-none']['message'] = __( 'Sorry, no teachers were found.', 'tprm-theme' );
        }
        if( function_exists('bp_is_group') && bp_is_group() && strpos($_SERVER['REQUEST_URI'], "students") !== false ){ 
            $feedback_messages['group-members-none']['message'] = __( 'Sorry, no students were found.', 'tprm-theme' );
        }
        //group-members-none

    }

    return $feedback_messages;
}


/**
 * Filter groups by year and school
 *
 * @since V3
 */

function groups_year_school_filter( $querystring, $object ) {
   
    if ( 'groups' !== $object ) {
        return $querystring;
    }

    $year = isset($_POST['group_year']) ? sanitize_text_field($_POST['group_year']) : get_option( 'school_year' );

    $meta_query = array(
        array(
            'key'     => 'ecole_year',
            'value'   => $year,
            'compare' => 'LIKE'
        ),
        /* 'orderby' => 'meta_value_num',
        'meta_key' => 'classroom_level',
        'order' => 'ASC' */
    );

    $querystring = bp_parse_args( $querystring );
    $querystring['scope']    = 'personal';
    /* $querystring['page']     = 1;
    $querystring['per_page'] = '20'; */
    $querystring['user_id'] = 0;
    $querystring['meta_query'] = $meta_query;

    if( function_exists('bp_is_group_subgroups') && bp_is_group_subgroups()){
		$school = bp_get_current_group_id();
        $querystring['parent_id'] = $school;
	}
    if( function_exists('bp_is_groups_directory') && bp_is_groups_directory()){
        if(isset($_POST['group_school'])){
            $school = sanitize_text_field($_POST['group_school']);
            $querystring['parent_id'] = $school;
        }else{
            unset( $querystring['parent_id'] );
        }
	}

    return http_build_query( $querystring );
}

add_filter( 'bp_ajax_querystring', 'groups_year_school_filter', 20, 2 );


/**
 * Get the groups type IDs of the groups of which a current user is a member.
 *
 * @since V2
 * @return array
 */

function get_groups_type() {
    // Get the current user ID
    $user_id = get_current_user_id();

    // Get the groups of the current user
    $groups = groups_get_user_groups($user_id);

    // Initialize an empty array for the parent groups
    $group_types = array();

    // Loop through the groups
    foreach ($groups['groups'] as $group_id) {

		$group_type = bp_groups_get_group_type( $group_id );

		// Get the group type id for non schools
		if ( $group_type != 'tprm-school' ) {
			$group_type = bp_group_get_group_type_id( $group_type );
		}

        // Check if the group type is not empty
        if ( !empty($group_type) ) {
            // Add the group type to the array
            $group_types[] = $group_type;
        }
	
	} 

    // Remove any duplicate values from the array
    $group_types = array_unique($group_types);

    // Return the parent groups array
    return $group_types;
}

/**
 * Get the subgroups of a given parent group as a numeric array of IDs
 *
 * @since V2
 * @param int parent_id
 * @return array
 */

function get_subgroups($parent_id) {
    // Initialize an empty array for the subgroups
    $subgroups = array();

    // Get the groups of the parent group
    $groups = bp_get_descendent_groups($parent_id);

    // Loop through the groups
    foreach ($groups as $group) {
        // Add the group ID to the array
        $subgroups[] = $group->id;
    }

    // Return the subgroups array
    return $subgroups;
}

/**
 * Exclude parent groups from the groups loop
 *
 * @since V2
 * @param array query_args 
 * @return array
 */

function exclude_parent_groups_from_groups_loop($query_args) {
    $schools_id = get_schools();

    if (count($schools_id) > 0) {
        $exclude_ids = implode(',', $schools_id);
        $query_args['exclude'] = $exclude_ids;
    }

    return $query_args;
}


function exclude_parent_groups_from_groups_count($count, $nav_item, $displayed_nav) {

	if (bp_is_groups_directory() && 'directory' === $displayed_nav && is_school_principal()) {
		$schools_id = get_schools();
 
		if (count($schools_id) > 0) {
			$exclude_ids = implode(',', $schools_id);

			$total_groups_count = bp_get_total_group_count_for_user(get_current_user_id());
 
			// Subtract the count of parent groups
			$count = $total_groups_count - count($schools_id);

		}
	}
 
	return $count;
}
 

/**
 * Get classrooms for a given school id
 *
 * @since V2
 * @param int group_id of the parent group we want to get the classrooms for
 * @return string of subgroups_ids separated by comma
 */

function get_school_classes($parent_id) {

    // Get the subgroups of the given parent group as an array of IDs
    $subgroups = get_subgroups($parent_id);

    // Convert the array to a comma-separated string
    $subgroups_string = implode(',', $subgroups);

    // Return the string of subgroup IDs
    return $subgroups_string;

}


//TODO : Debug why this is not working

function exclude_admins_from_members_loop($query_args) {
    // Check if it's the members loop
    if (isset($query_args['object']) && 'members' === $query_args['object']) {
        // Get the administrator role ID
        $admin_role = get_role('administrator');

        // Get all users with the administrator role
        $admins = get_users(array('role' => $admin_role->name));

        // Extract the IDs of administrators
        $admin_ids = wp_list_pluck($admins, 'ID');

        // Exclude administrators from the members loop
        if (!empty($admin_ids)) {
            $query_args['exclude'] = implode(',', $admin_ids);
        }
    }

    return $query_args;
}
add_filter('bp_after_has_members_parse_args', 'exclude_admins_from_members_loop');
add_filter('bp_ajax_querystring', 'exclude_admins_from_members_loop',20,1);

/**
 * Exclude Users from BuddyPress Members List by WordPress role.
 *
 * @param array $args args.
 *
 * @return array
 */
//TODO : Debug why this is not working
function TPRM_exclude_users_by_role( $args ) {
	// do not exclude in admin.
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return $args;
	}

	$excluded = isset( $args['exclude'] ) ? $args['exclude'] : array();

	if ( ! is_array( $excluded ) ) {
		$excluded = explode( ',', $excluded );
	}

	$role     = 'administrator';// change to the role to be excluded.
	$user_ids = get_users( array( 'role' => $role, 'fields' => 'ID' ) );

	$excluded = array_merge( $excluded, $user_ids );

	$args['exclude'] = $excluded;

	return $args;
}

add_filter( 'bp_after_has_members_parse_args', 'TPRM_exclude_users_by_role' );


/**  Disconnect classroom */ 

/**
 * @since V2
 *
 * Add an AJAX endpoint to send students_ids
 */ 

function send_students_ids() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'disconnect_classroom') {
        // Verify nonce
        check_ajax_referer('disconnect_classroom_nonce', 'security');       

        $students_ids = array();

        $group_members = groups_get_group_members(array(
            'group_id'            => bp_get_current_group_id(),
            'exclude_admins_mods' => true,
            'exclude_banned'      => true,
            'exclude'             => false,
            'group_role'          => array('member'),
            'search_terms'        => false,
        ));

        foreach ($group_members['members'] as $member) {
            $students_ids[] = $member->ID;
        }

        // Send students_ids to the JavaScript function
        wp_send_json_success(array('students_ids' => $students_ids));
    }
}


/**
 * @since V2
 *
 * Handle the AJAX logout group callback 
 * process logout using students_ids
 */

function TPRM_ajax_logout_group() {

    if (isset($_POST['payload']) && $_POST['payload'] == 'disconnect_classroom') {
        // Verify nonce
        check_ajax_referer('disconnect_classroom_nonce', 'security');

        // Get the user IDs from the POST data
        $user_ids = $_POST['user_ids'];

        // Loop through the user IDs and log them out
        foreach ($user_ids as $user_id) {

			if (is_user_logged_in($user_id)) {
				// Get all sessions for the user with ID $user_id
				$sessions = WP_Session_Tokens::get_instance($user_id);
		
				// We have got the sessions, destroy them all!
				$sessions->destroy_all();
			}

        }

		$group_name = bp_get_current_group_name();

        // Send a success response
		wp_send_json_success(array(
			'disconnected_message' => sprintf( __( 'Your students from %s classroom have been successfully disconnected', 'tprm-theme' ), $group_name )
        ));

    }
}

/**
 * Get teachers list for group directory.
 *
 * @since v3
 *
 * @param int   $group_id ID of the group.
 * @return string teachers list html.
 */
function classroom_loop_teachers( $group_id = 0 ) {

	if ( ! $group_id ) {
		$group_id = bp_get_group_id();
	}

	if ( ! $group_id ) {
		return '';
	}

    $teachers = get_classroom_teachers($group_id); ?>
        <?php if( !empty($teachers) ) {
            foreach ( $teachers as $teacher ) {
                ?>
                <div class="classroom_teacher">
                    <a class="teacher-avatar" href="<?php echo esc_url( bp_core_get_user_domain( $teacher ) ); ?>">
                        <img src="<?php echo TPRM_IMG_PATH . 'avatar.svg'; ?>" class="avatar" alt=""/>
                    </a>
                    <a target="_blank" class="teacher-item" href="<?php echo esc_url( bp_core_get_user_domain( $teacher ) ); ?>">
                        <?php echo bp_core_get_user_displayname($teacher);  ?>
                    </a>
                </div>
                <?php
            }   
        }
        ?>
    <?php
}

/**
 * Check if a classroom has a previous classroom meta.
 *
 * This function determines if the specified classroom has a 'previous_classroom' meta
 * associated with it.
 *
 * @since v3
 *
 * @param int $classroom_id ID of the classroom.
 * @return bool True if the classroom has a 'previous_classroom' meta, false otherwise.
 */
function has_previous_classroom($classroom_id) {
    // Get the previous_classroom meta value
    $previous_classroom = groups_get_groupmeta($classroom_id, 'previous_classroom', true);

    // Check if the meta value exists and is not empty
    return !empty($previous_classroom);
}

/**
 * Get the previous classroom ID for a given classroom.
 *
 * This function returns the value of the 'previous_classroom' meta for the specified classroom ID.
 *
 * @since v3
 *
 * @param int $classroom_id ID of the classroom.
 * @return mixed The previous classroom ID if it exists, or false if not set.
 */
function get_previous_classroom($classroom_id) {
    // Retrieve the 'previous_classroom' meta value
    return groups_get_groupmeta($classroom_id, 'previous_classroom', true);
}
