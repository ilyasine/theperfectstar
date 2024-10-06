<?php
namespace WTS_EAE\Pro\Modules\InstagramFeed;
use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'InstagramFeed',
		];
	}

	public function get_name() {
		return 'eae-instagram-feed';
	}

	public function get_title() {
		return __( 'Instagram Feed', 'wts-eae' );
	}

}
