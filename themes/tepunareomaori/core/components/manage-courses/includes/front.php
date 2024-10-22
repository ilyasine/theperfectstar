<?php 



add_action('learndash_init', 'remove_courses_tab_for_leaders' );

/**
 * Remove courses profile tab for Teachers and directors
 *
 * @since V2
 */
 
 function remove_courses_tab_for_leaders(){

	global $bp_ld_sync;

	if ( ! is_TPRM_admin() && ! is_student() ) {
	
		remove_action( 'bp_setup_nav', array( $bp_ld_sync, 'setup_nav' ), 100 );
		remove_filter( 'nav_menu_css_class', array( $bp_ld_sync, 'bb_ld_active_class' ), PHP_INT_MAX, 2 );
	}
}

/**
 * Remove extra profile tabs for Teachers and directors
 *
 * @since V3
 */

function remove_extra_tabs_for_leaders() {
	if ( ! is_TPRM_admin() && ! is_student() ) {
		bp_core_remove_nav_item('additional-content');
		bp_core_remove_nav_item('exam');
    }
}
add_action('bp_setup_nav', 'remove_extra_tabs_for_leaders', 1000);

/**
 * Helper function to check we are in a learndash learning page
 *
 * @since V2
 */

function is_learning(){
	if( is_singular('sfwd-courses') || is_singular('sfwd-question') || is_singular('sfwd-quiz') || is_singular('sfwd-lessons') || is_singular('sfwd-topic') ){
		return true;
	}

	return false;
}



function group_courses_ids(){

	$GroupCourses = bp_ld_sync('buddypress')->courses->getGroupCourses();
	$GroupCourses_Ids = array();

	foreach ($GroupCourses as $GroupCourse) {
		$GroupCourses_Ids[] = $GroupCourse->ID;
	}
	
	return $GroupCourses_Ids;
}


// Define your custom function to modify the filter
function render_related_course_list_query_args($filter, $atts) {

	global $current_course_id;

	 // Get terms for each taxonomy
	$age_terms =  '';
	if ( ! empty( wp_get_post_terms($current_course_id, 'age', array('fields' => 'ids')) ) ) {
		$age_terms =  wp_get_post_terms($current_course_id, 'age', array('fields' => 'ids'))[0];
	}
	$course_block_terms = '';
	if ( ! empty( wp_get_post_terms($current_course_id, 'course_block', array('fields' => 'ids')) ) ) {
		$course_block_terms =  wp_get_post_terms($current_course_id, 'course_block', array('fields' => 'ids'))[0];
	}
	
    // Check if the 'post__not_in' parameter needs to be added
    if (!isset($filter['post__not_in'])) {
        // If not set, initialize it as an empty array
        $filter['post__not_in'] = array();
    }

    if (!empty(group_courses_ids())) {
        $filter['post__not_in'] = array_merge($filter['post__not_in'], group_courses_ids());
    }

    // Initialize the tax_query parameter if not already set
    if (!isset($filter['tax_query'])) {
        $filter['tax_query'] = array();
    }


    // Construct tax_query for each taxonomy
    $tax_queries = array();

    if (!empty($age_terms)) {
        $tax_queries[] = array(
            'taxonomy' => 'age',
            'field' => 'term_id',
            'terms' => $age_terms,
            'operator' => 'IN',
        );
    }

    if (!empty($course_block_terms)) {
        $tax_queries[] = array(
            'taxonomy' => 'course_block',
            'field' => 'term_id',
            'terms' => $course_block_terms,
            'operator' => 'IN',
        );
    }

    // Combine tax_queries using relation 'AND'
    if (!empty($tax_queries)) {
        $filter['tax_query'][] = array(
            'relation' => 'AND',
            $tax_queries,
        );
    }

    // Return the modified filter
    return $filter;
}

add_filter('learndash_ld_course_list_query_args', 'render_related_course_list_query_args', 10, 2);

