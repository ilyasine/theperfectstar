<?php
/**
 * License Options
 */

defined( "ABSPATH" ) || exit;

if( ! function_exists('get_plugin_data') ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

$plugin_data = get_plugin_data( LEARNDASH_COURSE_IMPORT_EXPORT_FILE );
$license_option_prefix = 'learndash_course_import_export';
$license = get_option( "{$license_option_prefix}_license_key" );
$status  = get_option( "{$license_option_prefix}_license_status" );

?>
<div class="wn_settings_wrap wn_wrap ldcie-gen-panel">
	<form method="post">
		<?php
        settings_fields("{$license_option_prefix}_license");
		wp_nonce_field( "{$license_option_prefix}_nonce", "{$license_option_prefix}_nonce" );
		?>
		<?php if($status ==='valid'): ?>
        <h2><?php _e( 'Congratulations! You are receiving automatic updates', 'learndash-course-import-export' ) ?></h2>
		<?php else: ?>
		<h2><?php _e( 'Receive Automatic Updates', 'learndash-course-import-export' ) ?></h2>
		<?php endif; ?>
        <?php if($status ==='valid'): ?>
            <h4><?php _e( sprintf('Your license key has been verified and activated successfully, you will receive new features and improvements for "%s" automatically.', $plugin_data['Name']), 'learndash-course-import-export' ) ?></h4>
        <?php else: ?>
            <h4><?php _e( sprintf('Please enter the license key to keep your "%s" plugin updated, and receive new features and improvements automatically.', $plugin_data['Name']), 'learndash-course-import-export' ) ?></h4>
            <h4><?php _e( sprintf('Don\'t have the license key? Click <a href="%s" target="_blank">here</a> to get one.', $plugin_data['PluginURI']), 'learndash-course-import-export' ) ?></h4>
        <?php endif; ?>
		<table class="form-table">
			<tr>
				<th style="width:100px;"><label for="wn-license-key-field"><?php _e( 'License Key', 'learndash-course-import-export' ); ?></label>
				</th>
				<td>
					<input class="regular-text" type="text" id="wn-license-key-field"
					       placeholder="Enter license key provided with plugin"
					       name="<?php echo $license_option_prefix; ?>_license_key"
					       value="<?php esc_attr_e( $license ); ?>"
						<?php echo ( $status ==='valid' ) ? 'readonly' : ''; ?>><?php echo ( $status ==='valid' ) ? '<span class="dashicons dashicons-saved wn-license-activated-icon"></span>' : ''; ?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<?php if( $status !== 'valid' ) : ?>

				<input type="submit" name="<?php echo $license_option_prefix; ?>_license_activate" value="<?php _e( 'Activate', 'learndash-course-import-export' ); ?>"
				       class="button-primary"/>
			<?php else: ?>
				<input type="submit" name="<?php echo $license_option_prefix; ?>_license_deactivate" value="<?php _e( 'Deactivate', 'learndash-course-import-export' ); ?>"
				       class="button-primary"/>
			<?php endif; ?>
		</p>
	</form>
</div>