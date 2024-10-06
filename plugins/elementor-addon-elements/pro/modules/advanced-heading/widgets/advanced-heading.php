<?php

namespace WTS_EAE\Pro\Modules\AdvancedHeading\Widgets;

use Elementor\Controls_Manager;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;






use function GuzzleHttp\Promise\queue;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AdvancedHeading extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-advanced-heading';
	}

	public function get_title() {
		return __( 'Advanced Heading', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-advanced-heading';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'heading', 'multi-heading', 'link', 'advanced'];
	}

	
	public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

	protected function register_controls() {
        $this->start_controls_section(
			'eae_section_title',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);
		
       $this->add_control(
			'eae_heading_title',
			[
				'label'			 => esc_html__( 'Heading', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'description'	 => esc_html__( 'To Hightlight Specific Word Place them it between %%_%%', 'wts-eae' ),
				'label_block'	 => true,
				'placeholder'	 => esc_html__( 'I am Advance %%Heading%%', 'wta-eae' ),
				'default'		 => esc_html__( 'I am Advance %%Heading%%', 'wta-eae' ),

			]
		);
		$this->add_control(
			 'eae_heading_link', 
			 [
			'label'			 => esc_html__( 'Link', 'wta-eae' ),
			'type'			 => Controls_Manager::URL,
			'dynamic'		 => [
				'active' => true,
			],
			'label_block' => true,
			'placeholder' => esc_html__( 'Paste URL or type', 'wta-eae' ),
			'autocomplete' => false,
			'options' => [ 'is_external', 'nofollow', 'custom_attributes' ],
        ]);
		$this->add_control(
			'eae_heading_title_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);
		
        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'eae_heading_title_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
            ]
        );
		$this->add_control(
			'eae_heading_title_icon_position',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'row' => 'Before',
					'row-reverse' => 'After',
				],
				'selectors' => [
					'{{WRAPPER}} .wts-eae-title-wrapper' => 'flex-direction: {{VALUE}}',						
				],
				'default' => 'row',
			]
		);
		
		$this->add_control(
			'eae_Subtitle_heading',
			[
				'label'     => __( 'Sub Heading', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'eae_heading_sub_title_show',
			[
				'label' => esc_html__( 'Enable', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->add_control(
			'eae_heading_sub_title', [
				'label'			 =>esc_html__( 'Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'label_block'	 => true,
				'placeholder'	 =>esc_html__( 'I am Sub Heading', 'wts-eae' ),
				'default'		 =>esc_html__( 'I am Sub Heading', 'wts-eae' ),
				'condition' => [
					'eae_heading_sub_title_show' => 'yes'
				],
			]
		);
		
		$this->add_control(
			'eae_heading_sub_title_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
				'condition' => [
					'eae_heading_sub_title_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'eae_shadow_text_heading',
			[
				'label'     => __( 'Shadow Text', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_control( 
			'eae_show_shadow_text',
			[
				'label'        => __( 'Enable', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
				'default'      =>'yes'
			]
		);
		$this->add_control( 
			'eae_shadow_text_content', 
			[
				'label'			 => esc_html__( 'Text', 'wts-eae' ),
				'label_block'	 => true,
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'default'		 => esc_html__( 'Shadow Text', 'wts-eae' ),
				'condition' => [
					'eae_show_shadow_text!' => ''
				],

			]
		);
		$this->add_control(
			'eae_icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		Helper::eae_media_controls(
            $this,
            [
                'name'          => 'eae_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
            ]
        );
		$this->add_responsive_control(
			'eae_title_icon_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-icon-wrapper' => 'justify-content: {{VALUE}}',										
				],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
			'eae_section_separator',
			[
				'label' => __( 'Separator', 'wts-eae' ),
			]
		);
		$this->add_control(
			'eae_heading_enable_separator', 
			[
				'label'			 =>esc_html__( 'Show Separator', 'wts-eae' ),
				'type'			 => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' =>esc_html__( 'Yes', 'wts-eae' ),
				'label_off' =>esc_html__( 'No', 'wts-eae' ),
			]
		);

		$this->add_control(
			'eae_heading_separator_style_border',
			[
				'label'     => __( 'Type', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'solid'  => __( 'Solid', 'wts-eae' ),
					'double' => __( 'Double', 'wts-eae' ),
					'dotted' => __( 'Dotted', 'wts-eae' ),
					'dashed' => __( 'Dashed', 'wts-eae' ),
					'groove' => __( 'Groove', 'wts-eae' ),
					'ridge' => __( 'Groove', 'wts-eae' ),
					
				],
				'default'   => 'solid',
				'selectors' => [
					'{{WRAPPER}} .wts-eae-separator-wrapper .wts-eae-separator' => 'border-top-style: {{VALUE}};',						
					'{{WRAPPER}} .wts-eae-separator-without' => 'border-top-style: {{VALUE}};',
				],
				'condition' => [
					'eae_heading_enable_separator' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'eae_heading_separator_style',
			[
				'label' => esc_html__( 'Graphic Type', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => 'None', 
					'text' =>  'Text',
					'image' =>  'Image', 
					'icon' => 'Icon',
				],
				'default' => 'none',
				'condition' => [
					'eae_heading_enable_separator' => 'yes',
				],
			]
		);
		$this->add_control(
			'eae_heading_separator_with_text',
			[
				'label'			 => esc_html__( 'Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'label_block'	 => false,
				'condition' => [
					'eae_heading_separator_style' => 'text',
					'eae_heading_enable_separator' => 'yes',
				],
			]
		);
		$this->add_control(
			'eae_heading_separator_with_image',
			[
				'label' => esc_html__( 'Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'eae_heading_separator_style' => 'image',
					'eae_heading_enable_separator' => 'yes',
				],
			]
		);
	
		Helper::eae_media_controls(
            $this,
            [
                'name'          => 'eae_heading_separator_with_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> true,
				'conditions'     => [
					[
						'key'   => 'eae_heading_separator_style',
						'value' => 'icon',
					],
					[
						'key'   => 'eae_heading_enable_separator',
						'value' => 'yes',
					],
					
				]
            ]
        );


		$this->add_control(
				'eae_separator_wrapper_alignment',
				[
					'label' => esc_html__( 'Alignment Text', 'wts-eae' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => esc_html__( 'Left', 'wts-eae' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => esc_html__( 'Center', 'wts-eae' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => esc_html__( 'Right', 'wts-eae' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'prefix_class' => 'wts-eae-separator-pos-',
					'condition'=>[
						'eae_heading_separator_style!'=>'none',
						'eae_heading_enable_separator' => 'yes',
					]
				]
			);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'eae_section_order',
			[
				'label' => __( 'Order', 'wts-eae' ),
			]
		);
		$this->add_responsive_control(
			'eae_heading_order_title',
			[
				'label'			 => esc_html__( 'Heading ', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'default'		 =>'1',
				'selectors' => [
					'{{WRAPPER}} .wts-eae-title-wrapper' => 'order:{{VALUE}}'
				],
			]
		);
		$this->add_responsive_control(
			'eae_heading_order_sub_title',
			[
				'label'			 => esc_html__( 'Sub Heading ', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,				
				'label_block'	 => false,
				'default'		 =>'3',
				'selectors' => [
					'{{WRAPPER}} .wta-eae-sub-title-wrapper' => 'order:{{VALUE}}'
				],
			]
		);
		$this->add_responsive_control(
			'eae_heading_order_Separator',
			[
				'label'			 => esc_html__( 'Separator', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'default'		 =>'2',
				'selectors' => [
					'{{WRAPPER}} .wts-eae-separator-wrapper' => 'order:{{VALUE}};',
					'{{WRAPPER}} .wts-eae-separator-without' => 'order:{{VALUE}};'
				],
			]
		);
		$this->add_responsive_control(
			'eae_order_icon',
			[
				'label'			 => esc_html__( 'Icon', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'selectors' => [
					'{{WRAPPER}} .eae-icon-wrapper' => 'order:{{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Heading', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_title',
                'selector' => '{{WRAPPER}} .wts-eae-title',
            ]
        );

		$this->add_control(
			'eae_title_fill',
			[
				'label' => esc_html__( 'Fill Style', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' => 'Image', 
					'color' =>  'Color',
				],
				'prefix_class' => 'eae-title-background-',
				
				'default' => 'color',
			]
		);
		$this->add_control(
            'eae_title_color_style',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-title' => 'color: {{VALUE}};',
                ],
				'default'=>'#000000',
				'condition'=>[
					'eae_title_fill'=>'color'
				]
            ]
        );
		$this->add_control(
            'eae_title_hover_color_style',
            [
                'label' => esc_html__( 'Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-title:hover' => 'color: {{VALUE}};',
                ],
				'condition'=>[
					'eae_title_fill'=>'color'
				]
            ]
        );
		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}}  .wts-eae-title',
				
				'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                    'color' => [
                        'global' => [
                            'default' => Global_Colors::COLOR_PRIMARY,
                        ],
                    ],
                ],
				'condition'=>[
					'eae_title_fill'=>'image'
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .wts-eae-title',			
			]
		);
		
		$this->add_responsive_control(
			'eae_title_alignment_position',
			[
				'label' => esc_html__( 'Position', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Flex-start', 'wts-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-justify-center-h',
					],
					'end' => [
						'title' => esc_html__( 'Flex-end', 'wts-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wts-eae-title-wrapper' => 'justify-content: {{VALUE}}',						
				],
			]
		);
		$this->add_responsive_control(
			'eae_title_text_alignment',
			[
				'label' => esc_html__( 'Text Align', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wts-eae-title-wrapper' => 'text-align: {{VALUE}}',						
				],
			]
		);
		$this->add_responsive_control(
            'eae_title_padding_style',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-title-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );
		$this->add_responsive_control(
            'eae_title_margin_style',
            [
                'label' => esc_html__( 'Margin', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-title-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );
		$this->add_control(
			'eae_title_heading_heading',
			[
				'label'     => __( 'Heading Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		Helper::global_icon_style_controls(
            $this,
            [
                'name' => 'eae_heading_title_icon',
                'selector' => '.eae-ah-title-icon',	
                'is_repeater' => false,
            ]
        );
		
		$this->add_control(
			'eae_title_highlighted_text',
			[
				'label'     => __( 'Highlighted Text', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_highlighted_title',
                'selector' => '{{WRAPPER}} .wts-eae-title > span',
            ]
        );
		$this->add_control(
			'eae_highlighted_title_fill',
			[
				'label' => esc_html__( 'Fill Style', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' => 'Image', 
					'color' =>  'Color',
				],
				'default' => 'color',
			]
		);
		$this->add_control(
            'eae_highlighted_title_color_style',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-title > span' => 'color: {{VALUE}};',
                ],
				'default'=>'#000000',
				'condition'=>[
					'eae_highlighted_title_fill'=>'color'
				]
            ]
        );
		$this->add_control(
            'eae_highlighted_title_hover_color_style',
            [
                'label' => esc_html__( 'Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-title > span:hover' => 'color: {{VALUE}};',
                ],
				'condition'=>[
					'eae_highlighted_title_fill'=>'color'
				]
            ]
        );
		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_highlighted',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}} .wts-eae-title > span',
				'condition'=>[
					'eae_highlighted_title_fill'=>'image'
				]
            ]
        );
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow_highlighted',
				'selector' => '{{WRAPPER}} .wts-eae-title > span',
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_highlighted_wrapper',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}} .wts-eae-title span::before',
            ]
        );
		$this->add_responsive_control(
			'background_highlighted_border_radius',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .wts-eae-title span::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
            'background_highlighted_padding',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-title span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );

		
		$this->end_controls_section();
		$this->start_controls_section(
            'section_subtitle_style',
            [
                'label' => esc_html__( 'Sub Heading', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_subtitle',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .wta-eae-sub-title-wrapper',
            ]
        );
		$this->add_control(
			'eae_subtitle_fill',
			[
				'label' => esc_html__( 'Fill Style', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image' => 'Image', 
					'color' =>  'Color',
				],
				'default' => 'color',
			]
		);
		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_subtitle',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}} .wta-eae-sub-title-wrapper',
				'condition'=>[
					'eae_subtitle_fill'=>'image'
				]
            ]
        );
		$this->add_control(
            'eae_subtitle_color_style',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wta-eae-sub-title-wrapper' => 'color: {{VALUE}};',
                ],
				'default'=>'#000000',
				'condition'=>[
					'eae_subtitle_fill'=>'color'
				]
            ]
        );
		$this->add_control(
            'eae_subtitle_hover_color_style',
            [
                'label' => esc_html__( 'Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wta-eae-sub-title-wrapper:hover' => 'color: {{VALUE}};',
                ],
				'condition'=>[
					'eae_subtitle_fill'=>'color'
				]
            ]
        );
		
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'subtitle_shadow',
				'selector' => '{{WRAPPER}} .wta-eae-sub-title-wrapper',
			]
		);
		$this->add_responsive_control(
			'eae_subtitle_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wta-eae-sub-title-wrapper' => 'text-align: {{VALUE}};',										
				],
			]
		);
		$this->add_responsive_control(
            'eae_subtitle_padding_style',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wta-eae-sub-title-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );
		$this->add_responsive_control(
            'eae_subtitle_margin_style',
            [
                'label' => esc_html__( 'Margin', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wta-eae-sub-title-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
            'section_shadow_text_style',
            [
                'label' => esc_html__( 'Shadow Text', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_responsive_control(
            'shadow_text_position_top',
            [
                'label' => esc_html__( 'Top', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['%'],
                'range' => [
                    '%' => [
                        'min' => -150,
                        'max' => 150,
						'step'=>1,
                    ],
                ],
				'default' => [
                    'unit' => '%',
                    'size' => -27,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-shadow-text-wrapper' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'shadow_text_position_left',
            [
                'label' => esc_html__( 'Left', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['%'],
                'range' => [
                    '%' => [
                        'min' => -150,
                        'max' => 150,
						'step'=>1,
                    ],
                ],
				'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-shadow-text-wrapper' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_shadow_text',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .wts-eae-shadow-text-wrapper',
            ]
        );

		$this->add_control(
            'eae_shadow_text_stroke_color',
            [
                'label' => esc_html__( 'Stroke Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-shadow-text-wrapper' => '-webkit-text-stroke-color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control(
            'eae_shadow_text_stroke_fill_color',
            [
                'label' => esc_html__( 'Fill Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-shadow-text-wrapper' => '-webkit-text-fill-color: {{VALUE}};',
                ],
				
            ]
        );
		$this->add_control(
            'eae_shadow_text_stroke_width',
            [
                'label' => esc_html__( 'Stroke Width', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-shadow-text-wrapper' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();
		$this->start_controls_section(
            'section_separator_style',
            [
                'label' => esc_html__( 'Separator', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'eae_separator_width_style_',
            [
                'label' => esc_html__( 'Width', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-separator' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wts-eae-separator-without' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'eae_separator_height_style',
            [
                'label' => esc_html__( 'Height', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wts-eae-separator-without' => 'border-top-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'eae_separator_gap_style',
            [
                'label' => esc_html__( 'Gap', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-separator-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
			'eae_separator_wrapper_alignment_style',
			[
				'label' => esc_html__( 'Position', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Start', 'wts-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-justify-center-h',
					],
					'end' => [
						'title' => esc_html__( 'End', 'wts-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
				'selectors' => [
									'{{WRAPPER}} .wts-eae-separator-wrapper' => 'align-self: {{VALUE}}',						
									'{{WRAPPER}} .wts-eae-separator-without' => 'align-self: {{VALUE}}',						
				],
			]
		);

		$this->add_control(
            'eae_separator_color_style',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .wts-eae-separator' => 'border-top-color: {{VALUE}};',
                    '{{WRAPPER}} .wts-eae-separator-without' => 'border-top-color:{{VALUE}};',
                ],
            ]
        );
		$this->add_responsive_control(
			'separator_border_radius',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .wts-eae-separator' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wts-eae-separator-without' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);


		$this->add_responsive_control(
            'eae_separator_margin_style',
            [
                'label' => esc_html__( 'Margin', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}}  .wts-eae-separator-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}}  .wts-eae-separator-without' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );
		
		$this->add_control(
			'eae_separator_text_heading',
			[
				'label'     => __( 'Text', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
					'eae_heading_separator_style'=>'text'
				]
			]
		);
		$this->add_control(
            'eae_separator_text_color_style',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .wts-eae-separator-text' => 'color: {{VALUE}};',
                ],
				'condition'=>[
					'eae_heading_separator_style'=>'text'
				]
            ]
        );
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_separator_text',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .wts-eae-separator-text',
				'condition'=>[
					'eae_heading_separator_style'=>'text'
				]
            ]
        );
		
		$this->add_control(
			'eae_separator_icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		Helper::global_icon_style_controls($this,[
			'name' => 'eae_heading_separator_with_icon',
			'selector' => '.eae-sep-icon'
	    ]);
		

		
		$this->add_control(
			'eae_separator_img_heading',
			[
				'label'     => __( 'Image', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
					'eae_heading_separator_style'=>'image'
				]
			]
		);
		$this->add_responsive_control(
            'eae_separator_image_height_size_style',
            [
                'label' => esc_html__( 'Height', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%'],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .wts-eae-separator-wrapper img' => 'height: {{SIZE}}{{UNIT}};',
                ],
				'condition'=>[
					'eae_heading_separator_style'=>'image'
				]
            ]
        );
		$this->add_responsive_control(
            'eae_separator_image_width_size_style',
            [
                'label' => esc_html__( 'Width', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%'],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .wts-eae-separator-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
                ],
				'condition'=>[
					'eae_heading_separator_style'=>'image'
				]
            ]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		Helper::global_icon_style_controls($this,[
				'name' => 'eae_icon',
				'selector' => '.eae-ah-icon'
		]);
		
		$this->end_controls_section();

    }
    
    public function render(){
		echo '<div class="wts-eae-advance-heading-content-wrapper" >';
		   $this->render_raw_html();	
        echo '</div>';
    }


	public function get_heading_title($heading){
		$result = preg_replace_callback('/%%(.*?)%%/', function($matches) {
			// Replace "%%" with <span> tags
			return '<span>' . $matches[1] . '</span>';
		}, $heading);
		
		return $result;
	}

	public function render_raw_html(){

		$settings = $this->get_settings_for_display();

	
		if($settings['eae_heading_enable_separator']=='yes'&&$settings['eae_heading_separator_style']!='none'){
			echo '<div class="wts-eae-separator-wrapper">';
			echo '<div class="wts-eae-separator wts-separator-left"></div>';
			if($settings['eae_heading_separator_style']=='icon' ){
				Helper::render_icon_html($settings, $this,'eae_heading_separator_with_icon','eae-sep-icon');
			}
			if($settings['eae_heading_separator_style']=='image'){
				?>
				<div class="wts-eae-separator-image">
					<img src=<?php echo esc_url($settings['eae_heading_separator_with_image']['url']) ?> alt="" height="40" width="40">
				</div>
				<?php 
			}
			if($settings['eae_heading_separator_style']=='text'){
				?>
				<div class="wts-eae-separator-text">
					<?php echo Helper::eae_wp_kses($settings['eae_heading_separator_with_text']); ?>
				</div>
				<?php 
			}
			echo '<div class="wts-eae-separator wts-separator-right"></div>';
			echo '</div>';
		}else if($settings['eae_heading_enable_separator']=='yes'&&$settings['eae_heading_separator_style']=='none'){
			echo '<div class="wts-eae-separator-without"></div>';
		}

		echo '<div class="eae-icon-wrapper">';
			Helper::render_icon_html($settings,$this,'eae_icon','eae-ah-icon');
		echo '</div>';

		$target='';
		if($settings['eae_heading_link']['is_external']=='on'){
			$target='_blank';
		}

		if($settings['eae_heading_title'] != ''){
			// find the postion of all occrrence of %% in string
			$heading = $this->get_heading_title( Helper::eae_wp_kses($settings['eae_heading_title']));
			?>
			<div class="wts-eae-title-wrapper">
				<?php
				Helper::render_icon_html($settings,$this,'eae_heading_title_icon','eae-ah-title-icon'); 
				if($settings['eae_heading_link']['url'] != ''){
					$this->add_render_attribute('eae_heading_title_link', 'class', 'wts-eae-title');
					$this->add_link_attributes('eae_heading_title_link', $settings['eae_heading_link']);
					$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'eae_heading_title_link' ), $heading );
					printf('<%1$s class="wts-eae-title">%2$s</%1$s>',$settings['eae_heading_title_tag'], $title);
					?>
					<?php
				}else{
					$titleTag = Helper::validate_html_tag( $settings['eae_heading_title_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ], 'h2' );
					printf('<%1$s class="wts-eae-title">%2$s</%1$s>',$titleTag, $heading);
				}
				?>
			</div>
			<?php	
		}

		if($settings['eae_heading_sub_title_show']=='yes'&&$settings['eae_heading_sub_title']!=''){
			printf(
				'<%1$s %2$s> %3$s </%1$s>',
				Helper::validate_html_tag( $settings['eae_heading_sub_title_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ], 'h2' ),
				'class="wta-eae-sub-title-wrapper"',
				Helper::eae_wp_kses($settings['eae_heading_sub_title'])
			);
		};

		if($settings['eae_show_shadow_text']=='yes' && $settings['eae_shadow_text_content']!=''){?>
			<div class="wts-eae-shadow-text-wrapper">
				<?php echo Helper::eae_wp_kses($settings['eae_shadow_text_content']);?>
			</div> <?php 
		}
	}

	protected function content_template() {
		?>

        <#
        let renderIconHtml = function(sett, control_name, wClass = '',index = '') {
            var icon_class = '';
            var lottie_data = [];
            if(sett[control_name+'_graphic_type'] != 'none' && sett[control_name+'_graphic_type'] != ''){
                icon_class += ' eae-gbl-icon eae-graphich-type-'+ sett[control_name+'_graphic_type'];
                if(wClass != ''){
                    icon_class += ' '+wClass;     
                }
                icon_class += ' eae-graphic-view-'+sett[control_name+'_view']; 
                if(sett[control_name+'_view'] != 'default'){
                    icon_class += ' eae-graphic-shape-'+sett[control_name+'_shape'];
                }
                if(sett[control_name+'_graphic_type'] == 'lottie'){
                    if( (sett[control_name+'_lottie_animation_url'] != '' ) ||  (sett[control_name+'_source_json']['url'] != '') ) {
                        icon_class += ' eae-lottie-animation eae-lottie';
                        lottie_data = {
                            'loop' : ( sett[control_name+'_lottie_animation_loop'] === 'yes' ) ? true : false,
                            'reverse' : ( sett[control_name+'_lottie_animation_reverse'] === 'yes' ) ? true : false,
                        } 
                        if(sett[control_name+'_source'] == 'media_file' && (sett[control_name+'_source_json']['url'] != '')){
                            lottie_data.url = sett[control_name+'_source_json']['url'];
                        }else{
                            lottie_data.url = sett[control_name+'_lottie_animation_url'];
                        }
                        view.addRenderAttribute('panel-icon-'+ index, 'data-lottie-settings', JSON.stringify(lottie_data));
                    }         
                }
                view.addRenderAttribute('panel-icon-'+ index, 'class', icon_class);
                if(sett[control_name+'_graphic_type'] == 'lottie'){
                    #>
                    <div {{{ view.getRenderAttributeString( 'panel-icon-'+ index ) }}}></div>
                    <#
                }else{
                    if(sett[control_name+'_graphic_type'] === 'icon'){
                        var icon = elementor.helpers.renderIcon( view, sett[control_name+'_graphic_icon'], { 'aria-hidden': true }, 'i' , 'object' );
                        imageHtml = icon.value;
                        #>
                        <div {{{ view.getRenderAttributeString( 'panel-icon-'+ index ) }}}>
                            {{{imageHtml}}}
                        </div>
                        <#
                    }else{
                        var image = {
                            id: sett[control_name+'_graphic_image']['id'],
                            url: sett[control_name+'_graphic_image']['url'],
                            size: sett[control_name+'_graphic_image_size'],
                            dimension: sett[control_name+'_graphic_image_custom_dimension'],
                            model: view.getEditModel()
                        };
                        var image_url = elementor.imagesManager.getImageUrl( image );
                        imageHtml = '<img src="' + image_url + '" class="elementor-animation-' + settings.hover_animation + '" />';
                        #>
                        <div {{{ view.getRenderAttributeString( 'panel-icon-'+ index ) }}}>
                            {{{imageHtml}}}
                        </div>
                        <#
                    }
                }
            }#>
            
            <#
        }

		view.addRenderAttribute('heading-wrapper', 'class', 'wts-eae-advance-heading-content-wrapper');
		if(settings.eae_separator_wrapper_alignment != ''){
			view.addRenderAttribute('heading-wrapper', 'class', 'wts-eae-separator-pos-'+settings.eae_separator_wrapper_alignment);
		}else{
			view.addRenderAttribute('heading-wrapper', 'class', 'wts-eae-separator-pos-center');
		}
        #>
		<div {{{ view.getRenderAttributeString( 'heading-wrapper') }}}>

			<# if(settings.eae_heading_enable_separator == 'yes' && settings.eae_heading_separator_style != 'none'){ #>
				<div class="wts-eae-separator-wrapper">
					<div class="wts-eae-separator wts-separator-left"></div>
					<# if(settings.eae_heading_separator_style == 'icon'){ 
						iconHtml = window.renderIconHtml(view,elementor,settings, 'eae_heading_separator_with_icon', 'eae-sep-icon', index=1);
                                    if(iconHtml != ''){
                                        print(iconHtml);
                                    }
					 } #>
					<# if(settings.eae_heading_separator_style == 'image'){ #>
						<div class="wts-eae-separator-image">
						<img src={{settings.eae_heading_separator_with_image.url}} alt="" height="40" width="40">
						</div>
					<# } #>
					<# if(settings.eae_heading_separator_style == 'text'){ #>
						<div class="wts-eae-separator-text">{{settings.eae_heading_separator_with_text}}</div>
					<# } #>
				
					<div class="wts-eae-separator wts-separator-right"></div>
				</div>
			<# } if (settings.eae_heading_enable_separator == 'yes' && settings.eae_heading_separator_style == 'none'){ #>
				<div class="wts-eae-separator-without"></div>
			<# }  #>


			<div class="eae-icon-wrapper">
			<#
			iconHtml = window.renderIconHtml(view,elementor,settings, 'eae_icon', 'eae-ah-icon', index=2);
				if(iconHtml != ''){
					print(iconHtml);
				}
			#>
			</div>
				
			<#
			let string = settings.eae_heading_title;
			<!-- let titleReplace = titleData.replace( "{" , ' <span> '); -->
			var result = string.replace(/%%(.*?)%%/g, function(match, p1) {
  				return '<span>' + p1 + '</span>';
			});
			
			if(settings.eae_heading_title != ''){ 
				let headingTitleTag = window.eae.validateHTMLTag(settings.eae_heading_title_tag, null, 'h2');
				#>
				<div class="wts-eae-title-wrapper">
					<#if(settings.eae_heading_link.url != ''){
						view.addRenderAttribute('eae_heading_title_link', 'class', 'wts-eae-title custom-link');
						view.addRenderAttribute('eae_heading_title_link', 'href', _.escape(settings.eae_heading_link.url));
						iconHtml = window.renderIconHtml(view,elementor,settings, 'eae_heading_title_icon', 'eae-ah-title-icon', index=3);
							if(iconHtml != ''){
								print(iconHtml);
							}#>
							<{{{headingTitleTag}}} class= "wts-eae-title"> <a {{{ view.getRenderAttributeString( 'eae_heading_title_link' )}}} > {{{result}}} </a> </{{{headingTitleTag}}}>
					<# } else {
						iconHtml = window.renderIconHtml(view,elementor,settings, 'eae_heading_title_icon', 'eae-ah-title-icon', index=3);
							if(iconHtml != ''){
								print(iconHtml);
							}
						 #>
						<{{headingTitleTag}} class="wts-eae-title" > {{{result}}} </{{headingTitleTag}}>
					<# } #>

				</div>
			<# } #>


			<# if(settings.eae_heading_sub_title_show == 'yes' && settings.eae_heading_sub_title != ''){ 
				let subheadingTag = window.eae.validateHTMLTag(settings.eae_heading_sub_title_tag, null, 'h3');
				#>
				
				<{{subheadingTag}} class="wta-eae-sub-title-wrapper" >  {{settings.eae_heading_sub_title}}  </{{subheadingTag}}>
			<# } #>


			<# if(settings.eae_show_shadow_text == 'yes' && settings.eae_shadow_text_content != ''){ #>
				<div class="wts-eae-shadow-text-wrapper">
					{{settings.eae_shadow_text_content}}
				</div>
			<# } #>
		</div>		
		<?php
	}
}    