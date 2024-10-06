<?php

namespace WTS_EAE\Pro\Modules\ImageAccordion;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'ImageAccordion',
		];
	}

	public function get_name() {
		return 'eae-image-accordion';
	}

	public function get_title() {

		return __( 'Image Accordion', 'wts-eae' );
	}

}
