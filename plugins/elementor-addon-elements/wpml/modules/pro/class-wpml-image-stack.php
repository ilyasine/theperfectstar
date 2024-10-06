<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Image_Stack extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'content_images';
	}

    public function get_fields() {
		return [ 'content_type_text','tooltip'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'content_type_text':
				return esc_html__( 'Image Stack : Text', 'wts-eae' );

			case 'tooltip':
				return esc_html__( 'Image Stack : Tooltip', 'wts-eae' );
			
			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'content_type_text':
				return 'LINE';
			case 'tooltip':
				return 'LINE';

            default:
				return '';
		}
	}


}