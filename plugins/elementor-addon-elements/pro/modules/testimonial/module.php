<?php

namespace WTS_EAE\Pro\Modules\Testimonial;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'Testimonial',
		];
	}

	public function get_name() {
		return 'eae-testimonial';
	}

	public function get_title() {

		return __( 'Testimonial', 'wts-eae' );
	}

}
