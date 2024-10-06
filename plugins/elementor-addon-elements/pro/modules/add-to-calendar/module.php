<?php

namespace WTS_EAE\Pro\Modules\AddToCalendar;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'AddToCalendar',
		];
	}

	public function get_name() {
		return 'eae-add-to-calendar';
	}

	public function get_title() {

		return __( 'Add To Calendar', 'wts-eae' );
	}

}
