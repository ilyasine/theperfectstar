<?php 

namespace WTS_EAE\Pro\Modules\TableOfContent\Widgets;

use Elementor\Conditions;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Box_Shadow;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Plugin;

if( ! defined('ABSPATH')){
    exit;
}

class TableOfContent extends EAE_Widget_Base{

    public function get_name(){
        return 'eae-table-of-content';
    }

    public function get_title(){
        return __('Table Of Content','wts-eae');
    }

    public function get_script_depends() {
		return ['eae-lottie'];
	}

    public function get_categories(){
        return ['wts-eae'];
    }

    public function get_icon(){
        return 'eae-icon eae-table-of-content';
    }

    protected function register_controls(){
        
        $this->get_general_section();

        $this->get_settings_section();

        $this->get_navigation_section();

        $this->get_general_style_section();

        $this->get_heading_style_section();

        $this->get_list_style_section();

        $this->get_navigation_style_section();

    }

    public function get_general_style_section(){
        $this->start_controls_section(
            'general_style_section',
            [
                'label' => esc_html__('General','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-wrapper' => 'width:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'general_box_background',
                'selector' => '{{WRAPPER}} .eae-toc-wrapper'
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'general_box_shadow',
                'selector' => '{{WRAPPER}} .eae-toc-wrapper'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'general_box_border',
                'selector' => '{{WRAPPER}} .eae-toc-wrapper',
            ]
        );

        $this->add_responsive_control(
            'general_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'general_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_navigation_section(){
		$this->start_controls_section(
			'navigation_section',
			[
				'label' => esc_html__('Navigation', 'wts-eae'),
                'condition' => [
                    'collapse_box' => 'yes'
                ]
			]
		);

		$this->add_control(
			'collaps_icon_align',
			[
				'label'        => esc_html__( 'Alignment', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'options'      => [
					'row-reverse'  => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-h-align-left',
					],
					'row' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default' => 'row',
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-collapse' => 'flex-direction:{{VALUE}};'
                ]
			]
		);

		$this->add_control(
			'navigation_icon_heading',
			[
				'label'     => esc_html__( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		Helper::eae_media_controls(
			$this,
			[
				'name'          => 'icon',
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
				'label'     => esc_html__( 'Active Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		Helper::eae_media_controls(
			$this,
			[
				'name'          => 'active_icon',
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

    public function get_navigation_style_section() {

		$this->start_controls_section(
			'section_faq_nav_style',
			[
				'label' => esc_html__( 'Navigation', 'wts-eae' ),
				'tab'		=> Controls_Manager::TAB_STYLE,
				'condition' => [
                    'collapse_box' => 'yes'
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
                'name'          => 'icon',
                'selector'      => '.eae-toc-collapse-icon',
                'is_repeater'   => 'false', 
				'is_active_tab' => [
					'label'		=> 'Opened',
					'selector'  => '.eae-toc-active'
                ],
                'default' => [
                    'primary_color' => Global_Colors::COLOR_TEXT
                ]
            ]
        );

        $this->start_injection( [
			'of' => 'collapse_rotate',
		] );
		
		$this->add_responsive_control(
			'collapse_icon_space',
			[
				'label'     => esc_html__( 'Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ]
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-toc-collapse' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
            ]
		);

		$this->end_injection();

		$this->end_controls_section();
	}

    public function get_list_style_section(){
        $this->start_controls_section(
            'list_style_section',
            [
                'label' => esc_html__('List','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'list_item_marker_size',
            [
                'label' => esc_html__('Marker Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper::before' => 'font-size:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper i' => 'font-size:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'list_item_marker_spacing',
            [
                'label' => esc_html__('Marker Spacing','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper' => 'column-gap:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'list_item_indent',
            [
                'label' => esc_html__('Indent','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%','em'],
                'default' => [
                    'unit' => 'em'
                ],
                'condition' => [
                    'hierarchical_view' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-headings-wrapper li ul' => 'margin-left: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
			'list_item_spacing',
			[
				'label' => esc_html__('Element Gap','wts-eae'),
				'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .eae-toc-headings-wrapper ul' => 'row-gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-toc-headings-wrapper li ul' => 'row-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

        $this->add_control(
            'child_element_spacing',
            [
                'label' => esc_html__('Nested Element Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'condition' => [
                    'hierarchical_view' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-headings-wrapper ul li' => 'row-gap:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'divider_for_text_style_control',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'list_item_typography',
                'selector' => '{{WRAPPER}} .eae-toc-heading-anchor',
            ]
        );

        $this->start_controls_tabs('list_style_tabs');

        $this->start_controls_tab(
            'list_style_normal',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'list_item_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'list_item_marker_color',
            [
                'label' => esc_html__('Marker Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper::before' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper i' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'list_item_text_shadow',
                'selector' => '{{WRAPPER}} .eae-toc-heading-anchor',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_item_background',
                'selector' => '{{WRAPPER}} .eae-toc-heading-anchor-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'list_item_border',
                'selector' => '{{WRAPPER}} .eae-toc-heading-anchor-wrapper'
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'list_style_hover',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'list_item_text_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper:not(.eae-toc-active-heading):hover .eae-toc-heading-anchor' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'list_item_marker_hover_color',
            [
                'label' => esc_html__('Marker Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper:not(.eae-toc-active-heading):hover::before' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper:not(.eae-toc-active-heading):hover i' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'list_item_hover_text_shadow',
                'selector' => '{{WRAPPER}} .eae-toc-heading-anchor-wrapper:not(.eae-toc-active-heading):hover .eae-toc-heading-anchor',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_item_hover_background',
                'selector' => '{{WRAPPER}} .eae-toc-heading-anchor-wrapper:not(.eae-toc-active-heading):hover',
            ]
        );

        $this->add_control(
            'list_item_border_hover_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper:not(.eae-toc-active-heading):hover ' => 'border-color:{{VALUE}};'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'list_style_active',
            [
                'label' => esc_html__('Active','wts-eae'),
            ]
        );

        $this->add_control(
            'list_item_text_active_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-active-heading .eae-toc-heading-anchor' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'list_item_marker_active_color',
            [
                'label' => esc_html__('Marker Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper.eae-toc-active-heading::before' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper.eae-toc-active-heading i' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'list_item_active_texT_shadow',
                'selector' => '{{WRAPPER}} .eae-toc-active-heading .eae-toc-heading-anchor',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_item_active_background',
                'selector' => '{{WRAPPER}} .eae-toc-active-heading.eae-toc-heading-anchor-wrapper',
            ]
        );

        $this->add_control(
            'list_item_border_active_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-active-heading' => 'border-color:{{VALUE}};'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'list_item_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'default' => [
                    'top' => '3',
                    'right' => '3',
                    'bottom' => '0',
                    'left' => '0',
                    'unit' => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'list_item_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'list_box_style_heading',
            [
                'label' => esc_html__('List','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'list_max_height',
            [
                'label' => esc_html__('Height(px)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 1000,
                        'min' => 0,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-headings-wrapper' => 'max-height:{{SIZE}}{{UNIT}}; overflow:scroll;',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'list_box_background',
                'selector' => '{{WRAPPER}} .eae-toc-headings-wrapper'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'list_box_border',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => '0',
                            'right' => '1',
                            'bottom' => '1',
                            'left' => '1',
                            'isLinked' => false,
                        ],
                    ],
                    'color' => [
                        'default' => '#9DA5AE',
                    ],
                ],
                'selector' => '{{WRAPPER}} .eae-toc-headings-wrapper',
            ]
        );
        
        $this->add_responsive_control(
            'list_box_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-headings-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'list_box_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-headings-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_heading_style_section(){
        $this->start_controls_section(
            'heading_style_section',
            [
                'label' => esc_html__('Header','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_text_color',
            [
                'label' => esc_html__('Text Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
                'selector' => '{{WRAPPER}} .eae-toc-heading', 
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'headin_text_stroke',
                'selector' => '{{WRAPPER}} .eae-toc-heading'
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'heading_text_shadow',
                'selector' => '{{WRAPPER}} .eae-toc-heading'
            ]
        );

        $this->add_responsive_control(
            'heading_alignment',
            [
                'label' => esc_html__('Text Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'left',
                'options'     => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'wts-eae' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Middle', 'wts-eae' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'wts-eae' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-container' => 'text-align: {{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'header_background',
                'separator' => 'before',
                'selector' => '{{WRAPPER}} .eae-toc-heading-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'heading_box_border',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => '1',
                            'right' => '1',
                            'bottom' => '1',
                            'left' => '1',
                            'isLinked' => false,
                        ],
                    ],
                    'color' => [
                        'default' => '#9DA5AE',
                    ],
                ],
                'selector' => '{{WRAPPER}} .eae-toc-heading-container',
            ]
        );

        $this->add_responsive_control(
            'heading_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'heading_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-heading-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'icon_heading',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'heading_icon',
                'selector'      => '.eae-toc-heading-icon',
            ]
        );

        $this->start_injection( [
			'of' => 'heading_icon_rotate',
		] );
		
		$this->add_responsive_control(
			'heading_icon_space',
			[
				'label'     => esc_html__( 'Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ]
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-toc-icon-heading-wrapper' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
            ]
		);

		$this->end_injection();

        $this->end_controls_section();
    }

    public function get_general_section(){
        $this->start_controls_section(
            'general_section',
            [
                'label' => esc_html__('General','wts-eae'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        Helper::eae_media_controls(
            $this,
            [
                'name' => 'heading_icon',
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
            ]
        );

        $this->add_control(
            'heading',
            [
                'label' => esc_html__('Heading','wts-eae'),
                'default' => esc_html__('Table of Contents','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'heading_html_tag',
            [
                'label' => esc_html__('HTML Tag','wts-eae'),
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
                'default' => 'h4'
            ]
        );

        $this->add_control(
            'include_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->start_controls_tabs('heading_include_exclude');

        $this->start_controls_tab(
            'heading_include',
            [
                'label' => esc_html__('Include','wts-eae'),
            ]
        );

        $this->add_control(
			'anchors_by_tags',
			[
				'label' => esc_html__( 'Anchors By Tags', 'wts-eae' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => [ 'h2', 'h3', 'h4', 'h5', 'h6' ],
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'label_block' => true,
				'frontend_available' => true,
			]
		);

        $this->add_control(
            'included_container',
            [
                'label' => esc_html__('Container','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__( 'This control confines the Table of Contents to heading elements under a specific container', 'wts-eae' ),
                'frontend_available' => true,
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'exclude_tab',
            [
                'label' => esc_html__('Exclude','wts-eae'),
            ]
        );

        $this->add_control(
            'anchors_by_selector',
            [
                'label' => esc_html__('Anchors By Selector','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'description' => esc_html__( 'CSS selectors, in a comma-separated list', 'wts-eae' ),
                'frontend_available' => true,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'exclude_heading_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'marker_type',
            [
                'label' => esc_html__('Marker Type','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'counter' => esc_html__('Counter','wts-eae'),
                    'bullets' => esc_html__('Bullets','wts-eae'), 
                ],
                'default' => 'counter',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'wts-eae' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
				'condition' => [
					'marker_type' => 'bullets',
				],
				'skin' => 'inline',
				'label_block' => false,
				'exclude_inline_options' => [ 'svg' ],
				'frontend_available' => true,
			]
		);

        $this->add_control(
            'counter_style',
            [
                'label' => esc_html__('Counter Type','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'upper-alpha' => esc_html__('Upper Alpha','wts-eae'),
                    'lower-alpha' => esc_html__('Lower Alpha','wts-eae'),
                    'upper-roman' => esc_html__('Upper Roman','wts-eae'),
                    'lower-roman' => esc_html__('Lower Roman','wts-eae'),
                    'number' => esc_html__('Number','wts-eae'),
                    'decimal-leading-zero'  => esc_html__( 'Decimal Leading Zero', 'wts-eae' ),
                    'lower-greek'  => esc_html__( 'Lower Greek', 'wts-eae' ),
                ],
                'default' => 'number',
                'selectors' => [    
                    '{{WRAPPER}} .eae-toc-heading-anchor-wrapper::before' => 'counter-increment: toc-heading; content: counters(toc-heading,".",{{VALUE}} ) "{{list_counter_suffix.value}} ";',
                ],
                'condition' => [
                    'marker_type' => 'counter',
                ]
            ]
        );

        $this->add_control(
            'list_counter_suffix',
            [
                'label' => esc_html__('Suffix','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    ' ' => esc_html__('None','wts-eae'),
                    ':' => esc_html__('Colon','wts-eae'),
                    ')' => esc_html__('Bracket','wts-eae'),
                    '.' => esc_html__('Dot','wts-eae'),
                ],

                'default' => '.',
                'condition' => [
                    'marker_type' => 'counter',
                ],
            ]   
        );

        $this->add_control(
            'word_wrap_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'word_wrap',
            [
                'label' => esc_html__('Word Wrap','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'hierarchical_view',
            [
                'label' => esc_html__('Hierarchical View','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();
    }

    public function get_settings_section(){

        $this->start_controls_section( 
            'settings_section',
            [
                'label' => esc_html__('Settings','wts-eae'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'width_devider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'collapse_box',
            [
                'label' => esc_html__('Collapse Box','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
            ]
        );

        $breakpoints = Plugin::$instance->breakpoints->get_breakpoints_config();
        $active_devices = Plugin::$instance->breakpoints->get_active_devices_list();
        $device_name = [];
        $device_name_value = [];
		foreach($active_devices as $active_device_name){
            if($active_device_name == 'desktop'){
                continue;
            }
            $device_name[$active_device_name]= sprintf(
				esc_html__( '%1$s (%2$s %3$dpx)','wts-eae'),
				ucfirst($active_device_name),
				'<',
				$breakpoints[$active_device_name]['default_value']
            );
            $device_name_value[$breakpoints[$active_device_name]['default_value']] = sprintf(
				esc_html__( '%1$s (%2$s %3$dpx)','wts-eae'),
				ucfirst($active_device_name),
				'<',
				$breakpoints[$active_device_name]['default_value']
            );
        }
        $device_name['desktop'] = esc_html__( 'Desktop (< 1440px)','wts-eae');
        $device_name_value['1440'] = esc_html__( 'Desktop (< 1440px)','wts-eae');

        $this->add_control(
            'toc_collapse_devices',
            [
                'label' => esc_html__('Collapsed On','wts-eae'),
				'type' => Controls_Manager::SELECT,		
                'options' => $device_name_value,
                'default' => ['1440'],
                'frontend_available' => true,
                'condition' => [
                    'collapse_box' => 'yes'
                ]
            ]
        );

        // $this->add_control(
        //     'collapse_box',
        //     [
        //         'label' => esc_html__('Device','wts-eae')
        //     ]
        // );

        // $this->add_control(
        //     'collapse_box_icon',
        //     [
        //         'label' => esc_html__('Icon','wts-eae'),
        //         'type' => Controls_Manager::ICONS,
        //         'skin' => 'inline',
        //         'label_block' => false,
        //         'default' => [
        //             'value'   => 'fas fa-chevron-down',
        //             'library' => 'fa-solid'
        //         ],
        //         'condition' => [
        //             'collapse_box' => 'yes'
        //         ]   
        //     ]
        // );

        // $this->add_control(
        //     'collapse_box_active_icon',
        //     [
        //         'label' => esc_html__('Collapsed Icon','wts-eae'),
        //         'type' => Controls_Manager::ICONS,
        //         'skin' => 'inline',
        //         'label_block' => false,
        //         'default' => [
        //             'value' => 'fas fa-chevron-up',
        //             'library' => 'fa-solid',
        //         ],
        //         'condition' => [
        //             'collapse_box' => 'yes'
        //         ]
        //     ]
        // );

        $this->add_control(
            'sticky_devider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'toc_sticky',
            [
                'label' => esc_html__('Sticky','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'toc_sticky_devices',
            [
                'label' => esc_html__('Sticky Devices','wts-eae'),
                'label_block' => true,
				'type' => Controls_Manager::SELECT2,	
                'multiple' => true,			
                'options' => $device_name,
                'default' => [ 'desktop' ],
                'frontend_available' => true,
                'condition' => [
                    'toc_sticky' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'horizontal_alignment',
            [
                'label' => esc_html__('Horizontal Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-h-align-right'
                    ]
                ],
                'toggle' => false,
                'default' => 'left',
                'condition' => [
                    'toc_sticky' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'left_spacing',
            [
                'label' => esc_html__('Left','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ] 
                ],
                'default' => [
                    'size' => '60',
                    'unit' => 'px',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'horizontal_alignment',
                            'operator' => '==',
                            'value' => 'left',
                        ],
                        [
                            'name' => 'toc_sticky',
                            'operator' => '==',
                            'value'=> 'yes'
                        ]
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-sticky' => 'left: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'right_spacing',
            [
                'label' => esc_html__('Right','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'default' => [
                    'size' => '60',
                    'unit' => 'px',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'horizontal_alignment',
                            'operator' => '==',
                            'value' => 'right',
                        ],
                        [
                            'name' => 'toc_sticky',
                            'operator' => '==',
                            'value'=>'yes'
                        ]
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-sticky' => 'right: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'vertical_alignment',
            [
                'label' => esc_html__('Vertical Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top','wts-eae'),
                        'icon' => 'eicon-v-align-top'
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom','wts-eae'),
                        'icon' => 'eicon-v-align-bottom'
                    ]
                ],
                'default' => 'top',
                'toggle' => false,
                'frontend_available' => true,
                'condition' => [
                    'toc_sticky' =>'yes'
                ],
            ]
        );

        $this->add_responsive_control(
            'top_spacing',
            [
                'label' => esc_html__('Top (px)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
                'default' => [
                    'size' => '0',
                    'unit' => 'px'
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'toc_sticky',
                            'operator' => '==',
                            'value'=>'yes'
                        ],
                        [
                            'name' => 'vertical_alignment',
                            'operator' => '==',
                            'value' => 'top',
                        ]
                    ]
                ],
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-sticky' => 'top:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'bottom_spacing',
            [
                'label' => esc_html__('Bottom (px)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
                'default' => [
                    'size' => '0',
                    'unit' => 'px'
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'toc_sticky',
                            'operator' => '==',
                            'value'=>'yes'
                        ],
                        [
                            'name' => 'vertical_alignment',
                            'operator' => '==',
                            'value' => 'bottom',
                        ]
                    ]
                ],
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-sticky' => 'bottom:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'z-index',
            [
                'label' => esc_html__('Z-Index','wts-eae','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                ],
                'condition' => [
                    'toc_sticky' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-toc-wrapper' => 'z-index: {{SIZE}};'
                ],
            ]
        );

        $this->add_control(
            'toc_stay_in_column',
            [
                'label' => esc_html__('Stay In Container','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    'toc_sticky' => 'yes'
                ], 
            ]
        );

        $this->add_control(
            'follow_heading_control_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'follow_heading',
            [
                'label' => esc_html__('Follow Heading','wts-ese'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'follow_heading_offset',
            [
                'label' => esc_html__('Offset','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'unit' => '%',
                    'size' => '50',
                ],
                'frontend_available' => true,
                'condition' => [
                    'follow_heading' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function render(){
    
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('toc-wrapper','class','eae-toc-wrapper');
        $this->add_render_attribute('toc-heading-container','class','eae-toc-heading-container');
        $this->add_render_attribute('toc-heading','class','eae-toc-heading');
        $this->add_render_attribute('toc-heading-list','class','eae-toc-headings-wrapper');
        if($settings['toc_sticky'] == 'yes'){
            $this->add_render_attribute('toc-wrapper','class','eae-toc-sticky-type-on-place');
        }
        if($settings['marker_type'] == 'counter'){
            $this->add_render_attribute('toc-heading-list','class','eae-toc-counter');
        }
        if($settings['word_wrap'] == 'yes'){
            $this->add_render_attribute('toc-heading-list','class','eae-toc-word-wrap');
        }
        if($settings['collapse_box'] == 'yes'){
            $this->add_render_attribute('toc-heading-container','class','eae-toc-collapse');
            $this->add_render_attribute('toc-heading-list','class','eae-toc-hide');
        }
        ?>
            <div <?php echo $this->get_render_attribute_string('toc-wrapper'); ?>>
                <div <?php echo $this->get_render_attribute_string('toc-heading-container') ?>>
                    <?php
                        ?><div class="eae-toc-icon-heading-wrapper"><?php
                            Helper::render_icon_html($settings,$this, 'heading_icon', 'eae-toc-heading-icon');
                            if($settings['heading'] !== '')  { 
                                $heading = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['heading_html_tag'] ), $this->get_render_attribute_string( 'toc-heading' ), Helper::eae_wp_kses($settings['heading'])); 
                                echo $heading;
                            } ?>
                        </div><?php
                        if($settings['collapse_box'] == 'yes'){
                            ?><div class="eae-toc-icon-wrap">
                                <?php
                                    Helper::render_icon_html($settings, $this, 'icon', 'eae-toc-icon eae-toc-collapse-icon');
                                    Helper::render_icon_html($settings, $this, 'active_icon', 'eae-toc-active-icon eae-toc-collapse-icon');
                                ?>
                            </div><?php
                        }
                    ?>
                </div>
                <?php if($settings['anchors_by_tags'] != null) { ?>
                    <div <?php echo $this->get_render_attribute_string('toc-heading-list'); ?>></div>
                <?php } ?>
            </div>
        <?php
    }
}
?>