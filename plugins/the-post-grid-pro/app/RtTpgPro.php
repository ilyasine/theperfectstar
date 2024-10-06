<?php
/**
 * Main initialization class.
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGridPro\Controllers\Admin\MenuController;
use RT\ThePostGridPro\Controllers\Admin\MetaController;
use RT\ThePostGridPro\Controllers\Admin\TaxOrderController;
use RT\ThePostGridPro\Controllers\AjaxController;
use RT\ThePostGridPro\Controllers\FrontEndController;
use RT\ThePostGridPro\Controllers\GutenbergController;
use RT\ThePostGridPro\Controllers\Hooks\ActionHooks;
use RT\ThePostGridPro\Controllers\Hooks\FilterHooks;
use RT\ThePostGridPro\Controllers\LicensingController;
use RT\ThePostGridPro\Controllers\ScriptController;
use RT\ThePostGridPro\Controllers\Api\RestApi;
use RT\ThePostGridPro\Controllers\ShortcodeController;
use RT\ThePostGridPro\Controllers\TemplateController;
use RT\ThePostGridPro\Controllers\MyAccountController;
use RT\ThePostGridPro\Helpers\Install;
use RT\ThePostGridPro\Widgets\VcWidget;
use RT\ThePostGridPro\Controllers\ElementorController;
use RT\ThePostGridPro\Controllers\Admin\UpgradeController;
use RT\ThePostGrid\Controllers\Admin\UpgradeController as TPGUpgradeController;
use RT\ThePostGridPro\Controllers\Builder\TemplateBuilder;
use RT\ThePostGridPro\Controllers\Builder\TemplateBuilderFrontend;

require_once RT_THE_POST_GRID_PRO_PLUGIN_PATH . '/vendor/autoload.php';

if ( UpgradeController::check_plugin_version() == false ) {
	return;
}

if ( ! class_exists( RtTpgPro::class ) ) {
	/**
	 * Main initialization class.
	 */

	final class RtTpgPro {
		/**
		 * Options
		 *
		 * @var array
		 */
		public $options = [
			'version'           => RT_TPG_PRO_VERSION,
			'installed_version' => 'rt_the_post_grid_current_version',
			'slug'              => RT_THE_POST_GRID_PRO_PLUGIN_SLUG,
		];

		public $category_meta_key = 'rttpg_category_color';
		public $category_thumb_meta_key = 'rttpg_category_thumb';

		/**
		 * Store the singleton object.
		 *
		 * @var boolean
		 */
		private static $singleton = false;

		/**
		 * Create an inaccessible constructor.
		 */
		private function __construct() {
			add_action( 'init', [ $this, '__init' ], 0 );
		}

		/**
		 * Fetch an instance of the class.
		 *
		 * @return object
		 */
		public static function getInstance() {
			if ( false === self::$singleton ) {
				self::$singleton = new self();
			}

			return self::$singleton;
		}

		/**
		 * Plugin check.
		 *
		 * @return boolean
		 */
		public function isRtTPGActive() {
			$activated = false;
			/*if ( in_array( 'the-post-grid/the-post-grid.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				return true;
			}*/

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				if ( is_plugin_active( 'the-post-grid/the-post-grid.php' ) ) {
					$activated = true;
				}
			} else {
				if ( in_array( 'the-post-grid/the-post-grid.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					$activated = true;
				}
			}

			return $activated;
		}

		/**
		 * Init
		 *
		 * @return void
		 */
		public function __init() {
			// check if elementor is deactivate.
			if ( ! in_array( 'elementor/elementor.php', get_option( 'active_plugins' ) ) && get_option( 'tpg_flush_rewrite_rules' ) ) {
				update_option( 'tpg_flush_rewrite_rules', 0 );
			}

			if ( $this->isRtTPGActive() && ! TPGUpgradeController::check_plugin_version() ) {
				return;
			}

			if ( $this->isRtTPGActive() ) {
				add_filter( 'rttpg_default_template_path', [ $this, 'plugin_path' ] );

				new RestApi();
				new ScriptController();
				new MyAccountController();

				if ( is_admin() ) {
					new MenuController();
					new MetaController();
					new TaxOrderController();
					new LicensingController();
				}

				new FrontEndController();
				new TemplateController();
				new FilterHooks();
				new ActionHooks();
				new AjaxController();
				new ShortcodeController();

				TemplateBuilder::init();
				TemplateBuilderFrontend::init();

				if ( class_exists( '\Elementor\Plugin' ) ) {
					new ElementorController();
				}

				new GutenbergController();
				new VcWidget();

				$this->load_hooks();
			} else {
				add_action( 'admin_notices', [ $this, 'requirement_notice' ] );
			}
		}

		/**
		 * Load hooks.
		 *
		 * @return void
		 */
		private function load_hooks() {
			register_activation_hook( RT_THE_POST_GRID_PRO_PLUGIN_ACTIVE_FILE_NAME, [ Install::class, 'activate' ] );
			register_deactivation_hook( RT_THE_POST_GRID_PRO_PLUGIN_ACTIVE_FILE_NAME, [ Install::class, 'deactivate' ] );

			add_action( 'init', [ &$this, 'init_hooks' ], 1 );
		}

		/**
		 * Init hooks.
		 *
		 * @return void
		 */
		public function init_hooks() {
			do_action( 'rttpgp_before_init', $this );
			$this->load_language();
		}

		/**
		 * I18n
		 *
		 * @return void
		 */
		public function load_language() {
			load_plugin_textdomain( 'the-post-grid-pro', false, RT_THE_POST_GRID_PRO_LANGUAGE_PATH );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( RT_THE_POST_GRID_PRO_PLUGIN_ACTIVE_FILE_NAME ) );
		}

		/**
		 * Template path
		 *
		 * @return string
		 */
		public function plugin_template_path() {
			return $this->plugin_path() . '/templates/';
		}

		/**
		 * Assets URI
		 *
		 * @param string $file File.
		 * @return string
		 */
		public function get_assets_uri( $file ) {
			$file = ltrim( $file, '/' );

			return trailingslashit( RT_THE_POST_PRO_GRID_PLUGIN_URL . '/assets' ) . $file;
		}

		/**
		 * RTL check
		 *
		 * @param string $file File.
		 * @return string
		 */
		public function tpg_can_be_rtl( $file ) {
			$file = ltrim( str_replace( '.css', '', $file ), '/' );

			if ( is_rtl() ) {
				$file .= '.rtl';
			}

			return trailingslashit( RT_THE_POST_PRO_GRID_PLUGIN_URL . '/assets' ) . $file . '.min.css';
		}

		/**
		 * Requirement
		 *
		 * @return void
		 */
		public function requirement_notice() {
			$class = 'notice notice-error';

			$text = esc_html__( 'The Post Grid', 'the-post-grid-pro' );
			$link = add_query_arg(
				[
					'tab'       => 'plugin-information',
					'plugin'    => 'the-post-grid-pro',
					'TB_iframe' => 'true',
					'width'     => '640',
					'height'    => '500',
				],
				admin_url( 'plugin-install.php' )
			);

			printf(
				'<div class="%1$s"><p>The Post Grid Pro is not working because you need to install and activate <a class="thickbox open-plugin-details-modal" href="%2$s"><strong>%3$s</strong></a> plugin to get pro features.</p></div>',
				esc_attr( $class ),
				esc_url( $link ),
				esc_html( $text )
			);
		}

		/**
		 * ACF check
		 *
		 * @return boolean
		 */
		public function is_valid_acf_version() {
			if ( class_exists( 'acf_pro' ) && ACF_PRO ) {
				return version_compare( ACF_VERSION, '5.6.8', '>' );
			} else {
				return version_compare( ACF_VERSION, '5.7.5', '>' );
			}
		}

	}

	/**
	 * Function for external use.
	 *
	 * @return rtTpgPro
	 */
	function rtTpgPro() {
		return RtTpgPro::getInstance();
	}

	// Init app.
	rtTpgPro();
}
