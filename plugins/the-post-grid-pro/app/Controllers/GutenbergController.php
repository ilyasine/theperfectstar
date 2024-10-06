<?php

namespace RT\ThePostGridPro\Controllers;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Controllers\Blocks\SliderLayout;
use RT\ThePostGridPro\Controllers\Blocks\CategoryBlock;
use RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks\Title;
use RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks\Thumbnail;
use RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks\Content;
use RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks\Meta;
use RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks\ShareIcon;
use RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks\Comment;
use RT\ThePostGridPro\Traits\ELTempleateBuilderTraits;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'GutenbergController' ) ) :

	class GutenbergController {

		use ELTempleateBuilderTraits;

		/**
		 * Version
		 *
		 * @var int|string
		 */
		public $version;

		/**
		 * Settings
		 *
		 * @var
		 */
		public $settings;

		function __construct() {
			new SliderLayout();
			new CategoryBlock();
			new Title();
			new Thumbnail();
			new Content();
			new Meta();
			new ShareIcon();
			new Comment();
			$this->version  = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : RT_TPG_PRO_VERSION;
			$this->settings = get_option( rtTPG()->options['settings'] );
			add_action( 'enqueue_block_editor_assets', [ $this, 'editor_assets' ] );
			add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );
			add_action( 'wp_footer', [ $this, 'block_pro_scripts' ], 15 );
			add_action(
				'admin_head',
				function () {
					?>
				<script>
					var tpgIsBuilder = "<?php echo rtTPG()->hasPro() && self::is_builder_page_archive() ? 'yes' : 'no'; ?>"
				</script>
					<?php
				}
			);

			if ( function_exists( 'register_block_type' ) ) {
				register_block_type(
					'rttpg/post-grid-pro',
					array(
						'render_callback' => array( $this, 'render_shortcode' ),
					)
				);
			}
		}

		static function render_shortcode( $atts ) {
			if ( ! empty( $atts['gridId'] ) && $id = absint( $atts['gridId'] ) ) {
				// return do_shortcode( '[the-post-grid id="' . $id . '"]' );
				ob_start();
				echo do_shortcode( '[the-post-grid id="' . $id . '"]' );

				return ob_get_clean();
			}
		}


		function block_assets() {
			wp_enqueue_style( 'wp-blocks' );
		}

		function block_editor_assets() {
			// Scripts.
			wp_enqueue_script(
				'rt-tpg-pro-cgb-block-js',
				rtTpgPro()->get_assets_uri( 'js/post-grid-blocks.js' ),
				array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
				( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : RT_TPG_PRO_VERSION,
				true
			);
			wp_localize_script(
				'rt-tpg-pro-cgb-block-js',
				'rttpgGB',
				array(
					'short_codes' => Fns::getAllTPGShortCodeList(),
					'icon'        => rtTpgPro()->get_assets_uri( 'images/icon-16x16.png' ),
				)
			);
			wp_enqueue_style( 'wp-edit-blocks' );
		}

		/**
		 * Load Editor Assets
		 *
		 * @return void
		 */
		public function editor_assets() {

			// Main compile css and js file.
			wp_enqueue_style( 'rttpg-pro-blocks-css', rtTpgPro()->get_assets_uri( 'blocks/main.css' ), '', $this->version );
			wp_enqueue_script(
				'rttpg-pro-blocks-js',
				rtTpgPro()->get_assets_uri( 'blocks/main.js' ),
				[
					'wp-block-editor',
					'wp-blocks',
					'wp-components',
					'wp-element',
					'wp-i18n',
				],
				time(),
				true
			);

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$builder_post_id = absint( get_the_ID() ? get_the_ID() : ( $_GET['post'] ?? 0 ) );

			wp_localize_script(
				'rttpg-pro-blocks-js',
				'rttpgProParams',
				[
					'builderType' => self::builder_type( $builder_post_id ),
				]
			);
		}


		public function block_pro_scripts() {

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
				'primaryColor' => isset( $this->settings['tpg_primary_color_main'] ) ? $this->settings['tpg_primary_color_main'] : '#06f',
				'iconFont'     => Fns::tpg_option( 'tpg_icon_font' ),
				'uid'     => get_current_user_id(),
			];
			wp_localize_script( 'rttpg-block-pro', 'rttpg', $variables );

			?>
			<script>
				jQuery(document).ready(function () {

					jQuery(".rttpg-toc-main-wrapper").each(function () {
						jQuery(this).css({'opacity': 1})
						jQuery(this).parent().css({'display': 'block'})
					})

					jQuery('body').on("click", ".tpg-toc-collapseable-btn", function () {
						jQuery(this).toggleClass("collapsed");
						jQuery(this).closest(".tpg-table-of-contents-wrapper").find(".rttpg-toc-inner").slideToggle(300);
					});

					jQuery(".tpg-table-of-contents-wrapper a").on('click', function (event) {
						var hash = this.hash;
						if (hash !== "") {
							event.preventDefault();
							jQuery(".tpg-table-of-contents-wrapper a").removeClass('rttpg-toc-active');
							jQuery(this).closest('li').addClass('rttpg-toc-active');
							jQuery('html, body').animate({
								scrollTop: (jQuery(hash).offset().top - 150)
							}, 500, function () {
								jQuery(hash).addClass('rttpg-toc-active');
								setTimeout(function () {
									jQuery(hash).removeClass('rttpg-toc-active');
								}, 800)
							});
						}
					});


				})
			</script>
			<?php
		}

		// End Class
	}

endif;