<?php 

add_action('course_final_quiz', 'TPRM_course_final_quiz');
add_action('wp_ajax_enable_final_quiz', 'TPRM_ajax_enable_final_quiz');
add_action('wp_ajax_print_final_quiz', 'TPRM_ajax_print_final_quiz');
add_action('wp_ajax_delete_pdf_file', 'delete_quiz_pdf_file');
add_action('wp_ajax_nopriv_delete_pdf_file', 'delete_quiz_pdf_file');
add_action('add_meta_boxes', 'add_final_quiz_meta_box');
add_action('save_post', 'save_final_quiz_meta_box');
add_filter('display_post_states', 'add_final_quiz_post_state', 10, 2);
add_action('restrict_manage_posts', 'add_final_quiz_filter_dropdown');
add_action('parse_query', 'filter_final_quizzes');
add_filter('buddyboss_learndash_content', 'final_quiz_template_content', 99999, 2);
add_action('learndash-quiz-row-title-after', 'final_quiz_row_course' );

/* 
* *** Final Quiz Callback functions ***
*/

/**
 * Display the final quiz button in group courses page
 *
 * @since V2
 * @param int Course ID .
 */

 function TPRM_course_final_quiz($course_id){

	global $wpdb, $TPRM_ajax_nonce ;

	$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );

	$final_quiz = get_final_quiz($course_id);

	$quiz_status = '';
	$quiz_button = '';
	$data_ballon = '';

	if( is_TPRM_admin() || is_teacher() ) : 	
		
		if (bp_is_group()):

			$group_id = bp_get_current_group_id();
			$group_name = bp_get_current_group_name();
			$ld_group_id = bp_ld_sync( 'buddypress' )->helpers->getLearndashGroupId( bp_get_current_group_id() );
			$teacher_id = get_current_user_id();
			$course_name = get_the_title($course_id);
			$associated_group_id = get_post_meta($final_quiz, 'associated_group_id', true);
		
			if($final_quiz) :

				// Define your translated strings
				$quiz_status_enabled = __('The final quiz for this course is <strong>enabled</strong> for your students in this classroom.', 'tprm-theme');
				$quiz_status_disabled = __('The final quiz for this course is <strong>disabled</strong> for your students in this classroom.', 'tprm-theme');
				$quiz_button_enable = __('Enable Final Quiz', 'tprm-theme');
				$quiz_button_disable = __('Disable Final Quiz', 'tprm-theme');
				$data_ballon_enable = __('Click here to Enable Final Quiz for your students', 'tprm-theme');
				$data_ballon_disable = __('Click here to Disable Final Quiz for your students', 'tprm-theme');

				$enabled_quizzes = has_teacher_enabled_quiz($teacher_id);

				if ( final_quiz_is_enabled_for_group($final_quiz) && in_array($final_quiz, $enabled_quizzes) && $ld_group_id == $associated_group_id ) {
					//Enabled
					$quiz_status = $quiz_status_enabled;	
					$quiz_button = $quiz_button_disable;
					$data_ballon = $data_ballon_disable;
				} else {
					// Disabled
					$quiz_status = $quiz_status_disabled;
					$quiz_button = $quiz_button_enable;
					$data_ballon = $data_ballon_enable;
				}
				
				?>
				<div id="enable-final-quiz-container">
					<div id="final-quiz-status"><?php echo $quiz_status ?></div>
					<div class="final-quiz-btn-container">
						<a href="#tepunareomaori-quiz-content-<?php echo esc_attr($course_id); ?>" id="see-final-quiz" target="_blank"><?php _e('Final Quiz preview', 'tprm-theme'); ?></a>
						<button 
							data-balloon-pos="down" 
							data-balloon="<?php esc_attr_e($data_ballon); ?>" 
							id="enable-final-quiz-btn"
							data-course_name="<?php esc_attr_e($course_name) ?>"
							data-teacher_id="<?php esc_attr_e($teacher_id) ?>"
							data-group_id="<?php esc_attr_e($ld_group_id) ?>"
							data-final_quiz="<?php esc_attr_e($final_quiz); ?>"
							data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>">
							<?php esc_html_e($quiz_button); ?>
						</button>
						<br>
					</div>
				</div>
				<div id="tepunareomaori-quiz-content-<?php echo esc_attr($course_id); ?>" class="tepunareomaori-quiz-content mfp-with-anim mfp-hide white-popup">
					<header id="quiz-title"><?php echo esc_html(get_the_title($final_quiz)) ; ?></header>
					<div class="see-final-quiz-head">
						<div class="quiz-permalink"><?php _e('You can access the Quiz page from ', 'tprm-theme')?><a href="<?php esc_attr_e(get_permalink($final_quiz)) ?>" class="see-final-quiz" target="_blank"><?php _e('here', 'tprm-theme'); ?></a></div>
						<button 							
							data-balloon-pos="up"
							data-balloon="<?php esc_attr_e('Print Final Quiz content to a PDF file', 'tprm-theme'); ?>"
							data-course_name="<?php esc_attr_e($course_name) ?>"
							data-final_quiz="<?php esc_attr_e($final_quiz); ?>"
							data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>"
							id="print_pdf">
							<?php _e('PDF')?>
							<i class="bb-icon-l bb-icon-file-pdf"></i>
						</button>
					</div>
 					<div class="popup-scroll">
					<?php  
		
					$final_quiz_questions = learndash_get_quiz_questions($final_quiz);

					foreach ($final_quiz_questions as $final_quiz_question) {
						$question_id = learndash_get_question_post_by_pro_id($final_quiz_question);

						// Get the post object for the question
						$question_post = get_post($question_id);

						// Check if the post object exists
						if ($question_post) {
							// Get the title and content of the question
							$question_title = esc_html($question_post->post_title);
							$question_content = apply_filters('the_content', $question_post->post_content);

							echo '<div class="question-container">';
							// Output or use the title and content as needed
							echo '<h2>' . $question_title . '</h2>';
							echo '<div class="question_title">' . $question_content . '</div>';

							echo '<ul>';
						
							// Fetch question data from the database
							$question_data = $wpdb->get_row(
								$wpdb->prepare(
									"
									SELECT *
									FROM {$wpdb->prefix}learndash_pro_quiz_question
									WHERE id = %d
									",
									$final_quiz_question
								),
								ARRAY_A
							);

							$question_answers = array();

							if (empty($question_data)) {
								// Exit when no data is found
								return;
							}
						
							$answer_data = maybe_unserialize($question_data['answer_data']);
						
							if (!empty($answer_data)) {
								foreach ($answer_data as $answer_object) {
									if (!$answer_object instanceof WpProQuiz_Model_AnswerTypes) {
										continue;
									}					
									$question_answer = $answer_object->getAnswer();

									if( $answer_object->isCorrect() ){ ?>
									<li class="correct_answer">
										<?php echo $question_answer;?>
									</li>
									<?php }
									else{ ?>
										<li>
										<?php echo $question_answer; ?>
										</li>
									<?php 
									}
								}
								
							}
							echo '</ul>';
							echo '</div>';

						}
					}
				
					?>
					
					</div>
				</div>
				<!-- endpopup -->
			<?php 
			endif;
	
		endif;
		
	endif;

}


