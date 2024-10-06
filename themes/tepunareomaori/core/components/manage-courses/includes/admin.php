<?php 


add_action('user_register', 'TPRM_onboarding_course_user_update');
add_action('set_user_role', 'TPRM_onboarding_course_user_update');
add_action('profile_update', 'TPRM_onboarding_course_user_update');

/**
 * Enroll teachers and admins in all onboarding courses
 *
 * @since V2
 */

function TPRM_onboarding_course_user_update($user_id) {
	// Check if the user ID is valid
	if (!empty($user_id)) {
		//Teacher :
		// First: get all onboarding courses ( for teachers )
		$teacher_onboarding_course_slugs = array('teacher-support', 'support-pour-lenseignant');

		foreach($teacher_onboarding_course_slugs as $teacher_onboarding_course_slug) {
			$onboarding_course = get_page_by_path($teacher_onboarding_course_slug, OBJECT, 'sfwd-courses');

			if ($onboarding_course) {
				$onboarding_course_id = $onboarding_course->ID;

				// Get the roles for the user being created or updated
				$user_roles = get_userdata($user_id)->roles;

				// Check if the user has any of the specified roles
				if (array_intersect($user_roles, array('administrator', 'kwf-admin', 'teacher'))) {
					ld_update_course_access($user_id, $onboarding_course_id);
				}
			}
		}
		// School Admin :
		// First: get all onboarding courses ( for school admins )
		$school_admin_onboarding_course_slugs = array('support-for-the-administrator', 'support-pour-ladministrateur');

		foreach($school_admin_onboarding_course_slugs as $school_admin_onboarding_course_slug) {
			$onboarding_course = get_page_by_path($school_admin_onboarding_course_slug, OBJECT, 'sfwd-courses');

			if ($onboarding_course) {
				$onboarding_course_id = $onboarding_course->ID;

				// Get the roles for the user being created or updated
				$user_roles = get_userdata($user_id)->roles;

				// Check if the user has any of the specified roles
				if (array_intersect($user_roles, array('administrator', 'kwf-admin', 'school-admin', 'director'))) {
					ld_update_course_access($user_id, $onboarding_course_id);
				}
			}
		}
	}
}

add_action('wp_insert_post', 'TPRM_admins_course_access', 99999, 1);
/**
 * Set admins Enrolled in all courses by default
 *
 * @since V2
 */

 //TODO : should be hooked into update or create course

 function TPRM_admins_course_access($course_id){

	$post_type = get_post_type($course_id);

	if ( 'sfwd-courses' === $post_type ) {

		$user_query = new WP_User_Query(
			array(
				'role__in' => array('administrator', 'kwf-admin'),
			)
		);

		$administrators = $user_query->get_results();

		$TPRM_admin = '';

		foreach ($administrators as $administrator) {
			$TPRM_admin = $administrator->ID;
			ld_update_course_access($TPRM_admin, $course_id);
		}
	}

}


add_action('wp_insert_post', 'TPRM_update_course_groups', 99999, 1);

/**
 *  Update course groups lists after update or publish course
 *
 * @since V2
 */
/* $kwf-green-light: #D6FCF4;
$kwf-green-hover: #7cddca; */
 function TPRM_update_course_groups($post_id) {

    $post_type = get_post_type($post_id);

    if ('sfwd-courses' === $post_type) {
        // Get all selected course categories
        $course_terms = wp_get_post_terms($post_id, 'ld_course_category');

		// Get all groups for each course category
		foreach ($course_terms as $course_term) {

			$groups = get_all_groups_of_type($course_term->slug);

			// Loop through the groups for the current course category
			foreach ($groups as $group) {
				// Get Learndash group ID from BuddyPress group ID
				$ld_group_id = bp_ld_sync('buddypress')->helpers->getLearndashGroupId($group['id']);
				
				// Add the course to the curriculum for the current group
				update_post_meta($post_id, 'learndash_group_enrolled_' . $ld_group_id, time());
			}
		}
		
    }
}


