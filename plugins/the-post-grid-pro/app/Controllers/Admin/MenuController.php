<?php
/**
 * Menu Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers\Admin;

use RT\ThePostGridPro\Helpers\Functions;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Menu Controller Class.
 */
class MenuController {
	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'tpg_admin_menu' ] );
	}

	/**
	 * Admin menu
	 *
	 * @return void
	 */
	public function tpg_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=' . rtTPG()->post_type,
			esc_html__( 'Taxonomy Order', 'the-post-grid-pro' ),
			esc_html__( 'Taxonomy Order', 'the-post-grid-pro' ),
			'administrator',
			'tgp_taxonomy_order',
			[ $this, 'tpg_menu_page_taxonomy_order' ]
		);
	}

	/**
	 * Render view
	 *
	 * @return void
	 */
	public function tpg_menu_page_taxonomy_order() {
		Functions::view( 'taxonomy-order' );
	}
}
