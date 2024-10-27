<?php 

add_filter('the_title', 'TPRM_suppress_title', 10, 2 );
add_action('load-post.php', 'TPRM_post_meta_boxes_setup' );
add_action('load-post-new.php', 'TPRM_post_meta_boxes_setup' );


/**
 * Filter the title and return empty string if necessary.
 *
 * @param $title string The old title
 * @param int $post_id The post ID
 *
 * @return string Old title or empty string.
 */
function TPRM_suppress_title( $title, $post_id = 0 ) {
	if ( ! $post_id ) {
		return $title;
	}

	$hide_title = get_post_meta( $post_id, 'TPRM_hide_title', true );
	if ( ! is_admin() && is_singular() && intval( $hide_title ) && in_the_loop() ) {
		return '';
	}

	return $title;
}

function TPRM_post_meta_boxes_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'TPRM_add_post_meta_boxes' );

	/* Save post meta on the 'save_post' hook. */
	add_action( 'save_post', 'TPRM_save_meta', 10, 2 );
}

function TPRM_add_post_meta_boxes() {
	add_meta_box(
		'tprm-hide-title',
		esc_html__( 'Hide Title ?', 'tprm-theme' ),
		'TPRM_render_metabox',
		null,
		'side',
		'core'
	);
}

function TPRM_render_metabox( $post ) {
	$curr_value = get_post_meta( $post->ID, 'TPRM_hide_title', true );
	wp_nonce_field( basename( __FILE__ ), 'TPRM_meta_nonce' );
	?>
	<input type="hidden" name="tprm-hide-title-checkbox" value="0"/>
	<input type="checkbox" name="tprm-hide-title-checkbox" id="tprm-hide-title-checkbox"
	       value="1" <?php checked( $curr_value, '1' ); ?> />
	<label for="tprm-hide-title-checkbox"><?php esc_html_e( 'Hide the title for this item', 'tprm-theme' ); ?></label>
	<?php
}

