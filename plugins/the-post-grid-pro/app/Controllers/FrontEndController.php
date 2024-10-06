<?php
/**
 * Frontend Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

//phpcs:disable WordPress.Security.NonceVerification.Recommended

if ( ! class_exists( 'FrontEndController' ) ) :
	/**
	 * Frontend Controller Class.
	 */
	class FrontEndController {
		/**
		 * Class constructor
		 */
		public function __construct() {
			add_action( 'wp_ajax_tgpMultiPagePopUp', [ $this, 'tgpMultiPagePopUp' ] );
			add_action( 'wp_ajax_nopriv_tgpMultiPagePopUp', [ $this, 'tgpMultiPagePopUp' ] );
			add_action( 'wp_ajax_tgpSinglePopUp', [ $this, 'tgpSinglePopUp' ] );
			add_action( 'wp_ajax_nopriv_tgpSinglePopUp', [ $this, 'tgpSinglePopUp' ] );
			add_action( 'wp_ajax_addToCartWc', [ $this, 'addToCartWc' ] );
			add_action( 'wp_ajax_nopriv_addToCartWc', [ $this, 'addToCartWc' ] );
		}

		/**
		 * Add to cart
		 *
		 * @return void
		 */
		public function addToCartWc() {
			$msg   = null;
			$cls   = 'error';
			$error = true;

			if ( class_exists( 'WooCommerce' ) ) {
				$id  = ! empty( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : 0;
				$qtn = ! empty( $_REQUEST['qtn'] ) ? absint( $_REQUEST['qtn'] ) : 1;

				if ( $id ) {
					global $woocommerce;

					if ( $woocommerce->cart->add_to_cart( $id, $qtn ) ) {
						$error = false;
						$cls   = 'success';
						$msg  .= esc_html__( 'Product has been added to your cart.', 'the-post-grid-pro' );
					} else {
						$msg .= esc_html__( 'Product not found', 'the-post-grid-pro' );
					}
				} else {
					$msg .= esc_html__( 'Product not selected', 'the-post-grid-pro' );
				}
			} else {
				$error = true;
				$msg  .= esc_html__( 'Wocommerce not Installed', 'the-post-grid-pro' );
			}

			wp_send_json(
				[
					'msg'   => $msg,
					'cls'   => $cls,
					'error' => $error,
				]
			);
		}

		/**
		 * Single popup
		 *
		 * @return void
		 */
		public function tgpSinglePopUp() {
			$html  = $htmlCInfo = null;
			$error = true;

			if ( $_REQUEST['id'] ) {
				$postMetaTop = null;
				global $post;

				$post = get_post( $_REQUEST['id'] );
				setup_postdata( $post );

				if ( $post->post_type == 'product' ) {
					$html .= $this->loadWcData( $post, true );
				} elseif ( $post->post_type == 'download' ) {
					$html .= $this->loadEddData( $post, true );
				} else {
					$settings = get_option( rtTPG()->options['settings'] );
					$fields   = isset( $settings['popup_fields'] ) ? $settings['popup_fields'] : [];
					$html    .= "<div class='md-header'>";
					$html    .= "<h1 class='entry-title'>{$post->post_title}</h1>";

					if ( in_array( 'author', $fields ) ) {
						$author       = apply_filters( 'rttpg_author_link_with_icon', sprintf( '<a href="%s"><i class="fa fa-user"></i>%s</a>', get_author_posts_url( get_the_author_meta( 'ID' ) ), get_the_author() ) );
						$postMetaTop .= "<span class='author'>{$author}</span>";
					}

					if ( in_array( 'post_date', $fields ) ) {
						$postMetaTop .= "<span class='date'><i class='far fa-calendar-alt'></i>" . get_the_date(
							'M j, Y',
							$post->ID
						) . '</span>';
					}

					if ( in_array( 'categories', $fields ) ) {
						$postMetaTop .= "<span class='categories-links'><i class='fas fa-folder-open'></i>" . get_the_term_list(
							$post->ID,
							'category',
							null,
							'<span class="rt-separator">, </span>'
						) . '</span>';
					}

					if ( in_array( 'tags', $fields ) ) {
						$postMetaTop .= "<span class='post-tags-links'><i class='fa fa-tags'></i>" . get_the_term_list(
							$post->ID,
							'post_tag',
							null,
							'<span class="rt-separator">,</span>'
						) . '</span>';
					}

					if ( ! empty( $postMetaTop ) ) {
						$html .= "<div class='post-meta-user'>{$postMetaTop}</div>";
					}

					$html .= '</div>';
					$html .= '<div class="rt-md-content">';

					if ( has_post_thumbnail( $post->ID ) && in_array( 'feature_img', $fields ) ) {
						$html .= "<div class='feature-image'>";
						$html .= get_the_post_thumbnail( $post->ID, 'large' );
						$html .= '</div>';
					}

					if ( in_array( 'content', $fields ) ) {
						if ( class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) { // 1.17c. FIxes issues with ajax hopefully.
							\WPBMap::addAllMappedShortcodes();
						}

						$content = preg_replace( '#\[[^\]]+\]#', '', ( $post->post_content ?? '' ) );

						$html .= "<div class='tpg-content'>" . apply_filters(
							'the_content',
							$content
						) . '</div>';
					}

					if ( in_array( 'cf', $fields ) ) {
						$cf_group = ! empty( $settings['cf_group'] ) ? $settings['cf_group'] : [];
						$groups   = array_keys( Fns::get_groups_by_post_type( $post->post_type ) );
						$cf_group = array_intersect( $groups, $cf_group );

						if ( ! empty( $cf_group ) ) {
							$format = [
								'hide_empty'       => ! empty( $settings['cf_hide_empty_value'] ) ? 1 : 0,
								'show_value'       => ! empty( $settings['cf_show_only_value'] ) ? 1 : 0,
								'hide_group_title' => ! empty( $settings['cf_hide_group_title'] ) ? 1 : 0,
							];
							$html  .= Functions::get_cf_formatted_fields( $cf_group, $format, $post->ID );
						}
					}

					if ( in_array( 'social_share', $fields ) ) {
						$html .= Fns::socialShare( get_the_permalink( $post->ID ) );
					}

					$html .= '</div>';
				}
				wp_reset_postdata();

				// $html .= '<script>tpgMdScriptLoad();</script>';
				$error = false;
			} else {
				$html .= '<p>' . esc_html__( 'No item id found', 'the-post-grid-pro' ) . '</p>';
				$error = true;
			}

			wp_send_json(
				[
					'data'  => $html,
					'error' => $error,
				]
			);
			die();
		}

		/**
		 * Multi popup
		 *
		 * @return void
		 */
		public function tgpMultiPagePopUp() {
			$html  = $htmlCInfo = null;
			$error = true;

			if ( $_REQUEST['id'] ) {
				$postMetaTop = null;

				global $post;
				$post = get_post( $_REQUEST['id'] );

				if ( $post->post_type == 'product' ) {
					$html .= $this->loadWcData( $post );
				} elseif ( $post->post_type == 'download' ) {
					$html .= $this->loadEddData( $post );
				} else {
					$settings = get_option( rtTPG()->options['settings'] );
					$fields   = isset( $settings['popup_fields'] ) ? $settings['popup_fields'] : [];

					if ( in_array( 'author', $fields ) ) {
						$author       = apply_filters( 'rttpg_author_link_with_icon', sprintf( '<i class="fa fa-user"></i><a href="%s">%s</a>', get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) ), get_the_author_meta( 'display_name', $post->post_author ) ) );
						$postMetaTop .= "<span class='author'>{$author}</span>";
					}

					if ( in_array( 'post_date', $fields ) ) {
						$postMetaTop .= "<span class='date'><i class='far fa-calendar-alt'></i>" . get_the_date(
							'M j, Y',
							$post->ID
						) . '</span>';
					}

					if ( in_array( 'categories', $fields ) ) {
						$postMetaTop .= "<span class='categories-links'><i class='fas fa-folder-open'></i>" . get_the_term_list(
							$post->ID,
							'category',
							null,
							', '
						) . '</span>';
					}

					if ( in_array( 'tags', $fields ) ) {
						$postMetaTop .= "<span class='post-tags-links'><i class='fa fa-tags'></i>" . get_the_term_list(
							$post->ID,
							'post_tag',
							null,
							'<span class="rt-separator">,</span>'
						) . '</span>';
					}

					$html        .= "<div class='rt-tpg-container tpg-shortcode-main-wrapper'>";
					$html        .= "<div class='rt-row rt-detail'>";
					$contentClass = 'rt-col-md-12';

					if ( has_post_thumbnail( $post->ID ) && in_array( 'feature_img', $fields ) ) {
						$html        .= "<div class='rt-col-xs-12'>";
						$html        .= "<div class='feature-image'>";
						$html        .= get_the_post_thumbnail( $post->ID, 'full' );
						$html        .= '</div>';
						$html        .= '</div>';
						$contentClass = 'rt-col-xs-12';
					}

					$html .= "<div class='{$contentClass}'>";

					if ( in_array( 'title', $fields ) ) {
						$html .= "<h1 class='entry-title'>{$post->post_title}</h1>";
					}

					if ( ! empty( $postMetaTop ) ) {
						$html .= "<div class='post-meta-user'>{$postMetaTop}</div>";
					}

					if ( in_array( 'content', $fields ) ) {
						if ( class_exists( 'WPBMap' ) && method_exists(
							'WPBMap',
							'addAllMappedShortcodes'
						) ) { // 1.17c. FIxes issues with ajax hopefully.
							WPBMap::addAllMappedShortcodes();
						}

						$html .= "<div class='tpg-content'>" . apply_filters(
							'the_content',
							$post->post_content
						) . '</div>';
					}

					if ( in_array( 'cf', $fields ) ) {
						$cf_group = $settings['cf_group'] ? $settings['cf_group'] : [];
						$groups   = array_keys( Fns::get_groups_by_post_type( $post->post_type ) );
						$cf_group = array_intersect( $groups, $cf_group );

						if ( ! empty( $cf_group ) ) {
							$format = [
								'hide_empty'       => ! empty( $settings['cf_hide_empty_value'] ) ? 1 : 0,
								'show_value'       => ! empty( $settings['cf_show_only_value'] ) ? 1 : 0,
								'hide_group_title' => ! empty( $settings['cf_hide_group_title'] ) ? 1 : 0,
							];
							$html  .= Functions::get_cf_formatted_fields( $cf_group, $format, $post->ID );
						}
					}

					if ( in_array( 'social_share', $fields ) ) {
						$html .= Fns::socialShare( get_the_permalink( $post->ID ) );
					}

					$html .= '</div>';
					$html .= '</div>';
					$html .= '</div>';
					// $html .= '<script>tpgMdScriptLoad();</script>';
				}
				$error = false;
			} else {
				$html .= '<p>' . esc_html__( 'No item id found', 'the-post-grid-pro' ) . '</p>';
				$error = true;
			}

			wp_send_json(
				[
					'data'  => $html,
					'error' => $error,
				]
			);
			die();
		}

		/**
		 * Remove review tab
		 *
		 * @param array $tabs Tabs.
		 * @return array
		 */
		public function rt_woo_remove_reviews_tab( $tabs ) {
			unset( $tabs['reviews'] );

			return $tabs;
		}

		/**
		 * Woocommece data
		 *
		 * @param object  $post Post object.
		 * @param boolean $type Type.
		 * @return string
		 */
		private function loadWcData( $post, $type = false ) {
			$html = null;
			global $post;

			setup_postdata( $post );

			if ( $type ) {
				$html .= "<div class='md-header'>";
				$html .= "<h1 class='entry-title'>{$post->post_title}</h1>";
				$html .= '</div>';
				$html .= '<div class="rt-md-content">';
			}

			ob_start();
			echo "<div class='wc-product-holder'>";
			echo "<div itemscope itemtype='" . esc_attr( Functions::woocommerce_get_product_schema() ) . "' class='product-summery-holder' id='product-" . esc_attr( $post->ID ) . "'>";

			echo "<div class='images-container'>";
			echo Functions::rtProductGalleryImages(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';

			echo '<div class="summary entry-summary">';

			if ( ! $type ) {
				woocommerce_template_single_title();
			}

			woocommerce_template_single_price();
			echo '<div class="wc-add-to-cart">';
			woocommerce_template_single_add_to_cart();
			echo '</div>';
			woocommerce_template_single_excerpt();
			echo '</div><!-- .summary -->';

			echo "<meta itemprop='url' content='" . esc_attr( get_the_permalink() ) . "' />";

			echo '</div>';
			add_filter( 'woocommerce_product_tabs', [ $this, 'rt_woo_remove_reviews_tab' ], 98 );

			woocommerce_output_product_data_tabs();
			remove_filter( 'woocommerce_product_tabs', [ $this, 'rt_woo_remove_reviews_tab' ], 98 );
			echo '</div>';
			$html .= ob_get_contents();
			ob_end_clean();

			wp_reset_postdata();

			if ( $type ) {
				$html .= '</div>';
			}

			$html .= '<script>tpgWcFunctionRun();</script>';

			return $html;
		}

		/**
		 * EDD data.
		 *
		 * @param obejct  $post Post.
		 * @param boolean $type Type
		 * @return string
		 */
		private function loadEddData( $post, $type = false ) {
			$html = null;

			setup_postdata( $post );

			if ( $type ) {
				$html .= "<div class='md-header'>";
				$html .= "<h1 class='entry-title'>{$post->post_title}</h1>";
				$html .= '</div>';
				$html .= '<div class="rt-md-content">';
			}

			$html .= "<div class='rt-tpg-container tpg-shortcode-main-wrapper'>";
			$html .= "<div class='rt-row rt-detail'>";
			$html .= "<div class='rt-col-lg-5 rt-col-md-5 rt-col-sm-6 rt-col-xs-12'>";

			if ( has_post_thumbnail() ) {
				$html .= "<div class='feature-image'>";
				$html .= get_the_post_thumbnail( get_the_ID(), 'large' );
				$html .= '</div>';
			}

			$html .= '</div>';
			$html .= "<div class='rt-col-lg-7 rt-col-md-7 rt-col-sm-6 rt-col-xs-12'>";

			if ( ! $type ) {
				$html .= "<h1 class='entry-title'>" . get_the_title() . '</h1>';
			}

			$html .= "<div class='price'>" . edd_price( get_the_ID(), false ) . '</div>';
			$html .= do_shortcode( '[purchase_link id="' . $post->ID . '" text="Add to Cart" style="button"]' );
			$html .= "<div class='tpg-content'>" . apply_filters( 'the_content', get_the_content() ) . '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';

			wp_reset_postdata();

			if ( $type ) {
				$html .= '</div>';
			}

			return $html;
		}
	}
endif;
