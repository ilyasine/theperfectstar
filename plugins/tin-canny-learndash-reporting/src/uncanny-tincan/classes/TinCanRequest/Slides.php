<?php
/**
 * Processing Request
 *
 * @package    Tin Canny Reporting for LearnDash
 * @subpackage TinCan Module
 * @author     Uncanny Owl
 * @since      1.3.0
 */

namespace UCTINCAN\TinCanRequest;

use UCTINCAN\Services;

if ( !defined( "UO_ABS_PATH" ) ) {
	header( "Status: 403 Forbidden" );
	header( "HTTP/1.1 403 Forbidden" );
	exit();
}

abstract class Slides extends \UCTINCAN\TinCanRequest {
	/**
	 * SnC Table Name
	 *
	 * @access public
	 * @return string
	 * @since  1.0.0
	 */
	const TABLE_SNC = 'snc_file_info';

	public $module_url = '';

	/**
	 * Set Modules
	 *
	 * @access protected
	 * @return void
	 * @since  1.0.0
	 */
	protected function set_slides( $decoded_2 = false ) {

		// Set vars 
		$auth        = null;
		$activity_id = null;
		$filter_args = array();

		// Check for Referer
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			parse_str( $_SERVER[ 'HTTP_REFERER' ], $referer );
			$filter_args['referer'] = $referer;
			if ( isset( $referer['client'], $referer['auth'], $referer['activity_id'] ) ) {
				$auth        = $referer[ 'auth' ];
				$activity_id = $referer[ 'activity_id' ];
			}
		}

		// Check for Query String
		if ( empty( $auth ) || empty( $activity_id ) ) {
			if ( isset( $_SERVER['QUERY_STRING'] ) ) {
				parse_str( $_SERVER[ 'QUERY_STRING' ], $request_uri );
				$filter_args['request_uri'] = $request_uri;
				if ( isset( $request_uri['client'], $request_uri['auth'], $request_uri['activity_id'] ) ) {
					$auth        = $request_uri[ 'auth' ];
					$activity_id = $request_uri[ 'activity_id' ];
				}
			}
		}

		// Check for Decoded
		if ( $decoded_2 ) {
			$filter_args['decoded'] = $decoded_2;
			if ( isset( $decoded_2[ 'Authorization' ], $decoded_2[ 'content' ] ) ) {
				$auth        = $decoded_2[ 'Authorization' ];
				$content     = json_decode( $decoded_2[ 'content' ], true );
				$activity_id = $content[ 'object' ][ 'id' ];
			}
		}

		// Check for Auth Headers if still empty.
		if ( empty( $auth ) ) {
			// Try to read all headers first.
			if ( function_exists( 'getallheaders' ) ) {
				$all_headers = getallheaders();
				if ( isset( $all_headers['Authorization'] ) ) {
					$auth = $all_headers['Authorization'];
				}
			}
		}

		// Allow filtering
		$auth        = apply_filters( 'uo_tincanny_set_slides_auth_value', $auth, $filter_args );
		$activity_id = apply_filters( 'uo_tincanny_set_slides_activity_id', $activity_id, $filter_args );

		// Validate
		if ( empty( $auth ) || !is_string( $auth ) || empty( $activity_id ) || !is_string( $activity_id ) ) {
			// TODO REVIEW: Log Error
			return;
		}

		// Group and Parent
		$this->module_url = $activity_id . '&auth=' . $auth;

		$grouping = new \TinCan\Activity( array( 'id' => get_bloginfo( 'url' ) . '/?p=' . substr( $auth, 11 ) ) );
		$parent   = new \TinCan\Activity( array( 'id' => $activity_id ) );

		$this->TC_Context->getContextActivities()->setGrouping( $grouping );
		$this->TC_Context->getContextActivities()->setParent( $parent );

		// ID
		$matches = $this->get_slide_id_from_url( $activity_id );
		$this->content_id = $matches[1];

	}

	/**
	 * Get Module Information
	 *
	 * @access protected
	 * @return array
	 * @since  1.0.0
	 */
	protected function get_module() {
		global $wpdb;
		$module = $target = $module_name = $target_name = '';

		$query = sprintf( "
			SELECT file_name, url FROM %s%s
				WHERE ID = %s
				LIMIT 1;
			",
			$wpdb->prefix,
			self::TABLE_SNC,
			$this->content_id
		);

		$result = $wpdb->get_row( $query, ARRAY_A );

		if( ! empty($result) ) {
			$module = $target = $result[ 'url' ];
			$module_name = $result[ 'file_name' ];
		}

		return compact( 'module', 'module_name', 'target', 'target_name' );
	}

	/**
	 * Get Target Name from TinCan Activity Object
	 *
	 * @access protected
	 * @param  string $target
	 * @return string
	 * @since  1.0.0
	 */
	protected function get_target_from_activity_definition( $target_name ) {
		if ( !empty( $this->TC_Actitity->getDefinition() ) ) {
			if ( !empty( $this->TC_Actitity->getDefinition()->getName()->_map ) ) {
				$target_name = urldecode( array_pop( $this->TC_Actitity->getDefinition()->getName()->_map ) );

			} else if ( !empty( $this->TC_Actitity->getDefinition()->getDescription()->_map ) ) {
				$target_name = urldecode( array_pop( $this->TC_Actitity->getDefinition()->getDescription()->_map ) );

			}
		}

		return $target_name;
	}

	public function get_completion() {
		$service = new Services();
		$completion = $service->check_slide_completion( $this->module_url );
		return ! empty( $completion ) ? $completion : false ;
	}
}