/**
 * Print the final quiz ajax request
 *
 * @since V2
 */
function TPRM_ajax_print_final_quiz() {

	global $wpdb, $TPRM_ajax_nonce ;

	$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );
   
    if (isset($_POST['payload']) && $_POST['payload'] == 'print_final_quiz' && $_POST['final_quiz'] && $_POST['course_name']) {

        check_ajax_referer('TPRM_nonce', 'security');

        require_once TPRM_DEP . 'KWFpdf.php';

		ob_start(); // Start output buffering

		$course_name = get_the_title($course_id);

        $final_quiz = sanitize_text_field($_POST['final_quiz']);
		$course_name = sanitize_text_field($_POST['course_name']);

        $final_quiz_questions = learndash_get_quiz_questions($final_quiz);

		echo '<header id="quiz-title" 
		style="color: #fff;
		font-weight: bolder;
		background-color: #01a49c;
		text-align: center;
		line-height: 24px;
		margin: -30px -30px 30px;
		padding: 17px 30px;
		"> '. esc_html(get_the_title($final_quiz)) . '</header>';

        foreach ($final_quiz_questions as $final_quiz_question) {

            $question_id = learndash_get_question_post_by_pro_id($final_quiz_question);

            $question_post = get_post($question_id);

            if ($question_post) {
                $question_title = esc_html($question_post->post_title);
                $question_content = apply_filters('the_content', $question_post->post_content);

                echo '<div class="question-container">';
                echo '<h2 style="color: #01a49c;">' . $question_title . '</h2>';
                echo '<div style="color: #1c5cb2; font-weight: bolder;" class="question_title">' . $question_content . '</div>';

                echo '<ul>';

                $question_data = $wpdb->get_row(
                    $wpdb->prepare(
                        "
                        SELECT *
                        FROM {$wpdb->prefix}learndash_pro_quiz_question
                        WHERE id = %d
                        ",
                        $final_quiz_question
                    ),
                    ARRAY_A
                );

                $question_answers = array();

                if (empty($question_data)) {
                    return;
                }

                $answer_data = maybe_unserialize($question_data['answer_data']);

                if (!empty($answer_data)) {
                    foreach ($answer_data as $answer_object) {
                        if (!$answer_object instanceof WpProQuiz_Model_AnswerTypes) {
                            continue;
                        }

                        $question_answer = $answer_object->getAnswer();

                        if ($answer_object->isCorrect()) { ?>
                            <li style="color: #55c21b; font-weight: bold;" class="correct_answer">
                                <?php echo $question_answer; ?>
                            </li>
                        <?php } else { ?>
                            <li>
                                <?php echo $question_answer; ?>
                            </li>
                        <?php
                        }
                    }
                }
                echo '</ul>';
                echo '</div>';
            }
        }

        $quiz_content = ob_get_clean(); // Get the buffered content and clean the buffer

		$quiz_name = esc_html(get_the_title($final_quiz));

		$quiz_printed_message = sprintf(__('The final quiz %s for the <ins>%s</ins> course has been successfully printed.', 'tprm-theme'), $quiz_name, $course_name);

		

		$pdf_style = array(
			'margin_top' => 40,
		);

		/* $arrContextOptions = array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);
	
		$quiz_style =  file_get_contents( TPRM_CSS_PATH . 'final_quiz.css' ,false, stream_context_create($arrContextOptions)); */

		$mpdf = new KWFpdf($pdf_style);

		$mpdf->SetHTMLHeader('<img width="150" height="77" src="' . TPRM_icon . '"/>');
		$mpdf->WriteHTML($quiz_content);
		//$mpdf->OutputHttpDownload($quiz_name . '.pdf');

		// Get the WordPress upload directory path
		$upload_dir = wp_upload_dir();

		$filename  = strtolower(str_replace(' ', '-', $quiz_name)) . '.pdf';
	
		$pdf_file_path = $upload_dir['basedir'] . '/' . $filename;

		$download_link = esc_url($upload_dir['baseurl'] . '/' . $filename);

		// Save the PDF file to the upload directory
		$mpdf->OutputFile($pdf_file_path);

		// Return both the download link and the file path in the response

		echo json_encode(array(
			'download_link' => $download_link,
			'pdf_file_path' => $pdf_file_path,
            'quiz_printed_message' => $quiz_printed_message
        ));
	

        wp_die();
    }
}

