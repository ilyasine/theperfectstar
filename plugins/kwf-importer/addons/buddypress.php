<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'buddypress/bp-loader.php' ) && !function_exists( 'bp_is_active' ) ){
	return;
}

class TPRM_importer_Buddypress{
	var $fields;
	var $profile_groups;
	var $plugin_path;

	function __construct(){
		$this->plugin_path = is_plugin_active( 'buddyboss-platform/bp-loader.php' ) ? WP_PLUGIN_DIR . "/buddyboss-platform/" : WP_PLUGIN_DIR . "/buddypress/";

		if( !class_exists( 'BP_XProfile_Group' ) )
			require_once( $this->plugin_path . "bp-xprofile/classes/class-bp-xprofile-group.php" );
		
		$this->profile_groups = $this->get_profile_groups();
		$this->fields = $this->get_fields();
	}
	
	function hooks(){
		add_filter( 'TPRM_importer_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		//add_action( 'TPRM_importer_tab_import_before_import_button', array( $this, 'show_compatibility' ) );
		add_action( 'TPRM_importer_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_filter( 'TPRM_importer_export_columns', array( $this, 'export_columns' ), 10, 1 );
		add_filter( 'TPRM_importer_export_data', array( $this, 'export_data' ), 10, 3 );
		add_action( 'post_TPRM_importer_import_single_user', array( $this, 'import' ), 10, 6 );	
		add_action( 'post_TPRM_importer_import_single_user', array( $this, 'import_avatar' ), 10, 3 );
	}

	function restricted_fields( $TPRM_importer_restricted_fields ){
		return array_merge( $TPRM_importer_restricted_fields, array( 'bp_group', 'bp_group_role', 'bp_avatar' ), $this->fields );
	}

	function get_profile_groups(){
		return BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
	}

	public function get_fields(){
		$buddypress_fields = array();
		
		if ( !empty( $this->profile_groups ) ) {
			 foreach ( $this->profile_groups as $profile_group ) {
				if ( !empty( $profile_group->fields ) ) {				
					foreach ( $profile_group->fields as $field ) {
						$buddypress_fields[] = $field->name;
					}
				}
			}
		}

		return $buddypress_fields;
	}

	function get_field_type( $field_name ){
		if ( !empty( $this->profile_groups ) ) {
			 foreach ( $this->profile_groups as $profile_group ) {
				if ( !empty( $profile_group->fields ) ) {				
					foreach ( $profile_group->fields as $field ) {
						if( $field_name == $field->name )
							return $field->type;
					}
				}
			}
		}
	}

	function get_type_import_help( $type ){
		switch( $type ){
			case 'datebox':
				$help = __( sprintf( 'Format should be like this: %s-01-01 00:00:00', date( 'Y' ) ), 'kwf-importer' );
				break;
				
			case 'checkbox':
				$help = __( 'If you use more than one value, please use ## to separate each item', 'kwf-importer' );
				break;
		}

		return empty( $help ) ? '' : " <em>($help)</em>";
	}

	function get_groups( $user_id ){
		if( !class_exists( "BP_Groups_Member" ) )
			require_once( $this->plugin_path . "bp-groups/classes/class-bp-groups-member.php" );

		$groups = BP_Groups_Member::get_group_ids( $user_id );
		return implode( ",", $groups['groups'] );
	}

	function get_member_type( $user_id ){
		$member_types = bp_get_member_type( $user_id, false );
		return ( is_array( $member_types ) ) ? implode( ",", $member_types ) : $member_types;
	}

	function show_compatibility(){
		?>
		<h2><?php _e( 'BuddyPress & BuddyBoss compatibility', 'kwf-importer'); ?></h2>
	
		<table class="form-table">
			<tbody>
			<tr class="form-field form-required">
				<th scope="row"><label><?php _e( 'BuddyPress/BuddyBoss users', 'kwf-importer' ); ?></label></th>
				<td><?php _e( 'You can insert any profile from BuddyPress using his name as header. Plugin will check, before import, which fields are defined in BuddyPress and will assign it in the update. You can use this fields:', 'kwf-importer' ); ?>
				<ul style="list-style:disc outside none;margin-left:2em;">
					<?php foreach ( $this->get_fields() as $buddypress_field ): 
						$type = $this->get_field_type( $buddypress_field ); 
					?>
					<li><?php echo $buddypress_field; ?> - <?php echo $type . $this->get_type_import_help( $type ); ?></li>
					<?php endforeach; ?>
				</ul>
				</td>					
			</tr>
			</tbody>
		</table>
		<?php
	}
	
	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( 'BuddyPress/BuddyBoss avatar', 'kwf-importer' ); ?></th>
			<td><?php _e( 'You can import users avatars using a column called <strong>bp_avatar</strong>, in this field you can place:', 'kwf-importer' ); ?>
			<ul style="list-style:disc outside none;margin-left:2em;">
					<li>An integer which identify the ID of an attachment uploaded to your media library</li>
					<li>A string that contain a path or an URL to the image</li>
				</ul>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( "BuddyPress or BuddyBoss is activated", 'kwf-importer' ); ?></th>
			<td><?php _e( "You can use the <strong>profile fields</strong> you have created and also you can set one or more groups for each user. For example:", 'kwf-importer' ); ?>
				<ul style="list-style:disc outside none; margin-left:2em;">
					<li><?php _e( "If you want to assign an user to a group you have to create a column 'bp_group' and a column 'bp_group_role'", 'kwf-importer' ); ?></li>
					<li><?php _e( "Then in each cell you have to fill with the BuddyPress <strong>group slug</strong>", 'kwf-importer' ); ?></li>
                    <li><?php _e( "And the role assigned in this group:", 'kwf-importer' ); ?>  <em>Administrator, Moderator or Member</em></li>
					<li><?php _e( "You can also use group ids if you know it using a column 'bp_group_id' instead of 'bp_group'", 'kwf-importer' ); ?></li>
					<li><?php _e( "You can do it with multiple groups at the same time using commas to separate different groups, in bp_group column, i.e.: <em>group_1, group_2, group_3</em>", 'kwf-importer' ); ?></li>
					<li><?php _e( "But you will have to assign a role for each group:", 'kwf-importer' ); ?> <em>Moderator,Moderator,Member,Member</em></li>
                    <li><?php _e( "If you choose to update roles and group role is empty, user will be removed from the group", 'kwf-importer' ); ?></li>
					<li><?php _e( "If you get some error of this kind:", 'kwf-importer' ); ?> <code>Fatal error: Class 'BP_XProfile_Group'</code> <?php _e( "please enable Buddypress Extended Profile then import the csv file. You can then disable this afterwards", 'kwf-importer' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
	}

	function export_columns( $row ){
		foreach ( $this->fields as $key ) {
			$row[ $key ] = $key;
		}

		$row['bp_group_id'] = 'bp_group_id';
		$row['bp_member_type'] = 'bp_member_type';

		return $row;
	}

	function export_data( $row, $user, $args ){
		$fields_to_export = ( count( $args['filtered_columns'] ) == 0 ) ? $this->fields : array_intersect( $this->fields, $args['filtered_columns'] );
		
        foreach( $fields_to_export as $key ) {
			$row[ $key ] = xprofile_get_field_data( $key, $user, 'comma' );
		}

		if( count( $args['filtered_columns'] ) == 0 || in_array( 'bp_group_id', $args['filtered_columns'] ) )
			$row['bp_group_id'] = $this->get_groups( $user );

		if( count( $args['filtered_columns'] ) == 0 || in_array( 'bp_member_type', $args['filtered_columns'] ) )
			$row['bp_member_type'] = $this->get_member_type( $user );

		return $row;
	}

	public function get_all_group_ids( $user_id, $limit = false, $page = false, $force_all = false ) {
		global $wpdb;
		static $cache = array();

		$pag_sql = '';

		$cache_key = 'bp_group_ids_for_user_' . $user_id . '_' . bp_loggedin_user_id();
		if ( ! empty( $limit ) && ! empty( $page ) ) {
			$pag_sql   = $wpdb->prepare( ' LIMIT %d, %d', intval( ( $page - 1 ) * $limit ), intval( $limit ) );
			$cache_key = 'bp_group_ids_for_user_' . $user_id . '_' . bp_loggedin_user_id() . '_' . $limit . '_' . $page;
		}

		$bp = buddypress();

		if ( ! isset( $cache[ $cache_key ] ) ) {
			// If the user is logged in and viewing their own groups, we can show hidden and private groups.
			if ( bp_loggedin_user_id() != $user_id ) {

				$where_sql = $wpdb->prepare( "m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id );
				if ( $force_all ) {
					$where_sql = $wpdb->prepare( 'm.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0', $user_id );
				}

				$group_sql    = "SELECT DISTINCT m.group_id FROM {$bp->groups->table_name_members} m, {$bp->groups->table_name} g WHERE {$where_sql}{$pag_sql}";
				$total_groups = $wpdb->get_var( "SELECT COUNT(DISTINCT m.group_id) FROM {$bp->groups->table_name_members} m, {$bp->groups->table_name} g WHERE {$where_sql}" );
			} else {
				$group_sql    = $wpdb->prepare( "SELECT DISTINCT group_id FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0{$pag_sql}", $user_id );
				$total_groups = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT group_id) FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0", $user_id ) );
			}

			$groups = $wpdb->get_col( $group_sql );

			$group_ids = array(
				'groups' => $groups,
				'total'  => (int) $total_groups,
			);

			$cache[ $cache_key ] = $group_ids;
		} else {
			$group_ids = $cache[ $cache_key ];
		}

		return $group_ids;
	}

	function import( $headers, $row, $user_id, $role, $positions, $form_data ){
        $update_roles_existing_users = isset( $form_data["update_roles_existing_users"] ) ? sanitize_text_field( $form_data["update_roles_existing_users"] ) : '';
		$update_user_groups = isset( $form_data["update_user_groups"] ) ? sanitize_text_field( $form_data["update_user_groups"] ) : '';

		foreach( $this->fields as $field ){
			$pos = array_search( $field, $headers );

			if( $pos === FALSE )
				continue;

			switch( $this->get_field_type( $field ) ){
				case 'datebox':
					$date = $row[$pos];
					switch( true ){
						case is_numeric( $date ):
							$UNIX_DATE = ($date - 25569) * 86400;
							$datebox = gmdate("Y-m-d H:i:s", $UNIX_DATE);break;
						case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-]([4567890]{1}\d{1})/',$date,$match):
							$match[3]='19'.$match[3];
						case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-](20[4567890]{1}\d{1})/',$date,$match):
						case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-](19[4567890]{1}\d{1})/',$date,$match):
							$datebox= ($match[3].'-'.$match[2].'-'.$match[1]);
							break;

						default:
							$datebox = $date;
					}

					$datebox = strtotime( $datebox );
					xprofile_set_field_data( $field, $user_id, date( 'Y-m-d H:i:s', $datebox ) );
					unset( $datebox );
					break;
				
				case 'checkbox':
					xprofile_set_field_data( $field, $user_id, explode( '##', $row[ $pos ] ) );
					break;

				default:
					xprofile_set_field_data( $field, $user_id, $row[$pos] );
			}	
		}

