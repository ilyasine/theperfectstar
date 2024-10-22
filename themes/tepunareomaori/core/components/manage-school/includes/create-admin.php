<?php 

function create_admins($school_name, $school_id, $demo_group_id, $principal_details, $accounts_details, $ipl_details) {

    // Utility function to create a user
    function create_school_admin($first_name, $last_name, $email, $phone, $role, $member_type, $school_id, $demo_group_id, $school_name) {

        // Check if the user already exists by email
        $existing_user = get_user_by('email', $email);

        if ($existing_user) {
            $user_id = $existing_user->ID;

            // Update role for already existing
            $user = new WP_User($user_id);
            $user->set_role($role); 

            // Assign BuddyPress member type
            bp_set_member_type($user_id, $member_type);
        
            // Get existing user's first and last name
            $existing_user_data = get_userdata($user_id);
            $AdminFirstName = $existing_user_data->first_name;
            $AdminLastName = $existing_user_data->last_name;       
            $subject = __('Welcome to Te Puna Reo Māori Online Learning', 'tprm-theme');
            
            // Prepare other variables for the email template
            $admin_username = $existing_user_data->user_login; // Use the existing username
            $AdminPassword = 'Your existing password'; // Do not send a new password for existing users
            $login_url = wp_login_url();
        
            // Enroll the existing user in the school group as admin
            if (!groups_is_user_member($user_id, $school_id)) {
                $enroll_school = groups_join_group($school_id, $user_id);
                if ($enroll_school) {
                    groups_promote_member($user_id, $school_id, 'admin');
                    error_log('User enrolled in school : ' . $school_name);
                    error_log('$school_id, $user_id : ' . $school_id . ' , ' . $user_id );
                }
            }
        
            // Enroll the existing user in the demo group as admin
            if (!groups_is_user_member($user_id, $demo_group_id)) {
                $enroll_demo = groups_join_group($demo_group_id, $user_id);
                if ($enroll_demo) {
                    groups_promote_member($user_id, $demo_group_id, 'admin');
                    error_log('User enrolled in demo group');
                    error_log('$demo_group_id, $user_id : ' . $demo_group_id . ' , ' . $user_id );
                }
            }
        
            // Start output buffering to include the email template
            ob_start();
            include MSC_TEMPLATE_DIR . 'admin-email.php'; // Include the adapted admin email template
            $message = ob_get_clean();
        
            // Set email headers
            $headers = array('Content-Type: text/html; charset=UTF-8');
        
            // Send the email to the existing user
            wp_mail($email, $subject, $message, $headers);
        
            return $user_id; // Return the existing user's ID
        }        

        // Generate the username
        $username = strtolower(substr($first_name, 0, 1) . '.' . $last_name);

        // Check if the username already exists
        if (username_exists($username)) {
            $suffix = 1;
            while (username_exists($username . $suffix)) {
                $suffix++;
            }
            $username .= $suffix;
        }

        // Create the user
        $password = wp_generate_password(12, true); // Generate a secure password
        $userdata = array(
            'user_login' => $username,
            'user_pass'  => $password,
            'user_email' => sanitize_email($email),
            'first_name' => sanitize_text_field($first_name),
            'last_name'  => sanitize_text_field($last_name),
            'role'       => $role
        );

        $user_id = wp_insert_user($userdata);

        // Check for errors in user creation
        if (is_wp_error($user_id)) {
            error_log('User creation failed: ' . $user_id->get_error_message());
            //return false;
        }

        // Assign BuddyPress member type
        bp_set_member_type($user_id, $member_type);

        // Enroll the user in the school group as moderator
        $enroll_school = groups_join_group($school_id, $user_id);
        if ($enroll_school) {
            groups_promote_member($user_id, $school_id, 'admin');
        }

        // Enroll the user in the demo group as admin
        $enroll_demo = groups_join_group($demo_group_id, $user_id);
        if ($enroll_demo) {
            groups_promote_member($user_id, $demo_group_id, 'admin');
        }

        // Update user meta with school information
        update_user_meta($user_id, 'ecole', $school_id);
        update_user_meta($user_id, 'phone', sanitize_text_field($phone));

        // Send email to the Admin
        $subject = __('Welcome to Te Puna Reo Māori Online Learning', 'tprm-theme');
        
        // Prepare variables for the email template
        $AdminFirstName = $first_name;
        $AdminLastName = $last_name;
        $admin_username = $username;
        $AdminPassword = $password;
        $school_name = $school_name;
        $login_url = wp_login_url();

        // Start output buffering to include the email template
        ob_start();
        include MSC_TEMPLATE_DIR . 'admin-email.php'; // Include the adapted admin email template
        $message = ob_get_clean();

        // Set email headers
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Send the email
        wp_mail($email, $subject, $message, $headers);

        return $user_id;
    }


    // Principal details
    $principal_id = create_school_admin(
        $principal_details['first_name'],
        $principal_details['last_name'],
        $principal_details['email'],
        $principal_details['phone'],
        'school-admin',   // WordPress role
        'principal',      // BuddyPress member type
        $school_id,
        $demo_group_id,
        $school_name
    );

    // Accounts person details
    $accounts_id = create_school_admin(
        $accounts_details['first_name'],
        $accounts_details['last_name'],
        $accounts_details['email'],
        '',               // No phone number provided for Accounts person
        'school-admin',   // WordPress role
        'accounts-person', // BuddyPress member type
        $school_id,
        $demo_group_id,
        $school_name
    );

    // Programme Leader details (IPL)
    $ipl_id = create_school_admin(
        $ipl_details['first_name'],
        $ipl_details['last_name'],
        $ipl_details['email'],
        $ipl_details['phone'],
        'school-admin',     // WordPress role
        'programme-leader', // BuddyPress member type
        $school_id,
        $demo_group_id,
        $school_name
    );

    // Logging for success or failure
    if ($principal_id && $accounts_id && $ipl_id) {
        error_log('Admins created successfully.');
    } else {
        error_log('Failed to create one or more admins.');
    }
}
