<?php

namespace WTS_EAE\Pro\Modules\CallToAction;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'CallToAction',
		];
	}

	public function get_name() {
		return 'eae-call-to-action';
	}

	public function get_title() {

		return __( 'Call To Action', 'wts-eae' );
	}

}
