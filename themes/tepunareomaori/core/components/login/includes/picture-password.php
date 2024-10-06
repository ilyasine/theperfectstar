<?php 


add_action('wp_ajax_fetch_images', 'fetch_images_ajax');
add_action('wp_ajax_nopriv_fetch_images', 'fetch_images_ajax');
add_action('wp_ajax_save_update_image', 'save_update_image');
add_action('wp_ajax_nopriv_save_update_image', 'save_update_image'); 
add_action('wp_ajax_check_classroom_code', 'check_classroom_code');
add_action('wp_ajax_nopriv_check_classroom_code', 'check_classroom_code');

function validate_picture_password() {
    global $blog_id, $wpdb;

    check_ajax_referer('picture_password_login_nonce', 'security');

    $username = sanitize_text_field($_POST['username']);
    $selectedImageUrl = esc_url($_POST['selectedPicture']);
    $selectedImageName = basename($selectedImageUrl); // Extract the image file name

    $user = get_user_by('login', $username);

    if (!$user) {
        wp_send_json_error(array(
            'message' => __('Invalid username.', 'tprm-theme')
        ));
    }

    // Check if the user has the student role
    if (!in_array('student', (array) $user->roles) && !in_array('kwf-student', (array) $user->roles)) {
        wp_send_json_error(array(
            'message' => __('Login with picture is available only for students. Please use the regular login method.', 'tprm-theme')
        ));
    }

    $userId = $user->ID;
    $imagePassword = get_user_meta($userId, 'picture_password_image', true); // Retrieve the stored image file name
    $wrongAttempts = get_user_meta($userId, 'wrong_password_attempts', true);
    $maxAttempts = 300; // Maximum allowed attempts

    if ($wrongAttempts >= $maxAttempts) {
        wp_send_json_error(array(
            'message' => __('Maximum attempts reached. Please contact your teacher for the correct Picture Password.', 'tprm-theme')
        ));
    }

    if ($imagePassword) {
        if ($imagePassword === $selectedImageName) {
            // Correct image :  

            // Destroy other sessions.
            $manager = WP_Session_Tokens::get_instance($userId);
            $manager->destroy_all();

            //log in the user
            wp_set_auth_cookie($userId);
            do_action('wp_login', $user->user_login, $user);
            update_user_meta($userId, 'wrong_password_attempts', 0); // Reset wrong attempts

            // Get the redirect URL
            $redirect_url = redirect_after_login('', '', $user);

            // Return the preloader template and redirect URL
            ob_start();
            include TPRM_THEME_PATH . 'template-parts/preloader.php';
            $template_content = ob_get_clean();

            wp_send_json_success(array(
                'message' => __('Selected image for picture password is matched.', 'tprm-theme'),
                'template' => $template_content,
                'redirect_url' => $redirect_url
            ));
        } else {
            // Increment the wrong attempts counter
            $wrongAttempts++;
            update_user_meta($userId, 'wrong_password_attempts', $wrongAttempts);
            $remainingAttempts = $maxAttempts - $wrongAttempts;
            if ($wrongAttempts >= $maxAttempts) {
                wp_send_json_error(array(
                    'message' => __('Maximum attempts reached. Please contact your teacher for the correct Picture Password.', 'tprm-theme'),
                ));
            } else {
                wp_send_json_error(array(
                    'message' => sprintf(__('The selected picture is incorrect. Attempts remaining: <strong>%s</strong> ', 'tprm-theme'), $remainingAttempts),
                ));             
            }
        }
    } else {
        wp_send_json_error(array(
            'message' => __('No picture password set for this user.', 'tprm-theme')
        ));
    }
}


add_action('wp_ajax_validate_picture_password', 'validate_picture_password');
add_action('wp_ajax_nopriv_validate_picture_password', 'validate_picture_password');

