<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ){
	return;
}

class TPRM_importer_WPML{
	function __construct(){
    }

    function hooks(){
		add_filter( 'TPRM_importer_import_email_body_source', array( $this, 'translated_body' ), 10, 5 );
		add_filter( 'TPRM_importer_import_email_subject_source', array( $this, 'translated_subject' ), 10, 5 );
	}

	function get_translated_template( $template_id, $locale ){
		$translated_template_id = apply_filters( 'wpml_object_id', $template_id, 'TPRM_importer_email_template', false, $locale );

		if( empty( $translated_template_id ) )
			return false;

		return get_post( $translated_template_id );
	}
	
	function translated_body( $body, $headers, $data, $created, $user_id ){
		$locale = TPRM_importer_Helper::get_value_from_row( 'locale', $headers, $data );

		if( empty( $locale ) )
			$locale = apply_filters( 'wpml_current_language', NULL );

		$template_id = get_option( "TPRM_importer_mail_template_id" );
		$locale = substr( $locale, 0, 2 );

		$translated_template = $this->get_translated_template( $template_id, $locale );
		
		if( empty( $translated_template ) )
			return $body;

		return $translated_template->post_content;		
	}

	function translated_subject( $body, $headers, $data, $created, $user_id ){
		$locale = TPRM_importer_Helper::get_value_from_row( 'locale', $headers, $data, $user_id );

		if( empty( $locale ) )
			return $body;

		$template_id = get_option( "TPRM_importer_mail_template_id" );
		$locale = substr( $locale, 0, 2 );

		$translated_template = $this->get_translated_template( $template_id, $locale );
		
		if( empty( $translated_template ) )
			return $body;

		return $translated_template->post_title;		
	}

	
}

$TPRM_importer_wpml = new TPRM_importer_WPML();
$TPRM_importer_wpml->hooks();