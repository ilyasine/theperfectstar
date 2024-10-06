<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Image_Hotspot extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'markers';
	}

    public function get_fields() {
		return [ 'heading','tooltip_heading','tooltip_short_description','tooltip_description','tooltip_button_text'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'heading':
				return esc_html__( 'Image Hotspot : Text', 'wts-eae' );

			case 'tooltip_heading':
				return esc_html__( 'Image Hotspot : Heading', 'wts-eae' );
			case 'tooltip_short_description':
				return esc_html__( 'Image Hotspot : Short Description', 'wts-eae' );

			case 'tooltip_description':
				return esc_html__( 'Image Hotspot : Description', 'wts-eae' );

			case 'tooltip_button_text':
				return esc_html__( 'Image Hotspot : Button Text', 'wts-eae' );

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'heading':
				return 'LINE';
			case 'tooltip_heading':
				return 'LINE';
			case 'tooltip_short_description':
				return 'AREA';
			case 'tooltip_description':
				return 'AREA';
			case 'tooltip_button_text':
				return 'LINE';
            default:
				return '';
		}
	}


}