<?php
/**
 * Elementor: Post Thumbnail Widget.
 *
 * @package RT_TPG_PRO
 */

use Elementor\Controls_Manager;
use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

//phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude

/**
 * Elementor: Post Thumbnail Widget.
 *
 * @package RT_TPG_PRO
 */
class TPGPostThumbnail extends Custom_Widget_Base {

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
		$this->tpg_name     = esc_html__( 'TPG - Post Thumbnail', 'the-post-grid-pro' );
		$this->tpg_base     = 'tpg-post-thumbnail';
		$this->tpg_icon     = 'eicon-image tpg-grid-icon'; // .tpg-grid-icon class for just style
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
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name'    => 'thumbnail',
				'exclude' => [ 'custom' ],
				'include' => [],
				'default' => 'large',
			]
		);

		$this->add_control(
			'thubnail_position',
			[
				'label'   => esc_html__( 'Border Style', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'top'                    => esc_html__( 'Top', 'the-post-grid-pro' ),
					'tpg-single-image-left'  => esc_html__( 'Left', 'the-post-grid-pro' ),
					'tpg-single-image-right' => esc_html__( 'Right', 'the-post-grid-pro' ),
				],
			]
		);

		$this->add_control(
			'thumbnail_spacing',
			[
				'label'              => esc_html__( 'Thumbnail Spacing', 'the-post-grid-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-post-thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'border',
				'label'    => esc_html__( 'Thumbnail Border', 'the-post-grid-pro' ),
				'selector' => '{{WRAPPER}} .tpg-post-thumbnail img',
			]
		);

		$this->add_control(
			'thumb_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the-post-grid-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tpg-post-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'caption_options',
			[
				'label'   => esc_html__( 'Thumbnail Caption', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$this->add_control(
			'show_caption',
			[
				'label'        => esc_html__( 'Show Caption', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_caption_label',
			[
				'label'        => esc_html__( 'Show Caption Label', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'caption_label',
			[
				'label'       => esc_html__( 'Caption Label', 'the-post-grid-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Photo Credit:', 'the-post-grid-pro' ),
				'placeholder' => esc_html__( 'Type your title here', 'the-post-grid-pro' ),
				'condition'   => [
					'show_caption'       => 'yes',
					'show_caption_label' => 'yes',
				],
			]
		);

		$this->add_control(
			'caption_style_heading',
			[
				'label'     => esc_html__( 'Caption Style', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'caption_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'the-post-grid-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'the-post-grid-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'the-post-grid-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tpg-thumb-caption' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'      => 'content_typography',
				'label'     => esc_html__( 'Typography', 'the-post-grid-pro' ),
				'selector'  => '{{WRAPPER}} .tpg-thumb-caption',
				'exclude'   => [ 'font_weight' ],
				'condition' => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'caption_spacing',
			[
				'label'              => esc_html__( 'Caption Spacing', 'the-post-grid-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-thumb-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'caption_color',
			[
				'label'     => esc_html__( 'Caption Color', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-thumb-caption' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'caption_border_color',
			[
				'label'     => esc_html__( 'Caption Border Color', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-thumb-caption' => 'border-left-color: {{VALUE}}',
				],
				'condition' => [
					'show_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Label Color', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-thumb-caption .caption-label' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_caption'       => 'yes',
					'show_caption_label' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$data = $this->get_settings(); ?>
		<div class="tpg-post-thumbnail">
			<?php echo get_the_post_thumbnail( $this->last_post_id, $data['thumbnail_size'] ); ?>
			<?php if ( $data['show_caption'] && $tpg_thumb_caption = get_the_post_thumbnail_caption( $this->last_post_id ) ) : ?>
				<div class="tpg-thumb-caption">
					<?php if ( $data['show_caption_label'] && $data['caption_label'] ) : ?>
						<span class="caption-label"><?php echo esc_html( apply_filters( 'tpg-thumbnail-caption', $data['caption_label'] ) ); ?></span>
					<?php endif; ?>
					<?php Fns::print_html( $tpg_thumb_caption ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

}
