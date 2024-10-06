<?php

namespace WTS_EAE\Pro\Modules\RadialCharts\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Core\Kits\Manager as Kits_Manager;
use Elementor\Plugin as Plugin;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class RadialCharts extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-radial-charts';
	}

	public function get_title() {
		return __( 'Radial Charts', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-radial-charts';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'charts', 'pie-chart', 'polar', 'doughnut', 'chart', 'pie' ];
	}

	
	public function get_script_depends() {
		return [ 'eae-chart' ];
	}

	protected function register_controls() {
        $this->start_controls_section(
			'section_general',
			[
				'label' => __( 'General', 'wts-eae' ),
			]
		);
        $this->add_control(
			'chart_type',
			[
				'label'     => __( 'Type', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'pie'    => __( 'Pie Chart', 'wts-eae' ),
					'doughnut'   => __( 'Doughnut Chart', 'wts-eae' ),
					'polarArea' => __( 'Polar Chart', 'wts-eae' ),
				],
				'default'   => 'pie',
			]
		);

		$this->add_control(
			'table_count',
			[
				'label'       => __( 'Datasets', 'wts-eae' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'min'         => 1,
				'max'         => 10,
			]
		);
		
		$this->end_controls_section();
		$weight_options = array(
			'' => esc_html__( 'Default', 'wts-eae' ),
		);

		foreach ( array_merge( [ 'normal', 'bold' ], range( 100, 900, 100 ) ) as $weight ) {
			$weight_options[ $weight ] = ucfirst( $weight );
		}

		$this->start_controls_section(
            'section_chart_style_legend',
            [
                'label' => esc_html__( 'Legend', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'=> [
					'chart_legend'=>'true',
				]
            ]
        );
		$this->add_control(
			'chart_box_height',
			[
				'label' => esc_html__( 'Box Height', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],		
			]
		);
		$this->add_control(
			'chart_box_width',
			[
				'label' => esc_html__( 'Box Width', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);
		$this->add_control(
			'chart_box_font_size',
			[
				'label' => esc_html__( 'Font Size', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],				
			]
		);
		$this->add_control(
			'eae_chart_legend_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $weight_options,

			]
		);
		$this->add_control(
			'eae_chart_legend_color',
			[
				'label' => esc_html__( 'Font Color', 'wts-eae' ),
				'type'  => Controls_Manager::COLOR,
			]			
		);

		$this->end_controls_section();

		$this->start_controls_section(
            'section_chart_style_tooltip',
            [
                'label' => esc_html__( 'Tooltip', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'=> [
					'chart_tooltip'=>'true',
				]
            ]
        );
		$this->add_control(
			'eae_chart_tooltip_color',
			[
				'label' => esc_html__( 'Background Color', 'wts-eae' ),
				'type'  => Controls_Manager::COLOR,	
			]			
		);
		$this->add_control(
			'chart_tooltip_title_font',
			[
				'label'     => __( 'Title', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'chart_tooltip_title_font_size',
			[
				'label' => esc_html__( 'Font Size', 'wts-eae' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' =>[
						'min' => 0,
						'max' => 100,
					],
				],
			]		
		);
		$this->add_control(
			'eae_chart_tooltip_title_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $weight_options,

			]
		);
		$this->add_control(
			'eae_chart_title_tooltip_color',
			[
				'label' => esc_html__( 'Font Color', 'wts-eae' ),
				'type'  => Controls_Manager::COLOR,
			]			
		);

		$this->add_control(
			'chart_tooltip_body_font',
			[
				'label'     => __( 'Body', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'chart_tooltip_body_font_size',
			[
				'label' => esc_html__( 'Font Size', 'wts-eae' ),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' =>[
						'min' => 0,
						'max' => 100,
					],
				],
			]		
		);
		$this->add_control(
			'eae_chart_tooltip_body_font_weight',
			[
				'label'   => esc_html__( 'Font Weight', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $weight_options,

			]
		);
		$this->add_control(
			'eae_chart_body_tooltip_color',
			[
				'label' => esc_html__( 'Font Color', 'wts-eae' ),
				'type'  => Controls_Manager::COLOR,
			]			
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_chart_style_polar_chart',
            [
                'label' => esc_html__( 'Polar Area', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'chart_type',
							'operator' => '==',
							'value' => 'polarArea'
						],
						[
							'relation' => 'or',
							'terms' => [
								[	
									'name' => 'chart_enable_ticks',
									'operator' => '==',
									'value' => 'true'
								],
								[	
									'name' => 'chart_enable_grid_lines',
									'operator' => '==',
									'value' => 'true'
								],
							]
						]
					]
					
				]
            ]
        );
		$this->add_control(
			'chart_ticks_style',
			[
				'label'     => __( 'Ticks', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'condition'=> [
					'chart_enable_ticks'=>'true',
				]
			]
		);
		

		$this->add_control(
            'style_polar_chart_ticks',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
				'condition'=> [
					'chart_enable_ticks'=>'true',
				]
                
            ] 
        );
		
		$this->add_control(
            'style_polar_chart_ticks_background',
            [
                'label' => esc_html__( 'Background Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
				'condition'=> [
					'chart_enable_ticks'=>'true',
				]
                
            ]
        );
		$this->add_control(
			'chart_polar_chart_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' =>[
						'min' => 0,
						'max' => 10,
					],
				],
				'condition'=> [
					'chart_enable_ticks'=>'true',
				]
			]	
		);
		$this->add_control(
			'chart_box_ticks_font_size',
			[
				'label' => esc_html__( 'Font Size', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'separator' => 'after',

				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],	
				'condition'=> [
					'chart_enable_ticks'=>'true',
				]			
			]
		);
		$this->add_control(
			'chart_grid_style',
			[
				'label'     => __( 'Grid Line', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'condition'=> [
					'chart_enable_grid_lines'=>'true'
				]
			]
		);
		$this->add_control(
            'style_polar_chart_ticks_grid',
            [
                'label' => esc_html__( ' Grid Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
				'condition'=> [
					'chart_enable_grid_lines'=>'true'
				]
                
            ] 
        );

		$this->end_controls_section();

        $this->add_datasets();
		$this->start_controls_section(
			'section_Layout',
			[
				'label' => __( 'Layout', 'wts-eae' ),
			]
		);
		

        $this->add_responsive_control(
            'chart_height',
            [
                'label' => esc_html__( 'Height', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                        'px' => [
                        'min' => 220,
                        'max' => 1000,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
               
                'render_type' => 'template',
                'selectors' => [
                            '{{WRAPPER}} .eae-radial-chart-wrapper '=> 'height:{{SIZE}}{{UNIT}}; position:relative;',
                ],
            ]
        );
		
		$this->add_control(
			'chart_circular',
			[
				'label'        => __( 'Circular', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'true',
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'true',
			]
		);	
		$this->add_control(
			'chart_circumference',
			[
				'label' => esc_html__( 'Circumference', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
					],
				],
			]
		);
		$this->add_control(
			'chart_rotation',
			[
				'label' => esc_html__( 'Rotation', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
					],
				],
			]
		);
		$this->add_control(
			'chart_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);
		$this->add_control(
			'chart_cutout',
			[
				'label' => esc_html__( 'Cutout', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'condition' => [
					'chart_type'=>'doughnut',
				],	
			]
		);
		
		$this->start_controls_tabs(
            'style_tabs'
        );
        $this->start_controls_tab(
            'style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'wts-eae' ),
            ]
        );
		$this->add_control(
			'chart_off_set',
			[
				'label' => esc_html__( 'Offset', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition'=>[
					'table_count' => 1,
				],
			]
		);
		$this->add_control(
			'chart_border_width_set',
			[
				'label' => esc_html__( 'Border Width', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
			]
		);
		
		$this->end_controls_tab();
		$this->start_controls_tab(
            'style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'wts-eae' ),
            ]
        );
		$this->add_control(
			'chart_hover_border_width',
			[
				'label' => esc_html__( 'Hover Border Width', 'wts-eae' ),
				'type'  => Controls_Manager::SLIDER,
				
				'range' => [
					'px' =>[
						'min' => 0,
						'max' => 30,
					],
				],
			]	
		);

		$this->add_control(
			'chart_hover_off_set',
			[
				'label' => esc_html__( 'Hover Offset', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);
		
		$this->end_controls_tab();
        $this->end_controls_tabs();
		$this->add_control(
			'chart_animation_heading',
			[
				'label'     => __( 'Animation', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pie_chart_duration_animation',
			[
				'label'   => __( 'Animation Duration', 'wts-eae' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 10000,
				'step'    => 100,
				'default' => 1000,
			]
		);
		
		$this->add_control(
			'chart_animation',
			[
				'label'   => __( 'Animation', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'linear'           => __( 'Linear', 'wts-eae' ),
					'easeInQuad'       => __( 'Ease in Quad', 'wts-eae' ),
					'easeOutQuad'      => __( 'Ease out Quad', 'wts-eae' ),
					'easeInOutQuad'    => __( 'Ease in out Quad', 'wts-eae' ),
					'easeInCubic'      => __( 'Ease in Cubic', 'wts-eae' ),
					'easeOutCubic'     => __( 'Ease out Cubic', 'wts-eae' ),
					'easeInOutCubic'   => __( 'Ease in out Cubic', 'wts-eae' ),
					'easeInQuart'      => __( 'Ease in Quart', 'wts-eae' ),
					'easeOutQuart'     => __( 'Ease out Quart', 'wts-eae' ),
					'easeInOutQuart'   => __( 'Ease in out Quart', 'wts-eae' ),
					'easeInQuint'      => __( 'Ease in Quint', 'wts-eae' ),
					'easeOutQuint'     => __( 'Ease out Quint', 'wts-eae' ),
					'easeInOutQuint'   => __( 'Ease in out Quint', 'wts-eae' ),
					'easeInSine'       => __( 'Ease in Sine', 'wts-eae' ),
					'easeOutSine'      => __( 'Ease out Sine', 'wts-eae' ),
					'easeInOutSine'    => __( 'Ease in out Sine', 'wts-eae' ),
					'easeInExpo'       => __( 'Ease in Expo', 'wts-eae' ),
					'easeOutExpo'      => __( 'Ease out Expo', 'wts-eae' ),
					'easeInOutExpo'    => __( 'Ease in out Cubic', 'wts-eae' ),
					'easeInCirc'       => __( 'Ease in Circle', 'wts-eae' ),
					'easeOutCirc'      => __( 'Ease out Circle', 'wts-eae' ),
					'easeInOutCirc'    => __( 'Ease in out Circle', 'wts-eae' ),
					'easeInElastic'    => __( 'Ease in Elastic', 'wts-eae' ),
					'easeOutElastic'   => __( 'Ease out Elastic', 'wts-eae' ),
					'easeInOutElastic' => __( 'Ease in out Elastic', 'wts-eae' ),
					'easeInBack'       => __( 'Ease in Back', 'wts-eae' ),
					'easeOutBack'      => __( 'Ease out Back', 'wts-eae' ),
					'easeInOutBack'    => __( 'Ease in Out Back', 'wts-eae' ),
					'easeInBounce'     => __( 'Ease in Bounce', 'wts-eae' ),
					'easeOutBounce'    => __( 'Ease out Bounce', 'wts-eae' ),
					'easeInOutBounce'  => __( 'Ease in out Bounce', 'wts-eae' ),
				],
				'default' => 'linear',
			]
		);
		$this->add_control(
			'chart_animation_scale',
			[
				'label'        => __( 'Animation Scale', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'true',
			]
		);
		$this->add_control(
			'chart_legend_heading',
			[
				'label'     => __( 'Legend', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'chart_legend_message',
			[
				'label'      => __( 'Legend Note', 'wts-eae' ),
				'type'       => Controls_Manager::RAW_HTML,
				'raw'        => __( 'Note : Legend get color from Dataset 1.', 'wts-eae' ),
				'show_label' => false,
				'condition'  => [
					'chart_legend' => 'true',
				],
			]
		);

		$this->add_control(
			'chart_legend',
			[
				'label'        => __( 'Enable Legend', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'true',
				'default'      => 'true',

			]
		);
	
		$this->add_control(
			'chart_legend_shape',
			[
				'label'     => __( 'Shape', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'square'    => __( 'Square', 'wts-eae' ),
					'round'   => __( 'Round', 'wts-eae' ),
					
				],
				'default'   => 'square',
				'condition' => [
					'chart_legend' => 'true',
				],
			]
		);


		$this->add_control(
			'chart_legend_position',
			[
				'label'     => __( 'Position', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'top'    => __( 'Top', 'wts-eae' ),
					'left'   => __( 'Left', 'wts-eae' ),
					'bottom' => __( 'Bottom', 'wts-eae' ),
					'right'  => __( 'Right', 'wts-eae' ),
				],
				'default'   => 'top',
				'condition' => [
					'chart_legend' => 'true',
				],
			]
		);

		$this->add_control(
			'chart_legend_align',
			[
				'label'     => __( 'Alignment', 'wts-eae' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'start' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'condition' => [
					'chart_legend' => 'true',
				],
			]
		);

		$this->add_control(
			'chart_legend_reverse',
			[
				'label'        => __( 'Reverse', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'yes',
				'condition' => [
					'chart_legend' => 'true',
				],
			]
		);

		$this->add_control(
			'chart_tooltip_heading',
			[
				'label'     => __( 'Tooltip', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'chart_tooltip',
			[
				'label'        => __( 'Enable Tooltips', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'true',
				'default'      => 'true',
			]
		);
		

		$this->add_control(
			'chart_enable_ticks',
			[
				'label'        => __( 'Enable Ticks', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'separator' => 'before',
				'label_off'    => 'No',
				'return_value' => 'true',
				'default'      => 'true',
				'condition' => [
					'chart_type'=>'polarArea',
				],	
			]
		);
		
		$this->add_control(
			'chart_polar_percentage',
			[
				'label'        => __( 'Show  Percentage', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'true',
				'condition' => [
					'chart_enable_ticks' => 'true',
					'chart_type'=>'polarArea',
				],	
			]
		);
		$this->add_control(
			'chart_enable_grid_lines',
			[
				'label'        => __( 'Enable Grid Line', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'true',
				'default'      => 'true',
				'condition' => [
					'chart_type'=>'polarArea',
				],	
			]
		);
		$this->end_controls_section();


    }

    public function add_datasets() {
        $repeater = new Repeater();
        $repeater->start_controls_tabs( 'start_chart_data_tab' );

			$repeater->start_controls_tab(
				'pie_content',
				[
					'label' => __( 'Content', 'wts-eae' ),
				]
			);

			$repeater->add_control(
				'chart_label',
				[
					'label'       => __( 'Label', 'wts-eae' ),
					'label_block' => true,
					'type'        => Controls_Manager::TEXT,
					'default'     => __( 'Lable', 'wts-eae' ),
					'placeholder' => __( 'Enter your label', 'wts-eae' ),
				]
			);

			$repeater->add_control(
				'chart_data',
				[
					'label'       => __('Value', 'wts-eae' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'default'     => '',
					'placeholder' => __( '', 'wts-eae' ),
				]
			);

        $repeater->end_controls_tab();
        $repeater->start_controls_tab(
            'style',
            [
                'label' => __( 'Style', 'wts-eae' ),
            ]
        );

        $repeater->add_control(
            'chart_background_color',
            [
                'label'   => __( 'Background Color', 'wts-eae' ),
                'type'    => Controls_Manager::COLOR,
            ]
        );
        
        $repeater->add_control(
            'chart_background_hover_color',
            [
                'label'   => __( 'Background Hover Color', 'wts-eae' ),
                'type'    => Controls_Manager::COLOR,
            ]
        );
        $repeater->add_control(
            'chart_border_color',
            [
                'label'   => __( 'Border Color', 'wts-eae' ),
                'type'    => Controls_Manager::COLOR,
            ]
        );
        $repeater->add_control(
            'chart_border_hover_color',
            [
                'label'   => __( 'Border Hover Color', 'wts-eae' ),
                'type'    => Controls_Manager::COLOR,				
            ]
        );
        $repeater->end_controls_tab();
        $this->end_controls_tabs();
        
		for ( $i = 1; $i < 11; $i++ ) {
            $this->start_controls_section(
				'section_dataset_' . $i,
				[
					'label'     => sprintf( esc_html__( 'Dataset %s', 'wts-eae' ), $i ),
					'operator'  => '>',
					'condition' => [
						'table_count' => $this->add_condition_value( $i ),
					],
				]
			);

            $this->add_control(
				'dataset_label_' . $i,
				[
					'label'       => __( 'Label', 'wts-eae' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [
						'active' => true,
					],
					'default'     => __( 'Dataset', 'wts-eae' ),
					'placeholder' => __( 'Enter Dataset Label', 'wts-eae' ),
				]
			);


            $this->add_control(
				'chart_data_' . $i,
				[
					'label'      => __( 'Chart Data', 'wts-eae' ),
					'type'       => Controls_Manager::REPEATER,
					'show_label' => true,
					'default'    => [
						[
							'chart_label' => __( 'Google', 'wts-eae' ),
                            'chart_data'  => 15,
							'chart_background_color'             => '#dd4b39',
							'chart_background_hover_color'       => '#dd4b39',
							'chart_border_color'                 =>'#FFFFFF',
							'chart_border_hover_color'           =>'#FFFFFF',
						],
						[
							'chart_label' => __( 'Facebook', 'wts-eae' ),
                            'chart_data'  => 15,
							'chart_background_color'             => '#3b5998',
							'chart_background_hover_color'       => '#3b5998',
							'chart_border_color'                 =>'#FFFFFF',
							'chart_border_hover_color'           =>'#FFFFFF',
						],
						[
							'chart_label' => __( 'Twitter', 'wts-eae' ),
                            'chart_data'  => 20,
							'chart_background_color'             => '#55acee',
							'chart_background_hover_color'       => '#55acee',
							'chart_border_color'                 =>'#FFFFFF',
							'chart_border_hover_color'           =>'#FFFFFF',
						],
                        [
							'chart_label' => __( 'Instagram', 'wts-eae' ),
                            'chart_data'  => 50,
							'chart_background_color'             => '#0E293E',
							'chart_background_hover_color'       => '#0E293E',
							'chart_border_color'                 =>'#FFFFFF',
							'chart_border_hover_color'           =>'#FFFFFF',
						],
					],
                    'title_field' => '{{{chart_label}}}',
					'fields'     => $repeater->get_controls(),
				]
			);

            $this->end_controls_section();

        }
    }  
    
    public function add_condition_value( $j ) {
		$value = [];
		for ( $i = $j; $i < 11; $i++ ) {
			$value[] = $i;
		}

		return $value;
	}
    
	protected function render() {
		
		$settings     = $this->get_settings_for_display();
		
		
		$data_chart   = $this->get_chart_data();

		
		$this->add_render_attribute( 'container', 'class', 'eae-radial-chart-container');
		$this->add_render_attribute( 'container', 'data-chart', json_encode($data_chart));

		$this->add_render_attribute( 'canvas', 'class', 'eae-radial-chart' );
		?>	
			<div <?php $this->print_render_attribute_string( 'container' ); ?>>
				<div class="eae-radial-chart-wrapper">
					<canvas <?php $this->print_render_attribute_string( 'canvas' ); ?>></canvas>
				</div>
			</div>		 
		<?php
	}

	public function eae_get_color_value($settings, $key, $default = '') {
		
		if(!empty($settings[$key])) {
			return $settings[$key];
		}else{
			if(empty($settings[$key]) && (array_key_exists('__globals__', $settings)) ) {
				$is_global_color = true;
				$color_value = explode('=',$settings['__globals__'][$key]);	
				$color_id = $color_value[1];
				$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();
				$system_items = $kit->get_settings_for_display( 'system_colors' );
				$custom_items = $kit->get_settings_for_display( 'custom_colors' );
				$colors = array_merge( $system_items, $custom_items );
				foreach($colors as $color){
					if($color['_id']==$color_id){
						$color_value = $color['color'];
						$is_custom_color = true;
						break;
					}
				}
				if(is_array($color_value)&& empty($color_value[0])){
					$color_value = $default;
				}
				return $color_value;
			}else{
				return $default;
			}
		}
	}

	
	public function get_chart_data() {
		$eManager = new Kits_Manager();
		
		$settings = $this->get_settings_for_display();
		$dataset_count=$settings['table_count'];
		$data_chart=[];
		
		for ( $i = 1; $i <= $dataset_count; $i++ ) {
			$chart_data =$settings['chart_data_'.$i];
			$label = $settings['dataset_label_'.$i];
			$hoverOffSet=$settings['chart_hover_off_set']['size'] ;
			foreach ( $chart_data as $item ) {
				if($i==1){
					$data_chart['labels'][]  = ! empty( $item['chart_label'] ) ? $item['chart_label'] : '';
				}
	            $data_chart['datasets'][$i-1]['label'] = $label;
	            $data_chart['datasets'][$i-1]['data'][]  = ! empty( $item['chart_data'] ) ? $item['chart_data'] : '';				
				$data_chart['datasets'][$i-1]['backgroundColor'][]  =$this->eae_get_color_value($item,'chart_background_color','');				
	            $data_chart['datasets'][$i-1]['hoverBackgroundColor'][]  = $this->eae_get_color_value($item,'chart_background_hover_color','');
	            $data_chart['datasets'][$i-1]['borderColor'][]  = $this->eae_get_color_value($item,'chart_border_color','');	
	            $data_chart['datasets'][$i-1]['hoverBorderColor'][]  =$this->eae_get_color_value($item,'chart_border_hover_color','');
				$data_chart['datasets'][0]['circular'] =! empty($settings['chart_circular'] ) ? $settings['chart_circular']  : '';
				$data_chart['datasets'][0]['rotation'] =! empty($settings['chart_rotation']['size'] ) ? $settings['chart_rotation']['size']  : '0';
				$data_chart['datasets'][0]['borderRadius'] =! empty($settings['chart_border_radius']['size'] ) ? $settings['chart_border_radius']['size']  : '';
				$data_chart['datasets'][0]['cutout'] = ! empty(  $settings['chart_cutout']['size'] ) ? $settings['chart_cutout']['size'] . '%' : '';
				$data_chart['datasets'][0]['offset'] = ! empty($settings['chart_off_set']['size'] ) ? $settings['chart_off_set']['size'] : 0;
				$data_chart['datasets'][$i-1]['hoverOffset'] =$hoverOffSet;
				$data_chart['datasets'][0]['borderWidth'] =! empty($settings['chart_border_width_set']['size'] ) ? $settings['chart_border_width_set']['size']  : '0';
				$data_chart['datasets'][0]['hoverBorderWidth'] =! empty($settings['chart_hover_border_width']['size'] ) ? $settings['chart_hover_border_width']['size']  : '0';
				$data_chart['datasets'][$i-1]['circumference'] =! empty($settings['chart_circumference']['size'] ) ? $settings['chart_circumference']['size']  : '360';
				
			}
		}
		$round="";
	    if($settings['chart_legend_shape']=='round'){
		$round="true"; };
		if($settings['chart_legend']=='true'){	
		$legend=[
			'display'  => $settings['chart_legend'],
			'position' => ! empty( $settings['chart_legend_position'] ) ? $settings['chart_legend_position'] : 'top',
			'align'    => $settings['chart_legend_align'],
			'reverse'  => $settings['chart_legend_reverse'] === 'yes' ? true : false,
			'labels'=>$labels=[
				'boxHeight'=> ! empty( $settings['chart_box_height']['size'] ) ? $settings['chart_box_height']['size'] : 16,
				'boxWidth'=> ! empty( $settings['chart_box_width']['size'] ) ? $settings['chart_box_width']['size'] : 44,		
				'color' => ! empty( $settings['eae_chart_legend_color'] ) ? $settings['eae_chart_legend_color'] : '#000000',	
				'font'=> $font=[
					'size' =>$settings['chart_box_font_size']['size'], 
					'weight'=>$settings['eae_chart_legend_font_weight'],
				],
				'usePointStyle'=>$round,
			],	
		];
		}else{
			$legend=[
				'display'  => $settings['chart_legend'],
			];
		}
		$tooltip=[
			'enabled' =>$settings['chart_tooltip'],
			'backgroundColor' => $this->eae_get_color_value($settings,'eae_chart_tooltip_color','#000000'),
			'bodyColor'=> $this->eae_get_color_value($settings,'eae_chart_body_tooltip_color','#fff'),
			'titleColor'=> $this->eae_get_color_value($settings,'eae_chart_title_tooltip_color','#fff'),
			'titleFont'=> $font=[
				'size' =>! empty( $settings['chart_tooltip_title_font_size']['size'] ) ? $settings['chart_tooltip_title_font_size'] ['size']: 18,
				'weight'=>$settings['eae_chart_tooltip_title_font_weight'],
			],
			'bodyFont'=> $font=[
				'size' =>! empty( $settings['chart_tooltip_body_font_size']['size'] ) ? $settings['chart_tooltip_body_font_size'] ['size']: 16,
				'weight'=>$settings['eae_chart_tooltip_body_font_weight'],
			],
		];
		$animation=[
			'duration'=> $settings['pie_chart_duration_animation'],
			'easing'=> $settings['chart_animation'],
			'animateScale'=>$settings['chart_animation_scale'],
		];

		$title_h=[
			'legend'=> $legend,
			'tooltip'=> $tooltip,			
		];
		
		$ticks_file=[
			'display'=>! empty( $settings['chart_enable_ticks']) ? $settings['chart_enable_ticks']: false,
			'color'=>! empty( $settings['style_polar_chart_ticks']) ? $settings['style_polar_chart_ticks']: '#000000',
			'backdropColor'=>! empty( $settings['style_polar_chart_ticks_background']) ? $settings['style_polar_chart_ticks_background']: '#fff',
			'backdropPadding'=>! empty( $settings['chart_polar_chart_padding']['size']) ? $settings['chart_polar_chart_padding']['size']: '',
			'font'=>$font=[
				'size'=>! empty( $settings['chart_box_ticks_font_size']['size']) ? $settings['chart_box_ticks_font_size']['size']: 16,
			],
		]; 
		$grid=[
			'color'=>! empty( $settings['style_polar_chart_ticks_grid']) ? $settings['style_polar_chart_ticks_grid']: '#e2e2e2',
			'display'=>$settings['chart_enable_grid_lines'],
		];
		$ticks=[
			'ticks'=>$ticks_file,
			'grid'=>$grid
		];
		$polar=[
				'r'=> $ticks,
		];	
		$padding=[
			'padding'=>20
		];

		$options =[
			'plugins' => $title_h,
			'animation'=> $animation,
			'maintainAspectRatio' => false,
			'layout'=>$padding,
		];
		if($settings['chart_type']=='polarArea'){
			$options['scales'] =  $polar;
		}	
		
		$chart = (
			[
				'type'    =>$settings['chart_type'],			
				'data'    => $data_chart,	
				'options' =>$options,	
				'enablePercentage'=>$settings['chart_polar_percentage'],
				
			]
		);	
		return $chart;
	}
	
}    