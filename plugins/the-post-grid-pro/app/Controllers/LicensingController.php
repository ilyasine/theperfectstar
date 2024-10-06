<?php
/**
 * Licence Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Models\EDD_RT_TPG_Plugin_Updater;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

//phpcs:disable WordPress.Security.NonceVerification.Recommended

if ( ! class_exists( 'LicensingController' ) ) :
	/**
	 * Licence Controller Class.
	 */
	class LicensingController {
		/**
		 * Class constructor
		 */
		public function __construct() {
			add_action( 'wp_ajax_rtTPG_active_Licence', [ $this, 'rtTPG_active_Licence' ] );
			add_action( 'wp_ajax_rtTPGManageLicencing', [ $this, 'rtTPGManageLicencing' ] );
			add_action( 'admin_init', [ $this, 'tpg_licence' ] );
		}

		/**
		 * Licence
		 *
		 * @return void
		 */
		public function tpg_licence() {
			$settings = get_option( rtTPG()->options['settings'] );
			$license  = ! empty( $settings['license_key'] ) ? trim( $settings['license_key'] ) : null;

			$edd_updater = new EDD_RT_TPG_Plugin_Updater(
				EDD_RT_TPG_STORE_URL,
				RT_THE_POST_GRID_PRO_PLUGIN_ACTIVE_FILE_NAME,
				[
					'version' => RT_TPG_PRO_VERSION,
					'license' => $license,
					'item_id' => EDD_RT_TPG_ITEM_ID,
					'author'  => RT_TPG_PRO_AUTHOR,
					'url'     => home_url(),
					'beta'    => false,
				]
			);
		}

		/**
		 * Licence activation
		 *
		 * @return void
		 */
		public function rtTPG_active_Licence() {
			$error = true;
			$html  = $message = null;

			if ( Fns::verifyNonce() ) {
				$settings   = get_option( rtTPG()->options['settings'] );
				$license    = ! empty( $settings['license_key'] ) ? trim( $settings['license_key'] ) : null;
				$api_params = [
					'edd_action' => 'activate_license',
					'license'    => $license,
					'item_id'    => EDD_RT_TPG_ITEM_ID,
					'url'        => home_url(),
				];
				$response   = wp_remote_post(
					EDD_RT_TPG_STORE_URL,
					[
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					]
				);

				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					$err     = $response->get_error_message();
					$message = ( is_wp_error( $response ) && ! empty( $err ) ) ? $err : esc_html__( 'An error occurred, please try again.', 'the-post-grid-pro' );
				} else {
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );

					if ( false === $license_data->success ) {
						switch ( $license_data->error ) {
							case 'expired':
								$message = sprintf(
									__( 'Your license key expired on %s.', 'the-post-grid-pro' ), //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
									date_i18n(
										get_option( 'date_format' ),
										strtotime( $license_data->expires, current_time( 'timestamp' ) )
									)
								);
								break;
							case 'revoked':
								$message = esc_html__( 'Your license key has been disabled.', 'the-post-grid-pro' );
								break;
							case 'missing':
								$message = esc_html__( 'Invalid license.', 'the-post-grid-pro' );
								break;
							case 'invalid':
							case 'site_inactive':
								$message = esc_html__( 'Your license is not active for this URL.', 'the-post-grid-pro' );
								break;
							case 'item_name_mismatch':
								$message = sprintf(
									__( 'This appears to be an invalid license key for %s.', 'the-post-grid-pro' ), //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
									EDD_RT_TPG_ITEM_ITEM_NAME
								);
								break;
							case 'no_activations_left':
								$message = esc_html__( 'Your license key has reached its activation limit.', 'the-post-grid-pro' );
								break;
							default:
								$message = esc_html__( 'An error occurred, please try again.', 'the-post-grid-pro' );
								break;
						}
					}
					// Check if anything passed on a message constituting a failure.
					if ( empty( $message ) ) {
						$settings['license_status'] = $license_data->license;
						update_option( rtTPG()->options['settings'], $settings );
						$error   = false;
						$message = esc_html__( 'Successfully activated', 'the-post-grid-pro' );
					}

					$html = ( $license_data->license === 'valid' ) ? "<input type='submit' class='button-secondary rt-licensing-btn danger' name='license_deactivate' value='" . esc_html__( 'Deactivate License', 'the-post-grid-pro' ) . "'/>"
						: "<input type='submit' class='button-secondary rt-licensing-btn button-primary' name='license_activate' value='" . esc_html__( 'Activate License', 'the-post-grid-pro' ) . "'/>";
				}
			} else {
				$message = esc_html__( 'Session Error !!', 'the-post-grid-pro' );
			}
			$response = [
				'error' => $error,
				'msg'   => $message,
				'html'  => $html,
			];
			wp_send_json( $response );
			die();
		}

		/**
		 * Manage Licence
		 *
		 * @return void
		 */
		public function rtTPGManageLicencing() {
			$error = true;
			$name  = $value = $data = $class = $message = null;

			if ( Fns::verifyNonce() ) {
				$settings = get_option( rtTPG()->options['settings'] );
				$license  = ! empty( $settings['license_key'] ) ? trim( $settings['license_key'] ) : null;

				if ( ! empty( $_REQUEST['type'] ) && $_REQUEST['type'] == 'license_activate' ) {
					$api_params = [
						'edd_action' => 'activate_license',
						'license'    => $license,
						'item_id'    => EDD_RT_TPG_ITEM_ID,
						'url'        => home_url(),
					];
					$response   = wp_remote_post(
						EDD_RT_TPG_STORE_URL,
						[
							'timeout'   => 15,
							'sslverify' => false,
							'body'      => $api_params,
						]
					);
					if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
						$err     = $response->get_error_message();
						$message = ( is_wp_error( $response ) && ! empty( $err ) ) ? $err : esc_html__( 'An error occurred, please try again.', 'the-post-grid-pro' );
					} else {
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						if ( false === $license_data->success ) {
							switch ( $license_data->error ) {
								case 'expired':
									$message = sprintf(
										__( 'Your license key expired on %s.', 'the-post-grid-pro' ), //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
										date_i18n(
											get_option( 'date_format' ),
											strtotime( $license_data->expires, current_time( 'timestamp' ) )
										)
									);
									break;
								case 'revoked':
									$message = esc_html__( 'Your license key has been disabled.', 'the-post-grid-pro' );
									break;
								case 'missing':
									$message = esc_html__( 'Invalid license.', 'the-post-grid-pro' );
									break;
								case 'invalid':
								case 'site_inactive':
									$message = esc_html__( 'Your license is not active for this URL.', 'the-post-grid-pro' );
									break;
								case 'item_name_mismatch':
									$message = sprintf(
										__( 'This appears to be an invalid license key for %s.', 'the-post-grid-pro' ), //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
										EDD_RT_TPG_ITEM_ITEM_NAME
									);
									break;
								case 'no_activations_left':
									$message = esc_html__( 'Your license key has reached its activation limit.', 'the-post-grid-pro' );
									break;
								default:
									$message = esc_html__( 'An error occurred, please try again.', 'the-post-grid-pro' );
									break;
							}
						}
						// Check if anything passed on a message constituting a failure.
						if ( empty( $message ) ) {
							$settings['license_status'] = $license_data->license;
							update_option( rtTPG()->options['settings'], $settings );
							$error = false;
							$name  = 'license_deactivate';
							$value = 'Deactivate License';
							$class = 'button-primary';
						}
					}
				}
				if ( ! empty( $_REQUEST['type'] ) && $_REQUEST['type'] == 'license_deactivate' ) {
					$api_params = [
						'edd_action' => 'deactivate_license',
						'license'    => $license,
						'item_id'    => EDD_RT_TPG_ITEM_ID,
						'url'        => home_url(),
					];
					$response   = wp_remote_post(
						EDD_RT_TPG_STORE_URL,
						[
							'timeout'   => 15,
							'sslverify' => false,
							'body'      => $api_params,
						]
					);

					// Make sure there are no errors.
					if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
						$err     = $response->get_error_message();
						$message = ( is_wp_error( $response ) && ! empty( $err ) ) ? $err : __( 'An error occurred, please try again.', 'the-post-grid-pro' );
					} else {
						unset( $settings['license_status'] );
						update_option( rtTPG()->options['settings'], $settings );
						$error = false;
						$name  = 'license_activate';
						$value = 'Activate License';
						$class = 'button-primary';
					}
				}
			} else {
				$message = esc_html__( 'Session Error !!', 'the-post-grid-pro' );
			}
			$response = [
				'error' => $error,
				'msg'   => $message,
				'name'  => $name,
				'value' => $value,
				'class' => $class,
				'data'  => $data,
			];
			wp_send_json( $response );
			die();
		}
	}
endif;
