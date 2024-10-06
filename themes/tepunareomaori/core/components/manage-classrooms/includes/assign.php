<?php 

/* Assign Students */

add_action('wp_ajax_load_this_year_students', 'load_this_year_students_ajax_handler');

function load_this_year_students_ajax_handler() {
    if (isset($_POST['classroom_id']) && isset($_POST['this_classroom_year']) && isset($_POST['school_id'])) {
        $school_id = intval($_POST['school_id']);
        $classroom_id = sanitize_text_field($_POST['classroom_id']);
        $current_classroom_name = sanitize_text_field($_POST['current_classroom_name']);
        $this_classroom_year = sanitize_text_field($_POST['this_classroom_year']);
        $bp_tooltip = sprintf(esc_attr__('Assign this student to %s', 'tprm-theme'), $current_classroom_name);
        
        if ($classroom_id === 'students_without_classroom') {
            // Load students without a classroom
            $students = get_students_without_classroom_for_year($school_id, $this_classroom_year);
        } else {
            // Load students for the specific classroom
            $students = get_classroom_students($classroom_id);
        }

        if (!empty($students)) {
            // Prepare an array to hold student data
            $student_data = [];

            foreach ($students as $student) {
                $student_object = get_userdata($student);
                $student_username = $student_object->user_login;
                $student_display_name = bp_core_get_user_displayname($student);
                
                // Add student data to the array
                $student_data[] = [
                    'id' => $student,
                    'username' => $student_username,
                    'display_name' => $student_display_name,
                ];
            }

            // Sort the students by display name
            usort($student_data, function($a, $b) {
                return strcmp($a['display_name'], $b['display_name']);
            });

            // Output the sorted students
            foreach ($student_data as $student_info) {
                $student_id = $student_info['id'];
                $student_username = $student_info['username'];
                $student_display_name = $student_info['display_name'];
                $student_profile_url = bp_core_get_user_domain($student_id);
                $student_avatar = TPRM_IMG_PATH . 'avatar.svg'; // Update this as needed

                echo '<li id="' . esc_attr($student_id) . '" class="student">';
                echo '<div class="item-avatar student-avatar">';
                echo '<a href="' . esc_url($student_profile_url) . '">';
                echo '<img src="' . esc_url($student_avatar) . '" class="avatar" alt=""/>';
                echo '</a>';
                echo '</div>';
                echo '<div class="student-name">';
                echo '<div class="list-title member-name">';
                echo '<a target="_blank" href="' . esc_url($student_profile_url) . '">';
                echo esc_html($student_display_name);
                echo '</a>';
                echo '</div>';
                echo '</div>';
                echo '<div class="student-username">';
                echo esc_html($student_username);
                echo '</div>';
                echo '<div class="student-action">';
                echo '<button class="toggle-btn toggle-classroom-student"';
                echo ' data-student_id="' . esc_attr($student_id) . '"';
                echo ' data-bp-user-name="' . esc_attr($student_display_name) . '"';
                echo ' type="button" class="button toggle-classroom-student"';
                echo ' data-bp-tooltip-pos="left"';
                echo ' data-bp-tooltip="' . esc_attr($bp_tooltip) . '">';
                echo '<span class="icons" aria-hidden="true"></span> <span class="bp-screen-reader-text"></span>';
                echo '</button>';
                echo '</div>';
                echo '</li>';
            }
        } else {
            echo '<li>' . esc_html__('No students found for this selection.', 'tprm-theme') . '</li>';
        }
    }
    wp_die(); // This is required to terminate immediately and return a proper response.
}

function assign_teachers() {
    // Check if the necessary data is set and valid
    if (isset($_POST['payload']) && $_POST['payload'] === 'assign_teachers_payload'
        && isset($_POST['classroom_id']) && !empty($_POST['classroom_id'])) {

        // Verify nonce for security
        if (!check_ajax_referer('assign_teachers_nonce', 'security', false)) {
            wp_send_json_error(array('message' => 'Security check failed.'));
        }

        // Get and sanitize POST data
        $teacher_ids_to_assign = isset($_POST['teacher_ids_to_assign']) && is_array($_POST['teacher_ids_to_assign']) 
            ? array_map('intval', $_POST['teacher_ids_to_assign']) 
            : [];
        
        $classroom_id = intval($_POST['classroom_id']);

        if (!empty($teacher_ids_to_assign)) {
            // Process assignment of teachers
            foreach ($teacher_ids_to_assign as $teacher_id) {
                // Add teacher to the group
                groups_join_group($classroom_id, $teacher_id);

                // Make the teacher a group admin
                groups_promote_member($teacher_id, $classroom_id, 'admin'); 
                // Possible roles: 'member', 'mod', 'admin'
            }

            wp_send_json_success(array(
                'message' => __('Teachers assigned and promoted to admins successfully.', 'tprm-theme')
            ));
        } else {
            wp_send_json_error(array('message' => 'No teachers to assign.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Invalid request.'));
    }

    wp_die(); // This is required to terminate immediately and return the proper response
}
add_action('wp_ajax_assign_teachers', 'assign_teachers');



function assign_students() {
    // Check if the necessary data is set and valid
    if (isset($_POST['payload']) && $_POST['payload'] === 'assign_students_payload'
        && !empty($_POST['classroom_id']) ) {

        // Verify nonce for security
        check_ajax_referer('assign_students_nonce', 'security');

        // Get and sanitize POST data
        $student_ids_to_assign = isset($_POST['student_ids_to_assign']) && is_array($_POST['student_ids_to_assign']) 
        ? array_map('intval', $_POST['student_ids_to_assign']) 
        : [];
        $student_ids_to_remove = isset($_POST['student_ids_to_remove']) && is_array($_POST['student_ids_to_remove']) 
        ? array_map('intval', $_POST['student_ids_to_remove']) 
        : [];
        $classroom_id = intval($_POST['classroom_id']);

        if (!empty($student_ids_to_assign) || !empty($student_ids_to_remove)) {
            $this_year = get_option('school_year');

            // Process removal of students from old classrooms
            foreach ($student_ids_to_remove as $student_id) {
                groups_leave_group($classroom_id, $student_id);
            }
            $current_classroom_id = '';
            // Process assignment of students to the new classroom
            foreach ($student_ids_to_assign as $student_id) {
                $current_classroom_id = get_student_classroom_for_year($student_id, $this_year);

                if ($current_classroom_id && $current_classroom_id != $classroom_id) {
                    groups_leave_group($current_classroom_id, $student_id);
                }

                // Add the student to the new classroom
                groups_join_group($classroom_id, $student_id);
            }

            wp_send_json_success(
                array(
                    'message' => __('Students assigned successfully.', 'tprm-theme'),
                    '$current_classroom_id' => $current_classroom_id,
                    '$classroom_id' => $classroom_id,
                    )
            );
           
        } else {
            wp_send_json_error(array('message' => 'Invalid data.'));
        }
    } else {
        wp_send_json_error(array('message' => 'Invalid request.'));
    }

    wp_die();
}
add_action('wp_ajax_assign_students', 'assign_students');
