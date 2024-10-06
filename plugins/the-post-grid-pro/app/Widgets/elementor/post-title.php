<?php
/**
 * Elementor: Post Title Widget.
 *
 * @package RT_TPG_PRO
 */

use Elementor\Controls_Manager;
use RT\ThePostGridPro\Traits\ELTempleateBuilderTraits;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor: Post Title Widget.
 */
class TPGPostTitle extends Custom_Widget_Base {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * GridLayout constructor.
	 *
	 * @param  array  $data
	 * @param  null   $args
	 *
	 * @throws \Exception
	 */

	private string $title = 'TPG - Post Title';

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		if ( self::is_builder_page_archive() ) {
			$this->title = 'TPG - Archive Title';
		}
		$this->tpg_name     = $this->title;
		$this->tpg_base     = 'tpg-post-title';
		$this->tpg_icon     = 'eicon-post-title tpg-grid-icon'; // .tpg-grid-icon class for just style
		$this->tpg_category = $this->tpg_archive_category;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'tpg_post_title',
			[
				'label' => esc_html__( 'TPG Post Title', 'the-post-grid-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Choose Title Tag', 'the-post-grid-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => [
					'h1' => esc_html__( 'H1', 'the-post-grid-pro' ),
					'h2' => esc_html__( 'H2', 'the-post-grid-pro' ),
					'h3' => esc_html__( 'H3', 'the-post-grid-pro' ),
					'h4' => esc_html__( 'H4', 'the-post-grid-pro' ),
					'h5' => esc_html__( 'H5', 'the-post-grid-pro' ),
					'h6' => esc_html__( 'H6', 'the-post-grid-pro' ),
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'tpg_archive_title_typography',
				'label'    => esc_html__( 'Typography', 'the-post-grid-pro' ),
				'selector' => '{{WRAPPER}} .post-title-tag',
			]
		);

		$this->add_control(
			'tpg_archive_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-title-tag' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label'              => esc_html__( 'Title Spacing', 'the-post-grid-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .post-title-tag' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

	protected function render() {
		$data = $this->get_settings();
		printf(
			'<div class="tpg-archive-post-title"><%1$s class="post-title-tag">%2$s</%1$s></div>',
			esc_attr( $data['title_tag'] ),
			get_the_title( $this->last_post_id ) //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
}
