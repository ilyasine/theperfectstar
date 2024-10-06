<?php 

add_filter('site_transient_update_plugins', 'remove_update_notifications');
add_filter('all_plugins', 'TPRM_hide_plugins_network');
add_action('admin_bar_menu', 'TPRM_theme_add_admin_bar_link', 999);
add_filter('show_admin_bar', 'show_admin_bar_to_TPRM_admin',99,1);

/**
 * Remove plugin update notifications
 *
 * @since V2
 * @param object value of an existing site transient 
 * @return object
 */

function remove_update_notifications( $value ) {

    if ( isset( $value ) && is_object( $value ) ) {   
  
      unset( $value->response[ 'learndash-quiz-import-export/learndash-quiz-import-export.php' ] );
      unset( $value->response[ 'sfwd-lms/sfwd_lms.php' ] );
      unset( $value->response[ 'memberpress/memberpress.php' ] );
      unset( $value->response[ 'elementor-pro/elementor-pro.php' ] );
      unset( $value->response[ 'fluent-support-pro/fluent-support-pro.php' ] ); 
      unset( $value->response[ 'perfmatters/perfmatters.php' ] ); 
    }
  
    return $value;
}
  
/**
 * Hide plugin from the network admin area
 *
 * @since V2
 * @param array of main plugins file
 * @return array
 */

function TPRM_hide_plugins_network( $plugins ) {
  
	/* woocmmerce start */
	if( in_array( 'woocommerce-multilingual/wpml-woocommerce.php', array_keys( $plugins ) ) ) {
		unset( $plugins['woocommerce-multilingual/wpml-woocommerce.php'] );
	}
	if( in_array( 'CS-VPS/CS-VPS.php', array_keys( $plugins ) ) ) {
		unset( $plugins['CS-VPS/CS-VPS.php'] );
	}
	/* hide activators */
	if( in_array( 'wordfence-activator/main.php', array_keys( $plugins ) ) ) {
		unset( $plugins['wordfence-activator/main.php'] );
	}
	/* plugin organizer */
	if( function_exists('is_yskwf') && ! is_yskwf() && in_array( 'plugin-organizer/plugin-organizer.php', array_keys( $plugins ) ) ) {
		unset( $plugins['plugin-organizer/plugin-organizer.php'] );
	}

	return $plugins;
}

/**
 * Add admin and dashboard bar link 
 *
 * @since V2
 * @param array of main plugins file
 */

function TPRM_theme_add_admin_bar_link($wp_admin_bar) {
	// Check if the user has the manage_options capability
	if (!current_user_can('manage_options')) {
	  return;
	}
  
	// Check if the user is in the admin area
	if (is_admin()) {
	  // Use the dashboard page URL and title
	  $page_url = home_url('dashboard');
	  $page_title = __('Dashboard', 'tprm-theme');
	} else {
	  // Use the wp-admin URL and title
	  $page_url = admin_url();
	  $page_title = __('Admin', 'tprm-theme');
	}
  
	// Add a new node to the admin bar
	$wp_admin_bar->add_node(array(
	  'id' => 'tprm-theme-dashboard', // Node ID
	  'title' => $page_title, // Node title
	  'href' => $page_url, // Node URL
	  'meta' => array(
		'class' => 'tprm-theme-dashboard', // Node class
	  )
	));
}

/**
 * Show admin bar to users with 'kwf-admin' role
 *
 * @since V2
 * @param bool $show Current state of the admin bar visibility.
 * @return bool Updated state of the admin bar visibility.
 */
function show_admin_bar_to_TPRM_admin($show) {
    if (current_user_can('kwf-admin')) {
        return true;
    }
    return $show;
}

//callback to print the school year input field
function school_year_field_callback(){
	echo '<input type="text" id="school_year" placeholder="'. __('School Year', 'tprm-theme'). '" name="school_year" value="'. get_option('school_year') .'" />';
}

function add_school_year_section_to_settings(){

    //register setting to save the data
	register_setting( 'general', 'school_year' );

	//add a school year field to this section.
	add_settings_field(
		'school_year',
		__('School Year', 'tprm-theme'),
		'school_year_field_callback',
		'general',
		'default'
	);

}
add_action('admin_init', 'add_school_year_section_to_settings');

function add_inline_script_to_common_js() {
    // Check if the script with ID 'common-js' is registered

	if ( wp_script_is( 'common', 'enqueued' )) {
		// Inline JavaScript content
		$inline_js = '
			document.addEventListener(\'DOMContentLoaded\', function() {
				const urlParams = new URLSearchParams(window.location.search);
				const schoolYearParam = urlParams.get(\'school_year\');
				if (schoolYearParam !== null) {
					const schoolYearInput = document.querySelector(\'#school_year\');
					if (schoolYearInput !== null) {
						schoolYearInput.focus();
					}
				}
			});
		';
		
		// Add the inline JavaScript to the registered script
		wp_add_inline_script( 'common', $inline_js );
	}
}
add_action( 'admin_enqueue_scripts', 'add_inline_script_to_common_js', 99 );

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}
}