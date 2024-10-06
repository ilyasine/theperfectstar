<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'allow-multiple-accounts/allow-multiple-accounts.php' ) ){
	return;
}

class TPRM_importer_AllowMultipleAccounts{
	function __construct(){
		add_action( 'TPRM_importer_tab_import_before_import_button', array( $this, 'before_import_button' ) );
		add_action( 'TPRM_importer_tab_cron_before_log', array( $this, 'cron_before_log' ) );
	}

	function before_import_button(){
		?>
		<h2><?php _e( 'Allow multiple accounts compatibility', 'kwf-importer'); ?></h2>
	
		<table class="form-table">
			<tbody>
			<tr class="form-field form-required">
				<th scope="row"><label><?php _e( 'Repeated email in different users?', 'kwf-importer' ); ?></label></th>
				<td>
					<select name="allow_multiple_accounts">
						<option value="not_allowed"><?php _e( 'Not allowed', 'kwf-importer' ); ?></option>
						<option value="allowed"><?php _e( 'Allowed', 'kwf-importer' ); ?></option>
					</select>
					<p class="description"><strong>(<?php _e( 'Only for', 'kwf-importer' ); ?> <a href="https://wordpress.org/plugins/allow-multiple-accounts/"><?php _e( 'Allow Multiple Accounts', 'kwf-importer' ); ?></a> <?php _e( 'users', 'kwf-importer'); ?>)</strong>. <?php _e('Allow multiple user accounts to be created having the same email address.','kwf-importer' ); ?></p>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	function cron_before_log(){
		?>
		<h2><?php _e( 'Allow Multiple Accounts compatibility', 'kwf-importer'); ?></h2>
	
		<table class="form-table">
			<tbody>
	
			<tr class="form-field form-required">
				<th scope="row"><label><?php _e( 'Repeated email in different users?', 'kwf-importer' ); ?></label></th>
				<td>
					<input type="checkbox" name="allow_multiple_accounts" value="yes" <?php if( $allow_multiple_accounts == "allowed" ) echo "checked='checked'"; ?>/>
					<p class="description"><strong>(<?php _e( 'Only for', 'kwf-importer' ); ?> <a href="https://wordpress.org/plugins/allow-multiple-accounts/"><?php _e( 'Allow Multiple Accounts', 'kwf-importer' ); ?></a> <?php _e( 'users', 'kwf-importer'); ?>)</strong>. <?php _e('Allow multiple user accounts to be created having the same email address.','kwf-importer' ); ?></p>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	public static function hack_email( $email ) {
		if ( ! is_email( $email ) ) {
			return;
		}
	
		$old_email = $email;
	
		for ( $i = 0; ! $skip_remap && email_exists( $email ); $i++ ) {
			$email = str_replace( '@', "+ama{$i}@", $old_email );
		}
	
		return $email;
	}

	public static function hack_restore_remapped_email_address( $user_id, $email ) {
		global $wpdb;
	
		$wpdb->update(
			$wpdb->users,
			array( 'user_email' => $email ),
			array( 'ID' => $user_id )
		);
	
		clean_user_cache( $user_id );	
	}
}
new TPRM_importer_AllowMultipleAccounts();