<?php
/**
 * Tax Order Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers\Admin;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

//phpcs:disable WordPress.Security.NonceVerification.Recommended

/**
 * Tax Order Controller Class.
 */
class TaxOrderController {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'wp_ajax_tpg-get-taxonomy-list', [ $this, 'tpg_get_taxonomy_list' ] );
		add_action( 'wp_ajax_tpg-get-term-list', [ $this, 'tpg_get_term_list' ] );
		add_action( 'wp_ajax_tpg-update-term-order', [ $this, 'tpg_update_term_order' ] );
	}

	/**
	 * Get taxonomy list
	 *
	 * @return void
	 */
	public function tpg_get_taxonomy_list() {
		$data  = $msg = null;
		$error = true;

		if ( Fns::verifyNonce() ) {
			$pt = ( ! empty( $_REQUEST['pt'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['pt'] ) ) : null );

			if ( $pt ) {
				$error            = false;
				$taxonomy_objects = Functions::getAllTpgTaxonomyObject( $pt );
				$data            .= "<option value=''>" . esc_html__( 'Select one taxonomy', 'the-post-grid-pro' ) . '</option>';

				if ( ! empty( $taxonomy_objects ) ) {
					foreach ( $taxonomy_objects as $tax ) {
						$data .= '<option value="' . esc_attr( $tax->name ) . '">' . esc_html( $tax->label ) . '</option>';
					}
				} else {
					$msg .= '<p>' . esc_html__( 'No terms found', 'the-post-grid-pro' ) . '</p>';
				}
			} else {
				$msg .= '<p>' . esc_html__( 'Select a post type', 'the-post-grid-pro' ) . '</p>';
			}
		} else {
			$msg .= '<p>' . esc_html__( 'Security error', 'the-post-grid-pro' ) . '</p>';
		}

		wp_send_json(
			[
				'data'  => $data,
				'error' => $error,
				'msg'   => $msg,
			]
		);

		die();
	}

	/**
	 * Update term order
	 *
	 * @return void
	 */
	public function tpg_update_term_order() {
		$html  = $msg = null;
		$error = true;

		if ( Fns::verifyNonce() ) {
			$terms = ( ! empty( $_REQUEST['terms'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_REQUEST['terms'] ) ) ) : [] );

			if ( $terms && ! empty( $terms ) ) {
				$error = false;
				foreach ( $terms as $key => $term_id ) {
					update_term_meta( $term_id, '_rt_order', $key + 1 );
				}
			} else {
				$msg .= '<p>' . esc_html__( 'No terms in list', 'the-post-grid-pro' ) . '</p>';
			}
		} else {
			$msg .= '<p>' . esc_html__( 'Security error', 'the-post-grid-pro' ) . '</p>';
		}

		wp_send_json(
			[
				'data'  => $html,
				'error' => $error,
				'msg'   => $msg,
			]
		);

		die();
	}

	/**
	 * Get term list
	 *
	 * @return void
	 */
	public function tpg_get_term_list() {
		$html  = $msg = null;
		$error = true;

		if ( Fns::verifyNonce() ) {
			$tax = ( ! empty( $_REQUEST['tax'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tax'] ) ) : null );

			if ( $tax ) {
				$error      = false;
				$temp_terms = get_terms(
					[
						'taxonomy'   => $tax,
						'hide_empty' => false,
					]
				);

				if ( ! empty( $temp_terms ) && empty( $temp_terms['errors'] ) ) {
					foreach ( $temp_terms as $term ) {
						$order = get_term_meta( $term->term_id, '_rt_order', true );

						if ( '' === $order ) {
							update_term_meta( $term->term_id, '_rt_order', 0 );
						}
					}
				}

				$terms = get_terms(
					[
						'taxonomy'   => $tax,
						'orderby'    => 'meta_value_num',
						'meta_key'   => '_rt_order',  //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'order'      => 'ASC',
						'hide_empty' => false,
					]
				);

				if ( ! empty( $terms ) ) {
					$html .= '<ul id="order-target" data-taxonomy="' . esc_attr( $tax ) . '">';

					foreach ( $terms as $term ) {
						$html .= '<li data-id="' . absint( $term->term_id ) . '"><span>' . esc_html( $term->name ) . '</span></li>';
					}
					$html .= '</ul>';
				} else {
					$html .= '<p>' . esc_html__( 'No term found', 'the-post-grid-pro' ) . '</p>';
				}
			} else {
				$html .= '<p>' . esc_html__( 'Select a taxonomy', 'the-post-grid-pro' ) . '</p>';
			}
		} else {
			$html .= '<p>' . esc_html__( 'Security error', 'the-post-grid-pro' ) . '</p>';
		}

		wp_send_json(
			[
				'data'  => $html,
				'error' => $error,
				'msg'   => $msg,
			]
		);

		die();
	}

	/**
	 * Admin scripts
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		global $pagenow, $typenow;

		// validate page.
		if ( ! in_array( $pagenow, [ 'edit.php' ], true ) && ! empty( $_REQUEST['page'] ) && 'tgp_taxonomy_order' !== $_REQUEST['page'] ) {
			return;
		}

		if ( rtTPG()->post_type !== $typenow ) {
			return;
		}

		$select2Id = 'rt-select2';

		/*
		if ( class_exists( 'WPSEO_Admin_Asset_Manager' ) && class_exists( 'Avada' ) ) {
			$select2Id = 'yoast-seo-select2';
		} elseif ( class_exists( 'WPSEO_Admin_Asset_Manager' ) ) {
			$select2Id = 'yoast-seo-select2';
		} elseif ( class_exists( 'Avada' ) ) {
			$select2Id = 'select2-avada-js';
		}*/

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( $select2Id );
		wp_enqueue_script( 'tpg-admin-taxonomy' );

		wp_enqueue_style( 'rt-select2' );
		wp_enqueue_style( 'rt-tpg-admin' );

		wp_localize_script(
			'tpg-admin-taxonomy',
			'rttpg',
			[
				'nonceID' => esc_attr( \rtTPG()->nonceId() ),
				'nonce'   => esc_attr( wp_create_nonce( \rtTPG()->nonceText() ) ),
				'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'uid'     => get_current_user_id(),
			]
		);
	}
}
