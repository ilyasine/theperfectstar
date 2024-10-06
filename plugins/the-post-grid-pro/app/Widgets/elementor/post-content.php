<?php
/**
 * Elementor: Post Content Widget.
 *
 * @package RT_TPG_PRO
 */

use Elementor\Controls_Manager;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor: Post Content Widget.
 */
class TPGPostContent extends Custom_Widget_Base {

	/**
	 * GridLayout constructor.
	 *
	 * @param  array $data
	 * @param  null  $args
	 *
	 * @throws \Exception
	 */


	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->tpg_name     = esc_html__( 'TPG - Post Content', 'the-post-grid-pro' );
		$this->tpg_base     = 'tpg-post-content';
		$this->tpg_icon     = 'eicon-single-post tpg-grid-icon'; // .tpg-grid-icon class for just style
		$this->tpg_category = $this->tpg_archive_category;
	}


	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );
			array_push( $style, 'rt-tpg-block' );
		}

		return $style;
	}


	protected function register_controls() {
		$this->start_controls_section(
			'tpg_post_title',
			[
				'label' => esc_html__( 'TPG Post Content', 'the-post-grid-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => esc_html__( 'Content Typography', 'the-post-grid-pro' ),
				'selector' => '{{WRAPPER}} .tpg-details-post-content',
			]
		);

		$this->add_control(
			'p_mb',
			[
				'label'      => esc_html__( 'Paragrap Margin Bottom', 'the-post-grid-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .tpg-details-post-content p:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'details'    => esc_html__( 'Margin bottom for each paragraph (p) item', 'the-post-grid-pro' ),
			]
		);

		$this->add_control(
			'content_spacing',
			[
				'label'              => esc_html__( 'Content Spacing', 'the-post-grid-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-details-post-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() { ?>
		<div class="tpg-details-post-content">
			<?php echo get_the_content( null, null, $this->last_post_id ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
	}

}
