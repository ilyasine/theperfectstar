<?php

namespace WTS_EAE\Pro\Modules\CircularProgress\Widgets;

use Elementor\Conditions;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Base;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use WTS_EAE\Classes\Helper;
use Elementor\Repeater;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Utils;
use function PHPSTORM_META\type;

if( ! defined('ABSPATH')){
    exit;
}

class CircularProgress extends EAE_Widget_Base{
    
    public function get_name(){
        return 'eae-circular-progress';
    }

    public function get_title(){
        return __('Circular Progress','wts-eae');
    }

    public function get_script_depends() {
        return [ 'eae-lottie' ];
    }

    public function get_categories(){
        return ['wts-eae'];
    }

    public function get_icon() {
		return 'eae-icon eae-circular-progress';
	}
    

    protected function register_controls(){

        $this->get_layout_section();

        $this->get_settings_section();

        $this->get_circle_style_section();

        $this->get_content_style_section();

        $this->get_inside_box_style_section();

        $this->get_below_box_style_section();
    }

    public function get_settings_section(){
        $this->start_controls_section(
            'cp_settings_style_section',
            [
                'label' => esc_html__('Settings','wts-eae'),
            ]
        );

        $this->add_responsive_control(
            'cp_content_gap',
            [
                'label' => esc_html__('Inside Content Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%','px'],
                'default' => [
                    'size' => '8',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-text-contain' => 'gap:{{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'cp_outside_content_gap',
            [
                'label' => esc_html__('Outside Content Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%','px'],
                'default' => [
                    'size' => '5',
                    'unit' => 'px',
                ],
                'selectors' => [ 
                    '{{WRAPPER}} .eae-cp-text-contain-below-canvas' => 'gap:{{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'cp_suffix_gap',
            [
                'label' => esc_html__('Suffix Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%','px'],
                'selectors' => [ 
                    '{{WRAPPER}} .eae-cp-value-container' => 'gap:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'cp_hide_value!' => 'yes',
                ]
            ]
        );

        $this->add_responsive_control(
            'cp_circle_size',
            [
                'label' => esc_html__('Circle Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%'],
                'separator' => 'before',
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
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-wrapper' => 'width:{{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'cp_track_width',
            [
                'label' => esc_html__('Track Width (%)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'default' => [
                    'size' => 10,
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'cp_progress_width',
            [
                'label' => esc_html__('Progress Width (%)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'default' => [
                    'size' => 10,
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'cp_start_angle',
            [
                'label' => esc_html__('Start Angle','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 360,
                    ],
                ],
                'condition' => [
                    'cp_layout_type' => 'full-circle',
                ]
            ]
        );

        $this->add_control(
            'cp_circle_animation_direction',
            [
                'label' => esc_html__('Reverse','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'cp_animation_duration',
            [
                'label' => esc_html__('Duration (ms)','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1000,
                'min' => 100,
                'step' => 100,
            ]
        );

        $this->end_controls_section();
    }

    public function get_inside_box_style_section(){
        $this->start_controls_section(
            'cp_inside_style_section',
            [
                'label' => esc_html__('Inside Box','wts-eae'),
                'tab' => Controls_Manager:: TAB_STYLE,
            ]
        );
       
        $this->add_control(
            'cp_content_box_size',
            [
                'label' => esc_html__('Box Size (%)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'frontend_available' => true,
				'default' => [
                    'unit' => '%',
                    'size' => '100',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ]
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'cp_content_box_background',
                'selector' => '{{WRAPPER}} .eae-cp-text-contain',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'cp_content_box_border',
                'selector' => '{{WRAPPER}} .eae-cp-text-contain',
            ]
        );

        $this->add_responsive_control(
            'cp_content_box_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-text-contain' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'cp_layout_type' => 'full-circle'
                ]
            ]
        );

        $this->add_responsive_control(
            'cp_content_box_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-text-contain' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function get_below_box_style_section(){
        $this->start_controls_section(
            'below_style_section',
            [
                'label' => esc_html__('Below Box','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'cp_content_outside_box_background',
                'selector' => '{{WRAPPER}} .eae-cp-text-contain-below-canvas',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'cp_content_outside_box_shadow',
                'selector' => '{{WRAPPER}} .eae-cp-text-contain-below-canvas',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'cp_content_outside_box_border',
                'selector' => '{{WRAPPER}} .eae-cp-text-contain-below-canvas',
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
                        ],
                    ],
                    'color' => [
                        'default' => '#D5D8DC',
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'cp_content_outside_box_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-text-contain-below-canvas' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'cp_content_outside_box_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'default' => [
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'size' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-text-contain-below-canvas' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'cp_content_outside_box_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-text-contain-below-canvas' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function get_content_style_section(){
        $this->start_controls_section(
            'content_style_section',
            [
                'label' => esc_html__('Content','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'cp_title_style_heading',
            [
                'label' => esc_html__('Title','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separetor' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cp_title_typography',
                'selector' => '{{WRAPPER}} .eae-cp-title',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
            ]
        );

        $this->add_control(
            'cp_title_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-title' => 'color:{{VALUE}};',
                ] 
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'cp_title_shadow',
                'selector' => '{{WRAPPER}} .eae-cp-title',
            ]
        );

        $this->add_control(
            'cp_description_style_heading',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before', 
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cp_description_typography',
                'selector' => '{{WRAPPER}} .eae-cp-description'
            ]
        );

        $this->add_control(
            'cp_description_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-description' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'cp_description_shadow',
                'selector' => '{{WRAPPER}} .eae-cp-description',
            ]
        );

        $this->add_control(
            'cp_value_style_heading',
            [
                'label' => esc_html__('Value','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cp_value_typography',
                'selector' => '{{WRAPPER}} .eae-cp-procent',
            ]
        );

        $this->add_control(
            'cp_value_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-procent' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'cp_suffix_style_heading',
            [
                'label' => esc_html__('Suffix','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        ); 
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cp_suffix_typography',
                'selector' => '{{WRAPPER}} .eae-cp-suffix',
            ]
        );

        $this->add_control(
            'cp_suffix_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-suffix' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'cp_suffix_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row-reverse' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-justify-start-h',
                    ],
                    'row' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h',
                    ]
                ],
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-value-container' => 'flex-direction:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'cp_suffix_alingment',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => esc_html__('Top','wts-eae'),
                        'icon' => 'eicon-justify-start-v'
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-justify-center-v',
                    ],
                    'end' => [
                        'title' => esc_html__('Bottom','wts-eae'),
                        'icon' => 'eicon-justify-end-v',
                    ],
                    
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-suffix' => 'align-self:{{VALUE}};'
                ],
                'toggle' => false,
            ]
        );

        $this->add_control(
            'cp_suffix_offset',
            [
                'label' => esc_html__('Top Offset','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'range' 	=> [
                    'px' 	=> [
                        'min' 	=> 0,
                        'max' 	=> 3,
                        'step'	=> 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cp-suffix' => 'margin-top:{{SIZE}}em;',
                ]
            ]
        );

        $this->add_control(
            'cp_icon_style_heading',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'circle_icon',
                'selector'      => '.eae-cp-icon',
                'is_repeater'   => 'false', 
            ]
        );

        $this->end_controls_section();
    }

    public function get_circle_style_section(){
        $this->start_controls_section(
            'circle_style_section',
            [
                'label' => esc_html__('Circle','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'cp_track_color',
            [
                'label' => esc_html__('Track Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'cp_progress_color_type',
            [
                'label' => esc_html__('Progress Color Type','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'classic' => [
                        'title' => esc_html__('Classic','wts-eae'),
                        'icon' => 'eicon-paint-brush'
                    ],
                    'gradient' => [
                        'title' => esc_html__('Gradient','wts-eae'),
                        'icon' => 'eicon-barcode'
                    ]
                ]
            ]
        );

        $this->add_control(
            'cp_progress_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'cp_progress_color_type' => 'classic'
                ]
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'cp_progress_gradient_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
            ]
        );

        $repeater->add_control(
            'cp_progress_color_stop',
            [
                'label' => esc_html__('Color Stop (%)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                    'size' => '0',
                ],
                'range' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ]
        );

        $this->add_control(
            'cp_progress_gradient',
            [
                'label' => esc_html__('Gradient Color','wts-eae'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'cp_progress_gradient_color' => '#FF9E00',
                        'cp_progress_color_stop' => [
                            'size' => 1
                        ],
                    ],
                    [
                        'cp_progress_gradient_color' => '#FF0054',
                        'cp_progress_color_stop' => [
                            'size' => 50
                        ],
                    ],
                    [
                        'cp_progress_gradient_color' => '#5A189A',
                        'cp_progress_color_stop' => [
                            'size' => 100
                        ],
                    ],
                ],
                'title_field' => '<i class="fas fa-square" style="color:{{{cp_progress_gradient_color}}}"></i> {{{cp_progress_gradient_color}}} ({{{cp_progress_color_stop.size}}}%)' ,
                'condition' => [
                    'cp_progress_color_type' => 'gradient'
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function get_layout_section(){
        $this->start_controls_section(
            'layout_section',
            [
                'label' => esc_html__('Layout','wts-eae'),
            ]
        );

        $this->add_control(
            'cp_layout_type',
            [
                'label' => esc_html__('Type','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'default' => 'full-circle',  
                'options' => [
                    'full-circle' => esc_html__('Circle','wts-eae'),
                    'half-circle' => esc_html__('Half Circle','wts-eae'),
                ],
            ]
        );

        $this->add_control(
            'cp_track_layout',
            [
                'label' => esc_html__('Track Layout','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'default' => 'butt',
                'options' => [
                    'round' => esc_html__('Round','wts-eae'),
                    'butt' => esc_html__('Square','wts-eae'),
                ]
            ]
        );

		$this->add_control(
			'layout_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'cp-value-heading',
			[
				'label'     => __( 'Value', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_control(
            'cp_value_type',
            [
                'label' => esc_html__('Type','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'percentage' => esc_html__('Percentage','wts-eae'),
                    'custom' => esc_html__('Custom','wts-eae'),
                ],
                'default' => 'percentage',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'cp_value',
            [
                'label' => esc_html__('Value','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'default' => 90,
            ]
        );

        $this->add_control(
            'cp_max_value',
            [
                'label' => esc_html__('Max Value','wts-eae'),
                'type' => Controls_Manager::NUMBER, 
                'condition' => [
                    'cp_value_type' => 'custom'
                ],
                'frontend_available' => true,    
            ]
        );

        $this->add_control(
            'cp_suffix_after_value',
            [
                'label' => esc_html__('Suffix','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => '%',
            ]
        );

		$this->add_control(
            'cp_hide_value',
            [
                'label' => esc_html__('Hide','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

		$this->add_control(
			'value_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'cp-title-heading',
			[
				'label'     => __( 'Title', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_control(
            'cp_title',
            [
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Title','wts-eae'),
				'label_block'  => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'cp_title_url_enable',
            [
                'label' => esc_html__('Enable Link ','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'cp_title_url',
            [
                'label' => esc_html__('URL','wts-eae'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'cp_title_url_enable' => 'yes',
                ]
            ]
        );

		$this->add_control(
			'title_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'cp-description-heading',
			[
				'label'     => __( 'Description', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        $this->add_control(
            'cp_description',
            [
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Description','wts-eae'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

		$this->add_control(
			'description_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'cp-icon-heading',
			[
				'label'     => __( 'Icon', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

        Helper::eae_media_controls(
            $this,
            [
                'name' => 'circle_icon',
                'label' => 'Icon',
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
			'icon_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

        $repeater = new Repeater();

        $repeater->add_control(
            'cp_element_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'inside' => esc_html__('Inside','wts-eae'),
                    'below' => esc_html__('Below','wts-eae'),
                ],
            ]
        );

        $this->add_control(
            'cp_order',
            [
                'label' => esc_html__('Order','wts-eae'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'cp_element_heading' => esc_html__('Icon','wts-eae'),
                        'cp_element_position' => 'inside',
                    ],
                    [
                        'cp_element_heading' => esc_html__('Title','wts-eae'),
                        'cp_element_position' => 'inside',
                    ],
                    [
                        'cp_element_heading' => esc_html__('Description','wts-eae'),
                        'cp_element_position' => 'inside',
                    ],
                    [
                        'cp_element_heading' => esc_html__('Value','wts-eae'),
                        'cp_element_position' => 'inside',
                    ],
                ],
                'item_actions' => [
                    'add' => false,
                    'duplicate' => false,
                    'remove' => false,
                    'sort' => true,
                ],
                
                'title_field' => '{{{cp_element_heading}}} ',
            ]
        );

        $this->end_controls_section();
    }

    public function get_cp_title($settings){
        $this->add_render_attribute('cp-title','class','eae-cp-title');
        if(!empty($settings['cp_title'])){
            $tag = 'span';
            if($settings['cp_title_url_enable'] == 'yes'){
                $this->add_link_attributes('cp-title', $settings['cp_title_url']);
                $tag = 'a';
            }
            $title = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag($tag), $this->get_render_attribute_string( 'cp-title' ), Helper::eae_wp_kses($settings['cp_title'])); 
            echo $title; 
        }
    }

    public function get_description_text($settings){
        if(!empty($settings['cp_description'])){
            ?><span class="eae-cp-description"><?php echo Helper::eae_wp_kses($settings['cp_description']); ?></span><?php
        }
    }

    public function get_cp_value(){
        $settings = $this->get_settings_for_display();
        if($settings['cp_suffix_after_value'] !== '' || $settings['cp_value'] !== ''){
            ?>
                <div class="eae-cp-value-container">
                    <?php
                        if($settings['cp_value'] !== ''){
                            ?><span class="eae-cp-procent"></span><?php
                        }
                    ?>
                    <?php if($settings['cp_suffix_after_value'] !== ''){
                        ?><span class="eae-cp-suffix"><?php echo Helper::eae_wp_kses($settings['cp_suffix_after_value']); ?></span><?php
                    } ?>
                </div>
            <?php
        }
    }

    public function render(){
        $settings = $this->get_settings_for_display();
        
        $data['value'] = $settings['cp_value'];
        $data['layout_type'] = $settings['cp_layout_type'];
        $data['start_angle'] = ($settings['cp_layout_type'] == 'full-circle') ? $settings['cp_start_angle']['size'] : '';
        $data['animation_duration'] = $settings['cp_animation_duration'];
        $data['hide_value'] = $settings['cp_hide_value'];
        $data['animation_direction'] = ($settings['cp_circle_animation_direction'] == 'yes') ? 'reverse' : '';

        $data['progress_color_type'] = $settings['cp_progress_color_type'];
        $data['progress_color_type'] = $settings['cp_progress_color_type'];
        if($settings['cp_progress_color_type'] == 'gradient'){
            $data['progress_gradient_color'] = $settings['cp_progress_gradient'];
        }else{
            $data['progress_color'] = ($settings['cp_progress_color'] == '')? '#6f45ff' : $settings['cp_progress_color'];
        }

        $flag = 0;
        $element = 0; 
        $test = 0;
        $data['track_color'] = ($settings['cp_track_color'] == '')? '#b1b1b1' : $settings['cp_track_color'];
        $data['track_width'] =  $settings['cp_track_width']['size'];
        $data['track_layout'] = $settings['cp_track_layout'];

        $data['progress_width'] = $settings['cp_progress_width']['size'];

        $height = ($settings['cp_layout_type'] == 'full-circle')? 1000: 500;  

        $this->add_render_attribute('cp-wrapper','data-settings',wp_json_encode($data));
        $this->add_render_attribute('cp-wrapper','class','eae-cp-wrapper eae-cp-layout-' . $settings['cp_layout_type']);
        if($settings['cp_value'] !== '' || $settings['cp_suffix_after_value'] !== '' || $settings['circle_icon_graphic_type'] !== 'none' || !empty($settings['cp_title']) || !empty($settings['cp_description'])){
            ?>
                <div class="eae-circular-progress-container" >
                    <div <?php echo $this->get_render_attribute_string('cp-wrapper') ; ?> >
                        <div class="eae-cp-canvas-wrapper" >
                            <div class="eae-cp-canvas-container">
                                <canvas class="eae-cp-canvas" width="1000" height="<?php echo esc_attr($height); ?>" ></canvas>
                                <?php if($settings['cp_hide_value'] !== 'yes' || $settings['circle_icon_graphic_type'] !== 'none' || !empty($settings['cp_title']) || !empty($settings['cp_description'])){ ?>
                                    <?php $test = $settings['cp_order']; ?> 
                                    <?php foreach($settings['cp_order'] as $index => $item){
                                        if($item['cp_element_position'] == 'inside'){
                                            if(!empty($settings['cp_title'] && $item['cp_element_heading'] == 'Title')){
                                                $element +=1;
                                            }elseif(!empty($settings['cp_description']) && $item['cp_element_heading'] == 'Description'){
                                                $element +=1;
                                            }elseif($settings['cp_value'] !== '' && $item['cp_element_heading'] == 'Value' && $settings['cp_hide_value'] !== 'yes'){
                                                $element +=1;
                                            }elseif($settings['circle_icon_graphic_type'] !== 'none' && $item['cp_element_heading'] == 'Icon'){
                                                $element +=1;
                                            }
                                        }else if($item['cp_element_position'] == 'below'){
                                            
                                            if(!empty($settings['cp_title'] && $item['cp_element_heading'] == 'Title')){
                                                $flag +=1;
                                            }elseif(!empty($settings['cp_description']) && $item['cp_element_heading'] == 'Description'){
                                                $flag +=1;
                                            }elseif($settings['cp_value'] !== '' && $item['cp_element_heading'] == 'Value' && $settings['cp_hide_value'] !== 'yes'){
                                                $flag +=1;
                                            }elseif($settings['circle_icon_graphic_type'] !== 'none' && $item['cp_element_heading'] == 'Icon'){
                                                $flag +=1;
                                            }
                                        }
                                    }                                    
                                    if(!empty($settings['cp_description']) || $settings['cp_suffix_after_value'] !== '' || !empty($settings['cp_title']) || $settings['cp_value'] !== '' || $settings['circle_icon_graphic_type'] !== 'none'){
                                        if($element != 0 ){ ?>
                                            <div class="eae-cp-text-contain" >
                                                <?php 
                                                    foreach($settings['cp_order'] as $index => $item){
                                                        if($item['cp_element_heading'] == 'Icon'){
                                                            if($item['cp_element_position'] == 'inside'){
                                                                Helper::render_icon_html($settings, $this, 'circle_icon','eae-cp-icon');
                                                            }
                                                        }else if($item['cp_element_heading'] == 'Title'){
                                                            if($item['cp_element_position'] == 'inside'){
                                                                $this->get_cp_title($settings);
                                                            }
                                                        }else if($item['cp_element_heading'] == 'Description'){
                                                            if($item['cp_element_position'] == 'inside'){
                                                                $this->get_description_text($settings);
                                                            }
                                                        }else if($item['cp_element_heading'] == 'Value'){
                                                            if($item['cp_element_position'] == 'inside' && $settings['cp_hide_value'] !== 'yes'){
                                                                $this->get_cp_value();
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        <?php }
                                    }
                                } ?>
                            </div>
                        </div>
                        <?php if(!empty($settings['cp_description']) || $settings['cp_suffix_after_value'] !== '' || !empty($settings['cp_title'] || $settings['cp_value'] !== '' || $settings['circle_icon_graphic_type'] !== 'none')){ 
                            if($flag !== 0) {
                                ?>
                                    <div class="eae-cp-text-contain-below-canvas">
                                        <?php 
                                            foreach($settings['cp_order'] as $index => $item){
                                                
                                                if($item['cp_element_heading'] == 'Icon'){
                                                    if($item['cp_element_position'] == 'below'){
                                                        Helper::render_icon_html($settings, $this, 'circle_icon','eae-cp-icon');
                                                    }
                                                }else if($item['cp_element_heading'] == 'Title'){
                                                    if($item['cp_element_position'] == 'below'){
                                                        $this->get_cp_title($settings);
                                                    }
                                                }else if($item['cp_element_heading'] == 'Description'){
                                                    if($item['cp_element_position'] == 'below'){
                                                        $this->get_description_text($settings);
                                                    }
                                                }else if($item['cp_element_heading'] == 'Value'){
                                                    if($item['cp_element_position'] == 'below' && $settings['cp_hide_value'] !== 'yes'){
                                                        $this->get_cp_value();
                                                    }
                                                }
                                            }
                                        ?>
                                    </div>
                                <?php
                            }?>
                        <?php } ?>
                    </div>
                </div>
            <?php
        }
    }
}

?>