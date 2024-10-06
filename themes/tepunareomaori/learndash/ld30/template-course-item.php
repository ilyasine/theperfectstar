<?php
/**
 * Template part for displaying course list item
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */

global $post, $wpdb;

$is_enrolled            = false;
$current_user_id        = get_current_user_id();
$course_id              = get_the_ID();
$cats                   = wp_get_post_terms( $course_id, 'ld_course_category' );
$lession_list            = learndash_get_course_lessons_list( $course_id );
$lession_list            = array_column( $lession_list, 'post' );
$lesson_count            = learndash_get_course_lessons_list( $course_id, null, array( 'num' => - 1 ) );
$lesson_count            = array_column( $lesson_count, 'post' );
$paypal_settings        = LearnDash_Settings_Section::get_section_settings_all( 'LearnDash_Settings_Section_PayPal' );
$course_price           = trim( learndash_get_course_meta_setting( $course_id, 'course_price' ) );
$course_price_type      = learndash_get_course_meta_setting( $course_id, 'course_price_type' );
$course_button_url      = learndash_get_course_meta_setting( $course_id, 'custom_button_url' );
$courses_progress       = buddyboss_theme()->learndash_helper()->get_courses_progress( $current_user_id );
$course_progress        = isset( $courses_progress[ $course_id ] ) ? $courses_progress[ $course_id ] : null;
$course_status          = learndash_course_status( $course_id, $current_user_id );
$grid_col               = is_active_sidebar( 'learndash_sidebar' ) ? 3 : 4;
$course_progress_new    = buddyboss_theme()->learndash_helper()->ld_get_progress_course_percentage( $current_user_id, $course_id );
$admin_enrolled         = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
$course_pricing         = learndash_get_course_price( $course_id );
$user_course_has_access = sfwd_lms_has_access( $course_id, $current_user_id );


if ( $user_course_has_access ) {
	$is_enrolled = true;
} else {
	$is_enrolled = false;
}

// if admins are enrolled.
if ( current_user_can( 'administrator' ) && 'yes' === $admin_enrolled ) {
	$is_enrolled = true;
}

$class = '';
if ( ! empty( $course_price ) && ( 'paynow' === $course_price_type || 'subscribe' === $course_price_type || 'closed' === $course_price_type ) ) {
	$class = 'bb-course-paid';
}

$ribbon_text = get_post_meta( $course_id, '_learndash_course_grid_custom_ribbon_text', true );
?>

<li class="bb-course-item-wrap">

    <div class="bb-cover-list-item <?php echo esc_attr($class); ?>">
        <div class="bb-course-cover">
            <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" class="bb-cover-wrap">
                <?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } ?>
            </a>
        </div>
        <div class="bb-card-course-details <?php echo (is_user_logged_in() && isset($user_course_has_access) && $user_course_has_access) ? 'bb-card-course-details--hasAccess' : 'bb-card-course-details--noAccess'; ?>">
            <?php
            $lessons_count = sizeof($lesson_count);
            $total_lessons = ($lessons_count > 1 ? sprintf(__('%1$s %2$s', 'buddyboss-theme'), $lessons_count, LearnDash_Custom_Label::get_label('lessons')) : sprintf(__('%1$s %2$s', 'buddyboss-theme'), $lessons_count, LearnDash_Custom_Label::get_label('lesson')));
            $title_class = (function_exists('is_plugin_active') && is_plugin_active('wdm-course-review/wdm-course-review.php')) ? 'bb-course-title-with-review' : '';
            ?>
            <h2 class="bb-course-title <?php echo esc_attr($title_class); ?>">
                <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php if ($lessons_count > 0) { echo '<div class="course-lesson-count">' . $total_lessons . '</div>'; } else { echo '<div class="course-lesson-count">' . sprintf(__('0 %s', 'buddyboss-theme'), LearnDash_Custom_Label::get_label('lessons')) . '</div>'; } ?>
            </h2>
    		<div class="course-progress-wrap">
            <?php
                $xyz = learndash_activity_course_get_latest_completed_step($current_user_id, $course_id);
                ?>
                <script>
                var xyz = <?php echo json_encode($xyz); ?>;
                
                </script>

                <?php
                learndash_get_template_part(
                    'modules/progress.php',
                    array(
                        'context'   => 'course',
                        'user_id'   => $current_user_id,
                        'course_id' => $course_id,
                    ),
                    true
                );
                ?>

        </div>
            <div class="bb-course-footer">
                <?php if (!empty($course_price) && empty($is_enrolled)) { ?>
                    <span class="course-fee">
                        <?php echo '<span class="ld-currency">' . wp_kses_post(function_exists('learndash_get_currency_symbol') ? learndash_get_currency_symbol() : learndash_30_get_currency_symbol()) . '</span> ' . wp_kses_post($course_pricing['price']); ?>
                    </span>
                <?php } ?>
          
            </div>
        </div>
        <script>
        jQuery(document).ready(function ($) {
            $('#expand-course-<?php echo esc_attr($course_id); ?>').on('click', function(e) {
                e.preventDefault();
                var courseId = <?php echo esc_attr($course_id); ?>;
                var $this = $(this);
                var $content = $this.parents('.bb-course-item-wrap').find('.course-content-container');

                // Toggle visibility with slide effect
                $content.slideToggle(400, function() {
                    // Update icon classes and tooltip text after the animation completes
                    if ($content.is(':visible')) {
                        $this.find('span').removeClass('bb-icon-angle-down').addClass('bb-icon-angle-up');
                        $this.attr('data-bp-tooltip', '<?php esc_attr_e('Collapse Course Content', 'tprm-theme'); ?>');
                    } else {
                        $this.find('span').removeClass('bb-icon-angle-up').addClass('bb-icon-angle-down');
                        $this.attr('data-bp-tooltip', '<?php esc_attr_e('Expand Course Content', 'tprm-theme'); ?>');
                    }
                });
            });
        });
        </script>
        <div class="course-actions">
            <a  
                id="expand-course-<?php echo esc_attr($course_id) ?>" 
                data-bp-tooltip-pos="left"
                data-bp-tooltip="<?php esc_attr_e('Expand Course Content', 'tprm-theme') ?>"
                class="expand-course">			
                <span class="bb-icon-l bb-icon-angle-down"></span>
            </a>
        </div>
    </div>

    <!-- expand-course-content-container -->
	<div class="course-content-container"> 
		<?php echo do_shortcode('[course_content course_id="' . $course_id . '" user_id="' . $current_user_id . '" post_id="' . get_the_ID() . '"]'); ?> 
	</div>
</li>