function group_courses_position() {
    $ld_group_id = bp_ld_sync('buddypress')->helpers->getLearndashGroupId(bp_get_current_group_id());
    $group_courses = learndash_get_group_courses_list($ld_group_id);
    $group_courses_position_array = array();

    // Initialize highest and lowest course positions
    $highest_course_position = null;
    $lowest_course_position = null;

    foreach ($group_courses as $group_course) {
        $course_position = get_post_meta($group_course, 'course_position_in_group_' . $ld_group_id, true);

        // Skip NULL positions
        if ($course_position === null) {
            continue;
        }

        // Add course position to the array
        $group_courses_position_array[] = $course_position;

        // Update highest and lowest course positions
        if ($highest_course_position === null || $course_position > $highest_course_position) {
            $highest_course_position = $course_position;
        }

        if ($lowest_course_position === null || $course_position < $lowest_course_position) {
            $lowest_course_position = $course_position;
        }
    }

    // Now $highest_course_position and $lowest_course_position will contain the highest and lowest course positions respectively
    return array(
        'highest_course_position' => $highest_course_position,
        'lowest_course_position' => $lowest_course_position
    );
}


add_action('wp_ajax_update_course_position', 'update_course_position_callback');

function update_course_position_callback() {

	if (isset($_POST['payload']) && $_POST['payload'] == 'update_course_position' && 
		$_POST['group_id'] && $_POST['course_id'] && $_POST['new_position']) {

		check_ajax_referer('TPRM_nonce', 'security');

		$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
		$new_position = isset($_POST['new_position']) ? intval($_POST['new_position']) : 0;
		$group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;

		// Update the course position meta data
		update_post_meta($course_id, 'course_position_in_group_' . $group_id, $new_position);

		// Send success response
		wp_send_json_success(__('Courses order has been successfully updated', 'tprm-theme'));

		wp_die();
		
	} else {
		// Send error response if the required data is not received
		wp_send_json_error(__('Failed to update Courses order. Course ID or Group ID is missing.', 'tprm-theme'));

		wp_die();
	}
}


add_action('course_actions', 'course_actions_button');

function course_actions_button($course_id){

	global $wpdb, $TPRM_ajax_nonce, $current_course_id;

	$current_course_id = $course_id;

	$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );
	$current_user_id = get_current_user_id();
	$classe_name = bp_get_current_group_name();
	$ld_group_id = bp_ld_sync( 'buddypress' )->helpers->getLearndashGroupId( bp_get_current_group_id() );
	$course_name = get_the_title($course_id);
	$age =  '';
	if ( ! empty( wp_get_post_terms($course_id, 'age', array('fields' => 'ids')) ) ) {
		$age =  wp_get_post_terms($course_id, 'age', array('fields' => 'ids'))[0];
	}
	$block = '';
	if ( ! empty( wp_get_post_terms($course_id, 'course_block', array('fields' => 'ids')) ) ) {
		$block =  wp_get_post_terms($course_id, 'course_block', array('fields' => 'ids'))[0];
	}

	$course_position = get_post_meta($course_id, 'course_position_in_group_' . $ld_group_id, true);

	$popup_notes = array(
		 sprintf(__('You can choose from the courses below, the course you want to teach instead of <strong>%s</strong>', 'tprm-theme'), $course_name),
		 __('This change will affect all your students in this classroom', 'tprm-theme'),	
		 __('Only one course can be selected', 'tprm-theme'),	
	);
	//Display Expand course content
	?>
	<script>
