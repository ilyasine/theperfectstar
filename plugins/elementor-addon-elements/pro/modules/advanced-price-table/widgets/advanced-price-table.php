<?php 
namespace WTS_EAE\Pro\Modules\AdvancedPriceTable\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use Elementor\Repeater;
use Elementor\Utils;



if( ! defined('ABSPATH') ){
    exit;
}
class AdvancedPriceTable extends  EAE_Widget_Base{
    public function get_name(){
        return 'eae-advanced-price-table';
    }

    public function get_title(){
        return __( 'Advanced Price Table', 'wts-eae');
    }

	public function get_icon(){
		return 'eae-icon eae-advance-price-table';
	}

    public function get_categories(){
        return [ 'wts-eae' ];
    }

    public function get_script_depends() {
        return [ 'eae-lottie' ];
    }

    public function get_keywords(){
        return ['advanced price table'];
    }

    protected function get_stacked_options () {
        $ele_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
        $args = [
            'add_desktop' => false
        ];
        $breakpoints = Plugin::$instance->breakpoints->get_breakpoints_config();
        $active_devices = Plugin::$instance->breakpoints->get_active_devices_list($args);
		$active_breakpoints = array_keys($ele_breakpoints);
		$break_value = [];
        $break_value_arr = [];
		foreach($active_devices as $active_device){
			$min_breakpoint = Plugin::$instance->breakpoints->get_device_min_breakpoint($active_device);
            $break_value[$active_device] = $min_breakpoint;
            $break_value_arr[$breakpoints[$active_device]['default_value']] = ucfirst($active_device); 
		}
        asort($break_value_arr);

		return $break_value_arr;
	}

