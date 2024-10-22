<?php 

use FluentForm\App\Models\EntryDetails;
use FluentForm\App\Services\Submission\SubmissionService;

add_filter('fluentform/entry_statuses_core', 'modify_fluentform_entry_statuses');

/**
 * Custom callback function to modify Fluent Forms entry statuses.
 *
 * @param array $statuses An array of default Fluent Forms statuses.
 *                        Example: ['unread' => 'Unread', 'read' => 'Read']
 * 
 * @return array Modified array of statuses, changing 'unread' to 'Pending' and 'read' to 'Active'.
 */
function modify_fluentform_entry_statuses($statuses) {
    // Replace the default 'read' and 'unread' statuses with 'pending' and 'active'
    $statuses['read'] = 'Active';
    $statuses['unread'] = 'Pending';
    //$statuses['active'] = 'New Active';
    //$statuses['pending'] = 'New Pending';

    return $statuses;
}

// Disable the auto read functionality in Fluent Forms
add_filter('fluentform/auto_read_submission', function($autoRead, $form) {
    return false;  // Set autoRead to false to disable automatic reading
}, 10, 2);


add_filter('fluentform/admin_i18n', 'modify_fluentform_admin_i18n');

/**
 * Callback function to modify Fluent Forms admin interface translations.
 *
 * This function replaces 'Unread Only' with 'Pending Only', 'Read Only' with 'Active Only',
 * 'Unread' with 'Pending', and 'Read' with 'Active' in the admin interface.
 *
 * @param array $i18n An array of translated strings used in Fluent Forms admin UI.
 *                    Example: ['Unread Only' => __('Unread Only', 'fluentform'), ...]
 * 
 * @return array Modified array of translated strings.
 */
function modify_fluentform_admin_i18n($i18n) {
    // Modify specific admin labels related to form statuses
    $i18n['Unread Only'] = __('Pending Only', 'fluentform');
    $i18n['Read Only'] = __('Active Only', 'fluentform');
    $i18n['Status'] = __('Status', 'fluentform');
    $i18n['Unread'] = __('Pending', 'fluentform');
    $i18n['Read'] = __('Active', 'fluentform');

    return $i18n;
}

// Add a filter to modify the submission status before sending the success message
add_filter('fluentform_modify_submission_status', 'updated_submission_status_message', 10, 2);

function updated_submission_status_message($new_status, $original_status){

    //TODO : Note : always update the function updateStatus in this path : wp-content\plugins\fluentform\app\Http\Controllers\SubmissionController.php after each plugin Update

    /**
    * 
    *    public function updateStatus(SubmissionService $submissionService)
    *   {
    *       try {
    *            $status = $submissionService->updateStatus($this->request->all());
    *
    *            $new_status = $status; // Define a default status message
    *    
    *            // Apply a filter to allow modifying the status before sending the success response
    *            $new_status = apply_filters('fluentform_modify_submission_status', $new_status, $status);
    *    
    *            // Return the success message
    *            return $this->sendSuccess([
    *                'message' => __('The submission has been marked as ' . $new_status, 'fluentform'),
    *                'status'  => $status,
    *            ]);
    *        } catch (Exception $e) {
    *            return $this->sendError([
    *                'message' => $e->getMessage(),
    *            ]);
    *        }
    *    }
     */

    // Example: Change the status based on some condition
    if ($original_status === 'read') {
        return 'Active';
    } elseif ($original_status === 'unread') {
        return 'Pending';
    }

    // Return the new or unchanged status
    return $new_status;
}



/**
 * Callback function to handle actions after Fluent Forms submission status update.
 *
 * @param int    $submissionId The ID of the form submission.
 * @param string $status       The new status of the submission (e.g., 'read', 'unread', 'pending', 'active','trashed').
 */
