<?php
/**
 * Plugin Name: BuddyPress Default Email Notification Settings Control
 * Version: 1.0.8
 * Plugin URI: https://buddydev.com/bp-default-notification-email-settings-control/
 * Author: BuddyDev
 * Author URI: https://buddydev.com
 * License: GPL
 * Description: Allows site admins to set the default email preferences for new users
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_User_Notification_Email_Settings_Control
 */
class BP_User_Notification_Email_Settings_Control {

	/**
	 * Class instance
	 *
	 * @var BP_User_Notification_Email_Settings_Control
	 */
	private static $instance = null;

	/**
	 * Plugin directory absolute path
	 *
	 * @var string
	 */
	private $plugin_dir_path;

	/**
	 * Plugin directory url
	 *
	 * @var string
	 */
	private $plugin_dir_url;

	/**
	 * BP_User_Notification_Email_Settings_Control constructor.
	 */
	private function __construct() {
		$this->plugin_dir_path = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url  = plugin_dir_url( __FILE__ );

		// load required files.
		add_action( 'plugins_loaded', array( $this, 'load' ) );
		// add_action( 'bp_core_activated_user', array( $this, 'set_preference' ) );
		add_action( 'user_register', array( $this, 'set_preference' ), 5 );
	}

	/**
	 * Get Instance
	 *
	 * @return BP_User_Notification_Email_Settings_Control
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/***
	 * Load files
	 */
	public function load() {
		require_once $this->plugin_dir_path . 'bunec-functions.php';

		// Only load on main site
		// Not a good case for BP Multinetwork.
		if ( is_admin() && ! wp_doing_ajax() ) {
			require_once $this->plugin_dir_path . 'admin/options-buddy/ob-loader.php';
			require_once $this->plugin_dir_path . 'admin/admin.php';
		}
	}

	/**
	 * Get all settings as key val
	 * An optimization will be to
	 *
	 * @return array
	 */
	public function get_settings() {
		$all_settings = bunec_get_default_settings();
		$settings     = array();

		foreach ( $all_settings as $key => $setting_info ) {
			$settings[ $key ] = $setting_info['val'];
		}

		// now get current settings.
		$current_settings = bp_get_option( 'bp_user_email_preference', $settings );

		return $current_settings;
	}

	/**
	 * Set preference
	 *
	 * @param int $user_id User id.
	 */
	public function set_preference( $user_id ) {

		if ( ! function_exists( 'buddypress' ) ) {
			return;
		}

		// i am putting all the notifications to no by default.
		$settings_keys = $this->get_settings();

		foreach ( $settings_keys as $setting => $preference ) {
			bp_update_user_meta( $user_id, $setting, $preference );
		}
	}
}

// initialize.
BP_User_Notification_Email_Settings_Control::get_instance();
