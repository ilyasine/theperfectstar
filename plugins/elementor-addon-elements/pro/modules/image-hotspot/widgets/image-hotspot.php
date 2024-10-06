<?php 

namespace WTS_EAE\Pro\Modules\ImageHotspot\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use WTS_EAE\Classes\Helper;
use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Base;
use Elementor\Group_Control_Text_Shadow;

if( ! defined('ABSPATH')){
    exit;
}
// wp_enqueue_script( 'tippy' );

class ImageHotspot extends EAE_Widget_Base{
    
    public function get_name(){
        return 'eae-image-hotspot';
    }

    public function get_title(){
        return __('Image Hotspot','wts-eae');
    }

    public function get_script_depends() {
        return [ 'eae-lottie', 'eae-popper', 'eae-tippy'];
	}
    
    public function get_style_depends()
    {
        return ['eae-tippy-css'];
    }

    public function get_icon() {
		return 'eae-icon eae-image-hotspot';
	}

    public function get_categories(){
        return ['wts-eae'];
    }

    protected function register_controls(){

        $this->get_general_section();

        $this->get_hotspot_tour_section();

        $this->get_marker_style_section();

        $this->get_tooltip_style_section();

        $this->get_hotspot_tour_style_section();

        $this->get_close_button_style_section();

    }

