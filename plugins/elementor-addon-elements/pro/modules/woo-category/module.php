<?php

namespace WTS_EAE\Pro\Modules\WooCategory;


use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'WooCategory',
		];
	}

	public function get_name() {
		return 'eae-woo-category';
	}

	public function get_title() {
		return __( 'Woo Category', 'wts-eae' );
	}
}
