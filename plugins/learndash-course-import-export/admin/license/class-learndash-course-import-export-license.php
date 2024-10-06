<?php
if( !class_exists('Learndash_Course_Import_Export_License') ) {
	class Learndash_Course_Import_Export_License {
		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;
		private $license_option_prefix;
		private $api_url = 'https://wooninjas.com';

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		private $page_tab, $setting_menu_slug, $options;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->license_option_prefix = 'learndash_course_import_export';
			$this->version = $version;
			$this->page_tab = ( isset($_GET['tab']) && !empty($_GET['tab']) ) ? $_GET['tab'] : 'license';

			$this->setting_menu_slug = 'learndash-course-import-export';
			$this->options = []; //Learndash_Course_Import_Export_Helper::get_settings();
		}

		function plugin_updater() {

			// retrieve our license key from the DB
			$license_key = trim( get_option( "{$this->license_option_prefix}_license_key" ) );

			// setup the updater
			$edd_updater = new Learndash_Course_Import_Export_Updater( $this->api_url, LEARNDASH_COURSE_IMPORT_EXPORT_FILE,
				array(
					'version' => $this->version, // current version number
					'license' => $license_key,             // license key (used get_option above to retrieve from DB)
					'item_id' => null,       // ID of the product
					'item_name' => urlencode(LEARNDASH_COURSE_IMPORT_EXPORT_PLUGIN_NAME),    // Name of the product
					'author'  => 'WooNinjas', // author of this plugin
					'beta'    => false,
				)
			);

		}

		function register_license_option() {
			// creates our settings in the options table
			register_setting("{$this->license_option_prefix}_license", "{$this->license_option_prefix}_license_key", [$this, 'sanitize_license_key'] );
		}

		function sanitize_license_key( $new ) {
		    $new = trim($new);
			$old = get_option( "{$this->license_option_prefix}_license_key" );

			if( $old && $old != $new ) {
				delete_option( "{$this->license_option_prefix}_license_status" ); // new license has been entered, so must reactivate
			}
			return $new;
		}

		function activate_license() {

			// listen for our activate button to be clicked
			if( isset( $_POST["{$this->license_option_prefix}_license_activate"] ) ) {

				// run a quick security check
				if( ! check_admin_referer( "{$this->license_option_prefix}_nonce", "{$this->license_option_prefix}_nonce" ) )
					return; // get out if we didn't click the Activate button

				// retrieve the license from the database
				$license = '';
				if( isset($_POST[ "{$this->license_option_prefix}_license_key" ]) ) {
					$license = $this->sanitize_license_key($_POST[ "{$this->license_option_prefix}_license_key" ]);
				}

				// data to send in our API request
				$api_params = array(
					'edd_action' => 'activate_license',
					'license'    => $license,
					'item_name'  => urlencode( LEARNDASH_COURSE_IMPORT_EXPORT_PLUGIN_NAME ), // the name of our product in EDD
					'url'        => home_url()
				);

				// Call the custom API.
				$response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'body' => $api_params ) );

				// make sure the response came back okay
				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

					if ( is_wp_error( $response ) ) {
						$message = $response->get_error_message();
					} else {
						$message = __( 'An error occurred, please try again.' );
					}

				} else {

					$license_data = json_decode( wp_remote_retrieve_body( $response ) );

					if ( false === $license_data->success ) {

						switch( $license_data->error ) {

							case 'expired' :

								$message = sprintf(
									__( 'Your license key expired on %s.' ),
									date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
								);
								break;

							case 'disabled' :
							case 'revoked' :

								$message = __( 'Your license key has been disabled.' );
								break;

							case 'missing' :

								$message = __( 'Invalid license.' );
								break;

							case 'invalid' :
							case 'site_inactive' :

								$message = __( 'Your license is not active for this URL.' );
								break;

							case 'item_name_mismatch' :

								$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), LEARNDASH_COURSE_IMPORT_EXPORT_PLUGIN_NAME );
								break;

							case 'no_activations_left':

								$message = __( 'Your license key has reached its activation limit.' );
								break;

							default :

								$message = __( 'An error occurred, please try again.' );
								break;
						}

					}

				}

				// Check if anything passed on a message constituting a failure
				if ( ! empty( $message ) ) {
					$base_url = admin_url( 'admin.php?page=' . $this->plugin_name);
					$redirect = add_query_arg( array( 'tab' => $this->page_tab, 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

					wp_redirect( $redirect );
					exit();
				}

				// $license_data->license will be either "valid" or "invalid"

				update_option( "{$this->license_option_prefix}_license_key", $license );
				update_option( "{$this->license_option_prefix}_license_status", $license_data->license );
				wp_redirect( admin_url( 'admin.php?page=' . $this->plugin_name ) );
				exit();
			}
		}

		function deactivate_license() {

			// listen for our activate button to be clicked
			if( isset( $_POST["{$this->license_option_prefix}_license_deactivate"] ) ) {

				// run a quick security check
				if( ! check_admin_referer( "{$this->license_option_prefix}_nonce", "{$this->license_option_prefix}_nonce" ) )
					return; // get out if we didn't click the Activate button

				// retrieve the license from the database
				$license = trim( get_option( "{$this->license_option_prefix}_license_key" ) );


				// data to send in our API request
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $license,
					'item_name'  => urlencode( LEARNDASH_COURSE_IMPORT_EXPORT_PLUGIN_NAME ), // the name of our product in EDD
					'url'        => home_url()
				);

				// Call the custom API.
				$response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'body' => $api_params ) );

				// make sure the response came back okay
				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

					if ( is_wp_error( $response ) ) {
						$message = $response->get_error_message();
					} else {
						$message = __( 'An error occurred, please try again.' );
					}

					$base_url = admin_url( 'admin.php?page=' . $this->plugin_name );
					$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

					wp_redirect( $redirect );
					exit();
				}

				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// $license_data->license will be either "deactivated" or "failed"
				if( $license_data->license == 'deactivated' || $license_data->license == 'failed' ) {
					delete_option( "{$this->license_option_prefix}_license_status" );
				}

				wp_redirect( admin_url( 'admin.php?page=' . $this->plugin_name ) );
				exit();

			}
		}

		function check_license() {

			$license = trim( get_option( "{$this->license_option_prefix}_license_key" ) );

			$api_params = array(
				'edd_action' => 'check_license',
				'license' => $license,
				'item_name' => urlencode( LEARNDASH_COURSE_IMPORT_EXPORT_PLUGIN_NAME ),
				'url'       => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'body' => $api_params ) );

			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if( $license_data->license == 'valid' ) {
				return 'valid';
				// this license is still valid
			} else {
				return 'invalid';
				// this license is no longer valid
			}
		}

		/**
		 * This is a means of catching errors from the activation method above and displaying it to the customer
		 */
		function license_activation_notices() {

		    if( isset($_GET['page']) && $_GET['page'] !== $this->plugin_name) {
		        return;
            }

			if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

				switch( $_GET['sl_activation'] ) {

					case 'false':
						$message = urldecode( $_GET['message'] );
						?>
						<div class="notice notice-error wn-notice-error wn-license-error">
							<p><?php echo $message; ?></p>
						</div>
						<?php
						break;

					case 'true':
					default:
						?>
						<div class="notice notice-success">
							<p><?php _e('Your license has been activated successfully.'); ?></p>
						</div>
						<?php
						break;

				}
			}
		}

		function admin_notices() {
		    $license_status = get_option("{$this->license_option_prefix}_license_status",'invalid');

		    if( $license_status === 'valid' ) {
		        return;
            }

		    if( ! function_exists('get_plugin_data') ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$plugin_data = get_plugin_data( LEARNDASH_COURSE_IMPORT_EXPORT_FILE );
			$license_setting_url = add_query_arg( array( 'page' => $this->plugin_name, 'tab' => 'license' ), admin_url( 'admin.php' ) );
			?>
            <div class="notice notice-error"><p><?php echo sprintf(__('Your license key for <strong>%s</strong> is invalid or expired. Please go to the <a href="%s">License</a> page to fix this issue.'), $plugin_data['Name'], $license_setting_url);?></p></div>
            <?php
		}

		function weekly_license_check() {
			$license_status = $this->check_license();

			if($license_status === 'valid') {
				update_option("{$this->license_option_prefix}_license_status", 'valid');
			} else {
				delete_option("{$this->license_option_prefix}_license_status");
			}
		}
	}
}