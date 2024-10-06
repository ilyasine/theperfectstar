<?php

namespace WTS_EAE\Pro\Modules\ImageScroll;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'ImageScroll',
		];
	}

	public function get_name() {
		return 'eae-image-scroll';
	}

	public function get_title() {

		return __( 'Image Scroll', 'wts-eae' );
	}

}
