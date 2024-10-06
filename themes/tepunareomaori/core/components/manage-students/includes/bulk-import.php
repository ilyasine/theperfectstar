<?php

function ks_generate_password() {
    $prefix = 'ks';
    $digits = 5;

    // Generate a random number with the specified number of digits
    $random_number = str_pad(mt_rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);

    // Combine the prefix with the random number
    return $prefix . $random_number;
}


function upload_import_file() {
    check_ajax_referer('bulk_student_create_nonce', 'security');

    $school_id = isset($_POST['school_id']) ? intval($_POST['school_id']) : 0;
	$selectedClassroomslug = isset($_POST['selectedClassroomslug']) ? sanitize_text_field($_POST['selectedClassroomslug']) : '';
    $selectedClassroom = isset($_POST['selectedClassroom']) ? intval($_POST['selectedClassroom']) : 0;
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;

    if (!$file) {
        wp_send_json_error(array('message' => __('No file uploaded.', 'tprm-theme')));
        wp_die();
    }

    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'] . '/students-import/';
    if (!file_exists($upload_path)) {
        wp_mkdir_p($upload_path);
    }

    $uploaded_file = $file['tmp_name'];
	$uploaded_file_name = pathinfo($file['name'], PATHINFO_FILENAME) . '-' . $selectedClassroomslug . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $excelfile = $upload_path . $uploaded_file_name;

    if (move_uploaded_file($uploaded_file, $excelfile)) {
        if (!class_exists('KWFXLSXReader')) {
            require_once TPRM_DEP . 'KWFexcel.php';

            try {
                $reader = new KWFXLSXReader();
                $sheetData = [];

                $reader->open($excelfile);

                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $sheetData[] = $row->toArray(); // Store row data
                    }
                }

                $reader->close();

                $totalRows = count($sheetData) - 1;

                // Return the total rows count and file path
                wp_send_json_success(array(
                    'totalRows' => $totalRows,
                    'filePath' => $excelfile
                ));

            } catch (Exception $e) {
                wp_send_json_error(array('message' => $e->getMessage()));
            }
        }
    } else {
        wp_send_json_error(array('message' => __('Failed to move uploaded file.', 'tprm-theme')));
    }

    wp_die();
}

add_action('wp_ajax_upload_import_file', 'upload_import_file');


