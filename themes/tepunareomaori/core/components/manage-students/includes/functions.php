<?php 

function get_tprm_user_groups($student_id) {
    global $wpdb;
    $bp = buddypress();

    // Ensure $student_id is an integer
    $student_id = intval($student_id);

    // Prepare SQL to get all groups the user belongs to, including hidden groups
    $group_sql = $wpdb->prepare(
        "SELECT DISTINCT m.group_id 
         FROM {$bp->groups->table_name_members} m 
         JOIN {$bp->groups->table_name} g ON m.group_id = g.id 
         WHERE m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", 
         $student_id
    );

    // Execute the query to get the groups
    $groups = $wpdb->get_col($group_sql);

    return $groups;
}


function get_student_school($student_id = null) {


    // Use the current user if $student_id is not provided
    if (is_null($student_id)) {
        $student_id = get_current_user_id();
    }

    // Get the user object
    $user = get_userdata($student_id);
    $group_ids = get_tprm_user_groups($student_id);
  
    // Check if the user is a student
    if (is_student($user)) {
        // Get all group IDs the user belongs to
        /* $group_ids = groups_get_user_groups($student_id)['groups']; */

        $group_ids = get_tprm_user_groups($student_id);  

        // Iterate through the groups
        foreach ($group_ids as $group_id) {
			$parent_group_id = bp_get_parent_group_id($group_id);	
            // Check if the group is a school
            if (is_school($parent_group_id)) {
                // Return the group ID if it's a school
                return $parent_group_id;
            }
        }
    }

    // Return false or null if no school is found
    return false;
}


/**
 * Get the classroom group ID for a student for a specific year.
 *
 * @param int|null $student_id Optional. The ID of the student. Defaults to the current user.
 * @param string|null $the_year Optional. The school year. Defaults to the current year.
 * @return int|false The classroom group ID, or false if not found.
 */
