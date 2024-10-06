<?php

namespace RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks;

use RT\ThePostGrid\Controllers\Blocks\BlockBase;
use RT\ThePostGrid\Helpers\Fns;

class Meta extends BlockBase {

	private $prefix;
	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_asstets' ] );
		$this->prefix     = 'post-meta';
		$this->block_type = 'rttpg/tpg-' . $this->prefix . '-block';
	}

	public function block_editor_asstets() {
		if ( is_admin() ) {
			wp_enqueue_script( 'rt-tpg-guten-editor' );
		}
	}

	/**
	 * Register Block
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
			'uniqueId'               => [
				'type'    => 'string',
				'default' => '',
			],
			'preview'                => [
				'type'    => 'boolean',
				'default' => false,
			],
			'is_gutenberg'           => [
				'type'    => 'boolean',
				'default' => true,
			],
			'is_guten_builder'       => [
				'type'    => 'boolean',
				'default' => '1',
			],
			'show_meta'              => [
				'type'    => 'string',
				'default' => 'show',
			],
			'show_category'          => [
				'type'    => 'string',
				'default' => 'show',
			],
			'show_cat_icon'          => [
				'type'    => 'string',
				'default' => '',
			],
			'show_date'              => [
				'type'    => 'string',
				'default' => 'show',
			],
			'show_author'            => [
				'type'    => 'string',
				'default' => 'show',
			],
			'show_tags'              => [
				'type'    => 'string',
				'default' => '',
			],
			'show_comment_count'     => [
				'type'    => 'string',
				'default' => '',
			],
			'show_post_count'        => [
				'type'    => 'string',
				'default' => '',
			],
			'meta_separator'         => [
				'type'    => 'string',
				'default' => 'default',
			],
			'author_prefix'          => [
				'type'    => 'string',
				'default' => 'By',
			],
			'show_meta_icon'         => [
				'type'    => 'string',
				'default' => 'yes',
			],
			'author_icon_visibility' => [
				'type'    => 'string',
				'default' => 'default',
			],
			'show_author_image'      => [
				'type'    => 'string',
				'default' => 'icon',
			],
			'meta_ordering'          => [
				'type'    => 'array',
				'default' => [],
			],
			'post_meta_typography'   => [
				'type'    => 'object',
				'default' => (object) [
					'openTypography' => 1,
					'size'           => (object) [ 'lg' => '', 'unit' => 'px' ],
					'spacing'        => (object) [ 'lg' => '', 'unit' => 'px' ],
					'height'         => (object) [ 'lg' => '', 'unit' => 'px' ],
					'transform'      => '',
					'weight'         => ''
				],
				'style'   => [
					(object) [ 'selector' => '{{RTTPG}} .tpg-single-post-meta *' ]
				],
			],
			'postmeta_alignment'     => [
				'type'    => 'object',
				'default' => [],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta {justify-content: {{postmeta_alignment}};}'
					]
				]
			],
			"meta_wrap_spacing"      => [
				"type"    => "object",
				"default" => [
					'lg' => [
						"isLinked" => false,
						"unit"     => "px",
						"value"    => ''
					]
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta {{meta_wrap_spacing}}'
					]
				]
			],
			"meta_spacing"           => [
				"type"    => "object",
				"default" => [
					'lg' => [
						"isLinked" => false,
						"unit"     => "px",
						"value"    => ''
					]
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta span{{meta_spacing}}'
					]
				]
			],
			"meta_info_style_tabs"   => [
				'type'    => 'string',
				'default' => 'normal'
			],
			'meta_info_color'        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta span {color: {{meta_info_color}}; }'
					]
				]
			],
			'meta_link_color'        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta a {color: {{meta_link_color}}; }'
					]
				]
			],
			'meta_separator_color'   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta .separator {color: {{meta_separator_color}}; }'
					]
				]
			],
			'meta_icon_color'        => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta i {color: {{meta_icon_color}}; }'
					]
				]
			],
			'category_position'      => [
				'type'    => 'string',
				'default' => 'default',
			],
			'category'               => [
				'type'    => 'string',
				'default' => 'category',
			],
			'post_tag'               => [
				'type'    => 'string',
				'default' => 'post_tag',
			],
			'category_bg_style'      => [
				'type'    => 'string',
				'default' => 'default',
			],
			'category_style'         => [
				'type'    => 'string',
				'default' => 'style1',
			],
			'author_image_width'     => [
				'type'    => 'number',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta span img {width:{{author_image_width}}px;height:{{author_image_width}}px;}'
					]
				]
			],
			'meta_link_colo_hover'   => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-post-meta a:hover {color: {{meta_link_colo_hover}}; }'
					]
				]
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
		$last_post_id    = Fns::get_last_post_id();
		$uniqueId        = $data['uniqueId'] ?? null;
		$uniqueClass     = 'rttpg-block-postgrid rttpg-block-wrapper rttpg-block-' . $uniqueId;
		$dynamic_classes = '';
		ob_start();
		?>
        <div class="<?php echo esc_attr( $uniqueClass ) ?>">
            <div class="tpg-single-meta-wrapper clearfix <?php echo esc_attr( $dynamic_classes ) ?>">
                <div class="post-meta-tags tpg-single-post-meta">
					<?php Fns::get_post_meta_html( $last_post_id, $data ); ?>
                </div>
            </div>
        </div>
		<?php
		do_action( 'tpg_elementor_script' );

		return ob_get_clean();
	}

}