<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Team_Member extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'tm_content';
	}

    public function get_fields() {
		return [ 'tm_name','tm_designation','tm_description','tm_button_text'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'tm_name':
				return esc_html__( 'Team Member : Name', 'wts-eae' );

			case 'tm_designation':
				return esc_html__( 'Team Member : Designation', 'wts-eae' );

			case 'tm_description':
				return esc_html__( 'Team Member : Description', 'wts-eae' );

			case 'tm_button_text':
				return esc_html__( 'Team Member : Button Text', 'wts-eae' );

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'faq_question':
				return 'LINE';

			case 'faq_answer':
				return 'LINE';
                
			case 'faq_question':
				return 'AREA';

			case 'faq_answer':
				return 'AREA';

            default:
				return '';
		}
	}


}