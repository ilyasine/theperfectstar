<?php 

/* 
* Activate student
*/

add_action('wp_ajax_activate_student', 'activate_student');

function activate_student() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'activate_student_payload' && 
        !empty($_POST['schoolID']) && !empty($_POST['studentID']) ) {

        check_ajax_referer('activate_paid_student_nonce', 'security');

		global $blog_id, $wpdb;
        $school_id = intval($_POST['schoolID']);
        $studentID = intval($_POST['studentID']);
        $membershipID = get_this_year_membership_id();
        $schoolSeats = get_school_seats($school_id);
        
        // Response messages
		$studentName = bp_core_get_user_displayname( $studentID );
        $student_activated_success_message = sprintf(__('%s\'s account has been successfully activated !', 'tprm-theme'), $studentName);

        // Check if the student is already activated
        $user_membership = wc_memberships_get_user_membership($studentID, $membershipID);
        if ($user_membership) {
            wp_send_json_error(['msg' => sprintf(__('%s\'s account is already activated. !', 'tprm-theme'), $studentName)]);
            return;
        }
       
        if ($schoolSeats <= 0) {
            wp_send_json_error(['msg' => __("You don't have enough seats. Please contact <strong>support@tepunareomaori.com</strong> to purchase more seats for your students.", 'tprm-theme')]);
            return;
        }

       /*  if ($couponObj->get_usage_count() >= $couponObj->get_usage_limit()) {
            wp_send_json_error(['msg' => __('You have used all the available seats. You can no longer activate student accounts. Please contact support@tepunareomaori.com to purchase more seats for your students.', 'kwf-membership-coupon')]);
        } */

        if ( ! function_exists( 'wc_memberships' ) ) {
            return;
        }
    
        $args = array(
            'plan_id'	=> $membershipID,
            'user_id'	=> $studentID,
        );
    
        wc_memberships_create_user_membership( $args );
        
        // Get the new membership
        $user_membership = wc_memberships_get_user_membership( $studentID, $membershipID );

        if($user_membership){
            //Update New Seats
            $schoolSeats = $schoolSeats - 1;
            groups_update_groupmeta($school_id, 'school_seats', $schoolSeats);
            $new_school_seats = groups_get_groupmeta($school_id, 'school_seats');
        }
        
        // Add a note so we know how this was registered.
        $user_membership->add_note( $student_activated_success_message );
                  
        wp_send_json_success(array(
            'student_name' => $studentName,
            'new_school_seats' => $new_school_seats,
            'student_activated_success_message' => $student_activated_success_message,
            'student_activation_error_message' => __('An error occurred while activation the student. Please try again.', 'tprm-theme')
        ));

        wp_die();
    } else {
        //wp_send_json_error('Failed to activate the student.');
        error_log('Failed to activate the student: Required fields missing or invalid payload.');
        wp_send_json_error(__('Failed to activate the student.', 'tprm-theme' ));
    }
}


/* // Array of usernames (you can extract this from the image or input it directly)
$usernames = array(
    'izd0068', 'izd0011', 'izd0084', 'izd0085', 'izd0103', 
    'izd0120', 'izd0122', 'izd0182', 'izd0281', 'izd0284',
    'izd0325', 'izd0334', 'izd0353', 'izd0362', 'izd0402',
    'izd0423', 'izd0433', 'izd0453', 'izd0548', 'izd0569', 
    'izd0513', 'izd0617', 'izd0644', 'izd0647', 'izd0675',
    'izd0686', 'izd0755', 'izd0825', 'izd1004',
    'iss0065', 'iss0008', 'iss0009', 'iss0109', 'iss0125',
    'iss0254', 'iss0315', 'iss0377', 'iss0429', 'iss0499',
    'iss0503', 'iss0552', 'iss0594', 'iss0622', 'iss0678',
    'iss0695', 'iss0724', 'iss0727', 'iss0740'
);

// Get the membership ID for this year
$membershipID = get_this_year_membership_id();

// Loop through each username, get the user ID, and assign membership
foreach ( $usernames as $username ) {
    
    // Get user by username
    $user = get_user_by( 'login', $username );
    
    // Check if the user exists
    if ( $user ) {
        $studentID = $user->ID;
        
        // Set up the args for membership creation
        $args = array(
            'plan_id'   => $membershipID,
            'user_id'   => $studentID,
        );
        
        // Create the membership
        wc_memberships_create_user_membership( $args );
        
        // Get the newly created membership for the user (optional, if you need it)
        $user_membership = wc_memberships_get_user_membership( $studentID, $membershipID );
        
        // You can add logging or actions based on $user_membership here
    } else {
        // Handle case where the user was not found (optional)
        error_log( "User not found: " . $username );
    }
}
 */