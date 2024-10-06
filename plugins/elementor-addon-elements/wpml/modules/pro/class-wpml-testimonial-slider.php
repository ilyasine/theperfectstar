<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Testimonial_Slider extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'testimonial_data';
	}

    public function get_fields() {
		return [ 'author','designation','company_name','description'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'author':
				return esc_html__( 'Testimonial Slider : Author', 'wts-eae' );

			case 'designation':
				return esc_html__( 'Testimonial Slider : Designation', 'wts-eae' );

			case 'company_name':
				return esc_html__( 'Testimonial Slider : Company Name', 'wts-eae' );

			case 'description':
				return esc_html__( 'Testimonial Slider : Description', 'wts-eae' );

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'author':
				return 'LINE';
			case 'designation':
				return 'LINE';
			case 'company_name':
				return 'LINE';
			case 'description':
				return 'AREA';

            default:
				return '';
		}
	}


}