<?php
/**
 * Script Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'ShortcodeController' ) ) :
	/**
	 * Script Controller Class.
	 */
	class ShortcodeController {

		public static $sc_script;
		public $settings;

		public function __construct() {
			add_action( 'wp_footer', [ $this, 'register_sc_scripts' ], 15 );
			$this->settings = get_option( rtTPG()->options['settings'] );
		}

		public function register_sc_scripts() {
			$ajaxurl = '';

			if ( in_array( 'sitepress-multilingual-cms/sitepress.php', get_option( 'active_plugins' ) ) ) {
				$ajaxurl .= admin_url( 'admin-ajax.php?lang=' . ICL_LANGUAGE_CODE );
			} else {
				$ajaxurl .= admin_url( 'admin-ajax.php' );
			}

			$variables = [
				'nonceID'      => esc_attr( rtTPG()->nonceId() ),
				'nonce'        => esc_attr( wp_create_nonce( rtTPG()->nonceText() ) ),
				'ajaxurl'      => esc_url( $ajaxurl ),
				'uid'          => get_current_user_id(),
				'primaryColor' => isset( $this->settings['tpg_primary_color_main'] ) ? $this->settings['tpg_primary_color_main'] : '#06f'
			];

			wp_localize_script( 'rt-tpg-pro', 'rttpg', $variables );
		}
	}
endif;