function TPRM_save_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( ! isset( $_POST['TPRM_meta_nonce'] ) || ! wp_verify_nonce( $_POST['TPRM_meta_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return;
	}

	/* Get the posted data and sanitize it for use as an HTML class. */
	$form_data = ( isset( $_POST['tprm-hide-title-checkbox'] ) ? $_POST['tprm-hide-title-checkbox'] : '0' );
	update_post_meta( $post_id, 'TPRM_hide_title', $form_data );
}

function TPRM_nav_menu($items, $args) {
    // Check if the current menu being displayed is the primary menu
    if ($args->theme_location == 'buddypanel-loggedin' && is_user_logged_in()) {
        $manager_main_page = bp_get_group_permalink(groups_get_group(get_last_user_school()));
        $student_main_page = home_url('/members/me/my-course/');
        $student_additional_content_page = home_url('/members/me/additional-content/');
        $student_exam_page = home_url('/members/me/exam/');
        $admin_tutorial = esc_url(home_url('/my-course/support-for-the-administrator/'));
        $teacher_tutorial = esc_url(home_url('/my-course/teacher-support/'));
        
        // Global Links (common for all roles)
        $links = [];

        // Role-Specific Links
        if (is_tprm_admin()) {
            $links = [
                'dashboard' => [
                    'url' => esc_url(home_url('/dashboard/')),
                    'icon' => 'bb-icon-home',
                    'label' => __('Admin', 'tprm-theme'),
                    'is_current' => is_page('dashboard'),
                ],
                'schools' => [
                    'url' => esc_url(home_url('/schools/')),
                    'icon' => 'bb-icon-chart-area',
                    'label' => __('Schools', 'tprm-theme'),
                    'is_current' => is_page('schools'),
                ],
                'reporting' => [
                    'url' => esc_url(home_url('/reports/')),
                    'icon' => 'bb-icon-database',
                    'label' => __('Reporting', 'tprm-theme'),
                    'is_current' => is_reporting(),
                ],
            ];
        }

        if (is_school_principal() || is_school_leader()) {
            $links['classrooms'] = [
                'url' => esc_url($manager_main_page),
                'icon' => 'bb-icon-users',
                'label' => __('Manage Classrooms', 'tprm-theme'),
                'is_current' => bp_is_group_subgroups(),
            ];
            $links['teachers'] = [
                'url' => esc_url($manager_main_page . '/teachers/'),
                'icon' => 'bb-icon-user-friends',
                'label' => __('Manage Teachers', 'tprm-theme'),
                'is_current' => is_teachers_page(),
            ];
            $links['students'] = [
                'url' => esc_url($manager_main_page . '/students/'),
                'icon' => 'bb-icon-users',
                'label' => __('Manage Students', 'tprm-theme'),
                'is_current' => is_students_page(),
            ];
            $links['reporting'] = [ 
                'url' => esc_url(home_url('/reports/')),
                'icon' => 'bb-icon-database',
                'label' => __('Reporting', 'tprm-theme'),
                'is_current' => is_reporting(),
            ];
        }

        if (is_teacher()) {
            $links['classrooms'] = [
                'url' => esc_url($manager_main_page),
                'icon' => 'bb-icon-users',
                'label' => __('Classrooms', 'tprm-theme'),
                'is_current' => bp_is_group_subgroups(),
            ];
            $links['reporting'] = [ 
                'url' => esc_url(home_url('/reports/')),
                'icon' => 'bb-icon-database',
                'label' => __('Reporting', 'tprm-theme'),
                'is_current' => is_reporting(),
            ];
        }

		if (is_student()) {
            if (!is_active_student(get_current_user_id())) {
                $links['subscription'] = [
                    'url' => esc_url(home_url('/subscription/')),
                    'icon' => 'bb-icon-user-check',
                    'label' => __('Subscription', 'tprm-theme'),
                    'is_current' => is_subscription(),
                ];
            } else {
                $links['courses'] = [
                    'url' => esc_url($student_main_page),
                    'icon' => 'bb-icon-book-open',
                    'label' => __('Courses', 'tprm-theme'),
                    'is_current' => is_courses_page(),
                ];
                $links['additional-content'] = [
                    'url' => esc_url($student_additional_content_page),
                    'icon' => 'bb-icon-book',
                    'label' => __( 'Additional Content', 'tprm-theme' ),
                    'is_current' => is_additional_content_page(),
                ];
                $links['exam'] = [
                    'url' => esc_url($student_exam_page),
                    'icon' => 'bb-icon-award',
                    'label' => __('Exam', 'tprm-theme'),
                    'is_current' => is_exam_page(),
                ];
                $links['student_textbook'] = [
                    'url' => esc_url(home_url('/student-textbook/')),
                    'icon' => 'bb-icon-address-book',
                    'label' => __('Student Textbook', 'tprm-theme'),
                    'is_current' => is_page('student-textbook'),
                ];
                $links['reporting'] = [ 
                    'url' => esc_url(home_url('/reports/')),
                    'icon' => 'bb-icon-database',
                    'label' => __('Reporting', 'tprm-theme'),
                    'is_current' => is_reporting(),
                ];
            }
        }

        if (is_library()) {
            $links['library'] = [
                'url' => home_url('/library/'),
                'icon' => 'bb-icon-membership-card',
                'label' => __('Licenses Dashboard', 'tprm-theme'),
                'is_current' => is_library_page(),
            ];
        }

        if (is_libraries_manager()) {
            $links['library_manager'] = [
                'url' => home_url('/libraries-dashboard/'),
                'icon' => 'bb-icon-books',
                'label' => __('Libraries Dashboard', 'tprm-theme'),
                'is_current' => is_library_dashboard_page(),
            ];
            $links['manage-libraries'] = [
                'url' => home_url('/manage-libraries/'),
                'icon' => 'bb-icon-books',
                'label' => __('Manage Libraries', 'tprm-theme'),
                'is_current' => is_library_manage_page(),
            ];
        }

        // Add the Support
        if (! is_student()) {
            $links['support'] = [
                'url' => esc_url(home_url('/support/')),
                'icon' => 'bb-icon-question',
                'label' => __('Help', 'tprm-theme'),
                'is_current' => is_page('support'),
            ];
        }

        // Generate menu items HTML for all links except logout
        foreach ($links as $key => $link) {
            // Add current-menu-item class if is_current is true
            $active_class = $link['is_current'] ? ' current-menu-item' : '';         
            $items .= '<li class="menu-item' . $active_class . ' ' . $key . '"><a href="' . $link['url'] . '" class="bb-menu-item"><i class="_mi _before bb-icon-bl buddyboss ' . $link['icon'] . '"></i><span>' . $link['label'] . '</span></a></li>';
        }

        // Add the logout link at the end
        $logout_link = '<li class="menu-item logout-last"><a href="' . wp_logout_url() . '" class="bb-menu-item"><i class="_mi _before bb-icon-bl buddyboss bb-icon-sign-out"></i><span>' . __('Logout', 'tprm-theme') . '</span></a></li>';
        $items .= $logout_link;
    }

    return $items;
}

add_filter('wp_nav_menu_items', 'TPRM_nav_menu', 10, 2);



