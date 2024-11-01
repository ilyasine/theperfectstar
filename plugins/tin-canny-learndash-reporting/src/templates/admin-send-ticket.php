<?php

namespace uncanny_learndash_reporting;

if ( ! defined( 'WPINC' ) ) {
	die;
}

$name  = '';
$email = '';

if ( $license ) {
	$json = wp_remote_get( 'https://www.uncannyowl.com/wp-json/uncanny-rest-api/v1/license/' . $license . '?wpnonce=' . wp_create_nonce( time() ) );

	if ( ! is_wp_error( $json ) ) {
		if ( 200 === wp_remote_retrieve_response_code( $json ) ) {
			$data = json_decode( $json['body'], true );

			if ( $data ) {
				$name  = $data['name'];
				$email = $data['email'];
			}
		}
	}
}

ob_start();
require Config::get_template( 'admin-siteinfo.php' );
$installation_information = ob_get_clean();

?>

<div class="wrap">
	<div class="tclr">

		<?php

		// Add admin header and tabs
		$tab_active = 'uncanny-tincanny-kb';
		require Config::get_template( 'admin-header.php' );

		?>

		<div class="tclr-help">

			<?php if ( $license_is_active ) { ?>

				<?php if ( ultc_filter_has_var( 'sent' ) ) { ?>

					<div id="message" class="updated notice is-dismissible">
						<p>
							<?php esc_html_e( 'Your ticket has been created. Someone at Uncanny Owl will contact you regarding your issue.', 'uncanny-learndash-reporting' ); ?>
						</p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text">
								<?php esc_html_e( 'Dismiss this notice.', 'uncanny-learndash-reporting' ); ?>
							</span>
						</button>
					</div>

				<?php } ?>

				<div class="uo-core">
					<div class="uo-send-ticket">
						<div class="uo-send-ticket__form">
							<div class="uo-send-ticket__title">
								<?php esc_html_e( 'Submit a ticket', 'uncanny-learndash-reporting' ); ?>
							</div>

							<form name="uncanny-help" method="POST" action="<?php echo esc_attr( admin_url( 'admin.php' ) ); ?>">
								<?php wp_nonce_field( 'uncanny0w1', 'tclr-send-ticket' ); ?>

								<textarea class="uo-send-ticket__hidden-field"
									name="siteinfo"><?php echo $installation_information; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</textarea> 

								<input type="hidden" value="uncanny-tincanny-kb&send-ticket=true" name="page"/>

								<div class="uo-send-ticket-form__row">
									<label for="uo-fullname" class="uo-send-ticket-form__label">
										<?php esc_html_e( 'Full name', 'uncanny-learndash-reporting' ); ?>
									</label>
									<input required name="fullname" id="uo-fullname" type="text"
										class="uo-send-ticket-form__text"
										value="<?php echo esc_attr( $name ); ?>">
								</div>

								<div class="uo-send-ticket-form__row">
									<label for="uo-email" class="uo-send-ticket-form__label">
										<?php esc_html_e( 'Email', 'uncanny-learndash-reporting' ); ?>
									</label>
									<input required name="email" id="uo-email" type="email"
										class="uo-send-ticket-form__text"
										value="<?php echo esc_attr( $email ); ?>">
								</div>

								<div class="uo-send-ticket-form__row">
									<label for="uo-website" class="uo-send-ticket-form__label">
										<?php esc_html_e( 'Site URL', 'uncanny-learndash-reporting' ); ?>
									</label>
									<input required name="website" id="uo-website" type="url"
										class="uo-send-ticket-form__text"
										readonly value="<?php echo esc_attr( get_bloginfo( 'url' ) ); ?>">
								</div>

								<div class="uo-send-ticket-form__row">
									<label for="license_key" class="uo-send-ticket-form__label">
										<?php esc_html_e( 'License Key', 'uncanny-learndash-reporting' ); ?>
									</label>
									<input required name="license_key" id="license_key" type="text"
										class="uo-send-ticket-form__text"
										readonly value="<?php echo esc_attr( $license ); ?>">
								</div>

								<div class="uo-send-ticket-form__row">
									<label for="uo-subject" class="uo-send-ticket-form__label">
										<?php esc_html_e( 'Subject', 'uncanny-learndash-reporting' ); ?>
									</label>
									<input required name="subject" id="uo-subject" type="text"
										class="uo-send-ticket-form__text"
										value="">
								</div>

								<div class="uo-send-ticket-form__row">
									<label for="uo-message" class="uo-send-ticket-form__label">
										<?php esc_html_e( 'Message', 'uncanny-learndash-reporting' ); ?>
									</label>
									<textarea required name="message" id="uo-message"
										class="uo-send-ticket-form__textarea"></textarea>
								</div>

								<div class="uo-send-ticket-form__row">
									<input type="checkbox" value="yes" name="site-data"
										checked="checked"> <?php esc_html_e( 'Send site data', 'uncanny-learndash-reporting' ); ?>
								</div>

								<div class="uo-send-ticket-form__row">
									<p>
										<?php
										printf(
											/* translators: %s is the "My Account" page link */
											_x( "Emails must be enabled on your site to create a ticket using this form. If you don't receive a confirmation email shortly after submitting this form, please log the ticket through your %s page.", '%s is the "My Account" page', 'uncanny-learndash-reporting' ),
											sprintf(
												/* translators: %1$s is url, %2$s the translated "My Account" page text */
												'<a href="%1$s" target="_blank" rel="noreferrer">%2$s</a>',
												esc_url( 'https://www.uncannyowl.com/my-account/submit-a-request/?utm_medium=uo_tincanny&utm_campaign=support_page' ),
												esc_html__( 'My Account', 'uncanny-learndash-reporting' )
											)
										);
										?>
									</p>
									<button type="submit" class="uo-send-ticket-form__submit">
										<?php esc_html_e( 'Create ticket', 'uncanny-learndash-reporting' ); ?>
									</button>
								</div>
							</form>
						</div>
						<div class="uo-send-ticket__data">
							<div class="uo-send-ticket__title">
								<?php esc_html_e( 'Site data', 'uncanny-learndash-reporting' ); ?>
							</div>

							<?php echo $installation_information; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				</div>

			<?php } ?>

		</div>
	</div>
</div>
