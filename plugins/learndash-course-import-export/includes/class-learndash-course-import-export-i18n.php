<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Learndash_Course_Import_Export
 * @subpackage Learndash_Course_Import_Export/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Learndash_Course_Import_Export
 * @subpackage Learndash_Course_Import_Export/includes
 * @author     WooNinjas <info@wooninjas.com>
 */
class Learndash_Course_Import_Export_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'learndash-course-import-export',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
