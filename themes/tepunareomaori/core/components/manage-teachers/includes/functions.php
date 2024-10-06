<?php 

function full_group_member_link( $link ) {
	global $members_template;

	// Get the user's first name and last name from the user meta
	$first_name = get_user_meta( $members_template->member->user_id, 'first_name', true );
	$last_name = get_user_meta( $members_template->member->user_id, 'last_name', true );
	$full_name = trim($first_name . ' ' . $last_name);

	// If full_name is empty, fall back to the display name
	if ( empty( $full_name ) ) {
		$full_name = bp_core_get_user_displayname( $members_template->member->user_id );
	}

	// Replace the link text with the full name
	$link = '<a href="' . bp_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) . '">' . esc_html( $full_name ) . '</a>';

	return $link;
}
add_filter( 'bp_get_group_member_link', 'full_group_member_link' );


/* Delete record from students_credentials when user deleted */

function delete_student_credentials_on_user_delete($user_id) {
    global $wpdb;

    // Get the username of the user being deleted
    $user = get_userdata($user_id);
    $username = $user->user_login;

    // Define the students_credentials table name
    $std_cred_tbl = $wpdb->prefix . "students_credentials";

    // Delete the record from the students_credentials table
    $wpdb->delete($std_cred_tbl, array('username' => $username));
}

add_action('delete_user', 'delete_student_credentials_on_user_delete');


function get_teacher_classrooms_count($teacher_id) {
    if (!$teacher_id) {
        return new WP_Error('missing_teacher_id', __('Teacher ID is required', 'tprm-theme'));
    }

    // Check if the user has the 'teacher' role
    $user = get_userdata($teacher_id);
    if (!$user || !in_array('teacher', (array) $user->roles)) {
        return new WP_Error('not_a_teacher', __('User is not a teacher', 'tprm-theme'));
    }

    // Get the current school year
    $this_year = get_option('school_year');

    // Get groups where the user is an admin
    $groups = get_TPRM_user_groups($teacher_id);
    $admin_groups_count = 0;

    foreach ($groups as $group_id) {
        // Check if the user is an admin in this group
        $is_admin = groups_is_user_admin($teacher_id, $group_id);
        
        // Get group details
        $group = groups_get_group($group_id);
        $group_type = bp_groups_get_group_type($group_id);
        $group_year = groups_get_groupmeta($group_id, 'ecole_year');

        // Exclude parent groups of type 'kwf-ecole' and include only groups for the current year
        if ($is_admin && 
            ($group_type !== 'kwf-ecole' || $group->parent_id != 0) && 
            $group_year == $this_year) {
            $admin_groups_count++;
        }
    }

    return $admin_groups_count;
}


/**
 * Display full user name.
 *
 * @param string $display_name The current display name.
 * @param int    $user_id      ID of the user.
 * @param int    $current_user_id Optional. ID of the user viewing the profile.
 * @return string The full name of the user.
 */
function display_full_user_name( $display_name, $user_id, $current_user_id ) {
    // Get user first name and last name
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);
    
    // If either name is missing, fall back to the display name
    if (empty($first_name) || empty($last_name)) {
        return $display_name;
    }

    // Concatenate the full name
    $full_name = $first_name . ' ' . $last_name;

    return $full_name;
}

add_filter( 'bp_core_get_user_displayname', 'display_full_user_name', 100, 3 );




function edit_teacher() {
    // Check nonce for security
    if (!isset($_POST['security']) ) {
        wp_send_json_error(['message' => __('Invalid security token', 'tprm-theme')]);
    }

    // Check if required parameters are set
    if (!isset($_POST['teacher_id']) || !isset($_POST['teacher_name'])) {
        wp_send_json_error(['message' => __('Missing required parameters', 'tprm-theme')]);
    }

    $teacher_id = intval($_POST['teacher_id']);
    $teacher_name = sanitize_text_field($_POST['teacher_name']);
    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Get current user data
    $user = get_user_by('id', $teacher_id);
    if (!$user) {
        wp_send_json_error(['message' => __('User not found', 'tprm-theme')]);
    }

    // Prepare user data for update
    $userdata = [
        'ID' => $teacher_id,
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name' => $last_name,
    ];

    // Update the user data
    $user_id = wp_update_user($userdata);

    // Check if there was an error updating the user
    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
    }

    // Update the password if provided
    if (!empty($password)) {
        wp_set_password($password, $teacher_id);
    }

    wp_send_json_success(['message' => __('Teacher details updated successfully', 'tprm-theme')]);
}


// Register AJAX action for logged-in users
add_action('wp_ajax_edit_teacher', 'edit_teacher');
// Register AJAX action for logged-out users (if needed)
add_action('wp_ajax_nopriv_edit_teacher', 'edit_teacher');
