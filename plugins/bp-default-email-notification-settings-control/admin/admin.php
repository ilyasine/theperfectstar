<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_User_Notification_Email_Settings_Control_Admin
 */
class BP_User_Notification_Email_Settings_Control_Admin {

	/**
	 * Class instance
	 *
	 * @var OptionsBuddy_Settings_Page
	 */
	private $setting_page = null;

	/**
	 * Message to show
	 *
	 * @var string
	 */
	private $message = '';

	/**
	 * BP_User_Notification_Email_Settings_Control_Admin constructor.
	 */
	public function __construct() {
		$this->setting_page = new OptionsBuddy_Settings_Page( 'bp_user_email_preference' );
		$this->setting_page->set_bp_mode();

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Admin init
	 */
	public function admin_init() {
		// set the settings.
		$page    = $this->setting_page;
		$section = $page->add_section( 'basic_section', __( 'Email Settings', 'bp-default-email-notification-settings-control' ), __( 'Select the preference to be activated for the users when they register & activate their account.', 'bp-default-email-notification-settings-control' ) );

		$settings_default = bunec_get_default_settings();

		foreach ( $settings_default as $key => $val ) {
			$section->add_field(
				array(
					'name'    => $key,
					'label'   => $val['label'],
					'desc'    => isset( $val['desc'] ) ? $val['desc'] : '',
					'type'    => 'select',
					'default' => $val['val'],
					'options' => array(
						'yes' => __( 'Yes', 'bp-default-email-notification-settings-control' ),
						'no'  => __( 'No', 'bp-default-email-notification-settings-control' ),
					),
				)
			);
		}

		$page->init();
		$this->bulk_update();
	}

	/**
	 * Add admin menu
	 */
	public function admin_menu() {
		add_options_page(
			__( 'User Notification Email Settings', 'bp-default-email-notification-settings-control' ),
			__( 'User Notification Email Settings', 'bp-default-email-notification-settings-control' ),
			'manage_options',
			'bp-user-notification-email-control',
			array( $this, 'render' )
		);
	}

	/**
	 * Render
	 */
	public function render() {
		$this->setting_page->render(); // render settings page.

		?>
        <div id="bunec-admin-bulk-actions">
            <form method="post" action="">
				<?php wp_nonce_field( 'bunec-bulk-update' ); ?>
                <p><?php _e( 'Bulk updating settings will reset it to your current default settings for all the users. It will reset the settings for the users who have already set their preference too.', 'bunec' ); ?></p>
                <input type="submit" class="button button-primary" name="bunec-bulk-update" value="<?php _e( 'Bulk Update All Members Preference', 'bunec' ); ?>">
            </form>
            <style type="text/css">
                #bunec-admin-bulk-actions {
                    margin-top: 20px;
                }

                #bunec-admin-bulk-actions p {
                    background: #FFEE75;
                    color: #333;
                    padding: 10px;
                    margin-bottom: 10px;
                }

                #bunec-admin-bulk-actions input[type='submit'] {
                    background: #D03E13;
                    border-color: #AE3F1E;
                    text-shadow: none;
                }
            </style>
        </div>
		<?php
	}

	/**
	 * Bulk update
	 */
	private function bulk_update() {
		// is it bulk update.
		if ( empty( $_POST['bunec-bulk-update'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'bunec-bulk-update' ) ) {
			wp_die( __( 'Auth check failed.', 'bp-default-email-notification-settings-control' ) );
		}

		// only admins with capability to manage users can do it.
		if ( ! current_user_can( 'delete_users' ) ) {
			return;
		}

		// apologies for the implicit dependency.
		$settings = BP_User_Notification_Email_Settings_Control::get_instance()->get_settings();

		if ( empty( $settings ) ) {
			return;
		}

		$keys = array_keys( $settings );

		if ( empty( $keys ) ) {
			return;
		}

		$meta_keys = array_map( 'esc_sql', $keys );
		$list      = '\'' . join( '\', \'', $meta_keys ) . '\'';
		$meta_list = '(' . $list . ')';


		global $wpdb;
		$updated = 0;

		// delete current preference.
		$delete_sql = "DELETE FROM {$wpdb->usermeta} WHERE meta_key IN {$meta_list}";

		$wpdb->query( $delete_sql );

		// now update for each key.
		foreach ( $settings as $key => $val ) {

			$update_settings_query = "INSERT INTO {$wpdb->usermeta} (user_id, meta_key, meta_value) 
				SELECT  ID, %s as meta_key, %s as meta_value   FROM {$wpdb->users} where ID !=0";

			$prepared_query = $wpdb->prepare( $update_settings_query, $key, $val );

			$wpdb->query( $prepared_query );
			$updated = 1;
		}

		if ( $updated ) {
			$this->message = __( 'Notification settings updated for all users', 'bunec' );
			add_action( 'admin_notices', array( $this, 'show_notice' ) );
		}
	}

	/**
	 * Show notice
	 */
	public function show_notice() {

		if ( empty( $this->message ) ) {
			return;
		}

		echo "<div class='updated'>";
		echo "<p>{$this->message}</p>";
		echo "</div>";
	}
}

new BP_User_Notification_Email_Settings_Control_Admin();
