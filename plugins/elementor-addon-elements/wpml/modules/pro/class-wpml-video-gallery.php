<?php
namespace WTS_EAE;

use WPML_Elementor_Module_With_Items;

class WPML_EAE_Video_Gallery extends WPML_Elementor_Module_With_Items {

	public function get_items_field() {
        return 'vg_video_Gallery';
	}

	public function get_fields() {
        return [ 'vg_title','vg_description','vg_video_category'];
	}

	protected function get_title( $field ) {

		switch ( $field ) {
			case 'vg_title':
				return esc_html__( 'Video Gallery : Title', 'wts-eae' );
            case 'vg_description':
                return esc_html__( 'Video Gallery : Description', 'wts-eae' );
            case 'vg_video_category':
                return esc_html__( 'Video Gallery : Filter Category', 'wts-eae' );
			default:
				return '';
		}
	}

    protected function get_editor_type( $field ) {
        
		switch ( $field ) {

			case 'vg_title':
				return 'LINE';
            case 'vg_description':
                return 'LINE';
            case 'vg_video_category':
                return 'LINE';
        
			default:
				return '';
		}
	}
}