add_action('wp_insert_post', 'update_courses_position_groups', 99999, 1);

/**
 *  Update course groups lists after update or publish course
 *
 * @since V3
 */

 function update_courses_position_groups($post_id) {

    $post_type = get_post_type($post_id);

    if ('groups' === $post_type) {
		$group_id = $post_id;
		$group_courses = learndash_group_enrolled_courses( $group_id );

		foreach($group_courses as $group_course){
			$default_position = get_post_meta( $group_course, 'default_course_position_in_group' , true);
			$existing_position = get_post_meta( $group_course, 'course_position_in_group_' . $group_id, true );
			if($existing_position){
				update_post_meta( $group_course, 'course_position_in_group_' . $group_id, $existing_position );
			}else{
				update_post_meta( $group_course, 'course_position_in_group_' . $group_id, $default_position );
			}
		}
		
    }
}


function course_position_in_curriculum( $post ){
	add_meta_box( 
	  'course_position_in_curriculum', 
	  __('Default Course Position', 'tprm-theme'), 
	  'course_position_in_curriculum_callback', 
	  'sfwd-courses' ,
	  'manage-courses-metabox',
	  //'side',
	  );
  }
add_action('add_meta_boxes', 'course_position_in_curriculum' );

  /* Add a field to the metabox */

function course_position_in_curriculum_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'course_position_nonce' );
	$current_pos = get_post_meta( $post->ID, 'default_course_position_in_group' , true); 
	if( $post->post_type == 'sfwd-courses' ) :
	?>
	<p><?php _e('Enter the Default position at which you would like the course to appear in Curriculum. For exampe, course "1" will appear first, course "2" second, and so forth.', 'tprm-theme') ?> </p>
	<p><input type="number" name="course_position" value="<?php echo esc_attr($current_pos); ?>" /></p>
	<?php
	endif;
  }

  /* Save the input to post_meta_data */

  function save_course_position_in_curriculum($post_id) {
    // Check if this is an autosave or the user doesn't have permission to save the data
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check the nonce for security
    if (!isset($_POST['course_position_nonce']) || !wp_verify_nonce($_POST['course_position_nonce'], basename(__FILE__))) {
        return;
    }

    $new_position = '';

    // Fetch the associated course groups
    $course_groups = learndash_get_course_groups($post_id, true);

    if (isset($_POST['course_position'])) {
        $new_position = sanitize_text_field($_POST['course_position']);
        foreach ($course_groups as $course_group) {
            $existing_position = get_post_meta($post_id, 'course_position_in_group_' . $course_group, true);
            if ($existing_position != $new_position) {
                update_post_meta($post_id, 'course_position_in_group_' . $course_group, $new_position);
            }
        }
        // Default position
        update_post_meta($post_id, 'default_course_position_in_group', $new_position);
    }
}

// Use wp_insert_post for new courses to ensure metadata is set
add_action('wp_insert_post', 'save_course_position_in_curriculum', 10, 3);

/* Quick Edit */


add_filter('manage_sfwd-courses_posts_columns', 'courses_columns');

function courses_columns($columns) {
 
	unset($columns['categories']);
	unset($columns['author']);
	unset($columns['export_as_xls']);
	unset($columns['PO_override']);
	unset($columns['date']);

	$course_position_column = array(
		'course_position' => __('Default Position', 'tprm-theme'),
	);

	// Find the position of 'taxonomy-ld_course_category' column
	$position = array_search('taxonomy-ld_course_category', array_keys($columns));

	// Insert the custom column after 'taxonomy-ld_course_category'
	$columns = array_slice($columns, 0, $position + 1, true) +
				$course_position_column +
				array_slice($columns, $position + 1, null, true);
  
    return $columns;
}

/* Display custom post order in the post list */

