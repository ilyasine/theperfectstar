<?php
/**
 * LearnDash LD30 Displays course list
 *
 * @since 3.0.0
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );


if ( defined( 'LEARNDASH_COURSE_GRID_FILE' ) && isset( $shortcode_atts['course_grid'] ) && '' !== $shortcode_atts['course_grid'] ) {

	include get_stylesheet_directory(). '/learndash/ld30/shortcodes/course_list_grid_template.php';//

} else {

	$course_id = $shortcode_atts['course_id'];

	if ( is_user_logged_in() ) {
		$cuser   = wp_get_current_user();
		$user_id = $cuser->ID;
	} else {
		$user_id = false;
	}
	?>

	<div class="learndash-wrapper">
		<div class="ld-item-list">
			<div class="ld-item-list-item">
				<div class="ld-item-list-item-preview">				
					<div class="ld-item-name course-preview ld-primary-color-hover" data-course_name="<?php echo esc_attr( get_the_title() ); ?>" data-related_course="<?php echo esc_attr(get_the_ID()) ?>" target="_blank" href="<?php echo esc_url( learndash_get_step_permalink( get_the_ID() ) ); ?>"><?php echo esc_html( get_the_title() ); ?>
						<div href="#preview-course-<?php echo esc_attr(get_the_ID()) ?>" 
							id="preview-course"
							class="ld-item-link ld-primary-color-hover preview-course" 
							data-course-id="<?php echo esc_attr(get_the_ID()) ?>" 
							data-parent-course="<?php echo esc_attr(get_the_ID()) ?>" 
							data-security="<?php echo esc_attr($TPRM_ajax_nonce); ?>">
							<span><?php _e('Preview this Course', 'tprm-theme'); ?></span>
							<span class="bb-icon-l bb-icon-brand-youtube"></span>
						</div>
						<div class="ld-status-icon ld-status-incomplete"></div>						
					</div>
					
				</div>
			</div>

			<div id="preview-course-<?php echo esc_attr(get_the_ID()) ?>" 
				data-course-id="<?php echo esc_attr(get_the_ID()) ?>" 			
				class="bb-modal preview-course-container mfp-with-anim mfp-hide bb_course_video_details">
					<button title="Close (Esc)" id="see-related-courses" type="button" class="mfp-close">×</button>
				<div class="popup-scroll"></div>					
			</div>		
			
		<?php
		switch ( get_post_type() ) {

			case ( 'sfwd-courses' ):
				$wrapper = array(
					'<div class="learndash-wrapper">
                        <div class="ld-item-list">',
					'</div>
                    </div>',
				);

				$output = learndash_get_template_part(
					'/course/partials/row.php',
					array(
						'course_id' => $course_id,
						'user_id'   => $user_id,
					)
				);


				break;

			case ( 'sfwd-lessons' ):
				global $course_lessons_results;

				if ( isset( $course_lessons_results['pager'] ) ) :
					learndash_get_template_part(
						'modules/pagination.php',
						array(
							'pager_results' => $course_lessons_results['pager'],
							'pager_context' => 'course_lessons',
						),
						true
					);
				endif;

				break;

			case ( 'sfwd-topic' ):
				$wrapper = array(
					'<div class="learndash-wrapper">
                    <div class="ld-item-list">',
					'</div>
                </div>',
				);

				$output = learndash_get_template_part(
					'/topic/partials/row.php',
					array(
						'topic'     => $post,
						'course_id' => $course_id,
						'user_id'   => $user_id,
					)
				);

				break;
		}
		?>
	</div>

	<?php
}