    public function get_close_button_style_section(){
        $this->start_controls_section(
            'close_button_style_section',
            [
                'label' => esc_html__('Close Button','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'enable_close_button' => 'yes',
                ] 
            ]
        );

        $this->add_responsive_control(
            'horizontal_position',
            [
                'label' => esc_html__('Horizontal Position','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-close-icon' => 'right: {{SIZE}}{{UNIT}};' 
                ]
            ]
        );

        $this->add_responsive_control(
            'vertical_position',
            [
                'label' => esc_html__('Vertical Position','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-close-icon' => 'top: {{SIZE}}{{UNIT}};' 
                ]
            ]
        );

        $this->add_responsive_control(
            'close_icon_size',
            [
                'label' => esc_html__('Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-close-icon' => 'font-size:{{SIZE}}{{UNIT}}',
                ]
            ]
        );

        $this->add_control(
            'close_icon_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-close-icon i' => 'color:{{VALUE}}',
                    '{{WRAPPER}} .eae-ih-tooltip-close-icon svg' => 'fill:{{VALUE}}',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_general_section(){
        $this->start_controls_section(
            'general_section',
            [
                'label' => esc_html__('General','wts-eae'),
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => esc_html__('Image','wts-eae'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image',
                'default' => 'full',
            ]
        );

        $repeater = new Repeater();

        $repeater->start_controls_tabs('marker_tabs');

        $repeater->start_controls_tab(
            'marker_content_tab',
            [
                'label' => esc_html__('Content','wts-eae'), 
            ]
        );

        $repeater->add_control(
            'admin_label',
            [
                'label' => esc_html__('Admin Label','wts-eae'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        Helper::eae_media_controls(
            $repeater,
            [
                'name' => 'ih',
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
                'defaults'      => [
                    'graphic_type_default' => 'icon',
                    'graphic_icon_default' => [
                        'value' => 'fas fa-star',
                        'library' => 'fa-solid'
                    ],
                    'view_default' => 'stacked',
                ]
            ]
        );

        $repeater->add_control(
            'heading',
            [
                'label' => esc_html__('Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'dynamic'     => [
					'active' => true,
				],
            ]
        );

        $repeater->add_responsive_control(
            'horizontal_position',
            [
                'label' => esc_html__('Horizontal Position','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-marker' => 'left: {{SIZE}}{{UNIT}};' 
                ]
            ]
        );

        $repeater->add_responsive_control(
            'vertical_position',
            [
                'label' => esc_html__('Vertical Position','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-marker' => 'top: {{SIZE}}{{UNIT}};' 
                ]
            ]
        );

        $repeater->add_control(
            'tooltip_style_heading',
            [
                'label' => esc_html__('Tooltip','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'tooltip_preview',
            [
                'label' => esc_html__('Preview','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        Helper::eae_media_controls(
            $repeater,
            [
                'name' => 'tooltip_icon',
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> false,
            ]
        );

        $repeater->add_control(
            'tooltip_heading',
            [
                'label' => esc_html__('Heading','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Heading','wts-eae'),
                'label_block' => true,
                'dynamic'     => [
					'active' => true,
				],
                'separator' => 'before'
            ]
        );

        $repeater->add_control(
            'tooltip_short_description',
            [
                'label' => esc_html__('Short Description','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic'     => [
					'active' => true,
				],
            ]
        );

        $repeater->add_control(
            'tooltip_description',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::WYSIWYG,
            ]
        );

        $repeater->add_control(
            'enable_button',
            [
                'label' => esc_html__('Enable Button','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'tooltip_button_text',
            [
                'label' => esc_html__('Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Click Here','wts-eae'),
                'dynamic'     => [
					'active' => true,
				],
                'condition' => [
                    'enable_button' => 'yes',
                ]
            ]
        );

        $repeater->add_control(
            'tooltip_button_link',
            [
                'label' => esc_html__('Link','wts-eae'),
                'type' => Controls_Manager::URL,
                'dynamic'     => [
					'active' => true,
				],
                'condition' => [
                    'enable_button' => 'yes',
                ]
            ]
        );

        $repeater->add_control(
            'tooltip_button_icon',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'label_block' => false,
                'condition' => [
                    'enable_button' => 'yes',
                ] 
            ]
        );

        $repeater->add_control(
            'tooltip_button_icon_position',
            [
                'label' => esc_html__('Icon Position','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'skin' => 'inline',
                'options' => [
                    'before' => esc_html__('Before','wts-eae'),
                    'after' => esc_html__('After','wts-eae'),
                ],
                'default' => 'after',
                'condition' => [
                    'enable_button' => 'yes',
                ]
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'marker_style_tab',
            [
                'label' => esc_html__('Style','wts-eae'),
            ]
        );

        $repeater->add_control(
            'marker_style_heading',
            [
                'label' => esc_html__('Marker','wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $repeater->add_control(
            'marker_text_color',
            [
                'label' => esc_html__('Text Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-marker-text' => 'color:{{VALUE}};'
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'marker_background',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-marker',
            ]
        );

        $repeater->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'marker_box_border',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-marker',
            ]
        );

        $repeater->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'marker_box_shadow',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-marker',
            ]
        );

        $repeater->add_control(
            'tooltip_style',
            [
                'label' => esc_html__('Tooltip','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'tooltip_background',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .tippy-content'
            ]
        );

        $repeater->add_control(
            'tooltip_arrow_color',
            [
                'label' => esc_html__('Arrow Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .tippy-arrow' => 'color:{{VALUE}};',
                ]
            ]
        );

        $repeater->add_control(
            'tooltip_content_background_style_heading',
            [
                'label' => esc_html__('Content Background','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'label' => esc_html__('Tooltip Content Background','wts-eae'),
                'name' => 'tooltip_content_background',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-content-container'
            ]
        );

        $repeater->add_control(
            'tooltip_individual_style_heading',
            [
                'label' => esc_html__('Heading','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'tooltip_text_color',
            [
                'label' => esc_html__('Text Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-heading' => 'color:{{VALUE}};',
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'tooltip_heading_background',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-heading'
            ]
        );

        $repeater->add_control(
            'tooltip_individual_short_description_style',
            [
                'label' => esc_html__('Short Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'short_description_text_color',
            [
                'label' => esc_html__('Text Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-short-description' => 'color:{{VALUE}};'
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'short_description_typography',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-short-description',
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'short_description_background',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-short-description'
            ]
        );

        $repeater->add_control(
            'tooltip_individual_description_style',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'tooltip_description_color',
            [
                'label' => esc_html__('Text Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-description' => 'color:{{VALUE}};'
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'description_individual_background',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-ih-tooltip-description'
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'markers', 
            [
                'label' => esc_html__('Markers','wts-eae'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'tooltip_heading' => esc_html__('Add Your Tooltip Text Here','wts-eae'),
                        'horizontal_position' => [
                            'size' => '50',
                            'unit' => '%', 
                        ],
                        'vertical_position' => [
                            'size' => '50',
                            'unit' => '%',
                        ]
                    ],
                    [
                        'tooltip_heading' => esc_html__('Add Your Tooltip Text Here','wts-eae'),
                        'horizontal_position' => [
                            'size' => '20',
                            'unit' => '%', 
                        ],
                        'vertical_position' => [
                            'size' => '40',
                            'unit' => '%',
                        ]
                    ],
                ],
                'title_field' => '{{{admin_label}}}',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'trigger',
            [
                'label' => esc_html__('Trigger','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'mouseenter' => esc_html__('Hover','wts-eae'),
                    'click' => esc_html__('Click','wts-eae'),
                ],
                'default' => 'click',
                'frontend_available' => true
            ]
        );

        $this->add_control(
            'enable_close_button',
            [
                'label' => esc_html__('Enable Close Button','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'close_button_icon',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'default' => [
					'value' => 'fas fa-times',
					'library' => 'fa-solid',
				],
                'label_block' => false,
                'condition' => [
                    'enable_close_button' => 'yes',
                ] 
            ]
        );

        $this->add_control(
            'enable_hotspot_tour',
            [
                'label' => esc_html__('Enable Hotspot Tour','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    public function get_hotspot_tour_section(){
        $this->start_controls_section(
            'hotspot_tour_section',
            [
                'label' => esc_html__('Hotspot Tour','wts-eae'),
                'condition' => [
                    'enable_hotspot_tour' => 'yes',
                ]
            ]
        );

        $this->add_control(
            'previous_button_heading',
            [
                'label' => esc_html__('Previous Tour','wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'previous_button_text',
            [
                'label' => esc_html__('Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('« Previous','wts-eae'),
                'dynamic'     => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'previous_button_icon',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
            ]
        );

        $this->add_control(
            'next_button_heading',
            [
                'label' => esc_html__('Next Tour','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'next_button_text',
            [
                'label' => esc_html__('Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Next »','wts-eae'),
                'skin' => 'inline',
                'dynamic'     => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'next_button_icon',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::ICONS,
                'label_block' => false,
                'skin' => 'inline',
            ]
        );

        $this->add_control(
            'end_tour_button_heading',
            [
                'label' => esc_html__('End Tour','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'end_tour_text',
            [
                'label' => esc_html__('Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('End Tour','wts-eae'),
                'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'enable_count',
            [
                'label' => esc_html__('Enable Count','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

    public function get_tooltip_style_section(){
        $this->start_controls_section(
            'tooltip_style_section',
            [
                'label' => esc_html__('Tooltip','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'preview_tooltip',
            [
                'label' => esc_html__('Preview Tooltip','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'tooltip_background',
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
                'selector' => '{{WRAPPER}} .tippy-content'  
            ]
        );

        $this->add_control(
            'tooltip_arrow_color',
            [
                'label' => esc_html__('Arrow Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tippy-arrow' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'tooltip_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => 250,
                    'unit' => 'px'
                ],
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
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-add-tooltip' => 'width:{{SIZE}}{{UNIT}};',
                ] 
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'tooltip_content_box_shadow',
                'selector' => '{{WRAPPER}} .tippy-content',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'tooltip_content_border',
                'selector' => '{{WRAPPER}} .tippy-content',
            ]
        );

        $this->add_responsive_control(
            'tooltip_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .tippy-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'tooltip_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .tippy-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'icon_style_control',
            [
                'label' => esc_html__('Image/Icon ','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'icon_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => ' eicon-h-align-left',
                    ],
                    'top' => [
                        'title' => esc_html__('Top','wts-eae'),
                        'icon' => 'eicon-v-align-top'
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => ' eicon-h-align-right',
                    ]
                ],
                'selectors_dictionary' => [
                    'left' => 'row',
                    'top' => 'column',
                    'right' => 'row-reverse'
                ],
                'default' => 'top',
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-container' => 'flex-direction: {{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'icon_aling_column',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__('Start','wts-eae'),
                        'icon' => 'eicon-justify-start-v',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-justify-center-v',
                    ],
                    'end' => [
                        'title' => esc_html__('End','wts-eae'),
                        'icon' => 'eicon-justify-end-v',
                    ]
                ],
                'toggle' => false,
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-container' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tooltip_icon_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-container' => 'gap:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'tooltip_icon',
                'selector'      => '.eae-ih-tooltip-icon',
                'hover_selector'      => '.eae-ih-tooltip-icon:hover',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
                'default' => [
                    'primary_color' => Global_Colors::COLOR_SECONDARY
                ]
            ]
        );

        $this->add_control(
            'content_style_heading',
            [
                'label' => esc_html__('Content','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'content_align',
            [
                'label'       => esc_html__( 'Text Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
                'toggle' => false,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-content-container' => 'text-align:{{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-content-container',
            ]
        );

        $this->add_responsive_control(
            'content_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-content-container' => 'gap:{{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'default' => [
                    'top' => 20,
                    'left' => 20,
                    'right' => 20,
                    'bottom' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'tooltip_heading_style',
            [
                'label' => esc_html__('Heading','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tooltip_heading_typography',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-heading'
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-heading' => 'color:{{VALUE}};'
                ] 
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'heading_background',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-heading',
            ]
        );

        $this->add_control(
            'tooltip_short_description',
            [
                'label' => esc_html__('Short Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'short_description_typography',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-short-description'
            ]
        );

        $this->add_control(
            'short_description_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-short-description' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'short_description_background',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-short-description',
            ]
        );

        $this->add_control(
            'tooltip_description_style_heading',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-description' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'description_background',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-description',
            ]
        );

        $this->add_control(
            'tooltip_button',
            [
                'label' => esc_html__('Button','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'tooltip_button_position',
            [
                'label'       => esc_html__( 'Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
                'toggle' => false,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  =>  'eicon-justify-start-h',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-justify-center-h',
					],
					'right'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-justify-end-h',
					],
				],
                'selectors_dictionary' => [
                    'left' => 'start',
                    'center' => 'center',
                    'right' => 'end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-button' => 'align-self:{{VALUE}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'button_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-button' => 'gap: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'tooltip_button_text_color',
            [
                'label' => esc_html__('Text Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-button' => 'color: {{VALUE}};'
                ] 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-button'
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
							'default' => Global_Colors::COLOR_SECONDARY,
						],
					],
				],
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-button'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'tooltip_button_border',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-button' 
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'default' => [
                    'top' => 8,
                    'right' => 12,
                    'bottom' => 8,
                    'left' => 12,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'button_icon_style_heading',
            [
                'label' => esc_html__('Button Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'button_icon_size',
            [
                'label' => esc_html__('Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-button i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-ih-tooltip-button svg' => 'font-size: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'button_icon_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-button i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eae-ih-tooltip-button svg' => 'fill: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'tooltip_animation_control_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'tooltip_animation_heading',
            [
                'label' => esc_html__('Animation','wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'tooltip_animation_type',
            [
                'label' => esc_html__('Animation','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => esc_html__('None','wts-eae'),
                    'bounce' => esc_html__('Bounce','wts-eae'),
                    'flash' => esc_html__('Flash','wts-eae'),
                    'pulse' => esc_html__('Pulse','wts-eae'),
                    'rubberBand' => esc_html__('Rubber Band','wts-eae'),
                    'shake' => esc_html__('Shake','wts-eae'),
                    'headShake' => esc_html__( 'Head Shake','wts-eae'),
                    'swing' => esc_html__( 'Swing', 'wts-eae'),
                    'tada' => esc_html__('Tada','wts-eae'),
                    'wobble' => esc_html__('Wobble','wts-eae'),
                    'jello' => esc_html__('Jello','wts-eae'),
                ],
                'default' => 'none',
                'frontend_available' => true,
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'tooltip_animation_duration',
            [
                'label' => esc_html__('Duration (s)','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-animtion' => 'animation-duration:{{VALUE}}s;'
                ],
                'condition' => [
                    'tooltip_animation_type!' => 'none',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_hotspot_tour_style_section(){
        
        $this->start_controls_section(
            'hotspot_tour_style_section',
            [
                'label' => esc_html__('Hotspot Tour','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'enable_hotspot_tour' => 'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'hotspot_tour_background',
                'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_SECONDARY,
						],
					],
				],
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-tour',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hotspot_tour_box_shadow',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-tour',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'hotspot_tour_border',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-tour',
            ]
        );

        $this->add_responsive_control(
            'hotspot_tour_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-tour' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'hotspot_tour_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'default' => [
                    'top' => 10,
                    'left' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    "{{WRAPPER}} .eae-ih-tooltip-tour" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'hotspot_tour_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    "{{WRAPPER}} .eae-ih-tooltip-tour" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'count_style_heading',
            [
                'label' => esc_html__('Count','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
            'count_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-navigation-container' => 'column-gap: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'count_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-count' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'selector' => '{{WRAPPER}} .eae-ih-count',
            ]
        );

        $this->add_control(
            'next_button_style_heading',
            [
                'label' => esc_html__('Next Tour','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'next_button_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-next' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-ih-tooltip-next svg' => 'fill: {{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'next_button_typography',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-next',
            ]
        );

        $this->add_responsive_control(
            'hotspot_tour_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'unit' => 'px',
                    'size' => '12', 
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-pre-nxt-btn' => 'column-gap: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'hotspot_tour_alignment',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => true,
                'default' => 'space-between',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left'),
                        'icon' => 'eicon-justify-start-h',
                    ],
                    'center' => [
                        'title' => esc_html__('center'),
                        'icon' => 'eicon-justify-center-h',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h',
                    ],
                    'space-around' => [
                        'title' => esc_html__('Space Around','wts-eae'),
                        'icon' => 'eicon-justify-space-around-h',
                    ],
                    'space-between' => [
                        'title' => esc_html__('Space Between','wts-eae'),
                        'icon' => 'eicon-justify-space-between-h',
                    ],
                    'space-evenly' => [
                        'title' => esc_html__('Space Evenly','wts-eae'),
                        'icon' => 'eicon-justify-space-evenly-h',
                    ]
                ],
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-navigation-container' => 'justify-content:{{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'next_button_border',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-next',
            ]
        );

        $this->add_responsive_control(
            'next_button_border_radius:',
            [
                'label' =>esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'next_button_padding',
            [
                'label' =>esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'previous_button_style_heading',
            [
                'label' => esc_html__('Previous Tour','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'previous_button_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-prev' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eae-ih-tooltip-prev svg' => 'fill: {{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'previous_button_typography',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-prev',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'previous_button_background',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-prev',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'previous_button_border',
                'selector' => '{{WRAPPER}} .eae-ih-tooltip-prev',
            ]
        );

        $this->add_responsive_control(
            'previous_button_border_radius:',
            [
                'label' =>esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'previous_button_padding',
            [
                'label' =>esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'end_button_style_heading',
            [
                'label' => esc_html__('End Tour','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'end_button_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-end-tour-btn' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'end_button_typography',
                'selector' => '{{WRAPPER}} .eae-ih-end-tour-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'end_button_background',
                'selector' => '{{WRAPPER}} .eae-ih-end-tour-btn',
            ]
        );

        $this->add_control(
            'hotspot_tour_btn_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-tooltip-tour' => 'row-gap: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'end_tour_button_align',
            [
                'label'       => esc_html__( 'Text Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'right',
                'toggle' => false,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-justify-start-h',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-justify-center-h',
					],
					'right'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-justify-end-h',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-end-tour-btn-container' => 'justify-content:{{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'end_tour_button_border',
                'selector' => '{{WRAPPER}} .eae-ih-end-tour-btn',
            ]
        );

        $this->add_responsive_control(
            'end_tour_button_border_radius:',
            [
                'label' =>esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-end-tour-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'end_tour_button_padding',
            [
                'label' =>esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-end-tour-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_marker_style_section(){
        $this->start_controls_section(
            'marker_style_section',
            [
                'label' => esc_html__('Marker','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'text_gap',
            [
                'label' => esc_html__('Icon Gap','wts-eae'),
                'type' =>Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-marker' => 'column-gap: {{SIZE}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'marker_icon_position',
            [
                'label' => esc_html__('Icon Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-justify-start-h'
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h'
                    ],
                ],
                'selectors_dictionary' => [
                    'left' => 'row',
                    'right' => 'row-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-marker' => 'flex-direction: {{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'marker_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-marker-text' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'marker_text_typography',
                'label' => esc_html__('Text Typography','wts-eae'),
                'selector' => '{{WRAPPER}} .eae-ih-marker-text'
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'marker_text_shadow',
                'selector' => '{{WRAPPER}} .eae-ih-marker-text',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'marker_text_background',
                'selector' => '{{WRAPPER}} .eae-ih-marker',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'marker_box_shadow',
                'selector' => '{{WRAPPER}} .eae-ih-marker'
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'marker_border',
                'selector' => '{{WRAPPER}} .eae-ih-marker',
            ]
        );

        $this->add_responsive_control(
            'marker_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-marker' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_responsive_control(
            'marker_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-marker' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );

        $this->add_control(
            'marker_icon_controls_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'marker_icon_style',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'ih',
                'selector'      => ' .eae-ih-icon',
                'hover_selector'      => '.eae-ih-icon:hover',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
    
            ]
        );

        $this->add_control(
            'animation_control_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );

        $this->add_control(
            'marker_animation_heading',
            [
                'label' => esc_html__('Animation','wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'marker_animation_type',
            [
                'label' => esc_html__('Animation','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => esc_html__('None','wts-eae'),
                    'bounce' => esc_html__('Bounce','wts-eae'),
                    'flash' => esc_html__('Flash','wts-eae'),
                    'pulse' => esc_html__('Pulse','wts-eae'),
                    'rubberBand' => esc_html__('Rubber Band','wts-eae'),
                    'shake' => esc_html__('Shake','wts-eae'),
                    'headShake' => esc_html__( 'Head Shake','wts-eae'),
                    'swing' => esc_html__( 'Swing', 'wts-eae'),
                    'tada' => esc_html__('Tada','wts-eae'),
                    'wobble' => esc_html__('Wobble','wts-eae'),
                    'jello' => esc_html__('Jello','wts-eae'),
                ],
                'default' => 'none',
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'marker_continuous_animtion',
            [
                'label' => esc_html__('Continuous Animtion','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'marker_animation_type!' => 'none', 
                ]
                
            ]
        );

        $this->add_control(
            'marker_animation_duration',
            [
                'label' => esc_html__('Duration (s)','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'selectors' => [
                    '{{WRAPPER}} .eae-ih-marker-animtion' => 'animation-duration:{{VALUE}}s;'
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'marker_continuous_animtion',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'marker_animation_type',
                            'operator' => '!==',
                            'value'=>'none'
                        ],
                    ]
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function add_icon_class($icon_class, $data, $control_name){
        // die('dfaf');
        if(isset($data['ih_hover_animation']) && $data['ih_hover_animation'] != ''){
            $icon_class[] = 'elementor-animation-'.$data['ih_hover_animation'];
        }
        return $icon_class;
    }

    public function render(){
        $settings = $this->get_settings_for_display();
        $flag = 0;

        // echo '<pre>'; print_r( $settings); echo '</pre>';
        ?>
            <div class="eae-ih-wrapper">

                <?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'image' ); ?>
                
                <?php foreach($settings['markers'] as $index => $item){ ?>
                    <?php 
                        $this->add_render_attribute('eae-marker-container-'.$index,'class','elementor-repeater-item-'.$item['_id']); 
                        $tooltipId = $index + 1;

                        $this->add_render_attribute('eae-marker-' . $index,'class','eae-ih-marker');
                        if( isset($settings['marker_animation_type']) && $settings['marker_animation_type'] !== 'none'){
                            $this->add_render_attribute( 'eae-marker-' . $index, 'class', 'animated ' . esc_html( $settings['marker_animation_type'] ));
                            if( isset($settings['marker_continuous_animtion']) && $settings['marker_continuous_animtion'] == 'yes'){
                                $this->add_render_attribute( 'eae-marker-' . $index, 'class', 'eae-ih-marker-animtion');
                            }
                        }
                        
                        if($item['tooltip_preview'] == 'yes' && \Elementor\Plugin::$instance->editor->is_edit_mode() ){
                            $this->add_render_attribute('eae-marker-' . $index,'class','eae-ih-rep-tooltip-show');
                        }

                        if($settings['preview_tooltip'] == 'yes' && $flag == 0 && \Elementor\Plugin::$instance->editor->is_edit_mode()){
                            $this->add_render_attribute('eae-marker-' . $index,'class','eae-ih-tooltip-show');
                            $flag++;
                        }

                        if($item['enable_button'] == 'yes'){
                            $this->add_render_attribute('eae-button-' . $index,'class','eae-ih-tooltip-button');
                            if($item['tooltip_button_icon']['value'] !== ''){
                                $this->add_render_attribute('eae-button-' . $index,'class','eae-ih-icon-position-' . $item['tooltip_button_icon_position']);
                            }
                        }
                        if($item['enable_button'] == 'yes'){
                            $this->add_link_attributes('eae-button-' . $index, $item['tooltip_button_link']);
                        }
                    ?>
                    <div <?php echo $this->get_render_attribute_string('eae-marker-container-' . $index) ?>>
                        <div <?php echo $this->get_render_attribute_string('eae-marker-' . $index); ?> data-marker="<?php echo esc_attr($tooltipId); ?>" >
                            <?php 
                                if($item['ih_graphic_type'] !== 'none' ){
                                    // if($settings['ih_hover_animation'] != 'none'){
                                    //     add_filter('eae/eae-icon-class',[$this, 'add_icon_class'],10,3);
                                    // }
                                    unset($item['ih_hover_animation']);
                                    $item['ih_hover_animation'] = $settings['ih_hover_animation'];
                                    Helper::render_icon_html($item,$this, 'ih', 'eae-ih-icon');
                                }
                                if($item['heading'] !== ''){
                                    ?><div class="eae-ih-marker-text"><?php echo Helper::eae_wp_kses($item['heading']); ?></div><?php
                                }
                            ?>
                        </div>
                        <div class="eae-ih-tooltip" id="tooltip-<?php echo esc_attr($tooltipId); ?>">
                            <div class="eae-ih-tooltip-container">
                                <?php 
                                    if($item['tooltip_icon_graphic_type'] !== 'none' ){
                                        ?><span class="eae-ih-tooltip-icon-container"><?php 
                                            unset($item['tooltip_icon_hover_animation']);
                                            $item['tooltip_icon_hover_animation'] = $settings['tooltip_icon_hover_animation'];
                                            Helper::render_icon_html($item, $this, 'tooltip_icon','eae-ih-tooltip-icon'); 
                                        ?></span><?php
                                    }
                                ?>
                                <?php if($item['tooltip_heading'] != '' || $item['tooltip_short_description'] != '' || $item['tooltip_description'] !== '' || $item['enable_button'] !== ''){ ?>
                                    <div class="eae-ih-tooltip-content-container">
                                        <?php 
                                            if($item['tooltip_heading'] != ''){
                                                ?><span class="eae-ih-tooltip-heading"><?php echo Helper::eae_wp_kses($item['tooltip_heading']); ?></span><?php
                                            }
                                            if($item['tooltip_short_description'] !=''){
                                                ?><span class="eae-ih-tooltip-short-description"><?php echo Helper::eae_wp_kses($item['tooltip_short_description']); ?></span><?php
                                            }
                                            if($item['tooltip_description'] !== ''){
                                                ?><span class="eae-ih-tooltip-description"><?php echo Helper::eae_wp_kses($item['tooltip_description']); ?></span><?php
                                            }
                                            if($item['tooltip_button_text'] !== '' && $item['enable_button'] !== ''){
                                                ?>
                                                    <a <?php echo $this->get_render_attribute_string('eae-button-' . $index )?>>
                                                        <?php 
                                                            echo Helper::eae_wp_kses($item['tooltip_button_text']);
                                                            Icons_Manager::render_icon( $item['tooltip_button_icon'], [ 'aria-hidden' => 'true' ] );
                                                        ?>
                                                    </a>
                                                <?php
                                            }
                                            if($settings['enable_close_button'] == 'yes'){
                                                ?><a class="eae-ih-tooltip-close-icon" data-tooltip-id = "<?php echo esc_attr($tooltipId);?>">
                                                    <!-- <i class="fas fa-times"></i> close_button_icon -->
                                                    <?php 
                                                        Icons_Manager::render_icon( $settings['close_button_icon'], [ 'aria-hidden' => 'true' ] );
                                                    ?>
                                                </a><?php
                                            }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if($settings['enable_hotspot_tour'] == 'yes'){ ?>  
                                <div class="eae-ih-tooltip-tour">
                                    <div class="eae-ih-navigation-container">
                                        <?php if( $settings['enable_count'] == 'yes' ){ ?>
                                            <div class="eae-ih-tooltip-count-tour">
                                                <span class="eae-ih-count"><?php echo $tooltipId . ' of ' .count($settings['markers']);?></span>
                                            </div>
                                        <?php } ?>
                                        <div class="eae-ih-tooltip-pre-nxt-btn">
                                            <?php if($settings['previous_button_icon']['value'] != '' || $settings['previous_button_text'] !== ''){
                                                ?><a class="eae-ih-tooltip-prev" data-tooltip-id="<?php echo esc_attr($tooltipId);?>">
                                                    <?php 
                                                        Icons_Manager::render_icon( $settings['previous_button_icon'], [ 'aria-hidden' => 'true' ] ); 
                                                        echo Helper::eae_wp_kses($settings['previous_button_text']); 
                                                    ?>
                                                </a><?php
                                            } 
                                            
                                            if($settings['next_button_icon']['value'] != '' || $settings['next_button_text'] !== '') {
                                                ?><a class="eae-ih-tooltip-next" data-tooltip-id="<?php echo esc_attr($tooltipId);?>"><?php echo Helper::eae_wp_kses($settings['next_button_text']); Icons_Manager::render_icon( $settings['next_button_icon'], [ 'aria-hidden' => 'true' ] );?></a><?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php if($settings['end_tour_text'] !== ''){?>
                                        <div class="eae-ih-end-tour-btn-container">
                                            <a class="eae-ih-end-tour-btn" data-tooltip-id="<?php echo esc_attr($tooltipId); ?>"><?php echo Helper::eae_wp_kses($settings['end_tour_text']); ?></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php
    }

    protected function content_template(){
        ?>
            <#

                let renderIconHtml = function(sett, control_name, wClass = '', index='') {
                    
                    var icon_class = '';
                    let imageHtml = '';
                    
                    let lottie_data = [];
                    if(sett[control_name+'_graphic_type'] != 'none'){
                        icon_class += ' eae-gbl-icon eae-graphic-type-'+ sett[control_name+'_graphic_type'];
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
                        if(sett[control_name+'_hover_animation'] != 'none'){
                            view.addRenderAttribute('panel-icon-'+ index, 'class', 'elementor-animation-' + sett[control_name + '_hover_animation']);
                        }
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
                    }
                }

                let flag = 0;

                let image = {
                    id: settings.image.id,
                    url: settings.image.url,
                    size: settings.image_size,
                    dimension: settings.image_custom_dimension,
                    model: view.getEditModel()
			    };

                let image_url = elementor.imagesManager.getImageUrl(image);
 
                let image_html = '<img src="' + _.escape( image_url ) + '"  />'; 
                
                #>
                    <div class="eae-ih-wrapper">
                        {{{ image_html }}}
                        <#
                            _.each(settings.markers, function ( item, index){
                                view.addRenderAttribute('eae-marker-container-' + index,'class','elementor-repeater-item-' + item._id);
                                tooltipId = index + 1;

                                view.addRenderAttribute('eae-marker-' + index,'class','eae-ih-marker');
                                if(settings.marker_animation_type && settings.marker_animation_type !== 'none'){
                                    view.addRenderAttribute('eae-marker-' + index, 'class', 'animated ' + settings.marker_animation_type );
                                    if(settings.marker_continuous_animtion != '' && settings.marker_continuous_animtion == 'yes'){
                                        view.addRenderAttribute('eae-marker-' + index, 'class', 'eae-ih-marker-animtion');
                                    }
                                }

                                if(item.tooltip_preview == 'yes'){
                                    view.addRenderAttribute('eae-marker-' + index, 'class', 'eae-ih-rep-tooltip-show');
                                }

                                if(settings.preview_tooltip == 'yes' && flag == 0){
                                    view.addRenderAttribute('eae-marker-' + index, 'class','eae-ih-tooltip-show');
                                    flag++;
                                }

                                if(item.enable_button == 'yes'){
                                    view.addRenderAttribute('eae-button-' + index,'class','eae-ih-tooltip-button');
                                    if(item.tooltip_button_icon.value !== ''){
                                        view.addRenderAttribute('eae-button-' + index, 'class','eae-ih-icon-position-' + item.tooltip_button_icon_position);
                                    }
                                }

                                if(item.enable_button == 'yes'){
                                    view.addRenderAttribute('eae-button-' + index, 'href' , _.escape(item.tooltip_button_link.url));
                                }

                                #>
                                    <div {{{ view.getRenderAttributeString('eae-marker-container-' + index) }}} >
                                        <div {{{ view.getRenderAttributeString('eae-marker-' + index) }}} data-marker= {{{tooltipId}}} >
                                            <#
                                                if(item.ih_graphic_type !== 'none'){
                                                    marker_icon_html = window.renderIconHtml(view ,elementor, item, 'ih', 'eae-ih-icon', tooltipId);
                                                    if(marker_icon_html != ''){
                                                        print(marker_icon_html);
                                                    }
                                                }

                                                if(item.heading !== ''){
                                                    #>
                                                        <div class="eae-ih-marker-text" > {{{ item.heading }}} </div>
                                                    <#
                                                }
                                            #>
                                        </div>
                                        <div class="eae-ih-tooltip" id="tooltip-{{{tooltipId}}}">
                                            <div class="eae-ih-tooltip-container">
                                                <#
                                                    if(item.tooltip_icon_graphic_type !== 'none'){
                                                        #>
                                                            <span class="eae-ih-tooltip-icon-container">
                                                                <#
                                                                    let tooltip_icon_html = window.renderIconHtml(view,elementor,item, 'tooltip_icon', 'eae-ih-tooltip-icon', index);
                                                                    if(tooltip_icon_html != ''){
                                                                        print(tooltip_icon_html);
                                                                    }
                                                                #>
                                                            </span>
                                                        <#
                                                    }

                                                    if(item.tooltip_heading != '' || item.tooltip_short_description != '' || item.enable_button !== ''){
                                                        #>
                                                            <div class="eae-ih-tooltip-content-container">
                                                                <#
                                                                    if(item.tooltip_heading != ''){
                                                                        #>
                                                                            <span class="eae-ih-tooltip-heading"> {{{ item.tooltip_heading }}} </span>
                                                                        <#
                                                                    }

                                                                    if(item.tooltip_short_description != ''){
                                                                        #>
                                                                            <span class="eae-ih-tooltip-short-description"> {{{ item.tooltip_short_description }}} </span>
                                                                        <#
                                                                    }

                                                                    if(item.tooltip_description !== ''){
                                                                        #>
                                                                            <span class="eae-ih-tooltip-description"> {{{ item.tooltip_description }}} </span>
                                                                        <#
                                                                    }

                                                                    if(item.tooltip_button_text !== '' && item.enable_button !== ''){
                                                                        #>
                                                                            <a {{{ view.getRenderAttributeString('eae-button-' + index) }}} >
                                                                                <#
                                                                                    print(item.tooltip_button_text);
                                                                                    let icon_html = elementor.helpers.renderIcon(view, item.tooltip_button_icon , { 'aria-hidden': true }, 'i' , 'object' );
                                                                                #>
                                                                                {{{ icon_html.value }}}
                                                                            </a>
                                                                        <#
                                                                    }

                                                                    if(settings.enable_close_button == 'yes'){
                                                                        #>
                                                                            <a class="eae-ih-tooltip-close-icon" data-tooltip-id = "{{{ tooltipId }}}">
                                                                                <#
                                                                                    let tooltip_close_icon = elementor.helpers.renderIcon(view, settings.close_button_icon , { 'aria-hidden': true }, 'i' , 'object');
                                                                                    print(tooltip_close_icon.value);
                                                                                #>
                                                                            </a>
                                                                        <#
                                                                    }
                                                                #>
                                                            </div>
                                                        <#
                                                    }
                                                #>
                                            </div>
                                            <#
                                                if(settings.enable_hotspot_tour == 'yes'){
                                                    #>
                                                        <div class="eae-ih-tooltip-tour">
                                                            <div class="eae-ih-navigation-container">
                                                                <#
                                                                    if(settings.enable_count == 'yes'){
                                                                        #>
                                                                            <div class="eae-ih-tooltip-count-tour">
                                                                                <span class="eae-ih-count"> 
                                                                                    <#
                                                                                        let count = tooltipId + ' of ' + settings.markers.length;
                                                                                        print(count);
                                                                                    #>
                                                                                </span>
                                                                            </div>
                                                                        <#
                                                                    }
                                                                    #>
                                                                        <div class="eae-ih-tooltip-pre-nxt-btn">
                                                                            <#
                                                                                if(settings.previous_button_icon.value != '' || settings.previous_button_text !== ''){
                                                                                    #>
                                                                                        <a class="eae-ih-tooltip-prev" data-tooltip-id= "{{{ tooltipId }}}" >
                                                                                            <#
                                                                                                let tooltip_prev_icon_html = elementor.helpers.renderIcon(view, item.previous_button_icon , { 'aria-hidden': true }, 'i' , 'object' );
                                                                                                print(tooltip_prev_icon_html.value);
                                                                                            #>
                                                                                            {{{ settings.previous_button_text }}}
                                                                                        </a>
                                                                                    <#
                                                                                }

                                                                                if(settings.next_button_icon.value != '' || settings.next_button_text !== ''){
                                                                                    #>
                                                                                        <a class="eae-ih-tooltip-next" data-tooltip-id= "{{{tooltipId}}}">
                                                                                            {{{ settings.next_button_text }}}
                                                                                            <#
                                                                                                let tooltip_next_icon_html = elementor.helpers.renderIcon(view, item.next_button_text , { 'aria-hidden': true }, 'i' , 'object' );
                                                                                                print(tooltip_next_icon_html.value); 
                                                                                            #>
                                                                                        </a>
                                                                                    <#
                                                                                }
                                                                            #>
                                                                        </div>
                                                                    <#  
                                                                #>
                                                            </div>
                                                            <#
                                                                if(settings.end_tour_text !== ''){
                                                                    #>
                                                                        <div class="eae-ih-end-tour-btn-container">
                                                                            <a class="eae-ih-end-tour-btn" data-tooltip-id="{{{tooltipId}}}" >
                                                                                {{{ settings.end_tour_text }}}
                                                                            </a>
                                                                        </div>
                                                                    <#
                                                                }
                                                            #>
                                                        </div>
                                                    <#
                                                }
                                            #>
                                        </div>
                                    </div>
                                <#

                            });
                        #>
                    </div>
                <#
            #>
        <?php
    }
}
?>
