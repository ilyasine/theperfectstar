<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Advance_List extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'list_items';
	}

    public function get_fields() {
		return [ 'list_title','list_description','list_badge_text'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'list_title':
				return esc_html__( 'Advance List : Title', 'wts-eae' );

			case 'list_description':
				return esc_html__( 'Advance List : Description', 'wts-eae' );
			case 'list_badge_text':
					return esc_html__( 'Advance List : Badge Text', 'wts-eae' );		

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'list_title':
				return 'LINE';

			case 'list_description':
				return 'LINE';

			case 'list_badge_text':
				return 'LINE';		

            default:
				return '';
		}
	}


}