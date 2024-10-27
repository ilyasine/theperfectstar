<?php 
add_action('wp_ajax_std_cred_generate_excel_file', 'generate_students_credentials_excel_file');
add_action('wp_ajax_nopriv_std_cred_generate_excel_file', 'generate_students_credentials_excel_file');
add_action('wp_ajax_delete_excel_file', 'delete_excel_file');
add_action('wp_ajax_nopriv_delete_excel_file', 'delete_excel_file');
add_action('wp_ajax_std_cred_generate_pdf_file', 'generate_students_credentials_pdf_file');
add_action('wp_ajax_nopriv_std_cred_generate_pdf_file', 'generate_students_credentials_pdf_file');


/**
 *  EXCEL Students Credentials
 *
 * @since V2
 */

 function generate_students_credentials_excel_file(){

	global $wpdb, $TPRM_ajax_nonce ;

	$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );

	$std_cred_tbl = $wpdb->prefix . "students_credentials";

	if ( isset( $_POST['payload'] )  && $_POST['payload'] == 'students_credentials_excel' ) {

		// verify nonce
		check_ajax_referer( 'TPRM_nonce' ,'security');

		$group_type = bp_groups_get_group_type(bp_get_current_group_id());
		$group_members = array();
		$std_names = array();
		$std_emails = array();
		$std_usernames = array();
		$std_passwords = array();
		$std_classroms = array();
		$std_status = array();
		$status_colors = array();
		$status_bg_colors = array();

		$group_members = groups_get_group_members(array(
			'group_id'            => bp_get_current_group_id(),
			'exclude_admins_mods' => true,
			'exclude_banned'      => true,
			'exclude'             => false,
			'group_role'          => array('member'),
			'search_terms'        => false,
		));

		$students_credentials = $group_members['members'];

		foreach ($students_credentials as $student) {
			$id = intval($student->id);
			$username = sanitize_text_field($student->user_login);
			$email = sanitize_text_field($student->user_email);
		
			$sql = $wpdb->prepare("SELECT stdcred, first_name, last_name FROM " . esc_sql($std_cred_tbl) . " WHERE username = %s", esc_sql($username));
			$stdcred_object = $wpdb->get_results($sql, OBJECT);
			$passwordType = get_user_meta($id, 'password_type', true);
			if($passwordType == "picture"){ 
				$picturePassword = get_user_meta($id, 'picture_password_image', true);
				$stdcred = $picturePassword;
			}else{	
				$stdcred = $stdcred_object[0]->stdcred; 				
			}
			$first_name = get_user_meta( $id, 'first_name', true );
			$last_name = get_user_meta( $id, 'last_name', true );
			$name = $first_name . ' ' . $last_name;		
			$classe = '';		
			$classroom_id = get_student_classroom_for_year($id); 
					
			if($classroom_id){
				$classe = bp_get_group_name(groups_get_group($classroom_id));
			}				

			$account_status = '';
			$text_status = '';

			if (is_active_student($id)) {
				$account_status = __('Active', 'tprm-theme');
				$text_status = 'tprm-active';
				$status_color = '#ffffff';
				$status_bg_color = '#2e9e9e';
			} else {
				$account_status = __('Inactive', 'tprm-theme');
				$text_status = 'tprm-inactive';
				$status_color = '#000000';
				$status_bg_color = '';
			}
		
			// Save students data in arrays to be printed
			$std_names[] = $name;
			$std_emails[] = $email;
			$std_usernames[] = $username;
			$std_passwords[] = $stdcred;
			$std_classroms[] = ($group_type == 'tprm-school') ? $classe : bp_get_current_group_name();
			$std_status[] = $account_status;
			$status_colors[] = $status_color;
			$status_bg_colors[] = $status_bg_color;
		}

		$stdcount = $group_members["count"];;

		if ( !empty($name) && !empty($email) && !empty($username) && !empty($std_passwords) && !empty($account_status) && $stdcount > 0 ){

			// Excel

			// orange : #1c5cb2
			// green : #2e9e9e
			// green light : #a1cecc
			// font : Nunito
			// head text color : #ffffff
			// body text color : #000000

			//File headers :
			$std_name = __("Name", "tprm-theme"); 
			$std_email = __("Email", "tprm-theme"); 
			$std_username = __("Username", "tprm-theme");
			$std_password = __("Password", "tprm-theme");
			$std_classrom = __("Classroom", "tprm-theme"); 
			$account_status_name = __("Account Status", "tprm-theme"); 

			$students_data = [
				['<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>' . $std_name . '</center></middle></style>',
				'<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>' . $std_email . '</center></middle></style>',
				'<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>' . $std_username . '</center></middle></style>',
				'<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>' . $std_password . '</center></middle></style>',
				'<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>' . $std_classrom . '</center></middle></style>',
				'<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>' . $account_status_name . '</center></middle></style>',
				'<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>'
				],
			];
			
			for ($i = 0; $i < $stdcount; $i++) {
				// Create a row for the loop
				$row = [
					'<style height="30"><middle><center>' . $std_names[$i] . '</center></middle></style>',
					'<style height="30"><middle><center>' . $std_emails[$i] . '</center></middle></style>',
					'<style height="30"><middle><center>' . $std_usernames[$i] . '</center></middle></style>',
					'<style height="30"><middle><center>' . $std_passwords[$i] . '</center></middle></style>',
					'<style height="30"><middle><center>' . $std_classroms[$i] . '</center></middle></style>',
					'<style bgcolor="' . $status_bg_colors[$i] . '" color="' . $status_colors[$i] . '" height="30"><middle><center>' . $std_status[$i] . '</center></middle></style>',
				];
			
				// Add the row to the $classes array
				$students_data[] = $row;
			}		

			/* error_log('Name : ' . $name);
			error_log('Email : ' . $email);
			error_log('Username : ' . $username);
			error_log('Password : ' . $std_password);
			error_log('Classroom : ' . $classe);
			error_log('Account status : ' . $account_status); */

			// Get the current user's ID
			$current_user_id = get_current_user_id();

			// Get the user data for the current user
			$current_user = get_userdata($current_user_id);

			// Author (current user's name and email)
			$author = $current_user->display_name . ' <' . $current_user->user_email . '>';

			// Last Modified By (same as author)
			$lastModifiedBy = $author;

			$parent_id = bp_get_parent_group_id(bp_get_current_group_id());
			if ($group_type == 'tprm-school') {
				$ecole_name = bp_get_current_group_name();
			} else {
				if (!empty($parent_id)) {
					$parent_group = groups_get_group(array('group_id' => $parent_id));
					$ecole_name = bp_get_current_group_name() . ' - ' . $parent_group->name;
				}
			}


			$sheet_name = sprintf( __( 'Student Credentials - %s', 'tprm-theme' ), $ecole_name );

			$ecole_name_formatted = strtolower(str_replace(' ', '_', $ecole_name));

			$company = 'tepunareomaori <kiaora@tepunareomaori.co.nz>';

			$title = sprintf( __( 'Students Credentials : %s %s at %s', 'tprm-theme' ), $ecole_name, date('d-m-Y'), date('H:i:s') );

			$description = sprintf( __( 'Excel file contains the credentials of the students of the school %s', 'tprm-theme' ), $ecole_name );

			$filename = 'students_credentials_' . $ecole_name_formatted . '_' . date('Y_m_d') . '-' . str_replace(':', '_', date('H:i:s')) . '.xlsx';

			// Get the WordPress upload directory path
			$upload_dir = wp_upload_dir();

			// Save the Excel file to the upload directory
			$excel_file_path = $upload_dir['basedir'] . '/' . $filename;

			if (!class_exists('KWFxlsx')) {
				
				require_once TPRM_DEP . 'KWFxlsx.php';
	
				// Your existing code to generate the Excel file...
				try {
					$xlsx = new KWFxlsx();
					$xlsx->setAuthor($author)
						->setCompany($company)
						->setManager($lastModifiedBy)
						->setLastModifiedBy($lastModifiedBy)
						->setTitle($title)
						->setDescription($description)
						->setDefaultFont('Nunito')
						->setDefaultFontSize(14)
						->addSheet( $students_data, $sheet_name )
						->saveAs($excel_file_path); 

						// After saving the file, construct the download link
						//$download_link = esc_url($upload_dir['baseurl'] . '/' . $filename);

						// Return the download link in the response
						//echo '<a href="' . $download_link . '" download>' . __('Excel') . '</a>';

					// After saving the file, construct the download link
					$download_link = esc_url($upload_dir['baseurl'] . '/' . $filename);

					// Return both the download link and the file path in the response
					echo json_encode(array(
						'download_link' => $download_link,
						'file_path' => $excel_file_path,
					));
				

				} catch (Exception $e) {
					error_log('Error when generating Excel file: ' . $e->getMessage());
				}
			}
			
		}
		
		exit();
	}
}


