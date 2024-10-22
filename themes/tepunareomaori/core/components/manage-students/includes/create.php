<?php 

function generate_student_username($school_trigram) {
    $student_username_base = strtolower($school_trigram);
    $max_suffix = 9999; // Define a maximum suffix to prevent infinite loops
    $current_suffix = 1;

    while ($current_suffix <= $max_suffix) {
        // Generate the potential username with leading zeros
        $student_username = $student_username_base . str_pad($current_suffix, 4, '0', STR_PAD_LEFT);

        // Check if the username exists
        if (!username_exists($student_username)) {
            return $student_username; // Return the available username
        }

        // Increment the suffix for the next iteration
        $current_suffix++;
    }

    // Handle the case where all possible usernames are taken (e.g., return an error)
    return null; // or throw an exception, or handle it as needed
}

/* 
* Create student
*/

add_action('wp_ajax_create_student', 'create_student');

function create_student() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'create_student_payload' && 
        !empty($_POST['school_id']) && !empty($_POST['studentFirstName']) && 
        !empty($_POST['studentLastName']) && !empty($_POST['selectedPasswordMode']) && 
        !empty($_POST['selectedPasswordValue']) ) {

        check_ajax_referer('student_create_nonce', 'security');

		global $blog_id, $wpdb;
        $school_id = intval($_POST['school_id']);
        $school_trigram = groups_get_groupmeta($school_id, 'school_trigram');
        $school_name = sanitize_text_field($_POST['school_name']);
        $studentFirstName = sanitize_text_field($_POST['studentFirstName']);
        $studentLastName = sanitize_text_field($_POST['studentLastName']);
        $studentPasswordMode = sanitize_text_field($_POST['selectedPasswordMode']);
        $studentPassword = sanitize_text_field($_POST['selectedPasswordValue']);
       
		$studentFullName = $studentFirstName . ' ' . $studentLastName;

        //optional
        $ClassroomId = $_POST['selectedClassroomId'];

        // Generate the student username
        $student_username = generate_student_username($school_trigram);
        $studentEmail = $student_username . '@tepunareomaori.com';

        // Create the student user
        $userdata = array(
            'user_login' => $student_username,
            'user_pass' => $studentPassword,
            'user_email' => $studentEmail,
            'first_name' => $studentFirstName,
            'last_name' => $studentLastName,
            'role' => 'student' // Set default role, add 'student' role later
        );

        $student_id = wp_insert_user($userdata);

        // Check for errors in user creation
        if (is_wp_error($student_id)) {
            error_log('User creation failed: ' . $student_id->get_error_message());
            wp_send_json_error('Failed to create student. ' . $student_id->get_error_message());
            return;
        }

        # Assign Classrooms to the student only if there are
        if(!empty($ClassroomId)){
  
            // Add the user to the classroom
            $result = groups_join_group($ClassroomId, $student_id);

            // Check if the user was added successfully
            if ($result) {
                // Promote the user to group admin               
                bp_set_member_type($student_id, 'student');	
            } else {
                wp_send_json_error('Failed to add student ID ' . $student_id . ' to group ID ' . $ClassroomId);
                error_log("Failed to add student ID {$student_id} to group ID {$ClassroomId}");
            }
            
        }

        #Enroll student in the school
        groups_join_group($school_id, $student_id);     

        // Student Password
        update_user_meta($student_id, 'password_type', $studentPasswordMode);

        if($studentPasswordMode == "picture" && !empty($student_id)){
			$selectedImageUrl = esc_url($studentPassword);
			$selectedImageName = basename($selectedImageUrl);
			update_user_meta($student_id, 'picture_password_image',$selectedImageName);
			update_user_meta($student_id, 'text_password','');
		}else if($studentPasswordMode == 'text' && !empty($student_id)){
			$password = sanitize_text_field($studentPassword);
			update_user_meta($student_id, 'text_password',$password);
			update_user_meta($student_id, 'picture_password_image','');
			wp_set_password($password, $student_id);
			$student_info = get_userdata( $student_id );
			if($student_info ){
				// Store credentials in students_credentials table
                $std_cred_tbl = $wpdb->prefix . "students_credentials";

                $user_exists = $wpdb->get_row("SELECT * FROM $std_cred_tbl WHERE username = '$student_username'");
        
                if($user_exists) {
                    // Update the user details if the user already exists
                    $wpdb->update(
                        $std_cred_tbl,
                        array(
                            'username' => $student_username,
                            'stdcred' => $studentPassword,
                            'first_name' => $studentFirstName,
                            'last_name' => $studentLastName,
                        ),
                        array('username' => $student_username)
                    );
                } else {
                    // Insert the user if not exists (created)
                    $wpdb->insert($std_cred_tbl, array(
                        'username' => $student_username,
                        'stdcred' => $studentPassword,
                        'first_name' => $studentFirstName,
                        'last_name' => $studentLastName,
                    ));
                }
			}
		}
        

        // Response messages
		$student_link = bp_core_get_user_domain( $student_id );
        $student_created_success_message = sprintf(__('The student "%s" has been created successfully. You can access it via the following button', 'tprm-theme'), $studentFullName);

        echo json_encode(array(
            'student_name' => $studentFullName,
            'student_link' => $student_link,
            'student_being_created_message' => __('The student is being created...', 'tprm-theme'),
            'student_created_success_message' => $student_created_success_message,
            'student_creation_error_message' => __('An error occurred while creating the student. Please try again.', 'tprm-theme')
        ));

        wp_die();
    } else {
        //wp_send_json_error('Failed to create the student.');
        error_log('Failed to create the student: Required fields missing or invalid payload.');
        wp_send_json_error('Failed to create the student.');
    }
}


