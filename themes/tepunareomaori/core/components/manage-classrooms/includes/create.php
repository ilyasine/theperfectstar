<?php 

add_filter( 'bp_nouveau_get_groups_directory_nav_items', 'add_create_class_nav_item' );

function add_create_class_nav_item( $nav_items ) {

    $nav_items['personal']['text'] =  __( 'Classrooms', 'tprm-theme' );

    return $nav_items;
}

/* 
* Create Classroom
*/

add_action('wp_ajax_create_classroom', 'create_classroom');

function create_classroom() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'create_classroom_payload' && 
    !empty($_POST['school_id']) && !empty($_POST['classroomName']) ) {
        check_ajax_referer('classroom_create_nonce', 'security');

        global $wpdb;
        $this_year = get_option('school_year');
        $school_id = intval($_POST['school_id']);
        $school_name = sanitize_text_field($_POST['school_name']);
        $classroom_name = sanitize_text_field($_POST['classroomName']);
        
        $DirectorsIds = get_school_directors($school_id);
        $school_slug = sanitize_title_with_dashes($school_name);
        $classe_slug = sanitize_title_with_dashes($classroom_name);
        $classroom_slug = $classe_slug . '-' . $this_year . '-' . $school_slug;
        $school_trigram = groups_get_groupmeta($school_id, 'school_trigram');

        //optional
        $TeacherIds = $_POST['TeacherIds'];

        // Check if functions exist
        if ( !function_exists('groups_join_group') || !function_exists('groups_promote_member') ) {
            wp_send_json_error('Required BuddyPress functions are missing.');
            return;
        }

        // Create group if it doesn't exist
        $args = array (
            'name'          => $classroom_name,
            'slug'          => groups_check_slug($classroom_slug),
            'status'        => 'hidden',
        );

        $classroom_exist = BP_Groups_Group::group_exists($classroom_slug);

        if (!$classroom_exist) {
            $classroom_id = groups_create_group($args);
        } else {
            wp_send_json_error(__('Classroom already exists', 'tprm-theme'));
            return;
        }

        // Group created successfully
        if (!empty($classroom_id)) {
            $args = array(
                'meta_query' => array(
                    array(
                        'key' => '_sync_group_id',
                        'value' => $classroom_id,
                    )
                ),
                'post_type' => 'groups',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );
            $bp_group_id = get_posts($args);

            if (!empty($bp_group_id)) {
                $ld_group_id = $bp_group_id[0]->ID;

                // Modify the post's "guid" to match $classe_slug
                $classroom_link = bp_get_groups_directory_permalink() . $classroom_slug . '/';              

                /** Insert school (group parent) **/
                if (!empty($school_id)) {
                    $update_school = $wpdb->update(
                        $wpdb->prefix . 'bp_groups',
                        array('parent_id' => $school_id),
                        array('id' => $classroom_id)
                    );

                    if (false === $update_school) {
                        // Handle update error
                        error_log("Failed to update parent_id for group ID {$classroom_id}");

                        wp_send_json_error('Failed to update School for Classrrom ID' . $classroom_id);
                        return;
                    }
                }
            
                // Update LearnDash group post
                wp_update_post(array(
                    'ID' => $ld_group_id,
                    'guid' => $classroom_link,
                    'post_name' => $classroom_slug,
                ));

                # Generate the classroom code
                //$classroom_code = $school_trigram . $ld_group_id . $classe_slug;

                $base_code = $school_trigram . $ld_group_id . $classe_slug;
                $classroom_code = substr(md5($base_code), 0, 10);

                # Update classroom details
                groups_update_groupmeta($classroom_id, 'invite_status', 'admins');
                groups_update_groupmeta($classroom_id, 'ecole_name', $school_name);
                groups_update_groupmeta($classroom_id, 'ecole_year', $this_year);
                groups_update_groupmeta($classroom_id, 'classroom_code', $classroom_code);

                # Enroll teachers only if there are
                if(!empty($TeacherIds)){
                    
                    foreach ($TeacherIds as $teacherId) {
                        // Add the user to the group
                        $result = groups_join_group($classroom_id, $teacherId);

                        // Check if the user was added successfully
                        if ($result) {
                            // Promote the user to group admin
                            groups_promote_member($teacherId, $classroom_id, 'admin');
                        } else {
                            wp_send_json_error('Failed to add teacher ID ' . $teacherId . 'to group ID ' . $classroom_id);
                            error_log("Failed to add teacher ID {$teacherId} to group ID {$classroom_id}");
                        }
                    }
                }

                # Enroll directors
                foreach ($DirectorsIds as $directorId) {
                    // Add the user to the group
                    $result = groups_join_group($classroom_id, $directorId);

                    // Check if the user was added successfully
                    if ($result) {
                        // Promote the user to group admin
                        groups_promote_member($directorId, $classroom_id, 'admin');
                    } else {
                        wp_send_json_error('Failed to add director ID ' . $directorId . 'to group ID ' . $classroom_id);
                        error_log("Failed to add director ID {$directorId} to group ID {$classroom_id}");
                    }
                }
            }

            //Creating
            $creating_classrrom_header = __('CREATING CLASSROOM', 'tprm-theme');
            $classroom_being_created_message = __('The classroom is being created...', 'tprm-theme');
            $classroom_creation_error_message = __('An error occurred while creating the classroom. Please try again.', 'tprm-theme');
            //Created
            $classroom_created_success_message = sprintf(__('The classroom "%s" has been created successfully. You can access it via the following button', 'tprm-theme'), $classroom_name);
      
            echo json_encode(array(
                'classroom_name' => $classroom_name,
                'classroom_link' => $classroom_link,
                'classroom_being_created_message' => $classroom_being_created_message,
                'classroom_created_success_message' => $classroom_created_success_message,
                'classroom_creation_error_message' => $classroom_creation_error_message

            ));

            wp_die();
        } else {
            wp_send_json_error('Failed to create the classroom group.');
        }
    } else {
        wp_send_json_error('Invalid or missing parameters.');
    }
}


/* 
* delete Classroom
*/

add_action('wp_ajax_delete_classroom', 'delete_classroom');

function delete_classroom() {

     check_ajax_referer('delete_classroom_nonce', 'security');

    // Check if required POST data is set
    if (isset($_POST['payload']) && $_POST['payload'] === 'delete_classroom_payload' && 
        !empty($_POST['classroom_id']) && !empty($_POST['classroom_name'])) {
        
        global $wpdb;
        $classroom_id = intval($_POST['classroom_id']);
        $classroom_name = sanitize_text_field($_POST['classroom_name']);

        // Check if the required function exists
        if (!function_exists('groups_delete_group')) {
            wp_send_json_error('Required BuddyPress functions are missing.');
            return;
        }

        // Attempt to delete the classroom group
        if (groups_delete_group($classroom_id)) {
            $classroom_deleted_success_message = sprintf(__('The classroom "%s" has been deleted successfully.', 'tprm-theme'), $classroom_name);
            wp_send_json_success(array('message' => $classroom_deleted_success_message));
        } else {
            $classroom_deleting_error_message = __('An error occurred while deleting the classroom. Please try again.', 'tprm-theme');
            wp_send_json_error(array('message' => $classroom_deleting_error_message));
        }
    } else {
        wp_send_json_error('Failed to delete the classroom group. Missing required data.');
    }

    wp_die();
}