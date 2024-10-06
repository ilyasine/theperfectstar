<?php 

/* 
* *** woocommerce custom hooks ***
*/
//TODO uncomment in prod
if( ! current_user_can('administrator') ) :
	add_filter('hide_coupon', '__return_true');
	if( is_active_member() ){
		add_filter('hide_cart', '__return_true');
	}
endif;
add_filter('wc_add_to_cart_message', 'TPRM_hide_view_cart', 10, 2);
add_filter('woocommerce_show_admin_notice', '__return_false');
remove_action( 'admin_notices', 'woothemes_updater_notice' );
add_filter('woocommerce_currency_symbol', 'change_dh_currency_symbol', 10, 2);

/**
 * Change moroccan dirham currency symbol
 *
 * @since V2
 */

/* 
* *** woocommerce hooks functions ***
*/

/**
 * hide view cart on checkout page
 *
 * @since V2
 */
function TPRM_hide_view_cart($message, $product_id){
	$message = '';
	return $message;
}

function change_dh_currency_symbol( $currency_symbol, $currency ) {
    switch( $currency ) {
         case 'MAD': $currency_symbol = 'DH'; 
         break;
    }
    return $currency_symbol;
}

/**
 * 
 * @global function check if the current page is ecommerce page
 * 
 * @since V2
 */

 function TPRM_is_ecom(){

    // Check if WooCommerce is activated
    if ( class_exists( 'WooCommerce' ) ) {

        // WooCommerce is activated, so you can use its functions
        if ( 	
            is_cart() || 
            is_checkout() || 
            is_woocommerce() || 
            is_account_page() || 
            is_wc_endpoint_url() || 
            is_page('abonnement') ||
            is_page('subscription') )	
        {  
            return true; 
        }
    }

    // WooCommerce is not activated or the condition is false
    return false;
}

 /**
 * @global function Get Current Year membership plan ID.
 *
 * @since V3
 * @return int|bool Plan ID if exsist, false otherwise.
 */

function get_this_year_membership_id() {
    $plan = get_membership_id_by_slug('access-' . get_option('school_year'));

    return $plan;
}

 /**
 * @global function Get Specific membership plan ID from slug.
 *
 * @since V3
 * @return int|bool Plan ID if exsist, false otherwise.
 */

 function get_membership_id_by_slug( $plan_slug ) {
    $plan = get_page_by_path( $plan_slug, OBJECT, 'wc_membership_plan' );

    if ( $plan ) {
        return $plan->ID;
    }

    return false;
}