<?php
/**
 * Filter Hooks Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers\Hooks;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;
use RT\ThePostGridPro\Helpers\Functions;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'FilterHooks' ) ) :
	/**
	 * Filter Hooks Class.
	 */
	class FilterHooks {
		/**
		 * Class constructor
		 */
		public function __construct() {
			add_filter( 'rt_tpg_advanced_filters', [ $this, 'rtTPAdvanceFilters' ] );
			add_filter( 'rt_tpg_layout_misc_options', [ $this, 'layoutMiscSettings' ] );
			add_filter( 'rt_tpg_style_fields', [ $this, 'rtTPGStyleFields' ] );
			add_filter( 'rt_tpg_style_button_css_fields', [ $this, 'rtTPGStyleButtonColorFields' ] );
			add_filter( 'rt_tpg_layouts_type', [ $this, 'rtTPGLayoutType' ], 5 );
			add_filter( 'tpg_layouts', [ $this, 'rtTPGLayoutLists' ] );
			add_filter( 'tpg_image_sizes', [ $this, 'custom_image_size' ] );
			add_filter( 'tpg_field_selection_items', [ $this, 'field_selection_items' ] );
			add_filter( 'rt_tpg_post_orderby', [ $this, 'rtPostOrderBy' ], 10, 3 );
			add_filter( 'rt_tpg_sc_image_settings', [ $this, 'grid_hover_layout_settings' ] );
			add_filter( 'rttpg_pagination_type', [ $this, 'pagination_type' ] );
			add_filter( 'safe_style_css', [ $this, 'kses_style' ] );
			add_filter( 'wp_kses_allowed_html', [ $this, 'custom_post_tags' ], 10, 2 );
			add_filter( 'tpg_account_default_menu_items', [ $this, 'add_myaccount_menu' ] );
		}

		/**
		 * Pagination type
		 *
		 * @param array $types Types.
		 * @return array
		 */
		public function pagination_type( $types ) {
			return array_merge(
				$types,
				[
					'pagination_ajax' => esc_html__( 'Ajax Number Pagination ( Only for Grid )', 'the-post-grid-pro' ),
					'load_more'       => esc_html__( 'Load more button (by ajax loading)', 'the-post-grid-pro' ),
					'load_on_scroll'  => esc_html__( 'Load more on scroll (by ajax loading)', 'the-post-grid-pro' ),
				]
			);
		}

		/**
		 * Advanced filters
		 *
		 * @param array $fields Fields.
		 * @return array
		 */
		public function rtTPAdvanceFilters( $fields ) {
			return array_merge(
				$fields,
				[
					'date_range' => esc_html__( 'Date Range', 'the-post-grid-pro' ),
				]
			);
		}

		/**
		 * Layout settings.
		 *
		 * @param array $fields Fields.
		 * @return array
		 */
		public function grid_hover_layout_settings( $fields ) {
			$hoverFields = [
				'featured_small_image_size' => [
					'type'        => 'select',
					'label'       => esc_html__( 'Feature Image Size (Small)', 'the-post-grid-pro' ),
					'class'       => 'rt-select2',
					'holderClass' => 'rt-feature-small-image-option',
					'options'     => Fns::get_image_sizes(),
				],
				'custom_small_image_size'   => [
					'type'        => 'image_size',
					'label'       => esc_html__( 'Custom Image Size', 'the-post-grid-pro' ),
					'holderClass' => 'rt-sc-custom-small-image-size-holder tpg-hidden',
					'multiple'    => true,
				],
			];

			return array_merge( $fields, $hoverFields );
		}

		/**
		 * Post order by
		 *
		 * @param array $orderBy Order by.
		 * @param bool  $isWoCom Is Woocommerce active.
		 * @param bool  $metaOrder Meta order.
		 * @return array
		 */
		public function rtPostOrderBy( $orderBy, $isWoCom, $metaOrder ) {

			$orderBy['rand']          = esc_html__( 'Random', 'the-post-grid-pro' );
			$orderBy['comment_count'] = esc_html__( 'Number of comments', 'the-post-grid-pro' );
			$orderBy['post__in']      = esc_html__( 'Post In', 'the-post-grid-pro' );

			$wooOrder = [
				'price'  => esc_html__( 'Price', 'the-post-grid-pro' ),
				'rating' => esc_html__( 'AVG Rating', 'the-post-grid-pro' ),
			];

			$orderBy = $isWoCom ? array_merge( $orderBy, $wooOrder ) : $orderBy;
			$orderBy = $metaOrder ? array_merge( $orderBy, Options::rtMetaKeyType() ) : $orderBy;

			return $orderBy;
		}

		/**
		 * Layout type.
		 *
		 * @param array $layoutType Layout type.
		 * @return void
		 */
		public function rtTPGLayoutType( $layoutType ) {

			$layoutType['carousel'] = [
				'title' => esc_html__( 'Slider', 'the-post-grid-pro' ),
				'img'   => rtTPG()->get_assets_uri( 'images/slider.png' ),
			];

			if ( class_exists( 'WooCommerce' ) ) {
				$layoutType['woocommerce'] = [
					'title' => esc_html__( 'WooCommerce', 'the-post-grid-pro' ),
					'img'   => rtTPG()->get_assets_uri( 'images/woocommerce.png' ),
				];
			}

			if ( class_exists( 'Easy_Digital_Downloads' ) ) {
				$layoutType['edd'] = [
					'title' => esc_html__( 'Easy Digital Downloads', 'the-post-grid-pro' ),
					'img'   => rtTPG()->get_assets_uri( 'images/edd.png' ),
				];
			}

			return $layoutType;
		}

		/**
		 * Layout list.
		 *
		 * @param array $layouts Layouts.
		 * @return array
		 */
		public function rtTPGLayoutLists( $layouts ) {
			$proLayouts = [
				'layout4'      => [
					'title'       => esc_html__( 'Grid Layout 3', 'the-post-grid-pro' ),
					'layout'      => 'grid',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-layout-3/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid2.png' ),
				],
				'layout11'     => [
					'title'       => esc_html__( 'Grid Layout 4', 'the-post-grid-pro' ),
					'layout'      => 'grid',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-layout-4/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid9.png' ),
				],
				'layout14'     => [
					'title'       => esc_html__( 'Grid Layout 5', 'the-post-grid-pro' ),
					'layout'      => 'grid',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-layout-5/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid12.png' ),
				],
				'grid_layout1' => [
					'title'       => esc_html__( 'Grid Layout 6', 'the-post-grid-pro' ),
					'layout'      => 'grid',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-layout-6/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_layout8.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'offset05'     => [
					'title'       => esc_html__( 'Grid Layout 7', 'the-post-grid-pro' ),
					'layout'      => 'grid',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-layout-7/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_layout9.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'offset06'     => [
					'title'       => esc_html__( 'Grid Layout 8', 'the-post-grid-pro' ),
					'layout'      => 'grid',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-layout-8/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_layout10.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'layout17'     => [
					'title'       => esc_html__( 'Gallery Layout', 'the-post-grid-pro' ),
					'layout'      => 'grid',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/gallery/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/gallery.png' ),
				],
				'offset04'     => [
					'title'       => esc_html__( 'Grid Hover 4', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-4/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid16.png' ),
				],
				'offset03'     => [
					'title'       => esc_html__( 'Grid Hover 5', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-5/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid15.png' ),
				],
				'layout8'      => [
					'title'       => esc_html__( 'Grid Hover 6', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-6/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid6.png' ),
				],
				'layout9'      => [
					'title'       => esc_html__( 'Grid Hover 7', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-7/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid7.png' ),
				],
				'layout10'     => [
					'title'       => esc_html__( 'Grid Hover 8', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-8/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid8.png' ),
				],
				'layout13'     => [
					'title'       => esc_html__( 'Grid Hover 9', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-9/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid11.png' ),
				],
				'layout15'     => [
					'title'       => esc_html__( 'Grid Hover 10', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-10/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid13.png' ),
				],
				'layout16'     => [
					'title'       => esc_html__( 'Grid Hover 11', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-11/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid14.png' ),
				],
				'grid_hover1'  => [
					'title'       => esc_html__( 'Grid Hover 12', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-12/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_hover10.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'grid_hover2'  => [
					'title'       => esc_html__( 'Grid Hover 13', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-13/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_hover11.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'grid_hover3'  => [
					'title'       => esc_html__( 'Grid Hover 14', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-14/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_hover12.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'grid_hover4'  => [
					'title'       => esc_html__( 'Grid Hover 15', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-15/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_hover13.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'grid_hover5'  => [
					'title'       => esc_html__( 'Grid Hover 16', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-16/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_hover14.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'grid_hover6'  => [
					'title'       => esc_html__( 'Grid Hover 17', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-17/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_hover15.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'grid_hover7'  => [
					'title'       => esc_html__( 'Grid Hover 18', 'the-post-grid-pro' ),
					'layout'      => 'grid_hover',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/grid-hover-18/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/grid_hover16.png' ),
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
				],
				'offset01'     => [
					'title'       => esc_html__( 'List Layout 3', 'the-post-grid-pro' ),
					'layout'      => 'list',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/offset/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/list3.png' ),
				],
				'offset02'     => [
					'title'       => esc_html__( 'List Layout 4', 'the-post-grid-pro' ),
					'layout'      => 'list',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/offset-2/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/list4.png' ),
				],
				'list_layout1' => [
					'title'       => esc_html__( 'List Layout 5', 'the-post-grid-pro' ),
					'layout'      => 'list',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/list-layout-5/',
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/list_layout1.png' ),
				],
				'list_layout2' => [
					'title'       => esc_html__( 'List Layout 6', 'the-post-grid-pro' ),
					'layout'      => 'list',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/list-layout-6/',
					'tag'         => esc_html__( 'New', 'the-post-grid-pro' ),
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/list_layout2.png' ),
				],
				'isotope2'     => [
					'title'       => esc_html__( 'Isotope Layout 2', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-2/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope2.png' ),
				],
				'isotope3'     => [
					'title'       => esc_html__( 'Isotope Layout 3', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-3/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope3.png' ),
				],
				'isotope4'     => [
					'title'       => esc_html__( 'Isotope Layout 4', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-4/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope4.png' ),
				],
				'isotope5'     => [
					'title'       => esc_html__( 'Isotope Layout 5', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-5/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope5.png' ),
				],
				'isotope6'     => [
					'title'       => esc_html__( 'Isotope Layout 6', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-6/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope6.png' ),
				],
				'isotope7'     => [
					'title'       => esc_html__( 'Isotope Layout 7', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-7/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope7.png' ),
				],
				'isotope8'     => [
					'title'       => esc_html__( 'Isotope Layout 8', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-8/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope8.png' ),
				],
				'isotope9'     => [
					'title'       => esc_html__( 'Isotope Layout 9', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-9/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope9.png' ),
				],
				'isotope10'    => [
					'title'       => esc_html__( 'Isotope Layout 10', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-10/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope10.png' ),
				],
				'isotope11'    => [
					'title'       => esc_html__( 'Isotope Layout 11', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-11/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope11.png' ),
				],
				'isotope12'    => [
					'title'       => esc_html__( 'Isotope Layout 12', 'the-post-grid-pro' ),
					'layout'      => 'isotope',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/isotope-layout-12/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/isotope12.png' ),
				],
				'carousel1'    => [
					'title'       => esc_html__( 'Carousel Layout 1', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-1/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel1.png' ),
				],
				'carousel2'    => [
					'title'       => esc_html__( 'Carousel Layout 2', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-2/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel2.png' ),
				],
				'carousel3'    => [
					'title'       => esc_html__( 'Carousel Layout 3', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-3/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel3.png' ),
				],
				'carousel4'    => [
					'title'       => esc_html__( 'Carousel Layout 4', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-4/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel4.png' ),
				],
				'carousel5'    => [
					'title'       => esc_html__( 'Carousel Layout 5', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-5/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel5.png' ),
				],
				'carousel6'    => [
					'title'       => esc_html__( 'Carousel Layout 6', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-6/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel6.png' ),
				],
				'carousel7'    => [
					'title'       => esc_html__( 'Carousel Layout 7', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-7/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel7.png' ),
				],
				'carousel8'    => [
					'title'       => esc_html__( 'Carousel Layout 8', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-8/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel8.png' ),
				],
				'carousel9'    => [
					'title'       => esc_html__( 'Carousel Layout 9', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-9/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel9.png' ),
				],
				'carousel10'   => [
					'title'       => esc_html__( 'Carousel Layout 10', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-10/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel10.png' ),
				],
				'carousel11'   => [
					'title'       => esc_html__( 'Carousel Layout 11', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-11/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel11.png' ),
				],
				'carousel12'   => [
					'title'       => esc_html__( 'Carousel Layout 12', 'the-post-grid-pro' ),
					'layout'      => 'carousel',
					'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/slider-layout-12/',
					'img'         => rtTPG()->get_assets_uri( 'images/layouts/carousel12.png' ),
				],
			];

			if ( class_exists( 'WooCommerce' ) ) {
				$proLayouts = array_merge(
					$proLayouts,
					[
						'wc1'          => [
							'title'       => esc_html__( 'WC Layout 1', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woocommerce-layout-1/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc1.png' ),
						],
						'wc2'          => [
							'title'       => esc_html__( 'WC Layout 2', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woo-layout-2/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc2.png' ),
						],
						'wc3'          => [
							'title'       => esc_html__( 'WC Layout 3', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woo-layout-3/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc3.png' ),
						],
						'wc4'          => [
							'title'       => esc_html__( 'WC Layout 4', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woo-layout-4/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc4.png' ),
						],
						'wc-carousel1' => [
							'title'       => esc_html__( 'WC Carousel 1', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woo-slider-1/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc_slider1.png' ),
						],
						'wc-carousel2' => [
							'title'       => esc_html__( 'WC Carousel 2', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woo-slider-2/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc_slider2.png' ),
						],
						'wc-isotope1'  => [
							'title'       => esc_html__( 'WC Isotope 1', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woo-filter-1/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc_filter1.png' ),
						],
						'wc-isotope2'  => [
							'title'       => esc_html__( 'WC Isotope 2', 'the-post-grid-pro' ),
							'layout'      => 'woocommerce',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/woo-filter-2/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/wc_filter2.png' ),
						],
					]
				);
			}

			if ( class_exists( 'Easy_Digital_Downloads' ) ) {
				$proLayouts = array_merge(
					$proLayouts,
					[
						'edd1'          => [
							'title'       => esc_html__( 'EDD Layout 1', 'the-post-grid-pro' ),
							'layout'      => 'edd',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/edd-layout-1/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/edd1.png' ),
						],
						'edd2'          => [
							'title'       => esc_html__( 'EDD Layout 2', 'the-post-grid-pro' ),
							'layout'      => 'edd',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/edd-layout-2/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/edd2.png' ),
						],
						'edd3'          => [
							'title'       => esc_html__( 'EDD Layout 3', 'the-post-grid-pro' ),
							'layout'      => 'edd',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/edd-layout-3/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/edd3.png' ),
						],
						'edd-carousel1' => [
							'title'       => esc_html__( 'EDD Carousel 1', 'the-post-grid-pro' ),
							'layout'      => 'edd',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/edd-carousel-1/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/edd_slider1.png' ),
						],
						'edd-carousel2' => [
							'title'       => esc_html__( 'EDD Carousel 2', 'the-post-grid-pro' ),
							'layout'      => 'edd',
							'layout_link' => 'https://www.radiustheme.com/demo/plugins/the-post-grid/edd-carousel-2/',
							'img'         => rtTPG()->get_assets_uri( 'images/layouts/edd_slider2.png' ),
						],
						'edd-isotope1'  => [
							'title'  => esc_html__( 'EDD Isotope 1', 'the-post-grid-pro' ),
							'layout' => 'edd',
							'img'    => rtTPG()->get_assets_uri( 'images/layouts/edd_isotope1.png' ),
						],
						'edd-isotope2'  => [
							'title'  => esc_html__( 'EDD Isotope 2', 'the-post-grid-pro' ),
							'layout' => 'edd',
							'img'    => rtTPG()->get_assets_uri( 'images/layouts/edd_isotope2.png' ),
						],
					]
				);
			}

			return array_merge( $layouts, $proLayouts );
		}

		/**
		 * Layout misc settings
		 *
		 * @param array $options Options.
		 * @return array
		 */
		public function layoutMiscSettings( $options ) {
			$position = array_search( 'link_target', array_keys( $options ) );

			if ( $position > - 1 ) {
				$layoutFields = [
					'tgp_not_found_text'    => [
						'type'    => 'text',
						'default' => esc_html__( 'No post found', 'the-post-grid-pro' ),
						'label'   => 'Not found text',
					],
					'margin_option'         => [
						'type'        => 'radio',
						'label'       => esc_html__( 'Margin', 'the-post-grid-pro' ),
						'alignment'   => 'vertical',
						'description' => 'Select the margin for layout',
						'default'     => 'default',
						'options'     => Options::scMarginOpt(),
					],
					'grid_style'            => [
						'type'        => 'radio',
						'label'       => esc_html__( 'Grid style', 'the-post-grid-pro' ),
						'alignment'   => 'vertical',
						'description' => 'Select grid style for layout',
						'default'     => 'even',
						'options'     => Options::scGridOpt(),
					],
					'restriction_user_role' => [
						'type'        => 'select',
						'label'       => esc_html__( 'Content will be visible for', 'the-post-grid-pro' ),
						'class'       => 'rt-select2',
						'multiple'    => true,
						'blank'       => esc_html__( 'Allowed for all', 'the-post-grid-pro' ),
						'description' => esc_html__( 'Leave it blank for all', 'the-post-grid-pro' ),
						'options'     => Functions::getAllUserRoles(),
					],
					'default_preview_image' => [
						'type'        => 'image',
						'label'       => esc_html__( 'Default preview image', 'the-post-grid-pro' ),
						'description' => esc_html__( 'Add an image for default preview', 'the-post-grid-pro' ),
					],
				];
				Functions::array_insert( $options, $position, $layoutFields );
			}

			return $options;
		}

		/**
		 * Style fields.
		 *
		 * @param array $fields Fields.
		 * @return array
		 */
		public function rtTPGStyleFields( $fields ) {
			$position = array_search( 'primary_color', array_keys( $fields ) );

			if ( $position > - 1 ) {
				$styleFields = [
					'tgp_gutter'      => [
						'type'        => 'number',
						'label'       => esc_html__( 'Gutter / Padding', 'the-post-grid-pro' ),
						'description' => __(
							'Unit will be pixel, No need to give any unit. Only integer value will be valid.<br> Leave it blank for default',
							'the-post-grid-pro'
						),
					],
					'overlay_color'   => [
						'type'  => 'text',
						'label' => esc_html__( 'Overlay color', 'the-post-grid-pro' ),
						'class' => 'rt-color',
					],
					'overlay_opacity' => [
						'type'        => 'select',
						'label'       => esc_html__( 'Overlay opacity', 'the-post-grid-pro' ),
						'class'       => 'rt-select2',
						'default'     => .8,
						'options'     => Options::overflowOpacity(),
						'description' => esc_html__( 'Overlay opacity use only positive integer value', 'the-post-grid-pro' ),
					],
					'overlay_padding' => [
						'type'        => 'number',
						'label'       => esc_html__( 'Overlay top padding', 'the-post-grid-pro' ),
						'class'       => 'small-text',
						'description' => esc_html__(
							'Overlay top padding use only positive integer value, e.g : 20 (with out postfix like px, em, % etc). it will displayed by %',
							'the-post-grid-pro'
						),
					],
				];
				Functions::array_insert( $fields, $position, $styleFields );
			}

			return $fields;
		}

		/**
		 * Button color fields
		 *
		 * @param array $fields Fields.
		 * @return array
		 */
		public function rtTPGStyleButtonColorFields( $fields ) {
			$position = array_search( 'button_active_bg_color', array_keys( $fields ) );

			if ( $position > - 1 ) {
				$styleFields = [
					'button_border_color' => [
						'type'        => 'text',
						'label'       => esc_html__( 'Border', 'the-post-grid-pro' ),
						'holderClass' => 'rt-3-column',
						'class'       => 'rt-color',
					],
				];
				Functions::array_insert( $fields, $position, $styleFields );
			}

			return $fields;
		}

		/**
		 * Custom image size
		 *
		 * @param array $sizes Image sizes
		 * @return array
		 */
		public function custom_image_size( $sizes ) {
			$sizes['rt_custom'] = esc_html__( 'Custom Image Size', 'the-post-grid-pro' );

			return $sizes;
		}

		/**
		 * Field selection
		 *
		 * @param array $items Items.
		 * @return array
		 */
		public function field_selection_items( $items ) {
			$items['social_share'] = esc_html__( 'Social share', 'the-post-grid-pro' );
			$items['post_count']   = esc_html__( 'Post View Count', 'the-post-grid-pro' );

			if ( class_exists( 'WooCommerce' ) ) {
				$items['rating'] = esc_html__( 'Rating (WooCommerce)', 'the-post-grid-pro' );
			}

			if ( Fns::is_acf() ) {
				$items['cf'] = esc_html__( 'Custom Fields', 'the-post-grid-pro' );
			}

			return $items;
		}

		/**
		 * Adds in kses.
		 *
		 * @param array $styles Styles.
		 * @return array
		 */
		public function kses_style( $styles ) {
			$styles[] = 'display';
			$styles[] = '--row-w';

			return $styles;
		}

		/**
		 * Add script to allowed wp_kses_post tags
		 *
		 * @param array  $tags Allowed tags, attributes, and/or entities.
		 * @param string $context Context to judge allowed tags by. Allowed values are 'post'.
		 *
		 * @return array
		 */
		public function custom_post_tags( $tags, $context ) {

			if ( 'post' === $context ) {
				$tags['script'] = [
					'src' => true,
				];

				$tags['style'] = [
					'src' => true,
				];
			}

			return $tags;
		}


		/**
		 * MyAccount Menu Added
		 *
		 * @param $endpointes
		 *
		 * @return mixed
		 */
		public static function add_myaccount_menu( $menu_items ) {
			$menu_items['submit-post'] = esc_html__( 'Submit Post', 'the-post-grid' );
			return $menu_items;
		}
	}

endif;
