<?php
/**
 * Template Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers;

// Do not allow directly accessing this file.
use Elementor\Core\Editor\Loader\Editor_Loader_Factory;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'TemplateController' ) ) :
	/**
	 * Template Controller Class.
	 */
	class TemplateController {

		public function __construct() {
			add_filter( 'template_include', [ $this, 'template_loader' ], 99 );
			add_action( 'wp_enqueue_scripts', [ $this, 'load_rt_tpg_template_scripts' ] );
		}

		public static function template_loader( $template ) {
			$settings        = get_option( rtTPG()->options['settings'] );
			$oLayoutAuthor   = ! empty( $settings['template_author'] ) ? $settings['template_author'] : null;
			$oLayoutCategory = ! empty( $settings['template_category'] ) ? $settings['template_category'] : null;
			$oLayoutSearch   = ! empty( $settings['template_search'] ) ? $settings['template_search'] : null;
			$oLayoutTag      = ! empty( $settings['template_tag'] ) ? $settings['template_tag'] : null;

			$file = null;

			if ( is_tag() && $oLayoutTag ) {
				$file = 'tag-archive.php';
			} elseif ( is_category() && $oLayoutCategory ) {
				$file = 'category-archive.php';
			} elseif ( is_author() && $oLayoutAuthor ) {
				$file = 'author-archive.php';
			} elseif ( is_search() && $oLayoutSearch ) {
				$file = 'search.php';
			}
			if ( $file ) {
				$template = locate_template( [ 'templates/' . $file ] );
				if ( ! $template ) {
					$template = rtTpgPro()->plugin_template_path() . $file;
				}
			}

			return $template;
		}

		public function load_rt_tpg_template_scripts() {
			$settings        = get_option( rtTPG()->options['settings'] );
			$oLayoutAuthor   = ! empty( $settings['template_author'] ) ? $settings['template_author'] : null;
			$oLayoutCategory = ! empty( $settings['template_category'] ) ? $settings['template_category'] : null;
			$oLayoutSearch   = ! empty( $settings['template_search'] ) ? $settings['template_search'] : null;
			$oLayoutTag      = ! empty( $settings['template_tag'] ) ? $settings['template_tag'] : null;

			if ( ( is_tag() && $oLayoutTag ) || ( is_category() && $oLayoutCategory ) || ( is_author() && $oLayoutAuthor ) || ( is_search() && $oLayoutSearch ) ) {
				$script = [
					'jquery',
					'imagesloaded',
					'rt-isotope-js',
					'swiper',
					'rt-scrollbar',
					'rt-tpg-pro',
				];
				$style  = [
					'swiper',
					'rt-fontawsome',
					'rt-flaticon'
				];

				if ( class_exists( 'WooCommerce' ) ) {
					array_push( $script, 'rt-jzoom' );
				}

				if ( is_rtl() ) {
					array_push( $style, 'rt-tpg-rtl' );
				}

				wp_enqueue_style( $style );
				wp_enqueue_script( $script );

				$nonce   = wp_create_nonce( rtTPG()->nonceText() );
				$ajaxurl = '';

				if ( in_array( 'sitepress-multilingual-cms/sitepress.php', get_option( 'active_plugins' ) ) ) {
					$ajaxurl .= admin_url( 'admin-ajax.php?lang=' . ICL_LANGUAGE_CODE );
				} else {
					$ajaxurl .= admin_url( 'admin-ajax.php' );
				}

				wp_localize_script(
					'rt-tpg-pro',
					'rttpg',
					[
						'nonceID' => esc_attr( rtTPG()->nonceId() ),
						'nonce'   => esc_attr( $nonce ),
						'ajaxurl' => esc_url( $ajaxurl ),
						'uid'     => get_current_user_id(),
					]
				);
			}

		}

	}

endif;
