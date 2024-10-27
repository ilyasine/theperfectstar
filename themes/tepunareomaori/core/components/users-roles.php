<?php 

add_action('init', 'register_tprm_roles');
add_action('set_user_role', 'assign_group_leader_role_to_teacher_school_principal_admin', 10, 2);
add_action('user_register', 'assign_group_leader_role_to_teacher_school_principal_admin', 10, 1);
add_action('profile_update', 'assign_group_leader_role_to_teacher_school_principal_admin', 10, 1);
add_filter('pre_option_default_role', 'set_student_as_default_role',99,1);
//add_action('init', 'is_user_id_tprm_member');  

/* 
* *** Users & roles Helper functions   ***
*/

/**
 * Display user language field to user profile for selecting language
 *
 * @since V3
 */
function user_language_field($user) {

	global $blog_id, $wpdb;
    
    $user_lang = $wpdb->get_blog_prefix($blog_id) . 'lang';
    $user_language = get_user_meta($user->ID, $user_lang, true);

	ob_start();
    
    ?>
	<table class="form-table" role="presentation">
		<table class="form-table">
			<tr>
				<th>
					<label for="user_language"><?php _e('User Language', 'tprm-theme'); ?></label>
					<span class="dashicons dashicons-translation" aria-hidden="true"></span>
				</th>
				<td>
					<select name="user_language" id="user_language">
						<option value="en" <?php selected($user_language, 'en'); ?>><?php _e('English', 'tprm-theme'); ?></option>
						<option value="fr" <?php selected($user_language, 'fr'); ?>><?php _e('French', 'tprm-theme'); ?></option>
						<option value="bi" <?php selected($user_language, 'bi'); ?>><?php _e('Bilingual', 'tprm-theme'); ?></option>
					</select>
				</td>
			</tr>
		</table>
    </table>
	
    <?php

	return ob_get_clean();
}

/**
 * Display user language field to user profile for selecting language
 *
 * @since V3
 */
function TPRM_user_language_field() {
	global $current_user, $user_id, $pagenow;

	if ( ! isset( $user_id ) && 'profile.php' === $pagenow ) {
		$user_id = $current_user->ID;
	}

	$user = new WP_User( $user_id );
	echo user_language_field($user);
}

// Function to display user language field in user creation
function TPRM_user_creation_language_field($user) {
	echo user_language_field($user);
}

// Add user language field to user profile
add_action('personal_options', 'TPRM_user_language_field');

// Add user language field to user creation
add_action('user_new_form', 'TPRM_user_creation_language_field');

/**
 * Save the selected language to user meta
 *
 * @since V3
 */
function save_user_language_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    global $blog_id, $wpdb;

    $user_lang = $wpdb->get_blog_prefix($blog_id) . 'lang';
    
    if (isset($_POST['user_language'])) {
        update_user_meta($user_id, $user_lang, $_POST['user_language']);
    }
}
add_action('personal_options_update', 'save_user_language_field');
add_action('edit_user_profile_update', 'save_user_language_field');

// Add 'User Language' column to users table
function add_user_language_column($columns) {
    $columns['user_language'] = __('User Language', 'tprm-theme');
	unset($columns['wfls_2fa_status']);
	unset($columns['posts']);
    return $columns;
}
add_filter('manage_users_columns', 'add_user_language_column');

// Display user's language in 'User Language' column
function display_user_language_column($value, $column_name, $user_id) {
    if ('user_language' == $column_name) {
        global $blog_id, $wpdb;
        
        $user_lang = $wpdb->get_blog_prefix($blog_id) . 'lang';
        $user_language = get_user_meta($user_id, $user_lang, true);
        
        return $user_language;
    }
    return $value;
}
add_action('manage_users_custom_column', 'display_user_language_column', 10, 3);


/**
 * Helper function to check if student has an active subscription
 *
 * @since V2
 */

 function is_active_student($id_user) {
	// Helper function to check if a user has a specific role
	$user_has_role = function ($role) use ($id_user) {
		$user = get_user_by('ID', $id_user);
		return ($user && in_array($role, $user->roles));
	};

	// Check if the user has the "student" role and has an active in Membership
	if ($user_has_role('school_student') && function_exists('wc_memberships_is_user_active_member') && wc_memberships_is_user_active_member($id_user, 'access-' . get_option('school_year'))) {
		return true;
	}

	// If neither condition is met, return false
	return false;
}

/**
 * Helper function to check if current user is a valid kwf member
 *
 * @since V2
 * @return Boolean
 */

