<?php 

/* 
* Create teacher
*/

add_action('wp_ajax_create_teacher', 'create_teacher');

function create_teacher() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'create_teacher_payload' && 
        !empty($_POST['school_id']) && !empty($_POST['TeacherFirstName']) && 
        !empty($_POST['TeacherLastName']) && !empty($_POST['TeacherEmail']) && 
        !empty($_POST['TeacherPassword']) ) {

        check_ajax_referer('teacher_create_nonce', 'security');

		global $blog_id, $wpdb;
        $school_id = intval($_POST['school_id']);
        $school_name = sanitize_text_field($_POST['school_name']);
        $TeacherFirstName = sanitize_text_field($_POST['TeacherFirstName']);
        $TeacherLastName = sanitize_text_field($_POST['TeacherLastName']);       
        $TeacherEmail = sanitize_email($_POST['TeacherEmail']);
        $TeacherPassword = sanitize_text_field($_POST['TeacherPassword']);
       
		$TeacherFullName = $TeacherFirstName . ' ' . $TeacherLastName;

        //optional
        $ClassroomIds = $_POST['ClassroomIds'];

        // Generate the teacher username
        $teacher_username = strtolower(substr($TeacherFirstName, 0, 1) . '.' . $TeacherLastName);

        // Check if the username already exists
        if (username_exists($teacher_username)) {
            $suffix = 1;
            while (username_exists($teacher_username . $suffix)) {
                $suffix++;
            }
            $teacher_username .= $suffix;
        }

        // Create the teacher user
        $userdata = array(
            'user_login' => $teacher_username,
            'user_pass' => $TeacherPassword,
            'user_email' => $TeacherEmail,
            'first_name' => $TeacherFirstName,
            'last_name' => $TeacherLastName,
            'role' => 'group_leader' // Set default role, add 'teacher' role later
        );

        $teacher_id = wp_insert_user($userdata);

        // Check for errors in user creation
        /* if (is_wp_error($teacher_id)) {
            wp_send_json_error('Failed to create teacher.');
            return;
        } */
        if (is_wp_error($teacher_id)) {
            error_log('User creation failed: ' . $teacher_id->get_error_message());
            wp_send_json_error('Failed to create teacher. ' . $teacher_id->get_error_message());
            return;
        }

        // Assign 'teacher' role to the user
        $user = new WP_User($teacher_id);
        $user->add_role('teacher');

        # Assign Classrooms to the teacher only if there are
        if(!empty($ClassroomIds)){
            foreach ($ClassroomIds as $ClassroomId) {
                // Add the user to the classroom
                $result = groups_join_group($ClassroomId, $teacher_id);

                // Check if the user was added successfully
                if ($result) {
                    // Promote the user to group admin
                    groups_promote_member($teacher_id, $ClassroomId, 'admin');

                    // Get associated LearnDash group ID and update user meta
                    $ld_group_id = bp_ld_sync('buddypress')->helpers->getLearndashGroupId($ClassroomId);
                    update_user_meta($teacher_id, 'learndash_group_leaders_' . $ld_group_id, $ld_group_id);
                } else {
                    wp_send_json_error('Failed to add teacher ID ' . $teacher_id . ' to group ID ' . $ClassroomId);
                    error_log("Failed to add teacher ID {$teacher_id} to group ID {$ClassroomId}");
                }
            }
        }

        // Enroll the user in the school group as moderator
        $enroll_school = groups_join_group($school_id, $teacher_id);
        if ($enroll_school) {
            groups_promote_member($teacher_id, $school_id, 'mod');
        }

        // Update user meta
        update_user_meta($teacher_id, 'ecole', $school_id);

		//Update teacher lang :
		$lang_meta = $wpdb->get_blog_prefix($blog_id) . 'lang';
		$school_language = groups_get_groupmeta($school_id, 'ecole_lang');
		update_user_meta( $teacher_id, $lang_meta, $school_language );

        bp_set_member_type($teacher_id, 'teacher');	

        // Store credentials in students_credentials table
        $std_cred_tbl = $wpdb->prefix . "students_credentials";

        $user_exists = $wpdb->get_row("SELECT * FROM $std_cred_tbl WHERE username = '$teacher_username'");

        if($user_exists) {
            // Update the user details if the user already exists
            $wpdb->update(
                $std_cred_tbl,
                array(
                    'username' => $teacher_username,
                    'stdcred' => $TeacherPassword,
                    'first_name' => $TeacherFirstName,
                    'last_name' => $TeacherLastName,
                ),
                array('username' => $teacher_username)
            );
        } else {
            // Insert the user if not exists (created)
            $wpdb->insert($std_cred_tbl, array(
                'username' => $teacher_username,
                'stdcred' => $TeacherPassword,
                'first_name' => $TeacherFirstName,
                'last_name' => $TeacherLastName,
            ));
        }

        // Send email to the teacher
        $subject = __('Welcome to tepunareomaori.com', 'tprm-theme');
        ob_start();
        include MTE_TEMPLATE_DIR . 'teacher-email.php'; 
        $message = ob_get_clean();

        // Set email headers
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Send the email
        wp_mail($TeacherEmail, $subject, $message, $headers);

        // Response messages
		$teacher_link = bp_core_get_user_domain( $teacher_id );
        $teacher_created_success_message = sprintf(__('The teacher "%s" has been created successfully. You can access it via the following button', 'tprm-theme'), $TeacherFirstName);

        echo json_encode(array(
            'teacher_name' => $TeacherFullName,
            'teacher_link' => $teacher_link,
            'teacher_being_created_message' => __('The teacher is being created...', 'tprm-theme'),
            'teacher_created_success_message' => $teacher_created_success_message,
            'teacher_creation_error_message' => __('An error occurred while creating the teacher. Please try again.', 'tprm-theme')
        ));

        wp_die();
    } else {
        //wp_send_json_error('Failed to create the teacher.');
        error_log('Failed to create the teacher: Required fields missing or invalid payload.');
        wp_send_json_error('Failed to create the teacher.');
    }
}