/**
 *  Delete The Printed PDF file after being downloaded
 *
 * @since V2
 */

 function delete_quiz_pdf_file() {
    // Get the path of the file to be deleted from the AJAX request
    $pdf_file_path = sanitize_text_field($_POST['pdf_file_path']);

    // Log the file path for debugging
    error_log('Attempting to delete file php: ' . $pdf_file_path);

    // Check if the file exists and delete it
    if (file_exists($pdf_file_path)) {
        unlink($pdf_file_path);
        //error_log('File deleted from the server.');
        //echo 'File deleted from the server.';
    } else {
        //error_log('File not found on the server.');
        //echo 'File not found on the server.';
    }

    // Always exit to prevent further output
    exit();
}



 /**
 * Get enabled quizzes by a teacher.
 *
 * @since V2
 * @param int Teacher ID to get enabled quizzes for.
 * @return array of enabled quizzes ids.
 */

function has_teacher_enabled_quiz($teacher_id) {
    // Get the array of enabled quiz IDs from the teacher's user meta
    $enabled_quizzes = get_user_meta($teacher_id, 'enabled_quizzes', true);
    
    // Ensure it's an array
    if (!is_array($enabled_quizzes)) {
        $enabled_quizzes = array();
    }
    
    return $enabled_quizzes;
}

 /**
 * Get the final quiz for a given course
 * (only one final quiz by course is allowed )
 *
 * @since V2
 * @param int Course ID to get the final quiz for .
 * @return int Final quiz ID.
 */

