<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Learndash_Course_Import_Export
 * @subpackage Learndash_Course_Import_Export/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Learndash_Course_Import_Export
 * @subpackage Learndash_Course_Import_Export/admin
 * @author     WooNinjas <info@wooninjas.com>
 */
class Learndash_Course_Import_Export_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $license;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      string    $license    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $license = null ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->license = $license;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( isset( $_GET['page'] ) && 'learndash-course-import-export' === rtrim( $_GET['page'] ) ){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/learndash-course-import-export-admin.css', array(), $this->version, 'all' );
			// Font awesome
			wp_enqueue_style( 'learndash-cie-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css', array(), $this->version );
		}

		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'sfwd-courses' ){
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/learndash-course-import-export-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		// For progressbar animation
		wp_enqueue_script( $this->plugin_name.'-gsap', plugin_dir_url( __FILE__ ) . 'js/gsap.min.js', array('jquery'), $this->version, true );
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/learndash-course-import-export-admin.js', array( 'jquery' ), $this->version, false );
		$data = $this->get_localize_data();
		wp_localize_script( $this->plugin_name, 'LDCIEVars', $data );
	}

	/**
	 * Get the localized data for JavaScript.
	 *
	 * @return array Localized data.
	 */
	public function get_localize_data() {
		$data = array(
			'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
			'debug'        => defined( 'WP_DEBUG' ) ? true : false,
			'siteURL'      => site_url(),
			'_ajax_nonce'  => wp_create_nonce( 'Learndash_Course_Import_Export' ),
			'err_msg1'     => __( 'Invalid format! Please upload XLS/XLSX file only.', 'learndash-course-import-export' ),
			'err_msg2'     => __( 'Error occurred! Please check the format of the uploaded file.', 'learndash-course-import-export' ),
		);

		return $data;
	}

	/**
	 * Add plugin action links
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $links ) {
		$settings_link = '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">'. __( 'Settings' ). '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Add the plugin's menu to the WordPress admin.
	 */
	public function admin_menu() {
		$options = get_option( '__ldcie_plugin_global_settings', array() );

		// Set the default required capability if not set in options.
		if ( ! isset( $options['required_capability'] ) || empty( $options['required_capability'] ) ) {
			$options['required_capability'] = 'manage_options';
		}

		add_submenu_page(
			'learndash-lms',
			__( 'Course Import/Export', 'learndash-course-import-export' ),
			__( 'Course Import/Export', 'learndash-course-import-export' ),
			$options['required_capability'],
			$this->plugin_name,
			[ $this, 'plugin_page' ]
		);

		if ( current_user_can( $options['required_capability'] ) && ! is_admin() ) {
			// Remove submenus if LD Quiz Import/Export is not active.
			if ( ! class_exists( 'WN_LD_Quiz_Import_Export' ) ) {
				remove_submenu_page( 'learndash-lms', 'edit.php?post_type=sfwd-quiz' );
				remove_submenu_page( 'learndash-lms', 'edit.php?post_type=sfwd-question' );
				remove_submenu_page( 'learndash-lms', 'edit.php?post_type=ld-exam' );
			}
			remove_submenu_page( 'learndash-lms', 'edit.php?post_type=sfwd-certificates' );
		}
	}


	/**
	 * Modify the plugin's row meta links displayed on the plugins page.
	 *
	 * @param array  $links Array of existing row meta links.
	 * @param string $file  Path to the plugin file.
	 *
	 * @return array Modified row meta links.
	 */
	public function plugin_row_meta($links, $file) {
		if ( plugin_basename( LEARNDASH_COURSE_IMPORT_EXPORT_FILE ) === $file ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'Learndash_Course_Import_Export_docs_url', 'https://wooninjas.com/docs/learndash-addons/learndash-course-import-export/' ) ) . '" aria-label="' . sprintf( esc_attr__( 'View %s documentation', 'learndash-course-import-export' ), $this->plugin_name ) . '" target="_blank">' . esc_html__( 'Docs', 'learndash-course-import-export' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'Learndash_Course_Import_Export_support_url', 'https://wooninjas.com/open-support-ticket/' ) ) . '" aria-label="' . esc_attr__( 'Visit premium customer support', 'learndash-course-import-export' ) . '" target="_blank">' . esc_html__( 'Premium support', 'learndash-course-import-export' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}


	/**
	 * Display the plugin review notice on the plugin settings page.
	 */
	public function plugin_review_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! is_admin() || ! is_plugin_active( plugin_basename( LEARNDASH_COURSE_IMPORT_EXPORT_FILE ) ) ) {
			return;
		}

		$user_id                   = get_current_user_id();
		$review_dismissed_key      = $this->plugin_name . '_review_dismissed_' . $user_id;
		$review_dismissed_action_key = $this->plugin_name . '_dismiss_notice';

		if ( isset( $_GET[ $review_dismissed_action_key ] ) ) {
			set_transient( $review_dismissed_key, 1, MONTH_IN_SECONDS );
		}

		// Show review notice on plugin setting page.
		$is_settings_page = ( isset( $_GET['page'] ) && $_GET['page'] == $this->plugin_name );

		if ( $is_settings_page ) {
			$user_data        = get_userdata( get_current_user_id() );
			$review_dismissed = get_transient( $review_dismissed_key );
			$dismiss_url      = add_query_arg( $review_dismissed_action_key, 1 );

			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugin_data = get_plugin_data( LEARNDASH_COURSE_IMPORT_EXPORT_FILE );

			$message      = __( 'Hey %s, Thank you for using <strong>%s</strong>. If you find our plugin useful please take some time to leave a review <a href="%s" target="_blank">here</a>, it will really help us to grow our business.' );
			$message      = sprintf( $message, esc_html( $user_data->user_nicename ), $plugin_data['Name'], $plugin_data['PluginURI'] );
			$message_html = sprintf(
				__(
					'<div class="notice notice-info wn-review-notice" style="padding-right: 38px; position: relative;"><p>%s</p><button type="button" class="notice-dismiss" onclick="location.href=\'%s\';"><span class="screen-reader-text">%s</span></button></div>',
					'learndash-course-import-export'
				),
				$message,
				$dismiss_url,
				__( 'Dismiss this notice.', 'learndash-course-import-export' )
			);

			if ( ! $review_dismissed ) {
				echo $message_html;
			}
		}
	}


	/**
	 * Add branding to footer
	 *
	 * @param $footer_text
	 *
	 * @return mixed
	 */
	function admin_footer_text( $footer_text ) {
		if( isset( $_GET['page'] ) && ( $_GET['page'] == $this->plugin_name ) ) {
			_e('Powered by <a href="http://www.wordpress.org" target="_blank">WordPress</a> | Designed &amp; Developed by <a href="https://wooninjas.com" target="_blank">WooNinjas</a></p>', 'learndash-course-import-export');
		} else {
			return $footer_text;
		}
	}

	/**
	 * Render the plugin's main settings page.
	 */
	public function plugin_page() {
		// Determine the active tab based on user capabilities
		if ( current_user_can( 'manage_options' ) ) {
			$page_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'import';
		} else {
			$page_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'license';
		}
		?>
		<div class="wrap wn_wrap wooninjas_addon">

			<?php
			// Display any settings errors
			settings_errors();
			?>
			<div id="icon-options-general" class="icon32"></div>
			<h1 class="wooninjas_addon_main_heading"><?php echo esc_html__( 'LearnDash Course Import/Export', 'learndash-course-import-export' ); ?></h1>

			<div class="wooninjas_addon_top_nav-LDCIE">
				<?php
				// Get the sections for the navigation tabs
				$sections = $this->get_sections();
				$url      = admin_url( 'admin.php?page=' . $this->plugin_name );

				foreach ( $sections as $key => $section ) {
					$url = add_query_arg( 'tab', $key, $url );

					if ( isset( $section['action'] ) ) {
						$url = add_query_arg( 'action', $section['action'], $url );
					} else {
						remove_query_arg( 'action', $url );
					}

					if ( isset( $section['id'] ) ) {
						$url = add_query_arg( 'id', $section['id'], $url );
					} else {
						remove_query_arg( 'id', $url );
					}
					?>
					<a href="<?php echo esc_url( $url ); ?>"
					   class="nav-tab <?php echo $page_tab === $key ? 'nav-tab-active' : ''; ?>">
						<i class="dashicons dashicons-<?php echo esc_attr( $section['icon'] ); ?>" aria-hidden="true"></i>
						<?php echo esc_html( $section['title'] ); ?>
					</a>
					<?php
				}
				?>
			</div>

			<?php
			foreach ( $sections as $key => $section ) {
				if ( $page_tab === $key ) {
					include( 'partials/' . $key . '.php' );
				}
			}
			?>

		</div>
		<?php
	}


	/**
	 * Get the sections for the plugin's navigation tabs.
	 *
	 * @return array The sections array.
	 */
	private function get_sections() {
		$license_tab = array();

		$sections = array(
			'import' => array(
				'title' => sprintf( __( 'Import', 'learndash-course-import-export' ) . ' %s', learndash_get_custom_label( 'Course' ) ),
				'icon'  => 'upload',
			),
		);

		if ( current_user_can( 'manage_options' ) ) {

			$license_tab['settings'] = array(
				'title' => __( 'General Settings', 'learndash-course-import-export' ),
				'icon'  => 'admin-settings',
			);

			$license_tab['license'] = array(
				'title' => __( 'License', 'learndash-course-import-export' ),
				'icon'  => 'update',
			);

		}

		// Load logs and systems information tab when logs are enabled
		$ldcie_settings = get_option( '__ldcie_plugin_global_settings', array() );

		if ( isset( $ldcie_settings['ldcie_wp_log'] ) && '1' === $ldcie_settings['ldcie_wp_log'] ) {

			$license_tab['logs'] = array(
				'title' => __( 'Debug Logs', 'learndash-course-import-export' ),
				'icon'  => 'update',
			);
			
			$license_tab['system_info'] = array(
				'title' => __( 'System Information', 'learndash-course-import-export' ),
				'icon'  => 'admin-settings',
			);


		}

		// Merge the sections arrays
		return array_merge( $sections, $license_tab );
	}


	/**
	 * Save the plugin's settings.
	 */
	public function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if we are on the plugin settings page.
		if ( isset( $_REQUEST['page'] ) && 'learndash-course-import-export' === rtrim( $_REQUEST['page'] ) ) {

			// Check if the active tab is settings.
			if ( isset( $_REQUEST['tab'] ) && 'settings' === rtrim( $_REQUEST['tab'] ) ) {

				if ( ! empty( $_POST ) ) {
					$ldcie_nonce = isset( $_POST['learndash_course_import_export_nonce'] ) ? $_POST['learndash_course_import_export_nonce'] : -100;

					// Check if the nonce is valid.
					if ( ! wp_verify_nonce( $ldcie_nonce, 'learndash_course_import_export_nonce' ) ) {
						die( 'Process stopped, request could not be verified. Please contact the administrator.' );
					}

					$ldcie_settings = get_option( '__ldcie_plugin_global_settings', array() );

					$ldcie_settings['required_capability'] = isset( $_POST['required_capability'] ) ? $_POST['required_capability'] : 'manage_options';
					$ldcie_settings['publish_course'] = isset( $_POST['publish_course'] ) ? $_POST['publish_course'] : 'manage_options';
					$ldcie_settings['update_duplicate'] = isset( $_POST['update_duplicate'] ) ? $_POST['update_duplicate'] : '';
					$ldcie_settings['ldcie_wp_log'] = isset( $_POST['ldcie_wp_log'] ) ? $_POST['ldcie_wp_log'] : '';
					// Update ldcie global settings.
					update_option( '__ldcie_plugin_global_settings', $ldcie_settings );
				}
			}
		}
	}


	/**
	 * Adds the extra capabilities to manage the courses/lessons/topics
	 *
	 * @param $all_capabilities
	 *
	 * @return $all_capabilities
	 */
	public function learndash_course_import_export_add_extra_caps( $all_capabilities, $cap, $args ) {

		$options = get_option( '__ldcie_plugin_global_settings', array() );
		
		if( isset( $options['required_capability'] ) && ! empty( trim( $options['required_capability'] ) ) && trim( $options['required_capability'] ) != 'manage_options' ) {
		
			$user = new WP_User( get_current_user_id() );
		
			if( ! in_array( 'administrator', $user->roles ) ) {
				
				$editor_role_test = trim( $options['required_capability'] );
				
				if( trim( $options['required_capability'] ) == 'publish_posts' ) {
					$editor_role_test = 'delete_others_posts';
				}

				if( array_key_exists( $options['required_capability'], $all_capabilities ) || array_key_exists( $editor_role_test, $all_capabilities )  ) {
					
					$all_capabilities[ 'read_course' ] = 1;
					$all_capabilities[ 'edit_published_courses' ] = 1;
					$all_capabilities[ 'delete_published_courses' ] = 1;
					$all_capabilities[ 'edit_courses' ] = 1;
				}
			}
		}

		return $all_capabilities;
	}

	private function get_server_information() {
		
		return array(
	        'PHP Version' 			=> phpversion(),
	        'PHP OS' 				=> PHP_OS,
	        'PHP OS Family' 		=> php_uname('s'),
	        'max_execution_time' 	=> ini_get('max_execution_time'),
	        'max_file_uploads' 		=> ini_get('max_file_uploads'),
	        'max_input_time' 		=> ini_get('max_input_time'),
	        'max_input_vars' 		=> ini_get('max_input_vars'),
	        'post_max_size' 		=> ini_get('post_max_size'),
	        'upload_max_filesize' 	=> ini_get('upload_max_filesize'),
	        'curl Version' 			=> curl_version()['version'],
	        'SSL Version' 			=> curl_version()['ssl_version'],
	        'Libz Version' 			=> curl_version()['libz_version'],
	        'Protocols' 			=> implode(', ', curl_version()['protocols']),
	        'mbstring' 				=> extension_loaded('mbstring') ? 'Yes' : 'No',
	        'WordPress Version' 	=> get_bloginfo('version'),
	        'WordPress Home URL' 	=> home_url(),
	        'WordPress Site URL' 	=> site_url(),
	        'Is Multisite' 			=> is_multisite() ? 'Yes' : 'No',
	        'Site Language' 		=> get_locale(),
	        'Object Cache' 			=> defined('WP_CACHE') && WP_CACHE ? 'Yes' : 'No',
	        'DISABLE_WP_CRON' 		=> defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? 'Yes' : 'No',
	        'WP_DEBUG' 				=> defined('WP_DEBUG') && WP_DEBUG ? 'Yes' : 'No',
	        'WP_DEBUG_DISPLAY' 		=> defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ? 'Yes' : 'No',
	        'SCRIPT_DEBUG' 			=> defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'Yes' : 'No',
	        'WP_DEBUG_LOG' 			=> defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Yes' : 'No',
	        'WP_PLUGIN_DIR' 		=> WP_PLUGIN_DIR,
	        'WP_MAX_MEMORY_LIMIT' 	=> WP_MAX_MEMORY_LIMIT,
	        'WP_MEMORY_LIMIT' 		=> WP_MEMORY_LIMIT,
	    );	
	}
}
