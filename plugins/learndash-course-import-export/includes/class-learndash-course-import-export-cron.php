<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('Learndash_Course_Import_Export_Cron') ) {

	/**
	 * Class Learndash_Course_Import_Export_Cron
	 */
	class Learndash_Course_Import_Export_Cron {
		/**
		 * Learndash_Course_Import_Export_Cron constructor.
		 */
		public function __construct() {
			global $wp_version;

			if( version_compare($wp_version, '5.4', '<') ) {
				add_filter( 'cron_schedules', [ $this, 'add_weekly_schedule' ] ); //Add weekly schedule if WP version is less than 5.4
			}

			//Uncomment following line to add minute schedule, for testing purpose only.
			//add_filter( 'cron_schedules', [$this, 'add_one_min_schedule'] );

			add_action( 'wp', [$this, 'wp_cb'] );
		}

		/**
		 * Add a custom one-minute schedule interval.
		 *
		 * @param array $schedules Existing schedules.
		 * @return array Modified schedules.
		 */
		public function add_one_min_schedule( $schedules ) {
			$schedules['minute'] = array(
				'interval' => MINUTE_IN_SECONDS,
				'display'  => __('Every Minute'),
			);

			return $schedules;
		}

		/**
		 * Add a custom weekly schedule interval.
		 *
		 * @param array $schedules Existing schedules.
		 * @return array Modified schedules.
		 */
		public function add_weekly_schedule( $schedules ) {
			$schedules['weekly'] = array(
				'interval' => WEEK_IN_SECONDS,
				'display'  => __( 'Once Weekly' ),
			);

			return $schedules;
		}

		/**
		 * Callback function for the WordPress cron event.
		 */
		public function wp_cb() {
			$this->weekly_events();
		}

		/**
		 * Schedule the weekly events.
		 */
		private function weekly_events() {
			if ( ! wp_next_scheduled( 'learndash_course_import_export_weekly_scheduled_events' ) ) {
				// Change 'weekly' to 'minute' for testing purposes only.
				wp_schedule_event( current_time( 'timestamp', true ), 'weekly', 'learndash_course_import_export_weekly_scheduled_events' );
			}

			// Delete logs
			if ( ! wp_next_scheduled( 'ldcie_wp_logging_prune_routine' ) ) {
		        wp_schedule_event( time(), 'weekly', 'ldcie_wp_logging_prune_routine' );
		    }
		}

	}

	new Learndash_Course_Import_Export_Cron;
}