function default_course_position_value( $column, $post_id ){
	if ($column == 'course_position' ){
	  echo '<p style="
	  color: #ffffff;
	  background: #00a0d2;
	  font-size: 17px;
	  font-weight: 600;
	  text-align: center;
	  border-radius: 5px;
	  line-height: 31px;
	  width: fit-content;
	  padding: 0px 16px;
	  margin: 0; ">' . get_post_meta( $post_id, 'default_course_position_in_group', true) . '</p>';
	}
  }

add_action( 'manage_posts_custom_column' , 'default_course_position_value' , 10 , 2 );


add_action('add_meta_boxes', 'change_meta_box_titles');

function change_meta_box_titles() {
    global $wp_meta_boxes; 

	//$wp_meta_boxes['sfwd-courses']['side']["core"]["categorydiv"]["title"] = __('Age', 'tprm-theme');
	$wp_meta_boxes['sfwd-courses']['side']["core"]["ld_course_categorydiv"]["title"] = __('Curriculum', 'tprm-theme');
}

function rewrite_course_curriculum( $taxonomy, $object_type, $args ) {
    if ( 'ld_course_category' == $taxonomy ) {
        remove_action( current_action(), __FUNCTION__ );

        $args['labels'] = array(
            
            'name'              => esc_html_x( 'Curriculum', 'tprm-theme'),       
            'singular_name'     => esc_html_x( 'Curriculum', 'tprm-theme' ),       
            'search_items'      => esc_html_x( 'Search Curriculums', 'tprm-theme' ),        
            'all_items'         => esc_html_x( 'All Curriculums', 'tprm-theme' ),       
            'parent_item'       => esc_html_x( 'Parent Curriculum', 'tprm-theme' ),       
            'parent_item_colon' => esc_html_x( 'Parent Curriculum:', 'tprm-theme' ),         
            'edit_item'         => esc_html_x( 'Edit Curriculum', 'tprm-theme' ),        
            'update_item'       => esc_html_x( 'Update Curriculum', 'tprm-theme' ),          
            'add_new_item'      => esc_html_x( 'Add New Curriculum', 'tprm-theme' ),          
            'new_item_name'     => esc_html_x( 'New Curriculum Name', 'tprm-theme' ),         
            'menu_name'         => esc_html_x( 'Curriculums', 'tprm-theme' ),
        );

        $args['label'] = __('Curriculum', 'tprm-theme');

        register_taxonomy( $taxonomy, $object_type, $args );
    }


}
add_action( 'registered_taxonomy_ld_course_category', 'rewrite_course_curriculum', 10, 3 );

function TPRM_create_course_taxonomies() {
    // Define taxonomies in an array for easier management
    $taxonomies = array(
        array(
            'taxonomy'          => 'age',
            'singular_name'     => __('Age', 'tprm-theme'),
            'plural_name'       => __('Ages', 'tprm-theme'),
            'rewrite_slug'      => 'age',
        ),
        array(
            'taxonomy'          => 'course_block',
            'singular_name'     => __('Block', 'tprm-theme'),
            'plural_name'       => __('Blocks', 'tprm-theme'),
            'rewrite_slug'      => 'course_block',
        ),
    );

    foreach ($taxonomies as $taxonomy_data) {
        $taxonomy = $taxonomy_data['taxonomy'];
        $singular_name = $taxonomy_data['singular_name'];
        $plural_name = $taxonomy_data['plural_name'];
        $rewrite_slug = $taxonomy_data['rewrite_slug'];

        // Check if taxonomy already exists
        if (!taxonomy_exists($taxonomy)) {
            $labels = array(
                'name'              => __($singular_name, 'tprm-theme'),
                'singular_name'     => __($singular_name, 'tprm-theme'),
                'search_items'      => __('Search ' . $plural_name, 'tprm-theme'),
                'all_items'         => __('All ' . $plural_name, 'tprm-theme'),
                'parent_item'       => __('Parent ' . $singular_name, 'tprm-theme'),
                'parent_item_colon' => __('Parent ' . $singular_name . ':', 'tprm-theme'),
                'edit_item'         => __('Edit ' . $singular_name, 'tprm-theme'),
                'update_item'       => __('Update ' . $singular_name, 'tprm-theme'),
                'add_new_item'      => __('Add New ' . $singular_name, 'tprm-theme'),
                'new_item_name'     => __('New ' . $singular_name . ' Name', 'tprm-theme'),
                'menu_name'         => __($plural_name, 'tprm-theme'),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => $rewrite_slug),
                'public'            => true,
                'show_in_rest'      => true,
                'capabilities'      => array(
                    'manage_terms' => 'manage_categories',
                    'edit_terms'   => 'edit_categories',
                    'delete_terms' => 'delete_categories',
                    'assign_terms' => 'assign_categories',
                ),
            );

            // Register taxonomy
            register_taxonomy($taxonomy, array('sfwd-courses'), $args);
        }
    }
}

