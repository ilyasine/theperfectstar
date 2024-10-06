<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'woocommerce-membership/woocommerce-membership.php' ) ){
	return;
}

add_filter( 'TPRM_importer_restricted_fields', 'TPRM_importer_wmr_restricted_fields', 10, 1 );
add_action( 'TPRM_importer_documentation_after_plugins_activated', 'TPRM_importer_wmr_documentation_after_plugins_activated' );
add_action( 'post_TPRM_importer_import_single_user', 'TPRM_importer_wmr_post_import_single_user', 10, 3 );

function TPRM_importer_wmr_restricted_fields( $TPRM_importer_restricted_fields ){
	return array_merge( $TPRM_importer_restricted_fields, array( 'plan_id' ) );
}

function TPRM_importer_wmr_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "WooCommerce Membership by RightPress is activated", 'kwf-importer' ); ?></th>
		<td>
			<ol>
				<li><strong><?php _e( "Add users to membership plans", 'kwf-importer' ); ?></strong>: <?php _e( "In this case you will only have to use <strong>plan_id</strong> column in order to associate a user to their membership plan", 'kwf-importer' ); ?>.</li>
			</ol>
		</td>
	</tr>
	<?php
}

function TPRM_importer_wmr_post_import_single_user( $headers, $row, $user_id ){
	$pos = array_search( 'plan_id', $headers );

	if( $pos === FALSE )
		return;

	$plan_id = absint( $row[ $pos ] );
	$resultado = WooCommerce_Membership_Plan::add_member( $plan_id, $user_id );
}