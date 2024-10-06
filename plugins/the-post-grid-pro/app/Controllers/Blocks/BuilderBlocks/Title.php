<?php

namespace RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks;

use RT\ThePostGrid\Controllers\Blocks\BlockBase;
use RT\ThePostGrid\Helpers\Fns;

class Title extends BlockBase {

	private $prefix;
	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_asstets' ] );
		$this->prefix     = 'post-title';
		$this->block_type = 'rttpg/tpg-' . $this->prefix . '-block';
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
			'uniqueId'         => [
				'type'    => 'string',
				'default' => '',
			],

			'preview'          => [
				'type'    => 'boolean',
				'default' => false,
			],

			'title_tag'        => array(
				'type'    => 'string',
				'default' => 'h2',
			),

			'title_alignment'  => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-title-wrapper .post-title {text-align: {{title_alignment}}; }',
					],
				],
			],
			'title_color'      => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-single-title-wrapper .post-title {color: {{title_color}}; }',
					],
				],
			],
			'title_typography' => [
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
					(object) [ 'selector' => '{{RTTPG}} .tpg-single-title-wrapper .post-title' ],
				],
			],
			'title_spacing'    => [
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
						'selector' => '{{RTTPG}} .tpg-single-title-wrapper .post-title{{title_spacing}}',
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
		$last_post_id    = Fns::get_last_post_id();
		$uniqueId        = $data['uniqueId'] ?? null;
		$uniqueClass     = 'rttpg-block-postgrid rttpg-block-wrapper rttpg-block-' . $uniqueId;
		$dynamic_classes = '';
		$title_tag       = $data['title_tag'] ?? 'h2';
		ob_start();
		?>
		<div class="<?php echo esc_attr( $uniqueClass ); ?>">
			<div class="tpg-single-title-wrapper clearfix <?php echo esc_attr( $dynamic_classes ); ?>">
				<<?php echo esc_attr( $title_tag ); ?> class="post-title">
					<?php echo get_the_title( $last_post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</<?php echo esc_attr( $title_tag ); ?>>
			</div>
		</div>
		<?php
		do_action( 'tpg_elementor_script' );

		return ob_get_clean();
	}
}