function get_student_classroom_for_year($student_id = null, $the_year = null) {
    global $wpdb;

    // Use the current user if $student_id is not provided
    if (is_null($student_id)) {
        $student_id = get_current_user_id();
    }

    // If the year is not provided, get the current school year from the options
    if (is_null($the_year)) {
        $the_year = get_option('school_year');
    }

    // Get the student's school ID
    $school_id = get_student_school($student_id);

    // Prepare the query to fetch the classroom group ID for the student and the year
    $group_id_query = $wpdb->prepare("
        SELECT g.id 
        FROM {$wpdb->prefix}bp_groups g
        JOIN {$wpdb->prefix}bp_groups_groupmeta gm1 ON g.id = gm1.group_id
        JOIN {$wpdb->prefix}bp_groups_members gm ON g.id = gm.group_id
        WHERE gm1.meta_key = 'ecole_year' 
        AND gm1.meta_value = %s
        AND gm.user_id = %d
        AND g.parent_id = %d
        LIMIT 1
    ", $the_year, $student_id, $school_id);

    // Get the classroom group ID
    $group_id = $wpdb->get_var($group_id_query);

    return $group_id ? intval($group_id) : false;
}

/**
 * Get all classroom group IDs for a student that are children of the student's school.
 *
 * @param int|null $student_id Optional. The ID of the student. Defaults to the current user.
 * @return array|false An array of classroom group IDs, or false if none found.
 */
function get_all_student_classrooms($student_id = null) {
    global $wpdb;

    // Use the current user if $student_id is not provided
    if (is_null($student_id)) {
        $student_id = get_current_user_id();
    }

    // Get the student's school ID
    $school_id = get_student_school($student_id);

    // Prepare the query to fetch all classroom group IDs for the student where the parent group is the school
    $group_ids_query = $wpdb->prepare("
        SELECT g.id 
        FROM {$wpdb->prefix}bp_groups g
        JOIN {$wpdb->prefix}bp_groups_members gm ON g.id = gm.group_id
        WHERE gm.user_id = %d
        AND g.parent_id = %d
    ", $student_id, $school_id);

    // Get the array of classroom group IDs
    $group_ids = $wpdb->get_col($group_ids_query);

    // Return the array of classroom group IDs, or false if none found
    return !empty($group_ids) ? array_map('intval', $group_ids) : false;
}


/* function classroom_member_pagination_count( $message, $from_num, $to_num, $total ) {

    // teachers pagination
    if (function_exists('bp_is_group') && bp_is_group() && strpos($_SERVER['REQUEST_URI'], "teachers") !== false) {
        $message = sprintf(_n('Viewing 1 teacher', 'Viewing %1$s - %2$s of %3$s teachers', $total, 'tprm-theme'), $from_num, $to_num, $total);
    }
    // students pagination
    if (function_exists('bp_is_group') && bp_is_group() && strpos($_SERVER['REQUEST_URI'], "students") !== false) {
        $message = sprintf(_n('Viewing 1 student', 'Viewing %1$s - %2$s of %3$s students', $total, 'tprm-theme'), $from_num, $to_num, $total);
    }

    return $message;
}
add_filter('bp_get_group_member_pagination_count', 'classroom_member_pagination_count', 10, 4); */

function classroom_member_pagination_count( $message, $from_num, $to_num, $total ) {

    // Check if this is the teachers management section
    if ( function_exists( 'bp_is_group' ) && bp_is_group() && strpos( $_SERVER['REQUEST_URI'], "teachers" ) !== false ) {

        // Get the current group ID
        $group_id = bp_get_current_group_id();
        
        // Get the correct teacher count using a similar logic to teachers_count_int
        $group_members = groups_get_group_members( array(
            'group_id'   => $group_id,
            'group_role' => array( 'admin', 'mod' ),
        ) );
        
        $teacher_count = 0;
        
        foreach ( $group_members['members'] as $member ) {
            $user_id = $member->ID;
            $user    = get_userdata( $user_id );

            // Count only users with the 'school_staff' role
            if ( $user && in_array( 'school_staff', $user->roles ) ) {
                $teacher_count++;
            }
        }

        // If the group is a school, include members with the 'member' role
        if ( is_school( $group_id ) ) {
            $group_members = groups_get_group_members( array(
                'group_id'   => $group_id,
                'group_role' => array( 'member' ),
            ) );

            foreach ( $group_members['members'] as $member ) {
                $user_id = $member->ID;
                $user    = get_userdata( $user_id );

                if ( $user && in_array( 'school_staff', $user->roles ) ) {
                    $teacher_count++;
                }
            }
        }

        // Now we have the correct total number of teachers
        $total_teachers = $teacher_count;
        $to_num = $teacher_count;

        // Adjust the pagination message to reflect the number of teachers
        $message = sprintf( _n( 'Viewing 1 teacher', 'Viewing %1$s - %2$s of %3$s teachers', $total_teachers, 'tprm-theme' ), $from_num, $to_num, $total_teachers );
    }
    
    // For students pagination (if necessary)
    if ( function_exists( 'bp_is_group' ) && bp_is_group() && strpos( $_SERVER['REQUEST_URI'], "students" ) !== false ) {
        $message = sprintf( _n( 'Viewing 1 student', 'Viewing %1$s - %2$s of %3$s students', $total, 'tprm-theme' ), $from_num, $to_num, $total );
    }

    return $message;
}
add_filter( 'bp_get_group_member_pagination_count', 'classroom_member_pagination_count', 10, 4 );


function classroom_pagination_count( $message, $from_num, $to_num, $total ) {

    // classrooms pagination
    if (function_exists('bp_is_group') && bp_is_group() && function_exists('bp_is_group_subgroups') && bp_is_group_subgroups()) {
        $message = sprintf(_n('Viewing 1 classroom', 'Viewing %1$s - %2$s of %3$s classrooms', $total, 'tprm-theme'), $from_num, $to_num, $total);
    }

    return $message;
}
add_filter('bp_get_groups_pagination_count', 'classroom_pagination_count', 10, 4);


function get_classroom_students($classroom_id){
    $students = array();
    $student_ids = array();

    // Fetch group members using the 'group_id' parameter
    $members = groups_get_group_members(array(
        'group_id' => $classroom_id,
        'exclude_admins_mods' => true, // Include admins and mods if needed
        'exclude_banned' => true, // Exclude banned members
        'exclude' => false, // Include all members
    ));

    if (!empty($members['members'])) {
        foreach ($members['members'] as $member) {
            $user_id = $member->ID;
            // Check if the user has the 'school_student' role
            if ((user_has_role($user_id, 'school_student') || user_has_role($user_id, 'tprm-student')) ) {
                $student_ids[] = $user_id; // Track the student ID to avoid duplicates
            }
        }
    }
    
    return $student_ids;
}
/**
 * Remove profile and settings tab for Students
 *
 * @since V3
 */

function remove_student_profile_tab() {
    if(is_student()){
        bp_core_remove_nav_item('profile');
        bp_core_remove_subnav_item('settings', 'general');
    }
}
add_action('bp_setup_nav', 'remove_student_profile_tab', 1000);


add_action('wp_ajax_edit_student', 'handle_edit_student');
function handle_edit_student() {
    global $wpdb;  

    // Check if nonce is valid
    if (!isset($_POST['security'])) {
        wp_send_json_error(['message' => __('Invalid security nonce.', 'tprm-theme')]);
        wp_die();
    }

    // Sanitize and validate input data
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $student_name = isset($_POST['student_name']) ? sanitize_text_field($_POST['student_name']) : '';
    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
  //  $picture_password_url = isset($_POST['picture_password_url']) ? sanitize_text_field($_POST['picture_password_url']) : '';

    $selectedImageUrl = esc_url($_POST['picture_password_url']);
    $picture_password_url = basename($selectedImageUrl); // Extract the image file name

    if (empty($student_id)) {
        wp_send_json_error(['message' => __('Student ID is required.', 'tprm-theme')]);
        wp_die();
    }

    // Update student details
    $user_data = [
        'ID' => $student_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_email' => $email
    ];

    // Update user details
    $user_id = wp_update_user($user_data);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => __('Failed to update student details.', 'tprm-theme')]);
        wp_die();
    }

    // Logic to track which password type is being updated
    $password_type = null;

    // Update text password if provided
    if (!empty($password)) {
        wp_set_password($password, $student_id);

        // Update stdcred after password change
        $stdcred_tbl = $wpdb->prefix . "students_credentials";
        $username = get_userdata($student_id)->user_login; // Get the username

        // Check if the student already has a record
        $sql = $wpdb->prepare("SELECT COUNT(*) FROM " . esc_sql($stdcred_tbl) . " WHERE username = %s", esc_sql($username));
        $exists = $wpdb->get_var($sql);

        if ($exists) {
            // If record exists, update the stdcred value
            $wpdb->update(
                esc_sql($stdcred_tbl),
                ['stdcred' => $password],  // Updating with a hash of the password
                ['username' => $username],
                ['%s'],
                ['%s']
            );
        } else {
            // If no record exists, insert a new record
            $wpdb->insert(
                esc_sql($stdcred_tbl),
                ['username' => $username, 'stdcred' => $password],
                ['%s', '%s']
            );
        }

        // Set password type to "text" since the text password was updated
        $password_type = 'text';
    }

    // Save picture password URL if provided
    if (!empty($picture_password_url)) {
        update_user_meta($student_id, 'picture_password_image', $picture_password_url);
        
        // Set password type to "picture" since the picture password was updated
        $password_type = 'picture';
    }

    // Update the password type meta key if applicable
    if ($password_type) {
        update_user_meta($student_id, 'password_type', $password_type);
    }

    // Return success response
    wp_send_json_success(['message' => __('Student details updated successfully.', 'tprm-theme')]);
    wp_die();
}