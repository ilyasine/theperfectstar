<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_Frontend{
	function __construct(){
	}

    function hooks(){
        add_action( 'TPRM_importer_frontend_save_settings', array( $this, 'save_settings' ), 10, 1 );
		add_action( 'TPRM_importer_post_frontend_import', array( $this, 'email_admin' ) );
		add_shortcode( 'kwf-importer', array( $this, 'shortcode_import' ) );
        add_shortcode( 'export-users', array( $this, 'shortcode_export' ) );
    }
	
	static function admin_gui(){
		$send_mail_frontend = get_option( "TPRM_importer_frontend_send_mail" );
		$send_mail_updated_frontend = get_option( "TPRM_importer_frontend_send_mail_updated" );
		$send_mail_admin_frontend = get_option( "TPRM_importer_frontend_mail_admin" );
        $send_mail_admin_adress_list_frontend = get_option( "TPRM_importer_frontend_send_mail_admin_address_list" );
		$delete_users_frontend = get_option( "TPRM_importer_frontend_delete_users" );
		$delete_users_assign_posts_frontend = get_option( "TPRM_importer_frontend_delete_users_assign_posts" );
		$change_role_not_present_frontend = get_option( "TPRM_importer_frontend_change_role_not_present" );
		$change_role_not_present_role_frontend = get_option( "TPRM_importer_frontend_change_role_not_present_role" );
		$role = get_option( "TPRM_importer_frontend_role" );
		$update_existing_users = get_option( "TPRM_importer_frontend_update_existing_users" );
		$update_roles_existing_users = get_option( "TPRM_importer_frontend_update_roles_existing_users" );
		$activate_users_wp_members = get_option( "TPRM_importer_frontend_activate_users_wp_members" );
		$update_user_groups = get_option( "TPRM_importer_frontend_update_user_groups" );

		if( empty( $send_mail_frontend ) )
			$send_mail_frontend = false;

		if( empty( $send_mail_updated_frontend ) )
			$send_mail_updated_frontend = false;

		if( empty( $send_mail_admin_frontend ) )
			$send_mail_admin_frontend = false;
		
		if( empty( $update_existing_users ) )
			$update_existing_users = 'no';

		if( empty( $update_roles_existing_users ) )
			$update_roles_existing_users = 'no';

		if( empty( $update_user_groups ) )
			$update_user_groups = 'no';
		?>
		<h3><?php _e( "Execute an import of users in the frontend", 'kwf-importer' ); ?> <em><a href="#export_frontend">(<?php _e( "you can also do an export in the frontend", 'kwf-importer' ); ?>)</a></em></h3>

		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
			<table class="form-table">
				<tbody>

				<tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Use this shortcode in any page or post', 'kwf-importer' ); ?></label></th>
					<td>
						<pre>[kwf-importer]</pre>
						<input class="button-primary" type="button" id="copy_to_clipboard" value="<?php _e( 'Copy to clipboard', 'kwf-importer'); ?>"/>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute role', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use role as attribute to choose directly in the shortcode the role to use during the import. Remind that you must use the role slug, for example:', 'kwf-importer' ); ?> <pre>[kwf-importer role="editor"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute delete-only-specified-role', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use this attribute to make delete only users of the specified role that are not present in the CSV, for example:', 'kwf-importer' ); ?> <pre>[kwf-importer role="editor" delete-only-specified-role="true"]</pre> <?php _e( 'will only delete (if the deletion is active) the users not present in the CSV with are editors', 'kwf-importer' ); ?>
					</td>
				</tr>
                </tbody>
            </table>

            <h2 id="TPRM_importer_roles_header"><?php _e( 'Roles', 'kwf-importer'); ?></h2>
            <table class="form-table">
                <tbody>
				<tr class="form-field form-required">
					<th scope="row"><label for="role"><?php _e( 'Default role', 'kwf-importer' ); ?></label></th>
					<td>
						<?php TPRM_importerHTML()->select( array(
                            'options' => TPRM_importer_Helper::get_editable_roles(),
                            'name' => 'role-frontend',
                            'selected' => $role,
                            'show_option_all' => false,
                            'show_option_none' => __( 'Disable role assignment in frontend import', 'kwf-importer' ),
                        )); ?>
						<p class="description"><?php _e( 'Which role would be used to import users?', 'kwf-importer' ); ?></p>
					</td>
				</tr>
                </tbody>
            </table>

            <h2 id="TPRM_importer_Options_header"><?php _e( 'Options', 'kwf-importer'); ?></h2>
            <table class="form-table">
                <tbody>

                <tr id="TPRM_importer_send_email_wrapper" class="form-field">
					<th scope="row"><label for="user_login"><?php _e( 'Send mail', 'kwf-importer' ); ?></label></th>
					<td>
						<p id="sends_email_wrapper">
                            <?php TPRM_importerHTML()->checkbox( array( 'name' => 'send-mail-frontend', 'label' => __( 'Do you wish to send a mail with credentials and other data?', 'kwf-importer' ), 'compare_value' => $send_mail_frontend ) ); ?>
						</p>
						<p id="send_email_updated_wrapper">
                            <?php TPRM_importerHTML()->checkbox( array( 'name' => 'send-mail-updated-frontend', 'label' => __( 'Do you wish to send this mail also to users that are being updated? (not only to the one which are being created)', 'kwf-importer' ), 'compare_value' => $send_mail_updated_frontend ) ); ?>
						</p>
					</td>
				</tr>

                <tr class="form-field form-required">
					<th scope="row"><label for=""><?php _e( 'Force users to reset their passwords?', 'kwf-importer' ); ?></label></th>
					<td>
                        <?php TPRM_importerHTML()->checkbox( array( 'name' => 'force_user_reset_password', 'compare_value' => get_option( 'TPRM_importer_frontend_force_user_reset_password' ) ) ); ?>
                        <p class="description"><?php _e( 'If a password is set to an user and you activate this option, the user will be forced to reset their password in their first login', 'kwf-importer' ); ?></p>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="send_mail_admin_frontend"><?php _e( 'Send notification to admin when the frontend importer is used?', 'kwf-importer' ); ?></label></th>
					<td>
                        <div style="float:left; margin-top: 10px;">
                        <?php TPRM_importerHTML()->checkbox( array( 'name' => 'send_mail_admin_frontend', 'compare_value' => $send_mail_admin_frontend ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php TPRM_importerHTML()->text( array( 'name' => 'send_mail_admin_frontend_address_list', 'value' => $send_mail_admin_adress_list_frontend, 'class' => '', 'placeholder' => __( 'Include a list of emails where notification will be sent, use commas to separate addresses', 'kwf-importer' ) ) ); ?>
							<p class="description"><?php _e( 'If list is empty, the admin email will be used', 'kwf-importer' ); ?></p>
						</div>
					</td>
				</tr>
				</tbody>
			</table>

			<h2><?php _e( 'Update users', 'kwf-importer'); ?></h2>

			<table class="form-table">
				<tbody>
				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Update existing users?', 'kwf-importer' ); ?></label></th>
					<td>
                        <?php TPRM_importerHTML()->select( array(
                            'options' => array( 'no' => __( 'No', 'kwf-importer' ), 'yes' => __( 'Yes', 'kwf-importer' ) ),
                            'name' => 'update_existing_users',
                            'selected' => $update_existing_users,
                            'show_option_all' => false,
                            'show_option_none' => false,
                        )); ?>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Update roles for existing users?', 'kwf-importer' ); ?></label></th>
					<td>
                        <?php TPRM_importerHTML()->select( array(
                            'options' => array( 'no' => __( 'No', 'kwf-importer' ), 'yes_no_override' => __( 'Yes, add new roles and not override existing ones', 'kwf-importer' ), 'yes' => __( 'Yes', 'kwf-importer' ) ),
                            'name' => 'update_roles_existing_users',
                            'selected' => $update_roles_existing_users,
                            'show_option_all' => false,
                            'show_option_none' => false,
                        )); ?>
					</td>
				</tr>
				</tbody>
			</table>

			<h2><?php _e( 'Users not present in CSV file', 'kwf-importer'); ?></h2>
			<table class="form-table">
				<tbody>

				<tr class="form-field form-required">
					<th scope="row"><label for="delete_users_frontend"><?php _e( 'Delete users that are not present in the CSV?', 'kwf-importer' ); ?></label></th>
					<td>
                        <div style="float:left; margin-top: 10px;">
                            <?php TPRM_importerHTML()->checkbox( array( 'name' => 'delete_users_frontend', 'compare_value' => $delete_users_frontend ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php TPRM_importerHTML()->select( array(
                                'options' => TPRM_importer_Helper::get_list_users_with_display_name(),
                                'name' => 'delete-users-assign-posts-frontend',
                                'selected' => $delete_users_assign_posts_frontend,
                                'show_option_all' => false,
                                'show_option_none' => __( 'Delete posts of deleted users without assigning to any user', 'kwf-importer' ),
                            )); ?>
							<p class="description"><?php _e( 'After delete users, we can choose if we want to assign their posts to another user. Please do not delete them or posts will be deleted.', 'kwf-importer' ); ?></p>
						</div>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="change_role_not_present_frontend"><?php _e( 'Change role of users that are not present in the CSV?', 'kwf-importer' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
                            <?php TPRM_importerHTML()->checkbox( array( 'name' => 'change_role_not_present_frontend', 'compare_value' => $change_role_not_present_frontend ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php TPRM_importerHTML()->select( array(
                                'options' => TPRM_importer_Helper::get_editable_roles(),
                                'name' => 'change_role_not_present_role_frontend',
                                'selected' => $change_role_not_present_role_frontend,
                                'show_option_all' => false,
                                'show_option_none' => false,
                            )); ?>
							<p class="description"><?php _e( 'After import users which is not present in the CSV and can be changed to a different role.', 'kwf-importer' ); ?></p>
						</div>
					</td>
				</tr>
				</tbody>
			</table>

			<?php wp_nonce_field( 'kwf-security', 'security' ); ?>
			<input class="button-primary" type="submit" value="<?php _e( 'Save frontend import options', 'kwf-importer'); ?>"/>
		</form>

        <h3 id="export_frontend"><?php _e( "Execute an export of users in the frontend", 'kwf-importer' ); ?></h3>
        <table class="form-table">
			<tbody>

				<tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Use this shortcode in any page or post', 'kwf-importer' ); ?></label></th>
					<td>
						<pre>[export-users]</pre>
						<input class="button-primary" type="button" id="copy_to_clipboard_export" value="<?php _e( 'Copy to clipboard', 'kwf-importer'); ?>"/>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute role', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use role as attribute to choose directly in the shortcode the role to use during the export. Remind that you must use the role slug, for example:', 'kwf-importer' ); ?> <pre>[export-users role="editor"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute from', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use from attribute to filter users created from a specified date. Date format has to be: Y-m-d, for example:', 'kwf-importer' ); ?> <pre>[export-users from="<?php echo date( 'Y-m-d' ); ?>"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute to', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use from attribute to filter users created before a specified date. Date format has to be: Y-m-d, for example:', 'kwf-importer' ); ?> <pre>[export-users to="<?php echo date( 'Y-m-d' ); ?>"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute delimiter', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use delimiter attribute to set which delimiter is going to be used, allowed values are:', 'kwf-importer' ); ?> COMMA, COLON, SEMICOLON, TAB <pre>[export-users delimiter="SEMICOLON"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute order-alphabetically', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use order-alphabetically attribute to order alphabetically the fields, for example', 'kwf-importer' ); ?> <pre>[export-users order-alphabetically]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute columns', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use columns attribute to set which columns must be exported and in which order. Use a list of fields separated by commas, for example', 'kwf-importer' ); ?> <pre>[export-users columns="user_email,first_name,last_name"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute orderby', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'You can use orderby attribute to set the order in which users would be exported. You can use some of the next fields or a meta_key:', 'kwf-importer' ); ?>
                        <ul style="list-style:disc outside none;margin-left:2em;">
                            <li><strong>ID</strong>: <?php _e( 'Order by user id', 'kwf-importer' ); ?></li>
                            <li><strong>display_name</strong>: <?php _e( 'Order by user display name', 'kwf-importer' ); ?></li>
                            <li><strong>name</strong> or <strong>user_name</strong>: <?php _e( 'Order by user name', 'kwf-importer' ); ?></li>
                            <li><strong>login</strong> or <strong>user_login</strong>: <?php _e( 'Order by user login', 'kwf-importer' ); ?></li>
                            <li><strong>nicename</strong> or <strong>user_nicename</strong>: <?php _e( 'Order by user nicename', 'kwf-importer' ); ?></li>
                            <li><strong>email</strong> or <strong>user_email</strong>: <?php _e( 'Order by user email', 'kwf-importer' ); ?></li>
                            <li><strong>url</strong> or <strong>user_url</strong>: <?php _e( 'Order by user url', 'kwf-importer' ); ?></li>
                            <li><strong>registered</strong> or <strong>user_registered</strong>: <?php _e( 'Order by user registered date', 'kwf-importer' ); ?></li>
                            <li><strong>post_count</strong>: <?php _e( 'Order by user post count', 'kwf-importer' ); ?></li>
                            <li><strong><?php _e( 'Any meta_key', 'kwf-importer' ); ?></strong>: <?php _e( 'Order by user meta value', 'kwf-importer' ); ?></li>
                        </ul>
                        <?php _e( 'For example', 'kwf-importer' ); ?> <pre style="display: inline-block;">[export-users orderby="user_email"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute order', 'kwf-importer' ); ?></label></th>
					<td><?php _e( 'If you use orderby attrbute you can also use order attribute that designates the ascending or descending order of the "orderby" parameter, values can be "asc" or "desc", for example', 'kwf-importer' ); ?> <pre>[export-users orderby="display_name" order="asc"]</pre>
					</td>
				</tr>

            </tbody>
        </table>                            

		<script>
		jQuery( document ).ready( function( $ ){
			check_delete_users_checked();
            check_send_mail_admin_frontend();

			$( '#delete_users_frontend' ).on( 'click', function() {
				check_delete_users_checked();
			});

            $( '#send_mail_admin_frontend' ).on( 'click', function() {
                check_send_mail_admin_frontend();
            });

			$( '#copy_to_clipboard' ).click( function(){
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val( '[kwf-importer]' ).select();
				document.execCommand("copy");
				$temp.remove();
			} );

            $( '#copy_to_clipboard_export' ).click( function(){
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val( '[export-users]' ).select();
				document.execCommand("copy");
				$temp.remove();
			} );

			function check_delete_users_checked(){
				if( $('#delete_users_frontend').is(':checked') ){
					$( '#change_role_not_present_role_frontend' ).prop( 'disabled', true );
					$( '#change_role_not_present_frontend' ).prop( 'disabled', true );				
				} else {
					$( '#change_role_not_present_role_frontend' ).prop( 'disabled', false );
					$( '#change_role_not_present_frontend' ).prop( 'disabled', false );
				}
			}

            function check_send_mail_admin_frontend(){
				if( $('#send_mail_admin_frontend').is(':checked') ){
					$( '#send_mail_admin_frontend_address_list' ).prop( 'disabled', false );
				} else {
					$( '#send_mail_admin_frontend_address_list' ).prop( 'disabled', true );
				}
			}            
		});
		</script>
		<?php
	}

	function save_settings( $form_data ){
		if ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'kwf-security' ) ) {
			wp_die( __( 'Nonce check failed', 'kwf-importer' ) ); 
		}

		TPRM_importer_Options::save_options( $form_data, false, true );
		?>
		<div class="updated">
	       <p><?php _e( 'Settings updated correctly', 'kwf-importer' ) ?></p>
	    </div>
	    <?php
	}

	function email_admin(){
        $send_mail_admin_frontend = get_option( "TPRM_importer_frontend_mail_admin" );
        if( $send_mail_admin_frontend == false )
            return;

        $send_mail_admin_adress_list_frontend = get_option( "TPRM_importer_frontend_send_mail_admin_address_list" );
        if( empty( $send_mail_admin_adress_list_frontend ) )
            $send_mail_admin_adress_list_frontend = get_option( 'admin_email' );

		$current_user = wp_get_current_user();
		$current_user_name = ( empty( $current_user ) ) ? 'User not logged in' : $current_user->user_login;

		$body_mail = sprintf( __("User with username: %s has executed an import using the shortcode in the frontend.", 'kwf-importer'), $current_user_name );

		wp_mail( $send_mail_admin_adress_list_frontend, '[Import and export users and customers] Frontend import has been executed', $body_mail, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

    function shortcode_import( $atts ) {
		$atts = shortcode_atts( array( 'role' => '', 'delete-only-specified-role' => false ), $atts );

		ob_start();
		
		if( !current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) )
			wp_die( __( 'Only users who are able to create users can manage this form.', 'kwf-importer' ) );

		if ( $_FILES && !empty( $_POST ) ):
			if ( !wp_verify_nonce( $_POST['security'], 'kwf-security' ) ){
				wp_die( __( 'Nonce check failed', 'kwf-importer' ) );
			}

            if( $_FILES['uploadfile']['error'] != 0 || $_FILES['uploadfile']['size'] == 0 ){
                _e( 'You must choose a file', 'kwf-importer' );
            }
            else{
                do_action( 'TPRM_importer_pre_frontend_import' );

                $file = array_keys( $_FILES );
                $csv_file_id = $this->upload_file( $file[0] );

                // start
                $form_data = array();
                $form_data["path_to_file"] = get_attached_file( $csv_file_id );

                // emails
                $form_data["sends_email"] = get_option( "TPRM_importer_frontend_send_mail" );
                $form_data["send_email_updated"] = get_option( "TPRM_importer_frontend_send_mail_updated" );
                $form_data["force_user_reset_password"] = get_option( "TPRM_importer_frontend_force_user_reset_password" );

                // roles
                $form_data["role"] = empty( $atts["role"] ) ? get_option( "TPRM_importer_frontend_role") : $atts["role"];

                // update
                $form_data["update_existing_users"] = empty( get_option( "TPRM_importer_frontend_update_existing_users" ) ) ? 'no' : get_option( "TPRM_importer_frontend_update_existing_users" );
                $form_data["update_roles_existing_users"] = empty( get_option( "TPRM_importer_frontend_update_roles_existing_users" ) ) ? 'no' : get_option( "TPRM_importer_frontend_update_roles_existing_users" );

                // delete
                $form_data["delete_users_not_present"] = ( get_option( "TPRM_importer_frontend_delete_users" ) ) ? 'yes' : 'no';
                $form_data["delete_users_assign_posts"] = get_option( "TPRM_importer_frontend_delete_users_assign_posts" );
                $form_data["delete_users_only_specified_role"] = empty( $form_data[ "role" ] ) ? false : $atts['delete-only-specified-role'];

                // others
                $form_data["empty_cell_action"] = "leave";
                $form_data["activate_users_wp_members"] = empty( get_option( "TPRM_importer_frontend_activate_users_wp_members" ) ) ? 'no_activate' : get_option( "TPRM_importer_frontend_activate_users_wp_members" );
                $form_data["security"] = wp_create_nonce( "kwf-security" );

                $form_data = apply_filters( 'TPRM_importer_frontend_import_form_data', $form_data );
                
                $TPRM_importer_import = new TPRM_importer_Import();
                $TPRM_importer_import->fileupload_process( $form_data, false, true );

                wp_delete_attachment( $csv_file_id, true );

                do_action( 'TPRM_importer_post_frontend_import' );
            }
		else:
		?>

        <?php do_action( 'TPRM_importer_frontend_import_before_form' ); ?>

		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" class="TPRM_importer_frontend_form">
            <?php do_action( 'TPRM_importer_frontend_import_before_input_file' ); ?>

			<label><?php _e( 'CSV file <span class="description">(required)</span>', 'kwf-importer' ); ?></label></th>
			<input class="TPRM_importer_frontend_file_button" type="button" onclick="document.getElementById('uploadfile').click();" value="<?php echo apply_filters( 'TPRM_importer_import_shortcode_file_button_text', __( 'Choose file', 'kwf-importer' ) ); ?>">
            <input class="TPRM_importer_frontend_file" type="file" name="uploadfile" id="uploadfile" class="uploadfile" style="display:none;" onchange="document.getElementById('TPRM_importer_frontend_selected_file').innerHTML=this.value.replace(/C:\\fakepath\\/i, '');"/>
			<label id="TPRM_importer_frontend_selected_file"><?php _e( 'No file selected', 'kwf-importer' ) ?></label>

            <?php do_action( 'TPRM_importer_frontend_import_after_input_file' ); ?>

			<input class="TPRM_importer_frontend_submit" type="submit" value="<?php echo apply_filters( 'TPRM_importer_import_shortcode_button_text', __( 'Upload and process', 'kwf-importer' ) ); ?>" />

            <?php do_action( 'TPRM_importer_frontend_import_after_submit' ); ?>

			<?php wp_nonce_field( 'kwf-security', 'security' ); ?>
		</form>

        <?php do_action( 'TPRM_importer_frontend_import_after_form' ); ?>

		<?php endif; ?>
		
		<?php
		return ob_get_clean();
	}

	function upload_file( $file_handler ) {
	    if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) {
	        __return_false();
	    }
	    require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
	    require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
	    require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
	    $attach_id = media_handle_upload( $file_handler, 0 );
	    return $attach_id;
	}

    function shortcode_export( $atts ) {
        $atts = shortcode_atts( array( 'role' => '', 'from' => '', 'to' => '', 'delimiter' => '', 'order-alphabetically' => '', 'columns' => '', 'orderby' => '', 'order' => '' ), $atts );

		ob_start();
		
		if( !current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can export them.', 'kwf-importer' ) );

        TPRM_importer_Exporter::enqueue();
        TPRM_importer_Exporter::styles();
		?>
        
		<form method="POST" class="TPRM_importer_frontend_form" id="TPRM_importer_exporter">
            <input type="hidden" name="TPRM_importer_frontend_export" value="1"/>
        
            <?php foreach( $atts as $key => $value ): ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
            <?php endforeach; ?>
            
            <input class="TPRM_importer_frontend_submit" type="submit" value="<?php apply_filters( 'TPRM_importer_export_shortcode_button_text', _e( 'Export', 'kwf-importer' ) ); ?>"/>

			<?php wp_nonce_field( 'kwf-security', 'security' ); ?>

            <div class="user-exporter-progress-wrapper">
                <progress class="user-exporter-progress" value="0" max="100"></progress>
                <span class="user-exporter-progress-value">0%</span>
            </div>
		</form>
		<?php
		return ob_get_clean();
	}
}

$TPRM_importer_frontend = new TPRM_importer_Frontend();
$TPRM_importer_frontend->hooks();