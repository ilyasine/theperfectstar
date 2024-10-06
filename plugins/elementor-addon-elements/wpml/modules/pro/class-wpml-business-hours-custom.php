<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Business_Hours_Custom extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'content_list_custom';
	}

    public function get_fields() {
		return [ 'list_content_cus','eae_list_content_closed_text_cus', 'eae_business_label_cus1', 'eae_business_label_cus2', 'eae_business_label_cus3', 'eae_business_label_cus4', 'eae_business_label_cus5'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

            case 'list_content_cus':
				return esc_html__( 'Business Hours Custom : Text', 'wts-eae' );

			case 'eae_list_content_closed_text_cus':
				return esc_html__( 'Business Hours Custom : Closed Text', 'wts-eae' );

			case 'eae_business_label_cus1':
				return esc_html__( 'Business Hours Custom : Slot Label 1', 'wts-eae' );

			case 'eae_business_label_cus2':
				return esc_html__( 'Business Hours Custom : Slot Label 2', 'wts-eae' );

			case 'eae_business_label_cus3':
				return esc_html__( 'Business Hours Custom : Slot Label 3', 'wts-eae' );

			case 'eae_business_label_cus4':
				return esc_html__( 'Business Hours Custom : Slot Label 4', 'wts-eae' );

			case 'eae_business_label_cus5':
				return esc_html__( 'Business Hours Custom : Slot Label 5', 'wts-eae' );

			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {
			case 'list_content_cus':
				return 'LINE';

			case 'eae_list_content_closed_text_cus':
				return 'LINE';
                
			case 'eae_business_label_cus1':
				return 'LINE';

			case 'eae_business_label_cus2':
				return 'LINE';

			case 'eae_business_label_cus3':
				return 'LINE';

			case 'eae_business_label_cus4':
				return 'LINE';

			case 'eae_business_label_cus5':
				return 'LINE';

            default:
				return '';
		}
	}


}