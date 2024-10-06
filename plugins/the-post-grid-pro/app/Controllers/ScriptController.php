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

/**
 * Script Controller Class.
 */
class ScriptController {

	private $version;
	private $settings;
	private static $ajaxurl;

	public function __construct() {
		$this->version = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RT_TPG_PRO_VERSION;
		add_action( 'init', [ $this, 'init' ], 15 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ], 15 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_vendor' ], 10 );
	}

	public function init() {
		$this->settings = get_option( rtTPG()->options['settings'] );

		self::$ajaxurl = admin_url( 'admin-ajax.php' );
		wp_deregister_script( 'rt-tpg' );
		wp_dequeue_script( 'rt-tpg' );

		wp_deregister_style( 'rt-tpg-admin-preview' );
		wp_dequeue_style( 'rt-tpg-admin-preview' );
		wp_deregister_style( 'rt-tpg' );
		wp_dequeue_style( 'rt-tpg' );
		wp_deregister_style( 'rt-tpg-block' );
		wp_dequeue_style( 'rt-tpg-block' );
		wp_deregister_style( 'rt-tpg-shortcode' );
		wp_dequeue_style( 'rt-tpg-shortcode' );

		add_action( 'admin_enqueue_scripts', [ $this, 'tpg_admin_common_scripts' ] );

		$scripts = [];
		$styles  = [];

		$styles['rt-magnific-popup'] = rtTpgPro()->get_assets_uri( 'vendor/Magnific-Popup/magnific-popup.css' );
		$styles['swiper']            = rtTpgPro()->get_assets_uri( 'vendor/swiper/swiper.min.css' );

		// Plugin specific css.
		$styles['rt-tpg']           = rtTpgPro()->tpg_can_be_rtl( 'css/thepostgrid' );
		$styles['rt-tpg-block']     = rtTpgPro()->tpg_can_be_rtl( 'css/tpg-block' );
		$styles['rt-tpg-shortcode'] = rtTpgPro()->tpg_can_be_rtl( 'css/tpg-shortcode' );

		$scripts[] = [
			'handle' => 'rt-jzoom',
			'src'    => rtTpgPro()->get_assets_uri( 'js/jzoom.min.js' ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];
		$scripts[] = [
			'handle'  => 'jquery-mousewheel',
			'src'     => rtTpgPro()->get_assets_uri( 'vendor/jquery.mousewheel.min.js' ),
			'deps'    => [ 'jquery' ],
			'footer'  => true,
			'version' => '3.1.13',
		];
		$scripts[] = [
			'handle'  => 'rt-scrollbar',
			'src'     => rtTpgPro()->get_assets_uri( 'vendor/scrollbar/jquery.nicescroll.min.js' ),
			'deps'    => [ 'jquery', 'jquery-mousewheel' ],
			'footer'  => true,
			'version' => '3.1.5',
		];
		$scripts[] = [
			'handle' => 'rt-magnific-popup',
			'src'    => rtTpgPro()->get_assets_uri( 'vendor/Magnific-Popup/jquery.magnific-popup.min.js' ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];
		$scripts[] = [
			'handle' => 'rt-tpg-myaccount',
			'src'    => rtTpgPro()->get_assets_uri( 'js/my-account.js' ),
			'deps'   => [ 'jquery', 'media-upload', 'rt-select2' ],
			'footer' => true,
		];

		$default_swiper_path = rtTpgPro()->get_assets_uri( 'vendor/swiper/swiper.min.js' );

		if ( defined( 'ELEMENTOR_ASSETS_PATH' ) ) {
			$is_swiper8_enable = get_option( 'elementor_experiment-e_swiper_latest' );
			if ( $is_swiper8_enable == 'active' ) {
				$el_swiper_path = 'lib/swiper/v8/swiper.min.js';
			} else {
				$el_swiper_path = 'lib/swiper/swiper.min.js';
			}
			$elementor_swiper_path = ELEMENTOR_ASSETS_PATH . $el_swiper_path;
			if ( file_exists( $elementor_swiper_path ) ) {
				$default_swiper_path = ELEMENTOR_ASSETS_URL . $el_swiper_path;
			}
		}

		$scripts[] = [
			'handle' => 'swiper',
			'src'    => $default_swiper_path,
			'deps'   => [ 'jquery', 'imagesloaded' ],
			'footer' => true,
		];

		$scripts[] = [
			'handle' => 'rt-pagination',
			'src'    => rtTpgPro()->get_assets_uri( 'js/pagination.min.js' ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];
		$scripts[] = [
			'handle' => 'rt-tpg-pro',
			'src'    => rtTpgPro()->get_assets_uri( 'js/rttpg.js' ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];
		$scripts[] = [
			'handle' => 'rttpg-block-pro',
			'src'    => rtTpgPro()->get_assets_uri( 'js/rttpg-el.js' ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];

		$scripts[] = [
			'handle' => 'rt-tpg-guten-editor',
			'src'    => rtTpgPro()->get_assets_uri( 'js/rttpg-guten-editor.js' ),
			'deps'   => [ 'jquery' ],
			'footer' => true,
		];

		if ( is_admin() ) {
			$scripts[]                          = [
				'handle' => 'tpg-admin-taxonomy',
				'src'    => rtTpgPro()->get_assets_uri( 'js/admin-taxonomy.js' ),
				'deps'   => [ 'jquery' ],
				'footer' => true,
			];
			$styles['rt-tpg-pro-admin-preview'] = rtTpgPro()->get_assets_uri( 'css/admin/admin-preview.css' );
		}

		foreach ( $scripts as $script ) {
			wp_register_script( $script['handle'], $script['src'], $script['deps'], isset( $script['version'] ) ? $script['version'] : $this->version, $script['footer'] );
		}

		foreach ( $styles as $k => $v ) {
			wp_register_style( $k, $v, false, isset( $styles['version'] ) ? $styles['version'] : $this->version );
		}
	}

	public function enqueue() {
		if ( ! isset( $this->settings['tpg_load_script'] ) ) {
			$block_type = isset( $this->settings['tpg_block_type'] ) ? $this->settings['tpg_block_type'] : 'default';
			if ( 'default' == $block_type ) {
				wp_enqueue_style( 'rt-tpg' );
			}
			if ( 'elementor' == $block_type ) {
				wp_enqueue_style( 'rt-tpg-block' );
			}
			if ( 'shortcode' == $block_type ) {
				wp_enqueue_style( 'rt-tpg-shortcode' );
			}
		}

		$tpg_popupbar_color      = isset( $this->settings['tpg_popupbar_color'] ) ? $this->settings['tpg_popupbar_color'] : '';
		$tpg_popupbar_bg_color   = isset( $this->settings['tpg_popupbar_bg_color'] ) ? $this->settings['tpg_popupbar_bg_color'] : '';
		$tpg_popupbar_text_color = isset( $this->settings['tpg_popupbar_text_color'] ) ? $this->settings['tpg_popupbar_text_color'] : '';

		?>
		<style>
			:root {
			<?php
			 echo $tpg_popupbar_color ? '--tpg-popupbar-color:' . esc_attr( $tpg_popupbar_color ) . ';' : '';
			 echo $tpg_popupbar_bg_color ? '--tpg-popup-bg-color:' . esc_attr( $tpg_popupbar_bg_color ) . ';' : '';
			?>
			}

			<?php echo $tpg_popupbar_text_color ? '.rt-popup-content, .md-content > .rt-md-content-holder .rt-md-content *{color:' . esc_attr( $tpg_popupbar_text_color ) . '!important}' : ''; ?>
		</style>
		<?php
	}

	/**
	 * @return mixed
	 */
	public function enqueue_vendor() {
		$block_type = isset( $this->settings['tpg_block_type'] ) ? $this->settings['tpg_block_type'] : 'default';
		if ( ! isset( $this->settings['tpg_load_script'] ) && in_array( $block_type, [ 'elementor', 'shortcode' ] ) ) {
			wp_enqueue_style( 'swiper' );
			wp_enqueue_style( 'rt-magnific-popup' );
		}
	}

	/**
	 * Admin common script load
	 */
	function tpg_admin_common_scripts() {
		$screen_spost_type = get_current_screen();
		if ( $screen_spost_type->post_type !== 'tpg_builder' ) {
			return;
		}
		wp_enqueue_style( 'tpgp-common-style', rtTpgPro()->get_assets_uri( 'css/admin/admin-common.css' ), [], $this->version );
		wp_enqueue_script( 'tpgp-common-script', rtTpgPro()->get_assets_uri( 'js/common.js' ), [ 'jquery' ], $this->version, true );
		wp_enqueue_script( 'tpgp-el-template-script', rtTpgPro()->get_assets_uri( 'js/el-template.js' ), [ 'jquery' ], $this->version, true );
		wp_localize_script(
			'tpgp-el-template-script',
			'tpgp_el_tb',
			[
				'ajaxurl'          => self::$ajaxurl,
				'loading'          => esc_html__( 'Loading', 'the-post-grid-pro' ),
				rtTPG()->nonceId() => wp_create_nonce( rtTPG()->nonceText() ),
				'uid'              => get_current_user_id(),
			]
		);
	}
}
