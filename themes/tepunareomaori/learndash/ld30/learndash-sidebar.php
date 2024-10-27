<?php

global $post, $wpdb;

$parent_course_data = learndash_get_setting( $post, 'course' );
if ( 0 === $parent_course_data ) {
	$parent_course_data = $course_id;
	if ( 0 === $parent_course_data ) {
		$course_id = buddyboss_theme()->learndash_helper()->ld_30_get_course_id( $post->ID );
	}
	$parent_course_data = learndash_get_setting( $course_id, 'course' );
}

$parent_course       = get_post( $parent_course_data );
$parent_course_link  = $parent_course->guid;
$parent_course_title = $parent_course->post_title;
$is_enrolled         = false;
$current_user_id     = get_current_user_id();
$get_course_groups   = learndash_get_course_groups( $parent_course->ID );
$course_id           = $parent_course->ID;
$admin_enrolled      = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
$members_count       = 0;

if ( buddyboss_theme_get_option( 'learndash_course_participants', null, true ) ) {
	$members_count = buddyboss_theme()->learndash_helper()->buddyboss_theme_ld_course_enrolled_users_list( $parent_course->ID );
	$members_arr   = learndash_get_users_for_course( $course_id, array( 'number' => 5 ), false );
	if ( ( $members_arr instanceof WP_User_Query ) && ( property_exists( $members_arr, 'results' ) ) && ( ! empty( $members_arr->results ) ) ) {
		$members = $members_arr->get_results();
	} else {
		$members = array();
	}
}

if ( isset( $get_course_groups ) && ! empty( $get_course_groups ) && ( function_exists( 'buddypress' ) && bp_is_active( 'groups' ) ) ) {
	foreach ( $get_course_groups as $k => $group ) {
		$bp_group_id = (int) get_post_meta( $group, '_sync_group_id', true );
		if ( ! groups_is_user_member( bp_loggedin_user_id(), $bp_group_id ) ) {
			if ( ( $key = array_search( $group, $get_course_groups ) ) !== false ) {
				unset( $get_course_groups[ $key ] );
			}
		}
	}
}

if ( sfwd_lms_has_access( $course_id, $current_user_id ) ) {
	$is_enrolled = true;
} else {
	$is_enrolled = false;
}

// if admins are enrolled.
if ( is_tprm_admin() && is_tprm_manager() && 'yes' === $admin_enrolled ) {
	$is_enrolled = true;
}

// check if lesson sidebar is closed.
$side_panel_state_class = '';
if ( ( isset( $_COOKIE['lessonpanel'] ) && 'closed' === $_COOKIE['lessonpanel'] ) ) {
	$side_panel_state_class = 'lms-topic-sidebar-close';
}
?>


