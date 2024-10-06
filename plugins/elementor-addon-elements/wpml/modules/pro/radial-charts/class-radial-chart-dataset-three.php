<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Radial_Chart_Dataset_Three extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'chart_data_3';
	}

    public function get_fields() {
		return [ 'chart_label'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'chart_label':
				return esc_html__( 'Radial Chart : Dataset Label', 'wts-eae' );

			default:
				return '';
		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'chart_label':
				return 'LINE';

            default:
				return '';
		}
	}


}