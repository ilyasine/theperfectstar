<?php 


/* 
* Duplicate_structure
*/

add_action('wp_ajax_duplicate_structure', 'duplicate_structure');

function duplicate_structure() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'duplicate_structure_payload' && !empty($_POST['school_id'])  ) {
        check_ajax_referer('duplicate_structure_nonce', 'security');

        global $wpdb;
        $this_year = get_option('school_year');
        $previous_year = get_previous_year();
        $school_id = intval($_POST['school_id']);
        $classrooms_ids = get_school_classrooms_for_year($school_id, $previous_year);
        $school_object = groups_get_group($school_id);

        foreach($classrooms_ids as $classroom_id){
            $classroom = groups_get_group($classroom_id); // get classroom object
            $old_classroom_id = $classroom->id;
            $classroom_name = $classroom->name; //get classroom meta
            $school_name = groups_get_groupmeta($classroom_id, 'ecole_name');
            $classroom_level = groups_get_groupmeta($classroom_id, 'classroom_level');
            $classroom_teachers = get_classroom_teachers($classroom_id);
            $school_directors = get_school_directors($school_id);
            $school_admins = get_school_admins($school_id);
            $school_slug = $school_object->slug;
            $classe_slug = sanitize_title_with_dashes($classroom_name);
            $classroom_slug = $classe_slug . '-' . $this_year . '-' . $school_slug;
            $school_trigram = groups_get_groupmeta($school_id, 'school_trigram');
            [
                'curriculum' => $curriculum, //(group type)
                'front_curriculum' => $front_curriculum,
                'classroom_year' => $classroom_year,
            ] = get_curriculum_from_classroom_level($school_id, $classroom_level);

            // Check if functions exist
            if (!function_exists('groups_join_group') || !function_exists('groups_promote_member')) {
                wp_send_json_error('Required BuddyPress functions are missing.');
                wp_die();
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
                wp_die();
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

                    // Insert curriculum (group type) 
                    if (!empty($curriculum)) {
                        $group_type_args = array(
                            'meta_query' => array(
                                array(
                                    'key' => '_bp_group_type_key',
                                    'value' => $curriculum,
                                ),
                            ),
                            'post_type' => 'bp-group-type',
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                        );

                        $group_type_post = get_posts($group_type_args);

                        if (!empty($group_type_post)) {
                            $group_type_post_id = $group_type_post[0]->ID;
                            $group_type_slug = get_post_meta($group_type_post_id, '_bp_group_type_key', true);
                            wp_set_post_terms($classroom_id, $group_type_slug, 'bp_group_type', false);
                        }
                    }

                    // Insert school (group parent) 
                    if (!empty($school_id)) {
                        $update_school = $wpdb->update(
                            $wpdb->prefix . 'bp_groups',
                            array('parent_id' => $school_id),
                            array('id' => $classroom_id)
                        );

                        if (false === $update_school) {
                            // Handle update error
                            error_log("Failed to update parent_id for group ID {$classroom_id}");

                            wp_send_json_error('Failed to update School for Classroom ID' . $classroom_id);
                            wp_die();
                        }
                    }

                    // Get all courses that have the $curriculum category
                    $args = array(
                        'post_type' => 'sfwd-courses',
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'ld_course_category',
                                'field' => 'slug',
                                'terms' => $curriculum,
                            ),
                        ),
                    );

                    $courses = get_posts($args);
                    $courses_ids = array();
                    // Insert courses into the given group type
                    if (!empty($curriculum)) {
                        foreach ($courses as $course) {
                            $course_slug = $course->post_name;
                            $course_id = get_page_by_path($course_slug, OBJECT, 'sfwd-courses')->ID;
                            $courses_ids[] = $course_id;
                            update_post_meta($course_id, 'learndash_group_enrolled_' . $ld_group_id, time());
                        }
                    }

                    // Update LearnDash group post
                    wp_update_post(array(
                        'ID' => $ld_group_id,
                        'guid' => $classroom_link,
                        'post_name' => $classroom_slug,
                    ));

                    // Generate the classroom code
                    $base_code = $school_trigram . $ld_group_id . $classe_slug;
                    $classroom_code = substr(md5($base_code), 0, 10);

                    // Update classroom details
                    groups_update_groupmeta($classroom_id, 'invite_status', 'admins');
                    groups_update_groupmeta($classroom_id, 'ecole_name', $school_name);
                    groups_update_groupmeta($classroom_id, 'ecole_year', $this_year);
                    groups_update_groupmeta($classroom_id, 'classroom_year', $classroom_year);
                    groups_update_groupmeta($classroom_id, 'classroom_level', $classroom_level);
                    groups_update_groupmeta($classroom_id, 'classroom_code', $classroom_code);
                    //groups_update_groupmeta($classroom_id, 'previous_classroom', $old_classroom_id);

                    // Enroll teachers only if there are
                    if(!empty($classroom_teachers)){
                        foreach ($classroom_teachers as $classroom_teacher) {
                            // Add the user to the group
                            $result = groups_join_group($classroom_id, $classroom_teacher);

                            // Check if the user was added successfully
                            if ($result) {
                                // Promote the user to group admin
                                $member    = new BP_Groups_Member( $classroom_teacher, $classroom_id);
                                $member->promote( 'admin' );
                                
                            } else {
                                wp_send_json_error('Failed to add teacher ID ' . $classroom_teacher . ' to group ID ' . $classroom_id);
                                error_log("Failed to add teacher ID {$classroom_teacher} to group ID {$classroom_id}");
                                wp_die();
                            }
                        }
                    }

                    // Enroll directors
                    foreach ($school_directors as $school_director) {
                        // Add the user to the group
                        $result = groups_join_group($classroom_id, $school_director);

                        // Check if the user was added successfully
                        if ($result) {
                            // Promote the user to group admin
                            $member    = new BP_Groups_Member( $school_director, $classroom_id);
                            $member->promote( 'admin' );

                        } else {
                            wp_send_json_error('Failed to add director ID ' . $school_director . ' to group ID ' . $classroom_id);
                            error_log("Failed to add director ID {$school_director} to group ID {$classroom_id}");
                            wp_die();
                        }
                    }

                    // Enroll school admins
                    foreach ($school_admins as $school_admin) {
                        // Add the user to the group
                        $result = groups_join_group($classroom_id, $school_admin);

                        // Check if the user was added successfully
                        if ($result) {
                           // Promote the user to group admin
                           $member    = new BP_Groups_Member( $school_admin, $classroom_id);
                           $member->promote( 'admin' );

                        } else {
                            wp_send_json_error('Failed to add director ID ' . $school_admin . ' to group ID ' . $classroom_id);
                            error_log("Failed to add director ID {$school_admin} to group ID {$classroom_id}");
                            wp_die();
                        }
                    }
                }

            } else {
                wp_send_json_error('Failed to create the classroom group.');
                wp_die();
            }
        }

        wp_send_json_success('Structure duplicated successfully.');
    } else {
        wp_send_json_error('Invalid or missing parameters.');
    }

    wp_die(); // This will terminate the execution and return proper response to the AJAX call
}


