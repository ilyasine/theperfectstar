<?php

$ld_group_id =  bp_ld_sync('buddypress')->helpers->getLearndashGroupId(bp_get_current_group_id());

if ($ld_group_id) {
	$post_label_prefix  = 'group';
	$meta              = learndash_get_setting($ld_group_id);
	$post_price_type   = (isset($meta[$post_label_prefix . '_price_type'])) ? $meta[$post_label_prefix . '_price_type'] : '';
	$post_price        = (isset($meta[$post_label_prefix . '_price'])) ? $meta[$post_label_prefix . '_price'] : '';
	// format the Course price to be proper XXX.YY no leading dollar signs or other values.
	if (('paynow' === $post_price_type) || ('subscribe' === $post_price_type)) {
		if ('' !== $post_price) {
			$post_price = preg_replace('/[^0-9.]/', '', $post_price);
			$post_price = number_format(floatval($post_price), 2, '.', '');
		}
	}
	if (! empty($post_price) && ! learndash_is_user_in_group(bp_loggedin_user_id(), $ld_group_id)) {
?>
		<div class="bp-feedback error">
			<span class="bp-icon" aria-hidden="true"></span>
			<p>
				<?php echo esc_html__('You are not allowed to access group courses. Please purchase membership and try again.', 'buddyboss'); ?>
			</p>
		</div>
	<?php
		return;
	}
}

global $courses;
$courses = learndash_get_group_courses_list($ld_group_id);
$count 		 = count($courses);
//$count 		 = count( bp_ld_sync( 'buddypress' )->courses->getGroupCourses() );
//$courses_new = bp_ld_sync( 'buddypress' )->courses->getGroupCourses();