function get_final_quiz($course_id){

	$final_quiz_id = '';

	$quizzes = learndash_course_get_steps_by_type($course_id, 'sfwd-quiz');

	if (!empty($quizzes)) {
		foreach ($quizzes as $quiz) {
			$quiz = get_post($quiz);
			$quiz_id = $quiz->ID;
			$is_final_quiz = get_post_meta($quiz_id, 'final_quiz', true);

			if($is_final_quiz){
				$final_quiz_id = $quiz_id;
			}
		}
	}

	return $final_quiz_id;
}

/**
 * Handle the final quiz ajax request
 *
 * @since V2
 */

function TPRM_ajax_enable_final_quiz() {

    if (isset($_POST['payload']) && $_POST['payload'] == 'enable_final_quiz' && $_POST['final_quiz'] && $_POST['ld_group_id'] && $_POST['teacher_id'] && $_POST['course_name']) {
        check_ajax_referer('TPRM_nonce', 'security');

        $final_quiz_id = sanitize_text_field($_POST['final_quiz']);
        $group_id = sanitize_text_field($_POST['ld_group_id']);
        $teacher_id = sanitize_text_field($_POST['teacher_id']);
        $quiz_status = '';
        $group_name = bp_get_current_group_name();
        $ld_group_id = bp_ld_sync('buddypress')->helpers->getLearndashGroupId(bp_get_current_group_id());
        $teacher_id = get_current_user_id();
		$course_name = sanitize_text_field($_POST['course_name']);

		$quiz_status_enabled = sprintf(__('The final quiz for the <ins>%s</ins> course is <strong>enabled</strong> to your students in the classroom %s.', 'tprm-theme'), $course_name, $group_name);
		$quiz_status_disabled = sprintf(__('The final quiz for the <ins>%s</ins> course is <strong>disabled</strong> to your students in the classroom %s.', 'tprm-theme'), $course_name, $group_name);
        $quiz_button_enable = __('Enable Final Quiz', 'tprm-theme');
        $quiz_button_disable = __('Disable Final Quiz', 'tprm-theme');
		$data_ballon_enable = __('Click here to Enable Final Quiz for your students', 'tprm-theme');
		$data_ballon_disable = __('Click here to Disable Final Quiz for your students', 'tprm-theme');
        $teacher_id_from_request = sanitize_text_field($_POST['teacher_id']);

        if (get_current_user_id() == $teacher_id_from_request) {
            $enabled_quizzes = has_teacher_enabled_quiz($teacher_id);

            $quiz_index = array_search($final_quiz_id, $enabled_quizzes);

            if ($quiz_index !== false) {
                update_post_meta($final_quiz_id, 'quiz_enabled', 0);
                delete_post_meta($final_quiz_id, 'associated_group_id');
                delete_post_meta($final_quiz_id, 'enabling_user'); // Remove enabling user info
                $quiz_status = 'quiz_disabled';
                unset($enabled_quizzes[$quiz_index]);
            } else {
                update_post_meta($final_quiz_id, 'quiz_enabled', 1);
                update_post_meta($final_quiz_id, 'associated_group_id', $group_id);
                update_post_meta($final_quiz_id, 'enabling_user', get_current_user_id()); // Store the enabling user
                $quiz_status = 'quiz_enabled';
                $enabled_quizzes[] = $final_quiz_id;
            }

            update_user_meta($teacher_id, 'enabled_quizzes', $enabled_quizzes);
        }

        echo json_encode(array(
            'quiz_status' => $quiz_status,
            'teacher_id' => $teacher_id,
            'quiz_status_enabled' => $quiz_status_enabled,
            'quiz_status_disabled' => $quiz_status_disabled,
            'quiz_button_enable' => $quiz_button_enable,
            'quiz_button_disable' => $quiz_button_disable,
            'data_ballon_enable' => $data_ballon_enable,
            'data_ballon_disable' => $data_ballon_disable,
        ));

        wp_die();
    }
}

  /**
 * Check if the final quiz is enabled for a group
 * if so then return the group id , otherwise return false 
 * 
 *
 * @since V2
 * @param int the Quiz ID to check.
 * @return int|bool group id or false.
 */

