<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Image_Accordion extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'items';
	}

    public function get_fields() {
		return [ 'title','description', 'link_text'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'title':
				return esc_html__( 'Image Accordion : Title', 'wts-eae' );

			case 'description':
				return esc_html__( 'Image Accordion : Description', 'wts-eae' );

			case 'link_text':
				return esc_html__( 'Image Accordion : Button Text', 'wts-eae' );

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'title':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'link_text':
				return 'LINE';

            default:
				return '';
		}
	}


}