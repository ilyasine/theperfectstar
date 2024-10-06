<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'new-user-approve/new-user-approve.php' ) ){
	return;
}

add_action( 'TPRM_importer_tab_import_before_import_button', 'TPRM_importer_new_user_approve_tab_import_before_import_button' );
function TPRM_importer_new_user_approve_tab_import_before_import_button(){
	?>
	<h2><?php _e( 'New User Approve compatibility', 'kwf-importer'); ?></h2>

	<table class="form-table">
		<tbody>
		<tr class="form-field form-required">
			<th scope="row"><label><?php _e( 'Approve users at the same time is being created', 'kwf-importer' ); ?></label></th>
			<td>
				<select name="approve_users_new_user_appove">
					<option value="no_approve"><?php _e( 'Do not approve users', 'kwf-importer' ); ?></option>
					<option value="approve"><?php _e( 'Approve users when they are being imported', 'kwf-importer' ); ?></option>
				</select>

				<p class="description"><strong>(<?php _e( 'Only for', 'kwf-importer' ); ?> <a href="https://es.wordpress.org/plugins/new-user-approve/"><?php _e( 'New User Approve', 'kwf-importer' ); ?></a> <?php _e( 'users', 'kwf-importer' ); ?></strong>.</p>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
}

add_action( 'post_TPRM_importer_import_single_user', 'TPRM_importer_new_user_post_TPRM_importer_import_single_user', 10, 6  );
function TPRM_importer_new_user_post_TPRM_importer_import_single_user( $headers, $data, $user_id, $role, $positions, $form_data ){
	$approve_users_new_user_approve = ( empty( $form_data["approve_users_new_user_appove"] ) ) ? "no_approve" : sanitize_text_field( $form_data["approve_users_new_user_appove"] );
	if( $approve_users_new_user_approve == "approve" ){
		update_user_meta( $user_id, "pw_user_status", "approved" );
	}
	else{
		update_user_meta( $user_id, "pending", true );
	}
}