<?php
/**
 * Helper function class to be used globally
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Learndash_Course_Import_Export
 * @subpackage Learndash_Course_Import_Export/includes
 * @since 1.0.0
 */
if( !class_exists('Learndash_Course_Import_Export_Helper') ) {

	class Learndash_Course_Import_Export_Helper {

		/**
		 * Debug Log
		 *
		 * @param $var
		 * @param bool $print
		 * @param bool $show_execute_at
		 */
		public static function debug_log($var, $print=true, $show_execute_at=false) {
			ob_start();

			if($show_execute_at) {
				$bt = debug_backtrace();
				$caller = array_shift($bt);
				$execute_at = $caller['file'] . ':' . $caller['line'] . "\n";
				echo $execute_at;
			}

			if( $print ) {
				if( is_object($var) || is_array($var) ) {
					echo print_r($var, true);
				} else {
					echo $var;
				}
			} else {
				var_dump($var);
			}

			error_log(ob_get_clean());
		}

		/**
		 * Check if the current context is a WordPress cron execution.
		 *
		 * @return bool Whether the current context is a WordPress cron execution.
		 */
		public static function doing_cron() {
			// Bail if using WordPress cron (>4.8.0)
			if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
				return true;
			}

			// Bail if using WordPress cron (<4.8.0)
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				return true;
			}

			// Default to false
			return false;
		}

		/**
		 * Replace spaces with underscores in a string.
		 *
		 * @param string $string The input string.
		 * @return string The modified string with spaces replaced by underscores.
		 */
		public static function string_replace_spaces_to_underscores($string) {
			return strtolower( preg_replace( '/\s+/', '_', trim( $string ) ) );
		}

		/**
		 * Get meta keys for courses.
		 *
		 * @return array Associative array of meta keys and their labels.
		 */
		public static function get_course_meta_keys() {
			$settings = Learndash_Course_Import_Export_Helper::get_course_settings();
			$meta = array();
			foreach( $settings as $setting ) {
				$meta[ $setting ] = ucfirst( str_replace( '_', ' ', $setting ) );
			}
			return $meta;
		}

		/**
		 * Get meta keys for lessons.
		 *
		 * @return array Associative array of meta keys and their labels.
		 */
		public static function get_lesson_meta_keys() {
			$settings = Learndash_Course_Import_Export_Helper::get_lesson_settings();
			$meta = array();
			foreach( $settings as $setting ) {
				$meta[ $setting ] = ucfirst( str_replace( '_', ' ', $setting ) );
			}
			return $meta;
		}

		/**
		 * Get meta keys for topics.
		 *
		 * @return array Associative array of meta keys and their labels.
		 */
		public static function get_topic_meta_keys() {
			$settings = Learndash_Course_Import_Export_Helper::get_topic_settings();
			$meta = array();
			foreach( $settings as $setting ) {
				$meta[ $setting ] = ucfirst( str_replace( '_', ' ', $setting ) );
			}
			return $meta;
		}


		// LearnDash course settings : Prefix => sfwd-courses_
		public static function get_course_settings() {

			$course_data_keys = array(
			    // course material
			    'course_materials_enabled',
			    'course_materials',
			    
			    'course_trial_price',
			    'course_trial_duration_t1',
			    'course_trial_duration_p1',
			    
			    // paynow
			    'course_price_type',
			    'course_price_type_paynow_price',
			    'course_price_type_paynow_enrollment_url',
			    
			    'course_price_billing_cycle',
			    'course_no_of_cycles',
			    'course_price',
			    
			    // subscription
			    'course_price_type_subscribe_billing_cycle',
			    'course_price_type_subscribe_billing_recurring_times',
			    'course_price_type_subscribe_price',
			    'course_price_type_subscribe_enrollment_url',
			    'course_price_billing_t3',
			    'course_price_billing_p3',
			    
			    // closed
			    'course_price_type_closed_custom_button_label',
			    'course_price_type_closed_custom_button_url',
			    'course_price_type_closed_price',
			    
			    // prerequisite	
			    'course_prerequisite_enabled',
			    'course_prerequisite',
			    'course_prerequisite_compare',
			    
			    // points
			    'course_points_enabled',
			    'course_points',
			    'course_points_access',

			    // expire
			    'expire_access',
			    'expire_access_days',
			    'expire_access_delete_progress',
			    
			    // certificate / exam
			    'certificate',
			    'exam_challenge',
			    
			    'course_disable_lesson_progression',
			    // Order / Page
			    'course_disable_content_table',
			    'course_lesson_per_page',
			    'course_lesson_per_page_custom',
			    'course_topic_per_page_custom',
			    
			    'course_lesson_orderby',
			    'course_lesson_order',

			    'course_start_date',
			    'course_end_date',

			    'course_seats_limit',
			);

			return $course_data_keys;

		}

		public static function get_lesson_settings() {
			return array(
    			'forced_lesson_time', 
    			'lesson_assignment_points_amount', 
    			'visible_after',
    			'visible_after_specific_date',
    			'lesson_materials_enabled',
    			'lesson_materials',
    			'lesson_video_enabled',
    			'lesson_video_url',
    			'lesson_video_shown',
    			'lesson_video_auto_start', 
    			'lesson_video_show_controls', 
    			'lesson_video_auto_complete',
    			'lesson_video_auto_complete_delay',
    			'lesson_video_show_complete_button',
    			'lesson_assignment_upload', 
    			'assignment_upload_limit_extensions', 
    			'assignment_upload_limit_size',
    			'lesson_assignment_points_enabled', 
    			'assignment_upload_limit_count', 
    			'lesson_assignment_deletion_enabled', 
    			'auto_approve_assignment',
    			'forced_lesson_time_enabled', 
    			// 'lesson_video_hide_complete_button', 
    			'lesson_schedule', 
    			'sample_lesson', 
    			'lesson_video_focus_pause', 
    			'lesson_video_track_time',
			);
		}

		public static function get_topic_settings() {
			return array(
			    'topic_materials_enabled',
			    'topic_materials',
			    'lesson_video_enabled', 
			    'lesson_video_url',
			    'lesson_video_shown', 
			    'lesson_video_auto_start', 
			    'lesson_video_show_controls',
			    'lesson_video_focus_pause',
			    'lesson_video_track_time',
			    'lesson_video_auto_complete',
			    'lesson_video_auto_complete_delay',
			    'lesson_video_show_complete_button',
			    'lesson_assignment_upload',
			    'assignment_upload_limit_extensions',
			    'assignment_upload_limit_size',
			    'lesson_assignment_points_enabled',
			    'lesson_assignment_points_amount',
			    'assignment_upload_limit_count',
			    'lesson_assignment_deletion_enabled',
			    'auto_approve_assignment',
			    'forced_lesson_time_enabled',
			    'forced_lesson_time',
			    // 'lesson_video_hide_complete_button',
			    'lesson_schedule',
			    'visible_after', 
			    'visible_after_specific_date',
			);
		}

		/**
		 * Format term ids to names.
		 *
		 * @param array  $term_ids  Array of term IDs.
		 * @param string $taxonomy  Taxonomy name.
		 *
		 * @return string Formatted term names.
		 */
		public static function ld_course_import_export_category_format_term_ids( $term_ids, $taxonomy ) {
			// Parse term IDs as integers.
			$term_ids = wp_parse_id_list( $term_ids );

			if ( empty( $term_ids ) ) {
				return '';
			}

			$formatted_terms = array();

			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				foreach ( $term_ids as $term_id ) {
					$formatted_term = array();

					// Get ancestor term IDs in reverse order.
					$ancestor_ids = array_reverse( get_ancestors( $term_id, $taxonomy ) );

					foreach ( $ancestor_ids as $ancestor_id ) {
						// Get ancestor term object.
						$term = get_term( $ancestor_id, $taxonomy );

						if ( $term && ! is_wp_error( $term ) ) {
							$formatted_term[] = $term->name;
						}
					}

					// Get current term object.
					$term = get_term( $term_id, $taxonomy );

					if ( $term && ! is_wp_error( $term ) ) {
						$formatted_term[] = $term->name;
					}

					// Combine formatted term names with hierarchy separator.
					$formatted_terms[] = implode( ' > ', $formatted_term );
				}
			} else {
				foreach ( $term_ids as $term_id ) {
					// Get term object.
					$term = get_term( $term_id, $taxonomy );

					if ( $term && ! is_wp_error( $term ) ) {
						$formatted_terms[] = $term->name;
					}
				}
			}

			// Implode formatted term names with a comma.
			return Learndash_Course_Import_Export_Helper::ld_course_import_export_category_implode_values( $formatted_terms );
		}


		/**
		 * Parse a category field from a CSV.
		 * Categories are separated by commas and subcategories are "parent > subcategory".
		 *
		 * @param string $value    Field value.
		 * @param int    $post_id  Post ID.
		 * @param string $taxonomy Taxonomy name.
		 *
		 * @return array Array of term IDs.
		 */
		public static function ld_course_import_export_category_parse_categories_field( $value, $post_id, $taxonomy ) {
			if ( empty( $value ) ) {
				return array();
			}

			$row_terms  = Learndash_Course_Import_Export_Helper::ld_course_import_export_category_explode_values( $value );
			$categories = array();

			foreach ( $row_terms as $row_term ) {
				$parent = null;
				$_terms = array_map( 'trim', explode( '>', $row_term ) );
				$total  = count( $_terms );

				foreach ( $_terms as $index => $_term ) {
					$term = wp_insert_term( $_term, $taxonomy, array( 'parent' => intval( $parent ) ) );

					if ( is_wp_error( $term ) ) {
						if ( 'term_exists' === $term->get_error_code() ) {
							// When term exists, error data should contain the existing term ID.
							$term_id = $term->get_error_data();
						} else {
							break; // Cannot continue on any other error.
						}
					} else {
						// New term.
						$term_id = $term['term_id'];
					}

					// Only assign the last category.
					if ( ( 1 + $index ) === $total ) {
						$categories[] = $term_id;
					} else {
						// Store parent to be able to insert or query categories based on parent ID.
						$parent = $term_id;
					}
					wp_set_post_terms( $post_id, $categories, $taxonomy, true );
				}
			}

			return $categories;
		}

		
		/**
		 * Implode CSV cell values using commas by default, and wrapping values
		 * which contain the separator.
		 *
		 * @since  3.7.5
		 * @param  array $values Values to implode.
		 * @return string
		 */
		public static function ld_course_import_export_category_implode_values( $values ) {
			$values_to_implode = array();

			foreach ( $values as $value ) {
				$value               = (string) is_scalar( $value ) ? $value : '';
				$values_to_implode[] = str_replace( ',', '\\,', $value );
			}

			return implode( ', ', $values_to_implode );
		}

		/**
		 * Explode CSV cell values using commas by default, and handling escaped
		 * separators.
		 *
		 * @since  3.7.5
		 * @param  string $value     Value to explode.
		 * @param  string $separator Separator separating each value. Defaults to comma.
		 * @return array
		 */
		public static function ld_course_import_export_category_explode_values( $value, $separator = ',' ) {
			$value  = str_replace( '\\,', '::separator::', $value );
			$values = explode( $separator, $value );
			$values = array_map( array( 'Learndash_Course_Import_Export_Helper', 'ld_course_import_export_category_explode_values_formatter' ), $values );

			return $values;
		}

		/**
		 * Remove formatting and trim each value.
		 *
		 * @since  3.7.5
		 * @param  string $value Value to format.
		 * @return string
		 */
		public static function ld_course_import_export_category_explode_values_formatter( $value ) {
			return trim( str_replace( '::separator::', ',', $value ) );
		}
	}
}