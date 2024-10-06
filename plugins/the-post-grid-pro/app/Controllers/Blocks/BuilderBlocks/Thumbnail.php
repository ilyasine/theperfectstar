<?php

namespace RT\ThePostGridPro\Controllers\Blocks\BuilderBlocks;

use RT\ThePostGrid\Controllers\Blocks\BlockBase;
use RT\ThePostGrid\Helpers\Fns;

class Thumbnail extends BlockBase {

	private $prefix;
	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_asstets' ] );
		$this->prefix     = 'post-thumbnail';
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
			'uniqueId' => [
				'type'    => 'string',
				'default' => '',
			],

			'preview' => [
				'type'    => 'boolean',
				'default' => false,
			],

			'image_size' => [
				'type'    => 'string',
				'default' => 'full',
			],

			"image_spacing" => [
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
						'selector' => '{{RTTPG}} .tpg-post-thumbnail{{image_spacing}}'
					]
				]
			],

			"image_radius" => [
				"type"    => "object",
				"default" => [
					'lg' => [
						"isLinked" => true,
						"unit"     => "px",
						"value"    => ''
					]
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .tpg-post-thumbnail img{{image_radius}}'
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
		$last_post_id = Fns::get_last_post_id();
		$this->get_script_depends( $data );
		$uniqueId        = $data['uniqueId'] ?? null;
		$uniqueClass     = 'rttpg-block-postgrid rttpg-block-wrapper rttpg-block-' . $uniqueId;
		$dynamic_classes = '';
		ob_start();
		?>
        <div class="<?php echo esc_attr( $uniqueClass ) ?>">
            <div class="tpg-single-post-thumbnail-wrap clearfix <?php echo esc_attr( $dynamic_classes ) ?>">
                <div class="tpg-post-thumbnail">
					<?php echo get_the_post_thumbnail( $last_post_id, $data['image_size'] ) ?>
                </div>
            </div>
        </div>
		<?php
		do_action( 'tpg_elementor_script' );

		return ob_get_clean();
	}

}