/* 
* Delete teacher
*/

/*
 * Change teacher role to Suspended
 */
add_action('wp_ajax_delete_teacher', 'suspend_teacher');

function suspend_teacher() {
    // Initialize variables for debugging
    $teacher_id = isset($_POST['teacher_id']) ? intval($_POST['teacher_id']) : null;
    $teacher_name = isset($_POST['teacher_name']) ? sanitize_text_field($_POST['teacher_name']) : null;
    
    // Check if payload is set correctly
    if (isset($_POST['payload']) && $_POST['payload'] == 'delete_teacher_payload' && 
        !empty($teacher_id) && !empty($teacher_name)) {

        check_ajax_referer('delete_teacher_nonce', 'security');

        // Change user role to Suspended
        $user = new WP_User($teacher_id);
        $user->set_role('suspended'); // Ensure 'suspended' role is added in your system

        $success_message = sprintf(__('The teacher %s has been suspended.', 'tprm-theme'), $teacher_name);
        wp_send_json_success(array('message' => $success_message));
    } else {
        // Create a debug message to identify the source of the issue
        $error_message = __('Invalid request.', 'tprm-theme');
        if (empty($_POST['payload'])) {
            $error_message .= ' Payload is missing.';
        } elseif ($_POST['payload'] !== 'delete_teacher_payload') {
            $error_message .= ' Invalid payload value.';
        }
        if (empty($teacher_id)) {
            $error_message .= ' Teacher ID is missing.';
        }
        if (empty($teacher_name)) {
            $error_message .= ' Teacher name is missing.';
        }

        wp_send_json_error(array('message' => $error_message));
    }

    wp_die();
}

// Redirect suspended users on login
add_action('wp_login', 'redirect_suspended_users', 10, 2);
function redirect_suspended_users($user_login, $user) {
    if (in_array('suspended', (array) $user->roles)) {
        wp_redirect(home_url('/suspended-account')); // Change to your suspended account page URL
        exit;
    }
}

// Create the 'suspended' role if it doesn't exist
function create_suspended_role() {
    if (!get_role('suspended')) {
        add_role('suspended', __('Suspended', 'tprm-theme'), array(
            'read' => true, // Allows a user to log in
        ));
    }
}
add_action('init', 'create_suspended_role');
