<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_FAQ extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'faqs';
	}

    public function get_fields() {
		return [ 'faq_question','faq_answer'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'faq_question':
				return esc_html__( 'FAQ : FAQ Question', 'wts-eae' );

			case 'faq_answer':
				return esc_html__( 'FAQ : FAQ Answer', 'wts-eae' );

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'faq_question':
				return 'LINE';
			case 'faq_answer':
				return 'AREA';
            default:
				return '';
		}
	}


}