		$pos_bp_group = array_search( 'bp_group', $headers );
		$pos_old_group = array_search( 'old_group', $headers );
		$pos_role = array_search( 'role', $headers ); 

		// Remove user from old groups

		if ($pos_old_group !== FALSE) {
			$groups = explode('##', $row[$pos_old_group]);
		
			for ($j = 0; $j < count($groups); $j++) {
				$bp_group_slug = $groups[$j];
				$group_id = BP_Groups_Group::group_exists( $groups[ $j ] );
		
				// Get the BP Group data using the slug
				$bp_group = get_page_by_path($bp_group_slug, OBJECT, 'groups');		
		
				if ($bp_group && !empty( $group_id)) {

					// learndash group
					$ld_group_id = $bp_group->ID;

					// buddypress group
					$bp_group_id = BP_Groups_Group::get_id_from_slug($bp_group_slug);

					/* 
					* remove user from learndash group
					*/

					// get enrolled users in the group
					$ld_group_users = learndash_get_groups_user_ids($ld_group_id);

					// Check if the user is in the group
					if (in_array($user_id, $ld_group_users)) {
						// Remove the user from the group users array
						$ld_group_users = array_diff($ld_group_users, array($user_id));

						// Update the group users
						learndash_set_groups_users($ld_group_id, $ld_group_users);
					}

					/* 
					* remove user from buddypress group
					*/

					BP_Groups_Member::delete( $user_id, $bp_group_id );

				}
			}
		}
		

