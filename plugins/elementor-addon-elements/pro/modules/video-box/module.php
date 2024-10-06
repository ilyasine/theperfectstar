<?php

namespace WTS_EAE\Pro\Modules\VideoBox;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'VideoBox',
		];
	}

	public function get_name() {
		return 'eae-video-box';
	}

	public function get_title() {

		return __( 'Video Box', 'wts-eae' );
	}

}
