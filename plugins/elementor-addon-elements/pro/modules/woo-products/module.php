<?php

namespace WTS_EAE\Pro\Modules\WooProducts;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'WooProducts',
		];
	}

	public function get_name() {
		return 'eae-woo-products';
	}

	public function get_title() {
		return __( 'Woo Products', 'wts-eae' );
	}

}