		// Remove user from all groups that are not present in csv
		if( $update_user_groups == 'yes' ){

			if( !class_exists( "BP_Groups_Member" ) )
			require_once( $this->plugin_path . "bp-groups/classes/class-bp-groups-member.php" );

			$user_ld_groups = learndash_get_users_group_ids( $user_id, true );
			$user_bp_groups = $this->get_all_group_ids( $user_id )["groups"];


			foreach($user_ld_groups as $user_ld_group){
				/* 
				* remove user from learndash group
				*/
				// get enrolled users in the group
				$ld_group_users = learndash_get_groups_user_ids($user_ld_group);

				// Check if the user is in the group
				if (in_array($user_id, $ld_group_users)) {
					// Remove the user from the group users array
					$ld_group_users = array_diff($ld_group_users, array($user_id));

					// Update the group users
					learndash_set_groups_users($ld_group_id, $ld_group_users);
				}
			}

			foreach($user_bp_groups as $user_bp_group){
				/* 
				* remove user from buddypress group
				*/
				BP_Groups_Member::delete( $user_id, $user_bp_group );
			}
		}

		if ($pos_bp_group !== FALSE) {
			$groups = explode('##', $row[$pos_bp_group]);

			$roles = explode(',', $row[$pos_role]); 

			// Initialize a variable to store the role from the previous iteration
			$previous_role = null;
		
			for ($j = 0; $j < count($groups); $j++) {
				$bp_group_slug = $groups[$j];
				$group_id = BP_Groups_Group::group_exists( $groups[ $j ] );
				//$role = $roles[ $j ];
				$role = $row[$pos_role];
		
				// Get the BP Group data using the slug
				$bp_group = get_page_by_path($bp_group_slug, OBJECT, 'groups');		

				// If the current role is empty, use the previous role (if available)
				if (empty($role) && !empty($previous_role)) {
					$role = $previous_role;
				}
				
				// Update the previous_role variable with the current role
				$previous_role = $role;
							
				$subgroups = bp_get_descendent_groups( $group_id, '' );

				/* echo '<pre>';
				var_dump( $subgroups );
				echo '</pre>'; */

				$ecole_name = groups_get_groupmeta( $group_id, 'ecole_name' ) ;
		
				if ($bp_group && !empty( $group_id)) {

					$ld_group_id = $bp_group->ID;		
					
					$bp_group_id = BP_Groups_Group::get_id_from_slug($bp_group_slug);

					$group_type = bp_groups_get_group_type( $bp_group_id );

					$args = array(
						'post_type'      => 'sfwd-courses',
						'post_status'    => 'publish',
						'tax_query'      => array(
							array(
								'taxonomy' => 'ld_course_category',
								'field'    => 'slug',
								'terms'    => $group_type,
							)						
						),
					);

					$courses = get_posts($args);
								
					if ($role == 'school-admin') {
						update_user_meta($user_id, 'learndash_group_leaders_' . $ld_group_id, $ld_group_id);
						update_user_meta($user_id, 'ecole', $ecole_name);
						bp_set_member_type($user_id, 'school-administrator');
						// Loop through subgroups and make the director an admin
						foreach ($subgroups as $subgroup) {
							$subgroup_id = $subgroup->id;
							$this->add_user_group($user_id, $subgroup_id, 'Administrator', $update_roles_existing_users);	
						}
						$this->add_user_group($user_id, $group_id, 'Administrator', $update_roles_existing_users);			
					} elseif ($role == 'director') {
						update_user_meta($user_id, 'learndash_group_leaders_' . $ld_group_id, $ld_group_id);
						update_user_meta($user_id, 'ecole', $ecole_name);
						bp_set_member_type($user_id, 'director');
						// Loop through subgroups and make the director an admin
						foreach ($subgroups as $subgroup) {
							$subgroup_id = $subgroup->id;
							$this->add_user_group($user_id, $subgroup_id, 'Administrator', $update_roles_existing_users);	
						}
						$this->add_user_group($user_id, $group_id, 'Administrator', $update_roles_existing_users);							
					} elseif ($role == 'teacher') {
						update_user_meta($user_id, 'learndash_group_leaders_' . $ld_group_id, $ld_group_id);
						update_user_meta($user_id, 'ecole', $ecole_name);
						bp_set_member_type($user_id, 'teacher');						
						$this->add_user_group($user_id, $group_id, 'Administrator', $update_roles_existing_users);								
					} elseif ($role == 'student') {
						update_user_meta($user_id, 'learndash_group_users_' . $ld_group_id, $ld_group_id);
						update_user_meta($user_id, 'ecole', $ecole_name);
						bp_set_member_type($user_id, 'student');
						$this->add_user_group($user_id, $group_id, 'Member', $update_roles_existing_users);						
					}

					foreach ($courses as $course) {

						$course_id = $course->ID;

						$group_members = groups_get_group_members( array(
							'group_id'            => $bp_group_id,
							'per_page'            => false,
							'page'                => false,
							'exclude_admins_mods' => false,
							'exclude_banned'      => true,
							'exclude'             => false,
							'group_role'          => array('admin'),
							'search_terms'        => false,
							'type'                => 'first_joined') );
							;
							
							$group_leaders = $group_members['members'];

							foreach( $group_leaders as $group_leader ){	
								$teacher = $group_leader->ID;
								ld_update_course_access($teacher, $course_id, false);
							}

					}

				}
			}
		}
		

