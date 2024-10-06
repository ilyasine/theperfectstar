<?php

namespace RT\ThePostGridPro\Controllers\Blocks;

use RT\ThePostGrid\Controllers\Blocks\BlockController\SettingsTabController;
use RT\ThePostGrid\Controllers\Blocks\BlockController\StyleTabController;
use RT\ThePostGrid\Controllers\Blocks\BlockController\ContentTabController;
use RT\ThePostGridPro\Controllers\Blocks\BlockController\SliderSettingsAndStyle;
use RT\ThePostGrid\Controllers\Blocks\BlockBase;
use RT\ThePostGrid\Helpers\Fns;

class SliderLayout extends BlockBase {

	private $prefix;
	private $attribute_args;
	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_asstets' ] );
		$this->prefix         = 'slider';
		$this->block_type     = 'rttpg/tpg-' . $this->prefix . '-layout';
		$this->attribute_args = [
			'prefix'         => $this->prefix,
			'default_layout' => 'slider-layout1',
		];
	}

	public function block_editor_asstets() {
		if ( is_admin() ) {
			wp_enqueue_style( 'swiper' );
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'swiper' );
			wp_enqueue_script( 'rt-tpg-guten-editor' );
		}
	}

	/**
	 * Register Block
	 *
	 * @return void
	 */
	public function register_blocks() {
		register_block_type(
			$this->block_type,
			[
				'render_callback' => [ $this, 'render_block' ],
				'attributes'      => $this->get_attributes(),
			]
		);
	}

	/**
	 * Get attributes
	 *
	 * @param bool $default
	 *
	 * @return array
	 */
	public function get_attributes() {

		/**
		 * All Attribute
		 * Content Tab | Settings Tab | Style Tab
		 */
		$content_attribute  = ContentTabController::get_controller( $this->attribute_args );
		$settings_attribute = SettingsTabController::get_controller();
		$style_attribute    = StyleTabController::get_controller();
		$slider_settings    = SliderSettingsAndStyle::get_controller();

		return array_merge( $content_attribute, $settings_attribute, $style_attribute, $slider_settings );
	}

	/**
	 * @param array $data
	 *
	 * @return false|string
	 */
	public function render_block( $data ) {

		$this->get_script_depends( $data );
		$_prefix = $data['prefix'];
		$_layout = $data[ $_prefix . '_layout' ];

		if ( ! rtTPG()->hasPro() && ! is_admin() ) { ?>
			<h3 style="text-align: center"><?php echo esc_html__( 'Please upgrade to pro for slider layout!', 'the-post-grid-pro' ); ?></h3>
			<?php
			return;
		}

		// $this->get_script_depends( $data );

		if ( ! rtTPG()->hasPro() && ! in_array( $data[ $_prefix . '_layout' ], [ 'grid-layout1', 'grid-layout4', 'grid-layout3' ] ) ) {
			$data[ $_prefix . '_layout' ] = 'grid-layout1';
		}

		// Query
		$query_args     = $this->post_query_guten( $data, $_prefix );
		$query          = new \WP_Query( $query_args );
		$rand           = wp_rand();
		$layoutID       = 'rt-tpg-container-' . $rand;
		$posts_per_page = $data['display_per_page'] ? $data['display_per_page'] : $data['post_limit'];

		/**
		 * TODO: Get Post Data for render post
		 */

		$post_data = Fns::get_render_data_set( $data, $query->max_num_pages, $posts_per_page, $_prefix, 'yes' );

		// Category Source if exists
		if ( isset( $data['category_source'] ) ) {
			$post_data[ $data['post_type'] . '_taxonomy' ] = $data['category_source'];
		}
		// Tag source
		if ( isset( $data['tag_source'] ) ) {
			$post_data[ $data['post_type'] . '_tags' ] = $data['tag_source'];
		}

		$post_data['lazy_load'] = $data['lazyLoad'];

		$default_grid_column_desktop = '3';
		$default_grid_column_tab     = '2';
		$default_grid_column_mobile  = '1';

		if ( $_layout == 'slider-layout13' ) {
			$default_grid_column_desktop = '1';
			$default_grid_column_tab     = '1';
			$default_grid_column_mobile  = '1';
		}

		$grid_column_desktop = ( isset( $data['slider_column']['lg'] ) && 0 != $data['slider_column']['lg'] ) ? $data['slider_column']['lg'] : $default_grid_column_desktop;
		$grid_column_tab     = ( isset( $data['slider_column']['md'] ) && 0 != $data['slider_column']['md'] ) ? $data['slider_column']['md'] : $default_grid_column_tab;
		$grid_column_mobile  = ( isset( $data['slider_column']['sm'] ) && 0 != $data['slider_column']['sm'] ) ? $data['slider_column']['sm'] : $default_grid_column_mobile;

		if ( in_array( $_layout, [ 'slider-layout10', 'slider-layout11' ] ) ) {
			$grid_column_desktop = $grid_column_tab = $grid_column_mobile = '1';
		}

		$_layout      = $data[ $_prefix . '_layout' ];
		$dynamicClass = Fns::get_dynamic_class_gutenberg( $data );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $dynamicClass ); ?>">
			<div class="rt-container-fluid rt-tpg-container tpg-el-main-wrapper slider-layout-main loading <?php echo esc_attr( $_layout . '-main' ); ?>"
				 id="<?php echo esc_attr( $layoutID ); ?>"
				 data-layout="<?php echo esc_attr( $data[ $_prefix . '_layout' ] ); ?>"
				 data-grid-style=""
				 data-desktop-col="<?php echo esc_attr( $grid_column_desktop ); ?>"
				 data-tab-col="<?php echo esc_attr( $grid_column_tab ); ?>"
				 data-mobile-col="<?php echo esc_attr( $grid_column_mobile ); ?>"
				 data-sc-id="elementor"
				 data-el-query=''>
				<?php

				$settings = get_option( rtTPG()->options['settings'] );
				if ( isset( $settings['tpg_load_script'] ) || isset( $settings['tpg_enable_preloader'] ) ) {
					?>
					<div id="bottom-script-loader" class="bottom-script-loader">
						<div class="rt-ball-clip-rotate">
							<div></div>
						</div>
					</div>
					<?php
				}

				$wrapper_class   = [];
				$wrapper_class[] = 'rt-content-loader grid-behaviour';

				if ( $_layout == 'slider-layout1' ) {
					$wrapper_class[] = 'grid-layout1 ';
				} elseif ( $_layout == 'slider-layout2' ) {
					$wrapper_class[] = 'grid-layout3';
				} elseif ( $_layout == 'slider-layout3' ) {
					$wrapper_class[] = 'grid-layout4';
				} elseif ( $_layout == 'slider-layout4' ) {
					$wrapper_class[] = 'grid-layout7';
				} elseif ( $_layout == 'slider-layout5' ) {
					$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout1 grid_hover_layout_wrapper';
				} elseif ( $_layout == 'slider-layout6' ) {
					$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout3 grid_hover_layout_wrapper';
				} elseif ( $_layout == 'slider-layout7' ) {
					$wrapper_class[] = 'grid_hover-layout5 grid_hover_layout_wrapper';
				} elseif ( $_layout == 'slider-layout8' ) {
					$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout10 grid_hover_layout_wrapper';
				} elseif ( $_layout == 'slider-layout9' ) {
					$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout11 grid_hover_layout_wrapper';
				} elseif ( $_layout == 'slider-layout10' ) {
					$wrapper_class[] = 'grid_hover-layout5 grid_hover-layout7 grid_hover_layout_wrapper';
				} elseif ( $_layout == 'slider-layout11' ) {
					$wrapper_class[] = ' grid_hover-layout5 slider-layout';
				} elseif ( $_layout == 'slider-layout12' ) {
					$wrapper_class[] = ' grid_hover-layout5 slider-layout';
				}

				$wrapper_class[] = $_prefix . '_layout_wrapper';

				// Slider Options
				$slider_data = [
					'speed'            => absint( $data['speed'] ),
					'autoPlayTimeOut'  => absint( $data['autoplaySpeed'] ),
					'autoPlay'         => $data['autoplay'] == 'yes' ? true : false,
					'stopOnHover'      => $data['stopOnHover'] == 'yes' ? true : false,
					'nav'              => $data['arrows'] == 'yes' ? true : false,
					'dots'             => $data['dots'] == 'yes' ? true : false,
					'loop'             => $data['infinite'] == 'yes' ? true : false,
					'lazyLoad'         => $data['lazyLoad'] == 'yes' ? true : false,
					'autoHeight'       => $data['autoHeight'] == 'yes' ? true : false,
					'dynamic_dots'     => $data['dynamic_dots'] == 'yes' ? true : false,
					'grabCursor'       => $data['grabCursor'] == 'yes' ? true : false,
					'slider_per_group' => $data['slider_per_group'] == 'yes' ? true : false,
				];

				// section title settings
				echo "<div class='tpg-header-wrapper'>";
				Fns::get_section_title( $data );
				echo '</div>';
				?>

				<div class="slider-main-wrapper <?php echo esc_attr( $_layout ); ?>">
					<div class="rt-swiper-holder swiper"
						 data-rtowl-options='<?php echo wp_json_encode( $slider_data ); ?>'
						 dir="<?php echo esc_attr( $data['slider_direction'] ); ?>"
					>
						<div class="swiper-wrapper <?php echo esc_attr( implode( ' ', $wrapper_class ) ); ?>">
							<?php
							if ( $query->have_posts() ) {
								$pCount = 1;

								while ( $query->have_posts() ) {
									$query->the_post();
									set_query_var( 'tpg_post_count', $pCount );
									set_query_var( 'tpg_total_posts', $query->post_count );
									Fns::tpg_template( $post_data, 'gutenberg' );

									if ( $_layout == 'slider-layout10' && $pCount == 5 ) {
										$pCount = 0;
									}

									$pCount++;
								}
							} else {
								if ( $data['no_posts_found_text'] ) {
									printf( "<div class='no_posts_found_text'>%s</div>", esc_html( $data['no_posts_found_text'] ) );
								} else {
									printf( "<div class='no_posts_found_text'>%s</div>", esc_html__( 'No post found', 'the-post-grid-pro' ) );
								}
							}
							wp_reset_postdata();
							?>
						</div>

					</div>


					<?php if ( ! in_array( $_layout, [ 'slider-layout11', 'slider-layout12' ] ) ) : ?>
						<!--swiper-pagination-horizontal-->
						<?php if ( $data['dots'] == 'yes' ) : ?>
							<div class="swiper-pagination"></div>
						<?php endif; ?>

						<?php if ( $data['arrows'] == 'yes' ) : ?>
							<div class="swiper-navigation">
								<div class="slider-btn swiper-button-prev"></div>
								<div class="slider-btn swiper-button-next"></div>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( in_array( $_layout, [ 'slider-layout11', 'slider-layout12' ] ) ) : ?>
						<div class="slider-thumb-main-wrapper">
							<div class="swiper-thumb-wrapper gallery-thumbs swiper">
								<div class="swiper-wrapper">
									<?php
									if ( $query->have_posts() ) {
										$pCount = 1;
										while ( $query->have_posts() ) {
											$query->the_post();
											set_query_var( 'tpg_post_count', $pCount );
											set_query_var( 'tpg_total_posts', $query->post_count );
											?>
											<div class="swiper-slide">
												<div class="post-thumbnail-wrap">
													<div class="p-thumbnail">
														<?php echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' ); ?>
													</div>
													<div class="p-content">
														<div class="post-taxonomy">
															<?php
															$_cat_id = $data['category_source'] ?? 'category';
															Fns::print_html( Fns::rt_get_the_term_list( get_the_ID(), $_cat_id, null ), true );
															?>
														</div>
														<h3 class="thumb-title"><?php the_title(); ?></h3>
														<?php
														if ( 'show' == $data['show_meta'] ) :

															$new_post_data                       = $post_data;
															$new_post_data['show_author_image']  = 'icon';
															$new_post_data['show_tags']          = 'none';
															$new_post_data['show_comment_count'] = 'none';
															$new_post_data['show_post_count']    = 'none';

															?>
															<div class="post-meta-tags rt-el-post-meta">
																<?php Fns::get_post_meta_html( get_the_ID(), $new_post_data ); ?>
															</div>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<?php
											$pCount++;
										}
									} else {
										if ( $data['no_posts_found_text'] ) {
											printf( "<div class='no_posts_found_text'>%s</div>", esc_html( $data['no_posts_found_text'] ) );
										} else {
											printf( "<div class='no_posts_found_text'>%s</div>", esc_html__( 'No post found', 'the-post-grid-pro' ) );
										}
									}
									wp_reset_postdata();
									?>
								</div>
								<div class="swiper-thumb-pagination"></div>
							</div>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
		<?php

		do_action( 'tpg_elementor_script' );

		return ob_get_clean();
	}
}