/**
 *  Delete EXCEL Students Credentials file after being downloaded
 *
 * @since V2
 */

function delete_excel_file() {
     
	// Get the path of the file to be deleted from the AJAX request
	$excel_file_path = sanitize_text_field($_POST['file_path']);


	// Check if the file exists and delete it
	if (file_exists($excel_file_path)) {
		unlink($excel_file_path);
		//echo 'File deleted from the server.';
	} else {
		// echo 'File not found on the server.';
	}
   
    // Always exit to prevent further output
    exit();
}

/**
 *  PDF Students Credentials
 *
 * @since V2
 */

function generate_students_credentials_pdf_file(){

	global $wpdb, $TPRM_ajax_nonce ;

	$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );

	$std_cred_tbl = $wpdb->prefix . "students_credentials";

	if ( isset( $_POST['payload'] ) && $_POST['payload'] == 'students_credentials_pdf' ) {

		// verify nonce
		check_ajax_referer( 'TPRM_nonce' ,'security');

		$group_type = bp_groups_get_group_type(bp_get_current_group_id());
		$group_members = array();
		$std_names = array();
		$std_emails = array();
		$std_usernames = array();
		$std_passwords = array();
		$std_classroms = array();
		$std_status = array();
		$status_colors = array();
		$status_bg_colors = array();

		$group_members = groups_get_group_members(array(
			'group_id'            => bp_get_current_group_id(),
			'exclude_admins_mods' => true,
			'exclude_banned'      => true,
			'exclude'             => false,
			'group_role'          => array('member'),
			'search_terms'        => false,
		));

		$students_credentials = $group_members['members'];

		foreach ($students_credentials as $student) {
			$id = sanitize_text_field($student->id);
			$username = sanitize_text_field($student->user_login);
			$email = sanitize_text_field($student->user_email);
		
			$sql = $wpdb->prepare("SELECT stdcred, first_name, last_name FROM " . esc_sql($std_cred_tbl) . " WHERE username = %s", esc_sql($username));
			$stdcred_object = $wpdb->get_results($sql, OBJECT);
			$passwordType = get_user_meta($id, 'password_type', true);
			if($passwordType == "picture"){ 
				$picturePassword = get_user_meta($id, 'picture_password_image', true);
				$stdcred = $picturePassword;
			}else{
				$stdcred = $stdcred_object[0]->stdcred;
			}
			$first_name = get_user_meta( $id, 'first_name', true );
			$last_name = get_user_meta( $id, 'last_name', true );
			$name = $first_name . ' ' . $last_name;	
			$classe = '';		
			$classroom_id = get_student_classroom_for_year($id);
		
			if($classroom_id){                                      
				$classe = bp_get_group_name(groups_get_group($classroom_id));
			}				

			$account_status = '';
			$text_status = '';

			if (is_active_student($id)) {
				$account_status = __('Active', 'tprm-theme');
				$text_status = 'tprm-active';
				$status_color = '#ffffff';
				$status_bg_color = '#2e9e9e';
			} else {
				$account_status = __('Inactive', 'tprm-theme');
				$text_status = 'tprm-inactive';
				$status_color = '#000000';
				$status_bg_color = '';
			}
		
			// Save students data in arrays to be printed
			$std_names[] = $name;
			$std_emails[] = $email;
			$std_usernames[] = $username;
			$std_passwords[] = $stdcred;
			$std_classroms[] = ($group_type == 'tprm-school') ? $classe : bp_get_current_group_name();
			$std_status[] = $account_status;
			$status_colors[] = $status_color;
			$status_bg_colors[] = $status_bg_color;
		}

		$stdcount = count($students_credentials);

		if ( !empty($name) && !empty($email) && !empty($username) && !empty($std_passwords) && !empty($classe) && !empty($account_status) && $stdcount > 0 ){

			//File headers :
			$std_name = __("Name", "tprm-theme"); 
			$std_email = __("Email", "tprm-theme"); 
			$std_username = __("Username", "tprm-theme");
			$std_password = __("Password", "tprm-theme");
			$std_classrom = __("Classroom", "tprm-theme"); 
			$account_status_name = __("Account Status", "tprm-theme");  

			$students_header_data = [ $std_name , $std_email ,  $std_username, $std_password, $std_classrom ,  $account_status_name] ;
									
			for ($i = 0; $i < $stdcount; $i++) {
				// Create a row for the loop
				$row = array(
					'std_name' => $std_names[$i],
					'std_email' => $std_emails[$i],
					'std_username' => $std_usernames[$i],
					'std_password' => $std_passwords[$i],
					'std_classrom' => $std_classroms[$i],
					'account_status' => $std_status[$i],
					'status_bg_color' => $status_bg_colors[$i],
					'status_color' => $status_colors[$i],
				);
			
				// Add the row to the students_data array
				$students_data[] = $row;
			}

			// Get the current user's ID
			$current_user_id = get_current_user_id();

			// Get the user data for the current user
			$current_user = get_userdata($current_user_id);

			// Get the user's first and last name
			$first_name = get_user_meta($current_user_id, 'first_name', true);
			$last_name = get_user_meta($current_user_id, 'last_name', true);

			$author_email = $current_user->user_email;

			$author_name = $first_name . ' ' . $last_name;

			// Author (current user's name and email)
			$author = $author_name . ' <' . $author_email . '>';

			$parent_id = bp_get_parent_group_id(bp_get_current_group_id());
			if ($group_type == 'tprm-school') {
				$ecole_name = bp_get_current_group_name();
			} else {
				if (!empty($parent_id)) {
					$parent_group = groups_get_group(array('group_id' => $parent_id));
					$ecole_name = bp_get_current_group_name() . ' - ' . $parent_group->name;
				}
			}

			$year = date("Y");

			$sheet_name = sprintf( __( 'Student Credentials - %s', 'tprm-theme' ), $ecole_name );

			$ecole_name_formatted = strtolower(str_replace(' ', '_', $ecole_name));

			$company = '© tepunareomaori <kiaora@tepunareomaori.co.nz>';

			$title = sprintf( __( 'Students Credentials : %s ', 'tprm-theme' ), $ecole_name );

			$description = sprintf( __( 'This document is the property of tepunareomaori © %s and contains confidential student credentials from %s. Unauthorized reproduction, distribution, or transmission in any form is strictly prohibited. For permissions, please reach out to kiaora@tepunareomaori.co.nz.', 'tprm-theme' ), $year, $ecole_name );

			$imprinted_by = sprintf( __( 'This document was printed by : ', 'tprm-theme' ) );

			$copyright = sprintf( __( 'Copyright : %s ', 'tprm-theme' ), $company );

			$filename = 'students_credentials_' . $ecole_name_formatted ;

			$export_data = array(
                'students_header_data' => array(
                    'std_name' => __("Name", "tprm-theme"),
                    'std_email' => __("Email", "tprm-theme"),
                    'std_username' => __("Username", "tprm-theme"),
                    'std_password' => __("Password", "tprm-theme"),
                    'std_classrom' => __("Classroom", "tprm-theme"),
                    'account_status_name' => __("Account Status", "tprm-theme"),
                ),
                'students_data' => $students_data, // This should be an array of rows
                'ecole_name' => $ecole_name,
                'ecole_name_formatted' => $ecole_name_formatted,
                'company' => $company,
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'imprinted_by' => $imprinted_by,
                'img' => TPRM_icon,
            );

            // JSON encode the data
            $export_data_json = json_encode($export_data);

			echo $export_data_json;

		}
		
		exit();
	}
}