        /* if( $pos_bp_group_id !== FALSE ){
			$groups_id = explode( ',', $row[ $pos_bp_group_id ] );
			$groups_role = explode( ',', $row[ $pos_bp_group_role ] );

            for( $j = 0; $j < count( $groups_id ); $j++ ){
				$group_id = intval( $groups_id[ $j ] );

				if( !empty( $group_id ) ){
					$this->add_user_group( $user_id, $group_id, $groups_role[ $j ], $update_roles_existing_users );
				}
			}
		} */
			
		$pos_member_type = array_search( 'bp_member_type', $headers );
		if( $pos_member_type !== FALSE ){
			bp_set_member_type( $user_id, $row[$pos_member_type] );
		}

		//Import Admin

	}
	

    function add_user_group( $user_id, $group_id, $group_role, $update_roles_existing_users ){
        if( $update_roles_existing_users == 'yes' || $update_roles_existing_users == 'yes_no_override' ){
            $member = new BP_Groups_Member( $user_id, $group_id );
            $member->remove();
        }
        
        if( ( $update_roles_existing_users == 'yes' || $update_roles_existing_users == 'yes_no_override' ) && empty( $group_role ) )
            return;

        groups_join_group( $group_id, $user_id );

        if( $group_role == 'Moderator' ){
            groups_promote_member( $user_id, $group_id, 'mod' );
        }
        elseif( $group_role == 'Administrator' ){
            groups_promote_member( $user_id, $group_id, 'admin' );
        }
    }

	function import_avatar( $headers, $row, $user_id ){
		$pos = array_search( 'bp_avatar', $headers );

		if( $pos === FALSE )
			return;

		$this->import_avatar_raw( $user_id, $row[ $pos ] );
	}

	function import_avatar_raw( $user_id, $source ){
		$avatar_dir = bp_core_avatar_upload_path() . '/avatars';

		if ( ! file_exists( $avatar_dir ) ) {
			if ( ! wp_mkdir_p( $avatar_dir ) ) {
				return false;
			}
		}

		$avatar_folder_dir = apply_filters( 'bp_core_avatar_folder_dir', $avatar_dir . '/' . $user_id, $user_id, 'user', 'avatars' );

		if ( ! is_dir( $avatar_folder_dir ) ) {
			if ( ! wp_mkdir_p( $avatar_folder_dir ) ) {
				return false;
			}
		}

		$original_file = $avatar_folder_dir . '/import-export-users-customers-bp-avatar-' . $user_id . '.png';
		$data = ( (string)(int)$source == $source ) ? file_get_contents( get_attached_file( $source ) ) : file_get_contents( $source );
		
		if ( file_put_contents( $original_file, $data ) ) {
			$avatar_to_crop = str_replace( bp_core_avatar_upload_path(), '', $original_file );

			$crop_args = array(
				'item_id'       => $user_id,
				'original_file' => $avatar_to_crop,
				'crop_x'        => 0,
				'crop_y'        => 0,
			);

			return bp_core_avatar_handle_crop( $crop_args );
		} else {
			return false;
		}
	}
}
$TPRM_importer_buddypress = new TPRM_importer_Buddypress();
$TPRM_importer_buddypress->hooks();