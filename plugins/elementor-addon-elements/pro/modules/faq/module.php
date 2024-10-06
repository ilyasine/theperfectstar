<?php
namespace WTS_EAE\Pro\Modules\FAQ;
use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'faq',
		];
	}

	public function get_name() {
		return 'eae-faq';
	}

	public function get_title() {
		return __( 'FAQ', 'wts-eae' );
	}

}
