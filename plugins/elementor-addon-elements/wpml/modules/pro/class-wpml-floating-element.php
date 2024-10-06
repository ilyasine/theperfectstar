<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Floating_Element extends WPML_Elementor_Module_With_Items {

	public function get_items_field() {
        return 'content_images';
	}

	public function get_fields() {
        return [ 'content_type_text','content_title_custom'];
	}

	protected function get_title( $field ) {

		switch ( $field ) {
			case 'content_type_text':
				return esc_html__( 'Floating Element : Text', 'wts-eae' );
            case 'content_title_custom':
                return esc_html__( 'Floating Element : Title', 'wts-eae' );
			default:
				return '';
		}
	}

    protected function get_editor_type( $field ) {
        
		switch ( $field ) {

			case 'content_type_text':
				return 'LINE';
            case 'content_title_custom':
                return 'LINE';
        
			default:
				return '';
		}
	}
}