    protected function register_controls(){
        $this->start_controls_section(
            'pt_general',
            [
                'label' => esc_html__( 'General' , 'wts-eae'),
            ]
        );

        $this->add_control(
            'pt_number_of_price_table',
            [
                'label' => esc_html__( 'Number of Price Table' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'render_type' => 'template',
                'max' => 10,
                'min' => 1,
                'step' => 1
            ]
        );

        $this->add_control(
            'pt_tabs_show',
            [
                'label' => esc_html__('Show Tabs','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'pt_number_of_price_table',
                            'operator' => '!=',
                            'value' => '0',
                        ],
                        [
                            'name' => 'pt_number_of_price_table',
                            'operator' => '!=',
                            'value' => '1',
                        ],
                        [
                            'name' => 'pt_number_of_price_table',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ]
            ]
        );

        $this->end_controls_section();

        $repeater = new Repeater();
        
        $repeater->add_control(
            'pt_features',
            [
                'label' => esc_html__('Features','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        Helper::eae_media_controls(
            $repeater,
            [
                'name' => 'pt_feature_icon',
                'label' => 'Icon',
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> false,
            ]
        );

        $repeater->add_control(
            'pt_feature_not_avalabel',
            [
                'label' => esc_html__('Feature Not Available','wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'separator' => 'before',
                'return_value' => 'yes',
                'render_template' => true,
            ]
        );

        $repeater->add_control(
            'pt_features_tooltip_text',
            [
                'label' => esc_html__('Tooltip Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        for ( $i = 1; $i < 11; $i++){
            $this->start_controls_section(
                'pt_section_' . $i,
                [
                    'label' => sprintf( esc_html__( 'Plan %s', 'wts-eae' ), $i ),
                    'operator' => '>',
                    'condition' => [
                        'pt_number_of_price_table' => $this->add_condition_value( $i ),
                    ],
                ]
            );

            $this->start_controls_tabs(
                'apt_tab_' . $i
            );

            $this->start_controls_tab(
                'pt_content_' . $i,
                [
                    'label' => esc_html__('Content' , 'wts-eae'),
                ]
            );

            $this->add_control(
                'pt_tab_'.$i,
                [
                    'label' => esc_html__('Tab','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'tab-1',
                    'options' => [
                        'tab-1' => esc_html__('Tab 1','wts-eae'),
                        'tab-2' => esc_html__('Tab 2','wts-eae'),
                    ],
                    'condition' => [
                        'pt_tabs_show' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'pt_title_'. $i,
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'default' => esc_html__('Basic Plan','wts-eae'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'pt_description_' . $i,
                [
                    'label' => esc_html__('Description' , 'wts-eae'),
                    'type' => Controls_Manager::TEXTAREA,
                    'label_block' => true,
                    'default' => esc_html__('Lorem ipsum dolor sit amet consectetur adipisicing elit.','wts-eae'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'pt_price_prefix_'. $i,
                [
                    'label' => esc_html__('Price Prefix' , 'wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('$','wts-eae'),
					'placeholder' => __( '$', 'wts-eae' ),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'pt_price_' . $i,
                [
                    'label' => esc_html__('Price','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('39','wts-eae'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'pt_sale_price_' . $i,
                [
                    'label' => esc_html__('Sale Price','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'pt_duration_' . $i,
                [
                    'label' => esc_html__('Duration','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('/year','wts-eae'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'pt_highlight_' . $i,
                [
                    'label' => esc_html__('Highlight','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_responsive_control(
                'pt_highlight_value_'. $i,
                [
                    'label' => esc_html__('Value (%)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => '2',
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i => 'margin-top: -{{VALUE}}%; margin-bottom: -{{VALUE}}%; z-index: 1;',
                        '{{WRAPPER}} .eae-apt-tab-content-section' => 'padding: {{VALUE}}% 0;' 
                    ],
                    'condition' => [
                        'pt_highlight_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'pt_items_' . $i, 
                [
                    'label' => esc_html__('Features List','wts-eae'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'prevent_empty' => false,
                    'default' => [
                        [
                            'pt_features' => esc_html__('Unlimited Email Account','wts-eae'),
                        ],
                        [
                            'pt_features' => esc_html__('Unlimited Space','wts-eae'),
                        ],
                        [
                            'pt_features' => esc_html__('Unlimited Domain Name','wts-eae'),
                        ],
                        [
                            'pt_features' => esc_html__('Unlimited Bandwidth','wts-eae'),
                        ],
                    ],
                    'title_field' => '{{{pt_features}}}',
                ]
            );



            $this->add_control(
                'pt_button_heading_' . $i ,
                [
                    'label' => esc_html__('Button','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $this->add_control(
                'pt_button_text_' . $i,
                [
                    'label' => esc_html__('Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('Learn More','wts-eae'),
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'pt_button_url_' . $i,
                [
                    'label' => esc_html__('URL','wts-eae'),
                    'type' => Controls_Manager::URL,
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            Helper::eae_media_controls(
                $this,
                [
                    'name' => 'pt_button_icon_' . $i,
                    'label' => 'Icon',
                    'icon'			=> true,
                    'image'			=> false,
                    'lottie'		=> false,
                ]
            );

            $this->add_control(
                'pt_badge_style_'. $i,
                [
                    'label' => esc_html__( 'Badge' ,'wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'separator' => 'before'
                ]
            );

            $this->add_control(
                'pt_badge_style_preset_' .$i,
                [
                    'label' => esc_html__('Badge Style Preset','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'style-1' => esc_html__( 'Badge Style 1','wts-eae' ),
                        'style-2' => esc_html__( 'Badge Style 2','wts-eae' ),
                        'style-3' => esc_html__( 'Badge Style 3','wts-eae' ),
                        'style-4' => esc_html__( 'Badge Style 4','wts-eae' ),
                        'style-5' => esc_html__( 'Badge Style 5','wts-eae' ),
                    ],
                    'default' => 'style-1',
                    'condition' => [
                        'pt_badge_style_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'pt_badge_text_' .$i,
                [
                    'label' => esc_html__('Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'New',
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'pt_badge_style_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'pt_badge_position_' .$i,
                [
                    'label' => esc_html__('Badge Position','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'left' => esc_html__('Left','wts-eae'),
                        'right' => esc_html__('Right','wts-eae'),
                    ],
                    'default' => 'left',
                    'condition' => [
                        'pt_badge_style_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'pt_badge_width_' . $i,
                [
                    'label' => esc_html__('Width','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'default' => [
                        'size' => '50',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'pt_badge_style_'. $i,
                                'operator' => '==',
                                'value' => 'yes'
                            ],
                            [
                                'name' => 'pt_badge_style_preset_'. $i,
                                'operator' => '==',
                                'value' => 'style-1',
                            ]
                        ],
                    ]
                ]
            );

            $this->add_responsive_control(
                'pt_badge_position_top_' .$i,
                [
                    'label' => esc_html__('Vertical Position','wts-eae'),
                    'type' =>Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 400,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'size_units' => ['px','%'],
                    'selectors' =>  [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left' => 'top:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right' => 'top:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-left' => 'top:{{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-right' => 'top:{{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-right' => 'top:{{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-left' => 'top:{{SIZE}}{{UNIT}}',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            	[
                               	 	'name' => 'pt_badge_style_' . $i,
                                	'operator' => '==',
                                	'value' => 'yes',
                            	],
                            	[
                                	'name' => 'pt_badge_style_preset_' .$i,
                                	'operator' => '!==',
                                	'value'=>'style-4'
                            	],
                            	[
                                	'name' => 'pt_badge_style_preset_' .$i,
                                	'operator' => '!==',
                                	'value'=>'style-5'
                            	],
                            	[
                                	'name' => 'pt_badge_style_preset_' .$i,
                                	'operator' => '!==',
                                	'value'=>'style-6'
                            	],
                        ]
                    ]
                ]
            );

            $this->add_responsive_control(
                'pt_badge_position_horizontal_' .$i,
                [
                    'label' => esc_html__('Horizontal Position','wts-eae'),
                    'type' =>Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 400,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'size_units' => ['px','%'],
                    'selectors' =>  [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left' => 'left:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right' => 'right:{{SIZE}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            	[
                               	 	'name' => 'pt_badge_style_' . $i,
                                	'operator' => '==',
                                	'value' => 'yes',
                            	],
                                [
                                	'name' => 'pt_badge_style_preset_' .$i,
                                	'operator' => '==',
                                	'value'=>'style-1'
                            	],
                        ]
                    ]
                ]
            );

            $this->add_control(
                'pt_badge_style_4_position_' . $i,
                [
                    'label' => esc_html__('Distance','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '25',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-apt-badge-style-4-inner-right' => 'top: {{SIZE}}px; left: calc(16px - {{SIZE}}px);',
                        '{{WRAPPER}} .eae-apt-badge-style-4-inner-left' => 'top: {{SIZE}}px; right: calc(16px - {{SIZE}}px);'
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            	[
                               	 	'name' => 'pt_badge_style_' . $i,
                                	'operator' => '==',
                                	'value' => 'yes',
                            	],
                            	[
                                	'name' => 'pt_badge_style_preset_' .$i,
                                	'operator' => '==',
                                	'value'=>'style-4'
                            	],
                        ]
                    ]
                ]
            );

           

            $this->add_control(
                'pt_badge_style_controls_heading_' . $i,
                [
                    'label' => esc_html__('Badge Style','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_badge_style_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'label' => esc_html__('Badge Typography','wts-eae'),
                    'name' => 'pt_badge_typography'.$i ,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-5',
                    'condition' => [
                        'pt_badge_style_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'pt_badge_color_' . $i,
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    // 'global' => [
                    //     'default' => Global_Colors::COLOR_TEXT,
                    // ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-left span' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-right span' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-right' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-left' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-right' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-left' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-5' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_badge_style_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pt_badge_background_' . $i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-right , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-left , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-5',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                        // 'color' => [
                        //     'global' => [
                        //         'default' => Global_Colors::COLOR_ACCENT,
                        //     ],
                        // ],
                    ],
                    'condition' => [
                        'pt_badge_style_' . $i => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'pt_badge_border_radius_'.$i,
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-left' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-left' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-5' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'pt_badge_style_'. $i,
                                'operator' => '==',
                                'value' => 'yes'
                            ],
                            [
                                'name' => 'pt_badge_style_preset_' .$i,
                                'operator' => '!==',
                                'value'=>'style-4'
                            ]
                        ],
                    ],
                ]
            );

            $this->add_responsive_control(
                'pt_badge_padding_' . $i,
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-left' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-1-right' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-left' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-2-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-right' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-3-left' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-5' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'pt_badge_style_'. $i,
                                'operator' => '==',
                                'value' => 'yes'
                            ],
                            [
                                'name' => 'pt_badge_style_preset_' .$i,
                                'operator' => '!==',
                                'value'=>'style-4' 
                            ],
                        ],
                    ],
                ]
            );

            $this->add_responsive_control(
                'pt_badge_style_4_padding_' .$i,
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem'],
                    'allowed_dimensions' => 'vertical',
                    'placeholder' => [
                        'top' => '',
                        'right' => '100',
                        'bottom' => '',
                        'left' => '100',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-right' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-badge-style-4-inner-left' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'pt_badge_style_'. $i,
                                'operator' => '==',
                                'value' => 'yes'
                            ],
                            [
                                'name' => 'pt_badge_style_preset_' .$i,
                                'operator' => '==',
                                'value'=>'style-4' 
                            ],
                        ],
                    ],
                ]
            );


            $this->end_controls_tab();

            $this->start_controls_tab(
                'pt_content_icon_tab_' . $i,
                [
                    'label' => esc_html__('Icon','wts-eae'),
                ]
            );

            Helper::eae_media_controls(
                $this,
                [
                    'name' => 'apt_' . $i,
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

            $this->end_controls_tab();

            $this->start_controls_tab(
                'pt_content_style_' . $i,
                [
                    'label' => esc_html__( 'Style','wts-eae' ),
                ]
            );

            $this->add_control(
                'pt_override_style_' . $i,
                [
                    'label' => esc_html__('Override Style','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_control(
                'pt_title_heading_' . $i,
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'pt_title_typography_' .$i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-title',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_title_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-title' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_title_hover_color_' .$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-title' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_description_heading_'.$i,
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'pt_description_typography_' .$i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-description',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_description_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-description' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );
            
            $this->add_control(
                'pt_description_hover_color_' .$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-description' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_prefix_heading_'.$i,
                [
                    'label' => esc_html__('Price Prefix','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_prefix_font_size_' .$i,
                [
                    'label' => esc_html__('Font Size','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-price-prefix' => 'font-size:{{VALUE}}px;',
                    ], 
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            

            $this->add_control(
                'pt_price_prefix_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-price-prefix' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_prefix_hover_text_color_'.$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-price-prefix' => 'color:{{VALUE}};', 
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_heading_'.$i,
                [
                    'label' => esc_html__('Price','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_font_size_' .$i,
                [
                    'label' => esc_html__('Font Size','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-price' => 'font-size:{{VALUE}}px;',
                    ], 
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

           

            $this->add_control(
                'pt_price_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-price' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_line_color_'.$i,
                [
                    'label'     => __( 'Line Through Color', 'wts-eae' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-descount-font' => 'text-decoration-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_hover_color_' .$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-price' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );


            $this->add_control(
                'pt_fractional_heading_'.$i,
                [
                    'label' => esc_html__('Fractional','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_fractional_size_' .$i,
                [
                    'label' => esc_html__('Font Size','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-fractional' => 'font-size:{{VALUE}}px;',
                    ], 
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            

            $this->add_control(
                'pt_fractional_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-fractional' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_fractional_hover_color_' .$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-fractional' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_sale_price_heading_'.$i,
                [
                    'label' => esc_html__('Sale Price','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_price_sale_size_' .$i,
                [
                    'label' => esc_html__('Font Size','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-sale-price' => 'font-size:{{VALUE}}px;',
                    ], 
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            

            $this->add_control(
                'pt_sale_price_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-sale-price' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_sale_price_hover_color_' .$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-sale-price' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_duration_heading_'.$i,
                [
                    'label' => esc_html__('Duration','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'pt_duration_typography_' .$i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-duration',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_duration_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-duration' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_duration_hover_color_' .$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-duration' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_features_list_style_heading_'.$i,
                [
                    'label' => esc_html__('Features','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'pt_features_typography_' .$i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-features-list-item',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_features_text_color_'.$i,
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-features-list-item' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_features_text_hover_color_'.$i,
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .':hover .eae-apt-features-list-item' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_tooltip_heading_'. $i ,
                [
                    'label' => esc_html__('Tooltip','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );
            
            $this->add_control(
                'pt_tooltip_color_' . $i ,
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip-on-icon' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ] 
                ]
            );

            $this->add_control(
                'tooltip_icon_color_'. $i,
                [
                    'label' => esc_html__('Icon Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip-icon i' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-feature-not-available-icon .eae-apt-tooltip-icon i' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ] 
                ]
            );

            $this->add_control(
                'tooltip_icon_hover_color_'. $i,
                [
                    'label' => esc_html__('Icon Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip-icon i:hover' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-feature-not-available-icon .eae-apt-tooltip-icon i:hover' => 'color:{{VALUE}};'
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ] 
                ]
            );

            $this->add_control(
                'pt_tooltip_arrow_color_' . $i ,
                [
                    'label' => esc_html__('Arrow Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip::after' => 'border-color: {{VALUE}} transparent transparent transparent;',
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip-on-icon::after' => 'border-color: {{VALUE}} transparent transparent transparent;' 
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );
    
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pt_tooltip_background_' . $i ,
                    'separator' => 'after',
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip , {{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-tooltip-on-icon',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_icon_style_heading_' . $i,
                [
                    'label' => esc_html__('Icon'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
				'pt_icon_primary_color_' . $i,
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-gbl-icon.eae-graphic-view-framed, {{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						'{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-gbl-icon.eae-graphic-view-framed, {{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-graphic-view-default svg' => 'fill: {{VALUE}};',
					],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
				]
			);
		
            $this->add_control(
                'pt_icon_secondary_color_'. $i,
                [
                    'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .' .eae-apt-icon.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_icon_hover_primary_color_'.$i,
                [
                    'label' => esc_html__( 'Primary Hover Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-framed , {{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-framed, {{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-defult svg' => 'fill: {{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );
    
            $this->add_control(
                'pt_icon_hover_secondary_color_'. $i,
                [
                    'label' => esc_html__( 'Secondary Hover Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i .':hover .eae-apt-icon.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );
            

            $this->add_control(
                'pt_button_style_heading_' . $i,
                [
                    'label' => esc_html__('Button','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ],
                ]
            );

            $this->add_control(
                'pt_button_text_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_button_border_color_' . $i,
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button' => 'border-color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pt_button_background_' . $i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_hover_button_style_control_' . $i,
                [
                    'label' => esc_html__('Button Hover','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_button_text_hover_color_' . $i,
                [
                    'label' => esc_html__( 'Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button:hover' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_button_hover_border_color_' . $i,
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button:hover' => 'border-color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pt_hover_button_background_' .$i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button:hover',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_button_box_heading_'.$i,
                [
                    'label' => esc_html__('Button Box','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );
    
            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pt_button_box_background_'.$i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button-container',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_button_box_border_color_'. $i,
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button-container' => 'border-color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ],
                ]
            );
    
    
            $this->add_responsive_control(
                'pt_button_box_padding_'.$i,
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i .' .eae-apt-button-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ] 
                ]
            );

            $this->add_control(
                'pt_style_heading_' . $i,
                [
                    'label' => esc_html__( 'Price Table','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pt_background_' . $i,
                    'selector' => '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i,
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'pt_shadow_' . $i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i,
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'pt_border_'. $i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i,
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ],
                ]
            );

            $this->add_responsive_control(
                'pt_border_radius_'.$i,
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-wrapper.eae-price-table-'. $i => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ], 
                ]
            );

            $this->add_responsive_control(
                'pt_padding_'.$i,
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-apt-tab-content-section .eae-price-table-'. $i => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ], 
                ]
            );

            $this->add_control(
                'pt_hover_controls_heading_'.$i,
                [
                    'label' => esc_html__('Price Table Hover','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'pt_border_color_hover_' . $i,
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-price-table-'. $i.':hover' => 'border-color:{{VALUE}};',
                    ],
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'pt_background_hover_color_'.$i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i.':hover',
                    'condition' => [
                        'pt_override_style_' . $i => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'pt_shadow_hover_' . $i,
                    'selector' => '{{WRAPPER}} .eae-price-table-'. $i.':hover',
                    'condition' => [
                        'pt_override_style_'. $i => 'yes'
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->end_controls_section();

        }

        $this->get_switcher_section();

        $this->get_section_settings();

        $this->get_price_table_style_section();

        $this->get_content_style_section();

        $this->get_price_style_section();

        $this->get_features_style_section();

        $this->get_button_style_section();

        $this->get_switcher_button_style_section();

        $this->get_switcher_radio_button_style_section();
        
        $this->get_box_style_section();
    }

    public function get_box_style_section(){
        $this->start_controls_section(
            'price_table_box_section',
            [
                'label' => esc_html__('Box','wts-eae'),
                'tab' =>  Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'pt_price_table_container_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_price_table_style_section(){
        $this->start_controls_section(
            'price_table_style_section',
            [
                'label' => esc_html__('Price Table','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE
            ]
        );

        $this->start_controls_tabs('price_table_tabs');

        $this->start_controls_tab(
            'price_table_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'price_table_background',
                'selector'=> '{{WRAPPER}} .eae-price-table-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'price_table_shadow',
                'selector'=> '{{WRAPPER}} .eae-price-table-wrapper',
               
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'price_table_border',
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selector'=> '{{WRAPPER}} .eae-price-table-wrapper',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'price_table_hover',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'price_table_hover_background',
                'selector'=> '{{WRAPPER}} .eae-price-table-wrapper:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'price_table_hover_shadow',
                'selector' => '{{WRAPPER}} .eae-price-table-wrapper:hover'
            ]
        );

        $this->add_control(
            'price_table_border_hover_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover' => 'border-color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'price_table_element_gap',
            [
                'label' => esc_html__('Content Gap (px)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '15',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper' => 'gap:{{SIZE}}px;',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'price_table_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'default' => [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_section_settings(){
        $this->start_controls_section(
            'pt_setting_section',
            [
                'label' => esc_html__('Settings','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_title_tag',
            [
                'label' => esc_html__( 'Title HTML Tag' , 'wts-eae'),
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

        $this->add_responsive_control(
            'pt_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '10',
                    'unit' => 'px',
                ],
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tab-1' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-tab-2' => 'gap: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $break_value_arr = $this->get_stacked_options();
        $default_device = array_key_first($break_value_arr);
        
        $this->add_control(
            'stacked_below',
            [
                'label' => __('Stacked Device', 'wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => $break_value_arr,
                'default' => $default_device,
            ]
        );

        $this->add_control(
            'pt_feature_not_available',
            [
                'label'=> esc_html__( 'Feature Not Available Icon', 'wts-eae' ),
                'type' => Controls_Manager::CHOOSE,
                'toggle' => false,
                'default' => 'icon',
                'options' => [
                    'line-through' => [
                        'title' => esc_html__('Line Through','wts-eae'),
                        'icon' => 'fas fa-strikethrough',
                    ],
                    'icon' => [
                        'title' => esc_html__('Icon','wts-eae'),
                        'icon' => 'eicon-editor-close',
                    ]
                ],
            ]
        );

        $this->add_control(
            'pt_tooltip_display',
            [
                'label' => esc_html__('Tooltip Display','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'default' => 'feature',
                'options' => [
                    'icon' => esc_html__('Icon','wts-eae'),
                    'feature' => esc_html__('Feature','wts-eae'),
                ],
            ]
        );

        $this->add_control(
            'pt_order_heading',
            [
                'label' => esc_html__('Order','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pt_icon_order',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => '1',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-icon-container' => 'order:{{VALUE}};',
                ] 
            ]
        );

        $this->add_control(
            'pt_title_order',
            [
                'label' => esc_html__('Title','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => '2',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-title-container' => 'order:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_description_order',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => '3',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-description' => 'order:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_price_order',
            [
                'label' => esc_html__('Price','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => '4',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-price-wrapper' => 'order:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_features_order',
            [
                'label' => esc_html__('Features','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => '5',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-container' => 'order:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_button_order',
            [
                'label' => esc_html__('Button','wts-eae'),
                'type' => Controls_Manager::NUMBER,
                'default' => '6',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button-container' => 'order:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_section();
    } 

    public function get_switcher_section(){

        $this->start_controls_section(
            'pt_tab_section',
            [
                'label' => esc_html__('Tab','wts-eae'),
                'condition' => [
                    'pt_tabs_show' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pt_tab_skin',
            [
                'label' => esc_html__('Skin','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'default' => 'skin-1',
                'options' => [
                    'skin-1' => esc_html__('Skin 1','wts-eae'),
                    'skin-2' => esc_html__('Skin 2','wts-eae'),
                    'skin-3' => esc_html__('Skin 3','wts-eae'),
                    'skin-4' => esc_html__('Skin 4','wts-eae'),
                ]
            ]
        );

        $this->add_control(
            'pt_active_tab',
            [
                'label' => esc_html__('Active Tab','wts-eae'),
                'type' => Controls_Manager::SELECT,
                'default' => 'tab-1',
                'options' => [
                    'tab-1' => esc_html__('Tab 1','wts-eae'),
                    'tab-2' => esc_html__('Tab 2','wts-eae'),
                ],
            ]
        );
        
        $this->add_control(
            'pt_tab_1_heading',
            [
                'label' => esc_html__('Tab 1','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        Helper::eae_media_controls(
            $this,
            [
                'name' => 'pt_tab_1',
                'label' => 'Icon',
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> false,
            ]
        );

        $this->add_control(
            'pt_tab_1_text',
            [
                'label' => esc_html__('Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Tab 1'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );



        $this->add_control(
            'pt_tab_2_heading',
            [
                'label' => esc_html__('Tab 2','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        Helper::eae_media_controls(
            $this,
            [
                'name' => 'pt_tab_2',
                'label' => 'Icon',
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> false,
            ]
        );

        $this->add_control(
            'pt_tab_2_text',
            [
                'label' => esc_html__('Text','wts-eae'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Tab 2'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();
    }

    public function get_content_style_section(){
        $this->start_controls_section(
            'pt_content_style_section',
            [
                'label' => esc_html__('Content','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'pt_title_style_heading',
            [
                'label' => esc_html__('Title','wts-eae'),
                'type' =>Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_title_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'selector' => '{{WRAPPER}} .eae-apt-title',
            ]
        );

        $this->add_control(
            'pt_title_align',
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
                    '{{WRAPPER}} .eae-apt-title' => 'text-align:{{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs('pt_title_style_tab');

        $this->start_controls_tab(
            'pt_title_style_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_title_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-title' => 'color:{{VALUE}}',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_title_style_hover_tab',
            [
                'label' => esc_html__('Hover','wts-eae')
            ]
        );

        $this->add_control(
            'pt_title_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-title' => 'color: {{VALUE}};', 
                ]
            ]
        );

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_content_background',
                'selector' => '{{WRAPPER}} .eae-apt-title-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_content_border',
                'selector' => '{{WRAPPER}} .eae-apt-title-container',
            ]
        );

        $this->add_responsive_control(
            'pt_content_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-title-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_title_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-title-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_title_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-title-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'pt_description_style_heading',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before', 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_description_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                ],
                'selector' => '{{WRAPPER}} .eae-apt-description',
            ]
        );

        $this->add_control(
            'pt_description_align',
            [
                'label'       => esc_html__( 'Text Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'toggle' => false,
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
                    '{{WRAPPER}} .eae-apt-description' => 'text-align:{{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs('pt_description_style_tab');

        $this->start_controls_tab(
            'pt_description_style_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_description_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-description' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_description_background',
                'selector' => '{{WRAPPER}} .eae-apt-description',
            ]
        );

        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'pt_description_style_hover_tab',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_description_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-description' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_description_hover_background',
                'selector' => '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-description',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_description_boder',
                'selector' => '{{WRAPPER}} .eae-apt-description',
            ]
        );

        $this->add_responsive_control(
            'pt_description_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_description_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'pt_icon_style_section',
            [
                'label' =>esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pt_icon_align',
            [
                'label'       => esc_html__( 'Icon Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
                'toggle' => false, 
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-icon-container' => 'justify-content:{{VALUE}};',
                ],
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'apt',
                'selector'      => '.eae-apt-icon',
                'hover_selector'      => '.eae-price-table-wrapper:hover .eae-apt-icon',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
            ]
        );

        $this->add_responsive_control(
            'pt_icon_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' =>Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-icon-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );
        
        $this->end_controls_section();
    }

    public function get_switcher_button_style_section(){

        $this->start_controls_section(
            'pt_switcher_style_section',
            [
                'label' => esc_html__('Tab','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pt_tabs_show' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_switcher_button_typography',
                'selector' => '{{WRAPPER}} .eae-apt-content-switch-button-text',
            ]
        );

        $this->start_controls_tabs('pt_switcher_style_tabs');

        $this->start_controls_tab(
            'pt_switcher_normal_tab',
            [
                'label' => esc_html('Normal'),
            ]
        );

        $this->add_control(
            'pt_switcher_button_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-content-switch-button-text' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_handle_color',
            [
                'label' => esc_html__('Handle Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'pt_tab_skin!' => 'skin-1',
                ],
                'global'    => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-slider:before' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-slider-skin-3:before' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-slider-skin-4:before' => 'background-color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_slider_color',
            [
                'label' => esc_html__('Slider Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'pt_tab_skin!' => 'skin-1',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-slider' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-slider-skin-3' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-slider-skin-4' => 'background-color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_background_color',
            [
                'label' => esc_html__('Background Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-content-switch-button' => 'background-color:{{VALUE}};'
                ],
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'pt_switcher_button_shadow',
                'selector' => '{{WRAPPER}} .eae-apt-content-switch-button',
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_switcher_border',
                'selector' => '{{WRAPPER}} .eae-apt-content-switch-button',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => '2',
                            'right' => '2',
                            'bottom' => '2',
                            'left' => '2',
                        ],
                    ],
                    'color' => [
                        'global' => [
                            'default' => Global_Colors::COLOR_PRIMARY,
                        ],
                    ],
                ],
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_switcher_active_tab',
            [
                'label' => esc_html__('Active','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_switcher_active_button_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-content-switch-button.active-button' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-content-switch-button.active-button .eae-apt-content-switch-button-text' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-content-switch-button-text.active-button' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-content-switch-button:hover .eae-apt-content-switch-button-text' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_active_handle_color',
            [
                'label' => esc_html__('Handle Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-pt-content-toggle-switch:checked + .eae-apt-slider:before' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-pt-content-toggle-switch:checked + .eae-apt-slider-skin-3:before' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-pt-content-toggle-switch:checked + .eae-apt-slider-skin-4:before' => 'background-color:{{VALUE}};',
                ],
                'condition' => [
                    'pt_tab_skin!' => 'skin-1',
                ],
            ]
        );

        $this->add_control(
            'pt_switcher_active_slider_color',
            [
                'label' => esc_html__('Slider Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'pt_tab_skin!' => 'skin-1',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-pt-content-toggle-switch:checked + .eae-apt-slider' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-pt-content-toggle-switch:checked + .eae-apt-slider-skin-3' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-pt-content-toggle-switch:checked + .eae-apt-slider-skin-4' => 'background-color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_active_background_color',
            [
                'label' => esc_html__('Background Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .active-button.eae-apt-content-switch-button' => 'background-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-content-switch-button:hover ' => 'background-color:{{VALUE}};',
                ],
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'pt_switcher_active_button_shadow',
                'selector' => '{{WRAPPER}} .active-button.eae-apt-content-switch-button',
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_border_active_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_ACCENT,
                ],
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ],
                'selectors' => [
                    '{{WRAPPER}} .active-button.eae-apt-content-switch-button' => 'border-color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-content-switch-button:hover' => 'border-color:{{VALUE}};',
                ] 
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'pt_switcher_button_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ],
                'default' => [
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '20',
                    'left' => '20',
                    'unit' => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-content-switch-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_switcher_button_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'default' => [
                    'top' => '5',
                    'right' => '20',
                    'bottom' => '5',
                    'left' => '20',
                    'unit' => 'px'
                ],
                'condition' => [
                    'pt_tab_skin' => 'skin-1',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-content-switch-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_button_icon_style_heading',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pt_switcher_button_icon_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-content-switch-button' => 'gap:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-content-switch-button-text' => 'gap:{{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'pt_switcher_button_icon_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type'        => Controls_Manager::CHOOSE,
				'default'     => 'row',
				'toggle' => false,
				'options'     => [
					'row-reverse' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-justify-start-h',
					],
					'row'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => ' eicon-justify-end-h',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-content-switch-button' => 'flex-direction:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-content-switch-button-text' => 'flex-direction:{{VALUE}};',
                ]
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'apt_tab',
                'selector'      => '.eae-apt-tab-icon',
                'hover_selector'      => '.eae-apt-tab-icon',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
            ]
        );

        $this->add_control(
            'pt_switcher_button_position',
            [
                'label'       => esc_html__( 'Button Alignment', 'wts-eae' ),
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
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-switch-container' => 'justify-content:{{VALUE}};'
                ] 
            ]
        );

        $this->add_responsive_control(
            'pt_switcher_button_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => '10',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-switch-container' => 'gap:{{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_switch_container_background',
                'selector' => '{{WRAPPER}} .eae-apt-switch-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'pt_switch_container_shadow',
                'selector' => '{{WRAPPER}} .eae-apt-switch-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_switch_container_border',
                'selector' => '{{WRAPPER}} .eae-apt-switch-container',
            ]
        );

        $this->add_responsive_control(
            'pt_switch_container_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-switch-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_switch_container_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-switch-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_switch_container_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-switch-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_switcher_radio_button_style_section(){
        
    }

    public function get_price_style_section(){
        $this->start_controls_section(
            'pt_price_style_section',
            [
                'label' => esc_html__('Price','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // add color contol for price
        $this->add_control(
            'pt_price_string_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-price-prefix' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-price' => 'color : {{VALUE}};',
                    '{{WRAPPER}} .eae-apt-duration' => 'color : {{VALUE}};',
                    '{{WRAPPER}} .eae-apt-descount-font' => 'text-decoration-color : {{VALUE}};',
                    '{{WRAPPER}} .eae-apt-sale-price' => 'color : {{VALUE}};',
                    '{{WRAPPER}} .eae-apt-fractional' => 'color : {{VALUE}};',
                ]
            ]
        );


        $this->add_control(
            'pt_price_prefix_heading',
            [
                'label' => esc_html__('Price Prefix','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        ); 

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_price_prefix_typography',
                'selector' => '{{WRAPPER}} .eae-apt-price-prefix',
            ]
        );

        $this->add_control(
			'pt_price_prefix_align',
			[
				'label'       => esc_html__( 'Vertical Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'flex-start',
				'toggle' => false,
				'options'     => [
					'flex-start' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],

				'selectors'   => [
					'{{WRAPPER}} .eae-apt-price-prefix' => 'align-self: {{VALUE}};',
				],
			]
		);

        $this->start_controls_tabs('pt_price_prefix_style_tab');

        $this->start_controls_tab(
            'pt_price_prefix_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_price_prefix_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                // 'global' => [
                //     'default' => Global_Colors::COLOR_SECONDARY,
                // ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-price-container .eae-apt-price-prefix' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_price_prefix_hover_tab',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_price_prefix_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-price-prefix' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'pt_price_heading',
            [
                'label' => esc_html__('Price','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_price_typography',
                'selector' => '{{WRAPPER}} .eae-apt-price',
            ]
        );

        $this->add_control(
			'pt_price_align',
			[
				'label'       => esc_html__( 'Vertical Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'flex-end',
				'toggle' => false,
				'options'     => [
					'flex-start' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],

				'selectors'   => [
					'{{WRAPPER}} .eae-apt-price' => 'align-self: {{VALUE}};',
				],
			]
		);

        $this->start_controls_tabs('pt_price_style_tab');

        $this->start_controls_tab(
            'pt_price_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_price_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                // 'global' => [
                //     'default' => Global_Colors::COLOR_SECONDARY,
                // ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-price-container .eae-apt-price' => 'color:{{VALUE}};'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_price_hover_tab',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_price_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-price' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'pt_price_line_color',
            [
                'label'     => __( 'Line Through Color', 'wts-eae' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-descount-font' => 'text-decoration-color: {{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_sale_price_heading',
            [
                'label' => esc_html__('Sale Price','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_sale_price_typography',
                'selector' => '{{WRAPPER}} .eae-apt-sale-price',
            ]
        );

        $this->start_controls_tabs('pt_sale_price_style_tab');

        $this->start_controls_tab(
            'pt_sale_price_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_sale_price_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-sale-price' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_sale_price_hover_tab',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_sale_price_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-sale-price' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'pt_fractional_heading',
            [
                'label' => esc_html__('Fractional'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_fractional_typography',
                'selector' => '{{WRAPPER}} .eae-apt-fractional',
            ]
        );

        $this->add_control(
			'pt_fractional_align',
			[
				'label'       => esc_html__( 'Vertical Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'flex-start',
				'toggle' => false,
				'options'     => [
					'flex-start' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],

				'selectors'   => [
					'{{WRAPPER}} .eae-apt-fractional' => 'align-self: {{VALUE}};',
				],
			]
		);

        $this->start_controls_tabs('pt_fractional_style_tabs');

        $this->start_controls_tab(
            'pt_fractional_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_fractional_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                // 'global' => [
                //     'default' => Global_Colors::COLOR_SECONDARY,
                // ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-fractional' => 'color: {{VALUE}};'
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_fractional_hover_tab',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_fractional_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-fractional' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'pt_duration_heading',
            [
                'label' => esc_html__('Duration','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_duration_typography',
                'selector' => '{{WRAPPER}} .eae-apt-duration'
            ]
        );

        $this->add_control(
			'pt_duration_vertical_align',
			[
				'label'       => esc_html__( 'Vertical Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'column',
                'toggle' => false,
				'options'     => [
					'column' => [
						'title' => __( 'Column', 'wts-eae' ),
						'icon'  =>  'eicon-justify-start-h',
					],
					'row'   => [
						'title' => __( 'Row', 'wts-eae' ),
						'icon'  => 'eicon-justify-end-v',
					],
				],
                'toggle' => false,
				'selectors'   => [
					'{{WRAPPER}} .eae-apt-price-wrapper' => 'flex-direction: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'pt_duration_horizontal_align',
			[
				'label'       => esc_html__( 'Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
                'toggle' => false,
				'options'     => [
					'flex-start' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  =>  'eicon-justify-start-h',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-justify-center-h',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-justify-end-h',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .eae-apt-duration' => 'align-self: {{VALUE}};',
				],
			]
		);

        $this->start_controls_tabs('pt_duration_style_tabs');

        $this->start_controls_tab(
            'pt_duration_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_duration_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                // 'global' => [
                //     'default' => Global_Colors::COLOR_SECONDARY,
                // ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-duration' => 'color: {{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_duration_hover_tab',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_duration_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-duration' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'pt_price_box_style_heading',
            [
                'label' => esc_html__('Price Box','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs('pt_price_box__style_tabs');

        $this->start_controls_tab(
            'pt_price_box_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_price_background',
                'selector' => '{{WRAPPER}} .eae-apt-price-wrapper',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_price_box_hover_tab',
            [
                'label' =>esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_price_box_hover_background',
                'selector' => '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-price-wrapper',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'pt_price_element_align',
            [
                'label'       => esc_html__( 'Text Alignment', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
                'toggle' => false,
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
                    '{{WRAPPER}} .eae-apt-price-wrapper' => 'justify-content:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-price-container' => 'justify-content:{{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_price_border',
                'selector' => '{{WRAPPER}} .eae-apt-price-wrapper',   
            ]
        );

        $this->add_responsive_control(
            'pt_price_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-price-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_price_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-price-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_price_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-price-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }

    public function get_button_style_section(){
        $this->start_controls_section(
            'pt_button_style_section',
            [
                'label' => esc_html__('Button','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_button_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .eae-apt-button',
            ]
        );
        
        $this->add_control(
            'pt_button_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type'        => Controls_Manager::CHOOSE,
                'default'     => 'center',
                'toggle' => false,
                'options'     => [
                    'flex-start' => [
                        'title' => __( 'Left', 'wts-eae' ),
                        'icon'  => 'eicon-justify-start-h',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'wts-eae' ),
                        'icon'  => 'eicon-justify-center-h',
                    ],
                    'flex-end'   => [
                        'title' => __( 'Right', 'wts-eae' ),
                        'icon'  => ' eicon-justify-end-h',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button-container' => 'align-items:{{VALUE}};',
                ]
            ]
        );

        $this->start_controls_tabs('pt_button_style_tabs');

        $this->start_controls_tab(
            'pt_button_style_normal_tab',
            [
                'label' => esc_html__('Normal','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_button_text_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_button_background',
                'selector' => '{{WRAPPER}} .eae-apt-button',
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
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_button_border',
                'selector' => '{{WRAPPER}} .eae-apt-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'pt_button_shadow',
                'selector' => '{{WRAPPER}} .eae-apt-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pt_button_hover',
            [
                'label' => esc_html__('Hover','wts-eae'),
            ]
        );

        $this->add_control(
            'pt_button_hover_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button-container:hover .eae-apt-button' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_button_hover_background',
                'selector' => '{{WRAPPER}} .eae-apt-button-container:hover .eae-apt-button'
            ]
        );

        $this->add_control(
            'pt_button_hover_border_color',
            [
                'label' => esc_html__('Border Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectros' => [
                    '{{WRAPPER}} .eae-apt-button-container:hover .eae-apt-button' => 'border-color: {{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'pt_button_hover_shadow',
                'selector' => '{{WRAPPER}} .eae-apt-button-container:hover .eae-apt-button',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'pt_button_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', 
                ] 
            ]
        );

        $this->add_responsive_control(
            'pt_button_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_button_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'pt_button_icon_style_heading',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pt_button_icon_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type'        => Controls_Manager::CHOOSE,
				'default'     => 'row',
				'toggle' => false,
				'options'     => [
					'row-reverse' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-justify-start-h',
					],
					'row'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => ' eicon-justify-end-h',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button' => 'flex-direction:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_button_icon_align',
            [
                'label' => esc_html__('Icon Alignment','wts-eae'),
                'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'toggle' => false,
				'options'     => [
					'start' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-justify-start-v',
					],
                    'center'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => ' eicon-justify-center-v',
					],
					'end'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => ' eicon-justify-end-v',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button-icon' => 'align-self:{{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_button_icon_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => '5',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button' => 'column-gap:{{SIZE}}{{UNIT}};',
                ]
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'pt_button_icon',
                'selector'      => ' .eae-apt-button-icon',
                'hover_selector'      => '.eae-apt-button-container:hover .eae-apt-button-icon',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
            ]
        );

        $this->add_control(
            'pt_button_box_heading',
            [
                'label' => esc_html__('Box','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_button_box_background',
                'selector' => '{{WRAPPER}} .eae-apt-button-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_button_box_border',
                'selector' => '{{WRAPPER}} .eae-apt-button-container',
            ]
        );

        $this->add_responsive_control(
            'pt_button_box_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ] 
            ]
        );

        $this->add_responsive_control(
            'pt_button_box_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-button-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ] 
            ]
        );

        $this->end_controls_section();
    }

    public function get_features_style_section(){

        $this->start_controls_section(
            'pt_features_style_section',
            [
                'label' => esc_html__('Features','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_features_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .eae-apt-features-list-item',
            ]
        );

        $this->add_control(
            'pt_features_align',
            [
                'label' => esc_html__('Alignment','wts-eae'),
                'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'toggle' => false,
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
						'icon'  => ' eicon-justify-end-h',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-list-item' => 'justify-content:{{VALUE}}; text-align:{{VALUE}};',
                ] 

            ]
        );

        $this->add_control(
            'pt_features_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-list-item' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_control(
            'pt_features_hover_color',
            [
                'label' => esc_html__('Hover Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-price-table-wrapper:hover .eae-apt-features-list-item' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_features_background',
                'selector' => '{{WRAPPER}} .eae-apt-features-list-item',
            ]
        );

        $this->add_responsive_control(
            'pt_features_gap',
            [
                'label' => esc_html__('Gap (px)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '10'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-container' => 'row-gap:{{SIZE}}px;',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_features_border',
                'selector' => '{{WRAPPER}} .eae-apt-features-list-item',
            ]
        );

        $this->add_responsive_control(
            'pt_features_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_features_margin',
            [
                'label' => esc_html__('Margin','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'pt_features_icon_style_heading',
            [
                'label' => esc_html__('Icon','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pt_features_icon_align',
            [
                'label' => esc_html__('Icon Alignment','wts-eae'),
                'type'        => Controls_Manager::CHOOSE,
				'default'     => 'center',
				'toggle' => false,
				'options'     => [
					'start' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-justify-start-v',
					],
					'center'     => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-justify-center-v',
					],
					'end'   => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => ' eicon-justify-end-v',
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-list-item' => 'align-items:{{VALUE}};',
                ] 

            ]
        );

        $this->add_responsive_control(
            'pt_features_icon_gap',
            [
                'label' => esc_html__('Icon Gap (px)','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%'],
                'default' => [
                    'size' => '10',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-features-list-item' => 'column-gap:{{SIZE}}{{UNIT}};',
                ] 
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'apt_feature',
                'selector'      => '.eae-apt-feature-icon',
                'hover_selector'      => '.eae-price-table-wrapper:hover .eae-apt-feature-icon',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
            ]
        );

        $this->add_control(
            'pt_tooltip_heading' ,
            [
                'label' => esc_html__('Tooltip','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'preview_details_on_hover',
            array(
                'label'        => __( 'Preview Tooltip Content', 'wts-eae' ),
                'description'  => __('It is only for editor preview. Its helps you to design your layout properly', 'wts-eae'),
                 'type'         => Controls_Manager::SWITCHER,
                'label_off'    => __( 'No', 'wts-eae' ),
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'default'      => 'no',
                'return_value' => 'yes',
            )
        );

        $this->add_control(
            'pt_tooltip_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'default' => [
                    'size' => '5',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip-icon-container' => 'gap:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'pt_tooltip_display' => 'icon',
                ]
            ]
        );

        $this->add_control(
            'pt_tooltip_icon_size',
            [
                'label' => esc_html__('Icon Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_unit' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip-icon i' => 'font-size:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-feature-not-available-icon .eae-apt-tooltip-icon i' => 'font-size:{{SIZE}}{{UNIT}};'
                ],
                'condition' => [
                    'pt_tooltip_display' => 'icon',
                ]
            ]
        );

        $this->add_control(
            'pt_tooltip_icon_alignment',
            [
                'label' => esc_html__('Icon Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'row',
                'toggle' => false,
                'options' => [
                    'row-reverse' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => ' eicon-justify-start-h'
                    ],
                    'row' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h'
                    ]
                ],
                'condition' => [
                    'pt_tooltip_display' => 'icon',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip-icon-container' => 'flex-direction:{{VALUE}};'
                ]
            ]
        );

        $this->add_control(
            'pt_tooltip_icon_color',
            [
                'label' => esc_html__('Icon Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip-icon i' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-feature-not-available-icon .eae-apt-tooltip-icon i' => 'color:{{VALUE}};'
                ],
                'condition' => [
                    'pt_tooltip_display' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'pt_tooltip_icon_hover_color',
            [
                'label' => esc_html__('Icon Hover Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip-icon i:hover' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-feature-not-available-icon .eae-apt-tooltip-icon i:hover' => 'color:{{VALUE}};'
                ],
                'condition' => [
                    'pt_tooltip_display' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'pt_tooltip_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' =>Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 400,
						'step' => 10,
					],
                    '%' => [
                        'min'  => 0,
						'max'  => 100,
						'step' => 5,
                    ]
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip' => 'width:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-tooltip-on-icon' => 'width:{{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pt_tooltip_typography',
                'selector' => '{{WRAPPER}} .eae-apt-tooltip , {{WRAPPER}} .eae-apt-tooltip-on-icon',
            ]
        );
        
        $this->add_control(
            'pt_tooltip_color' ,
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-tooltip-on-icon' => 'color:{{VALUE}};',
                ] 
            ]
        );

        $this->add_control(
            'pt_tooltip_arrow_color' ,
            [
                'label' => esc_html__('Arrow Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip::after' => 'border-color: {{VALUE}} transparent transparent transparent;',
                    '{{WRAPPER}} .eae-apt-tooltip-on-icon::after' => 'border-color: {{VALUE}} transparent transparent transparent;',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_tooltip_background' ,
                'selector' => '{{WRAPPER}} .eae-apt-tooltip , {{WRAPPER}} .eae-apt-tooltip-on-icon',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pt_tooltip_border',
                'selector' => '{{WRAPPER}} .eae-apt-tooltip , {{WRAPPER}} .eae-apt-tooltip-on-icon',
            ]
        );

        $this->add_responsive_control(
            'pt_tooltip_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-tooltip-on-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_tooltip_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-tooltip-on-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        ); 

        $this->add_control(
            'pt_feature_not_available_style_heading',
            [
                'label' => esc_html__('Features Not Available','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pt_feature_not_available_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-feature-not-available' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-feature-not-available-icon' => 'color:{{VALUE}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'pt_feature_not_available_icon_size',
            [
                'label' => esc_html__('Icon Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-feature-not-available-icon i' => 'font-size:{{SIZE}}{{UNIT}};',
                ]  
            ]
        );

        $this->add_control(
            'pt_feature_not_available_icon_color',
            [
                'label' => esc_html__('Icon Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-feature-not-available-icon i' => 'color:{{VALUE}};',
                ]
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'badge_style_section',
            [
                'label' => esc_html__('Badge Style','wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'pt_badge_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-badge-style-1-left' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-1-right' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-2-left span' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-2-right span' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-3-right' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-3-left' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-4-inner-right' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-4-inner-left' => 'color:{{VALUE}};',
                    '{{WRAPPER}} .eae-apt-badge-style-5' => 'color:{{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pt_badge_background',
                'selector' => '{{WRAPPER}} .eae-apt-badge-style-1-left , {{WRAPPER}} .eae-apt-badge-style-1-right , {{WRAPPER}} .eae-apt-badge-style-2-left , {{WRAPPER}} .eae-apt-badge-style-2-right , {{WRAPPER}} .eae-apt-badge-style-3-right , {{WRAPPER}} .eae-apt-badge-style-3-left , {{WRAPPER}} .eae-apt-badge-style-4-inner-right , {{WRAPPER}} .eae-apt-badge-style-4-inner-left , {{WRAPPER}} .eae-apt-badge-style-5',
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
            ]
        );

        $this->add_responsive_control(
            'pt_badge_border_radius',
            [
                'label' => esc_html__('Border Radius','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-badge-style-1-left' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-1-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-2-left' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-2-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-3-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-3-left' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-5' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pt_badge_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-badge-style-1-left' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-1-right' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-2-left' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-2-right' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-3-right' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-3-left' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-5' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pt_badge_style_4_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'allowed_dimensions' => 'vertical',
                'placeholder' => [
                    'top' => '',
                    'right' => '100',
                    'bottom' => '',
                    'left' => '100',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-apt-badge-style-4-inner-right' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
                    '{{WRAPPER}} .eae-apt-badge-style-4-inner-left' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
                ],
                
            ]
        );

        $this->end_controls_section();
    }

    public function add_condition_value( $j ) {
		$value = [];
		for ( $i = $j; $i < 11; $i++ ) {
			$value[] = $i;
		}

		return $value;
	}

    public function add_icon_class($icon_class, $data, $control_name){
        if(isset($data['apt_hover_animation']) && $data['apt_hover_animation'] != ''){
            $icon_class[] = 'elementor-animation-'.$data['apt_hover_animation'];
        }
        return $icon_class;
    }

    public function get_tab_control_buttons(){
        $settings = $this->get_settings_for_display();
        $tab_1_button = '';
        $tab_2_button = '';
        $tab_1_label = '';
        $tab_2_label = '';
        $checked = '';
        if($settings['pt_active_tab'] == 'tab-2'){
            $tab_2_button = 'active-button';
            $tab_2_label = 'active-button';
            $checked = 'checked';
        }else{
            $tab_1_button = 'active-button';
            $tab_1_label = 'active-button';
        }

        $this->add_render_attribute('pt-tab-1-button','class',['eae-apt-content-switch-button', $tab_1_button]);
        $this->add_render_attribute('pt-tab-1-button','data-active-tab','tab-1');

        $this->add_render_attribute('pt-tab-2-button','class',['eae-apt-content-switch-button', $tab_2_button]);
        $this->add_render_attribute('pt-tab-2-button','data-active-tab','tab-2');


        if($settings['pt_tab_skin'] == 'skin-1'){
            if($settings['pt_tab_1_text'] !== '' || $settings['pt_tab_1_graphic_type'] !== 'none') {?>
                <a href=""  <?php echo $this->get_render_attribute_string('pt-tab-1-button'); ?>>
                    <?php if($settings['pt_tab_1_text'] !== '') { ?>
                        <span class="eae-apt-content-switch-button-text" ><?php echo Helper::eae_wp_kses($settings['pt_tab_1_text']); ?></span>
                    <?php } ?>
                    <?php Helper::render_icon_html($settings,$this, 'pt_tab_1', 'eae-apt-tab-icon'); ?>
                </a>
            <?php }
            if($settings['pt_tab_2_text'] !== '' || $settings['pt_tab_2_graphic_type'] !== 'none') { ?>
                <a href="" <?php echo $this->get_render_attribute_string('pt-tab-2-button'); ?>>
                    <?php if($settings['pt_tab_2_text'] !== '') { ?>
                        <span class="eae-apt-content-switch-button-text" ><?php echo Helper::eae_wp_kses($settings['pt_tab_2_text']); ?></span>
                    <?php } ?>
                    <?php Helper::render_icon_html($settings,$this, 'pt_tab_2', 'eae-apt-tab-icon'); ?>
                </a>
            <?php } 
        }else{
            $this->add_render_attribute('pt-tab-1-label','class',['eae-apt-content-switch-button-text','eae-label-tab-1',$tab_1_label]);
            $this->add_render_attribute('pt-tab-2-label','class',['eae-apt-content-switch-button-text','eae-label-tab-2',$tab_2_label]);
            if($settings['pt_tab_skin'] == 'skin-2'){
                $this->add_render_attribute('pt-tab-slider','class','eae-apt-slider');
            }else if($settings['pt_tab_skin'] == 'skin-3'){
                $this->add_render_attribute('pt-tab-slider','class','eae-apt-slider-skin-3');
            }
            else if($settings['pt_tab_skin'] == 'skin-4'){
                $this->add_render_attribute('pt-tab-slider','class','eae-apt-slider-skin-4');
            }
            ?>
                <?php if($settings['pt_tab_1_text'] !== '') { ?>
                    <span <?php echo $this->get_render_attribute_string('pt-tab-1-label'); ?> ><?php echo Helper::eae_wp_kses($settings['pt_tab_1_text']); ?><?php Helper::render_icon_html($settings,$this, 'pt_tab_1', 'eae-apt-tab-icon'); ?></span>
                <?php } ?>
                <div class="eae-apt-button-switch-container">
                    <label class="eae-apt-switch-label">
                        <input class="eae-pt-content-toggle-switch" type="checkbox" <?php echo $checked; ?> >
                        <span <?php echo $this->get_render_attribute_string('pt-tab-slider'); ?>></span>
                    </label>
                </div>
                <?php if($settings['pt_tab_2_text'] !== '') { ?>
                    <span <?php echo $this->get_render_attribute_string('pt-tab-2-label'); ?> ><?php echo Helper::eae_wp_kses($settings['pt_tab_2_text']); ?><?php Helper::render_icon_html($settings,$this, 'pt_tab_2', 'eae-apt-tab-icon'); ?></span>
                <?php } ?>
            <?php
        }
    }

    protected function content_template() {
        ?>
        <#
            function getFilterButton(){
                var tab_1_button = '';
                var tab_2_button = '';
                var tab_1_label = '';
                var tab_2_label = '';
                var checked = '';
                if(settings.pt_active_tab == 'tab-2'){
                    tab_2_button = 'active-button';
                    tab_2_label = 'active-button';
                    checked = 'checked';
                }else{
                    tab_1_button = 'active-button';
                    tab_1_label = 'active-button';
                }
                view.addRenderAttribute('pt-tab-1-button','class',['eae-apt-content-switch-button', tab_1_button]);
                view.addRenderAttribute('pt-tab-1-button','data-active-tab','tab-1');

                view.addRenderAttribute('pt-tab-2-button','class',['eae-apt-content-switch-button', tab_2_button]);
                view.addRenderAttribute('pt-tab-2-button','data-active-tab','tab-2');
                if(settings.pt_tab_skin == 'skin-1'){
                    if(settings.pt_tab_1_text !== '' || settings.pt_tab_1_graphic_type !== 'none'){
                        #>
                        <a href=""  {{{view.getRenderAttributeString('pt-tab-1-button')}}}>
                            <# if(settings.pt_tab_1_text !== '') { #>
                                <span class="eae-apt-content-switch-button-text" >{{{settings.pt_tab_1_text}}}</span>
                            <# } #>  
                            <#
                                iconHtml = window.renderIconHtml(view,elementor,settings, 'pt_tab_1', 'eae-apt-tab-icon' , 1);
                                if(iconHtml != ''){
                                    print(iconHtml);
                                }
                            #>
                              
                        </a>
                        <#
                    }
                    if(settings.pt_tab_2_text !== '' || settings.pt_tab_2_graphic_type !== 'none'){
                        #>
                        <a href="" {{{view.getRenderAttributeString('pt-tab-2-button')}}}>
                            <# if(settings.pt_tab_2_text !== '') { #>
                                <span class="eae-apt-content-switch-button-text" >{{{settings.pt_tab_2_text}}}</span>
                            <# } #>
                            <#
                                iconHtml = window.renderIconHtml(view,elementor,settings, 'pt_tab_2', 'eae-apt-tab-icon', 2);
                                if(iconHtml != ''){
                                    print(iconHtml);
                                }
                            #>  
                        </a>
                        <#
                    }
                }else{
                    view.addRenderAttribute('pt-tab-1-label','class',['eae-apt-content-switch-button-text','eae-label-tab-1',tab_1_label]);
                    view.addRenderAttribute('pt-tab-2-label','class',['eae-apt-content-switch-button-text','eae-label-tab-2',tab_2_label]);
                    if(settings.pt_tab_skin == 'skin-2'){
                        view.addRenderAttribute('pt-tab-slider','class','eae-apt-slider');
                    }else if(settings.pt_tab_skin == 'skin-3'){
                        view.addRenderAttribute('pt-tab-slider','class','eae-apt-slider-skin-3');
                    }
                    else if(settings.pt_tab_skin == 'skin-4'){
                        view.addRenderAttribute('pt-tab-slider','class','eae-apt-slider-skin-4');
                    }
                    if(settings.pt_tab_1_text !== '') { #>
                        <span {{{view.getRenderAttributeString('pt-tab-1-label')}}} >
                            <#    
                                print(settings.pt_tab_1_text);
                                iconHtml = window.renderIconHtml(view,elementor,settings, 'pt_tab_1', 'eae-apt-tab-icon', 1);
                                if(iconHtml != ''){
                                    print(iconHtml);
                                } 
                            #>
                        </span>   
                    <# } #>
                    <div class="eae-apt-button-switch-container">
                    <label class="eae-apt-switch-label">
                        <input class="eae-pt-content-toggle-switch" type="checkbox" {{{checked}}}>
                        <span {{{view.getRenderAttributeString('pt-tab-slider')}}} ></span>
                    </label>
                </div>
                <# if(settings.pt_tab_2_text !== '') { #>
                    <span {{{view.getRenderAttributeString('pt-tab-2-label')}}}>
                        <#
                        print(settings.pt_tab_2_text);
                        iconHtml = window.renderIconHtml(view,elementor,settings, 'pt_tab_2', 'eae-apt-tab-icon', 2);
                        if(iconHtml != ''){
                            print(iconHtml);
                        } #>
                    </span>
                <# } 
                
                }

            }

            function get_badge(badgeText, badgeStyle, badgePosition){
                if(badgeText == ''){
                    return;
                }
                var html = '';
                switch(badgeStyle){
                    case 'style-1' :    html = '<div class="eae-apt-badge-style-1-'+ badgePosition +'"><span>'+ badgeText +'</span></div>';
                                        break;
                    case 'style-2' :    html = '<div class="eae-apt-badge-style-2-'+ badgePosition +'"><span>'+ badgeText +'</span></div>';
                                        break;
                    case 'style-3' :    html = '<span class="eae-apt-badge-style-3-'+ badgePosition +'">' + badgeText + '</span>';
                                        break;
                    case 'style-4' :    html = '<div class="eae-apt-badge-style-4-' + badgePosition + '"><span class="eae-apt-badge-style-4-inner-'+ badgePosition +'">' + badgeText + '</span></div>';
                                        break;
                    case 'style-5' :    html = '<span class="eae-apt-badge-style-5">' + badgeText +'</span>';
                                        break;
                }
                print(html);
            }
            
            function get_price(price_data){
                var price = price_data.split( '.' );
                var fractional_price = [];
                fractional_price.push(price[0])
                if ( price.length > 1 ) {
                    fractional_price.push('<span class="eae-apt-fractional">' + price[1] +'</span>');
                }
                return fractional_price;   
            }

            function getPriceTable(condition){
                for (let i = 1; i <= settings.pt_number_of_price_table; i++) {
                        let tab = 'pt_tab_' + i;
                    if(settings[tab] == null || settings[tab] == undefined){
                        settings[tab] = 'tab-1';
                    }
                    if(settings[tab] === condition){
                        
                        view.addRenderAttribute('price-table-wrapper-'+i, 'class', 'eae-price-table-wrapper');
                        view.addRenderAttribute('price-table-wrapper-'+i, 'class', 'eae-price-table-'+i);
                        #>
                        <div {{{view.getRenderAttributeString('price-table-wrapper-'+i)}}}>
                            <#
                                if(settings['apt_' + i + '_graphic_type'] != 'none'){ #>
                                    <div class="eae-apt-icon-container">
                                        <#
                                        iconHtml = window.renderIconHtml(view,elementor,settings, 'apt_'+i, 'eae-apt-icon', i);
                                        if(iconHtml != ''){
                                            print(iconHtml);
                                        }
                                        #>
                                    </div>
                                <# } 
                                 if(settings['pt_title_' + i] != ''){ 
                                    let titleTag = window.eae.validateHTMLTag(settings.pt_title_tag, null, 'h2');
                                    #>
                                    <div class="eae-apt-title-container">
                                        <#
                                            view.addRenderAttribute('apt-title-'+i,'class','eae-apt-title');
                                            pt_title = '<' + titleTag + ' ' + view.getRenderAttributeString('apt-title-'+i) + '>' + settings['pt_title_' + i] + '</' + titleTag + '>';
                                            print(pt_title);
                                        #>
                                    </div>
                                <# } 
                                if(settings['pt_description_' + i] != ''){ 
                                    print('<span class="eae-apt-description">' + settings['pt_description_' + i] + '</span>')
                                } 
                                if(settings['pt_duration_' + i] !== '' || settings['pt_sale_price_' + i] !== '' || settings['pt_price_prefix_' + i] !== '' || settings['pt_sale_price_' + i] !== ''){ #>
                                    <div class="eae-apt-price-wrapper">
                                        <#
                                            if(settings['pt_sale_price_' + i] !== '' || settings['pt_price_prefix_' + i] !== '' || settings['pt_sale_price_' + i] !== ''){ #>
                                                <div class="eae-apt-price-container">
                                                    <#
                                                        if(settings['pt_sale_price_' + i] !== '' && settings['pt_price_' + i] !== ''){
                                                            print('<span class="eae-apt-price eae-apt-descount-font">' + settings['pt_price_prefix_' + i] + settings['pt_price_' + i] + '</span>');
                                                        }
                                                        if(settings['pt_price_prefix_' + i] !== ''){
                                                            print('<span class="eae-apt-price-prefix">' + settings['pt_price_prefix_' + i] + '</span>');
                                                        }
                                                        if(settings['pt_sale_price_' + i] == ''){
                                                            if(settings['pt_price_' + i] !== ''){
                                                                price = get_price(settings[ 'pt_price_' + i]);
                                                                print('<span class="eae-apt-price eae-apt-price-font">' + price[0] + '</span>');
                                                            }
                                                        }else{
                                                            price = get_price(settings[ 'pt_sale_price_' + i]);
                                                            print('<span class="eae-apt-sale-price eae-apt-price-font">' + price[0] + '</span>');
                                                        }
                                                        if(price[1] != ''){
                                                            print(price[1]);
                                                        }
                                                    #>
                                                </div>
                                            <# } 
                                            if(settings['pt_duration_' + i] !== ''){ 
                                                print('<span class="eae-apt-duration">' + settings['pt_duration_' + i] + '</span>')
                                             } #>
                                    </div>
                                <# }
                                if(settings['pt_items_' + i].length > 0){ #>
                                    <div class="eae-apt-features-container">
                                        <#
                                            var flag = 0;
                                            <!-- each loop over items -->
                                            _.each(settings['pt_items_' + i], function(item, index){
                                                var class_name = [];
                                                view.addRenderAttribute('apt-feature-list-item-'+index,'class','eae-apt-features-list-item');
                                                view.addRenderAttribute('apt-feature-list-item-'+index,'class','eae-repeater-item-'+item._id);
                                                if(item.pt_feature_not_avalabel == 'yes'){
                                                    if(settings.pt_feature_not_available == 'icon'){
                                                        view.addRenderAttribute('apt-feature-list-item-'+index,'class','eae-apt-feature-not-available-icon');
                                                    }else{
                                                        view.addRenderAttribute('apt-feature-list-item-'+index,'class','eae-apt-feature-not-available');
                                                    }    
                                                }
                                                if(item.pt_features !== '' || item.pt_feature_icon_graphic_type !== 'none' || (settings.pt_feature_not_available == 'icon' && item.pt_feature_not_avalabel == 'yes')){
                                                    #><span {{{view.getRenderAttributeString('apt-feature-list-item-'+index)}}}>
                                                    <#
                                                    if(item.pt_feature_icon_graphic_type !== 'none'){
                                                        if(settings.pt_feature_not_available == 'icon' && item.pt_feature_not_avalabel == 'yes'){
                                                            var icon = {
                                                                value   : 'fas fa-times',
                                                                library : 'fa-solid',
                                                            }
                                                            var iconHTML = elementor.helpers.renderIcon( view, icon , { 'aria-hidden': true }, 'i' , 'object' );
                                                            if(iconHTML != ''){
                                                                print(iconHTML.value);
                                                            }
                                                        }else{
                                                            var iconHtml = window.renderIconHtml(view,elementor,item,'pt_feature_icon', 'eae-apt-feature-icon',index);
                                                            if(iconHtml != ''){
                                                                print(iconHtml);
                                                            }
                                                        }
                                                    }else if(settings.pt_feature_not_available == 'icon' && item.pt_feature_not_avalabel == 'yes'){
                                                        var icon = {
                                                                value   : 'fas fa-times',
                                                                library : 'fa-solid',
                                                            }
                                                            var iconHTML = elementor.helpers.renderIcon( view, icon , { 'aria-hidden': true }, 'i' , 'object' );
                                                            if(iconHTML != ''){
                                                                print(iconHTML.value);
                                                            }
                                                    }
                                                    if(item.pt_features_tooltip_text !== ''){
                                                        flag++;
                                                         if(settings.pt_tooltip_display == 'icon'){
                                                            var tooltip_icon = {
                                                                value  : 'fas fa-info-circle',
                                                                library : 'fa-solid',
                                                            };
                                                            #>
                                                            <div class="eae-apt-tooltip-icon-container">
                                                                <#
                                                                    print(item.pt_features);
                                                                    #>
                                                                    <div class="eae-apt-tooltip-icon">
                                                                        <#
                                                                            var tooltipiconHTML = elementor.helpers.renderIcon( view, tooltip_icon , { 'aria-hidden': true }, 'i' , 'object' );
                                                                            if(iconHTML != ''){
                                                                                print(tooltipiconHTML.value);
                                                                            }
                                                                            view.addRenderAttribute('apt-tooltip-on-icon','class','eae-apt-tooltip-on-icon');
                                                                            if(settings.preview_details_on_hover == 'yes' && flag == 1){
                                                                                view.addRenderAttribute( 'apt-tooltip-on-icon', 'class', 'eae-apt-tooltip-preview');   
                                                                            }
                                                                            #>
                                                                            <span {{{view.getRenderAttributeString('apt-tooltip-on-icon')}}} >
                                                                                {{{item.pt_features_tooltip_text}}}
                                                                            </span><#
                                                                        #>
                                                                    </div>
                                                                    <#
                                                                #>
                                                                </div>
                                                               <# 
                                                         }else{
                                                            print(item.pt_features);
                                                            view.addRenderAttribute('apt-tooltip','class','eae-apt-tooltip');
                                                            if(settings.preview_details_on_hover == 'yes' && flag == 1){
                                                                view.addRenderAttribute( 'apt-tooltip', 'class', 'eae-apt-tooltip-preview');
                                                            }
                                                            #>
                                                            <span {{{view.getRenderAttributeString('apt-tooltip')}}} >
                                                                {{{item.pt_features_tooltip_text}}}
                                                            </span><#
                                                         }
                                                    }else{
                                                        print(item.pt_features);
                                                    }
                                                    #>
                                                    </span>
                                                    <#
                                                }
                                            });
                                        #>
                                    </div>
                                <# }
                                if( settings['pt_button_text_' + i] !== '' || settings['pt_button_icon_' + i + '_graphic_type'] !== 'none'){
                                    view.addRenderAttribute('apt_button_' + i, 'class', 'eae-apt-button');
                                    view.addRenderAttribute('apt_button_' + i, 'href', _.escape(settings['pt_button_url_'+ i]['url']));
                                #>
                                    <div class="eae-apt-button-container">
                                        <a {{{view.getRenderAttributeString('apt_button_' + i)}}}>
                                            {{{ settings['pt_button_text_'+ i]}}}
                                            <#
                                                let iconHtml = window.renderIconHtml(view,elementor,settings, 'pt_button_icon_'+i, 'eae-apt-icon', i);
                                                if(iconHtml != ''){
                                                    print(iconHtml);
                                                }
                                            #>
                                        </a>
                                    </div>
                                <# }

                                if( settings['pt_badge_style_' + i ] !== ''){
                                    get_badge(settings['pt_badge_text_' + i], settings['pt_badge_style_preset_' + i] , settings['pt_badge_position_' + i] );
                                }
                            #>
                        </div>
                        <#
                    }
                }
            }

            

            var tab1 = '';
            var tab2 = '';
            if(settings.pt_active_tab == 'tab-2'){
                tab2 = 'active';
            }else{
                tab1 = 'active';
            }
            view.addRenderAttribute('eae-price-table', 'class', 'eae-price-table');
            view.addRenderAttribute('eae-price-table', 'data-stacked', settings.stacked_below);
            view.addRenderAttribute('eae-apt', 'class', 'eae-apt');
            $badgeClass = '';
            for(let i = 1; i <= settings.pt_number_of_price_table; i++){
                    if(settings['pt_badge_style_preset_' + i] === 'style-5' && settings['pt_badge_style_' + i] == 'yes' && settings['pt_badge_text_' + i] != ''){
                        $badgeClass = 'eae-badge';
                    }
            }
            view.addRenderAttribute('eae-apt', 'class', $badgeClass);

            view.addRenderAttribute('pt-tab-1','class',['eae-apt-tab-1','eae-apt-tab-content-section', tab1]);
            view.addRenderAttribute('pt-tab-2','class', ['eae-apt-tab-2','eae-apt-tab-content-section', tab2]);
        #>
        <div {{{ view.getRenderAttributeString( 'eae-price-table' ) }}}>
            <# if(settings.pt_tabs_show == 'yes' && settings.pt_number_of_price_table != '' && settings.pt_number_of_price_table > 1) {#>
                <div class="eae-apt-switch-container">
                <#    
                    getFilterButton();
                #>    
                </div>  
            <# } #>
            <div {{{ view.getRenderAttributeString( 'eae-apt' ) }}}>
                <div {{{ view.getRenderAttributeString( 'pt-tab-1' ) }}}>
                <#     
                    getPriceTable('tab-1');
                #>
                </div>
                <#
                if(settings.pt_tabs_show == 'yes'){
                    #>
                    <div {{{ view.getRenderAttributeString( 'pt-tab-2' ) }}}>
                        <#     
                            getPriceTable('tab-2');
                        #>
                    </div>
                    <#
                }
                #>
            </div>
        </div>
        <?php
    }

    public function get_price_table($condition){
        $settings = $this->get_settings_for_display();
        for($i = 1; $i <= $settings['pt_number_of_price_table']; $i++){
            if(!isset($settings['pt_tab_'.$i])){
                $settings['pt_tab_'.$i] = 'tab-1';
            }
            if($settings['pt_tab_'.$i] == $condition){
                $this->set_render_attribute('price-table-wrapper','class',['eae-price-table-wrapper','eae-price-table-' . $i]);
                ?>
                    <div <?php echo $this->get_render_attribute_string('price-table-wrapper'); ?> >
                        <?php if($settings['apt_' . $i . '_graphic_type'] !== 'none'){ ?>
                            <!-- <div class="eae-apt-icon-container"><?php //Helper::render_icon_html($settings,$this, 'apt_' . $i, 'eae-apt-icon','apt'); ?></div> -->
                            <div class="eae-apt-icon-container">
                                <?php
                                    if($settings['apt_hover_animation'] != 'none'){
                                        add_filter('eae/eae-icon-class',[$this, 'add_icon_class'],10,3);
                                    }
                                    Helper::render_icon_html($settings,$this, 'apt_' . $i, 'eae-apt-icon'); 
                                ?>
                            </div>
                        <?php } ?>
                        <?php if($settings['pt_title_' . $i] !== '') { ?>
                            <div class="eae-apt-title-container">
                                <?php 
                                    $this->set_render_attribute('apt-title','class','eae-apt-title');
                                    $pt_title = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag($settings['pt_title_tag'] ), $this->get_render_attribute_string( 'apt-title' ),  Helper::eae_wp_kses($settings['pt_title_' . $i])); 
                                    echo $pt_title;
                                ?>
                            </div>
                        <?php } ?>
                        <?php if($settings['pt_description_' . $i] !== ''){
                            ?><span class="eae-apt-description"><?php echo Helper::eae_wp_kses($settings['pt_description_' . $i]) ?></span><?php
                        }?>
                        <?php if($settings['pt_duration_' . $i] !== '' || $settings['pt_sale_price_' . $i] !== '' || $settings['pt_price_prefix_' . $i] !== '' || $settings['pt_sale_price_' . $i] !== '') {?>
                            <div class="eae-apt-price-wrapper">
                                <?php if($settings['pt_sale_price_' . $i] !== '' || $settings['pt_price_prefix_' . $i] !== '' || $settings['pt_sale_price_' . $i] !== ''){ ?>
                                    <div class="eae-apt-price-container">
                                        <?php 
                                            if($settings['pt_sale_price_' . $i] !== '' && $settings['pt_price_' . $i] !== ''){
                                                ?><span class="eae-apt-price eae-apt-descount-font"><?php echo Helper::eae_wp_kses($settings['pt_price_prefix_' . $i] . $settings['pt_price_' . $i]); ?></span><?php
                                            }
                                            if($settings['pt_price_prefix_' . $i] !== ''){
                                                ?><span class="eae-apt-price-prefix"><?php echo Helper::eae_wp_kses($settings['pt_price_prefix_' . $i]); ?></span><?php
                                            }
                                            if($settings['pt_sale_price_' . $i] == ''){
                                                if($settings['pt_price_' . $i] !== ''){
                                                    $price = $this->get_price(Helper::eae_wp_kses($settings[ 'pt_price_' . $i]));
                                                    ?><span class="eae-apt-price eae-apt-price-font"><?php echo $price[0]; ?></span><?php
                                                }
                                            }else{
                                                $price = $this->get_price(Helper::eae_wp_kses($settings[ 'pt_sale_price_' . $i]));
                                                ?><span class="eae-apt-sale-price eae-apt-price-font"><?php echo $price[0]; ?></span><?php
                                            }
                                            if(isset($price[1])){
                                                echo $price[1];
                                            }
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php 
                                    if($settings['pt_duration_' . $i] !== ''){
                                        ?><span class="eae-apt-duration"><?php echo Helper::eae_wp_kses($settings['pt_duration_' . $i]) ?></span><?php
                                    }
                                ?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($settings['pt_items_' . $i])){ ?> 
                            <div class="eae-apt-features-container">
                                <?php 
                                    $flag = 0;
                                    foreach ( $settings['pt_items_' . $i] as $index => $item){
                                        if($item['pt_feature_not_avalabel'] == 'yes'){
                                            if($settings['pt_feature_not_available'] == 'icon'){
                                                $this->set_render_attribute('features-list-item','class',['eae-apt-features-list-item','eae-apt-feature-not-available-icon','elementor-repeater-item-'.$item['_id']]);
                                            }else if($settings['pt_feature_not_available'] == 'line-through' ){
                                                $this->set_render_attribute('features-list-item','class',['eae-apt-features-list-item','eae-apt-feature-not-available','elementor-repeater-item-'.$item['_id']]);
                                            }
                                        }else{
                                            $this->set_render_attribute('features-list-item','class',['eae-apt-features-list-item','elementor-repeater-item-'.$item['_id']]);
                                        }
                                        
                                        if($item['pt_features'] !== '' || $item['pt_feature_icon_graphic_type'] !== 'none' || $settings['pt_feature_not_available'] == 'icon' && $item['pt_feature_not_avalabel'] == 'yes'){
                                            ?><span <?php echo $this->get_render_attribute_string('features-list-item'); ?> >
                                                <?php

                                                    if($item['pt_feature_icon_graphic_type'] !== 'none'){
                                                        if($settings['pt_feature_not_available'] == 'icon' && $item['pt_feature_not_avalabel'] == 'yes'){
                                                            $icon = [
                                                                'value'   => 'fas fa-times',
                                                                'library' => 'solid',
                                                            ];
                                                            Icons_Manager::render_icon( $icon );
                                                        }else{
                                                            Helper::render_icon_html($item,$this, 'pt_feature_icon', 'eae-apt-feature-icon');
                                                        }
                                                    }else if($settings['pt_feature_not_available'] == 'icon' && $item['pt_feature_not_avalabel'] == 'yes'){
                                                        $icon = [
                                                            'value'   => 'fas fa-times',
                                                            'library' => 'solid',
                                                        ];
                                                        Icons_Manager::render_icon( $icon );
                                                    }
                                                    
                                                    if($item['pt_features_tooltip_text'] !== ''){
                                                        $flag++;
                                                        if($settings['pt_tooltip_display'] == 'icon'){
                                                            $icon = [
                                                                'value'   => 'fas fa-info-circle',
                                                                'library' => 'solid',
                                                            ];
                                                            ?>
                                                                <div class="eae-apt-tooltip-icon-container">
                                                                    <?php 
                                                                        echo Helper::eae_wp_kses($item['pt_features']);
                                                                        ?><div class="eae-apt-tooltip-icon"><?php
                                                                            Icons_Manager::render_icon( $icon ); 
                                                                            $this->set_render_attribute('apt-tooltip-on-icon','class','eae-apt-tooltip-on-icon');
                                                                            if($settings['preview_details_on_hover'] == 'yes' && $flag == 1){
                                                                                if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                                                                                    $this->set_render_attribute( 'apt-tooltip-on-icon', 'class', ['eae-apt-tooltip-on-icon','eae-apt-tooltip-preview']);
                                                                                }
                                                                            }
                                                                            ?>
                                                                            <span <?php echo $this->get_render_attribute_string('apt-tooltip-on-icon'); ?>><?php echo Helper::eae_wp_kses($item['pt_features_tooltip_text']) ; ?></span>
                                                                        </div><?php
                                                                    ?>
                                                                </div>
                                                            <?php
                                                        }else{
                                                            echo Helper::eae_wp_kses($item['pt_features']);
                                                            $this->set_render_attribute('apt-tooltip', 'class', 'eae-apt-tooltip');
                                                            if($settings['preview_details_on_hover'] == 'yes' && $flag == 1){
                                                                if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                                                                    $this->set_render_attribute( 'apt-tooltip', 'class', ['eae-apt-tooltip','eae-apt-tooltip-preview']);
                                                                }
                                                            }
                                                            ?><span <?php echo $this->get_render_attribute_string('apt-tooltip'); ?>><?php echo Helper::eae_wp_kses($item['pt_features_tooltip_text'] ); ?></span><?php
      
                                                        }
                                                    }else{
                                                        echo Helper::eae_wp_kses($item['pt_features']);
                                                    }
                                                ?>
                                            </span><?php
                                        }    
                                    }    
                                ?>
                            </div>
                        <?php } ?>    
                        <?php 
                            if( $settings['pt_button_text_' . $i] !== '' || $settings['pt_button_icon_' . $i . '_graphic_type'] !== 'none'){
                                $this->add_link_attributes('button_url_'. $i,$settings['pt_button_url_'.$i]); ?>
                                    <div class="eae-apt-button-container">
                                        <a class="eae-apt-button" <?php echo $this->get_render_attribute_string('button_url_'.$i); ?>>
                                            <?php echo Helper::eae_wp_kses($settings['pt_button_text_' . $i]); Helper::render_icon_html($settings,$this, 'pt_button_icon_'.$i, 'eae-apt-button-icon');?>
                                        </a>
                                    </div>
                                <?php
                            }
                            if( $settings['pt_badge_style_' . $i ] !== ''){
                                $this->get_badge(Helper::eae_wp_kses($settings['pt_badge_text_' .$i]),$settings['pt_badge_style_preset_' .$i] , $settings['pt_badge_position_' .$i] );
                            }
                        ?>
                    </div> 
                <?php
            }
        }
    }

    public function get_price($price_data){
        $price = explode( '.', $price_data);
        $fractional_price[0] = $price[0];
        if ( count( $price ) > 1 ) {
            $fractional_price[1] = '<span class="eae-apt-fractional">' . $price[1] . '</span>';
        }
        return $fractional_price;                
    } 
    
    

    public function get_badge($text,$style,$position){
        $html = '';
        switch($style){
            case 'style-1':
                $html = '<div class="eae-apt-badge-style-1-'. $position .'"><span>'. $text .'</span></div>';
                break;
            case 'style-2':
                $html = '<div class="eae-apt-badge-style-2-'. $position .'"><span>'. $text . '</span></div>';
                break;
            case 'style-3':
                $html = '<span class="eae-apt-badge-style-3-'. $position .'">' . $text . '</span>';
                break;
            case 'style-4':
                $html = '<div class="eae-apt-badge-style-4-' . $position . '"><span class="eae-apt-badge-style-4-inner-'. $position .'">' . $text . '</span></div>';
                break;
            case 'style-5':
                $html = '<span class="eae-apt-badge-style-5">' . $text . '</span>';
        }
        echo $html;
    }

    public function render(){
        $settings = $this->get_settings_for_display();
        $stacked_below = Helper::validate_option_value($settings['stacked_below'], $this->get_stacked_options(), '');
        $tab_1 = '';
        $tab_2 = '';
        if($settings['pt_active_tab'] == 'tab-2'){
            $tab_2 = 'active';
        }else{
            $tab_1 = 'active';
        }


        $this->add_render_attribute('pt-tab-1','class',['eae-apt-tab-1','eae-apt-tab-content-section', $tab_1]);
        $this->add_render_attribute('pt-tab-2','class', ['eae-apt-tab-2','eae-apt-tab-content-section', $tab_2]);
        ?>
            <div class="eae-price-table" data-stacked="<?php echo esc_attr($stacked_below);?>">
                <?php if($settings['pt_tabs_show'] == 'yes' && $settings['pt_number_of_price_table'] !== '' && $settings['pt_number_of_price_table'] > 1 ){ ?>
                    <div class="eae-apt-switch-container">
                        <?php $this->get_tab_control_buttons(); ?>
                    </div>
                <?php } 
                $badgeClass = ''; 
                for($i = 1; $i <= $settings['pt_number_of_price_table']; $i++){
                    if($settings['pt_badge_style_preset_' .$i] === 'style-5'){
                        $badgeClass = 'eae-badge';
                    }
                }
                $this->add_render_attribute('eae-apt','class',['eae-apt',$badgeClass])
                ?>
                
                <div <?php echo $this->get_render_attribute_string('eae-apt'); ?>>
                    <div <?php echo $this->get_render_attribute_string('pt-tab-1'); ?>>
                        <?php 
                            $this->get_price_table('tab-1');
                        ?>
                    </div>
                    <?php if($settings['pt_tabs_show'] == 'yes') {?>
                        <div <?php echo $this->get_render_attribute_string('pt-tab-2'); ?>>
                            <?php 
                                $this->get_price_table('tab-2');
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div> 
        <?php
        
    }
   
}
?>