<?php 

use FluentForm\App\Models\EntryDetails;
use FluentForm\App\Services\Submission\SubmissionService;
use FluentForm\App\Services\FluentConversational\Classes\Form;
use FluentCrm\App\Models\Subscriber;
use FluentCrm\App\Models\Tag;

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

/* add_action('init', 'gettags');

function gettags(){

    // Get the user ID (replace with actual user ID)
    $user_id = 13511;

    // Get or create the new tags
    $new_tags = [$fluentcrm_tag];
    $new_tag_ids = array_map('get_or_create_fluentcrm_tag', $new_tags);

    // Convert new tag IDs to strings
    $new_tag_ids = array_map('strval', $new_tag_ids);

    // Get the subscriber by user ID
    $subscriber = Subscriber::where('user_id', $user_id)->first();

    // Check if the subscriber exists
    if ($subscriber) {
        // Attach new tags to the subscriber
        $subscriber->attachTags($new_tag_ids);
        $subscriber->save();

        // Retrieve existing tags from user meta
        $existing_tags = get_user_meta($user_id, 'fluentcrm_tags', true);
        if (!is_array($existing_tags)) {
            $existing_tags = [];
        }

        // Merge existing tags with new tags
        $updated_tags = array_unique(array_merge($existing_tags, $new_tag_ids));

        // Update the user meta with the combined list of tag IDs
        update_user_meta($user_id, 'fluentcrm_tags', $updated_tags);


    } else {
        echo 'Subscriber not found.';
    }
}

// Function to get or create a FluentCRM tag
function get_or_create_fluentcrm_tag($tag_name) {
    $tag = Tag::firstOrCreate(['title' => $tag_name]);
    return $tag->id;
}

// Helper function to add tags in FluentCRM
function fluentcrm_add_contact_tags($user_id, $tag_ids) {
    if (function_exists('FluentCrm\App\Services\Libs\ContactApi')) {
        $contact = FluentCrm\App\Services\Libs\ContactApi::createOrUpdate($user_id, ['tags' => $tag_ids]);
        return $contact;
    }
    return false;
} */

/**
 * Callback function to handle actions after Fluent Forms submission status update.
 *
 * @param int    $submissionId The ID of the form submission.
 * @param string $status       The new status of the submission (e.g., 'read', 'unread', 'pending', 'active','trashed').
 */
function handle_after_submission_status_update($submissionId, $status) {

    if ($status === 'read') {
        // Perform school creation actions when the status is 'read' //Active in our case
        error_log("Process school, Submission is active");
    
        // Get entry details by submission ID
        $entryDetails = EntryDetails::where('submission_id', $submissionId)->get();
    
        // Initialize variables
        $school_name = '';
        $school_seats = '';
        $school_year = '';
        $school_id = '';
        $demo_group_id = '';
        $principal_email = '';
        $fluentcrm_tag = '';

       // Principal details
       $principal_details = array();

       // School leaders in repeater field
       $school_leaders = array();
    
       foreach ($entryDetails as $detail) {
            if ($detail->field_name === 'school_name') {
                $school_name = $detail->field_value;
            }

            if ($detail->field_name === 'school_seats') {
                $school_seats = $detail->field_value;
            }

            if ($detail->field_name === 'school_year') {
                $school_year = $detail->field_value;
            }

            if ($detail->field_name === 'fluentcrm_tag') {
                $fluentcrm_tag = $detail->field_value;
            }
            //school_roll_july_2022 ==> school_seats

            // Extracting Principal details
            if ($detail->field_name === 'names_principal') {
                if ($detail->sub_field_name === 'first_name') {
                    $principal_details['first_name'] = $detail->field_value;
                }
                if ($detail->sub_field_name === 'last_name') {
                    $principal_details['last_name'] = $detail->field_value;
                }
            }
            if ($detail->field_name === 'phone_principal') {
                $principal_details['phone'] = $detail->field_value;
            }
            if ($detail->field_name === 'email_principal') {
                $principal_details['email'] = $detail->field_value;
                $principal_email = $principal_details['email'];
            }

            // Extract repeater field data for school leaders
            if ($detail->field_name === 'school_leaders') {
                $leader_data = unserialize($detail->field_value);
                if (is_array($leader_data) && count($leader_data) === 4) {
                    $school_leaders[] = array(
                        'first_name' => $leader_data[0],
                        'last_name'  => $leader_data[1],
                        'phone'      => $leader_data[2],
                        'email'      => $leader_data[3]
                    );
                }
            }
        }

        $school_result = create_school($school_name, $school_seats, $school_year, $fluentcrm_tag);

        $product_slug = 'schools-sign-up';
        $product_id = get_product_id_by_slug($product_slug);  

        if (!is_wp_error($school_result)) {
            $school_id = $school_result['school_id'];
            $demo_group_id = $school_result['demo_group_id'];

            $principal_id = create_admins($school_name, $school_id, $demo_group_id, $principal_details, $school_leaders, $fluentcrm_tag);
            
            if ($product_id && $school_name && $school_seats && $principal_id) {
                $order_id = create_school_order($product_id, $school_name, $school_seats, $principal_email, $principal_id);
                error_log("Order created with ID: $order_id");
            } else {
                error_log('Missing product ID or school details; order creation skipped.');
            }
            
        } else {
            error_log('Error creating school: ' . $school_result->get_error_message());
        }

    }
    
}

// Hook into Fluent Forms after submission status update action
add_action('fluentform/after_submission_status_update', 'handle_after_submission_status_update', 10, 2);


function create_school_order($product_id, $school_name, $school_seats, $school_email, $principal_id) {
    // Create a new order
    $order = wc_create_order();

    // Set the quantity for the order based on school seats
    $product = wc_get_product($product_id);
    if ($product) {
        $order->add_product($product, $school_seats); // Quantity set to school_seats
    }

    // Set the billing details
    $order->set_billing_first_name($school_name);  // Setting school name as the "First Name" in billing
    $order->set_billing_last_name(''); // Optional, leave empty or add more details if needed
    $order->set_billing_email($school_email); // Set a default email or use a specific one for each school

    // Add the school name as order meta for clarity
    $order->update_meta_data('School Name', $school_name);

    // Calculate totals and save the order
    $order->calculate_totals();
    $order->save();

    $order->calculate_totals();
    $order->payment_complete();
    $order->update_status('completed');

    $order_id = $order->get_id();

    update_post_meta($order_id, '_customer_user', $principal_id);

    return $order_id;
}



function get_product_id_by_slug($slug) {
    if (!is_string($slug) || empty($slug)) {
        // Validate input to ensure it's a non-empty string.
        return null;
    }

    // Sanitize the slug to avoid any potential security issues.
    $sanitized_slug = sanitize_title($slug);

    $product = get_page_by_path($sanitized_slug, OBJECT, 'product');
    if ($product && is_a($product, 'WP_Post')) {
        return (int) $product->ID; // Cast ID to integer for consistency.
    }
    return null;
}