function is_active_member(){

	$user = wp_get_current_user();

	$valid_roles = ['administrator', 'school_principal', 'school_staff', 'school_leader', 'library', 'libraries_manager'];

	if (function_exists('wc_memberships_is_user_active_member') && isset($user->roles) && is_array($user->roles)) {
		// Check for valid roles or member had an active subscription
		if (array_intersect($user->roles, $valid_roles) || wc_memberships_is_user_active_member($user->ID, 'access-' . get_option('school_year'))) {
			return true;
		}
		return false;
	}
}


	
/**
 * Helper function to check if curent user has active membership
 *
 * @since V2
 * @return Boolean
 */

function user_has_active_membership(){

	if( function_exists('wc_memberships_is_user_active_member') && wc_memberships_is_user_active_member(get_current_user_id(), 'access-' . get_option('school_year') ) ){
		return true;
	}else{
		return false;
	}
	
}

/**
 * Helper function to check if current user is a valid kwf member
 *
 * @since V2
 * @return Boolean
 */

function is_tprm_member(){

	$user = wp_get_current_user();

	$valid_roles = ['administrator', 'school_principal', 'school_staff', 'school_leader', 'library', 'libraries_manager'];

	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for valid roles or member had a valid subscription
		if ( user_has_active_membership() || array_intersect($user->roles, $valid_roles) ) {
			// kwf member
			return true;
		}
		return false;
	}

}

/**
 * Helper function to check if current user is a valid kwf member by id
 *
 * @since V2
 * @return Boolean
 */

function is_user_id_tprm_member($user_id){

	$user = get_user_by( 'id', $user_id);

	$valid_roles = ['administrator', 'school_principal', 'school_staff', 'school_leader', 'library', 'libraries_manager'];

	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for valid roles or member had a valid subscription
		if ( user_has_active_membership() || array_intersect($user->roles, $valid_roles) ) {
			// kwf member
			return true;
		}
		return false;
	}

}



/* 
* *** Users & roles callback functions   ***
*/


/**
 * 
 * @global function to check if role is exists
 * 
 * @since V2
 */

function role_exists( $role ) {
    if ( ! empty( $role ) ) {
        return wp_roles()->is_role( $role );
    }

    return false;
}

/**
 * Create main roles to be used  : school_principal , Teacher , Student
 *
 * @since V2
 * @return void
 */

