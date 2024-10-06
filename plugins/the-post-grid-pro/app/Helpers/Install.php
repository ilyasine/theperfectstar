<?php
/**
 * Install Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Helpers;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Install Class.
 */
class Install {

	public static function activate() {
		flush_rewrite_rules();
	}

	public static function deactivate() {
		update_option( 'tpg_flush_rewrite_rules', 0 );
	}

}