// Hook into the init action and call TPRM_create_course_taxonomies when it fires
add_action('init', 'TPRM_create_course_taxonomies', 0);



/* Quic and bulk Edit */

add_action( 'bulk_edit_custom_box', 'quick_edit_custom_box' );
add_action( 'quick_edit_custom_box', 'quick_edit_custom_box' );

/**
 * Add custom field to quick edit screen.
 *
 * @param string $column_name The name of the column.
 */
 function quick_edit_custom_box( $column_name ) {
    
	switch ( $column_name ) {
        
		case 'course_position':
			if ( current_filter() === 'quick_edit_custom_box' ) {
				wp_nonce_field( 'course_position_quick_edit_nonce', 'course_position_quick_edit_nonce' );
			} else {
				wp_nonce_field( 'course_position_bulk_edit_nonce', 'course_position_bulk_edit_nonce' );
			}
			?>
			<fieldset class="inline-edit-col-left inline-edit-course_position">
				<div class="inline-edit-col column-<?php echo esc_attr( $column_name ); ?>">
					<label class="inline-edit-group">
						<?php esc_html_e( 'Default Course position', 'tprm-theme' ); ?>					
                        <input type="number" name="course_position" value="" />
					</label>
				</div>
			</fieldset>
			<?php
			break;
	}
}

add_action('wp_ajax_get_quick_edit_custom_box', 'get_quick_edit_custom_box_callback');

function get_quick_edit_custom_box_callback() {
    // Check nonce
    check_ajax_referer('manage_courses_bulk_edit_nonce', '_wpnonce');

    // Get the post ID from the AJAX request
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    // Return the value of the meta directly into the input field
    if ($post_id > 0) {
        $current_pos = get_post_meta($post_id, 'default_course_position_in_group', true); 
        echo $current_pos;
    }

    wp_die();
}

add_action( 'wp_ajax_course_position_save_bulk_edit', 'course_position_save_bulk_edit' );

/**
 * Save bulk edit data.
 */
function course_position_save_bulk_edit() {
	// Security check.
	check_ajax_referer( 'manage_courses_bulk_edit_nonce', 'course_position_bulk_edit_nonce' );

	// Get the post IDs.
	$course_ids = isset( $_POST['course_ids'] ) ? wp_parse_id_list( wp_unslash( $_POST['course_ids'] ) ) : array();

	foreach ( $course_ids as $course_id ) {
		$course_groups = learndash_get_course_groups($course_id, true);	
            if ( isset( $_POST['course_position'] ) ) {
                foreach ($course_groups as $course_group) { 
                    $existing_position = get_post_meta( $course_id, 'course_position_in_group_' . $course_group, true );
                    $new_position = sanitize_text_field( $_POST['course_position'] );
                    if ( $existing_position != $new_position ) {
                        update_post_meta( $course_id, 'course_position_in_group_' . $course_group, $new_position );
                    }
           		}
				// Default position
				update_post_meta( $course_id, 'default_course_position_in_group', $new_position );
            }
	}

	wp_send_json_success();
}