function register_tprm_roles() {

	global $wp_roles;

	$existed_roles = array('school_principal', 'school_staff', 'school_student', 'school_leader', 'library', 'libraries_manager');

	foreach( $existed_roles as $existed_role ){

		if ( role_exists( $existed_role ) ) :

			return;

		endif;
	}

	if (!isset($wp_roles))
		$wp_roles = new WP_Roles();


    $TPRM_roles = array(		
		'school_leader' => array(
			'role_slug' => 'school_leader',
			'role_name' => __( 'School Leader', 'tprm-theme' ),
			'capabilities' => array(
				"read" => true,
				"manage_school" => true,
				"group_leader" => true,
				"level_1" => false,
				"level_0" => true,
				"edit_groups" => true,
				"edit_published_groups" => true,
				"delete_group" => true,
				"delete_groups" => true,
				"delete_published_groups" => true,
				"publish_groups" => true,
				"manage_terms_group_categories" => true,
				"assign_terms_group_categories" => true,
				"edit_users" => true,
				"edit_essays" => true,
				"edit_others_essays" => true,
				"publish_essays" => true,
				"read_essays" => true,
				"read_private_essays" => true,
				"delete_essays" => true,
				"edit_published_essays" => true,
				"delete_others_essays" => true,
				"delete_published_essays" => true,
				"read_assignment" => true,
				"edit_assignments" => true,
				"edit_others_assignments" => true,
				"edit_published_assignments" => true,
				"delete_others_assignments" => true,
				"delete_published_assignments" => true,
				"tincanny_reporting" => true,
				"view_others_h5p_contents" => true,
				"view_h5p_contents" => true,
				"view_h5p_results" => true
			),
		),
		'school_principal' => array(
			'role_slug' => 'school_principal',
			'role_name' => __( 'School Principal', 'tprm-theme' ),
			'capabilities' => array(
				"read" => true,			
				"group_leader" => true,
				"level_1" => false,
				"level_0" => true,
				"edit_groups" => true,
				"edit_published_groups" => true,
				"delete_group" => true,
				"delete_groups" => true,
				"delete_published_groups" => true,
				"publish_groups" => true,
				"manage_terms_group_categories" => true,
				"assign_terms_group_categories" => true,
				"edit_users" => true,
				"edit_essays" => true,
				"edit_others_essays" => true,
				"publish_essays" => true,
				"read_essays" => true,
				"read_private_essays" => true,
				"delete_essays" => true,
				"edit_published_essays" => true,
				"delete_others_essays" => true,
				"delete_published_essays" => true,
				"read_assignment" => true,
				"edit_assignments" => true,
				"edit_others_assignments" => true,
				"edit_published_assignments" => true,
				"delete_others_assignments" => true,
				"delete_published_assignments" => true,
				"tincanny_reporting" => true,
				"view_others_h5p_contents" => true,
				"view_h5p_contents" => true,
				"view_h5p_results" => true
			),
		),
		'school_staff' => array(
			'role_slug' => 'school_staff',
			'role_name' => __( 'School Staff', 'tprm-theme' ),
			'capabilities' => array(
				"read" => true,
				"group_leader" => true,
				"level_1" => false,
				"level_0" => true,
				"edit_groups" => true,
				"edit_published_groups" => true,
				"delete_group" => true,
				"delete_groups" => true,
				"delete_published_groups" => true,
				"publish_groups" => true,
				"manage_terms_group_categories" => true,
				"assign_terms_group_categories" => true,
				"edit_users" => true,
				"edit_essays" => true,
				"edit_others_essays" => true,
				"publish_essays" => true,
				"read_essays" => true,
				"read_private_essays" => true,
				"delete_essays" => true,
				"edit_published_essays" => true,
				"delete_others_essays" => true,
				"delete_published_essays" => true,
				"read_assignment" => true,
				"edit_assignments" => true,
				"edit_others_assignments" => true,
				"edit_published_assignments" => true,
				"delete_others_assignments" => true,
				"delete_published_assignments" => true,
				"tincanny_reporting" => true,
				"view_others_h5p_contents" => true,
				"view_h5p_contents" => true,
				"view_h5p_results" => true
			),
		),
		'school_student' => array(
			'role_slug' => 'school_student',
			'role_name' => __( 'School Student', 'tprm-theme' ),
			'capabilities' => array(
				"level_0"=> true,
				"read"=> true,
				"read_others_assignments"=> true,
				"read_others_courses"=> true,
				"read_others_essays"=> true,
				"read_others_forums"=> true,
				"read_others_groupss"=> true,
				"read_others_job_listings"=> true,
				"read_others_pages"=> true,
				"read_others_posts"=> true,
				"read_others_products"=> true,
				"read_others_replies"=> true,
				"read_others_topics"=> true,
				"view_h5p_contents"=> true,
				"view_h5p_results"=> true,
				"view_others_h5p_contents"=> true,
			),
		),
		
	);

	foreach( $TPRM_roles as $TPRM_role ){

		if ( ! ( role_exists( $TPRM_role['role_slug'] ) ) ) :

			add_role($TPRM_role['role_slug'], $TPRM_role['role_name'], $TPRM_role['capabilities']);

		endif;
	}

	

}

/*$TPRM_roles = new WP_Roles();
$roles = $TPRM_roles->get_names();

 echo '<pre style="width: 50%; margin: auto">';
var_dump( $TPRM_roles );
echo '</pre>'; */

/**
 * Check if the user has the role of 'administrator'.
 *
 * @since V2
 * @param int|WP_User $user User ID or WP_User object to check.
 * @return bool True if the user has the role, false otherwise.
 */
function is_tprm_admin($user = null) {
    // If $user is not provided, use the current user.
    if (null === $user) {
        $user = wp_get_current_user();
    }

	 // Check if the user has either 'administrator' ( super admin with full privileges).
	 return in_array('administrator', (array)$user->roles);
}

/**
 * Check if the user has the role of 'school_student' .
 *
 * @since V2
 * @param int|WP_User $user User ID or WP_User object to check.
 * @return bool True if the user has the role, false otherwise.
 */
function is_student($user = null) {
    // If $user is not provided, use the current user.
    if (null === $user) {
        $user = wp_get_current_user();
    }

    // Check if the user has either 'school_student'
    return in_array('school_student', (array)$user->roles);
}

/**
 * Check if the passed user ID or WP_User object has the role of 'school_student'.
 *
 * @since V2
 * @param int|WP_User|null $user User ID or WP_User object to check. If null, return false.
 * @return bool True if the user has the role, false otherwise.
 */
function is_student_user($user = null) {
    // If $user is null, return false.
    if (null === $user) {
        return false;
    }

    // If a user ID is passed, retrieve the WP_User object.
    if (is_numeric($user)) {
        $user = get_user_by('ID', $user);
    }

    // Ensure that $user is a valid WP_User object before proceeding.
    if (!$user || !($user instanceof WP_User)) {
        return false;
    }

    // Check if the user has either 'school_student' or 'tprm-student' role.
    return in_array('school_student', (array)$user->roles);
}


/**
 * Check if the user is a School Principal.
 *
 * @since V2
 * @param int|WP_User $user User ID or WP_User object to check.
 * @return bool True if the user has the role, false otherwise.
 */
