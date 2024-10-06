<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'wp-access-areas/wp-access-areas.php' ) ){
	return;
}

class TPRM_importer_WP_Access_Area{
	function __construct(){
    }

    function hooks(){
        add_filter( 'TPRM_importer_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
        add_action( 'post_TPRM_importer_import_single_user', array( $this, 'import' ), 10, 3 );
    }

    function restricted_fields( $TPRM_importer_restricted_fields ){
		return array_merge( $TPRM_importer_restricted_fields, array( 'wp-access-areas' ) );
    }
    
    function import( $headers, $row, $user_id ){
		$pos = array_search( 'wp-access-areas', $headers );

		if( $pos === FALSE )
			return;

        $wpaa_labels = WPAA_AccessArea::get_available_userlabels(); 
        $active_labels = array_map( 'trim', explode( "#", $row[ $pos ] ) );

        foreach( $wpaa_labels as $wpa_label ){
            if( in_array( $wpa_label->cap_title , $active_labels )){
                $this->set_cap_for_user( $wpa_label->capability , $user_object , true );
            }
            else{
                $this->set_cap_for_user( $wpa_label->capability , $user_object , false );
            }
        }
    }
    
    function set_cap_for_user( $capability , &$user , $add ) {
        $has_cap = $user->has_cap( $capability );
        $is_change = ($add && ! $has_cap) || (!$add && $has_cap);
        if ( $is_change ) {
            if ( $add ) {
                $user->add_cap( $capability , true );
                do_action( 'wpaa_grant_access' , $user , $capability );
                do_action( "wpaa_grant_{$capability}" , $user );
            } else if ( ! $add ) {
                $user->remove_cap( $capability );
                do_action( 'wpaa_revoke_access' , $user , $capability );
                do_action( "wpaa_revoke_{$capability}" , $user );
            }
        }
    }
}

$TPRM_importer_wp_access_area = new TPRM_importer_WP_Access_Area();
$TPRM_importer_wp_access_area->hooks();