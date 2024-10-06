<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order">

	<?php
	if ( $order ) :
		
		do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>
   
        <div class="woocommerce-order-over">

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'tprm-theme' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'tprm-theme' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'tprm-theme' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>
			
                <h2 class="woocommerce-heading--success woocommerce-thankyou-order-received-heading"><i class="bb-icon-rl bb-icon-check"></i><?php esc_html_e( 'Your Order Confirmed', 'tprm-theme' ); ?></h2>

    			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'tprm-theme' ), $order ); ?></p>
    
    			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
    
    				<li class="woocommerce-order-overview__order order">
    					<?php esc_html_e( 'Order number:', 'tprm-theme' ); ?>
    					<span><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
    				</li>
    
    				<li class="woocommerce-order-overview__date date">
    					<?php esc_html_e( 'Date:', 'tprm-theme' ); ?>
    					<span><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
    				</li>
    
    				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
    					<li class="woocommerce-order-overview__email email">
    						<?php esc_html_e( 'Email:', 'tprm-theme' ); ?>
    						<span><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
    					</li>
    				<?php endif; ?>
    
    				<li class="woocommerce-order-overview__total total">
    					<?php esc_html_e( 'Total:', 'tprm-theme' ); ?>
    					<span><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
    				</li>
    
    				<?php if ( $order->get_payment_method_title() ) : ?>
    					<li class="woocommerce-order-overview__payment-method method">
    						<?php esc_html_e( 'Payment method:', 'tprm-theme' ); ?>
    						<span><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
    					</li>
    				<?php endif; ?>
    
    			</ul>
				

		<?php endif; ?>
		

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'TPRM_thankyou', $order->get_id() ); ?>
        </div> 
		<?php // woocommerce-order-over 
		$couponprint = '';
		if(isset($couponprint)) :
			echo apply_filters('TPRM_coupon_print', $couponprint) ;
			/* echo apply_filters('help_text_print', $couponprint) ; */
			echo apply_filters('TPRM_pdf_coupon_print', $couponprint);
		endif;
		
		?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>


	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'tprm-theme' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>