function is_school_principal($user = null) {
    // If $user is not provided, use the current user.
    if (null === $user) {
        $user = wp_get_current_user();
    }

    // Check if the user has both 'school_principal' and 'group_leader' roles.
    return in_array('school_principal', (array)$user->roles) && in_array('group_leader', (array)$user->roles);
}

/**
 * Check if the user is a School Leader.
 *
 * @since V2
 * @param int|WP_User $user User ID or WP_User object to check.
 * @return bool True if the user has the role, false otherwise.
 */
function is_school_leader($user = null) {
    // If $user is not provided, use the current user.
    if (null === $user) {
        $user = wp_get_current_user();
    }

    // Check if the user has both 'school_principal' and 'group_leader' roles.
    return in_array('school_leader', (array)$user->roles) && in_array('group_leader', (array)$user->roles);
}

/**
 * Check if the user is a teacher.
 *
 * @since V2
 * @param int|WP_User $user User ID or WP_User object to check.
 * @return bool True if the user has the role, false otherwise.
 */
function is_teacher($user = null) {
    // If $user is not provided, use the current user.
    if (null === $user) {
        $user = wp_get_current_user();
    }

    // Check if the user has both 'school_staff' and 'group_leader' roles.
    return in_array('school_staff', (array)$user->roles) && in_array('group_leader', (array)$user->roles);
}

/**
 *  Assign group_leader role to teacher and school_principal
 *
 * @since V2
 * @param int $user User ID.
 * @return void
 */
function assign_group_leader_role_to_teacher_school_principal_admin($user_id) {
    $user = get_userdata($user_id);
    
    if ($user) {
        $user_roles = $user->roles;

        // Check if the user has the roles 'school_staff' or 'school_principal'
        if (in_array('school_staff', $user_roles) || in_array('school_principal', $user_roles) || in_array('school_leader', $user_roles)) {
            $user->add_role('group_leader'); // Assign the 'group_leader' role
        }
    }	

}

/**
 * Helper function to check if the current user is a KWF leader
 *
 * @since V2
 * @since V3 , Updated to add new School Leader Role
 * @return Boolean
 */

 function is_tprm_leader() {
    $user = wp_get_current_user();

    $valid_roles = ['administrator', 'school_principal', 'school_staff', 'group_leader', 'school_leader', 'library', 'libraries_manager'];

    foreach ($valid_roles as $role) {
        if (in_array($role, $user->roles)) {
            return true; // Return true if the user has a valid role
        }
    }

    return false; // Return false if none of the valid roles are found
}

/**
 * Helper function to check if the current user is a KWF leader
 *
 * @since V3 
 * @return Boolean
 */

 function is_tprm_manager() {
    $user = wp_get_current_user();

    $valid_roles = ['administrator', 'school_principal', 'school_leader'];

    foreach ($valid_roles as $role) {
        if (in_array($role, $user->roles)) {
            return true; // Return true if the user has a valid role
        }
    }

    return false; // Return false if none of the valid roles are found
}


/**
 * Hijack the option, the role will follow!
 *
 * @since V2
 * @param string value of an existing default role
 * @return string
 */
function set_student_as_default_role($default_role){
	$default_role = 'school_student';
    return $default_role;
}

/**
 * Check if the user is a Library.
 *
 * @since V3
 * @param int|WP_User $user User ID or WP_User object to check.
 * @return bool True if the user has the role, false otherwise.
 */
function is_library($user = null) {
    // If $user is not provided, use the current user.
    if (null === $user) {
        $user = wp_get_current_user();
    }

    // Check if the user has both 'school_staff' and 'group_leader' roles.
    return in_array('library', (array)$user->roles);
}

/**
 * Check if the user is a libraries_manager.
 *
 * @since V3
 * @param int|WP_User $user User ID or WP_User object to check.
 * @return bool True if the user has the role, false otherwise.
 */
function is_libraries_manager($user = null) {
    // If $user is not provided, use the current user.
    if (null === $user) {
        $user = wp_get_current_user();
    }

    // Check if the user has both 'school_staff' and 'group_leader' roles.
    return in_array('libraries_manager', (array)$user->roles);
}


function add_user_role_to_body_class($classes) {
    // Check if the user is logged in
    if (is_user_logged_in()) {
        // Get the current user object
        $current_user = wp_get_current_user();
        
        // Add each role of the user to the body class
        if (!empty($current_user->roles)) {
            foreach ($current_user->roles as $role) {
                $classes[] = 'role-' . esc_attr($role);
            }
        }
    }
    
    return $classes;
}
add_filter('body_class', 'add_user_role_to_body_class');