function final_quiz_is_enabled_for_group($quiz_id) {
    $quiz_enabled = get_post_meta($quiz_id, 'quiz_enabled', true);
    $associated_group_id = get_post_meta($quiz_id, 'associated_group_id', true);
    $user_id = get_current_user_id();

    if ($quiz_enabled && !empty($associated_group_id)) {
        // Check if the user is a teacher or admin
        if (is_teacher() || is_TPRM_admin()) {
            return $associated_group_id; // Return the associated group ID
        }

        // Check if the user is a member of the associated group
        if (learndash_is_user_in_group($user_id, $associated_group_id)) {
            return $associated_group_id; // Return the associated group ID
        }
    }

    return false; // Quiz is not enabled for any specific group
}

/* *** Final quiz admin area *** */

/**
 * Add the final quiz meta box
 *
 * @since V2
 */

function add_final_quiz_meta_box() {
    add_meta_box(
        'final_quiz_meta_box', // id
        'Final Quiz', // title
        'show_final_quiz_meta_box', // callback
        'sfwd-quiz', // screen
        'side', // context
        'high' // priority
    );
}

/**
 * Show the meta box
 *
 * @since V2
 */

function show_final_quiz_meta_box($post) {

    $final_quiz = get_post_meta($post->ID, 'final_quiz', true); ?>

	<input type="checkbox" name="final_quiz" <?php echo checked($final_quiz, 'on', false) ?> >

	<?php
	
	_e('Make as Final Quiz', 'tprm-theme');

}

/**
 * Save the meta box
 *
 * @since V2
 */

function save_final_quiz_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['final_quiz'])) {
        update_post_meta($post_id, 'final_quiz', $_POST['final_quiz']);
    } else {
        delete_post_meta($post_id, 'final_quiz');
    }
}


/**
 * Add a post state for final quizzes 
 *
 * @since V2
 */

function add_final_quiz_post_state($post_states, $post) {
    $final_quiz = get_post_meta($post->ID, 'final_quiz', true);
    if ($final_quiz == 'on') {
        $post_states['final_quiz'] = __('Final Quiz', 'tprm-theme');
    }
    return $post_states;
}

/**
 * Add a filter dropdown for final quizzes
 *
 * @since V2
 */

