<?php
/**
 * Elementor: Post Meta Widget.
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude

use Elementor\Controls_Manager;
use RT\ThePostGrid\Helpers\Fns;

/**
 * Elementor: Post Meta Widget.
 */
class TPGPostMeta extends Custom_Widget_Base {

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
		$this->tpg_name     = esc_html__( 'TPG - Post Meta', 'the-post-grid-pro' );
		$this->tpg_base     = 'tpg-post-meta';
		$this->tpg_icon     = 'eicon-post-info tpg-grid-icon'; // .tpg-grid-icon class for just style
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
				'label' => esc_html__( 'TPG Post Meta', 'the-post-grid-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'meta_visibility_opt',
			[
				'label'   => esc_html__( 'Meta Visibility Options', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'        => esc_html__( 'Post Date', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'        => esc_html__( 'Post Categories', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'        => esc_html__( 'Post Author', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
			]
		);
		$this->add_control(
			'show_author_image',
			[
				'label'        => esc_html__( 'Author Image / Icon', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Image', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Icon', 'the-post-grid-pro' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
				'condition'    => [
					'show_author' => 'show',
				],
			]
		);

		$this->add_control(
			'show_tags',
			[
				'label'        => esc_html__( 'Post Tags', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'show',
				'default'      => 'show',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'show_comment_count',
			[
				'label'        => esc_html__( 'Post Comment Count', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'show',
				'default'      => 'show',
			]
		);

		$this->add_control(
			'show_post_count',
			[
				'label'        => esc_html__( 'Post View Count', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'show',
				'default'      => 'show',
			]
		);

		$this->add_control(
			'show_meta_icon',
			[
				'label'        => esc_html__( 'Show Meta Icon', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'the-post-grid-pro' ),
				'label_off'    => esc_html__( 'Hide', 'the-post-grid-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'additional_options',
			[
				'label'   => esc_html__( 'Additional Options', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$this->add_control(
			'meta_separator',
			[
				'label'   => esc_html__( 'Meta Separator', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default - None', 'the-post-grid-pro' ),
					'.'       => esc_html__( 'Dot ( . )', 'the-post-grid-pro' ),
					'/'       => esc_html__( 'Single Slash ( / )', 'the-post-grid-pro' ),
					'//'      => esc_html__( 'Double Slash ( // )', 'the-post-grid-pro' ),
					'-'       => esc_html__( 'Hyphen ( - )', 'the-post-grid-pro' ),
					'|'       => esc_html__( 'Vertical Pipe ( | )', 'the-post-grid-pro' ),
				],
			]
		);

		$this->add_control(
			'author_prefix',
			[
				'label'       => esc_html__( 'Author Prefix', 'the-post-grid-pro' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'By', 'the-post-grid-pro' ),
				'condition'   => [
					'show_author!' => '',
				],
			]
		);

		/**
		 * Start Popover
		 * =============================================
		 */

		$this->add_control(
			'meta_popover_toggle',
			[
				'label'        => esc_html__( 'Change Meta Icon', 'the-post-grid-pro' ),
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'the-post-grid-pro' ),
				'label_on'     => esc_html__( 'Custom', 'the-post-grid-pro' ),
				'return_value' => 'yes',
				'condition'    => [
					'show_meta_icon' => 'yes',
				],
			]
		);

		$this->start_popover();

		$this->add_control(
			'user_icon',
			[
				'label'     => esc_html__( 'Author Icon', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-user',
					'library' => 'solid',
				],
				'condition' => [
					'show_author_image!' => 'show',
				],
			]
		);

		$this->add_control(
			'cat_icon',
			[
				'label'     => esc_html__( 'Category Icon', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-folder-open',
					'library' => 'solid',
				],
				'condition' => [
					'show_category' => 'show',
				],
			]
		);

		$this->add_control(
			'date_icon',
			[
				'label'     => esc_html__( 'Date Icon', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'far fa-calendar-alt',
					'library' => 'solid',
				],
				'condition' => [
					'show_date' => 'show',
				],
			]
		);

		$this->add_control(
			'tag_icon',
			[
				'label'     => esc_html__( 'Tags Icon', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fa fa-tags',
					'library' => 'solid',
				],
				'condition' => [
					'show_tags' => 'show',
				],
			]
		);

		$this->add_control(
			'comment_icon',
			[
				'label'     => esc_html__( 'Comment Icon', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-comments',
					'library' => 'solid',
				],
				'condition' => [
					'show_comment_count' => 'show',
				],
			]
		);

		$this->add_control(
			'post_count_icon',
			[
				'label'     => esc_html__( 'Post Count Icon', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-eye',
					'library' => 'solid',
				],
				'condition' => [
					'show_post_count' => 'show',
				],
			]
		);

		$this->end_popover(); // End Popover =============================================

		$this->add_control(
			'meta_spacing',
			[
				'label'              => esc_html__( 'Meta Spacing', 'the-post-grid-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .post-meta-tags' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'style_options_heading',
			[
				'label'   => esc_html__( 'Meta Style', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',

			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__( 'Meta Typography', 'the-post-grid-pro' ),
				'exclude'  => [ 'text_decoration', 'word_spacing' ],
				'selector' => '{{WRAPPER}} .tpg-single-post-meta.post-meta-tags',
			]
		);

		$this->add_control(
			'author_img_dimension',
			[
				'label'      => esc_html__( 'Author Image Size', 'the-post-grid-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 20,
						'max'  => 80,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-single-post-meta.post-meta-tags span img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'between_meta_space',
			[
				'label'      => esc_html__( 'Space between each meta', 'the-post-grid-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tpg-single-post-meta.post-meta-tags > span' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		/**
		 * Meta Color Options
		 * ==================================================
		 */
		$this->add_control(
			'meta_color_heading',
			[
				'label'     => esc_html__( 'Meta Color Options', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__( 'Meta Color', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-single-post-meta' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'meta_icon_color',
			[
				'label'     => esc_html__( 'Meta Icon Color', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-single-post-meta i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'meta_line_color',
			[
				'label'     => esc_html__( 'Meta Link Color', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-single-post-meta a, {{WRAPPER}} .tpg-single-post-meta a + .rt-separator' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'meta_line_color_hover',
			[
				'label'     => esc_html__( 'Meta Link Color - Hover', 'the-post-grid-pro' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tpg-single-post-meta a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'meta_ordering_heading',
			[
				'label'   => esc_html__( 'Meta Ordering', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'repeater_hidden',
			[
				'type' => \Elementor\Controls_Manager::HIDDEN,
			]
		);

		$this->add_control(
			'meta_ordering',
			[
				'label'       => esc_html__( 'Meta Ordering (Drag and Drop)', 'the-post-grid-pro' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'meta_title' => esc_html__( 'Author', 'the-post-grid-pro' ),
						'meta_name'  => 'author',
					],
					[
						'meta_title' => esc_html__( 'Date', 'the-post-grid-pro' ),
						'meta_name'  => 'date',
					],
					[
						'meta_title' => esc_html__( 'Category', 'the-post-grid-pro' ),
						'meta_name'  => 'category',
					],
					[
						'meta_title' => esc_html__( 'Tags', 'the-post-grid-pro' ),
						'meta_name'  => 'tags',
					],
					[
						'meta_title' => esc_html__( 'Comment Count', 'the-post-grid-pro' ),
						'meta_name'  => 'comment_count',
					],
					[
						'meta_title' => esc_html__( 'Post Count', 'the-post-grid-pro' ),
						'meta_name'  => 'post_count',
					],
					[
						'meta_title' => esc_html__( 'Extra Field', 'the-post-grid-pro' ),
						'meta_name'  => 'post_like',
					],
				],
				'classes'     => 'tpg-item-order-repeater',
				'title_field' => '{{{ meta_title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$data                      = $this->get_settings();
		$data['category_position'] = 'default';
		$data['category']          = 'category';
		$data['post_tag']          = 'post_tag';
		?>
		<div class="post-meta-tags tpg-single-post-meta">
			<?php Fns::get_post_meta_html( $this->last_post_id, $data ); ?>
		</div>
		<?php
	}

}
