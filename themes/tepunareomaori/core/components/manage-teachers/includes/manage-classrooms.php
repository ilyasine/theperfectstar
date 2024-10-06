<?php 

/* 
* Manage teacher Classrooms
*/

add_action('wp_ajax_manage_teacher_classrooms', 'manage_teacher_classrooms');

function manage_teacher_classrooms() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'manage_teacher_classrooms_payload' && 
        !empty($_POST['teacher_id']) && !empty($_POST['teacher_name']) && 
       ( isset($_POST['AddedClassrooms']) || isset($_POST['RemovedClassrooms'])) ) {

        check_ajax_referer('manage_teacher_classrooms_nonce', 'security');

        global $wpdb;
        $teacher_id = intval($_POST['teacher_id']);
        $teacher_name = sanitize_text_field(($_POST['teacher_name']));
        $AddedClassrooms = $_POST['AddedClassrooms'];
        $RemovedClassrooms = $_POST['RemovedClassrooms'];

        // Assign classrooms to the teacher
        if (!empty($AddedClassrooms)) {
            foreach ($AddedClassrooms as $ClassroomId) {
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

        // Remove classrooms from the teacher
        if (!empty($RemovedClassrooms)) {
            foreach ($RemovedClassrooms as $ClassroomId) {
                // Remove the user from the classroom
                $result = groups_leave_group($ClassroomId, $teacher_id);

                // Check if the user was removed successfully
                if ($result) {
                    // Get associated LearnDash group ID and delete user meta
                    $ld_group_id = bp_ld_sync('buddypress')->helpers->getLearndashGroupId($ClassroomId);
                    delete_user_meta($teacher_id, 'learndash_group_leaders_' . $ld_group_id);
                } else {
                    wp_send_json_error('Failed to remove teacher ID ' . $teacher_id . ' from group ID ' . $ClassroomId);
                    error_log("Failed to remove teacher ID {$teacher_id} from group ID {$ClassroomId}");
                }
            }
        }

        $teacher_classrooms_updated_success_message = sprintf(__('The classrooms for teacher "%s" have been updated successfully.', 'tprm-theme'), $teacher_name);

        echo json_encode(array(
            'teacher_classrooms_updated_success_message' => $teacher_classrooms_updated_success_message,
        ));

        wp_die();
    } else {
        error_log( 'Failed to update classrooms: Required fields missing or invalid payload.');
        wp_send_json_error( __('Failed to update classrooms.', 'tprm-theme'));
    }
}
