<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_Email_Options{
	function __construct(){
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 10, 1 );
		add_action( 'wp_ajax_TPRM_importer_mail_options_remove_attachment', array( $this, 'ajax_remove_attachment' ) );
		add_action( 'wp_ajax_TPRM_importer_send_test_email', array( $this, 'ajax_send_test_email' ) );
		add_action( 'TPRM_importer_users_importer_start', array( $this, 'maybe_fill_empty_options' ) );
		add_action( 'TPRM_importer_mail_options_save_settings', array( $this, 'save_mail_template' ), 10, 1 );
	}

	public static function admin_gui(){
		$automatic_created_edited_wordpress_email = get_option( "TPRM_importer_automatic_created_edited_wordpress_email" );
		$automatic_wordpress_email = get_option( "TPRM_importer_automatic_wordpress_email" );
		$subject_mail = get_option( "TPRM_importer_mail_subject" );
		$body_mail = get_option( "TPRM_importer_mail_body" );
		$template_id = get_option( "TPRM_importer_mail_template_id" );
		$attachment_id = get_option( "TPRM_importer_mail_attachment_id" );
		$enable_email_templates = get_option( "TPRM_importer_enable_email_templates" );
		$disable_wp_editor = get_option( "TPRM_importer_mail_disable_wp_editor" );
	?>
		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
		<h3><?php _e('WordPress automatic emails','kwf-importer'); ?></h3>
		
		<table class="optiontable form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'User created or edited', 'kwf-importer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e( 'User created or edited', 'kwf-importer' ); ?></span>
							</legend>
							<label for="automatic_created_edited_wordpress_email">
                                <?php TPRM_importerHTML()->select( array(
                                    'options' => array( 'false' => __( "Deactivate WordPress automatic email when an user is created or edited", 'kwf-importer' ), 'true' => __( 'Activate WordPress automatic email when an user is created or edited', 'kwf-importer' ) ),
                                    'name' => 'automatic_created_edited_wordpress_email',
                                    'selected' => $automatic_created_edited_wordpress_email,
                                    'show_option_all' => false,
                                    'show_option_none' => false,
                                )); ?>
								<span class="description"><? _e( "When you create or update an user, WordPress prepare and send automatic email, you can deactivate it here.", 'kwf-importer' ); ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Password changed', 'kwf-importer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e( 'Send automatic change password WordPress emails?', 'kwf-importer' ); ?></span>
							</legend>
							<label for="automatic_wordpress_email">
                                <?php TPRM_importerHTML()->select( array(
                                    'options' => array( 'false' => __( "Deactivate WordPress automatic email when an user is updated or his password is changed", 'kwf-importer' ), 'true' => __( 'Activate WordPress automatic email when an user is updated or his password is changed', 'kwf-importer' ) ),
                                    'name' => 'automatic_wordpress_email',
                                    'selected' => $automatic_wordpress_email,
                                    'show_option_all' => false,
                                    'show_option_none' => false,
                                )); ?>
								<span class="description"><? _e( "When you update an user or change his password, WordPress prepare and send automatic email, you can deactivate it here.", 'kwf-importer' ); ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e( 'Email templates from this plugin', 'kwf-importer' ); ?></h3>
		<table class="optiontable form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'Enable mail templates:', 'kwf-importer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e( 'Do you want to enable mail templates?', 'kwf-importer' ); ?></span>
							</legend>
							<label for="enable_email_templates">
                                <?php TPRM_importerHTML()->checkbox( array( 'name' => 'enable_email_templates', 'compare_value' => $enable_email_templates ) ); ?>
								<span class="description"><? _e( "If you activate it, a new option in the menu will be created to store and manage mail templates, instead of using only the next one.", 'kwf-importer' ); ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php _e( 'Disable WP Editor:', 'kwf-importer' ); ?>
					</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php _e( 'Do you want to disable WP Editor?', 'kwf-importer' ); ?></span>
							</legend>
							<label for="disable_wp_editor">
                                <?php TPRM_importerHTML()->checkbox( array( 'name' => 'disable_wp_editor', 'compare_value' => $disable_wp_editor ) ); ?>
								<span class="description"><?php _e( 'If you want to use email with custom HTML and CSS tags, disable WP Editor', 'kwf-importer' ); ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<?php if( $enable_email_templates && wp_count_posts( 'TPRM_importer_email_template' )->publish > 0 ): ?>
			<h3><?php _e( 'Load custom email from email templates', 'kwf-importer' ); ?></h3>
			<?php wp_dropdown_pages( array( 'id' => 'email_template_selected', 'post_type' => 'TPRM_importer_email_template', 'selected' => $template_id ) ); ?>
			<input id="load_email_template" class="button-primary" type="button" value="<?php _e( "Load subject, content and attachment from this email template", 'kwf-importer' ); ?>"/>
		<?php endif; ?>			

		<h3><?php _e( 'Customize the email that can be sent when importing users', 'kwf-importer' ); ?></h3>

		<p><?php _e( 'Mail subject:', 'kwf-importer' ); ?><input name="subject_mail" size="100" value="<?php echo $subject_mail; ?>" id="title" autocomplete="off" type="text"></p>
		
		<?php if( $disable_wp_editor ): ?>
		<p><textarea name='body_mail' style="width:100%;" rows="20"><?php echo $body_mail; ?></textarea></p>
		<?php else: ?>
		<?php wp_editor( $body_mail, 'body_mail'); ?>
		<?php endif; ?>

		<input type="hidden" id="template_id" name="template_id" value="<?php echo $template_id; ?>"/>

		<fieldset>
			<div>
				<label for="email_template_attachment_file"><?php _e( 'Attachment', 'kwf-importer' )?></label><br>
                <?php TPRM_importerHTML()->text( array( 'type' => 'url', 'name' => 'email_template_attachment_file', 'value' => wp_get_attachment_url( $attachment_id ), 'class' => 'large-text', 'readonly' => true ) ); ?>
				<input type="hidden" name="email_template_attachment_id" id="email_template_attachment_id" value="<?php echo $attachment_id ?>"/>
				<button type="button" class="button" id="TPRM_importer_email_option_upload_button"><?php _e( 'Upload file', 'kwf-importer' )?></button>
				<button type="button" class="button" id="TPRM_importer_email_option_remove_upload_button"><?php _e( 'Remove file', 'kwf-importer' )?></button>
			</div>
		</fieldset>

		<br/>
		<input class="button-primary" type="submit" value="<?php _e( 'Save email template and options', 'kwf-importer'); ?>" id="save_mail_template_options"/>
		<input class="button-primary" type="button" value="<?php _e( 'Send test email', 'kwf-importer'); ?>" id="send_test_email" title="<?php _e( 'This test email will be sent to the current user', 'kwf-importer'); ?>"/>
        <?php _e( 'If you send a test email, no wildcards will be replaced becuase when you test, we have no data to replace.', 'kwf-importer' ); ?>

		<?php wp_nonce_field( 'kwf-security', 'security' ); ?>
		
		<?php do_action( 'TPRM_importer_email_options_after_editor' ); ?>

		</form>
		<?php
	}

	function maybe_fill_empty_options(){
		if( get_option( "TPRM_importer_mail_body" ) == "" )
			update_option( "TPRM_importer_mail_body", __( 'Welcome,', 'kwf-importer' ) . '<br/>' . __( 'Your data to login in this site is:', 'kwf-importer' ) . '<br/><ul><li>' . __( 'URL to login', 'kwf-importer' ) . ': **loginurl**</li><li>' . __( 'Username', 'kwf-importer' ) . ' = **username**</li><li>' . __( 'Password', 'kwf-importer' ) . ' = **password**</li></ul>' );
	
		if( get_option( "TPRM_importer_mail_subject" ) == "" )
			update_option( "TPRM_importer_mail_subject", __('Welcome to','kwf-importer') . ' ' . get_bloginfo("name") );
	}

	function save_mail_template( $form_data ){
		if ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'kwf-security' ) ) {
			wp_die( __( 'Nonce check failed', 'kwf-importer' ) ); 
		}
	
		add_filter( 'wp_kses_allowed_html', array( $this, 'allow_more_post_tags' ), 10, 2 );

		$automatic_wordpress_email = sanitize_text_field( $form_data["automatic_wordpress_email"] );
		$automatic_created_edited_wordpress_email = sanitize_text_field( $form_data["automatic_created_edited_wordpress_email"] );
		$subject_mail = sanitize_text_field( stripslashes_deep( $form_data["subject_mail"] ) );
		$body_mail = wp_kses_post( stripslashes( $form_data["body_mail"] ) );
		$template_id = intval( $form_data["template_id"] );
		$email_template_attachment_id = intval( $form_data["email_template_attachment_id"] );
		$disable_wp_editor = isset( $form_data['disable_wp_editor'] ) && $form_data['disable_wp_editor'] == '1';

		remove_filter( 'wp_kses_allowed_html', array( $this, 'allow_more_post_tags' ), 10, 2 );
	
		update_option( "TPRM_importer_automatic_wordpress_email", $automatic_wordpress_email );
		update_option( "TPRM_importer_automatic_created_edited_wordpress_email", $automatic_created_edited_wordpress_email );
		update_option( "TPRM_importer_mail_subject", $subject_mail );
		update_option( "TPRM_importer_mail_body", $body_mail );
		update_option( "TPRM_importer_mail_template_id", $template_id );
		update_option( "TPRM_importer_mail_attachment_id", $email_template_attachment_id );
		update_option( "TPRM_importer_mail_disable_wp_editor", $disable_wp_editor );
	
		$template_id = absint( $form_data["template_id"] );
	
		if( !empty( $template_id  ) ){
			wp_update_post( array(
				'ID'           => $template_id,
				'post_title'   => $subject_mail,
				'post_content' => $body_mail,
			) );
	
			update_post_meta( $template_id, 'email_template_attachment_id', $email_template_attachment_id );
		}
		?>
		<div class="updated">
		   <p><?php _e( 'Mail template and options updated correctly', 'kwf-importer' )?></p>
		</div>
		<?php
	}

	static function send_email( $user_object, $positions = array(), $headers = array(), $data = array(), $created = false, $password = '' ){
		$user_id = $user_object->ID;
		$user_email = $user_object->user_email;
		$key = get_password_reset_key( $user_object );
		
		$body = apply_filters( 'TPRM_importer_import_email_body_source', get_option( "TPRM_importer_mail_body" ), $headers, $data, $created, $user_id );
		$subject = apply_filters( 'TPRM_importer_import_email_subject_source', get_option( "TPRM_importer_mail_subject" ), $headers, $data, $created, $user_id );
		
		$body = self::apply_wildcards( $body, $user_object, $created, $positions, $headers, $data, $password, $key );
		$subject = self::apply_wildcards( $subject, $user_object, $created, $positions, $headers, $data, $password, $key );

		$body = apply_filters( 'TPRM_importer_import_email_body_before_wpautop', $body, $headers, $data, $created, $user_id );
		
		$attachments = array();
		$attachment_id = get_option( 'TPRM_importer_mail_attachment_id' );
		if( !empty( $attachment_id ) )
			$attachments[] = get_attached_file( $attachment_id );

		$email_to = apply_filters( 'TPRM_importer_import_email_to', $user_email, $headers, $data, $created, $user_id );
		$subject = apply_filters( 'TPRM_importer_import_email_subject', $subject, $headers, $data, $created, $user_id );
		$body = apply_filters( 'TPRM_importer_import_email_body', wpautop( $body ), $headers, $data, $created, $user_id );
		$headers_mail = apply_filters( 'TPRM_importer_import_email_headers', array( 'Content-Type: text/html; charset=UTF-8' ), $headers, $data, $created, $user_id );
		$attachments = apply_filters( 'TPRM_importer_import_email_attachments', $attachments, $headers, $data, $created, $user_id );

		wp_mail( $email_to, $subject, $body, $headers_mail, $attachments );
	}

	static function apply_wildcards( $string, $user_object, $created, $positions, $headers, $data, $password, $key ){
		$TPRM_importer_helper = new TPRM_importer_Helper();
		$wp_users_fields = $TPRM_importer_helper->get_wp_users_fields();

		$user_login = $user_object->user_login;
		$user_email = $user_object->user_email;
		
		$string = str_replace( "**username**", $user_login, $string );
		$string = str_replace( "**password**", $password, $string );
		$string = str_replace( "**email**", $user_email, $string );

		$string = str_replace( "**loginurl**", wp_login_url(), $string );
		$string = str_replace( "**lostpasswordurl**", wp_lostpassword_url(), $string );

		if( !is_wp_error( $key ) ){
			if( is_multisite() ){
				$sites = get_blogs_of_user( $user_object->ID );

				if( count( $sites ) == 1 ){
					$passwordreseturl = get_site_url( array_keys( $sites )[0], 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ), 'login' );
				}
				else{
					$passwordreseturl = network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ), 'login' );
				}
			}
			else{
				$passwordreseturl = site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_login ), 'login' );
			}

			$passwordreseturl = apply_filters( 'TPRM_importer_email_passwordreseturl', $passwordreseturl );
			
			$string = str_replace( "**passwordreseturl**", $passwordreseturl, $string );
		
			$passwordreseturllink = wp_sprintf( '<a href="%s">%s</a>', $passwordreseturl, __( 'Password reset link', 'kwf-importer' ) );
			$string = str_replace( "**passwordreseturllink**", $passwordreseturllink, $string );
		}
		
		if( empty( $password ) && !$created ){
			$password = __( 'Password has not been changed', 'kwf-importer' );
		}

		foreach ( $wp_users_fields as $wp_users_field ) {								
			if( $positions[ $wp_users_field ] != false && $wp_users_field != "password" ){
				$string = str_replace( "**" . $wp_users_field .  "**", $data[ $positions[ $wp_users_field ] ] , $string );
			}
		}

		for( $i = 0 ; $i < count( $headers ); $i++ ) {
			$to_replace = "**" . $headers[ $i ] .  "**";
			
			if( strpos( $string, $to_replace ) === false )
				continue;
			
			$data[ $i ] = ( is_array( $data[ $i ] ) ) ? implode( "-", $data[ $i ] ) : $data[ $i ];
			$string = str_replace( $to_replace, $data[ $i ] , $string );
		}

		$string = apply_filters( 'TPRM_importer_email_apply_wildcards', $string, array( 'key' => $key, 'user_login' => $user_login ) );

		return $string;
	}

	function load_scripts( $hook ) {
		global $typenow;
		
		if( $typenow == 'TPRM_importer_email_template' || $hook == 'toplevel_page_TPRM_importer' ) {
			wp_enqueue_media();
			wp_register_script( 'TPRM_importer-email-template-attachment-admin', esc_url( plugins_url( 'assets/email-template-attachment-admin.js', dirname( __FILE__ ) ) ), array( 'jquery' ) );
			wp_localize_script( 'TPRM_importer-email-template-attachment-admin', 'email_template_attachment_admin',
				array(
					'title' => __( 'Choose or upload file', 'kwf-importer' ),
					'button' => __( 'Use this file', 'kwf-importer' ),
					'security' => wp_create_nonce( "kwf-security" )
				)
			);
			wp_enqueue_script( 'TPRM_importer-email-template-attachment-admin' );
		}

		if( $hook == 'toplevel_page_TPRM_importer' ){ 
			wp_register_script( 'TPRM_importer-email-options', esc_url( plugins_url( 'assets/email-options.js', dirname( __FILE__ ) ) ), array( 'jquery' ) );
			wp_localize_script( 'TPRM_importer-email-options', 'email_options',
				array(
					'security' => wp_create_nonce( "kwf-security" ),
					'success_message' => __( 'Test email sent', 'kwf-importer' ),
				)
			);
			wp_enqueue_script( 'TPRM_importer-email-options' );
		}
	}

	function ajax_remove_attachment(){
		check_ajax_referer( 'kwf-security', 'security' );
		update_option( "TPRM_importer_mail_attachment_id", "" );
	}

	function ajax_send_test_email(){
		check_ajax_referer( 'kwf-security', 'security' );

		self::send_email( wp_get_current_user() );
	}

	function allow_more_post_tags( $tags, $context ) {
		if ( 'post' === $context ) {
			$tags['style'] = array();
		}
	
		return $tags;
	}
}
new TPRM_importer_Email_Options();