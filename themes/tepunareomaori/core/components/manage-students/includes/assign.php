<?php 

/* 
* Manage student Classrooms for current year ( Assign )
*/

add_action('wp_ajax_manage_student_classrooms', 'manage_student_classrooms');

function manage_student_classrooms() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'manage_student_classrooms_payload' && 
        !empty($_POST['student_id']) && !empty($_POST['student_name']) && 
       ( isset($_POST['AddedClassroom']) || isset($_POST['RemovedClassroom'])) ) {

        check_ajax_referer('manage_student_classrooms_nonce', 'security');

        global $wpdb;
        $student_id = intval($_POST['student_id']);
        $student_name = sanitize_text_field(($_POST['student_name']));
        $AddedClassroom = intval($_POST['AddedClassroom']);
        $RemovedClassroom = intval($_POST['RemovedClassroom']);

        // Assign classrooms to the student
        if (!empty($AddedClassroom)) {
            // Add the user to the classroom
            $result = groups_join_group($AddedClassroom, $student_id);

            // Check if the user was added successfully
            if (!$result) {
                wp_send_json_error('Failed to add student ID ' . $student_id . ' to group ID ' . $ClassroomId);
                error_log("Failed to add student ID {$student_id} to group ID {$ClassroomId}");
            }          
        }

        // Remove classrooms from the student
        if (!empty($RemovedClassroom)) {
            // Remove the user from the classroom
            $result = groups_leave_group($RemovedClassroom, $student_id);

            // Check if the user was removed successfully
            if (!$result) {
                wp_send_json_error('Failed to remove student ID ' . $student_id . ' from group ID ' . $ClassroomId);
                error_log("Failed to remove student ID {$student_id} from group ID {$ClassroomId}");
            }        
        }

        $student_classrooms_updated_success_message = sprintf(__('The classrooms for "%s" have been updated successfully.', 'tprm-theme'), $student_name);

        echo json_encode(array(
            'student_classrooms_updated_success_message' => $student_classrooms_updated_success_message,
        ));

        wp_die();
    } else {
        error_log( 'Failed to update classrooms: Required fields missing or invalid payload.');
        wp_send_json_error( __('Failed to update classrooms.', 'tprm-theme'));
    }
}
