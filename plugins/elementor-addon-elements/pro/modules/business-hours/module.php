<?php

namespace WTS_EAE\Pro\Modules\BusinessHours;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'BusinessHours',
		];
	}

	public function get_name() {
		return 'eae-business-hours';
	}

	public function get_title() {

		return __( 'Business Hours', 'wts-eae' );
	}

}