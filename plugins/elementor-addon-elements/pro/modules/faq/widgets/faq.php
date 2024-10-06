<?php
namespace WTS_EAE\Pro\Modules\FAQ\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use Elementor\Plugin as EPlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FAQ extends EAE_Widget_Base {

	public $faq_question_icon = 'faq_question_icon';
	public $faq_nav_icon = 'faq_nav_icon';
	public $faq_nav_active_icon = 'active_faq_nav_icon';

	public function get_name() {
		return 'eae-faq';
	}

	public function get_title() {
		return __( 'FAQ', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-faq';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

	public function get_keywords() {
		return [];
	}

	protected function register_controls() {
		$this->register_content_controls();
	}
		
	protected function register_content_controls() {

		$this->start_controls_section(
			'section_faqs',
			[
				'label'  => esc_html__( 'FAQs', 'wts-eae' ),
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'faqs_content_tabs' );

			$repeater->start_controls_tab(
				'faqs_content_tab',
				[
					'label' => __( 'Content', 'wts-eae' ),
				]
			);

				$repeater->add_control(
					'faq_question',
					[
						'label'                 => __( 'Question', 'wts-eae' ),
						'type'                  => Controls_Manager::TEXT,
						'dynamic'               => [
							'active'   => true,
						],
					]
				);

				$repeater->add_control(
					'faq_content_type',
					[
						'label'					=> __( 'Content type', 'wts-eae' ),
						'type'					=> Controls_Manager::SELECT,
						'options'				=> [
							'content'			=> __( 'Content', 'wts-eae' ),
							'saved_section'			=> __( 'Saved Section', 'wts-eae' ),
							'saved_container'			=> __( 'Saved Container', 'wts-eae' ),
							'ae_template'		=> __( 'AE Template', 'wts-eae' ),
						],
						'default'				=> 'content',
					]
				);

				$saved_sections[''] = __( 'Select Section', 'wts-eae' );
				$saved_sections     = $saved_sections + Helper::select_elementor_page( 'section' );
				$repeater->add_control(
					'saved_section',
					[
						'label'     => __( 'Sections', 'wts-eae' ),
						'type'      => Controls_Manager::SELECT,
						'options'   => $saved_sections,
						'condition' => [
							'faq_content_type' => 'saved_section',
						],
					]
				);
				$saved_page[''] = __( 'Select Container', 'wts-eae' );
				$saved_page     = $saved_page + Helper::select_elementor_page( 'container' );
				$repeater->add_control(
					'saved_container',
					[
						'label'     => __( 'Container', 'wts-eae' ),
						'type'      => Controls_Manager::SELECT,
						'options'   => $saved_page,
						'condition' => [
							'faq_content_type' => 'saved_container',
						],
					]
				);

				$saved_ae_template[''] = __( 'Select AE Template', 'wts-eae' );
				$saved_ae_template     = $saved_ae_template + Helper::select_ae_templates();
				$repeater->add_control(
					'ae_template',
					[
						'label'     => __( 'AE Templates', 'wts-eae' ),
						'type'      => Controls_Manager::SELECT,
						'options'   => $saved_ae_template,
						'condition' => [
							'faq_content_type' => 'ae_template',
						],
					]
				);

				$repeater->add_control(
					'faq_answer',
					[
						'label'                 => esc_html__( 'Answer', 'wts-eae' ),
						'type'                  => Controls_Manager::WYSIWYG,
						'default'               => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'wts-eae' ),
						'dynamic'               => [ 'active' => true ],
						'condition' => [
							'faq_content_type' => 'content',
						],
					]
				);

				$repeater->add_control(
					'faq_default_active',
					[
						'label'                 => esc_html__( 'Active as Default', 'wts-eae' ),
						'type'                  => Controls_Manager::SWITCHER,
						'default'               => 'no',
						'return_value'          => 'yes',
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'faqs_icon_tab',
				[
					'label' => __( 'Icon', 'wts-eae' ),
				]
			);

			Helper::eae_media_controls(
				$repeater,
				[
					'name'          => $this->faq_question_icon,
					'label'         => __( 'Icon', 'wts-eae' ),
					'icon'			=> true,
					'image'			=> true,
					'lottie'		=> true,
				]
			);

			$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'faqs',
			[
				'label'                 => esc_html__( 'Add FAQs', 'wts-eae' ),
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[ 'faq_question' => esc_html__( 'FAQ Question 1', 'wts-eae' ) ],
					[ 'faq_question' => esc_html__( 'FAQ Question 2', 'wts-eae' ) ],
					[ 'faq_question' => esc_html__( 'FAQ Question 3', 'wts-eae' ) ],
				],
				'fields'                => $repeater->get_controls(),
				'title_field'           => '{{faq_question}}',
			]
		);

		$this->add_control(
			'faq_layout',
			[
				'label'			=> __( 'Layout', 'wts-eae' ),
				'type'			=> Controls_Manager::SELECT,
				'default'		=> 'list',
				'options' 		=> [
					'list'		=> __( 'List', 'wts-eae' ),
					'grid' 		=> __( 'Grid', 'wts-eae' ),
					'accordion'	=> __( 'Accordion', 'wts-eae' ),

				]
			]
		);

		$this->add_control(
			'faq_default_first_open',
			[
				'label'                 => esc_html__( 'First FAQ Default Open', 'wts-eae' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'return_value'          => 'yes',
				'condition' => [
					'faq_layout' => 'accordion',
				],
			]
		);

		$this->add_responsive_control(
			'faq_columns',
			[
				'label'			=> __( 'Columns', 'wts-eae' ),
				'type'			=> Controls_Manager::NUMBER,
				'default' => '2',
				'min'             => 1,
				'max'             => 12,
				'selectors'		=> [
					'{{WRAPPER}} .eae-faq-wrapper' => 'display: grid; grid-template-columns: repeat({{VALUE}}, 1fr);'
				],
				'condition' => [
					'faq_layout' => 'grid',
				],
			]
		);

		$this->add_responsive_control(
			'faq_item_column_gap',
			[
				'label'     => __( 'Column Gap', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-faq-wrapper' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'faq_layout' => 'grid',
				],
			]
		);

		$this->add_responsive_control(
			'faq_item_row_gap',
			[
				'label'     => __( 'Row Gap', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-faq-wrapper' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'faq_layout' => ['list', 'grid', 'accordion'],
				],
			]
		);

		$this->add_control(
			'faq_question_html_tag',
			[
				'label' => esc_html__( 'Question HTML Tag', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default' => 'div',
			]
		);

		$this->add_control(
			'faqs_default_icon_heading',
			[
				'label'     => __( 'Question Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		Helper::eae_media_controls(
			$this,
			[
				'name'          => $this->faq_question_icon,
				'label'         => __( 'Question Icon', 'wts-eae' ),
				'icon'			=> true,
				'image'			=> true,
				'lottie'		=> true,
				'defaults'      => [
                    'graphic_type_default' => 'icon',
                    'graphic_icon_default' => [
                        'value' => 'fas fa-star',
                        'library' => 'fa-solid'
                    ],
                ]
			]
		);

		$this->add_control(
			'faq_question_icon_position',
			[
				'label'			=> __( 'Icon Position', 'wts-eae' ),
				'type'			=> Controls_Manager::SELECT,
				'options'		=> [
					'before'	=> __( 'Before', 'wts-eae' ),
					'after'		=> __( 'After', 'wts-eae' ),
				],
				'default'		=> 'before',
				'selectors_dictionary' => [
					'before' 	=> 'unset',
					'after' => '2',
				],
				'selectors'		=> [
					'{{WRAPPER}} .eae-faq-question .eae-faq-question-icon' => 'order: {{VALUE}}'
				],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'faq_schema',
			[
				'label' => esc_html__( 'FAQ Schema', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->get_accordion_controls();

		$this->get_icon_controls();

		$this->get_faq_item_style_controls();
		
		$this->get_faq_question_style_controls();

		$this->get_faq_answer_style_controls();

	}

	public function get_faq_item_style_controls(){
		$this->start_controls_section(
			'faq_item_style_section',
			[
				'label'     => __( 'Item', 'wts-eae' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'faq_item_bg_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-faq-item-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'faq_item_border',
				'label'          => __( 'Border', 'wts-eae' ),
				'default'        => '1px',
				'selector'       => '{{WRAPPER}} .eae-faq-item-wrapper',
			]
		);

		$this->add_responsive_control(
			'faq_item_border_radius',
			[
				'label'     => __( 'Border Radius', 'wts-eae' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .eae-faq-item-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'faq_item_shadow',
				'selector' => '{{WRAPPER}} .eae-faq-item-wrapper',
			]
		);

		$this->add_responsive_control(
			'faq_item_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-faq-item-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'faq_item_margin',
			[
				'label'      => __( 'Margin', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-faq-item-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_faq_question_style_controls(){
		$this->start_controls_section(
			'faq_question_style_section',
			[
				'label'     => __( 'Question', 'wts-eae' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'faq_question_align',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-faq-question-bar' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'faq_question_typography',
				'selector' => '{{WRAPPER}} .eae-faq-question .eae-faq-question-title',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'faq_title_text_stroke',
				'selector' => '{{WRAPPER}} .eae-faq-question-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'faq_title_text_shadow',
				'selector' => '{{WRAPPER}} .eae-faq-question-title',
			]
		);

		$this->add_control(
			'faq_title_blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Normal', 'wts-eae' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-faq-question-title' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->start_controls_tabs( 'faqs_question_style_tabs' );

			$this->start_controls_tab(
				'faq_question_normal_tab',
				[
					'label' => __( 'Closed', 'wts-eae' ),
					'condition' => [
						'faq_layout' => 'accordion',
					]
				]
			);

				$this->add_control(
					'faq_question_color',
					[
						'label'     => __( 'Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .eae-faq-question .eae-faq-question-bar .eae-faq-question-title' => 'color: {{VALUE}};',
						],
						'global'    => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'faq_question_bg_color',
						'types' => [ 'classic', 'gradient' ],
						'exclude' => [ 'image' ],
						'selector' => '{{WRAPPER}} .eae-faq-question',
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'           => 'faq_question_border',
						'label'          => __( 'Border', 'wts-eae' ),
						'default'        => '1px',
						'selector'       => '{{WRAPPER}} .eae-faq-question',
						'fields_options' => [
							'color' => [
								'default' => '#D5D8DC'
							],
							'border' => [
								'default' => 'solid',
							],
							/* 'width'  => [
								'default' => [
									'top'    => 1,
									'right'  => 1,
									'bottom' => 1,
									'left'   => 1,
								],
							], */
						],
					]
				);
			
			$this->end_controls_tab();

			$this->start_controls_tab(
				'faq_question_active_tab',
				[
					'label' => __( 'Opened', 'wts-eae' ),
					'condition' => [
						'faq_layout' => 'accordion',
					]
				]
			);

				$this->add_control(
					'faq_question_active_color',
					[
						'label'     => __( 'Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .eae-faq-question.eae-faq-active  .eae-faq-question-bar a' => 'color: {{VALUE}};',
						],
						'global'    => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name' => 'faq_question_bg_active_color',
						'types' => [ 'classic', 'gradient' ],
						'exclude' => [ 'image' ],
						'selector' => '{{WRAPPER}} .eae-faq-question.eae-faq-active',
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'           => 'faq_question_active_border',
						'label'          => __( 'Border', 'wts-eae' ),
						'default'        => '1px',
						'selector'       => '{{WRAPPER}} .eae-faq-question.eae-faq-active',
						/* 'fields_options' => [
							'border' => [
								'default' => 'solid',
							],
							'width'  => [
								'default' => [
									'top'    => 1,
									'right'  => 1,
									'bottom' => 1,
									'left'   => 1,
								],
							],
						], */
					]
				);
			
			$this->end_controls_tab();
		
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'faq_question_border_radius',
			[
				'label'     => __( 'Border Radius', 'wts-eae' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .eae-faq-question' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'faq_question_shadow',
				'selector' => '{{WRAPPER}} .eae-faq-question',
			]
		);

		$this->add_responsive_control(
			'faq_question_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top'    => 15,
					'right'  => 20,
					'bottom' => 15,
					'left'   => 20,
					'unit'	 => 'px'
				],
				'selectors'  => [
					'{{WRAPPER}} .eae-faq-question' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'faq_question_margin',
			[
				'label'      => __( 'Margin', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-faq-question' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'faq_question_icon_style_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		Helper::global_icon_style_controls(
            $this,
            [
                'name'          => $this->faq_question_icon,
                'selector'      => '.eae-faq-question-icon',
                'is_repeater'   => 'false',
				'is_active_tab' => [
					'label'		=> 'Opened',
					'selector'  => '.eae-faq-active'
				]
            ]
        );

		$this->add_control(
			'faq_ques_icon_style_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note: Padding, Border Width and Border Radius only works when Stack or Framed is selected as Icon View', 'wts-eae' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->start_injection( [
			'of' => 'faq_question_icon_rotate',
		] );
		
		$this->add_responsive_control(
			'faq_question_icon_space',
			array(
				'label'     => __( 'Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .eae-faq-question .eae-faq-question-icon-before .eae-faq-question-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eae-faq-question .eae-faq-question-icon-after .eae-faq-question-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_injection();

		$this->end_controls_section();
	}

	public function get_faq_answer_style_controls(){
		$this->start_controls_section(
			'faq_answer_style_section',
			[
				'label'		=> __( 'Answer', 'wts-eae'),
				'tab'		=> Controls_Manager::TAB_STYLE
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'faq_answer_typography',
				'selector' => '{{WRAPPER}} .eae-faq-answer',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'faq_answer_text_shadow',
				'selector' => '{{WRAPPER}} .eae-faq-answer',
			]
		);

		$this->add_control(
			'faq_answer_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-faq-answer' => 'color: {{VALUE}};',
				],
				'global'    => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
			]
		);

		$this->add_control(
			'faq_answer_background_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-faq-answer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'faq_answer_border',
				'label'          => __( 'Border', 'wts-eae' ),
				'selector'       => '{{WRAPPER}} .eae-faq-answer',
			]
		);

		$this->end_controls_tabs();


		$this->add_responsive_control(
			'faq_answer_border_radius',
			[
				'label'     => __( 'Border Radius', 'wts-eae' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .eae-faq-answer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'faq_answer_shadow',
				'selector' => '{{WRAPPER}} .eae-faq-answer',
			]
		);

		$this->add_responsive_control(
			'faq_answer_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top'    => 15,
					'right'  => 20,
					'bottom' => 15,
					'left'   => 20,
					'unit'	 => 'px'
				],
				'selectors'  => [
					'{{WRAPPER}} .eae-faq-answer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'faq_answer_margin',
			[
				'label'      => __( 'Margin', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-faq-answer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->get_icon_style_controls();
	}

	public function get_accordion_controls(){
		$this->start_controls_section(
			'faq_accordion_section',
			[
				'label'     => __( 'Accordion', 'wts-eae' ),
				'condition' => [
					'faq_layout' => 'accordion'
				]
			]
		);

		$this->add_control(
			'faq_accordion_toggle',
			[
				'label' => esc_html__( 'Toggle', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'faq_trigger_action',
			[
				'label'		=> esc_html__( 'Trigger Action', 'wts-eae' ),
				'type' 		=> Controls_Manager::SELECT,
				'options'	=> [
					'click' => __( 'Click', 'wts-eae' ),
					'mouseenter' => __( 'Hover', 'wts-wea' ),
				],
				'default' 	=> 'click',
			]
		);

		$this->add_control(
			'faq_accordion_transition_speed',
			[
				'label'			=> __( 'Transition Speed', 'wts-eae' ),
				'type'			=> Controls_Manager::SELECT,
				'default'		=> 'normal',
				'options' 		=> [
					'slow'		=> __( 'Slow', 'wts-eae' ),
					'normal' 		=> __( 'Normal', 'wts-eae' ),
					'fast'	=> __( 'Fast', 'wts-eae' ),

				],
			]
		);

		$this->end_controls_section();
	}

	public function get_icon_controls(){
		$this->start_controls_section(
			'section_faq_nav',
			[
				'label' => esc_html__( 'Navigation', 'wts-eae' ),
				'condition' => [
					'faq_layout' => 'accordion',
				]
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'        => __( 'Alignment', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'label_block'  => false,
				'options'      => array(
					'left'  => array(
						'title' => __( 'Start', 'wts-eae' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'End', 'wts-eae' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'      => is_rtl() ? 'left' : 'right',
			]
		);

		$this->add_control(
			'navigation_icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		Helper::eae_media_controls(
			$this,
			[
				'name'          => $this->faq_nav_icon,
				'label'         => __( 'Icon', 'wts-eae' ),
				'icon'			=> true,
				'image'			=> false,
				'lottie'		=> false,
				'defaults'      => [
                    'graphic_type_default' => 'icon',
                    'graphic_icon_default' => [
                        'value' => 'fas fa-angle-down',
                        'library' => 'fa-solid'
                    ],
                 ]
			]
		);

		$this->add_control(
			'navigation_active_icon_heading',
			[
				'label'     => __( 'Active Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		Helper::eae_media_controls(
			$this,
			[
				'name'          => $this->faq_nav_active_icon,
				'label'         => __( 'Active Icon', 'wts-eae' ),
				'icon'			=> true,
				'image'			=> false,
				'lottie'		=> false,
				'defaults'      => [
                    'graphic_type_default' => 'icon',
                    'graphic_icon_default' => [
                        'value' => 'fas fa-angle-up',
                        'library' => 'fa-solid'
                    ],
                 ]
			]
		);

		$this->end_controls_section();
	}

	public function get_icon_style_controls() {

		$this->start_controls_section(
			'section_faq_nav_style',
			[
				'label' => esc_html__( 'Navigation', 'wts-eae' ),
				'tab'		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'faq_layout' => 'accordion',
				]
			]
		);

		$this->add_control(
			'navigation_icon_style_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		Helper::global_icon_style_controls(
            $this,
            [
                'name'          => $this->faq_nav_icon,
                'selector'      => '.eae-faq-nav-icon',
                'is_repeater'   => 'false', 
				'is_active_tab' => [
					'label'		=> 'Opened',
					'selector'  => '.eae-faq-active'
				]
            ]
        );

		$this->add_control(
			'faq_nav_style_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note: Padding, Border Width and Border Radius only works when Stack or Framed is selected as Icon View', 'wts-eae' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->start_injection( [
			'of' => 'faq_nav_icon_rotate',
		] );
		
		$this->add_responsive_control(
			'faq_icon_space',
			array(
				'label'     => __( 'Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .eae-faq-question.eae-faq-icon-align-left .eae-faq-nav-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eae-faq-question.eae-faq-icon-align-right .eae-faq-nav-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_injection();

		$this->end_controls_section();
	}

	public function render(){

		$settings = $this->get_settings_for_display();

		$faqs = $settings['faqs'];

		$data_settings = ['faq_layout' => $settings['faq_layout']];

		$faq_schema = [];

		$this->add_render_attribute( 'faq_wrapper', 'class', 'eae-faq-wrapper eae-faq-layout-' . $settings['faq_layout'] );

		if($settings['faq_layout'] === 'accordion'){
			$this->add_render_attribute( 'faq_wrapper', 'class', ['elementor-accordion', 'eae-faq-wrapper'] );
			$data_settings['faq_trigger_action'] = $settings['faq_trigger_action'];
			$data_settings['faq_accordion_transition_speed'] = $settings['faq_accordion_transition_speed'];
			$data_settings['faq_accordion_toggle'] = $settings['faq_accordion_toggle'];
		}

		$this->add_render_attribute( 'faq_wrapper', 'data-settings', json_encode( $data_settings ) );

		echo '<div ' . $this->get_render_attribute_string('faq_wrapper') .'>';

		foreach ( $faqs as $index => $item ):
			$tab_count = $index + 1;
			$faq_nav_icon_classes = [];
			$this->set_render_attribute( 'faq_item_wrapper', 'class', 'eae-faq-item-wrapper elementor-repeater-item-' . $item['_id'] );
			$this->set_render_attribute( 'faq_item_wrapper', 'role', 'tablist');
			$this->set_render_attribute( 'faq_question', 'class', 'eae-faq-question ' . 'eae-faq-icon-align-' . $settings['icon_align'] );
			$this->set_render_attribute( 'faq_question', 'role', 'tab' );
			$this->set_render_attribute( 'faq_answer', 'class', 'eae-faq-answer' );
			$this->set_render_attribute( 'faq_answer', 'role', 'tabpanel' );

			if($settings['faq_layout'] === 'accordion'){

				$id_int = substr( $this->get_id_int(), 0, 3 );

				if(($tab_count == 1 && $settings['faq_default_first_open'] === 'yes') || $item['faq_default_active'] === 'yes'){
					$this->set_render_attribute( 'faq_question', 'aria-expanded', 'true' );
					$this->set_render_attribute( 'faq_question', 'class', 'eae-faq-question eae-faq-active ' . 'eae-faq-icon-align-' . $settings['icon_align'] );
					$this->set_render_attribute('faq_answer', 'style', 'display:block');
				}else{
					$this->set_render_attribute( 'faq_question', 'aria-expanded', 'false' );
					$this->set_render_attribute('faq_answer', 'style', 'display:none');
				}

				$this->set_render_attribute( 'faq_question', [
					'id' => 'eae-faq-question-' . $id_int . $tab_count,
					'data-tab' => $tab_count,
					'aria-controls' => 'eae-faq-question-' . $id_int . $tab_count,
				] );

				$this->set_render_attribute( 'faq_answer', [
					'id' => 'eae-faq-answer-' . $id_int . $tab_count,
					'data-tab' => $tab_count,
					'aria-labelledby' => 'eae-faq-answer-' . $id_int . $tab_count,
				] );

			}

			echo '<div ' . $this->get_render_attribute_string('faq_item_wrapper') . '>';
				?>
				<<?php Utils::print_validated_html_tag( $settings['faq_question_html_tag'] ); ?> <?php echo $this->get_render_attribute_string('faq_question'); ?>>
				<?php
					if( $settings['faq_nav_icon_graphic_type'] !== 'none' && $settings['faq_layout'] === 'accordion' ) {

						$faq_nav_icon_classes[] = 'eae-faq-icon-wrap';
						
						if(($tab_count == 1 && $settings['faq_default_first_open'] === 'yes') || $item['faq_default_active'] === 'yes'){
							$faq_nav_icon_classes[] = 'open';
						}else{
							$faq_nav_icon_classes[] = 'close';
						}
						$this->set_render_attribute( 'faq_question_icon', 'class', $faq_nav_icon_classes );
						echo '<div ' . $this->get_render_attribute_string( 'faq_question_icon') . '>';
							Helper::render_icon_html($settings, $this, $this->faq_nav_icon, 'eae-faq-nav-icon eae-faq-icon-inactive');
							Helper::render_icon_html($settings, $this, $this->faq_nav_active_icon, 'eae-faq-nav-icon eae-faq-icon-active');
						echo '</div>';
					}
					
					$this->set_render_attribute( 'faq_question_bar','class', 'eae-faq-question-bar eae-faq-question-icon-' . $settings['faq_question_icon_position']);
					echo '<div ' . $this->get_render_attribute_string('faq_question_bar') . '>';
						if(  $settings['faq_question_icon_graphic_type'] !== 'none'){
							if( $item['faq_question_icon_graphic_type'] !== 'none' ) {
								Helper::render_icon_html($item, $this, $this->faq_question_icon, 'eae-faq-question-icon');
							}else{
								Helper::render_icon_html($settings, $this, $this->faq_question_icon, 'eae-faq-question-icon');
							}
						}else{
							if( $item['faq_question_icon_graphic_type'] !== 'none' ) {
								Helper::render_icon_html($item, $this, $this->faq_question_icon, 'eae-faq-question-icon');
							}
						}
						$this->set_render_attribute( 'faq_question_wrap', 'class', 'eae-faq-question-title');
						$question_wrap_element = 'div';
						if($settings['faq_layout'] === 'accordion'){
							$question_wrap_element = 'a';
						}
						echo '<'. $question_wrap_element . ' class="eae-faq-question-title">';
							echo Helper::eae_wp_kses($item['faq_question']);
						echo '</' . $question_wrap_element . '>';
					echo '</div>';
				?>
				</<?php Utils::print_validated_html_tag( $settings['faq_question_html_tag'] ); ?>>
				<?php
				echo '<div ' . $this->get_render_attribute_string('faq_answer') . '>';
					
					switch ($item['faq_content_type']) {
						case 'saved_container':
							$answer = EPlugin::instance()->frontend->get_builder_content_for_display( $item['saved_container'] );
							break;
						case 'saved_section':
							$answer = EPlugin::instance()->frontend->get_builder_content_for_display( $item['saved_section'] );
							break;
						case 'ae_template':
							$answer = EPlugin::instance()->frontend->get_builder_content_for_display( $item['ae_template'] );
							break;
						default:
							$answer = do_shortcode( $item['faq_answer'] );
					}
					echo $answer;
				echo '</div>';

			echo '</div>';

			if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
				$faq_schema[] = [
					'@type' => 'Question',
					'name' => wp_strip_all_tags( $item['faq_question'] ),
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text' => $this->parse_text_editor( $answer ),
					],
				];
			}
		endforeach;
		
		echo '</div>';
		?>	
		<?php
			if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
				$json = [
					'@context' => 'https://schema.org',
					'@type' => 'FAQPage',
					'mainEntity' => $faq_schema,
				];
				?>
				<script type="application/ld+json"><?php echo wp_json_encode( $json ); ?></script>
		<?php }
    }
}
