<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Business_Hours extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'content_list_add';
	}

    public function get_fields() {
		return [ 'list_content','eae_list_content_closed_text', 'eae_business_label1', 'eae_business_label2', 'eae_business_label3', 'eae_business_label4', 'eae_business_label5'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

            case 'list_content':
				return esc_html__( 'Business Hours : Text', 'wts-eae' );

			case 'eae_list_content_closed_text':
				return esc_html__( 'Business Hours : Closed Text', 'wts-eae' );

			case 'eae_business_label1':
				return esc_html__( 'Business Hours : Slot Label 1', 'wts-eae' );

			case 'eae_business_label2':
				return esc_html__( 'Business Hours : Slot Label 2', 'wts-eae' );

			case 'eae_business_label3':
				return esc_html__( 'Business Hours : Slot Label 3', 'wts-eae' );

			case 'eae_business_label4':
				return esc_html__( 'Business Hours : Slot Label 4', 'wts-eae' );

			case 'eae_business_label5':
				return esc_html__( 'Business Hours : Slot Label 5', 'wts-eae' );

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'list_content':
				return 'LINE';

			case 'eae_list_content_closed_text':
				return 'LINE';
                
			case 'eae_business_label1':
				return 'LINE';

			case 'eae_business_label2':
				return 'LINE';

			case 'eae_business_label3':
				return 'LINE';

			case 'eae_business_label4':
				return 'LINE';

			case 'eae_business_label5':
				return 'LINE';

            default:
				return '';
		}
	}


}