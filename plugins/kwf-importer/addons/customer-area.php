<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'customer-area/customer-area.php' ) || !is_plugin_active( 'customer-area-managed-groups/customer-area-managed-groups.php' ) ){
	return;
}

class TPRM_importer_CustomerArea{
	function __construct(){
		add_filter( 'TPRM_importer_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'TPRM_importer_documentation_after_plugins_activated', array( $this, 'after_plugins_activated' ) );
		add_action( 'post_TPRM_importer_import_single_user', array( $this, 'post_import_single_user' ), 10, 3 );
	}

	function restricted_fields( $TPRM_importer_restricted_fields ){
		return array_merge( $TPRM_importer_restricted_fields, array( 'customer_area_groups' ) );
	}
	
	function after_plugins_activated(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "WP Customer Area Managed Groups is activated", 'kwf-importer' ); ?></th>
			<td>
				<?php _e( "You can import user groups and assign them to the users using the next format", 'kwf-importer' ); ?>.
				<ul style="list-style:disc outside none; margin-left:2em;">
					<li><?php _e( "customer_area_groups as the column title", 'kwf-importer' ); ?></li>
					<li><?php _e( "The value of each cell will be the slug of the group", 'kwf-importer' ); ?></li>
					<li><?php _e( "If you want to import multiple values, you can use a list using commas to separate items", 'kwf-importer' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
	}
	
	function post_import_single_user( $headers, $row, $user_id ){
		$pos = array_search( 'customer_area_groups', $headers );
	
		if( $pos === FALSE )
			return;
	
		$user_groups = explode( ',', $row[ $pos ] );
		$user_groups = array_filter( $user_groups, function( $value ){ return $value !== ''; } );
		$new_group_ids = array();
	
		foreach ( $user_groups as $user_group ) {
			$group = get_page_by_path( $user_group, OBJECT, 'cuar_user_group' );
	
			if( is_object( $group ) )
				$new_group_ids[] = $group->ID;
			else{
				echo "$group is not a name of a group in Customer Area<br/>";
				return;
			}
		}
	
		$object_addon = new CUAR_UserGroupAddOn();
		$user_groups = $object_addon->get_groups_of_user( $user_id );
			
		// Remove from current groups that are not selected anymore 
		foreach ( $user_groups as $group ) {
			if ( !in_array( $group->ID, $new_group_ids ) ) {
				$object_addon->remove_user_from_group( $user_id, $group->ID );
			}
		}
		
		// Add to all groups 
		foreach ( $new_group_ids as $new_group_id ) {
			$object_addon->add_user_to_group( $user_id, $new_group_id );
		}
	}
}