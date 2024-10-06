<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Info_Group extends WPML_Elementor_Module_With_Items {

    public function get_items_field() {
		return 'list_items';
	}

    public function get_fields() {
		return [ 'title','sub_title','short_description','content','button_text','active_button_text'];
	}

    protected function get_title( $field ) {

		switch ( $field ) {

			case 'title':
				return esc_html__( 'Info Group : Title', 'wts-eae' );

			case 'sub_title':
				return esc_html__( 'Info Group : Sub Title', 'wts-eae' );

			case 'short_description':
				return esc_html__( 'Info Group : Short Description', 'wts-eae' );

			case 'content':
				return esc_html__( 'Info Group : Content', 'wts-eae' );

			case 'button_text':
				return esc_html__( 'Info Group : Button Text', 'wts-eae' );
			
			case 'active_button_text':
				return esc_html__( 'Info Group : After Button Text', 'wts-eae' );
			
			default:
				return '';

		}
	}

    protected function get_editor_type( $field ) {

		switch ( $field ) {

			case 'title':
				return 'LINE';
			case 'sub_title':
				return 'LINE';
			case 'short_description':
				return 'AREA';
			case 'content':
				return 'VISUAL';
			case 'button_text':
				return 'LINE';
			case 'active_button_text':
				return 'LINE';

            default:
				return '';
		}
	}


}