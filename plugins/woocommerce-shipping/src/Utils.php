<?php
/**
 * General Automattic\WCShipping utils.
 *
 * Provides utility functions useful for multiple parts of WCShipping.
 *
 * @package Automattic\WCShipping
 */

namespace Automattic\WCShipping;

use Automattic\WCShipping\Connect\WC_Connect_Jetpack;

/**
 * Automattic\WCShipping utils class.
 */
class Utils {
	/**
	 * Get WooCommerce Shipping plugin version.
	 *
	 * @return string
	 */
	public static function get_wcshipping_version() {
		if ( defined( 'WCSHIPPING_VERSION' ) ) {
			return WCSHIPPING_VERSION;
		}
		// Fallback to reading the version from the plugin file.
		$plugin_data = get_file_data( WCSHIPPING_PLUGIN_FILE, array( 'Version' => 'Version' ) );
		return $plugin_data['Version'];
	}

	/**
	 * Return an array of usefull settings that can be used throughout the codebase and as a JS object.
	 *
	 * @return array Array of settings.
	 */
	public static function get_settings_object() {
		$wcshipping_version = self::get_wcshipping_version();

		$jetpack_blog_id = WC_Connect_Jetpack::get_wpcom_site_id();
		if ( $jetpack_blog_id instanceof \WP_Error ) {
			$jetpack_blog_id = -1;
		}

		$settings = array(
			'version'             => $wcshipping_version,
			'is_atomic'           => WC_Connect_Jetpack::is_atomic_site(),
			'is_connected'        => WC_Connect_Jetpack::is_connected(),
			'is_development_site' => WC_Connect_Jetpack::is_development_site(),
			'is_safe_mode'        => WC_Connect_Jetpack::is_safe_mode(),
			'is_offline_mode'     => WC_Connect_Jetpack::is_offline_mode(),
			'environment'         => wp_get_environment_type(),
		);

		return $settings;
	}
}
