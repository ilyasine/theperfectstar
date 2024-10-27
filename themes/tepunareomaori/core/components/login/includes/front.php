<?php 

add_action('wp_head', 'hide_bb_signup_button');
add_filter('bp_static_pages', 'TPRM_static_pages');
add_filter('gettext', 'TPRM_login_placeholder_username', 20, 3 );
add_action('login_head', 'TPRM_help_for_connection', 150 );
add_filter('bp_core_change_privacy_policy_link_on_private_network', 'display_help_for_first_connection_page', 10, 2);
add_filter('wp_die_handler', 'TPRM_wp_die_handler');
/*add_action("login_form_lostpassword", 'disable_lost_password_action');
 if( ! current_user_can('administrator') ) :
	add_filter( 'show_password_fields', 'TPRM_lost_pass_disable'  );
	add_filter( 'allow_password_reset', 'TPRM_lost_pass_disable' );
	add_filter( 'gettext', 'TPRM_lost_pass_remove' );
endif; */


/* 
* *** login hooks callbacks ***
*/

/**
 * @global function to check if we are in the login page endpoint
 *
 * @since V2
 */

 function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

/**
 * Change placehader Email to username
 *
 * @since V2
 */

function TPRM_login_placeholder_username( $translated_text, $text, $domain ) {
    if ( is_login_page() && $text == 'Email Address' ) {
		if( $_SERVER['REQUEST_URI'] == '/en/access/' ){
			$translated_text = 'Username';
		}
		if( $_SERVER['REQUEST_URI'] == '/fr/access/' ){
			$translated_text = 'Nom d\'utilisateur';
		}
    }
    return $translated_text;
}


/**
 * Add Help for the first connection bp page
 *
 * @since V2
 */

function TPRM_static_pages($static_pages) {

	$TPRM_static_pages = array(
		'first'  => __( 'Help for the first connection', 'tprm-theme' ),
	);

	$static_pages = array_merge( $TPRM_static_pages, $static_pages );

	return $static_pages;
	
} 

// Restrict user registration
 update_option( 'users_can_register' , false );

 /**
 * Display help for connection popup in login page
 *
 * @since V2
 */

function TPRM_help_for_connection(){


    $html = "<script>";
    $html .= "jQuery( document ).ready( function () {";
    $html .= "jQuery('.login #login_error a').remove();";
    $html = "<script>";
    $html .= "jQuery( document ).ready( function () {";
    $html .= "    jQuery('.login #login_error a').remove();";
    $html .= "    jQuery('.login-heading').after(";
    $html .= "        `<div class='welcome-div'>";
    $html .= "        </div>`";
    $html .= "    );";
    $html .= "    jQuery('#login').append(";
    $html .= "        `<p class='support-div'>";
    $html .= "            " . esc_html__( 'If you are having trouble logging in, please contact our support team at kiaora@tepunareomaori.co.nz', 'tprm-theme' ) . "";
    $html .= "        </p>`";
    $html .= "    );";
    $html .= "} )</script>";
    

    echo wp_kses(
        $html,
        array(
            'script' => array(
                'type' => array(),
            ),
            'div' => array(
                'class' => array(),
            ),
            'h3' => array(),
            'i' => array(
                'class' => array(),
            ),
            'a' => array(
                'class' => array(),
                'onclick' => array(),
                'href' => array(),
            ),
            'p' => array(
                'class' => array(),
            ),
        )
    );

}

/**
 * @since V2
 *
 * Change the die message handle
 */ 

function TPRM_wp_die_handler() {
    ?>
    <style>
        a {
            color: #2e9e9e !important;
        }
		a:hover, a:active {
			color: #1c5cb2 !important;
		}
		#error-page {
			margin-top: 50px;
			height: fit-content;
			text-align: center;
		}
    </style>

	<title><?php _e('tepunareomaori Error', 'tprm-theme') ?></title>

    <?php
}


 /**
 * Disable login password action
 *
 * @since V2
 */

function disable_lost_password_action() {

	wp_die(TPRM_SUPPORT);

}

 /**
 * Display help for first connection page
 *
 * @since V2
 */

function display_help_for_first_connection_page($link, $privacy_policy_url) {

	$page_ids = bp_core_get_directory_page_ids('all');
	$first    = isset( $page_ids['first'] ) ? $page_ids['first'] : false;

	// Do not show the page if page is not published.
	if ( false !== $first && 'publish' !== get_post_status( $first ) ) {
		$first = false;
	}

    if (!empty($first)) {
        $page_title = !empty($first) ? get_the_title($first) : '';
        $get_first = get_post($first);		
		$get_first_content = $get_first->post_content;
        $first_link = sprintf(
            '<a class="first-link popup-modal-login popup-first" style="display:none;" href="%s">%s</a><div id="first-modal" class="mfp-hide login-popup bb-modal"><h1>%s</h1>%s<button title="%s" type="button" class="mfp-close">%s</button></div>',
            '#first-modal',
            $page_title,
            $page_title,
            $get_first_content,
            esc_html('Close (Esc)'),
            esc_html('×')
        );

        $link .= $first_link;
    }

    return $link;
}


/**
 * Disable lost password
 *
 * @since V2
 */

function TPRM_lost_pass_disable() {
	if ( is_admin() ) {
		$userdata = wp_get_current_user();
		$user = new WP_User($userdata->ID);
		if ( !empty( $user->roles ) && is_array( $user->roles ) && $user->roles[0] == 'administrator' )
		return true;
	}
	return false;
}
  
/**
 * Remove lost password text
 *
 * @since V2
 */
function TPRM_lost_pass_remove($text) {
	// Define an array of text strings to remove
	$strings_to_remove = array('Lost your password?', 'Lost your password', 'Mot de passe oublié?', 'Mot de passe oublié','Forgot Password');

	// Replace all instances of the specified text with an empty string
	return str_replace($strings_to_remove, '', trim($text, '?'));
}

/**
 * Hide login button if logged in
 *
 * @since V2
 */
function hide_bb_signup_button() {   
    $style_markup = '<style>.bb-header-buttons .button.signup { display: none; }</style>';

    echo wp_kses(
        $style_markup,
        array(
            'style' => array(),
        )
    );
      
}

/**
 * Modify the 'and' text in the privacy policy link.
 *
 * @since V2
 */
function modify_privacy_policy_link_and_text( $link, $privacy_policy_url ) {
    // Replace the plain 'and' with a span that has the grey-and class.
    $link = str_replace( ' and ', ' <span class="grey-and">and</span> ', $link );
    
    return $link;
}
add_filter( 'bp_core_change_privacy_policy_link_on_private_network', 'modify_privacy_policy_link_and_text', 15, 2 );

