<?php
if ( ! defined( 'ABSPATH' ) ) 
    exit;

class TPRM_import_users{
	function __construct(){
	}

    function hooks(){
        add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 10, 1 );
		add_action( 'TPRM_importer_users_importer_start', array( $this, 'maybe_remove_old_csv' ) );
        add_action( 'wp_ajax_TPRM_importer_delete_attachment', array( $this, 'delete_attachment' ) );
		add_action( 'wp_ajax_TPRM_importer_bulk_delete_attachment', array( $this, 'bulk_delete_attachment' ) );
		add_action( 'wp_ajax_TPRM_importer_delete_users_assign_posts_data', array( $this, 'delete_users_assign_posts_data' ) );
    }

    function load_scripts( $hook ){
        if( $hook != 'toplevel_page_TPRM_importer' || ( isset( $_GET['tab'] ) && $_GET['tab'] != 'import-users' ) )
            return;

        wp_enqueue_style( 'select2-css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' );
    }

	static function admin_gui(){
		$settings = new TPRM_importer_Settings( 'import_backend' );
		$settings->maybe_migrate_old_options( 'import_backend' );

		if( is_int( $settings->get( 'delete_users_assign_posts' ) ) ){
			$delete_users_assign_posts_user = get_user_by( 'id', $settings->get( 'delete_users_assign_posts' ) );
			$delete_users_assign_posts_options = array( $settings->get( 'delete_users_assign_posts' ) => $delete_users_assign_posts_user->display_name );
			$delete_users_assign_posts_option_selected = $settings->get( 'delete_users_assign_posts' );
		}
		else{
			$delete_users_assign_posts_options = array( 0 => __( 'No user selected', 'kwf-importer' ) );
			$delete_users_assign_posts_option_selected = 0;
		}

		?>
		<div class="wrap TPRM_importer">	

			<?php do_action( 'TPRM_importer_users_importer_start' ); ?>
			<div id='message' class='updated'>    
				<h3><i><strong><?php _e('Notes :', 'kwf-importer') ?></strong></i></h3>
				<ul style="list-style: inside;">
					<li><?php _e('File must contain at least <strong>8 columns and respect CSV Sample format</strong>.', 'kwf-importer') ?></li>
					<li><?php _e('CSV file structure must match with the sample.', 'kwf-importer') ?></li>
					<li><strong><?php _e('If you want to remove user from a specific groups you should add a column "[old_group]" just before "[bp_group]" ', 'kwf-importer') ?></strong></li>
					<li><?php _e( 'Please, be sure <strong> you have imported all Groups first ( School and Classes ) </strong> before importing users data.', 'kwf-importer' ); ?></li>				
					<li><strong><?php _e('After completing the processing of your users-import.xlsx file, please ensure that you save the file as a CSV (Comma-Separated Values) file format.', 'kwf-importer') ?></strong></li>
					<li><?php _e('If you get "Request timeout" or similar timeout message while trying to import large CSV file contact your hosting support or split your files into two or more part.', 'kwf-importer') ?></li>
					<li><?php _e('Prepare CSV file, make sure the structure match with the sample below :', 'kwf-importer') ?></li>
				</ul>
			</div>
			<div>
				<h2><?php _e( 'Import users into groups from CSV','kwf-importer' ); ?></h2>
			</div>

			<div style="clear:both;"></div>

			<div id="TPRM_importer_form_wrapper" class="main_bar">
				<form method="POST" id="TPRM_importer_form" enctype="multipart/form-data" action="" accept-charset="utf-8">
				<table  id="TPRM_importer_file_wrapper" class="form-table">
					<tbody>

					<?php do_action( 'TPRM_importer_users_importer_before_file_rows' ); ?>

					<tr class="form-field form-required">
						<th scope="row">
						<h4><i><strong><label for="uploadfile"><?php _e('CSV file (required)', 'kwf-importer') ?></label></strong></i></h4>
					</th>
						<td>
							<div id="upload_file">
								<input type="file" name="uploadfile" id="uploadfile" size="35" class="uploadfile" />							
							</div>
							<p>
								<?php _e('For downloading users-import.xlsx file for users, click', 'kwf-importer') ?>
								<a href="<?php echo plugins_url( '../samples/users-import.xlsx', __FILE__  ); ?> "><?php _e('Here', 'kwf-importer') ?></a>
							</p>
							<div id="introduce_path" style="display:none;">
								<input placeholder="<?php _e( 'You have to introduce the path to file, i.e.:' ,'kwf-importer' ); ?><?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/test.csv" type="text" name="path_to_file" id="path_to_file" value="<?php echo $settings->get( 'path_to_file' ); ?>" style="width:70%;" />
								<em><?php _e( 'or you can upload it directly from your PC', 'kwf-importer' ); ?>, <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'kwf-importer' ); ?></a>.</em>
							</div>
						</td>

					</tr>
					<tr>
					<th style="vertical-align: middle;"><h4><i><strong><?php _e('CSV Sample', 'kwf-importer') ?></strong></i></h4></th>
					<td>
						<table class="form-table" cellspacing="0">
								<thead>
									<tr> <!-- username, email, passsword, first_name, last_name, bp_group, bp_group_role, lang -->
										<th class="column-kwf-username"><code>[username]</code></th>						
										<th class="column-kwf-email"><code>[email]</code></th>
										<th class="column-kwf-passord"><code>[passsword]</code></th>
										<th class="column-kwf-first-name"><code>[first_name]</code></th>
										<th class="column-kwf-last-name"><code>[last_name]</code></th>
										<th class="column-kwf-bp-role"><code>[role]</code></th>
										<th class="column-kwf-old-group" colspan="2"><code>[old_group]</code></th>
										<th class="column-kwf-bp-group" colspan="2"><code>[bp_group]</code></th>
										<th class="column-kwf-lang"><code>[lang]</code></th>
										<th class="column-kwf-ecole"><code>[ecole]</code></th>
									</tr>
								</thead>
								<tbody> <!-- yassine;yassine@kodingkids.ma;ks573;Yassine;Idrissi;CE1A;etudiant100;en -->
									<tr id="kwf-csv-example" valign="middle">
										<td class="column-kwf-username"><code>user</code></td>								
										<td class="column-kwf-email"><code>user@tepunareomaori.com</code></td>
										<td class="column-kwf-passord"><code>ks573</code></td>
										<td class="column-kwf-first-name"><code>first_name</code></td>
										<td class="column-kwf-last-name"><code>last_name</code></td>
										<th class="column-kwf-bp-role"><code>director</code></th>
										<td class="column-kwf-old-group" colspan="2"><code>cm1-kwf-2023##cm2-kwf-2023</code></td>
										<td class="column-kwf-bp-group" colspan="2"><code>cp-kwf-2023##ce1-kwf-2023</code></td>									
										<th class="column-kwf-lang"><code>en</code></th>
										<th class="column-kwf-ecole"><code>tepunareomaori</code></th>
									</tr>																
								</tbody>
						</table>
					</td>
					</tr>
					<tr>
			
					</tr>

					<?php do_action( 'TPRM_importer_users_importer_after_file_rows' ); ?>

					</tbody>
				</table>
					
				<h2 id="TPRM_importer_roles_header"><?php _e( 'Roles', 'kwf-importer'); ?></h2>
				<!-- <table id="TPRM_importer_roles_wrapper" class="form-table">
					<tbody>

					<?php do_action( 'TPRM_importer_users_importer_before_roles_rows' ); ?>

					<tr class="form-field">
						<th scope="row"><label for="role"><?php _e( 'Default role', 'kwf-importer' ); ?></label></th>
						<td>
						<?php

							foreach ( TPRM_importer_Helper::get_editable_roles() as $key => $value )
							TPRM_importerHTML()->checkbox( array( 'label' => translate_user_role( $value ), 'name' => 'role[]', 'compare_value' => $settings->get( 'role' ), 'current' => $key, 'array' => true, 'class' => 'roles' ) );
				
						?>
						<p class="description"><?php _e( 'You can also import roles from a CSV column. Please read documentation tab to see how it can be done. If you choose more than one role, the roles would be assigned correctly but you should use some plugin like <a href="https://wordpress.org/plugins/user-role-editor/">User Role Editor</a> to manage them.', 'kwf-importer' ); ?></p>
						</td>
					</tr>

					<?php do_action( 'TPRM_importer_users_importer_after_roles_rows' ); ?>

					</tbody>
				</table> -->
				<table class="form-table" cellspacing="0">
						<thead>
							<tr> <!-- username, email, passsword, first_name, last_name, bp_group, bp_group_role, lang -->
								<th class="column-kwf-for-directors"><code><?php _e('For Directors :','kwf-importer'); ?></code></th>						
								<th class="column-kwf-for-teachers"><code><?php _e('For Teachers :','kwf-importer'); ?></code></th>
								<th class="column-kwf-for-students"><code><?php _e('For Students :','kwf-importer'); ?></code></th>
								<th class="column-kwf-for-students"><code><?php _e('For Students with active subscription :','kwf-importer'); ?></code></th>
							</tr>
						</thead>
						<tbody> <!-- yassine;yassine@kodingkids.ma;ks573;Yassine;Idrissi;CE1A;etudiant100;en -->
							<tr id="kwf-csv-example" valign="middle">
								<td class="column-kwf-for-directors"><code>
									role = director</code><br><br>
								</td>								
								<td class="column-kwf-for-teachers"><code>
									role = teacher</code><br><br>
								</td>								
								<td class="column-kwf-for-students"><code>
									role = student</code><br>
								</td>								
								<td class="column-kwf-for-subscribed-students"><code>
									role = kwf-student</code><br>
								</td>								

							</tr>																
						</tbody>
				</table>

				<h2 id="TPRM_importer_Options_header"><?php _e( 'Options', 'kwf-importer'); ?></h2>
				<table id="TPRM_importer_Options_wrapper" class="form-table">
					<tbody>

					<?php do_action( 'TPRM_importer_users_importer_before_options_rows' ); ?>

					<tr id="TPRM_importer_empty_cell_wrapper" class="form-field form-required">
						<th scope="row"><label for="empty_cell_action"><?php _e( 'What should the plugin do with empty cells?', 'kwf-importer' ); ?></label></th>
						<td>
							<?php TPRM_importerHTML()->select( array(
								'options' => array( 'leave' => __( 'Leave the old value for this metadata', 'kwf-importer' ), 'delete' => __( 'Delete the metadata', 'kwf-importer' ) ),
								'name' => 'empty_cell_action',
								'show_option_all' => false,
								'show_option_none' => false,
								'selected' => $settings->get( 'empty_cell_action' ),
							)); ?>
						</td>
					</tr>

					<tr id="TPRM_importer_send_email_wrapper" class="form-field">
						<th scope="row"><label for="user_login"><?php _e( 'Send mail', 'kwf-importer' ); ?></label></th>
						<td>
							<p id="sends_email_wrapper">
								<?php TPRM_importerHTML()->checkbox( array( 'name' => 'sends_email', 'label' => __( 'Do you wish to send a mail from this plugin with credentials and other data? <a href="' . admin_url( 'tools.php?page=TPRM_importer&tab=mail-options' ) . '">(email template found here)</a>', 'kwf-importer' ), 'current' => 'yes', 'compare_value' => $settings->get( 'sends_email' ) ) ); ?>
							</p>
							<p id="send_email_updated_wrapper">
								<?php TPRM_importerHTML()->checkbox( array( 'name' => 'send_email_updated', 'label' => __( 'Do you wish to send this mail also to users that are being updated? (not only to the one which are being created)', 'kwf-importer' ), 'current' => 'yes', 'compare_value' => $settings->get( 'send_email_updated' ) ) ); ?>
							</p>
						</td>
					</tr>

					<tr class="form-field form-required">
						<th scope="row"><label for=""><?php _e( 'Force users to reset their passwords?', 'kwf-importer' ); ?></label></th>
						<td>
							<?php TPRM_importerHTML()->checkbox( array( 'name' => 'force_user_reset_password', 'label' => __( 'If a password is set to an user and you activate this option, the user will be forced to reset their password in their first login', 'kwf-importer' ), 'current' => 'yes', 'compare_value' => $settings->get( 'force_user_reset_password' ) ) ); ?>
							<p class="description"><?php echo sprintf( __( 'Please, <a href="%s">read the documentation</a> before activating this option', 'kwf-importer' ), admin_url( 'tools.php?page=TPRM_importer&tab=doc#force_user_reset_password' ) ); ?></p>
						</td>
					</tr>

					<?php do_action( 'TPRM_importer_users_importer_after_options_rows' ); ?>

					</tbody>
				</table>

				<h2 id="TPRM_importer_update_groups_header"><?php _e( 'Update Groups', 'kwf-importer'); ?></h2>

				<table id="TPRM_importer_update_groups_wrapper" class="form-table">
					<tbody>

					<?php do_action( 'TPRM_importer_users_groups_importer_before_update_groups_rows' ); ?>

					<tr id="TPRM_importer_update_user_groups_wrapper" class="form-field form-required">
						<th scope="row"><label for="update_user_groups"><?php _e( 'Remove users from groups that are not present in the CSV? ', 'kwf-importer' ); ?></label></th>
						<td>
							<?php TPRM_importerHTML()->select( array(
								'options' => array( 'no' => __( 'No', 'kwf-importer' ), 'yes' => __( 'Yes', 'kwf-importer' ), ),
								'name' => 'update_user_groups',
								'show_option_all' => false,
								'show_option_none' => false,
								'selected' => $settings->get( 'update_user_groups' ),
							)); ?>
						</td>
					</tr>

					<?php do_action( 'TPRM_importer_users_importer_after_update_groups_rows' ); ?>

					</tbody>
				</table>



				<h2 id="TPRM_importer_update_users_header"><?php _e( 'Update users', 'kwf-importer'); ?></h2>

				<table id="TPRM_importer_update_users_wrapper" class="form-table">
					<tbody>

					<?php do_action( 'TPRM_importer_users_importer_before_update_users_rows' ); ?>

					<tr id="TPRM_importer_update_existing_users_wrapper" class="form-field form-required">
						<th scope="row"><label for="update_existing_users"><?php _e( 'Update existing users?', 'kwf-importer' ); ?></label></th>
						<td>
							<?php TPRM_importerHTML()->select( array(
								'options' => array( 'no' => __( 'No', 'kwf-importer' ), 'yes' => __( 'Yes', 'kwf-importer' ), ),
								'name' => 'update_existing_users',
								'show_option_all' => false,
								'show_option_none' => false,
								'selected' => $settings->get( 'update_existing_users' ),
							)); ?>
						</td>
					</tr>

					<tr id="TPRM_importer_update_emails_existing_users_wrapper" class="form-field form-required">
						<th scope="row"><label for="update_emails_existing_users"><?php _e( 'Update emails?', 'kwf-importer' ); ?></label></th>
						<td>
							<?php TPRM_importerHTML()->select( array(
								'options' => array( 'no' => __( 'No', 'kwf-importer' ), 'create' => __( 'No, but create a new user with a prefix in the username', 'kwf-importer' ), 'yes' => __( 'Yes', 'kwf-importer' ) ),
								'name' => 'update_emails_existing_users',
								'show_option_all' => false,
								'show_option_none' => false,
								'selected' => $settings->get( 'update_emails_existing_users' ),
							)); ?>
							<p class="description"><?php _e( 'What the plugin should do if the plugin find an user, identified by their username, with a different email', 'kwf-importer' ); ?></p>
						</td>
					</tr>

					<tr id="TPRM_importer_update_roles_existing_users_wrapper" class="form-field form-required">
						<th scope="row"><label for="update_roles_existing_users"><?php _e( 'Update roles for existing users?', 'kwf-importer' ); ?></label></th>
						<td>
							<?php TPRM_importerHTML()->select( array(
								'options' => array( 'no' => __( 'No', 'kwf-importer' ), 'yes' => __( 'Yes, update and override existing roles', 'kwf-importer' ), 'yes_no_override' => __( 'Yes, add new roles and not override existing ones', 'kwf-importer' ) ),
								'name' => 'update_roles_existing_users',
								'show_option_all' => false,
								'show_option_none' => false,
								'selected' => $settings->get( 'update_roles_existing_users' ),
							)); ?>
						</td>
					</tr>

					<tr id="TPRM_importer_update_allow_update_passwords_wrapper" class="form-field form-required">
						<th scope="row"><label for="update_allow_update_passwords"><?php _e( 'Never update passwords?', 'kwf-importer' ); ?></label></th>
						<td>
							<?php TPRM_importerHTML()->select( array(
								'options' => array( 'no' => __( 'Never update passwords when updating a user', 'kwf-importer' ), 'yes_no_override' => __( 'Yes, add new roles and not override existing ones', 'kwf-importer' ), 'yes' => __( 'Update passwords as it is described in documentation', 'kwf-importer' ) ),
								'name' => 'update_allow_update_passwords',
								'show_option_all' => false,
								'show_option_none' => false,
								'selected' => $settings->get( 'update_allow_update_passwords' ),
							)); ?>
						</td>
					</tr>

					<?php do_action( 'TPRM_importer_users_importer_after_update_users_rows' ); ?>

					</tbody>
				</table>

				<h2 id="TPRM_importer_users_not_present_header"><?php _e( 'Users not present in CSV file', 'kwf-importer'); ?></h2>

				<table id="TPRM_importer_users_not_present_wrapper" class="form-table">
					<tbody>

					<?php do_action( 'TPRM_importer_users_importer_before_users_not_present_rows' ); ?>
					
					<tr id="TPRM_importer_delete_users_wrapper" class="form-field form-required">
						<th scope="row"><label for="delete_users_not_present"><?php _e( 'Delete users that are not present in the CSV?', 'kwf-importer' ); ?></label></th>
						<td>
							<div style="float:left; margin-top: 10px;">
								<?php TPRM_importerHTML()->checkbox( array( 'name' => 'delete_users_not_present', 'current' => 'yes', 'compare_value' => $settings->get( 'delete_users_not_present' ) ) ); ?>
							</div>
							<div style="margin-left:25px;">
								<?php TPRM_importerHTML()->select( array(
									'options' => $delete_users_assign_posts_options,
									'name' => 'delete_users_assign_posts',
									'show_option_all' => false,
									'show_option_none' => __( 'Delete posts of deleted users without assigning to any user or type to search a user', 'kwf-importer' ),
									'selected' => $delete_users_assign_posts_option_selected,
								)); ?>
								<p class="description"><?php _e( 'Administrators will not be deleted anyway. After delete users, we can choose if we want to assign their posts to another user. If you do not choose some user, content will be deleted.', 'kwf-importer' ); ?></p>
							</div>
						</td>
					</tr>

					<tr id="TPRM_importer_not_present_wrapper" class="form-field form-required">
						<th scope="row"><label for="change_role_not_present"><?php _e( 'Change role of users that are not present in the CSV?', 'kwf-importer' ); ?></label></th>
						<td>
							<div style="float:left; margin-top: 10px;">
								<?php TPRM_importerHTML()->checkbox( array( 'name' => 'change_role_not_present', 'current' => 'yes', 'compare_value' => $settings->get( 'change_role_not_present' ) ) ); ?>
							</div>
							<div style="margin-left:25px;">
								<?php TPRM_importerHTML()->select( array(
									'options' => TPRM_importer_Helper::get_editable_roles(),
									'name' => 'change_role_not_present_role',
									'show_option_all' => false,
									'show_option_none' => false,
									'selected' => $settings->get( 'change_role_not_present_role' ),
								)); ?>
								<p class="description"><?php _e( 'After import users which is not present in the CSV and can be changed to a different role.', 'kwf-importer' ); ?></p>
							</div>
						</td>
					</tr>

					<?php do_action( 'TPRM_importer_users_importer_after_users_not_present_rows' ); ?>

					</tbody>
				</table>

				<?php do_action( 'TPRM_importer_tab_import_before_import_button' ); ?>
					
				<?php wp_nonce_field( 'kwf-security', 'security' ); ?>

				<input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="<?php _e( 'Start importing', 'kwf-importer' ); ?>"/>
				<input class="button-primary" type="submit" name="save_options" id="save_options" value="<?php _e( 'Save options without importing', 'kwf-importer' ); ?>"/>
				</form>
			</div>


		</div>
		<script type="text/javascript">
		jQuery( document ).ready( function( $ ){
			check_delete_users_checked();

			$( '#uploadfile_btn' ).click( function(){
				if( $( '#uploadfile' ).val() == "" && $( '#upload_file' ).is( ':visible' ) ) {
					alert("<?php _e( 'Please choose a file', 'kwf-importer' ); ?>");
					return false;
				}

				if( $( '#path_to_file' ).val() == "" && $( '#introduce_path' ).is( ':visible' ) ) {
					alert("<?php _e( 'Please enter a path to the file', 'kwf-importer' ); ?>");
					return false;
				}
			} );

			$( '.TPRM_importer-checkbox.roles[value="no_role"]' ).click( function(){
				var checked = $( this ).is(':checked');
				if( checked ) {
					if( !confirm( '<?php _e( 'Are you sure you want to disables roles from this users?', 'kwf-importer' ); ?>' ) ){         
						$( this ).removeAttr( 'checked' );
						return;
					}
					else{
						$( '.TPRM_importer-checkbox.roles' ).not( '.TPRM_importer-checkbox.roles[value="no_role"]' ).each( function(){
							$( this ).removeAttr( 'checked' );
						} )
					}
				}
			} );

			$( '.TPRM_importer-checkbox.roles' ).click( function(){
				if( $( this ).val() != 'no_role' && $( this ).val() != '' )
					$( '.TPRM_importer-checkbox.roles[value="no_role"]' ).removeAttr( 'checked' );
			} );

			$( '#delete_users_not_present' ).on( 'click', function() {
				check_delete_users_checked();
			});

			$( '.delete_attachment' ).click( function(){
				var answer = confirm( "<?php _e( 'Are you sure to delete this file?', 'kwf-importer' ); ?>" );
				if( answer ){
					var data = {
						'action': 'TPRM_importer_delete_attachment',
						'attach_id': $( this ).attr( "attach_id" ),
						'security': '<?php echo wp_create_nonce( "kwf-security" ); ?>'
					};

					$.post(ajaxurl, data, function(response) {
						if( response != 1 )
							alert( response );
						else{
							alert( "<?php _e( 'File successfully deleted', 'kwf-importer' ); ?>" );
							document.location.reload();
						}
					});
				}
			});

			$( '#bulk_delete_attachment' ).click( function(){
				var answer = confirm( "<?php _e( 'Are you sure to delete ALL CSV files uploaded? There can be CSV files from other plugins.', 'kwf-importer' ); ?>" );
				if( answer ){
					var data = {
						'action': 'TPRM_importer_bulk_delete_attachment',
						'security': '<?php echo wp_create_nonce( "kwf-security" ); ?>'
					};

					$.post(ajaxurl, data, function(response) {
						if( response != 1 )
							alert( "<?php _e( 'There were problems deleting the files, please check files permissions', 'kwf-importer' ); ?>" );
						else{
							alert( "<?php _e( 'Files successfully deleted', 'kwf-importer' ); ?>" );
							document.location.reload();
						}
					});
				}
			});

			$( '.toggle_upload_path' ).click( function( e ){
				e.preventDefault();
				$( '#upload_file,#introduce_path' ).toggle();
			} );

			$( '#vote_us' ).click( function(){
				var win=window.open( 'http://wordpress.org/support/view/plugin-reviews/kwf-importer?free-counter?rate=5#postform', '_blank');
				win.focus();
			} );

			$( '#change_role_not_present_role' ).select2();

			$( '#delete_users_assign_posts' ).select2({
				ajax: {
					url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
					cache: true,
					dataType: 'json',
					minimumInputLength: 3,
					allowClear: true,
					placeholder: { id: '', title: '<?php _e( 'Delete posts of deleted users without assigning to any user', 'kwf-importer' )  ?>' },
					data: function( params ) {
						if( params.term.trim().length < 3 )
							throw false;
	
						var query = {
							search: params.term,
							_wpnonce: '<?php echo wp_create_nonce( 'kwf-security' ); ?>',
							action: 'TPRM_importer_delete_users_assign_posts_data',
						}

						return query;
					}
				},	
			});

			function check_delete_users_checked(){
				if( $( '#delete_users_not_present' ).is( ':checked' ) ){
					$( '#delete_users_assign_posts' ).prop( 'disabled', false );
					$( '#change_role_not_present' ).prop( 'disabled', true );
					$( '#change_role_not_present_role' ).prop( 'disabled', true );				
				} else {
					$( '#delete_users_assign_posts' ).prop( 'disabled', true );
					$( '#change_role_not_present' ).prop( 'disabled', false );
					$( '#change_role_not_present_role' ).prop( 'disabled', false );
				}
			}
		} );
		</script>
		<?php 
	}

	function maybe_remove_old_csv(){
		$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
		$old_csv_files = new WP_Query( $args_old_csv );

		if( $old_csv_files->found_posts > 0 ): ?>
		<div class="postbox">
		    <div title="<?php _e( 'Click to open/close', 'kwf-importer' ); ?>" class="handlediv">
		      <br>
		    </div>

		    <h3 class="hndle"><span>&nbsp;&nbsp;&nbsp;<?php _e( 'Old CSV files uploaded', 'kwf-importer' ); ?></span></h3>

		    <div class="inside" style="display: block;">
		    	<p><?php _e( 'For security reasons you should delete this files, probably they would be visible in the Internet if a bot or someone discover the URL. You can delete each file or maybe you want delete all CSV files you have uploaded:', 'kwf-importer' ); ?></p>
		    	<input type="button" value="<?php _e( 'Delete all CSV files uploaded', 'kwf-importer' ); ?>" id="bulk_delete_attachment" style="float:right;" />
		    	<ul>
		    		<?php while($old_csv_files->have_posts()) : 
		    			$old_csv_files->the_post(); 

		    			if( get_the_date() == "" )
		    				$date = "undefined";
		    			else
		    				$date = get_the_date();
		    		?>
		    		<li><a href="<?php echo wp_get_attachment_url( get_the_ID() ); ?>"><?php the_title(); ?></a> <?php _e( 'uploaded on', 'kwf-importer' ) . ' ' . $date; ?> <input type="button" value="<?php _e( 'Delete', 'kwf-importer' ); ?>" class="delete_attachment" attach_id="<?php the_ID(); ?>" /></li>
		    		<?php endwhile; ?>
		    		<?php wp_reset_postdata(); ?>
		    	</ul>
		        <div style="clear:both;"></div>
		    </div>
		</div>
		<?php endif;
	}

    function delete_attachment() {
		check_ajax_referer( 'kwf-security', 'security' );
	
		if( ! current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can delete CSV attachments.', 'kwf-importer' ) );
	
		$attach_id = absint( $_POST['attach_id'] );
		$mime_type  = (string) get_post_mime_type( $attach_id );
	
		if( $mime_type != 'text/csv' )
			_e('This plugin only can delete the type of file it manages, CSV files.', 'kwf-importer' );
	
		$result = wp_delete_attachment( $attach_id, true );
	
		if( $result === false )
			_e( 'There were problems deleting the file, please check file permissions', 'kwf-importer' );
		else
			echo 1;
	
		wp_die();
	}

	function bulk_delete_attachment(){
		check_ajax_referer( 'kwf-security', 'security' );
	
		if( ! current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) )
        wp_die( __( 'Only users who are able to create users can bulk delete CSV attachments.', 'kwf-importer' ) );
	
		$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
		$old_csv_files = new WP_Query( $args_old_csv );
		$result = 1;
	
		while($old_csv_files->have_posts()) : 
			$old_csv_files->the_post();
	
			$mime_type  = (string) get_post_mime_type( get_the_ID() );
			if( $mime_type != 'text/csv' )
				wp_die( __('This plugin only can delete the type of file it manages, CSV files.', 'kwf-importer' ) );
	
			if( wp_delete_attachment( get_the_ID(), true ) === false )
				$result = 0;
		endwhile;
		
		wp_reset_postdata();
	
		echo $result;
	
		wp_die();
	}

    function delete_users_assign_posts_data(){
        check_ajax_referer( 'kwf-security', 'security' );
	
		if( ! current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can manage this option.', 'kwf-importer' ) );

        $results = array( array( 'id' => '', 'value' => __( 'Delete posts of deleted users without assigning to any user', 'kwf-importer' ) ) );
        $search = sanitize_text_field( $_GET['search'] );

        if( strlen( $search ) >= 3 ){
            $blogusers = get_users( array( 'fields' => array( 'ID', 'display_name' ), 'search' => '*' . $search . '*' ) );
            
            foreach ( $blogusers as $bloguser ) {
                $results[] = array( 'id' => $bloguser->ID, 'text' => $bloguser->display_name );
            }
        }
        
        echo json_encode( array( 'results' => $results, 'more' => 'false' ) );
        
        wp_die();
    }
}

$TPRM_import_users = new TPRM_import_users();
$TPRM_import_users->hooks();
