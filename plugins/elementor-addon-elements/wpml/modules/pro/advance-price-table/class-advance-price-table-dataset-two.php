<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Advance_Price_Table_Two extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'pt_items_2';
	}

    public function get_fields() {
		return [ 'pt_features'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'pt_features':
				return esc_html__( 'Advance Price Table : Features List 2', 'wts-eae' );

			default:
				return '';
		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'pt_features':
				return 'LINE';

            default:
				return '';
		}
	}


}