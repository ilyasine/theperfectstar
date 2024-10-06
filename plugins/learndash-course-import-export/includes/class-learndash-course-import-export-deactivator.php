<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Learndash_Course_Import_Export
 * @subpackage Learndash_Course_Import_Export/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Learndash_Course_Import_Export
 * @subpackage Learndash_Course_Import_Export/includes
 * @author     WooNinjas <info@wooninjas.com>
 */
class Learndash_Course_Import_Export_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        wp_clear_scheduled_hook( 'learndash_course_import_export_daily_scheduled_events' );

	}

}