jQuery(document).ready(function ($) {
    $('#expand-course-<?php echo esc_attr($course_id); ?>').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var $content = $this.closest('.course-actions').find('.course-content-container').first();

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
		<div class="course-content-container">
			<?php echo do_shortcode('[course_content course_id="' . $course_id . '" user_id="' . $current_user_id . '" post_id="' . get_the_ID() . '"]'); ?> 
		</div>
		<?php
		// Display change course button only if there are related courses available
		if( !empty($age) && !empty($block) ){ 
		//start change course ?>
			<a href="#see-related-courses-<?php echo esc_attr($course_id) ?>" 
				data-mfp-src="#see-related-courses-<?php echo esc_attr($course_id) ?>"
				id="see-related-courses" 
				data-course_id="<?php esc_attr_e($course_id); ?>"
				data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>"
				data-bp-tooltip-pos="right"
				data-bp-tooltip="<?php esc_attr_e('Change Course', 'tprm-theme') ?>"
				class="change-course button1">			
				<span class="bb-icon-l bb-icon-exchange"></span>
			</a>
			<?php // related courses popoup start ?>
			<div id="see-related-courses-<?php echo esc_attr($course_id) ?>" class="see-related-courses mfp-prevent-close mfp-with-anim mfp-hide white-popup">
				<header class="see-related-courses-head-title"><?php echo sprintf(__('Courses Related to %s', 'tprm-theme'), $course_name)  ?></header>
				<div class="see-related-courses-head">
					<h1 class="notes"><?php _e('Notes', 'tprm-theme') ?></h1>
					<ul>
						<?php foreach ($popup_notes as $popup_note) { ?>
							<li class="see-related-courses-note"><?php echo $popup_note; ?></li>
						<?php } ?>			
					</ul>
					<div class="see-related-courses-head-notices">
						<?php _e('No Course has been Selected', 'tprm-theme')  ?>
					</div>
				</div>
				<div class="see-related-courses-footer">
					<button 							
						data-balloon-pos="up"
						data-balloon="<?php echo sprintf(esc_attr__('I confirm the selected course will replace %s for this classroom', 'tprm-theme'), $course_name); ?>"
						data-course_name="<?php esc_attr_e($course_name) ?>"
						data-current_course_id="<?php esc_attr_e($course_id); ?>"
						data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>"
						data-group="<?php esc_attr_e($ld_group_id); ?>"
						data-position="<?php echo esc_attr($course_position) ?>"
						id="confirm_selected_course"
						class="confirm_selected_course">
						<?php _e('Confirm', 'tprm-theme')?>
					</button>
					<button 							
						data-balloon-pos="up"
						data-balloon="<?php esc_attr_e('Cancel change, The course will remain the same before you click on change course', 'tprm-theme'); ?>"
						class="mfp-close button"
						type="button"
						id="cancel_selected_course">
						<?php _e('Cancel', 'tprm-theme')?>
					</button>			
				</div>
				<div class="popup-scroll">
					<?php echo ld_course_list(); ?>			
				</div>			
			</div>
			<?php // related courses popoup end ?>
	</div>
	<?php }
	//End course actions div	

}

add_action('wp_ajax_get_course_preview', 'get_course_preview_callback');

function get_course_preview_callback() {
    // Check nonce
    check_ajax_referer('TPRM_nonce', 'security');

    // Get the course ID from the AJAX request
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

	$course_video_embed  = get_post_meta( $course_id, '_buddyboss_lms_course_video', true );
	$file_info           = pathinfo( $course_video_embed );
	
	 if ( has_excerpt( $course_id ) ) { ?>						
	<div class="course-description">	
		<h2 class="course-description-heading"><?php _e('Course Description', 'tprm-theme') ?></h2>	
		<div class="course-description-content">					
			<?php echo get_the_excerpt( $course_id ); ?>
		</div>
	</div>
	<?php } 
	if ( '' !== $course_video_embed ) { ?>
	<div class="course-video-embed">
		<h2 class="course-video-embed-content"><?php _e('Course Video', 'tprm-theme') ?></h2>	
		<div class="course-video-embed-content">					
		<?php if ( wp_oembed_get( $course_video_embed ) ) {
			echo wp_oembed_get( $course_video_embed );
		} elseif ( isset( $file_info['extension'] ) && 'mp4' === $file_info['extension'] ) {
			?>
			<video width="100%" controls>
				<source src="<?php echo $course_video_embed; ?>" type="video/mp4">
				<?php _e( 'Your browser does not support HTML5 video.', 'tprm-theme' ); ?>
			</video>
			<?php
		} else {
			_e( 'There is no Video Preview for this Course.', 'tprm-theme' );
		} ?>
		</div>
	</div>		
	<?php }else {
		_e( 'There is no Video Preview for this Course.', 'tprm-theme' );
	} ?>					
	<div class="course-content"><?php echo do_shortcode('[course_content course_id="' . $course_id . '"]'); ?></div>
	<?php
    // Don't forget to exit
    wp_die();
}

/**
 * Order courses by position
 *
 * @since V3
 */

add_filter('learndash_group_courses_order', 'group_courses_order', 10, 2);

function group_courses_order($group_courses_args, $group_id){

    $group_courses_args = array(
         'meta_key' => 'course_position_in_group_' . $group_id,
         'orderby' => 'meta_value',
         'order' => 'ASC'
    );
 
    return $group_courses_args;
 
 }
 
 
 add_action('wp_ajax_replace_course', 'TPRM_ajax_replace_course');
 
function TPRM_ajax_replace_course(){
 
    if (isset($_POST['payload']) && $_POST['payload'] == 'replace_course' && $_POST['current_course_id'] && $_POST['ld_group_id'] && $_POST['selected_course_id'] && $_POST['course_position']) {

		check_ajax_referer('TPRM_nonce', 'security');
		$current_course_id = sanitize_text_field($_POST['current_course_id']);
		$selected_course_id = sanitize_text_field($_POST['selected_course_id']);
		$group_id = sanitize_text_field($_POST['ld_group_id']);
		$classe_name = bp_get_current_group_name();
		$current_course_name = get_the_title($current_course_id);
		$selected_course_name = get_the_title($selected_course_id);
		$course_position = isset($_POST['course_position']) ? intval($_POST['course_position']) : 0;

		// Remove current course from the group
		delete_post_meta($current_course_id, 'learndash_group_enrolled_' . $group_id);

		// Add selected course to the group
		update_post_meta($selected_course_id, 'learndash_group_enrolled_' . $group_id, time());

		// Update the course position meta data /* very important, if we did not update the position; the course will disapear from the group */
		update_post_meta($selected_course_id, 'course_position_in_group_' . $group_id, $course_position);

		$success_msg = sprintf(__('<strong>%s</strong> has been successfully replaced by <strong>%s</strong> in <ins>%s</ins> Classroom', 'tprm-theme'), $current_course_name, $selected_course_name, $classe_name);

		echo json_encode(array(
			'success_msg' => $success_msg,
		));

		wp_die();
    }
 
}

add_action('wp_ajax_assign_course_to_group', 'TPRM_ajax_assign_course_to_group');
 
function TPRM_ajax_assign_course_to_group(){
 
    if (isset($_POST['payload']) && $_POST['payload'] == 'assign_course_to_group' && $_POST['ld_group_id'] && $_POST['course_id']) {

		check_ajax_referer('assign_course_to_group_nonce', 'security');
		$selected_course_id = intval($_POST['course_id']);
		$group_id = intval($_POST['ld_group_id']);
		$classe_name = get_the_title($group_id);
		$selected_course_name = get_the_title($selected_course_id);

		// Add selected course to the group
		update_post_meta($selected_course_id, 'learndash_group_enrolled_' . $group_id, time());

		wp_update_post(array(
			'ID' => $group_id
		));

		$success_msg = sprintf(__('<strong>%s</strong> has been successfully assigned to <strong>%s</strong> Group', 'tprm-theme'), $selected_course_name, $classe_name);

		echo json_encode(array(
			'success_msg' => $success_msg,
			'selected_course_id' => $selected_course_id,
			'group_id' => $group_id,
		));

		wp_die();
    }
 
}

 //TODO : V3

/* show courses per year */

//add_action( 'bp_setup_nav', 'add_course_years_tabs', 100 );

function add_course_years_tabs() {
    $user_id  = bp_displayed_user_id();
    $user_groups = learndash_get_users_group_ids($user_id);

    foreach($user_groups as $user_group){
        $bp_group_id = get_post_meta($user_group, '_sync_group_id', true);
        $year = groups_get_groupmeta($bp_group_id, 'ecole_year');

        bp_core_new_subnav_item(array(
            'name'              => $year,
            'slug'              => $year,
            'parent_url'        => trailingslashit(bp_displayed_user_domain() . 'my-course'),
            'parent_slug'       => 'my-course',
            'screen_function'   => 'display_courses_for_year',
            'position'          => 100,
            'user_has_access'   => bp_is_my_profile(),
            'item_css_id'       => 'year-' . $year
        ));
    }
}

function display_courses_for_year() {
    add_action('bp_template_content', 'display_courses_content_for_year');
    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function display_courses_content_for_year() {
    $current_year = bp_current_action(); // Get the current year from the URL
    $user_id = bp_displayed_user_id();
    $user_groups = learndash_get_users_group_ids($user_id);

    echo '<h2>' . $current_year . ' Courses</h2>';

    // Loop through user groups to get courses for the current year
    foreach ($user_groups as $user_group) {
        $bp_group_id = get_post_meta($user_group, '_sync_group_id', true);
        $year = groups_get_groupmeta($bp_group_id, 'ecole_year');

        if ($year == $current_year) {
            $courses = learndash_get_group_courses_list($user_group);
            if ($courses) { ?>
               <ul class="bb-course-list bb-course-items <?php echo esc_attr( $class_grid_show . $class_list_show ); ?>" aria-live="assertive" aria-relevant="all">
			   <?php 
				foreach ( $courses as $course_id ) :
					$course = get_post( $course_id );
					$post   = $course;
					get_template_part( 'learndash/ld30/template-course-item' );
				endforeach;          
                echo '</ul>';
            } else {
                echo '<p>No courses found for ' . $current_year . '</p>';
            }
        }
    }
}


function this_year_group(){
	$user_groups = learndash_get_users_group_ids(bp_displayed_user_id());
	$this_year = get_option('school_year');

	foreach ($user_groups as $user_group) {
		$bp_group_id = get_post_meta($user_group, '_sync_group_id', true);
		$classe_year = groups_get_groupmeta($bp_group_id, 'ecole_year');
		if( $this_year == $classe_year ){
			return $user_group;
		}
	}
}


function display_courses_count( $count, $nav_item, $displayed_nav ) {

    if ( $displayed_nav == 'groups' ) {
        if ( $nav_item->slug == 'courses' ) {
            $count = true;
        }
    }

    return $count;

}
add_filter( 'bp_nouveau_nav_has_count', 'display_courses_count', 99, 3 );


function display_courses_count_int( $count, $nav_item, $displayed_nav ) {

    if ( $displayed_nav == 'groups' ) {

        if ( $nav_item->slug == 'courses' ) {

            $ld_group_id =  bp_ld_sync( 'buddypress' )->helpers->getLearndashGroupId( bp_get_current_group_id() );

            $courses = learndash_get_group_courses_list($ld_group_id);
			$count   = count( $courses );

        }

    }

    return $count;

}
add_filter( 'bp_nouveau_get_nav_count', 'display_courses_count_int', 99, 3 );

function add_custom_buddypress_tabs() {
    // Add 'Additional Content' tab
    bp_core_new_nav_item( array(
        'name'                => __( 'Additional Content', 'tprm-theme' ),
        'slug'                => 'additional-content',
        'default_subnav_slug' => 'additional-content',
        'position'            => 150,
        'screen_function'     => 'course_additional_content_screen',
        'show_for_displayed_user' => false, // Set to false if only visible to the current user
        'item_css_id'         => 'additional-content'
    ) );

    // Add 'Exam' tab
    bp_core_new_nav_item( array(
        'name'                => __( 'Exam', 'tprm-theme' ),
        'slug'                => 'exam',
        'default_subnav_slug' => 'exam',
        'position'            => 155,
        'screen_function'     => 'course_exam_screen',
        'show_for_displayed_user' => false,
        'item_css_id'         => 'exam'
    ) );
}
add_action( 'bp_setup_nav', 'add_custom_buddypress_tabs', 100 );

function course_additional_content_screen() {
    add_action( 'bp_template_content', 'course_additional_content_screen_content' );
    bp_core_load_template( 'buddypress/members/single/plugins' );
}

function course_exam_screen() {
	require_once 'exam.php';
    add_action( 'bp_template_content', 'course_exam_screen_content' );
    bp_core_load_template( 'buddypress/members/single/plugins' );
}

function course_additional_content_screen_content() {
    echo '<h2>' . __( 'No Additional Content Available for you', 'tprm-theme' ) . '</h2>';

}