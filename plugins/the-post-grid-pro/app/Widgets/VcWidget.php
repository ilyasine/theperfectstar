<?php
/**
 * Visual Composer Widget.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Widgets;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'VcWidget' ) ) :
	/**
	 * Visual Composer Widget.
	 */
	class VcWidget {

		function __construct() {
			if ( ! defined( 'WPB_VC_VERSION' ) ) {
				return;
			}
			add_action( 'vc_before_init', [ $this, 'postGridIntegration' ] );
		}

		function scListA() {
			$sc            = [];
			$scQ           = get_posts(
				[
					'post_type'      => 'rttpg',
					'order_by'       => 'title',
					'order'          => 'DESC',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				]
			);
			$sc['Default'] = '';
			if ( count( $scQ ) ) {
				foreach ( $scQ as $post ) {
					$sc[ $post->post_title ] = $post->ID;
				}
			}
			return $sc;
		}


		function postGridIntegration() {

			vc_map(
				[
					'name'              => esc_html__( 'The Post Grid', 'the-post-grid-pro' ),
					'base'              => 'the-post-grid-pro',
					'class'             => '',
					'icon'              => rtTPG()->get_assets_uri( 'images/icon-32x32.png' ),
					'controls'          => 'full',
					'category'          => 'Content',
					'admin_enqueue_js'  => '',
					'admin_enqueue_css' => '',
					'params'            => [
						[
							'type'        => 'dropdown',
							'heading'     => esc_html__( 'Short Code', 'the-post-grid-pro' ),
							'param_name'  => 'id',
							'value'       => $this->scListA(),
							'admin_label' => true,
							'description' => esc_html__( 'Short Code list', 'the-post-grid-pro' ),
						],
					],
				]
			);
		}
	}

endif;
