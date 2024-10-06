<?php
/**
 * Elementor Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers;

use Elementor\Plugin;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Controllers\Builder\TemplateBuilder;
use RT\ThePostGridPro\Controllers\Builder\TemplateBuilderFrontend;
use RT\ThePostGridPro\Traits\ELTempleateBuilderTraits;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'ElementorController' ) ) :
	/**
	 * Elementor Controller Class.
	 */
	class ElementorController {

		/**
		 * Template builder related traits.
		 */
		use ELTempleateBuilderTraits;

		/**
		 * Version
		 *
		 * @var string
		 */
		private $version;

		/**
		 * Settings
		 * @var
		 */
		public $settings;

		/**
		 * Class constructor
		 */
		public function __construct() {
			$this->version  = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RT_TPG_PRO_VERSION;
			$this->settings = get_option( rtTPG()->options['settings'] );
			if ( did_action( 'elementor/loaded' ) ) {
				add_action( 'wp_footer', [ $this, 'tpg_el_pro_scripts' ], 15 );
				add_action( 'elementor/widgets/register', [ $this, 'widgets_init' ] );
				add_filter( 'tpg_el_widget_register', [ $this, 'add_new_pro_widget' ] );
				add_action( 'elementor/preview/enqueue_scripts', [ $this, 'elementor_preview_scripts' ] );
			}
		}

		function elementor_preview_scripts() {
			wp_enqueue_script( 'rt-isotope-js' );
			wp_enqueue_script( 'rt-pagination' );
		}

		/**
		 * Pro widget
		 *
		 * @param array $widgets Widgets.
		 *
		 * @return array
		 */
		public function add_new_pro_widget( $widgets ) {
			if ( rtTPG()->hasPro() && self::is_builder_page_single() ) {
				$widgets['related-post'] = 'TPGRelatedPost';
			}

			if ( rtTPG()->hasPro() && self::is_builder_page_archive() ) {
				// Remove default block widget.
				//unset( $widgets['grid-layout'] );
				//unset( $widgets['list-layout'] );
				//unset( $widgets['grid-hover-layout'] );
				//unset( $widgets['slider-layout'] );

				// Add new block widget for only archive page.
				$widgets['grid-layout-archive']       = 'TPGGridLayoutArchive';
				$widgets['list-layout-archive']       = 'TPGListLayoutArchive';
				$widgets['grid-hover-layout-archive'] = 'TPGGridHoverLayoutArchive';
				$widgets['slider-layout-archive']     = 'TPGSliderLayoutArchive';
			}

			return $widgets;
		}

		public function tpg_el_pro_scripts() {

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
			wp_localize_script( 'rttpg-block-pro', 'rttpg', $variables );
		}

		public function widgets_init() {
			require_once RT_THE_POST_GRID_PLUGIN_PATH . '/app/Widgets/elementor/base.php';

			/**
			 * Widget register for single page builder
			 */
			if ( self::is_builder_page_single() ) {
				$widgets['post-title']     = 'TPGPostTitle';
				$widgets['post-thumbnail'] = 'TPGPostThumbnail';
				$widgets['post-content']   = 'TPGPostContent';
				$widgets['post-meta']      = 'TPGPostMeta';
				$widgets['social-share']   = 'TPGSocialShare';

				if ( Fns::is_acf() ) {
					$widgets['acf'] = 'TPGAdvanceCustomField';
				}

				$widgets['post-comment'] = 'TPGPostComment';
			}

			if ( ! empty( $widgets ) ) {
				foreach ( $widgets as $file_name => $class ) {
					$template_name = 'the-post-grid/elementor/' . $file_name . '.php';
					if ( file_exists( get_stylesheet_directory() . $template_name ) ) {
						$file = get_stylesheet_directory() . $template_name;
					} elseif ( file_exists( get_template_directory() . $template_name ) ) {
						$file = get_template_directory() . $template_name;
					} else {
						$file = RT_THE_POST_GRID_PRO_PLUGIN_PATH . '/app/Widgets/elementor/' . $file_name . '.php';
					}
					require_once $file;

					Plugin::instance()->widgets_manager->register( new $class() );
				}
			}
		}
	}
endif;
