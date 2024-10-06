<?php
/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>


<?php /* translators: %s: Customer billing full name */ ?>
<p><?php  
echo apply_filters("woocommerce_email_body_text", $text, $order, $email);
?></p>
<?php

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );


/*
 * @ Shows school details
 */
do_action( 'woocommerce_email_school_details', $order, $sent_to_admin, $plain_text, $email );


?>


<p><?php  
echo apply_filters("woocommerce_email_after_order_details", $details_head, $order, $email );
?></p>
<?php


/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */

 do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

 
/*
 * @hooked TPRM_membership_email->TPRM_generated_coupon_details_after_order_table() 
 * Shows license code details
 */
do_action( 'woocommerce_email_order_details_license_for_admin', $order, $sent_to_admin, $plain_text, $email );


/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );


/**
 * Show user-defined additional content - this is set in each email's settings.
 */
/* if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
} */

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
