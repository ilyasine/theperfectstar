<?php

namespace WTS_EAE\Pro\Modules\Devices;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'Devices'
		];
	}

	public function get_name() {
		return 'eae-devices';
	}

	public function get_title() {
		return __( 'Devices', 'wts-eae' );
	}

}