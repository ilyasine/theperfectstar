<?php

namespace WTS_EAE\Pro\Modules\FloatingElement;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'FloatingElement',
		];
	}

	public function get_name() {
		return 'eae-floating-element';
	}

	public function get_title() {

		return __( 'Floating Element', 'wts-eae' );
	}

}