function add_final_quiz_filter_dropdown() {
    global $typenow;
    if ($typenow == 'sfwd-quiz') {
        ?>
        <select name="final_quiz" id="final_quiz">
            <option value=""><?php _e('All Quizzes', 'tprm-theme'); ?></option>
            <option value="on" <?php echo isset($_GET['final_quiz']) && $_GET['final_quiz'] == 'on' ? 'selected' : ''; ?>><?php _e('Final Quizzes', 'tprm-theme'); ?></option>
        </select>
        <?php
    }
}

/**
 * Filter quizzes based on the selection
 *
 * @since V2
 */

function filter_final_quizzes($query) {
    global $pagenow;
    $type = 'sfwd-quiz';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ('sfwd-quiz' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['final_quiz']) && $_GET['final_quiz'] != '') {
        $query->query_vars['meta_key'] = 'final_quiz';
        $query->query_vars['meta_value'] = $_GET['final_quiz'];
    }
}

/**
 * Display Quiz not accessible content on quiz single page
 *
 * @since V2
 */

function final_quiz_template_content($content, $post) {

	if ( empty( $post->post_type ) ) {
		return $content;
	}

	$course_id = learndash_get_course_id( $post );
	if ( empty( $course_id ) ) {
		return $content;
	}

	$user_id = get_current_user_id();
	$user_groups = learndash_get_users_group_ids( $user_id , true );

    // Check if we're inside the main loop in a single post page.
    if ($post->post_type == 'sfwd-quiz') {
        $quiz_id = get_the_ID();

        $quiz_enabled = get_post_meta($quiz_id, 'quiz_enabled', true);
        $is_final_quiz = get_post_meta($quiz_id, 'final_quiz', true);

		foreach ($user_groups as $group_id) :

			$quiz_group = final_quiz_is_enabled_for_group($quiz_id);

			$quiz_enabled = learndash_is_user_in_group($user_id, $quiz_group);

			if (!$quiz_enabled && $is_final_quiz && is_student()) {

				if ( ! defined( 'ABSPATH' ) ) {
					exit; // Exit if accessed directly.
				}

				// First generate the message.
				$message = sprintf( '<span class="ld-display-label">%s</span>', __( 'Final Quiz not yet available', 'tprm-theme' ) );

				$button = false;

				if ( ( ! isset( $course_id ) ) || ( empty( $course_id ) ) ) {
					$course_id = learndash_get_course_id( $quiz_id );
				}
				if ( ! empty( $course_id ) ) {
					$button = array(
						'url'           => get_permalink( $course_id ),
						'label'         => learndash_get_label_course_step_back( learndash_get_post_type_slug( 'course' ) ),
						'icon'          => 'arrow-left',
						'icon-location' => 'left',
					);
				}

				ob_start();  // Start output buffering
				?>

				<div class="learndash-wrapper">
					<?php
					learndash_get_template_part(
						'modules/alert.php',
						array(
							'type'    => 'info',
							'icon'    => 'calendar',
							'button'  => $button,
							'message' => $message,
						),
						true
					);
					?>
				</div>
				<?php

				$content = ob_get_clean();  // Store buffer in variable and clean it
			}
		endforeach;
	}

    return $content;
}

/**
 * Display Quiz not accessible on course page
 *
 * @since V2
 */

function final_quiz_row_course($quiz_id){
	$quiz_enabled = get_post_meta($quiz_id, 'quiz_enabled', true);
	$is_final_quiz = get_post_meta($quiz_id, 'final_quiz', true);

	$user_id = get_current_user_id();
	$user_groups = learndash_get_users_group_ids( $user_id , true );

	foreach ($user_groups as $group_id) :

		$quiz_group = final_quiz_is_enabled_for_group($quiz_id);

		$quiz_enabled = learndash_is_user_in_group($user_id, $quiz_group) ;

		if ( !$quiz_enabled && $is_final_quiz && is_student()) :
			?>
			<span class="lms-quiz-status-icon" data-balloon-pos="left" data-balloon="<?php _e( "The final quiz is not yet available", 'tprm-theme' ); ?>"><i class="bb-icon-f bb-icon-lock"></i></span>
			<?php
		endif;

	endforeach;
}


