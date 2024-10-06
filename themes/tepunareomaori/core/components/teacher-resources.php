<?php 


/**
 * LearnDash Settings Metabox for Lesson Resources.
 *
 * @since 3.0.0
 * @package tepunareomaori\Core\Components
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function get_lesson_setting($post_id, $meta_key) {
    if (isset($post_id)) {
        // Get the lesson meta
        $lesson_meta = get_post_meta($post_id, '_sfwd-lessons', true);

        // Check if the specific meta key exists in the retrieved metadata
        if (isset($lesson_meta['sfwd-lessons_' . $meta_key])) {
            return $lesson_meta['sfwd-lessons_' . $meta_key];
        } else {
            // Optionally, log the missing key or return a default value
            error_log("Meta key 'sfwd-lessons_$meta_key' not found for post ID $post_id.");
            return null; // Or return a default value if needed
        }
    }
    return null; // Return null if $post_id is not set
}


function is_ressource_enabled($post, $meta_key) {
    // Get the meta value as an array
    $meta_values = get_post_meta($post->ID, '_' . $post->post_type, true);

    // Ensure that $meta_values is an array
    if (is_array($meta_values) && isset($meta_values[$post->post_type . '_' . $meta_key . '_enabled'])) {
        $meta_value = $meta_values[$post->post_type . '_' . $meta_key . '_enabled'];

        // Check if the meta value is 'on'
        if ($meta_value === 'on') {
            return true;
        }
    }

    // Return false if conditions are not met
    return false;
}
//customize
function add_attributes_to_iframes($content) {
    // Utiliser une expression régulière pour trouver tous les iframes
    $pattern = '/<iframe(.*?)>/i';
    
    // Fonction de remplacement pour ajouter les attributs
    $replacement = function($matches) {
        $iframe = $matches[0];
        $attributes = ' allowfullscreen="true" frameborder="0" mozallowfullscreen="true" webkitallowfullscreen="true" loading="lazy"';
        
        // Vérifie si l'iframe se termine par />
        if (substr(trim($iframe), -2) === '/>') {
            $iframe = substr(trim($iframe), 0, -2) . $attributes . ' />';
        } else {
            $iframe = substr(trim($iframe), 0, -1) . $attributes . '>';
        }
        
        return $iframe;
    };
    
    // Appliquer le remplacement
    return preg_replace_callback($pattern, $replacement, $content);
}

function tab_content($post, $meta_key) {
    // Get the meta value as an array
    $meta_values = get_post_meta($post->ID, '_' . $post->post_type, true);

    // Initialize content to an empty string
    $content = '';

    // Check if $meta_values is an array and contains the required key
    if (is_array($meta_values) && isset($meta_values[$post->post_type . '_' . $meta_key])) {
        $content = wp_specialchars_decode(strval($meta_values[$post->post_type . '_' . $meta_key]), ENT_QUOTES);
    }

    // Process content if it's not empty
    if (!empty($content)) {
        $content = do_shortcode($content);
        $content = wpautop($content);
		// Ajouter les attributs aux iframes existants
        $content = add_attributes_to_iframes($content);
    }

    return $content;
}



//TODO V3


function resources_learndash_content_tabs($tabs, $context, $course_id, $user_id) {
	global $post;

	// Display tabs only on lesson page
	if( $context == 'lesson' ) :
		// Add slides tab
		$tabs[] = array(
			'id'        => 'slides',
			'icon'      => 'bb-icon-airplay', 
			'label'     => __('Slides', 'tprm-theme'),
			'content'   => tab_content($post, 'slides'),
			'condition' => is_ressource_enabled($post, 'slides') && ( is_teacher() || is_TPRM_admin() ), 
		);

		// Add script tab
		$tabs[] = array(
			'id'        => 'script',
			'icon'      => 'bb-icon-article', 
			'label'     => __('Script', 'tprm-theme'),
			'content'   => tab_content($post, 'script'),
			'condition' => is_ressource_enabled($post, 'script') && ( is_teacher() || is_TPRM_admin() ), 
		);

		// Add video tab
		$tabs[] = array(
			'id'        => 'video',
			'icon'      => 'bb-icon-brand-youtube', 
			'label'     => __('Video', 'tprm-theme'),
			'content'   => tab_content($post, 'video'),
			'condition' => is_ressource_enabled($post, 'video') && ( is_teacher() || is_TPRM_admin() ), 
		);

		// Add resources tab
		$tabs[] = array(
			'id'        => 'resources',
			'icon'      => 'bb-icon-file-bookmark', 
			'label'     => __('Resources', 'tprm-theme'),
			'content'   => tab_content($post, 'resources'),
			'condition' => is_ressource_enabled($post, 'resources') && ( is_teacher() || is_TPRM_admin() ), 
		);

		// Add quiz tab
		$tabs[] = array(
			'id'        => 'quiz',
			'icon'      => 'bb-icon-quiz', 
			'label'     => __('Quiz', 'tprm-theme'),
			'content'   => tab_content($post, 'quiz'),
			'condition' => is_ressource_enabled($post, 'quiz') && ( is_teacher() || is_TPRM_admin() ), 
		);

		$is_teacher_or_admin = is_teacher() || is_TPRM_admin();

		// If the user is not a teacher or admin, remove the 'materials' tab
		if (!$is_teacher_or_admin) {
			foreach ($tabs as $index => $tab) {
				if ($tab['id'] === 'materials') {
					unset($tabs[$index]);
					break; // Break the loop once the tab is removed
				}
			}
		}
	endif;

	return $tabs;
	
}


add_filter('learndash_content_tabs', 'resources_learndash_content_tabs', 10, 4);


add_filter('learndash_metabox_save_fields', 'save_metabox_resources_values', 10, 3);

function save_metabox_resources_values($settings_field_updates, $settings_metabox_key, $settings_screen_id) {
    // Check if the metabox key and screen ID match the lesson screen ID
    if ($settings_metabox_key != 'learndash-lesson-display-content-settings' || 
        $settings_screen_id != 'sfwd-lessons' ||
        true != wp_nonce_field($settings_metabox_key, $settings_metabox_key . '[nonce]')) {
        return $settings_field_updates; // If not, return early
    }

    // Define an array of resource-related field keys
    $resource_field_keys = array(
        'slides_enabled',
        'slides',
        'script_enabled',
        'script',
        'video_enabled',
        'video',
        'resources_enabled',
        'resources',
        'quiz_enabled',
        'quiz',
    );

    // Iterate over the resource field keys and update the corresponding values
    foreach ($resource_field_keys as $field_key) {
        if (isset($_POST[$settings_metabox_key][$field_key])) {
            $settings_field_updates[$field_key] = $_POST[$settings_metabox_key][$field_key];
        }else {
			// If the checkbox is not set in the $_POST array, set the value to an empty string
			$settings_field_updates[$field_key] = '';
		}
    }

    return $settings_field_updates;
}


add_filter( 'learndash_settings_fields', 'resources_learndash_settings_fields', 10, 2 );


function resources_learndash_settings_fields( $fields, $settings_metabox_key ) {

	global $setting_resources_values, $settings_field_updates, $post_id;

	if ( 'learndash-lesson-display-content-settings' == $settings_metabox_key ) {
		
		$resources = array(

			/* slides */
			'teacher_resources'      => array(
				'name'                => 'teacher_resources',
				'type'                => 'custom',
				'label'               => esc_html__( 'Teacher Resources', 'tprm-theme' ),		
				'help_text'      => sprintf(
					// translators: placeholder: lesson, lesson.
					esc_html_x( 'Display Teacher Resources for this %1$s. This is visible only to the teacher having access to the %2$s.', 'placeholder: lesson, lesson', 'tprm-theme' ),
					learndash_get_custom_label_lower( 'lesson' ),
					learndash_get_custom_label_lower( 'lesson' )
				),
				
			),

			/* slides */
			'slides_enabled'      => array(
				'name'                => 'slides_enabled',
				'type'                => 'checkbox-switch',
				'label'               => esc_html__( 'Slides', 'tprm-theme' ),
				'help_text'           => sprintf(
					// translators: placeholder: lesson, lesson.
					esc_html_x( 'Display slides for this %1$s. This is visible only to the teacher.', 'placeholder: lesson', 'tprm-theme' ),
					learndash_get_custom_label_lower( 'lesson' )
				),
				'value'               => get_lesson_setting($post_id, 'slides_enabled') ?? '',
				'default'             => '',
				'options'             => array(
					'on' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Slides are enabled for this %s', 'placeholder: Lesson', 'tprm-theme' ),
						learndash_get_custom_label( 'lesson' )
					),
					''   => '',
				),
				'child_section_state' => ( 'on' === get_lesson_setting($post_id, 'slides_enabled') ) ? 'open' : 'closed',
			),
			'slides'  => array(
				'name'           => 'slides',
				'type'           => 'wpeditor',
				'parent_setting' => 'slides_enabled',
				'value'          =>  get_lesson_setting($post_id, 'slides'),
				//'value'          =>  learndash_get_setting($post_id)['slides'],
				'default'        => '',
				'placeholder'    => esc_html__( 'Add a list of needed documents or URLs. This field supports HTML.', 'tprm-theme' ),
				'editor_args'    => array(
					'textarea_name' => $settings_metabox_key.'[slides]',
					'textarea_rows' => 3,
				),
			),
			/* script */
			'script_enabled'      => array(
				'name'                => 'script_enabled',
				'type'                => 'checkbox-switch',
				'label'          	  => esc_html__( 'Script', 'tprm-theme' ),			
				'value'               => get_lesson_setting($post_id, 'script_enabled'),
				'help_text'           => sprintf(
					// translators: placeholder: lesson, lesson.
					esc_html_x( 'Display the script for this %1$s. This is visible only to the teacher.', 'placeholder: lesson', 'tprm-theme' ),
					learndash_get_custom_label_lower( 'lesson' )
				),
				'default'             => '',
				'options'             => array(
					'on' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Script is enabled for this %s', 'placeholder: Lesson', 'tprm-theme' ),
						learndash_get_custom_label( 'lesson' )
					),
					''   => '',
				),
				'child_section_state' => ( 'on' === get_lesson_setting($post_id, 'script_enabled') ) ? 'open' : 'closed',
			),
			'script'  => array(
				'name'           => 'script',
				'type'           => 'wpeditor',
				'parent_setting' => 'script_enabled',
				'value'          => get_lesson_setting($post_id, 'script'),
				'default'        => '',		
				'placeholder'    => esc_html__( 'Add a list of needed documents or URLs. This field supports HTML.', 'tprm-theme' ),
				'editor_args'    => array(
					'textarea_name' => $settings_metabox_key.'[script]',
					'textarea_rows' => 3,
				),
			),
			/* video */
			'video_enabled'           => array(
				'name'                => 'video_enabled',
				'type'                => 'checkbox-switch',
				'label'          	  => esc_html__( 'Video', 'tprm-theme' ),
				'help_text'           => sprintf(
					// translators: placeholder: lesson, lesson.
					esc_html_x( 'Display the video for this %1$s. This is visible only to the teacher.', 'placeholder: lesson', 'tprm-theme' ),
					learndash_get_custom_label_lower( 'lesson' )
				),
				'value'               => get_lesson_setting($post_id, 'video_enabled'),
				'default'             => '',
				'options'             => array(
					'on' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Video is enabled for this %s', 'placeholder: Lesson', 'tprm-theme' ),
						learndash_get_custom_label( 'lesson' )
					),
					''   => '',
				),
				'child_section_state' => ( 'on' === get_lesson_setting($post_id, 'video_enabled') ) ? 'open' : 'closed',
			),
			/* get_post_meta($post_id, '_sfwd-lessons')[0]['sfwd-lessons_video'],  */
			'video'  => array(
				'name'           => 'video',
				'type'           => 'wpeditor',
				'parent_setting' => 'video_enabled',
				'value'          => get_lesson_setting($post_id, 'video'), 
				'default'        => '',		
				'placeholder'    => esc_html__( 'Add a list of needed documents or URLs. This field supports HTML.', 'tprm-theme' ),
				'editor_args'    => array(
					'textarea_name' => $settings_metabox_key.'[video]',
					'textarea_rows' => 3,
				),
			),
			/* resources */
			'resources_enabled'  => array(
				'name'           => 'resources_enabled',
				'type'           => 'checkbox-switch',
				'value'          => get_lesson_setting($post_id, 'resources_enabled'),
				'default'        => '',
				'label'          => esc_html__( 'Resources', 'tprm-theme' ),
				'help_text'           => sprintf(
					// translators: placeholder: lesson, lesson.
					esc_html_x( 'Display the resources for this %1$s. This is visible only to the teacher.', 'placeholder: lesson', 'tprm-theme' ),
					learndash_get_custom_label_lower( 'lesson' )
				),
				'options'             => array(
					'on' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Resources are enabled for this %s', 'placeholder: Lesson', 'tprm-theme' ),
						learndash_get_custom_label( 'lesson' )
					),
					''   => '',
				),
				'child_section_state' => ( 'on' === get_lesson_setting($post_id, 'resources_enabled') ) ? 'open' : 'closed',
			),
			'resources'  => array(
				'name'           => 'resources',
				'type'           => 'wpeditor',
				'parent_setting' => 'resources_enabled',
				'value'          => get_lesson_setting($post_id, 'resources'),
				'default'        => '',		
				'placeholder'    => esc_html__( 'Add a list of needed documents or URLs. This field supports HTML.', 'tprm-theme' ),
				'editor_args'    => array(
					'textarea_name' => $settings_metabox_key.'[resources]',
					'textarea_rows' => 3,
				),
			),
			/* Quiz */
			'quiz_enabled'           => array(
				'name'                => 'quiz_enabled',
				'type'                => 'checkbox-switch',
				'label'          	  => esc_html__( 'Quiz', 'tprm-theme' ),			
				'value'               => get_lesson_setting($post_id, 'quiz_enabled'),
				'default'             => '',
				'help_text'           => sprintf(
					// translators: placeholder: lesson, lesson.
					esc_html_x( 'Display the Quiz for this %1$s. This is visible only to the teacher.', 'placeholder: lesson', 'tprm-theme' ),
					learndash_get_custom_label_lower( 'lesson' )
				),
				'options'             => array(
					'on' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Quiz is enabled for this %s', 'placeholder: Lesson', 'tprm-theme' ),
						learndash_get_custom_label( 'lesson' )
					),
					''   => '',
				),
				'child_section_state' => ( 'on' === get_lesson_setting($post_id, 'quiz_enabled') ) ? 'open' : 'closed',
			),
			'quiz'  => array(
				'name'           => 'quiz',
				'type'           => 'wpeditor',
				'parent_setting' => 'quiz_enabled',
				'value'          => get_lesson_setting($post_id, 'quiz'),
				'default'        => '',			
				'placeholder'    => esc_html__( 'Add a list of needed documents or URLs. This field supports HTML.', 'tprm-theme' ),
				'editor_args'    => array(
					'textarea_name' => $settings_metabox_key.'[quiz]',
					'textarea_rows' => 3,
				),
			),
		);

		$fields = array_merge(array_slice($fields, 0, 2, true), $resources, array_slice($fields, 1, count($fields)-1, true));
	}


    return $fields;
}