// Remove the metabox from the parent theme
function remove_parent_metabox() {
    remove_meta_box('post_price_box', 'sfwd-courses', 'normal');
	remove_meta_box( 'postexcerpt', null, 'normal' );
}
add_action('add_meta_boxes', 'remove_parent_metabox', 33);


// Metabox content for the child theme
function course_video_preview_meta_box_callback($post) { ?>
	<div class="sfwd sfwd_options sfwd-courses_settings">
	<div class="sfwd_input">
		<span class="sfwd_option_label">
			<a class="sfwd_help_text_link" style="cursor:pointer;"
			   title="<?php _e( 'Click for Help!', 'buddyboss-theme' ) ?>"
			   onclick="toggleVisibility('sfwd-courses_course_video_url_tip');">
				<img alt="" src="<?php echo get_template_directory_uri(); ?>/assets/images/question.png"/>
				<label for="buddyboss_lms_course_video"
					   class="sfwd_label buddyboss_lms_course_video_label"><?php echo __( 'Preview Video URL', 'buddyboss-theme' ); ?></label>
			</a>
		</span>
		<span class="sfwd_option_input">
			<div class="sfwd_option_div">
				<?php
				// Add a nonce field so we can check for it later.
				wp_nonce_field( 'buddyboss_lms_course_video_meta_box', 'buddyboss_lms_course_video_meta_box_nonce' );

				$value = get_post_meta( $post->ID, '_buddyboss_lms_course_video', true );
				echo '<textarea id="buddyboss_lms_course_video" name="buddyboss_lms_course_video" rows="2" style="width:100%;">' . esc_attr( $value ) . '</textarea>';
				?>
			</div>
			<div class="sfwd_help_text_div" style="display:none"
				 id="sfwd-courses_course_video_url_tip">
				 <label class="sfwd_help_text"><?php echo __( 'Enter preview video URL for the course. The video will be added on single course price box.', 'buddyboss-theme' ); ?></label></div>
		</span>
		<p style="clear:left"></p>
	</div>
</div>
<?php
}


function create_manage_courses_metabox_context( $post ) {

	if( get_current_screen()->id == 'sfwd-courses' ){
		do_meta_boxes(get_current_screen(), 'manage-courses-metabox', $post );

		add_action('add_meta_boxes', 'course_preview_meta_box');
	}
    
}
add_action( 'edit_form_after_title', 'create_manage_courses_metabox_context' );
add_action( 'load-post-new.php', 'create_manage_courses_metabox_context' );
add_action( 'load-post.php', 'create_manage_courses_metabox_context' );

function course_preview_meta_box() {

	add_meta_box(
		'course_video_preview',
		__('Course Video Preview', 'buddyboss-theme'), // Title
		'course_video_preview_meta_box_callback',
		'sfwd-courses',
		'manage-courses-metabox',	// a new context
	);

	add_meta_box(
		'course_description_preview',
		__('Course Short Description', 'buddyboss-theme'), // Title
		'course_short_description_output_callback',
		'sfwd-courses',
		'manage-courses-metabox',	// a new context
	);

}
//add_action('add_meta_boxes', 'course_video_preview_meta_box');

function course_short_description_output_callback( $post ) {
	$settings = [
		'textarea_name' => 'excerpt',
		'quicktags'     => [ 'buttons' => 'em,strong,link' ],
		'tinymce'       => [
			'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
			'theme_advanced_buttons2' => '',
		],
		'editor_css'    => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
	];

	wp_editor( htmlspecialchars_decode( $post->post_excerpt, ENT_QUOTES ), 'excerpt', $settings );
}



add_action('save_post', 'save_course_video_data');

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function save_course_video_data( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}
	$post_type = get_post_type( $post_id );

	if( trim( $post_type ) == 'sfwd-courses' && isset($_POST['buddyboss_lms_course_video'])) {
		// Sanitize user input.
		$data = sanitize_text_field( $_POST['buddyboss_lms_course_video'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_buddyboss_lms_course_video', $data );
	}
	
}