function fetch_all_images() {
    global $wpdb, $TPRM_ajax_nonce;
    $upload_dir = wp_upload_dir();
    $image_dir = $upload_dir['basedir'] . '/picture-passwords/';

    if (!empty($image_dir)) {
        $image_files = glob($image_dir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
        $image_urls = array_map(function ($file) use ($upload_dir) {
            return $upload_dir['baseurl'] . '/picture-passwords/' . basename($file);
        }, $image_files);

        $imageContent = '';
        foreach ($image_urls as $imageUrl) {
            $escapedImageUrl = esc_url($imageUrl);
            $img = '<div class="picture-container" style="position: relative;">
                        <img src="' . $escapedImageUrl . '" alt="Image" class="gallery-image" data-url="' . $escapedImageUrl . '" style="width: 100%; height: 100%; max-height: 80px; object-fit: cover; cursor: pointer; filter: brightness(0.9); transition: .3s ease-in-out;">
                        <span class="bb-icon-thumbtack-star" style="display: none; position: absolute; padding: 2px; border-radius: 50%; top: 5px; right: 5px; background: #fff;"></span>
                    </div>';
            $imageContent .= $img;
        }
        $imageContent .= '</div>';

        $response = array(
            'html' => $imageContent
        );
    } else {
        $response = array(
            'error' => 'Image directory does not exist.'
        );
    }

    wp_send_json($response);
}
add_action('wp_ajax_fetch_all_images', 'fetch_all_images');
add_action('wp_ajax_nopriv_fetch_all_images', 'fetch_all_images');


function group_access_shortcode($atts, $content = null) {
    // If the user is logged in, redirect them to the homepage
    if (is_user_logged_in()) {
        wp_redirect(home_url());
        exit;
    }

    // Display the modal and progress elements
    ?>
    <div id="imageModal" class="modal">
        <div class="modal-content"></div>	  
    </div>
    <div id="nprogress"><div class="spinner" role="spinner" style="display: none;"><div class="spinner-icon"></div><div class="spinner-inverse"></div></div></div>
    <?php

    // Initialize the $classCode variable
    $classCode = isset($_GET['classroom_code']) ? sanitize_text_field($_GET['classroom_code']) : '';

    // Prepare the member list
    $memberList = '';

    // Check if the classroom_code is present in the URL
    if (!empty($classCode)) {
        global $wpdb, $TPRM_ajax_nonce;

        // Sanitize the classroom_code
        $classroom_code = sanitize_text_field($classCode);

        // Query to get the group IDs associated with the classroom_code
        $group_ids_query = $wpdb->prepare("
            SELECT group_id 
            FROM {$wpdb->prefix}bp_groups_groupmeta 
            WHERE meta_key = 'classroom_code' 
            AND meta_value = %s
        ", $classroom_code);

        $group_ids = $wpdb->get_col($group_ids_query);

        // If group IDs are found, build the member list
        if (!empty($group_ids)) {
            $memberList .= "<div class='header_list' style='display: flex; justify-content: space-between;'><h2>" . __('Classroom Students List', 'kwh-theme') . "</h2>";
            $memberList .= "<a href=" . esc_url(wp_login_url()) . "> " . __('Back to the access page', 'tprm-theme') . "</a></div>";
            $memberList .= "<div class='group-members' style='display: flex; flex-wrap: wrap; gap: 20px;'>";
            $TPRM_ajax_nonce = wp_create_nonce("TPRM_nonce");

            // Loop through each group ID to get group members
            foreach ($group_ids as $group_id) {
                $group_members = groups_get_group_members(array('group_id' => $group_id));

                foreach ($group_members['members'] as $member) {
                    $user_id = $member->ID;
                    $user_info = get_userdata($user_id);
                    $user_first_name = $user_info->first_name;
                    $user_last_name = $user_info->last_name;
                    $user_username = $user_info->user_login;
                    $user_avatar = get_avatar($user_id, 80);

                    $memberList .= "<div class='group-member' style='display: flex; align-items: center; background-color: #f9f9f9; border-radius: 10px; padding: 10px; width: 98vw; width: 100%; cursor: pointer;' data-security='$TPRM_ajax_nonce' data-user-id='$user_id'>";
                    $memberList .= "<div class='avatar' style='border-radius: 50%; overflow: hidden; margin-right: 15px;'>$user_avatar</div>";
                    $memberList .= "<div class='info' style='flex-grow: initial; display: flex; width: 100%; justify-content: space-between; padding-right: 70px;'>";
                    $memberList .= "<div class='usernicename'>$user_first_name $user_last_name</div>";
                    $memberList .= "<div class='username' style='font-weight: bold;'>$user_username</div>";
                    $memberList .= "</div>";
                    $memberList .= "</div>";
                }
            }

            $memberList .= "</div>";
        } else {
            // No groups found for the provided classroom code
            $memberList .= "<p>" . __('No groups found for the provided classroom code.', 'tprm-theme') . "</p>";
        }
    } else {
        // Handle the case when classroom_code is not provided
        $memberList .= "<p>" . __('Please provide a valid classroom code.', 'tprm-theme') . "</p>";
    }

    return $memberList;
}
add_shortcode('group_access', 'group_access_shortcode');


function fetch_images_ajax() {
    global $wpdb, $TPRM_ajax_nonce;

    $TPRM_ajax_nonce = wp_create_nonce("TPRM_nonce");

    if (function_exists('wc_memberships_is_user_active_member') && !empty($_POST['userId'])) {
        // Verify nonce
        check_ajax_referer('TPRM_nonce', 'security');
        $userId = intval($_POST['userId']);

        // Check if the user has active memberships
        $has_active_membership = wc_memberships_is_user_active_member($userId, 'access-' . get_option('school_year'));

        if ($has_active_membership) {
            $upload_dir = wp_upload_dir();
            $image_dir = $upload_dir['basedir'] . '/picture-passwords/';
            $membership = 'User with ID ' . $userId . ' has active memberships.';
            if (file_exists($image_dir) && is_dir($image_dir)) {
                $image_files = glob($image_dir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                $image_urls = array_map(function ($file) use ($upload_dir) {
                    return $upload_dir['baseurl'] . '/picture-passwords/' . basename($file);
                }, $image_files);
                $passwordType = get_user_meta($userId, 'password_type', true);
                $storedImage = get_user_meta($userId, 'picture_password_image', true);
                $textPassword = get_user_meta($userId, 'text_password', true);
                $modalContent = "";
                $modalTitle = "";
                $imageSelection = "";
                if ($storedImage || $textPassword) {
                    $present = true;
                    $modalTitle .= "Login";
                } else {
                    $present = false;
                    $modalTitle .= "Register";
                }

                $modalContent .= '<div class="modal-title">
                                    <h1>' . $modalTitle . '</h1>
                                    <span class="close">&times;</span>
                                  </div>';
                if (!($storedImage || $textPassword)) {
                    $modalContent .= '<input type="password" name="confirm_pwd" id="confirm_pass" class="input password-input" value="" size="20" autocomplete="current-password" spellcheck="false" required="required" placeholder="Confirm Password"><br><br>';
                    $modalContent .= '<label>
                                        <input type="radio" name="passwordType" value="text" checked> Text Password
                                      </label>
                                      <label>
                                        <input type="radio" name="passwordType" value="picture"> Picture Password
                                      </label><br><br>';
                }

                if (!($storedImage || $textPassword)) {
                    $modalContent .= '<input type="password" name="tpwd" id="text_pass" class="input password-input" value="" size="20" autocomplete="current-password" spellcheck="false" required="required" placeholder="Password">';
                    $modalContent .= '<p style="margin:10px;">Or</p>';
                    $modalContent .= '<div id="img_pass" class="image-grid">';
                    foreach ($image_urls as $index => $imageUrl) {
                        $escapedImageUrl = esc_url($imageUrl);
                        $img = '<img src="' . $escapedImageUrl . '">';
                        $modalContent .= $img;
                    }
                } else {
                    if ($passwordType == "picture") {
                        $modalContent .= '<p style="margin-bottom: 0px;">Picture Password</p>';
                        $modalContent .= '<div id="img_pass" class="image-grid">';
                        foreach ($image_urls as $index => $imageUrl) {
                            $escapedImageUrl = esc_url($imageUrl);
                            $img = '<img src="' . $escapedImageUrl . '">';
                            $modalContent .= $img;
                        }
                        $imageSelection = "imageSelection();";
                    } else if ($passwordType == "text") {
                        $modalContent .= '<p style="margin-bottom: 0px;">Text Password</p>';
                        $modalContent .= '<input type="password" name="tpwd" id="text_pass" class="input password-input" value="" size="20" autocomplete="current-password" spellcheck="false" required="required" placeholder="Password">';
                    }
                }
                $modalContent .= '</div>
                                  <div class="modal-message"></div>
                                  <button id="saveButton" data-security="' . $TPRM_ajax_nonce . '"
                                          data-user_id=' . $userId . '>' . $modalTitle . '</button>';
                $javascript_code = "
                    var Content = " . json_encode($modalContent) . ";
                    var modalContent = jQuery('#imageModal .modal-content');
                    modalContent.empty();
                    modalContent.append(Content);
                    jQuery('#imageModal').css('display', 'flex');
                    " . $imageSelection . "
                ";
                $images = array(
                    'javascript' => $javascript_code
                );
            } else {
                $images = array(
                    'error' => 'Image directory does not exist.'
                );
            }
        } else {
            $membership = 'User with ID ' . $userId . ' does not have active memberships.';
            $userDetail = get_user_by('ID', $userId);
            wp_set_auth_cookie($userId);
            do_action('wp_login', $userDetail->user_login, $userDetail);
            $javascript_code = "
                window.location.href = '/subscription/';
                ";
            $images = array(
                'javascript' => $javascript_code
            );
        }
    } else {
        $images = array(
            'error' => 'WooCommerce Memberships function is not available or user ID is missing.'
        );
    }

    wp_send_json($images);
}


function save_update_image(){
	global $wpdb, $TPRM_ajax_nonce ;
	$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );
    if(isset($_POST['userId'])){
		// verify nonce
		check_ajax_referer( 'TPRM_nonce' ,'security');
		
        $userId = intval($_POST['userId']);
        $selectedType = sanitize_text_field($_POST['selectedType']);
		$confirmPassValue = sanitize_text_field($_POST['confirmPass']);
		$user = get_user_by('ID', $userId);
		if(!$user){
			$response = array(
				'success' => false,
				'message' => __('User is Not Valid.', 'tprm-theme')
			);
			wp_send_json($response);
		}
		$wrong_attempts = get_user_meta($userId, 'wrong_password_attempts', true);
		if($wrong_attempts >= 3){
			$response = array(
				'success' => false,
				'message' => __('Maximum attempts reached.', 'tprm-theme')
			);
			update_user_meta($userId, 'is_active', 'inactive');
			wp_send_json($response);
		}
		if(empty($selectedType) && empty($confirmPassValue)){
			$selectedType = get_user_meta($userId, 'password_type', true);
		}
		if($selectedType == "picture"){
			$selectedImageUrl = esc_url($_POST['selectedImages'][0]);
			$selectedImageName = basename($selectedImageUrl);
			$imagePassword = get_user_meta($userId, 'picture_password_image', true);
			if($imagePassword){
				if($imagePassword == $selectedImageName){
					wp_set_auth_cookie($userId);
					do_action('wp_login', $user->user_login, $user);
					update_user_meta($userId, 'wrong_password_attempts',0);
					$response = array(
						'success' => true,
						'message' => __('Selected image for picture password is matched.', 'tprm-theme')
					);
					
				}else{
					$wrong_attempts++;
					update_user_meta($userId, 'wrong_password_attempts', $wrong_attempts);
					$remaining_attempts = max(0, 3 - $wrong_attempts);
					$response = array(
						'success' => false,
						'message' => __('Selected image for picture password is not matched. Remaining attempts: ', 'tprm-theme') . $remaining_attempts
					);
				}
			}else if(!empty($selectedImageName)){
				$is_valid_password = wp_check_password($confirmPassValue, $user->user_pass, $userId);
				if (!$is_valid_password) {
					$response = array(
						'success' => false,
						'message' => __('Confirm Password is not Valid.', 'tprm-theme')
					);
					wp_send_json($response);
				}
				update_user_meta($userId, 'password_type', 'picture');
				update_user_meta($userId, 'picture_password_image', $selectedImageName);
				wp_set_auth_cookie($userId);
				do_action('wp_login', $user->user_login, $user);
				$response = array(
					'success' => true,
					'message' => __('Selected image for picture password stored successfully.', 'tprm-theme')
				);
			}
		}else if($selectedType == "text"){
			$password = sanitize_text_field($_POST['textPass']);
			if($password == "EMPTY_VALUE"){
				$response = array(
					'success' => false,
					'message' => __('Password Is empty', 'tprm-theme')
				);
				wp_send_json($response);
			}
			$textPassword = get_user_meta($userId, 'text_password', true);
			if($textPassword){
				if($textPassword == $password){
					wp_set_auth_cookie($userId);
					do_action('wp_login', $user->user_login, $user);
					update_user_meta($userId, 'wrong_password_attempts',0);
					$response = array(
						'success' => true,
						'message' => __('Text password is matched.', 'tprm-theme')
					);
				}else{
					$wrong_attempts++;
					update_user_meta($userId, 'wrong_password_attempts', $wrong_attempts);
					$remaining_attempts = max(0, 3 - $wrong_attempts);
					$response = array(
						'success' => false,
						'message' => __('Text password is not matched. Remaining attempts: ', 'tprm-theme') .$remaining_attempts
					);
				}
			}else if(!empty($password)){
				global $wpdb;
				$is_valid_password = wp_check_password($confirmPassValue, $user->user_pass, $userId);
				if (!$is_valid_password) {
					$response = array(
						'success' => false,
						'message' => __('Confirm Password is not Valid.', 'tprm-theme')
					);
					wp_send_json($response);
				}
				update_user_meta($userId, 'password_type', 'text');
				update_user_meta($userId, 'text_password', $password);
				wp_set_password($password, $userId);
				if($user){
					$username = $user->user_login;
					$table_name = $wpdb->prefix."students_credentials";
					$update_result = $wpdb->update(
						$table_name,                     
						array('stdcred' => $password),  
						array('username' => $username), 
						array('%s'),                   
						array('%s')
					);					
				}
				wp_set_auth_cookie($userId);
				do_action('wp_login', $user->user_login, $user);
				$response = array(
					'success' => true,
					'message' => __('Text password stored successfully.', 'tprm-theme')
				);
			}
		}
    }else{
        $response = array(
            'success' => false,
            'message' => __('Invalid request data.', 'tprm-theme')
        );
    }
	wp_send_json($response);
}

function check_classroom_code(){
	global $wpdb ;
    ob_start();
    include TPRM_THEME_PATH . 'template-parts/preloader.php';
    $template_content = ob_get_clean();
	$classroom_code = sanitize_text_field($_POST['classCodeValue']);
	if(isset($classroom_code)){
		// verify nonce
		check_ajax_referer( 'classroom_code_nonce' ,'security');
        $group_account_access_url = home_url('/group-account-access/?classroom_code=' . $classroom_code);
		$group_ids_query = $wpdb->prepare("
			SELECT group_id 
			FROM {$wpdb->prefix}bp_groups_groupmeta 
			WHERE meta_key = 'classroom_code' 
			AND meta_value = %s
		", $classroom_code);
		$group_id = $wpdb->get_col($group_ids_query);
		if($group_id){
			$response = array(
					'success' => true,
					'message' => __('classroom found for the provided classroom code !', 'tprm-theme'),
                    'template' => $template_content,
                    'group_account_access_url' => $group_account_access_url,
				);
		}else{
			$response = array(
					'success' => false,
					'message' => __('No classroom found for the provided classroom code !', 'tprm-theme'),
				);
		}
	}
    //wp_send_json_success(array('template' => $template_content));
	wp_send_json($response);
}