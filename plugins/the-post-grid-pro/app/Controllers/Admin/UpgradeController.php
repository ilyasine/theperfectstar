<?php
/**
 * Upgrade Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers\Admin;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Upgrade Controller Class.
 */
class UpgradeController {

	/**
	 * Version compare
	 *
	 * @var string
	 */
	public static $compare_version = '5.0.0';

	/**
	 * Add plugin dependency
	 *
	 * @return bool
	 */
	public static function check_plugin_version() {
		$tpg_free_version = self::get_plugin_info( 'Version' );

		if ( version_compare( $tpg_free_version, self::$compare_version, '<' ) ) {
			add_action(
				'admin_notices',
				function () {
					$tpg_free_version = self::get_plugin_info( 'Version' );
					$class            = 'notice notice-error';
					$text             = esc_html__( 'The Post Grid', 'the-post-grid-pro' );
					$link             = add_query_arg(
						[
							'tab'       => 'plugin-information',
							'plugin'    => 'the-post-grid-pro',
							'TB_iframe' => 'true',
							'width'     => '640',
							'height'    => '500',
						],
						admin_url( 'plugin-install.php' )
					);
					$link_pro         = 'https://wordpress.org/plugins/the-post-grid/';

					printf(
						'<div class="%1$s"><p><a target="_blank" href="%3$s"><strong>The Post Grid %6$s</strong></a> is not compatible with the <strong>The Post Grid Pro %5$s</strong>, You need to update <a class="thickbox open-plugin-details-modal" href="%2$s"><strong>%4$s</strong></a> free version to %7$s or more to get the pro features.</p></div>',
						esc_attr( $class ),
						esc_url( $link ),
						esc_url( $link_pro ),
						esc_html( $text ),
						esc_html( RT_TPG_PRO_VERSION ),
						esc_html( $tpg_free_version ),
						esc_html( self::$compare_version )
					);
				}
			);

			return false;
		}

		return true;
	}


	/**
	 * Get TPG Free Plugin Info
	 *
	 * @param string $parameter Paramenter.
	 *
	 * @return string
	 */
	public static function get_plugin_info( $parameter ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$tpg_free_path = WP_PLUGIN_DIR . '/the-post-grid/the-post-grid.php';

		if ( file_exists( $tpg_free_path ) ) {
			$plugin_path = get_plugin_data( $tpg_free_path );

			if ( isset( $plugin_path[ $parameter ] ) ) {
				return $plugin_path[ $parameter ];
			}
		}

		return '';
	}
}