<div class="lms-topic-sidebar-wrapper <?php echo esc_attr( $side_panel_state_class ); ?>">
	<div class="lms-topic-sidebar-data">
		<?php
		$course_progress = learndash_course_progress(
			array(
				'user_id'   => get_current_user_id(),
				'course_id' => $parent_course->ID,
				'array'     => true,
			)
		);

		if ( empty( $course_progress ) ) {
			$course_progress = array(
				'percentage' => 0,
				'completed'  => 0,
				'total'      => 0,
			);
		}
		?>

		<div class="bb-elementor-header-items">
			<a href="#" id="bb-toggle-theme">
				<span class="sfwd-dark-mode" data-balloon-pos="down" data-balloon="<?php esc_attr_e( 'Dark Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-rl bb-icon-moon"></i></span>
				<span class="sfwd-light-mode" data-balloon-pos="down" data-balloon="<?php esc_attr_e( 'Light Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-sun"></i></span>
			</a>
			<a href="#" class="header-maximize-link course-toggle-view" data-balloon-pos="down" data-balloon="<?php esc_attr_e( 'Maximize', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-expand"></i></a>
			<a href="#" class="header-minimize-link course-toggle-view" data-balloon-pos="down" data-balloon="<?php esc_attr_e( 'Minimize', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-merge"></i></a>
		</div>

		<div class="lms-topic-sidebar-course-navigation">
			<div class="ld-course-navigation">
				<a title="<?php echo esc_attr( $parent_course_title ); ?>" href="<?php echo esc_url( get_permalink( $parent_course->ID ) ); ?>" class="course-entry-link">
					<span>
						<i class="bb-icon-l bb-icon-angle-left"></i>
						<?php echo sprintf( esc_html_x( 'Back to %s', 'link: Back to Course', 'buddyboss-theme' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?>
					</span>
				</a>
				<h2 class="course-entry-title"><?php echo esc_html( $parent_course_title ); ?></h2>
			</div>
		</div>

		<?php
		$course_progress = get_user_meta( get_current_user_id(), '_sfwd-course_progress', true );
		?>

<div class="lms-lessions-list">
    <?php
    if ( ! empty( $lession_list ) ) :
        $sections = learndash_30_get_course_sections( $parent_course->ID );
		$current_lesson_id = $post->ID;
    ?>
<select id="lessonDropdown" class="bb-lesson-dropdown">
    <option value="">Select a Lesson</option>
    <?php foreach ( $lession_list as $lesson ) : ?>
        <option value="<?php echo esc_url( get_permalink( $lesson->ID ) ); ?>" data-lesson-id="<?php echo esc_attr( $lesson->ID ); ?>"
            <?php selected( $current_lesson_id, $lesson->ID ); ?>>
            <?php echo esc_html( $lesson->post_title ); ?>
        </option>
    <?php endforeach; ?>
</select>


    <div id="lessonsContent">
        <?php
        foreach ( $lession_list as $lesson ) :
            $lesson_topics  = learndash_get_topic_list( $lesson->ID, $parent_course->ID );
            $lesson_quizzes = learndash_get_lesson_quiz_list( $lesson->ID, get_current_user_id(), $course_id );
            $lesson_sample  = learndash_get_setting( $lesson->ID, 'sample_lesson' ) == 'on' ? 'bb-lms-is-sample' : '';
            $attributes 	= learndash_get_course_step_attributes( $lesson->ID, $course_id, $user_id );

            $is_sample            = ( isset( $lesson->sample ) ? $lesson->sample : false );
            $bb_lesson_has_access = sfwd_lms_has_access( $lesson->ID, $user_id );
            $bb_available_date 	  = learndash_course_step_available_date( $lesson->ID, $course_id, $user_id, true );
            $atts                 = apply_filters( 'learndash_quiz_row_atts', ( ( isset( $bb_lesson_has_access ) && ! $bb_lesson_has_access && ! $is_sample ) || ( ! empty( $bb_available_date ) && ! $is_sample ) ? 'data-balloon-pos="up" data-balloon="' . __( "You don't currently have access to this content", 'buddyboss-theme' ) . '"' : '' ) );
            $atts_access_marker   = apply_filters( 'learndash_quiz_row_atts', ( ( isset( $bb_lesson_has_access ) && ! $bb_lesson_has_access && ! $is_sample ) || ( ! empty( $bb_available_date ) && ! $is_sample ) ? '<span class="lms-is-locked-ico" data-balloon-pos="left" data-balloon="' . esc_attr( $attributes[0]['label'] ) . '"><i class="bb-icon-f bb-icon-lock"></i></span>' : '' ) );
            $locked_class         = apply_filters( 'learndash_quiz_row_atts', ( ( isset( $bb_lesson_has_access ) && ! $bb_lesson_has_access && ! $is_sample ) || ( ! empty( $bb_available_date ) && ! $is_sample ) ? 'lms-is-locked' : 'lms-not-locked' ) );

            if ( $bb_lesson_has_access || ( ! $bb_lesson_has_access && apply_filters( 'bb_theme_ld_show_locked_lessons', true ) ) ) :
                ?>
                	<div id="lessonContent_<?php echo esc_attr( $lesson->ID ); ?>" class="lms-lesson-content" style="display: none;">
					   
					<div id="dropdownToggle" class="dropdown-trigger">
					<a href="<?php echo esc_url(get_permalink($lesson->ID)); ?>" class="dropdown-trigger">
			<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 512 512">
				<path d="M260 0c2.674 1.812 2.959 2.878 4 6l.098 6.637-.01 3.936-.025 4.115-.014 4.15L264 35l3.2-.008 77.066-.147 37.268-.071 32.48-.056 17.201-.035 16.187-.022 5.943-.018 8.112-.003 2.376-.026c5.679.046 9.819 1.222 14.029 5.106l1.449 1.781 1.488 1.781c3.591 5.147 2.462 12.154 2.494 18.177l.062 4.431c.31 15.448.31 15.448-4.357 22.111-1.895 1.875-1.895 1.875-3.812 3l-1.895 1.188c-3.125 1.107-6.001.862-9.293.813l.008 2.625.147 63.22.071 30.573.056 26.645.035 14.11.022 13.279.018 4.875.003 6.655.008 3.763c-.402 3.543-1.235 5.427-3.369 8.255-2.853 1.427-4.835 1.31-8 1l-5-4V93H64v52c-2 5-2 5-3.687 6.375-3.38.913-5.933.424-9.312-.375-1.694-1.214-1.694-1.214-3-3-.361-2.449-.361-2.449-.341-5.389l.001-3.336.047-3.599.013-3.686.093-11.677.041-7.908L48 93l-3.797.105C39.733 93.23 37.459 91.909 34 89c-4.439-5.039-5.165-8.066-5.203-14.641l-.017-2.302-.02-4.832-.072-7.354-.016-4.699-.037-2.207c.021-4.74.868-7.94 3.366-11.965 5.631-5.197 9.776-6.296 17.34-6.241l2.572-.005 8.545.019 6.12-.001 16.629.025 17.373.016 32.905.043 37.458.043L248 35l-.063-2.782-.156-10.243-.082-4.429-.089-6.375-.063-3.872C248 4 248 4 249.897 1.583 253.409-1.061 255.694-.478 260 0zM45 51c-1.241 2.482-1.129 4.164-1.133 6.938l-.004 2.961.012 3.102-.012 3.102.004 2.961.003 2.723c-.014 2.254-.014 2.254 1.13 4.213h422V52c-1.098-1.323-1.098-1.323-3.824-1.124l-3.874.001-2.157-.003c-2.416-.003-4.832.002-7.248.006l-5.212-.003c-4.798-.002-9.596.002-14.393.007l-15.495.002c-8.957-.001-17.913.003-26.87.008l-38.849.012-63.023.018-61.238.021-3.81.001-18.921.004L45 51zm11.625 115.063c4.518 1.255 4.518 1.255 6.375 3.938 1.146 3.439 1.128 6.084 1.131 9.712l.006 2.078.004 6.909.011 4.947.016 10.662.041 16.853.019 5.767.01 2.917.087 39.241.069 26.462.027 14.008.037 13.171v4.837l.028 6.597.007 3.731c.62 3.815 1.485 4.76 4.505 7.108 2.958.595 2.958.595 6.306.495l3.954.026 4.354-.033 4.617.012 12.708-.021 13.697-.002 26.841-.031 31.239-.029 55.727-.06 54.11-.062 3.366-.001 13.394-.006 51.516-.04 15.575-.014 32.759-.042 13.551-.019 12.394-.021 4.494-.004 6.09-.013 3.422-.005c2.475-.111 4.526-.407 6.884-1.129.922-3.146 1.135-5.983 1.158-9.256l.031-3.175.022-3.412.086-7.152.104-11.278.122-10.882.01-3.412.05-3.175.025-2.79c.471-2.964 1.303-4.33 3.392-6.466 3.429-.873 5.893-1.329 9.313-.312 2.081 1.619 2.697 2.871 3.688 5.313.261 2.471.261 2.471.275 5.268l.029 3.187-.003 3.431.013 3.55.002 7.442.046 11.33.002 7.24.028 3.401c-.051 8.685-.993 16.752-7.391 23.15-6.254 4.758-11.821 6.306-19.593 6.241l-2.255.005c-2.483.003-4.965-.008-7.448-.019a5629.02 5629.02 0 0 0-5.352.001c-4.836.001-9.673-.011-14.509-.025l-15.171-.016-28.718-.043-32.699-.043L264 362v24h7c6.122 1.205 10.656 3.552 15 8 2.908 4.613 4.718 9.547 3.574 15.047-.367 1.33-.765 2.652-1.199 3.961-.652 3.463.124 6.177 1.953 9.154l1.707 2.182 1.929 2.496 2.098 2.66 4.383 5.652 2.282 2.925 10.71 13.985 2.051 2.691 12.023 15.941 6.445 8.484 2.168 2.836 4.293 5.602c9.438 12.339 9.438 12.339 10.082 17.008-.5 3.375-.5 3.375-2 5.813-2.83 1.769-5.088 2.393-8.441 2.172-4.366-1.292-6.481-5.07-9.059-8.609l-2.543-3.355-6.027-8.047-11.242-14.848-17.725-23.473-14.428-18.987-1.523-1.981-1.329-1.725c-1.113-1.49-2.151-3.036-3.182-4.584-2.207-.364-2.207-.364-4.625-.187l-2.477.082L264 427l.02 2.965.135 27.676.071 14.228.058 13.733.033 5.238.023 7.34.033 2.184c-.029 4.794-.926 8.188-4.373 11.636-7.192.589-7.192.589-10.103-1.534-2.586-3.363-2.43-6.287-2.351-10.356l-.002-2.396.065-7.824.017-5.431.099-14.283.079-14.579L248 427l-4.353-.109-2.448-.061c-2.389-.011-2.389-.011-4.554 1.908l-1.832 2.574-2.25 3.047-1.223 1.74-4.527 6.229-5.307 7.206c-4.072 5.567-8.246 11.04-12.505 16.466a727.4 727.4 0 0 0-14.125 18.625l-1.741 2.373-3.43 4.678-8.391 11.386-1.495 2.04c-2.126 2.856-3.827 4.904-6.818 6.898-3.017.335-5.098.559-8 0-2.562-2.062-2.562-2.062-4-5-.082-7.167 3.78-11.29 8.125-16.687l4.402-5.621 2.265-2.871c3.369-4.305 6.631-8.686 9.895-13.071l13.75-18.187 1.555-2.033 9.305-12.08 4.391-5.699 2.109-2.709 1.965-2.557 1.749-2.259c1.583-2.365 2.647-4.508 3.49-7.226a166.67 166.67 0 0 0-2-6c-.469-5.904.093-9.687 3-15 3.816-4.418 7.462-7.61 13.332-8.512L248 386v-24l-6.804.027-63.476.18-32.634.095-28.45.075-15.058.047-14.188.03-5.193.024c-10.029.085-19.443.161-27.635-6.442L53 354.063l-1.625-1.965c-3.103-4.733-3.629-8.692-3.649-14.316l-.016-2.17-.016-7.181-.025-5.155-.036-13.989-.017-8.738-.041-30.475-.075-28.423-.051-24.398-.038-14.574-.012-13.698-.021-5.031.008-6.859-.006-3.88C48 170 48 170 50.312 167.553 53 166 53 166 56.625 166.063zM238 405c.276 1.941.276 1.941 1 4 3.494 2.329 4.93 2.257 9.059 2.266l3.607.008 3.771-.023 3.76.023 3.611-.008 3.306-.007c2.828-.254 4.485-.795 6.885-2.259.25-2.416.25-2.416 0-5-5.057-3.371-11-2.327-16.937-2.312l-3.904-.049-3.748-.002-3.433-.009c-3.244.406-4.606 1.182-6.977 3.372z"/>
			</svg>
			</a>
		
		</div>	
					
					<div class="lms-lesson-header <?php echo esc_attr( $lesson_sample . ' ' . $locked_class ); ?>">

							<?php if (!empty($lesson_topics)) : ?>
								<ol class="bb-type-list">
									<?php 
									$current_index = -1;
									foreach ($lesson_topics as $index => $lesson_topic) {
										$bb_topic_has_access = sfwd_lms_has_access($lesson_topic->ID, $user_id);
										$learndash_available_date = learndash_course_step_available_date($lesson_topic->ID, $course_id, $user_id, true);
										$is_topic_complete = learndash_is_topic_complete($user_id, $lesson_topic->ID, $course_id);

										if ($bb_topic_has_access || (!$bb_topic_has_access && apply_filters('bb_theme_ld_show_locked_topics', true))) {
											if ($lesson_topic->ID === $post->ID) {
												$current_index = $index;
											}
											?>
											<li class="lms-topic-item <?php echo $lesson_topic->ID === $post->ID ? esc_attr('current') : ''; ?> <?php echo (!empty($learndash_available_date)) ? 'lms-topic-is-locked' : 'lms-topic-not-locked'; ?>">
												<a class="flex bb-title bb-lms-title-wrap" href="<?php echo esc_url(get_permalink($lesson_topic->ID)); ?>">
													<div class="bb-lms-topic-circle" style="<?php echo $is_topic_complete ? 'border-color: teal; background: teal; color: white;' : ''; ?>">
														<span class="bb-lms-topic-tooltip"><?php echo esc_html($lesson_topic->post_title); ?></span>
														<?php echo $index + 1; ?>
													</div>
												</a>
												<div class="backbar" style="<?php echo $is_topic_complete ? 'background-color: teal;' : ''; ?>"> 
												</div>
											</li>
											<?php
										}
									} ?>
								</ol>
							<?php endif; ?>

							<?php if (!empty($lesson_quizzes)) : ?>
								<ol class="bb-type-list">
									<?php 
									foreach ($lesson_quizzes as $quiz_counter => $quiz) {
										// Ensure $quiz is an object and has a valid ID property
										if (is_object($quiz) && isset($quiz->ID) && is_int($quiz->ID)) {
											$quiz_has_access = sfwd_lms_has_access($quiz->ID, $user_id);
											$learndash_available_date = learndash_course_step_available_date($quiz->ID, $course_id, $user_id, true);
											$is_quiz_complete = learndash_is_quiz_complete($user_id, $quiz->ID, $course_id);

											if ($quiz_has_access || (!$quiz_has_access && apply_filters('bb_theme_ld_show_locked_quizzes', true))) {
												?>
												<li class="lms-quiz-item">
													<a class="flex bb-title bb-lms-title-wrap" href="<?php echo esc_url(get_permalink($quiz->ID)); ?>">
														<div class="bb-lms-quiz-circle" style="<?php echo $is_quiz_complete ? 'border-color: teal; background: teal; color: white;' : ''; ?>">
															<span class="bb-lms-quiz-tooltip"><?php echo esc_html($quiz->post_title); ?></span>
															<?php echo $quiz_counter + 1; ?>
														</div>
													</a>
													<div class="backbar" style="<?php echo $is_quiz_complete ? 'background-color: red;' : ''; ?>"> 
													</div>
												</li>
												<?php
											}
										} else {
											// Handle case where $quiz is not an object or doesn't have a valid ID
											error_log('Invalid quiz data detected. $quiz is either not an object or does not have a valid ID.');
										}
									} ?>
								</ol>
							<?php endif; ?>
<style>.course-entry-title{
	display:none;
}</style>
				     	</div>


              		  </div>
                <?php
            endif;
        endforeach;
        ?>
    </div>
    <?php
    endif;
    ?>
</div>


		<div class="ld-lesson-status">
			<div class="ld-breadcrumbs after">

				<?php
				learndash_get_template_part(
					'modules/breadcrumbs.php',
					array(
						'context'   => 'lesson',
						'user_id'   => $user_id,
						'course_id' => $course_id,
						'post'      => $post,
					),
					true
				);

				$status = '';
				if ( ( is_user_logged_in() ) && ( true === $has_access ) ) {
					$status = ( learndash_is_item_complete( $post->ID, $user_id, $course_id ) ? 'complete' : 'incomplete' );
				} else {
					$course_status = '';
					$status        = '';
				}
			
				?>

			</div>

		</div>

		<?php

		$course_quizzes = learndash_get_course_quiz_list( $course_id );

		$user_id = get_current_user_id();
		$user_groups = learndash_get_users_group_ids( $user_id , true );

		if ( ! empty( $course_quizzes ) ) :
			?>
			<div class="lms-course-quizzes-list">
				<h4 class="lms-course-quizzes-heading"><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ); ?></h4>
				<ul class="lms-quiz-list bb-type-list">
					<?php
					foreach ( $course_quizzes as $course_quiz ) {

						$is_sample          = ( isset( $lesson->sample ) ? $lesson->sample : false );
						$bb_quiz_has_access = sfwd_lms_has_access( $course_quiz['post']->ID, $user_id );
						$atts               = apply_filters( 'learndash_quiz_row_atts', ( isset( $bb_quiz_has_access ) && ! $bb_quiz_has_access && ! $is_sample ? 'data-balloon-pos="up" data-balloon="' . __( "You don't currently have access to this content", 'buddyboss-theme' ) . '"' : '' ) );
						$atts_access_marker = apply_filters( 'learndash_quiz_row_atts', ( isset( $bb_quiz_has_access ) && ! $bb_quiz_has_access && ! $is_sample ? '<span class="lms-is-locked-ico"><i class="bb-icon-f bb-icon-lock"></i></span>' : '' ) );
						$locked_class       = apply_filters( 'learndash_quiz_row_atts', ( isset( $bb_quiz_has_access ) && ! $bb_quiz_has_access && ! $is_sample ? 'lms-is-locked' : 'lms-not-locked' ) );
						$attributes         = learndash_get_course_step_attributes( $course_quiz['post']->ID, $course_id, $user_id );

						$quiz_enabled = get_post_meta($course_quiz['post']->ID, 'quiz_enabled', true);
						$is_final_quiz = get_post_meta($course_quiz['post']->ID, 'final_quiz', true);

						$quiz_id = $course_quiz['post']->ID;

						foreach ($user_groups as $group_id) :

							$quiz_group = final_quiz_is_enabled_for_group($quiz_id);
				
							$quiz_enabled = learndash_is_user_in_group($user_id, $quiz_group);

							if (!$quiz_enabled && $is_final_quiz && is_student())	$locked_class = 'lms-is-locked';

						endforeach;
						
						if ( ! empty( $attributes ) && empty( $atts ) ) :
							foreach ( $attributes as $attribute ) :
								$scheduled_class = $attribute['icon'] == 'ld-icon-calendar' ? 'lms-is-scheduled' : 'lms-not-scheduled';
							endforeach;
						endif;

						?>
						<li class="lms-quiz-item <?php echo $course_quiz['post']->ID == $post->ID ? esc_attr( 'current' ) : ''; ?> <?php echo esc_attr( $locked_class ); ?> <?php echo isset( $scheduled_class ) ? esc_attr( $scheduled_class ) : ''; ?>">
							<a class="flex bb-title bb-lms-title-wrap" href="<?php echo esc_url( get_permalink( $course_quiz['post']->ID ) ); ?>" title="<?php echo esc_attr( $course_quiz['post']->post_title ); ?>">
								<span class="bb-lms-ico bb-lms-ico-quiz"><i class="bb-icon-rl bb-icon-question"></i></span>
								<span class="flex-1 push-left bb-lms-title <?php echo learndash_is_quiz_complete( $user_id, $course_quiz['post']->ID, $course_id ) ? esc_attr( 'bb-completed-item' ) : esc_attr( 'bb-not-completed-item' ); ?>">
									<span class="bb-quiz-title"><?php echo $course_quiz['post']->post_title; ?></span>
									<?php echo $atts_access_marker; ?>
								</span>
								<?php								
								if ( isset( $scheduled_class ) && $scheduled_class == 'lms-is-scheduled' ) :
									?>
									<span class="lms-quiz-status-icon" data-balloon-pos="left" data-balloon="<?php echo esc_attr( $attribute['label'] ); ?>"><i class="bb-icon-f bb-icon-lock"></i></span>
									<?php
								endif;
								//TODO Final Quiz
								foreach ($user_groups as $group_id) :

									$quiz_group = final_quiz_is_enabled_for_group($quiz_id);
						
									$quiz_enabled = learndash_is_user_in_group($user_id, $quiz_group);
		
									if ( !$quiz_enabled && $is_final_quiz && is_student() ) :
										?>
										<span class="lms-quiz-status-icon" data-balloon-pos="left" data-balloon="<?php _e( "The final quiz is not yet available", 'tprm-theme' ); ?>"><i class="bb-icon-f bb-icon-lock"></i></span>
										<?php
									endif;
		
								endforeach;						
								?>
								<?php
								if ( learndash_is_quiz_complete( $user_id, $course_quiz['post']->ID, $course_id ) ) :
									?>
									<div class="bb-completed bb-lms-status" data-balloon-pos="left" data-balloon="<?php esc_attr_e( 'Completed', 'buddyboss-theme' ); ?>">
										<div class="i-progress i-progress-completed"><i class="bb-icon-check"></i></div>
									</div>
									<?php
								else :
									?>
									<div class="bb-not-completed bb-lms-status" data-balloon-pos="left" data-balloon="<?php esc_attr_e( 'Not Completed', 'buddyboss-theme' ); ?>">
										<div class="i-progress i-progress-not-completed"><i class="bb-icon-l bb-icon-circle"></i>
										</div>
									</div>
									<?php
								endif;
								?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<?php
		endif;

		if ( buddyboss_theme_get_option( 'learndash_course_participants', null, true ) && ! empty( $members ) ) :
			?>
			<div class="lms-course-members-list">
				<h4 class="lms-course-sidebar-heading">
					<?php esc_html_e( 'Participants', 'buddyboss-theme' ); ?>
					<span class="lms-count"><?php echo esc_html( $members_count ); ?></span>
				</h4>
				<input type="hidden" name="buddyboss_theme_learndash_course_participants_course_id" id="buddyboss_theme_learndash_course_participants_course_id" value="<?php echo esc_attr( $course_id ); ?>">
				<div class="bb-course-member-wrap">

					<ul class="course-members-list">
						<?php
						$count = 0;
						foreach ( $members as $course_member ) :
							if ( $count > 4 ) {
								break;
							}
							?>
							<li>

								<?php if ( class_exists( 'BuddyPress' ) ) { ?>
								<a href="<?php echo esc_url( bp_core_get_user_domain( (int) $course_member ) ); ?>">
									<?php } ?>
									<img class="round" src="<?php echo esc_url( get_avatar_url( (int) $course_member, array( 'size' => 96 ) ) ); ?>" alt=""/>
									<?php
									if ( class_exists( 'BuddyPress' ) ) {
										?>
										<span><?php echo bp_core_get_user_displayname( (int) $course_member ); ?></span>
										<?php
									} else {
										$course_member = get_userdata( (int) $course_member );
										?>
										<span><?php echo $course_member->display_name; ?></span>
										<?php
									}
									if ( class_exists( 'BuddyPress' ) ) {
										?>
								</a>
										<?php
									}
									?>
							</li>
							<?php
							$count ++;
						endforeach;
						?>
					</ul>

					<ul class="course-members-list course-members-list-extra">
					</ul>
					<?php
					if ( $members_count > 5 ) {
						?>
						<a href="javascript:void(0);" class="list-members-extra lme-more"><span class="members-count-g"></span> <?php esc_html_e( 'Show more', 'buddyboss-theme' ); ?><i class="bb-icon-l bb-icon-angle-down"></i></a>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		endif;

		if ( is_active_sidebar( 'learndash_lesson_sidebar' ) ) {
			?>
			<div class="ld-sidebar-widgets">
				<ul>
					<?php dynamic_sidebar( 'learndash_lesson_sidebar' ); ?>
				</ul>
			</div>
			<?php
		}
		?>
	</div>
</div>