/* add_action('wp_ajax_load_previous_students', 'load_students_ajax_handler');
add_action('wp_ajax_nopriv_load_previous_students', 'load_students_ajax_handler');

function load_students_ajax_handler() {
    if (isset($_POST['classroom_id'])) {
        $classroom_id = intval($_POST['classroom_id']);
        $students = get_classroom_students($classroom_id);
        $this_year = get_option('school_year');
        $bp_tooltip = sprintf(esc_attr__('Promote this student to %s', 'tprm-theme'), $this_year);
        
        if (!empty($students)) {
            foreach ($students as $student) {
                $student_object = get_userdata($student);
                $student_username = $student_object->user_login;
                $student_display_name = bp_core_get_user_displayname($student);
                $student_profile_url = bp_core_get_user_domain($student);
                $student_avatar = TPRM_IMG_PATH . 'avatar.svg'; // Update this as needed

                echo '<li id="' . esc_attr($student) . '" class="student">';
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
                echo ' data-student_id="' . esc_attr($student) . '"';
                echo ' data-bp-user-name="' . esc_attr($student_display_name) . '"';
                echo ' type="button" class="button toggle-classroom-student"';
                echo ' data-bp-tooltip-pos="left"';
                echo ' data-bp-tooltip="' . $bp_tooltip . '">';
                echo '<span class="icons" aria-hidden="true"></span> <span class="bp-screen-reader-text"></span>';
                echo '</button>';
                echo '</div>';
                echo '</li>';
            }
        } else {
            echo '<li class="nostudent">' . esc_html__('No students found for this classroom.', 'tprm-theme') . '</li>';
        }
    }
    wp_die(); // This is required to terminate immediately and return a proper response.
}
 */

add_action('wp_ajax_load_previous_students', 'load_students_ajax_handler');
add_action('wp_ajax_nopriv_load_previous_students', 'load_students_ajax_handler');

function load_students_ajax_handler() {
    if (isset($_POST['classroom_id'])) {
        $classroom_id = intval($_POST['classroom_id']);
        $students = get_classroom_students($classroom_id);
        $this_year = get_option('school_year');
        $bp_tooltip = sprintf(esc_attr__('Promote this student to %s', 'tprm-theme'), $this_year);
        
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
                echo ' data-bp-tooltip="' . $bp_tooltip . '">';
                echo '<span class="icons" aria-hidden="true"></span> <span class="bp-screen-reader-text"></span>';
                echo '</button>';
                echo '</div>';
                echo '</li>';
            }
        } else {
            echo '<li class="nostudent">' . esc_html__('No students found for this classroom.', 'tprm-theme') . '</li>';
        }
    }
    wp_die(); // This is required to terminate immediately and return a proper response.
}
 

function promote_students() {
    if (isset($_POST['payload']) && $_POST['payload'] == 'promote_students_payload'
        && !empty($_POST['classroom_id'])) {
        
        check_ajax_referer('promote_students_nonce', 'security');

        $student_ids_to_promote = $_POST['student_ids_to_promote'];
        $student_ids_to_demote = $_POST['student_ids_to_demote'];
        $classroom_id = intval($_POST['classroom_id']);

        if (!empty($student_ids_to_promote) || !empty($student_ids_to_demote)) {
            $this_year = get_option('school_year');
            
            // Process demotion
            foreach ($student_ids_to_demote as $student_id) {
                $current_classroom_id = get_student_classroom_for_year($student_id, $this_year);
                groups_leave_group($current_classroom_id, $student_id);
            }

            // Process promotion
            foreach ($student_ids_to_promote as $student_id) {
                $current_classroom_id = get_student_classroom_for_year($student_id, $this_year);
                
                if ($current_classroom_id && $current_classroom_id != $classroom_id) {
                    groups_leave_group($current_classroom_id, $student_id);
                }

                // Add the student to the new classroom
                groups_join_group($classroom_id, $student_id);
            }

            wp_send_json_success(array('message' => __('Students promoted successfully.', 'tprm-theme')));
        } else {
            wp_send_json_error(array('message' => 'Invalid data.'));
        }
    }

    wp_die();
}
add_action('wp_ajax_promote_students', 'promote_students');
