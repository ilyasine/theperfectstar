<?php 

namespace WTS_EAE\Pro\Modules\InfoGroup\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Repeater;
use Elementor\Plugin as EPlugin;
use WTS_EAE\Classes\Helper;
use Elementor\Icons_Manager;
use Elementor\Utils;
use ElementorPro\Modules\Lottie\Widgets\Lottie;

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class InfoGroup extends EAE_Widget_Base {

    public function get_name() {
		return 'eae-info-group';
	}

    public function get_title(){
        return __('Info Group','wts-eae');
    } 

    public function get_icon() {
		return 'eae-icon eae-info-group';
	}

    public function get_categories(){
        return ['wts-eae'];
    }

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

    protected function register_controls(){

        $this->get_general_section();

        $this->get_grid_section();

        $this->get_item_style_section();

        $this->get_media_style_section();

        $this->get_content_style_section();

        $this->get_description_style_section();

        $this->get_button_style_section();
    }

    public function get_content_style_section(){
        $this->start_controls_section(
            'content_style_section',
            [
                'label' => esc_html__('Content','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'text_alignment',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__('Justify','wts-eae'),
                        'icon' => 'eicon-text-align-justify',
                    ]
                ],
                'default' => 'left',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper' => 'text-align:{{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'title_style_heading',
            [
                'label' => esc_html__('Title','wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => esc_html__('Spacing','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-title' => 'margin-bottom:{{SIZE}}px',
                ]
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global'    => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-title' => 'color:{{VALUE}};',
                ] 
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover/Active Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-title, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-title' => 'color:{{VALUE}};' 
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .eae-ig-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'title_text_stroke',
                'selector' => '{{WRAPPER}} .eae-ig-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-title',
            ]
        );

        $this->add_control(
            'sub_title_style_heading',
            [
                'label' => esc_html__('Sub Title','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'sub_title_spacing',
            [
                'label' => esc_html__('Spacing','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-sub-title' => 'margin-bottom:{{SIZE}}px',
                ]
            ]
        );

        $this->add_control(
            'sub_title_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global'    => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-sub-title' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'sub_title_text_hover_color',
            [
                'label' => esc_html__('Hover/Active','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-sub-title, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-sub-title' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'selector' => '{{WRAPPER}} .eae-ig-sub-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'sub_title_text_stroke',
                'selector' => '{{WRAPPER}} .eae-ig-sub-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'sub_title_text_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-sub-title',
            ]
        );

        $this->add_control(
            'short_description_style_heading',
            [
                'label' => esc_html__('Short Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'short_description_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global'    => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-short-description' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'short_description_hover_color',
            [
                'label' => esc_html__('Hover/Active Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-short-description, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-short-description' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'short_descrioption_typography',
                'selector' => '{{WRAPPER}} .eae-ig-short-description',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'short_description_text_stroke',
                'selector' => '{{WRAPPER}} .eae-ig-short-description',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'short_description_text_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-short-description',
            ]
        );

        $this->end_controls_section();
    }

    public function get_description_style_section(){
        $this->start_controls_section(
            'description_style_section',
            [
                'label' => esc_html__('Description','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'description_alignment',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__('Justify','wts-eae'),
                        'icon' => 'eicon-text-align-justify',
                    ]
                ],
                'default' => 'center',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-info-wrapper' => 'text-align:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'description_animtion_type',
            [
                'label' => esc_html__('Animation Type','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'fade' => esc_html__('Fade','wts-eae'),
                    'slide' => esc_html__('Slide','wts-eae'),
                ],
                'default' => 'slide',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'description_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-info-wrapper' => 'width:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'description_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-justify-start-h',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-justify-center-h',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h',
                    ],
                ],
                'default' => 'center',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-info-wrapper' => 'justify-self:{{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'description_spacing',
            [
                'label' => esc_html__('Spacing','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-info-wrapper' => 'margin-top: calc( {{SIZE}}{{UNIT}} - {{row_gap.size}}{{row_gap.unit}} ); margin-bottom: calc( {{SIZE}}{{UNIT}} - {{row_gap.size}}{{row_gap.unit}} );',
                    '{{WRAPPER}} .eae-ig-wrapper.eae-ig-active' => 'padding-bottom: calc( ( {{SIZE}}{{UNIT}} - {{row_gap.size}}{{row_gap.unit}} ) * -1 );'
                ]
            ]
        );
    
        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-info-wrapper' => 'color:{{VALUE}};',
                ]
            ]
        );
    
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .eae-ig-item-info-wrapper'
            ]
        );
    
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'description_background',
                'selector' => '{{WRAPPER}} .eae-ig-item-info-wrapper'
            ]
        );
    
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'description_box_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-item-info-wrapper'
            ]
        );
    
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'description_border',
                'selector' => '{{WRAPPER}} .eae-ig-item-info-wrapper'
            ]
        );
    
        $this->add_responsive_control(
            'description_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [ 
                    '{{WRAPPER}} .eae-ig-item-info-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
    
        $this->add_responsive_control(
            'description_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-info-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
    
        $this->add_control(
            'description_icon_style_heading',
            [
                'label' => esc_html__('Close Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_icon_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-close-button i' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-ig-close-button svg' => 'fill:{{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'description_icon_hover_color',
            [
                'label' => esc_html__('Hover Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-close-button:hover i' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-ig-close-button:hover svg' => 'fill:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'description_icon_size',
            [
                'label' => esc_html__('Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-close-button i' => 'font-size:{{SIZE}}px;',
                    '{{WRAPPER}} .eae-ig-close-button svg' => 'font-size:{{SIZE}}px;',
                ]
            ]
        );

        $this->add_responsive_control(
            'description_horizontal_offset',
            [
                'label' => esc_html__('Horizontal Offset','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default'    => [
					'size' => 10,
                    'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-close-button' => 'right:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'description_vertical_offset',
            [
                'label' => esc_html__('Vertical Offset','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default'    => [
					'size' => 5,
                    'unit' => 'px',
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-close-button' => 'top:{{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_media_style_section(){
        $this->start_controls_section(
            'media_style_section',
            [
                'label' => esc_html__('Media','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'media_alignment',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-justify-start-h',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-justify-center-h',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h',
                    ],
                ],
                'default' => 'left',
                'selectors_dictionary' => [
                    'left' => 'start;',
                    'center' => 'center;',
                    'right' => 'end;',
                ],
                'toggle' => false,
                'condition' => [
                    'icon_position' => 'top'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-media-container' => 'align-self:{{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'media_spacing',
            [
                'label' => esc_html__('Spacing','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => 10,
                    'unit' => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'media_size',
            [
                'label' => esc_html__('Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
						'max' => 330,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-media-type-image' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-ig-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-ig-media-type-lottie' => 'font-size: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'media_rotate',
            [
                'label' => esc_html__('Rotate','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
                'selectors' => [
					'{{WRAPPER}} .eae-ig-icon i, {{WRAPPER}} .eae-ig-lottie, {{WRAPPER}} .eae-ig-image, {{WRAPPER}} .eae-ig-media-type-image' => 'transform: rotate({{SIZE}}{{UNIT}});',
				]
            ]
        );

        $this->start_controls_tabs('media_style_tabs');

        $this->start_controls_tab(
            'media_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'media_icon_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global'    => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-media-container i' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-ig-media-container.eae-ig-icon svg' => 'fill:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'media_background',
                'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'default' => '#D5D8DC',
					],
				],
                'selector' => '{{WRAPPER}} .eae-ig-media-view-type-framed, {{WRAPPER}} .eae-ig-media-view-type-stacked, {{WRAPPER}} .eae-ig-media-view-type-stacked svg',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'media_border',
                'selector' => '{{WRAPPER}} .eae-ig-media-view-type-framed, {{WRAPPER}} .eae-ig-media-container img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'media_hover_tab',
            [
                'label' => esc_html__('Hover/Active','wts-eae'),
            ]
        );

        $this->add_control(
            'media_icon_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-media-container i, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-media-container i' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-media-container.eae-ig-icon svg, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-media-container.eae-ig-icon svg' => 'fill:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'media_hover_background',
                'selector' => '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-media-view-type-framed, {{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-media-view-type-stacked, {{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-media-view-type-stacked svg, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-media-view-type-framed, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-media-view-type-stacked, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-media-view-type-stacked svg,',
            ]
        );

        $this->add_control(
            'media_border_hover_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-media-view-type-framed' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover .eae-ig-media-container.eae-ig-media-type-image img' => 'border-color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'media_hover_animation',
            [
                'label' => esc_html__('Hover Animation','wts-eae'),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'media_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [ 
                    '{{WRAPPER}} .eae-ig-media-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-ig-media-container img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'media_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-icon.eae-ig-media-container:not(.eae-ig-media-view-type-default)' => 'padding: {{SIZE}}px;',
                    '{{WRAPPER}} .eae-ig-lottie.eae-ig-media-container:not(.eae-ig-media-view-type-default)' => 'padding: {{SIZE}}px;'
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_grid_section(){
        $this->start_controls_section(
            'grid_section',
            [
                'label' => esc_html__('Grid','wts-eae'),
            ]
        );

        $this->add_responsive_control(
            'column_number',
            [
                'label' => esc_html__('Column','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-wrapper' => 'grid-template-columns : repeat({{VALUE}}, 1fr);'
                ]
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => '15',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-wrapper' => 'column-gap: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Row Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => '15',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-wrapper' => 'row-gap:{{SIZE}}{{UNIT}};',
                ] 
            ]
        );

        $this->end_controls_section();
    }

    public function get_item_style_section(){
        $this->start_controls_section(
            'item_style_section',
            [
                'label' => esc_html__('Item','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE, 
            ]
        );

        $this->add_responsive_control(
            'icon_position',
            [
                'label' => esc_html__('Media Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'top' => [
						'title' => esc_html__( 'Top', 'wts-eae' ),
						'icon' => 'eicon-v-align-top',
					],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-h-align-right'
                    ]
                ],
                'selectors_dictionary' => [
                    'left' => 'row',
                    'top' => 'column',
                    'right' => 'row-reverse',
                ],
                'toggle' => false,
                'default' => 'top',
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper' => 'flex-direction: {{VALUE}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'item_vertical_alignment',
            [
                'label' => esc_html__('Vertical Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top','wts-eae'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => esc_html__('Middle','wts-eae'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom','wts-eae'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'middle',
				'toggle' => false,
                'selectors_dictionary' => [
                    'top' => 'start',
                    'middle' => 'center',
                    'bottom' => 'end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-wrapper .eae-ig-item-wrapper' => 'justify-content:{{VALUE}};',
                ],
                'condition' => [
                    'icon_position' => 'top'
                ]
            ]
        );

        $this->add_responsive_control(
            'vertical_alignment',
            [
                'label' => esc_html__('Vertical Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top','wts-eae'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => esc_html__('Middle','wts-eae'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom','wts-eae'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'top',
				'toggle' => false,
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-wrapper .eae-ig-item-wrapper' => 'align-items:{{VALUE}};',
                ],
                'condition' => [
                    'icon_position!' => 'top'
                ]
            ]
        );

        $this->start_controls_tabs('box_container_tab');

        $this->start_controls_tab(
            'box_container_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae')
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'box_container_background_color',
                'selector' => '{{WRAPPER}} .eae-ig-item-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_container_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-item-wrapper'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_container_border',
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
                        'default' => '#D5D8DC',
                    ],
                ],
                'selector' => '{{WRAPPER}} .eae-ig-item-wrapper', 
            ]
        );

        $this->add_responsive_control(
            'box_container_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'box_container_hover_tab',
            [
                'label' => esc_html__('Hover/Active','wts-eae'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'box_container_hover_background_color',
                'selector' => '{{WRAPPER}} .eae-ig-item-wrapper:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_container_hover_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-item-wrapper:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item'
            ]
        );

        $this->add_control(
            'box_container_hover_border_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item' => 'border-color: {{VALUE}};',
                ], 
            ]
        );

        $this->add_responsive_control(
            'box_container_hover_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'box_container_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'separator' => 'before',
                'size_units' => ['px','%'],
                'default' => [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-item-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function select_elementor_page( $type ) {
		$args  = [
			'tax_query'      => [
				[
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => $type,
				],
			],
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		];
		$query = new \WP_Query( $args );

		$posts = $query->posts;

		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}

		if ( empty( $items ) ) {
			$items = [];
		}

		return $items;
	}

	private function select_ae_templates() {
		$ae_id = [];
		if ( wp_verify_nonce( isset( $_GET['post'] ) ) ) {
			$ae_id = wp_verify_nonce( [ $_GET['post'] ] );
		}
		$args  = [
			'post_type'      => 'ae_global_templates',
			'meta_key'       => 'ae_render_mode',
			'meta_value'     => 'block_layout',
			'posts_per_page' => -1,
			'post__not_in'   => $ae_id,
		];
		$query = new \WP_Query( $args );

		$posts = $query->posts;

		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}

		if ( empty( $items ) ) {
			$items = [];
		}

		return $items;
	}

    public function get_general_section(){

        $this->start_controls_section(
            'general_section',
            [
                'label' => esc_html__('General','wts-eae'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'media_type',
            [
                'label' => esc_html__('Media Type','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'none' => [
                        'title' => esc_html__('None','wts-eae'),
                        'icon' => 'eicon-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__('Icon','wts-eae'),
                        'icon' => 'eicon-star',
                    ],
                    'image' => [
                        'title' => esc_html__('Image','wts-eae'),
                        'icon' => 'eicon-image-bold',
                    ],
                    'lottie' => [
                        'title' => esc_html__('Lottie Animation','wts-eae'),
                        'icon' => 'eicon-lottie',
                    ]
                ],
                'default' => 'icon',
                'toggle' => false,
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-star',
                    'library' => 'fa-solid'
                ], 
                'condition' => [
                    'media_type' => 'icon'
                ]
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__('Image','wts-eae'),
                'type'        => Controls_Manager::MEDIA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'condition' => [
                    'media_type' => 'image'
                ]
            ]
        );

        $repeater->add_control(
			'lottie_source',
			[
				'label' => esc_html__( 'Source', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media_file',
				'options' => [
					'media_file' => esc_html__( 'Media File', 'wts-eae' ),
					'external_url' => esc_html__( 'External URL', 'wts-eae' ),
				],
                'condition' => [
                    'media_type' => 'lottie'
                ]
			]
		);

        $repeater->add_control(
			'lottie_source_json',
			[
				'label' => esc_html__( 'Upload JSON File', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'lottie_source',
                            'operator' => '==',
                            'value' => 'media_file',
                        ],
                        [
                            'name' => 'media_type',
                            'operator' => '==',
                            'value' => 'lottie',
                        ],
                    ]
                ],
			]
		);

        $repeater->add_control(
			'lottie_animation_url',
			[
				'label' => esc_html__( 'Animation JSON URL', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'lottie_source',
                            'operator' => '==',
                            'value' => 'external_url',
                        ],
                        [
                            'name' => 'media_type',
                            'operator' => '==',
                            'value' => 'lottie',
                        ],
                    ]
                ],
			]
		);

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image',
                'default'   => 'full',
                'condition' => [
                    'media_type' => 'image',
                ]
            ]
        );

        $repeater->add_control(
			'lottie_animation_loop',
			[
				'label' => esc_html__( 'Loop', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'default' => 'yes',		
                'condition' => [
                    'media_type' => 'lottie'
                ]
			]
		);

        $repeater->add_control(
			'lottie_animation_reverse',
			[
				'label' => esc_html__( 'Reverse', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
                    'media_type' => 'lottie'
                ]
			]
		);

        $repeater->add_control(
            'media_view',
            [
                'label' => esc_html__('View','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => esc_html__('Default','wts-eae'),
                    'stacked' => esc_html__('Stacked','wts-eae'),
                    'framed' => esc_html__('Framed','wts-eae'),
                ],
                'default' => 'default',
                'condition' => [
                    'media_type!' => ['none','image']
                ]
            ]
        );

        $repeater->add_control(
            'media_shap',
            [
                'label' => esc_html__('Shape','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'circle' => esc_html__('Circle','wts-eae'),
                    'square' => esc_html__('Square','wts-eae'),
                ],
                'default' => 'circle',
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'media_view',
                            'operator' => '!=',
                            'value' => 'default',
                        ],
                        [
                            'name' => 'media_type',
                            'operator' => '!=',
                            'value' => 'image',
                        ],
                    ]
                ],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__('Title','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'separator' => 'before',
                'default' => esc_html__('List Item','wts-eae'),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'sub_title',
            [
                'label' => esc_html__('Sub Title','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ] 
            ]
        );

        $repeater->add_control(
            'short_description',
            [
                'label' => esc_html__('Short Description','wts-eae'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae'),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'description_heading',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'long_description_type',
            [
                'label' => esc_html__('Type'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'content'      => esc_html__( 'Content', 'wts-eae' ),
                    'saved_section' => esc_html__( 'Saved Section', 'wts-eae' ),
                    'saved_container' => esc_html__('Saved Container','wts-eae'),
                    'saved_page'    => esc_html__( 'Saved Page', 'wts-eae' ),
                    'ae_template'   => esc_html__( 'AE Template', 'wts-eae' ),
                ],
                'default' => 'content',
            ]
        );

        $saved_container[''] = __( 'Select Container', 'wts-eae' );
        $saved_container     = $saved_container + Helper::select_elementor_page( 'container' );
        $repeater->add_control(
            'saved_container',
            [
                'label'     => __( 'Container', 'wts-eae' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => $saved_container,
                'condition' => [
                    'long_description_type' => 'saved_container',
                ],
            ]
        );
        
        $saved_sections[''] = __( 'Select Section', 'wts-eae' );
		$saved_sections     = $saved_sections + $this->select_elementor_page( 'section' );
		$repeater->add_control(
			'saved_sections',
			[
				'label'     => __( 'Select Section', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_sections,
				'condition' => [
					'long_description_type' => 'saved_section',
				],
			]
		);

        $saved_pages[''] = __( 'Select Page', 'wts-eae' );
		$saved_pages     = $saved_pages + $this->select_elementor_page( 'page' );
		$repeater->add_control(
			'saved_pages',
			[
				'label'     => __( 'Select Page', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_pages,
				'condition' => [
					'long_description_type' => 'saved_page',
				],
			]
		);

		$saved_ae_template[''] = __( 'Select AE Template', 'wts-eae' );
		$saved_ae_template     = $saved_ae_template + $this->select_ae_templates();
		$repeater->add_control(
			'saved_ae_template',
			[
				'label'     => __( 'Select AE Template', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_ae_template,
				'condition' => [
					'long_description_type' => 'ae_template',
				],
			]
		);

        $repeater->add_control(
            'content',
            [
                'label' => esc_html__('Content','wts-eae'),
                'type' => Controls_Manager::WYSIWYG,
                'dynamic'     => [
					'active' => true,
				],
				'default'     => "<h4>Neque porro quisquam est</h4><p>Maecenas aliquam ex sed mollis ullamcorper. Nullam aliquam justo orci, eu dictum diam convallis ut. Suspendisse ex quam, pretium sit amet ornare in, pretium ut felis. Fusce quis neque tortor. Curabitur ut eros at turpis faucibus vulputate a eu metus. Nullam suscipit eu quam quis tempus. Nunc a velit id eros pellentesque lobortis. Curabitur non libero in lacus venenatis volutpat. Sed at nisl interdum, faucibus metus et, faucibus turpis. Nulla euismod condimentum sem, id semper libero. Phasellus interdum dui non leo feugiat ornare. Nullam non urna nisl. Morbi iaculis nulla magna, id euismod libero pharetra vitae. Nunc massa risus, mattis quis volutpat nec, vulputate et ipsum. Aliquam vel diam sed felis gravida dictum.</p>",
				'condition'   => [
					'long_description_type' => 'content',
				],
            ]
        );

        $repeater->add_control(
            'default_active',
            [
                'label' => esc_html__('Active On Load','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $repeater->add_control(
            'enable_button',
            [
                'label' => esc_html__('Enable Button'),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
                'default' => 'yes'
            ]
        );

        $repeater->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Text'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Expand','wts-eae'),
                'condition' => [
                    'enable_button' => 'yes'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $repeater->add_control(
            'active_button_text',
            [
                'label' => esc_html__('Active Button Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__('Hide','wts-eae'),
                'condition' => [
                    'enable_button' => 'yes'
                ],
            ]
        );

        $repeater->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Icon', 'wts-eae' ),
				'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'default' => [
                    'value'   => 'fas fa-angle-down',
                    'library' => 'fa-solid'
                ],
                'condition' => [
                    'enable_button' => 'yes',
                ],
			]
		);

        $repeater->add_control(
            'button_active_icon',
            [
                'label' => esc_html__('Active Icon','wts-eae'),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'default' => [
                    'value'   => 'fas fa-angle-up',
                    'library' => 'fa-solid'
                ],
                'condition' => [
                    'enable_button' => 'yes',
                ],
            ]
        );

        $repeater->add_control(
			'icon_position',
			[
				'label' => esc_html__( 'Icon Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'before' => 'Before',
					'after' => 'After',
				],
				'default' => 'after',
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'enable_button',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'relation' => 'or',
			                'terms' => [
                                [
                                    'name' => 'button_icon[value]',
                                    'operator' => '!=',
                                    'value'=> '',
                                ],
                                [
                                    'name' => 'button_active_icon[value]',
                                    'operator' => '!=',
                                    'value'=> '',
                                ],
                            ]
                        ]
                    ]
                ],
			]
		);

        $this->add_control(
            'list_items', 
            [
                'label' => esc_html__('Info Item','wts-eae'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => esc_html__('List Item 1','wts-eae'),
                        'short_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae')
                    ],
                    [
                        'title' => esc_html__('List Item 2','wts-eae'),
                        'short_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae')
                    ],
                    [
                        'title' => esc_html__('List Item 3','wts-eae'),
                        'short_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae')
                    ]
                ],
                'title_field' => '{{{title}}}',
            ]
        );

        $this->add_control(
            'title_html_tag',
            [
                'label' => esc_html__('Title HTML Tag','wts-eae'),
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
            ]
        );

        $this->add_control(
            'description_close_icon',
            [
                'label' => esc_html__('Close Icon','wts-eae'),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'default' => [
                    'value'   => 'far fa-times-circle',
                    'library' => 'fa-solid'
                ], 
            ]
        );

        $this->add_control(
            'description_trigger_on',
            [
                'label' => esc_html__('Trigger On','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'box' => esc_html__('Box','wts-eae'),
                    'button' => esc_html__('Button','wts-eae'),
                    'both' => esc_html__('Both','wts-eae'),
                ],
                'default' => 'box',
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();
    }

    public function get_button_style_section(){
        
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => esc_html__('Button','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_align',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-justify-start-h',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-justify-center-h',
                    ],
                    'end' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h',
                    ],
                ],
                'selectors_dictionary' => [
                    'left' => 'start',
                    'center' => 'center',
                    'right' => 'end',
                ],
                'default' => 'left',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link' => 'align-self:{{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'button_spacing',
            [
                'label' => esc_html__('Spacing','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '10'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link' => 'margin-top: {{SIZE}}px;',
                ]
            ]
        );

        $this->add_responsive_control(
            'button_icon_gap',
            [
                'label' => esc_html__('Icon gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link' => 'column-gap:{{SIZE}}{{UNIT}};',
                ] 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_text_typography',
                'selector' => '{{WRAPPER}} .eae-ig-link',
            ]
        );

        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Color','wts'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eae-ig-link .eae-ig-button-icon svg' => 'fill:{{VALUE}};',
                    '{{WRAPPER}} .eae-ig-link .eae-ig-active-button-icon svg' => 'fill:{{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
					],
				],
                'selector' => '{{WRAPPER}} .eae-ig-link',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .eae-ig-link',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-link',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => esc_html__('Hover/Active','wts-eae'),
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => esc_html__('Color','wts'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-link' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eae-ig-link:hover .eae-ig-button-icon svg' => 'fill:{{VALUE}};',
                    '{{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-active-button-icon svg' => 'fill:{{VALUE}};',
                    '{{WRAPPER}} .eae-ig-link:hover .eae-ig-active-button-icon svg' => 'fill:{{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_hover_background',
                'selector' => '{{WRAPPER}} .eae-ig-link:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-link',
            ]
        );

        $this->add_control(
            'button_border_hover_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-link' => 'border-color:{{VALUE}};',
                ] 
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .eae-ig-link:hover, {{WRAPPER}} .eae-ig-item-wrapper.eae-ig-active-item .eae-ig-link',
            ]
        );        

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'button_border_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ig-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_media($item,$index){

        $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-media-container');

        if($item['media_type'] == 'image'){

            $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-media-type-image');
            
        }else if($item['media_type'] == 'lottie'){

            $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-media-type-lottie');
            $lottie_data = [
                'loop'    => ( $item['lottie_animation_loop'] === 'yes' ) ? true : false,
                'reverse' => ( $item['lottie_animation_reverse'] === 'yes' ) ? true : false,
            ];

            if($item['lottie_source'] == 'media_file' && !empty($item['lottie_source_json']['url'])){
                $lottie_data['url'] = $item['lottie_source_json']['url'];
            }else{
                $lottie_data['url'] = $item['lottie_animation_url'];
            }
            $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-lottie');
            $this->add_render_attribute('ig-media-container-'.$index,'data-lottie-settings',wp_json_encode($lottie_data));
            $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-media-view-'.$item['media_view']);

        }else if($item['media_type'] == 'icon'){

            $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-icon');

        }
        if($item['media_type'] != 'image'){
            $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-media-view-type-'.$item['media_view']);
            if($item['media_view'] != 'default'){
                $this->add_render_attribute('ig-media-container-'.$index,'class','eae-ig-icon-shape-'.$item['media_shap']);
            }
        }
        switch($item['media_type']){
            case 'icon':
                if($item['icon']['value'] !== ''){
                    ?><div <?php echo $this->get_render_attribute_string('ig-media-container-'.$index) ?>><?php Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?></div><?php
                }
                break;
            case 'image':
                if($item['image']['url'] !== ''){
                    ?><div <?php echo $this->get_render_attribute_string('ig-media-container-'.$index) ?>><?php echo Group_Control_Image_Size::get_attachment_image_html( $item, 'image' ); ?></div><?php
                }
                break;
            case 'lottie':
                if($item['lottie_source'] == 'media_file'){
                    if($item['lottie_source_json']['url'] !== ''){
                        ?><div <?php echo $this->get_render_attribute_string('ig-media-container-'.$index) ?>></div><?php
                    }
                }else{
                    if($item['lottie_animation_url'] !== ''){
                        ?><div <?php echo $this->get_render_attribute_string('ig-media-container-'.$index) ?>></div><?php
                    }
                }
                break;
        }
    }

    public function render(){
        $settings = $this->get_settings_for_display();
        $flag = 0;
        $this->add_render_attribute('title','class','eae-ig-title');
        ?>
            <div class="eae-ig-wrapper">
            <?php 
                foreach($settings['list_items'] as $index => $item){

                    $this->add_render_attribute('ig-item-wrapper-'.$index,'class','eae-ig-item-wrapper');

                    $this->add_render_attribute('ig-link-'.$index,'class','eae-ig-link');
                    
                    if ( ! empty( $settings['media_hover_animation'] ) ) {
                        $this->add_render_attribute( 'ig-media-container-'.$index, 'class', 'elementor-animation-' . $settings['media_hover_animation'] );
                    }

                    if($item['enable_button'] == 'yes'){
                        if($item['button_icon']['value'] !== ''){
                            $this->add_render_attribute('ig-link-'.$index,'class','eae-ig-link-icon-pos-'.$item['icon_position']);
                        }
                    }

                    if($item['default_active'] == 'yes' && $flag == 0){
                        $this->add_render_attribute('ig-item-wrapper-'.$index,'class','eae-ig-active-item');
                        $flag++;
                    }
                    ?>
                         <div <?php echo $this->get_render_attribute_string('ig-item-wrapper-'.$index); ?>>

                            <?php if($item['media_type'] !== 'none'){ ?> 
                                <?php
                                    $this->get_media($item,$index);  
                                ?>
                            <?php } ?>
                            <?php if($item['title'] !== '' || $item['short_description'] !== ''|| $item['enable_button'] == 'yes'){ ?>
                                <div class="eae-ig-content-container">
                                    <?php if($item['title'] !== ''){ 
                                        $title = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['title_html_tag'] ), $this->get_render_attribute_string( 'title' ), $item['title']); 
                                        echo $title;
                                    } 
                                    if($item['sub_title'] !== ''){
                                        ?><span class="eae-ig-sub-title"><?php echo Helper::eae_wp_kses($item['sub_title']); ?></span><?php
                                    }
                                    if($item['short_description'] !== ''){ ?>
                                        <span class="eae-ig-short-description"><?php echo Helper::eae_wp_kses($item['short_description']) ?></span>
                                    <?php }

                                    if($item['enable_button'] == 'yes'){
                                        if($item['button_text'] !== '' || $item['button_icon']['value'] != '' || $item['button_active_icon']['value'] != ''){
                                            ?><a <?php echo $this->get_render_attribute_string('ig-link-'. $index); ?> >
                                                <?php
                                                    $this->add_render_attribute('eae-ig-button-' . $index,'class','eae-ig-button-text');
                                                    if($item['active_button_text'] != ''){
                                                        ?><span class="eae-ig-active-button-text"><?php echo Helper::eae_wp_kses($item['active_button_text']); ?></span><?php
                                                    }else{
                                                        $this->add_render_attribute('eae-ig-button-' . $index,'class','eae-ig-active-button');
                                                    }
                                                    if($item['button_text'] != ''){
                                                        ?><span <?php echo $this->get_render_attribute_string('eae-ig-button-' . $index) ?>><?php echo Helper::eae_wp_kses($item['button_text']); ?></span><?php
                                                    }
                                                    
                                                    if($item['button_icon']['value'] != ''){
                                                        ?><span class="eae-ig-button-icon"><?php Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true' ] ); ?></span><?php 
                                                    } 
                                                    if($item['button_active_icon']['value'] != ''){
                                                        ?><span class="eae-ig-active-button-icon"><?php Icons_Manager::render_icon( $item['button_active_icon'], [ 'aria-hidden' => 'true' ] ); ?></span><?php
                                                    }
                                                ?>
                                            </a><?php
                                        }
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if( $item['content'] != '' || $item['saved_sections'] != '' || $item['saved_pages'] != '' || $item['saved_ae_template'] != '' || $item['saved_container'] != '' ){ ?>
                            <div class="eae-ig-item-info-wrapper">
                                <div class="eae-ig-close-button">
                                    <?php Icons_Manager::render_icon( $settings['description_close_icon'], [ 'aria-hidden' => 'true' ]); ?>
                                </div>
                                <?php
                                    switch ($item['long_description_type']) {
                                        case 'saved_section':
                                            echo EPlugin::instance()->frontend->get_builder_content( $item['saved_sections'] );
                                            break;
                                        case 'saved_page':
                                            echo EPlugin::instance()->frontend->get_builder_content( $item['saved_pages'] );
                                            break;
                                        case 'saved_container':
                                            echo EPlugin::instance()->frontend->get_builder_content( $item['saved_container'] );
                                            break;
                                        case 'ae_template':
                                            echo EPlugin::instance()->frontend->get_builder_content( $item['saved_ae_template'] );
                                            break;
                                        case 'content':
                                            echo do_shortcode($item['content']);
                                            break;
                                    }
                                ?>
                            </div>
                        <?php } ?>
                    <?php
                }
            ?>   
            </div>
        <?php
    }
    
     /**
        * Render icon list widget output in the editor.
        *
        * Written as a Backbone JavaScript template and used to generate the live preview.
        *
        * @since 2.9.0
        * @access protected
        */
        protected function testing_content_template() {
            ?>
                <#
                    function get_media(item, index){

                        view.addRenderAttribute('ig-media-container-' + index,'class','eae-ig-media-container');

                        if(item.media_type == 'image'){
                            view.addRenderAttribute('ig-media-container-' + index,'class','eae-ig-media-type-image');
                        }else if(item.media_type == 'lottie'){
                            
                            view.addRenderAttribute('ig-media-container-' + index,'class','eae-ig-media-type-lottie');
                            let lottie_data = {};
                            lottie_data.loop = ( item.lottie_animation_loop == 'yes' ) ? true : false;
                            lottie_data.reverse = ( item.lottie_animation_reverse == 'yes') ? true : false;
                            

                            if(item.lottie_source == 'media_file' && item.lottie_source_json.url !== ''){
                                lottie_data.url = item.lottie_source_json.url;
                            }else{
                                lottie_data.url = item.lottie_animation_url;
                            }
                            view.addRenderAttribute('ig-media-container-' + index,'class','eae-ig-lottie');
                            view.addRenderAttribute('ig-media-container-' + index,'data-lottie-settings',JSON.stringify(lottie_data));
                            view.addRenderAttribute('ig-media-container-' + index,'class','eae-ig-media-view-' + item.media_view);
                        }else if(item.media_type == 'icon'){
                            view.addRenderAttribute('ig-media-container-' + index,'class','eae-ig-icon');
                        }
                        if(item.media_type != 'image'){
                            view.addRenderAttribute('ig-media-container-' + index, 'class', 'eae-ig-media-view-type-' + item.media_view);
                            if(item.media_view != 'default'){
                                view.addRenderAttribute('ig-media-container-' + index, 'class', 'eae-ig-icon-shape-' + item.media_shap );
                            }
                        }

                        switch(item.media_type){
                            case 'icon':
                                if(item.icon.value !== ''){
                                    let icon_html = elementor.helpers.renderIcon(view, item.icon , { 'aria-hidden': true }, 'i' , 'object' );
                                    #>
                                        <div {{{ view.getRenderAttributeString('ig-media-container-' + index) }}}>
                                            {{{icon_html.value}}}
                                        </div>
                                    <#
                                }
                                break;
                            case 'image':
                                if(item.image.url !== ''){
                                    let image = {
                                        id: item.image.id,
                                        url: item.image.url,
                                        size: item.image_size,
                                        dimension: item.image_custom_dimension,
                                        model: view.getEditModel()
                                    };

                                    let image_url = elementor.imagesManager.getImageUrl( image );
                                    image_html = '<img src="' + image_url + '" class="elementor-animation-' + item.hover_animation + '" />';
                                    #>
                                        <div {{{ view.getRenderAttributeString('ig-media-container-' + index) }}}>
                                            {{{image_html}}}
                                        </div>
                                    <#
                                } 
                                break;
                            case 'lottie':
                                if(item.lottie_source == 'media_file' ){
                                    if(item.lottie_source_json.url !== ''){
                                        #>
                                        <div {{{ view.getRenderAttributeString('ig-media-container-' + index) }}} ></div>
                                        <#
                                    }
                                }else{
                                    if(item.lottie_animation_url !== ''){
                                        #>
                                            <div {{{ view.getRenderAttributeString('ig-media-container-' + index) }}} ></div>
                                        <#
                                    }
                                }
                                break;
                        }

                    }

                    let flag = 0;
                    view.addRenderAttribute('title','class','eae-ig-title');
                #>
                <div class="eae-ig-wrapper">
                <#
                _.each( settings.list_items,function ( item, index ){
                    view.addRenderAttribute('ig-item-wrapper-' + index, 'class','eae-ig-item-wrapper');
                    view.addRenderAttribute('ig-link-' + index,'class','eae-ig-link');

                    if(settings.media_hover_animation != ''){
                        view.addRenderAttribute('ig-media-container-' + index, 'class', 'elementor-animation-' . settings.media_hover_animation );
                    }

                    if(item.enable_button == 'yes'){
                        if(item.button_icon.value !== ''){
                            view.addRenderAttribute('ig-link-' + index, 'class', 'eae-ig-link-icon-pos-' + item.icon_position);
                        }
                    }

                    if(item.default_active == 'yes' && flag == 0){
                        view.addRenderAttribute('ig-item-wrapper-' + index, 'class', 'eae-ig-active-item');
                    }
                    #>
                        <div {{{ view.getRenderAttributeString('ig-item-wrapper-' + index) }}}>
                            <#
                                if(item.media_type !== 'none'){
                                    get_media(item,index);
                                }

                                if(item.title !== '' || item.short_description !== '' || item.enable_button == 'yes'){
                                    #>
                                        <div class="eae-ig-content-container">
                                            <#
                                                if(item.title !== ''){
                                                    let title_tag = validateHTMLTag(settings.title_html_tag, null, 'h2');
                                                    let title_html =  '<'+ title_tag +' '+view.getRenderAttributeString('title')+'>'+item.title+'</'+ titleTag +'>';
                                                    print( title_html );
                                                }

                                                if(item.sub_title !== ''){
                                                    #>
                                                        <span class="eae-ig-sub-title">{{{ item.sub_title }}}</span>
                                                    <#
                                                }

                                                if(item.short_description !== ''){
                                                    #>
                                                        <span class="eae-ig-short-description"> {{{ item.short_description }}} </span>
                                                    <#
                                                }

                                                if(item.enable_button == 'yes'){
                                                    if(item.button_text !== '' || item.button_icon.value != '' || item.button_active_icon.value != ''){
                                                        #>
                                                            <a {{{ view.getRenderAttributeString('ig-link-' + index) }}}>
                                                                <# 
                                                                    view.addRenderAttribute('eae-ig-button-' + index,'class','eae-ig-button-text');

                                                                    if(item.active_button_text != ''){
                                                                        #>
                                                                            <span class="eae-ig-active-button-text"> {{{ item.active_button_text}}} </span>
                                                                        <#
                                                                    }else{
                                                                        view.addRenderAttribute('eae-ig-button-' + index,'class','eae-ig-active-button');
                                                                    }
                                                                    if(item.button_text != ''){
                                                                        #>
                                                                            <span {{{ view.getRenderAttributeString('eae-ig-button-' + index) }}}>
                                                                                {{{ item.button_text }}}
                                                                            </span>
                                                                        <#
                                                                    }

                                                                    if(item.button_icon.value != ''){
                                                                        let icon_html = elementor.helpers.renderIcon(view, item.button_icon , { 'aria-hidden': true }, 'i' , 'object' );
                                                                        #>
                                                                            <span class="eae-ig-button-icon" >
                                                                                {{{ icon_html.value }}}
                                                                            </span>
                                                                        <#
                                                                    }

                                                                    if(item.button_active_icon.value != ''){
                                                                        let active_icon_html = elementor.helpers.renderIcon(view, item.button_active_icon, { 'aria-hidden': true }, 'i', 'object');
                                                                        #>
                                                                            <span class="eae-ig-active-button-icon">
                                                                                {{{ active_icon_html.value }}}
                                                                            </span>
                                                                        <#
                                                                    }
                                                                #>
                                                            </a>
                                                        <#
                                                    }
                                                }
                                            #>
                                        </div>
                                    <#
                                }
                            #>
                        </div>
                    <#
                        if(item.content != '' || item.saved_sections != '' || item.saved_pages != '' || item.saved_ae_template != '' || item.saved_container != ''){
                            #>
                                <div class="eae-ig-item-info-wrapper">
                                    <div class="eae-ig-close-button">
                                        <# 
                                            let close_icon_html = elementor.helpers.renderIcon(view, settings.description_close_icon, { 'aria-hidden': true }, 'i', 'object');
                                        #>
                                        {{{ close_icon_html }}}
                                    </div>
                                    <#
                                        
                                    #>
                                </div>
                            <#
                        }
                });
                #>
            </div>
                
            <?php
        }
}
?>