if ($count > 0) {
	?>
	<div id="create-final-quiz-modal" class="mfp-hide modal-content">
		<div class="course-wise-lessons"></div>
		<div class="lessons-wise-questions"></div>
	</div>
	<div class="item-body-inner">
		<div id="bb-learndash_profile">
			<div id="learndash-content" class="learndash-course-list">
				<form id="bb-courses-directory-form" class="bb-courses-directory" method="get" action="">
					<div class="flex align-items-center bb-courses-header">
						<div id="courses-dir-search" class="bs-dir-search" role="search"></div>
					</div>
					<!-- <div class="courses-order-container">
					<button id="toggle-order" type="button">
						<span class="toggle-order-text">
							<?php _e('Unlock order', 'tprm-theme') ?>
						</span>
						<span id="lock-icon" class="bb-icon-l bb-icon-lock-alt"></span>
					</button>
					<div class="order-notice">
						<?php _e('The course order is currently <strong>disabled</strong>. To enable it, please press the <strong>Unlock Order</strong> button to start ordering the courses.', 'tprm-theme') ?>
					</div>
				</div> -->

					<div class="grid-view bb-grid">

						<div id="course-dir-list" class="course-dir-list bs-dir-list">
							<ul id="courses-list" class="bb-course-items list-view bb-list">

								<?php
								foreach ($courses as $post) :
									if ($post == 70228) {

										continue;
									}
									setup_postdata($post);
									bp_locate_template('groups/single/courses-loop.php', true, false);

								endforeach;
								wp_reset_postdata();
								?>
								<li>
									<?php
									global $wpdb;

									// Get course by slug
									$slug = 'final-course-test';
									$post_type = 'sfwd-courses';
									$group_id = $ld_group_id;
									$final_quiz_course = get_page_by_path($slug, OBJECT, $post_type);
									$course_groups = array();
									$quiz_list = "";
									if ($final_quiz_course) {
										$course_id = $final_quiz_course->ID;
										$course_groups = learndash_get_course_groups($course_id);
										$quiz_list = learndash_get_course_quiz_list($course_id);
									}

									if (!empty($quiz_list) && in_array($group_id, $course_groups)) {
										$TPRM_ajax_nonce = wp_create_nonce("TPRM_nonce");
									?>
										<div style="display: flex; flex-wrap: wrap; justify-content: center;">
											<?php
											foreach ($quiz_list as $quiz) {
												$quiz_id = $quiz['post']->ID;
												$meta_value = get_post_meta($quiz_id, 'final_quize_group_' . $group_id, true);

												if ($meta_value) {
													$enable_meta = get_post_meta($quiz_id, 'final_quiz_enable_' . $group_id, true);
													$quizzQuetions = get_post_meta($quiz_id, 'final_quize_group_' . $group_id, true);

													// Check if the array is not empty
													if (!empty($quizzQuetions) && is_array($quizzQuetions)) {
														// Extract the values from the array
														$question_ids = array_values($quizzQuetions);

														// Prepare the placeholders for the SQL query
														$placeholders = implode(',', array_fill(0, count($question_ids), '%d'));

														// Build the SQL query to get the points from the learndash_pro_quiz_question table
														$table_name = $wpdb->prefix . 'learndash_pro_quiz_question'; // Assuming table uses WP prefix
														$query = $wpdb->prepare(
															"SELECT id, points FROM $table_name WHERE id IN ($placeholders)",
															...$question_ids
														);

														// Execute the query
														$results = $wpdb->get_results($query);

														// Calculate the total points
														$total_points = 0;
														if ($results) {
															foreach ($results as $row) {
																$total_points += $row->points;
															}
														}
													} else {
														$total_points = 0; // If no questions are found, set points to 0
													}
											?>
													<div class="quiz-meta-box" style="display: flex; background-color: <?php echo ($enable_meta == 'yes') ? 'green' : '#f5923f'; ?>;">
														<div>
															<h4><?php echo get_the_title($quiz_id); ?></h4>
															<span>Questions: '<?php echo count($quizzQuetions); ?>'</span>

														</div>
														<div style="display:flex;flex-direction: column; margin-left:5px;">
															<button class="update-meta-button" data-quiz-id="<?php echo $quiz_id; ?>"
																data-group-id="<?php echo $group_id; ?>"
																data-meta-value="<?php echo $enable_meta == 'yes' ? 'yes' : 'no'; ?>">
																<?php echo $enable_meta == 'yes' ? 'Disable' : 'Enable'; ?> <i class="fas fa-pencil-alt"></i>
															</button>
															<span>Total Points: <?php echo $total_points; ?></span>
														</div>
													</div>
											<?php
												}
											}
											?>

											<?php
											$current_quizzes = learndash_get_course_quiz_list($course_id);
											$quizIds = [];
											foreach ($current_quizzes as $quiz) {
												$quizIds[] = $quiz['id'];
											}
											$commaSeparatedQuizIds = implode(',', $quizIds);
											$current_quiz_count = count($current_quizzes);
											$meta_key = 'final_quize_group_' . $group_id;
											$quiz_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = '$meta_key' AND post_id in($commaSeparatedQuizIds)");

											if ($current_quiz_count > $quiz_count) { ?>
												<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
												<button id="create-final-quiz-modal-button" data-group_id="<?php esc_attr_e($ld_group_id) ?>" data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>">
													<i class="fas fa-plus"></i>
												</button>
											<?php } ?>

											<style>
												.quiz-meta-box {
													border-radius: 8px;
													color: white;
													padding: 16px;
													min-width: 335px;
													max-width: unset !important;
													justify-content: space-between;
													margin: 5px !important;
													box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
													text-align: left;
													position: relative;
												}

												.quiz-meta-box h4 {
													font-size: 18px;
													margin-bottom: 0px !important;
													color: white;
												}

												.quiz-meta-box p {
													font-size: 14px;
													margin: 4px 0;
												}

												.update-meta-button {
													background-color: #2e9e9e !important;
													border: none;
													color: white;
													top: 25px;
													right: 40px;
													cursor: pointer;
													font-size: 14px;
												}

												#create-final-quiz-modal-button {
													background-color: #ddd;
													color: #333;
													padding: 20px;
													text-align: center;
													font-size: 24px;
													min-width: 335px;
													margin: 5px;

													border: solid 1px;
													border-radius: 8px;
													cursor: pointer;
													transition: background-color 0.3s ease;
													display: flex;
													justify-content: center;
													align-items: center;
													width: 60px;
													height: 100px;
												}

												#create-final-quiz-modal-button:hover {
													background-color: #ccc;
												}
											</style>

											<script type="text/javascript">
												jQuery(document).ready(function($) {
													$(document).on('click', '.update-meta-button', function(event) {
														event.preventDefault();
														event.stopPropagation();
														var button = $(this);
														var quizId = button.data('quiz-id');
														var groupId = button.data('group-id');
														var currentValue = button.data('meta-value');
														var newValue = currentValue === 'yes' ? 'no' : 'yes';
														var nonce = '<?php echo $TPRM_ajax_nonce; ?>';

														$.ajax({
															url: ajaxurl,
															type: 'post',
															data: {
																action: 'update_final_quiz_meta',
																quiz_id: quizId,
																group_id: groupId,
																meta_value: newValue,
																nonce: nonce
															},
															success: function(response) {
																if (response.success) {
																	button.data('meta-value', newValue);
																	button.text(newValue === 'yes' ? 'Disable Final Quiz' : 'Enable Final Quiz');
																	button.closest('.quiz-meta-box').css('background-color', newValue === 'yes' ? 'green' : '#f5923f');

																	window.reload();
																} else {
																	alert('Failed to update meta. Please try again.');
																}
															}
														});
													});
												});
											</script>
										<?php
									}
										?>
										</div>
								</li>

							</ul>

						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}
