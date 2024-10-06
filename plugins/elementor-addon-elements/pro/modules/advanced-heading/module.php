<?php

namespace WTS_EAE\Pro\Modules\AdvancedHeading;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'AdvancedHeading',
		];
	}

	public function get_name() {
		return 'eae-advanced-heading';
	}

	public function get_title() {

		return __( 'Advanced Heading', 'wts-eae' );
	}

}