/* 
* Suspend student
*/
add_action('wp_ajax_suspend_student', 'suspend_student');

function suspend_student() {
    // Initialize variables for debugging
    $school_id  = isset($_POST['school_id']) ? intval($_POST['school_id']) : null;
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : null;   
    $classroom_id = isset($_POST['classroom_id']) ? intval($_POST['classroom_id']) : null;   
    $student_name = isset($_POST['student_name']) ? sanitize_text_field($_POST['student_name']) : null;
    
    // Check if payload is set correctly
    if (isset($_POST['payload']) && $_POST['payload'] == 'suspend_student_payload' && 
        !empty($student_id) && !empty($school_id) && !empty($student_name)) {

        check_ajax_referer('suspend_student_nonce', 'security');
        
        // ban member from school
        $member    = new BP_Groups_Member( $student_id, $school_id);
        $member->ban();

        //ban member from classroom of this year
        if(!empty($classroom_id)){
            $member    = new BP_Groups_Member( $student_id, $classroom_id); //ban member from classroom of this year
            $member->ban();
        }
       
        // Change user role to Suspended
        $user = new WP_User($student_id);
        $user->set_role('suspended'); // Ensure 'suspended' role is added in your system

        //Lock student Account
        update_user_meta( (int)$student_id, sanitize_key( 'TPRM_user_suspended' ), 'yes' );

        //Log out the student
        $student = WP_Session_Tokens::get_instance($student_id);
        $student->destroy_all();

        $success_message = sprintf(__('The student %s has been suspended.', 'tprm-theme'), $student_name);
        wp_send_json_success(array('message' => $success_message));
    } else {
        // Create a debug message to identify the source of the issue
        $error_message = __('Invalid request.', 'tprm-theme');
        if (empty($_POST['payload'])) {
            $error_message .= ' Payload is missing.';
        } elseif ($_POST['payload'] !== 'suspend_student_payload') {
            $error_message .= ' Invalid payload value.';
        }
        if (empty($student_id)) {
            $error_message .= ' Student ID is missing.';
        }
        if (empty($student_name)) {
            $error_message .= ' Student name is missing.';
        }

        wp_send_json_error(array('message' => $error_message));
    }

    wp_die();
}

add_filter( 'wp_authenticate_user','check_suspend');

function check_suspend( $user ){
    if( is_wp_error( $user ) ){
        return $user;
    }
    if( is_object( $user ) && isset( $user->ID ) && 'yes' === get_user_meta( (int)$user->ID, sanitize_key( 'TPRM_user_suspended' ), true ) ){

        return new WP_Error( 'suspended', ( $error_message ) ? $error_message : __( 'Your tepunareomaori account is suspended !', 'tprm-theme' ) );
    }
    else{
        return $user;
    }
}

//TODO Restore Account