function handle_after_submission_status_update($submissionId, $status) {

    if ($status === 'read') {
        // Perform school creation actions when the status is 'read'
        error_log("Process school, Submission is active");
    
        // Get entry details by submission ID
        $entryDetails = EntryDetails::where('submission_id', $submissionId)->get();
    
        // Initialize variables
        $school_name = '';
        $school_seats = '';
        $school_id = '';
        $demo_group_id = '';
        
        // Principal details
        $principal_first_name = '';
        $principal_last_name = '';
        $principal_phone = '';
        $principal_email = '';
        
        // Accounts person details
        $accounts_first_name = '';
        $accounts_last_name = '';
        $accounts_email = '';
    
        // Programme Leader details (IPL)
        $ipl_first_name = '';
        $ipl_last_name = '';
        $ipl_phone = '';
        $ipl_email = '';
    
        foreach ($entryDetails as $detail) {
            // Extracting school_name
            if ($detail->field_name === 'school_name') {
                $school_name = $detail->field_value;
            }
    
            // Extracting school_seats (school_roll_july_2022)
            if ($detail->field_name === 'school_roll_july_2022') {
                $school_seats = $detail->field_value;
            }
    
            // Extracting Principal details
            if ($detail->field_name === 'names_principal' && $detail->sub_field_name === 'first_name') {
                $principal_first_name = $detail->field_value;
            }
            if ($detail->field_name === 'names_principal' && $detail->sub_field_name === 'last_name') {
                $principal_last_name = $detail->field_value;
            }
            if ($detail->field_name === 'phone_principal') {
                $principal_phone = $detail->field_value;
            }
            if ($detail->field_name === 'email_principal') {
                $principal_email = $detail->field_value;
            }
    
            // Extracting Accounts Person details
            if ($detail->field_name === 'names_accounts' && $detail->sub_field_name === 'first_name') {
                $accounts_first_name = $detail->field_value;
            }
            if ($detail->field_name === 'names_accounts' && $detail->sub_field_name === 'last_name') {
                $accounts_last_name = $detail->field_value;
            }
            if ($detail->field_name === 'email_accounts') {
                $accounts_email = $detail->field_value;
            }
    
            // Extracting Programme Leader (IPL) details
            if ($detail->field_name === 'names_ipl' && $detail->sub_field_name === 'first_name') {
                $ipl_first_name = $detail->field_value;
            }
            if ($detail->field_name === 'names_ipl' && $detail->sub_field_name === 'last_name') {
                $ipl_last_name = $detail->field_value;
            }
            if ($detail->field_name === 'phone_ipl') {
                $ipl_phone = $detail->field_value;
            }
            if ($detail->field_name === 'email_ipl') {
                $ipl_email = $detail->field_value;
            }
    
            // Logging each field for debugging purposes
            //error_log($detail->field_name . ' : ' . $detail->field_value);
        }


        //create_school($school_name, $school_seats);

        $school_result = create_school($school_name, $school_seats);

        if (!is_wp_error($school_result)) {
            $school_id = $school_result['school_id'];
            $demo_group_id = $school_result['demo_group_id'];

            // Principal details
            $principal_details = array(
                'first_name' => $principal_first_name,
                'last_name' => $principal_last_name,
                'phone' => $principal_phone,
                'email' => $principal_email
            );

            // Accounts person details
            $accounts_details = array(
                'first_name' => $accounts_first_name,
                'last_name' => $accounts_last_name,
                'email' => $accounts_email
            );

            // Programme Leader details (IPL)
            $ipl_details = array(
                'first_name' => $ipl_first_name,
                'last_name' => $ipl_last_name,
                'phone' => $ipl_phone,
                'email' => $ipl_email
            );

            error_log('School id: ' . $school_id);
            error_log('Demo_group_id: ' . $demo_group_id);
            // Create admins
            create_admins($school_name, $school_id, $demo_group_id, $principal_details, $accounts_details, $ipl_details);

        } else {
            error_log('Error creating school: ' . $school_result->get_error_message());
        }


        //TODO Create admins
    
        // Now we have all the required details
    /*  error_log('School Name: ' . $school_name);
        error_log('School Seats: ' . $school_seats);
        error_log('School id: ' . $school_id);
        error_log('Demo_group_id: ' . $demo_group_id);
        error_log('Principal Name: ' . $principal_first_name . ' ' . $principal_last_name);
        error_log('Principal Phone: ' . $principal_phone);
        error_log('Principal Email: ' . $principal_email);
        error_log('Accounts Name: ' . $accounts_first_name . ' ' . $accounts_last_name);
        error_log('Accounts Email: ' . $accounts_email);
        error_log('IPL Name: ' . $ipl_first_name . ' ' . $ipl_last_name);
        error_log('IPL Phone: ' . $ipl_phone);
        error_log('IPL Email: ' . $ipl_email); */
    }
    

}

// Hook into Fluent Forms after submission status update action
add_action('fluentform/after_submission_status_update', 'handle_after_submission_status_update', 10, 2);