function process_excel_rows() {
    check_ajax_referer('bulk_student_create_nonce', 'security');

    global $blog_id, $wpdb;
    $school_id = isset($_POST['school_id']) ? intval($_POST['school_id']) : 0;
    $startRow = isset($_POST['startRow']) ? intval($_POST['startRow']) : 0;
    $chunkSize = 5; // 5 rows per batch
    $excelfile = isset($_POST['excelfile']) ? sanitize_text_field($_POST['excelfile']) : '';   
    $processedRows = isset($_POST['processedRows']) ? intval($_POST['processedRows']) : 0;
    $skippedRows = isset($_POST['skippedRows']) ? intval($_POST['skippedRows']) : 0;
    $totalRows = 0;
    $selectedClassroom = isset($_POST['selectedClassroom']) ? intval($_POST['selectedClassroom']) : 0;
    $selectedClassroomName = bp_get_group_name(groups_get_group($selectedClassroom));
    $classroom_link = bp_get_group_permalink(groups_get_group($selectedClassroom));
    $classroom_students_tab = $classroom_link . 'students';
    $classroom_students_name = sprintf(__('%s Students', 'tprm-theme'), $selectedClassroomName);
    $image_upload_dir = wp_upload_dir();
    $image_dir = $image_upload_dir['basedir'] . '/picture-passwords/';
    

    if (!$excelfile) {
        wp_send_json_error(array('message' => __('Failed to retrieve Excel file.', 'tprm-theme')));
        wp_die();
    }

    if (!class_exists('KWFXLSXReader')) {
        require_once TPRM_DEP . 'KWFexcel.php';
    }

    try {
        $reader = new KWFXLSXReader();
        $reader->open($excelfile);

        foreach ($reader->getSheetIterator() as $sheet) {
            $rowIndex = 0;
            foreach ($sheet->getRowIterator() as $row) {
                $rowIndex++;

                // Skip rows until we reach the startRow
                if ($rowIndex < $startRow) {
                    continue;
                }

               // Stop processing after the chunk size is reached
               if ($rowIndex >= $startRow + $chunkSize) {
                    $nextStartRow = $rowIndex;
                    break 2; // Break out of both loops
                }

                // Verify column count
                $rowData = $row->toArray();
                if (count($rowData) !== 3) {
                    wp_send_json_error(array('message' => __('Invalid column count in row ' . $rowIndex, 'tprm-theme')));
                }

                $studentFirstName = $rowData[0];
                $studentLastName = $rowData[1];
                $passwordType = strtolower(trim($rowData[2]));

                // Check for special characters in the first two columns
                if (preg_match('/[\*\$\&\(\)\+\!\@\#\%\^\{\}\[\]\'\"\,\.\?\/\:\;\|\`~\-\_\=\+\<\>\^\\\]/', $firstName) ||
                    preg_match('/[\*\$\&\(\)\+\!\@\#\%\^\{\}\[\]\'\"\,\.\?\/\:\;\|\`~\-\_\=\+\<\>\^\\\]/', $lastName)) {
                    $skippedRows++;
                    continue; // Skip this row
                }

                // Validate password type
                if ($passwordType !== 'text' && $passwordType !== 'picture') {
                    $skippedRows++;
                    continue; // Skip this row
                }

                // Process the row (e.g., create students)
                /* Start Create student from row */

                // Generate the student username
                $school_trigram = groups_get_groupmeta($school_id, 'school_trigram');
                $student_username = generate_student_username($school_trigram);
                $studentEmail = $student_username . '@tepunareomaori.com';
                $studentPassword = ks_generate_password();

                // Prepare the student data
                $userdata = array(
                    'user_login' => $student_username,
                    'user_pass' => $studentPassword,
                    'user_email' => $studentEmail,
                    'first_name' => $studentFirstName,
                    'last_name' => $studentLastName,
                    'role' => 'student'
                );

                // Create the student user
                $student_id = wp_insert_user($userdata);

                // Check for errors in user creation
                if (is_wp_error($student_id)) {
                    $skippedRows++;
                    continue;
                }

                // Assign the student to the classroom
                if ($selectedClassroom) {
                    $result = groups_join_group($selectedClassroom, $student_id);
                    if ($result) {
                        // Set Student Profile               
                        bp_set_member_type($student_id, 'student');	
                    }else{              
                        $skippedRows++;
                        continue;
                    }
                }
                // Enroll the student to the school as regulare member
                if ($school_id) {
                    groups_join_group($school_id, $student_id);                 
                }
  
                // Update user meta
                update_user_meta($student_id, 'ecole', $school_id);   
                $lang_meta = $wpdb->get_blog_prefix($blog_id) . 'lang';    
                $school_language = groups_get_groupmeta($school_id, 'ecole_lang');            
                update_user_meta($student_id, $lang_meta, $school_language);

                // Student Password
                update_user_meta($student_id, 'password_type', $passwordType);

                if($passwordType== "picture" && !empty($student_id)){
                    $picture_image = glob($image_dir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE)[$rowIndex] ?? null;
                    $student_picture_image = basename($picture_image);
                    update_user_meta($student_id, 'picture_password_image',$student_picture_image);
                    update_user_meta($student_id, 'text_password','');
                }else if($passwordType== 'text' && !empty($student_id)){
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

                $processedRows++;

                /* End Create student from row */
  

                // Send progress update after each row
                $response = array(
                    'success' => true,
                    'nextStartRow' => $rowIndex + 1,
                    'processedRows' => $processedRows,
                    'skippedRows' => $skippedRows,
                    'selectedClassroom' => $selectedClassroom,
                    'selectedClassroomName' => $selectedClassroomName,
                    'classroom_students_tab' => $classroom_students_tab,
                    'classroom_students_name' => $classroom_students_name,
                    'school_trigram' => $school_trigram,
                    'school_id' => $school_id,
                );
                wp_send_json_success($response);
            }
        }

        $reader->close();

    } catch (Exception $e) {
        wp_send_json_error(array('message' => $e->getMessage()));
    }

    wp_die();
}
add_action('wp_ajax_process_excel_rows', 'process_excel_rows');

