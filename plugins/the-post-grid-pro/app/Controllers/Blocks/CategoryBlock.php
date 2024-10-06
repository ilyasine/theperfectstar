<?php

namespace RT\ThePostGridPro\Controllers\Blocks;

use RT\ThePostGrid\Controllers\Blocks\BlockBase;
use RT\ThePostGrid\Helpers\Fns;

class CategoryBlock extends BlockBase {

	private $prefix;
	private $attribute_args;
	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_asstets' ] );
		$this->prefix         = 'category';
		$this->block_type     = 'rttpg/tpg-' . $this->prefix . '-block';
		$this->attribute_args = [
			'prefix'         => $this->prefix,
			'default_layout' => 'slider-layout1',
		];
	}

	public function block_editor_asstets() {
		if ( is_admin() ) {
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

		return [
			'uniqueId' => [
				'type'    => 'string',
				'default' => '',
			],

			'preview' => [
				'type'    => 'boolean',
				'default' => false,
			],

			'category_layout' => [
				'type'    => 'string',
				'default' => 'category-layout1',
			],

			'grid_column' => [
				'type'    => 'object',
				'default' => [
					'lg' => 0,
					'md' => 0,
					'sm' => 0,
				],
			],

			'category_lists' => [
				'type'    => 'array',
				'default' => [],
			],

			'cat_gap' => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-row {margin-left:-{{cat_gap}};margin-right:-{{cat_gap}}}
						{{RTTPG}} .rt-row > .cat-item-col {padding-left:{{cat_gap}};padding-right:{{cat_gap}};padding-bottom:calc({{cat_gap}} * 2)}',
					],
				],
			],

			'category_alignment' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper {text-align: {{category_alignment}}; }',
					],
				],
			],

			// Category Style

			'cat_tag' => [
				'type'    => 'string',
				'default' => 'h3',
			],

			'category_typography' => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .category-name a' ],
				],
			],

			'cat_spacing' => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => false,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .category-name{{cat_spacing}}',
					],
				],
			],

			'cat_padding' => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .category-name{{cat_padding}}',
					],
				],
			],

			'cat_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .category-name a{color: {{cat_color}}; }',
					],
				],
			],

			'category_color_hover' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .category-name a:hover {color: {{category_color_hover}}; }',
					],
				],
			],

			// Image Style
			'img_visibility'       => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'image_size' => [
				'type'    => 'string',
				'default' => 'medium_large',
			],

			'image_width'         => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .cat-thumb {max-width: {{image_width}}; }',
					],
				],
			],
			'image_height'        => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .cat-thumb {height: {{image_height}}; }',
					],
				],
			],
			'image_border_radius' => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .cat-thumb .cat-link{{image_border_radius}}',
					],
				],
			],

			'image_border' => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .cat-thumb .cat-link',
					],
				],
			],

			'cat_thumb_bg' => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .cat-thumb .cat-link .overlay',
					],
				],
			],

			'cat_thumb_bg_hover' => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .cat-thumb:hover .cat-link .overlay',
					],
				],
			],

			// Count Style

			'count_visibility' => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'show_bracket' => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'count_position' => [
				'type'    => 'string',
				'default' => 'thumb',
			],

			'count_typography' => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'spacing'        => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'height'         => (object) [
						'lg'   => '',
						'unit' => 'px',
					],
					'transform'      => '',
					'weight'         => '',
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .count' ],
				],
			],

			'count_padding' => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .count{{count_padding}}',
					],
				],
			],

			'count_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .count {color: {{count_color}}; }',
					],
				],
			],

			'count_bg' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .count {background-color: {{count_bg}}; }',
					],
				],
			],

			'count_border' => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .count',
					],
				],
			],

			'count_left_pos' => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .count-thumb {left: {{count_left_pos}};right:auto; }',
					],
				],
			],
			'count_top_pos'  => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .count-thumb {top: {{count_top_pos}};bottom:auto; }',
					],
				],
			],

			'count_right_pos'  => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .count-thumb {right: {{count_right_pos}};left:auto; }',
					],
				],
			],
			'count_bottom_pos' => [
				'type'    => 'object',
				'default' => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-category-block-wrapper .count-thumb {bottom: {{count_bottom_pos}}; top:auto; }',
					],
				],
			],

			// Card Style
			'card_background'  => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .card-inner-wrapper',
					],
				],
			],

			'card_background_hover' => [
				'type'    => 'object',
				'default' => (object) [
					'openBGColor' => 0,
					'type'        => 'classic',
					'classic'     => (object) [
						'color'       => '',
						'img'         => (object) [
							'imgURL' => '',
							'imgID'  => '',
						],
						'imgProperty' => (object) [
							'imgPosition'   => (object) [ 'lg' => '' ],
							'imgAttachment' => (object) [ 'lg' => '' ],
							'imgRepeat'     => (object) [ 'lg' => '' ],
							'imgSize'       => (object) [ 'lg' => '' ],
						],
					],
					'gradient'    => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .card-inner-wrapper',
					],
				],
			],

			'card_border' => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '',
					'style'         => '',
					'width'         => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .card-inner-wrapper',
					],
				],
			],

			'card_padding' => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .card-inner-wrapper {{card_padding}}',
					],
				],
			],

			'card_radius' => [
				'type'    => 'object',
				'default' => [
					'lg' => [
						'isLinked' => true,
						'unit'     => 'px',
						'value'    => '',
					],
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .card-inner-wrapper {{card_radius}}',
					],
				],
			],

		];
	}

	/**
	 * @return void
	 */
	public function get_script_depends( $data ) {
		$settings = get_option( rtTPG()->options['settings'] );
		if ( isset( $settings['tpg_load_script'] ) ) {
			wp_enqueue_style( 'rt-fontawsome' );
			wp_enqueue_style( 'rt-flaticon' );
			wp_enqueue_style( 'rt-tpg-block' );
		}
	}

	/**
	 * @param array $data
	 *
	 * @return false|string
	 */
	public function render_block( $data ) {
		$this->get_script_depends( $data );
		// $data       = $this->get_settings();
		$category = $data['category_lists'];

		if ( ! empty( $category ) && is_array( $category ) ) {
			$categories = wp_list_pluck( $category, 'value' );
		} else {
			$categories = get_terms(
				[
					'taxonomy'   => 'category',
					'orderby'    => 'count',
					'order'      => 'DESC',
					'hide_empty' => 0,
					'fields'     => 'ids',
					'number'     => 5,
				]
			);
		}

		$uniqueId        = isset( $data['uniqueId'] ) ? $data['uniqueId'] : null;
		$uniqueClass     = 'rttpg-block-postgrid rttpg-block-wrapper rttpg-block-' . $uniqueId;
		$dynamic_classes = $data['category_layout'] == 'category-layout3' ? ' category-layout2' : '';

		ob_start();
		?>
        <div class="<?php echo esc_attr( $uniqueClass ); ?>">
            <div class="tpg-category-block-wrapper clearfix <?php echo esc_attr( $data['category_layout'] . ' ' . $dynamic_classes ); ?>">
				<?php if ( is_array( $categories ) ) { ?>
                <div class="rt-row">
					<?php
					$category_date                     = [];
					$category_date['layout']           = $data['category_layout'];
					$category_date['image_size']       = $data['image_size'];
					$category_date['grid_column']      = $data['grid_column'];
					$category_date['count_position']   = $data['count_position'];
					$category_date['show_bracket']     = $data['show_bracket'];
					$category_date['cat_tag']          = $data['cat_tag'];
					$category_date['count_visibility'] = $data['count_visibility'];
					$category_date['img_visibility']   = $data['img_visibility'];
					foreach ( $categories as $cat ) {
						$category_date['cat'] = $cat;
						Fns::tpg_template( $category_date, 'gutenberg' );
					}
					?>
                </div>
            </div>
			<?php
			} else {
				?>
                <p style="padding: 30px;background: #d1ecf1;"><?php echo esc_html__( 'Please choose few categories from the category lists.', 'the-post-grid-pro' ); ?></p>
				<?php
			}
			?>
        </div>
		<?php
		do_action( 'tpg_elementor_script' );

		return ob_get_clean();
	}
}