<?php

namespace WTS_EAE\Pro\Modules\WooCategory\Widgets;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use Elementor\Plugin;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use WTS_EAE\Classes\Swiper_helper;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WooCategory extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-woo-category';
	}

	public function get_title() {
		return __( 'Woo Category', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-woo-category';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return ['woo category'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

    protected function register_controls(){
        $this->start_controls_section(
			'eae_woo_category_layout',
			[
				'label' => __( 'Layout', 'wts-eae' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'grid' => __('Grid', 'wts-eae'),
					'slider' => __('Slider', 'wts-eae'),
				],
				'default' => 'grid',
			]
		);

		$this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
				'default' => 4,
                'tablet_default' => 3,
                'mobile_default' => 1,
                'min' => 1,
                'step' => 1,
                'condition'=>[
                    'layout'=>'grid',
                ],
				'selectors' => [
                    '{{WRAPPER}}' => '--eae-wc-column:{{VALUE}};'
                ],
            ]
        );

		$this->add_responsive_control(
            'columns_gap',
            [
                'label' => esc_html__( 'Columns Gap' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
				'default' => 20,
                'min' => 1,
                'step' => 1,
                'condition'=>[
                    'layout'=>'grid',
                ],
				'selectors' => [
                    '{{WRAPPER}} .eae-category.eae-grid-layout' => 'column-gap:{{VALUE}}px;'
                ],
            ]
        );

		$this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__( 'Row Gap' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
				'default' => 20,
                'min' => 1,
                'step' => 1,
                'condition'=>[
                    'layout'=>'grid',
                ],
				'selectors' => [
                    '{{WRAPPER}} .eae-category.eae-grid-layout' => 'row-gap:{{VALUE}}px;'
                ],
            ]
        );

		$this->add_control(
			'pre_layout',
			[
				'label' => esc_html__( 'Preset Layout', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'pre1' => __('Preset 1', 'wts-eae'),
					'pre2' => __('Preset 2', 'wts-eae'),
					'pre3' => __('Preset 3', 'wta-eae'),
				],
				'default' => 'pre1',
			]
		);
		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' => __('Top', 'wts-eae'),
					'left' => __('Left', 'wts-eae'),
					'bottom' => __('Bottom', 'wts-eae'),
					'right' => __('Right', 'wts-eae'),
				],	
                'default' => 'top',
                'condition' => [
                    'pre_layout' => 'pre3',
                ]
			]
		);
    
		$this->add_control( 
			'show_content',
			[
				'label'        => __( 'Show Content', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'	   => 'yes'
			]
		);

		

		$this->add_control( 
			'show_title',
			[
				'label'        => __( 'Show Title', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'	   => 'yes',
				'condition' => [
                    'show_content' => 'yes'
                ]
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h2',
				'condition' => [
                    'show_content' => 'yes',
                    'show_title' => 'yes',
                ]
			]
		);

		$this->add_control( 
			'show_count',
			[
				'label'        => __( 'Show Count', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'condition' => [
                    'show_content' => 'yes'
                ],
				'default'	   => 'yes'
			]
		);

		$this->add_control(
			'count_position',
			[
				'label' => esc_html__( 'Count Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'inline' => __('Inline','wts-eae'),
					'outside' => __('Outside','wts-eae'),
				],
				'default' => 'inline',
				'condition' => [
                    'show_content' => 'yes',
                    'show_count' => 'yes',
                ]
			]
		);

		$this->add_control(
			'count_align',
			[
				'label' => esc_html__( 'Count Alignment', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options'   => [
					'left' => __('Left','wts-eae'),
					'right' => __('Right','wts-eae'),
				],
				'default' => 'left',
				'condition' => [
                    'show_content' => 'yes',
                    'show_count' => 'yes',
                    'count_position' => 'inline',
                ]
			]
		);

		$this->add_control( 
			'show_des',
			[
				'label'        => __( 'Show Description', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'	   => 'yes',
				'condition' => [
                    'show_content' => 'yes'
                ]
			]
		);
		$this->add_control(
            'word_limit',
            [
                'label' => esc_html__( 'Word Limit' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default' => 30,
                'min' => 1,
                'step' => 1,
                'condition'=>[
                    'show_des'=>'yes',
					'show_content' => 'yes'
                ],
            ]
        );

		$this->add_control( 
			'show_child_cate',
			[
				'label'        => __( 'Show Child Categories', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'condition' => [
                    'show_content' => 'yes'
                ]
			]
		);

		$this->add_control(
			'child_separator',
			[
				'label'			 => esc_html__( 'Separator', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,	
				'default'		 => esc_html__( ',', 'wts-eae' ),
				'condition' => [
                    'show_content' => 'yes',
                    'show_child_cate' => 'yes',
					
                ]
			]
		);

		$this->add_control( 
			'show_button',
			[
				'label'        => __( 'Show Button', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'	   => 'yes',
				'condition' => [
                    'show_content' => 'yes'
                ]
			]
		);

		$this->add_control(
			'btn_text',
			[
				'label'			 => esc_html__( 'Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,			
				'placeholder'    =>__('Explore','wts-eae'),
				'condition' => [
                    'show_content' => 'yes',
                    'show_button' => 'yes',
                ]
			]
		);

		Helper::eae_media_controls(
            $this,
            [
                'name'          => 'eae_wc_button_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> true,
				'conditions'     => [
					[
						'key'   => 'show_button',
						'value' => 'yes',
					],
					[
						'key'   => 'show_content',
						'value' => 'yes',
					]
				]
            ]
        );

		$this->add_control(
			'icon_position',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'row-reverse' => __('Before','wts-eae'),
					'row' => __('After','wts-eae'),
				],
				'selectors' => [
					'{{WRAPPER}} .eae-category-content .eae-buttons' => 'flex-direction: {{VALUE}}',						
				],
				'default' => 'row',
				'condition' => [
                    'show_content' => 'yes',
                    'show_button' => 'yes',
                ]
			]
		);

		$this->add_control( 
			'show_feature_img',
			[
				'label'        => __( 'Show Feature Image', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'	   => 'yes',
				'condition' => [
                    'pre_layout' => 'pre1'
                ]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'default' => 'large',
				'separator' => 'none',
			]
		);
        $this->end_controls_section();

		$this->start_controls_section(
			'eae_woo_category_settings',
			[
				'label' => __( 'Category Settings', 'wts-eae' ),
			]
		);
		$options = $this->get_category_name();
		
		$this->add_control(
			'filter_by',
			[
				'label' => esc_html__( 'Filter By', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
                    'all'           => __('Show All', 'wts-eae'),
					'by_parent'     => __('By Parent', 'wts-eae'),
					'by_id'         => __('Manual Selection','wpv_bu'),
					'current_cat'   => __('Current Subcategories','wts-eae'),

				],
                'default' => 'all',
			]
		);
		$this->add_control(
			'include',
			[
				'label' => esc_html__( 'Include Categories', 'wts-eae' ),
				'type' => Controls_Manager::SELECT2,
                'options' => $options,
                'default' => '',
                'multiple' => true,
				'condition' => [
                    'filter_by' => 'by_id', 
                ]
 			]
		);

		$this->add_control(
			'parent',
			[
				'label' => esc_html__( 'Parent', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => $options,
				'description' => __('All the subcategories will be shown of the selected parent','wts-eae'),
                'multiple' => true,
				'condition' => [
                    'filter_by' => 'by_parent', 
                ]
 			]
		);

		$this->add_control(
			'exclude',
			[
				'label' => esc_html__( 'Exclude Categories', 'wts-eae' ),
				'type' => Controls_Manager::SELECT2,
                'options' => $options,
                'multiple' => true,
 			]
		);
		
		$this->add_control( 
			'exclude_child',
			[
				'label'        => __( 'Exclude Child Categories', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
			]
		);

		$this->add_control(
			'category_count',
			[
				'label' => esc_html__('No. of Category to show','wts-eae'),
				'type' => Controls_Manager::NUMBER,
				'default' =>''
			]
		);

		$this->add_control(
			'order_by',
			[
				'label' => esc_html__( 'Order By', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => [
					''  	=> __('Select', 'wts-eae'),
					'name'  => __('Name', 'wts-eae'),
					'id'    => __('ID', 'wts-eae'),
					'count' => __('Count', 'wts-eae'),
					'slug'  => __('Slug','wts-eae'),
					'term_group' => __('Term Group','wts-eae'),
					'none'  => __('None', 'wts-eae'),
                ],
                'default' => '',
			]
		);

		$this->add_control(
			'order',
			[
				'label' => esc_html__( 'Order', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => [
                    'ASC' => __('Ascending','wts-eae'),
                    'DESC' => __('Descending', 'wts-eae'),
                ],
                'default' => 'ASC',
			]
		);

		$this->add_control( 
			'only_parent_cat',
			[
				'label'        => __( 'Show only top Level', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'condition' => [
                    'filter_by!' => 'by_parent', 
                ]
			]
		);

		$this->add_control( 
			'hide_empty',
			[
				'label'        => __( 'Hide Empty', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
				'default'      => 'yes'
			]
		);
        $this->end_controls_section();

		$this->start_controls_section(
			'eae_woo_slider_settings',
			[
				'label' => __( 'Slider Settings', 'wts-eae' ),
				'condition' => [
                    'layout' => 'slider', 
                ]
			]
		);
			Swiper_helper::carousel_controls($this);

        $this->end_controls_section();
		 
        $this->start_controls_section(
            'section_card_style',
            [
                'label' => esc_html__( 'Category Card Style', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'card_heading',
			[
				'label'     => __( 'Card', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
            'sp_layout',
            [
                'label' => esc_html__('Split Layout','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'up' => [
                        'title' => esc_html__('Up','wts-eae'),
                        'icon' => 'eicon-arrow-up',
                    ],
                    
                    'down' =>[
                        'title' => esc_html__('Down','wts-eae'),
                        'icon' => 'eicon-arrow-down',
                    ] , 
                    'right' =>[
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-arrow-right',
                    ]
                ],
				'prefix_class' => 'eae-woo-split-',
                'selectors_dictionary' => [
                    'left' => 'row',
                    'up' => 'column',
                    'right' => 'row-reverse',
                    'down' => 'column-reverse',
                ],
                'condition' => [
					'pre_layout' => 'pre1',
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-style1 .eae-category-card' => 'flex-direction: {{VALUE}}',
                ],
            ]
        );

		$ele_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
        $args = [
            'add_desktop' => false
        ];
        $breakpoints = Plugin::$instance->breakpoints->get_breakpoints_config();
        $active_devices = Plugin::$instance->breakpoints->get_active_devices_list($args);
		$break_value = [];
        $break_value_arr = [];
		foreach($active_devices as $active_device){
			$min_breakpoint = Plugin::$instance->breakpoints->get_device_min_breakpoint($active_device);
            $break_value[$active_device] = $min_breakpoint;
            $break_value_arr[$breakpoints[$active_device]['default_value']] = ucfirst($active_device); 
		}
        asort($break_value_arr);
        $defualt_device = array_key_first($break_value_arr);
        

		$this->add_control(
            'stacked_below',
            [
                'label' => __('Stack On', 'wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => $break_value_arr,
                'default' => $defualt_device,
				'condition' => [
					'sp_layout' => ['left','right'],
				],

            ]
        );

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card ',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'selector' => '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card',
				
			]
		);

		$this->add_responsive_control(
			'card_border_radius',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-cat-wrapper .eae-category-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'card_margin',
			[
				'label' => esc_html__( 'Margin', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-cat-wrapper .eae-category-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label'     => __( 'Content', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
                    'show_content' => 'yes'
				]
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_content',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card .eae-category-content',
				'condition'=>[
                    'show_content' => 'yes'
				]
            ]
        );

		$this->add_responsive_control(
            'pre2_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-style2 .eae-category-card .eae-category-content' => 'width:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'pre_layout' => 'pre2',
                    'show_content' => 'yes'
				]
            ]
        );

		$this->add_responsive_control(
            'pre2_height',
            [
                'label' => esc_html__('Height','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-style2 .eae-category-card .eae-category-content' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'pre_layout' => 'pre2',
                    'show_content' => 'yes'
				]
            ]
        );

		$this->add_responsive_control(
            'pre2_top',
            [
                'label' => esc_html__('Top','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-style2 .eae-category-card .eae-category-content' => 'top:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'pre_layout' => 'pre2',
                    'show_content' => 'yes'
				]
            ]
        );

		$this->add_responsive_control(
            'pre2_left',
            [
                'label' => esc_html__('Left','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-style2 .eae-category-card .eae-category-content' => 'left:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'pre_layout' => 'pre2',
                    'show_content' => 'yes'
				]
            ]
        );

	

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-cat-wrapper .eae-category-card .eae-category-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
                    'show_content' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'content_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Start', 'wta-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-h',
					],
					'end' => [
						'title' => esc_html__( 'End', 'wta-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'center' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card .eae-category-content' => 'align-items: {{VALUE}}',
                ],
				'condition'=>[
                    'show_content' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'justify_content',
			[
				'label' => esc_html__( 'Alignment (Vertical)', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Start', 'wta-eae' ),
						'icon' => 'eicon-justify-start-v',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-center-v',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-justify-end-v',
					],
					'spaceBetween' => [
						'title' => esc_html__( 'Space between', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-v',
					],
					'spaceAround' => [
						'title' => esc_html__( 'Space around', 'wta-eae' ),
						'icon' => 'eicon-justify-space-around-v',
					],
					'spaceEvenly' => [
						'title' => esc_html__( 'Space evenly', 'wta-eae' ),
						'icon' => 'eicon-justify-space-evenly-v',
					],
				],
                'selectors_dictionary' => [
                    'start' => 'start',
                    'center' => 'center',
                    'end' => 'end',
                    'spaceBetween' => 'space-between',
                    'spaceAround' => 'space-around',
                    'spaceEvenly' => 'space-evenly',
                ],
                'condition'=>[
					'pre_layout' => 'pre2',
                    'show_content' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card .eae-category-content' => 'justify-content: {{VALUE}}',
                ],
			]
		);

		$this->add_responsive_control(
            'content_space',
            [
                'label' => esc_html__('Space','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card .eae-category-content' => 'gap:{{SIZE}}{{UNIT}};',
                ],
				'condition'=>[
                    'show_content' => 'yes'
				]
            ]
        );


		$this->add_responsive_control(
            'media_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-media-btn .eae-wp-icon' => 'width:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				]
            ]
        );

		$this->add_control(
			'image_heading',
			[
				'label'     => __( 'Image', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label' => esc_html__( 'Aspect Ratio', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'169' => '16:9',
					'219' => '21:9',
					'43' => '4:3',
					'32' => '3:2',
					'11' => '1:1',
					'916' => '9:16',
				],
				'selectors_dictionary' => [
					'169' => '16 / 9',
					'219' => '21 / 9',
					'43' => '4 / 3',
					'32' => '3 / 2',
					'11' => '1 / 1',
					'916' => '9 / 16',
				],
				'render_type' => 'template',
				'default' => '11',
				'selectors' => [
					'{{WRAPPER}}  .eae-category-card .eae-image img' => 'aspect-ratio: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .eae-category-card .eae-image',
			]
		);

		$this->add_control( 
			'scale_on_hover',
			[
				'label'        => __( 'Scale On Hover', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
				'default'      => 'yes',
				'condition'=>[
                    'layout!' => 'pre3'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .eae-category-card .eae-image',
			]
		);

		$this->add_control(
			'count_heading',
			[
				'label'     => __( 'Count', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
                    'show_count' => 'yes'
				]
			]
		);

		$this->add_control(
            'count_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-category-card .eae-category-content .eae-count' => 'color: {{VALUE}};',
                ],
				'condition'=>[
                    'show_count' => 'yes'
				]
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_count',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-count',
				'condition'=>[
                    'show_count' => 'yes'
				]
            ]
        );

		$this->add_control(
			'title_heading',
			[
				'label'     => __( 'Title', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
                    'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-category-card .eae-category-content .eae-title a' => 'color: {{VALUE}};',
                ],
				'condition'=>[
                    'show_title' => 'yes'
				]
            ]
        );

		$this->add_control(
            'title_hov_color',
            [
                'label' => esc_html__( 'Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-category-card .eae-category-content .eae-title:hover a' => 'color: {{VALUE}};',
                ],
				'condition'=>[
                    'show_title' => 'yes'
				]
            ]
        );

		

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_title',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-title a',
				'condition'=>[
                    'show_title' => 'yes'
				]
            ]
        );


		$this->add_control(
			'des_heading',
			[
				'label'     => __( 'Description', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
                    'show_des' => 'yes'
				]
			]
		);

		$this->add_control(
            'des_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-category-card .eae-category-content .eae-description' => 'color: {{VALUE}};',
                ],
				'condition'=>[
                    'show_des' => 'yes'
				]
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_des',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-description',
				'condition'=>[
                    'show_des' => 'yes'
				]
            ]
        );

		$this->add_control(
			'child_cate',
			[
				'label'     => __( 'Child Category', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
                    'show_child_cate' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
            'child_layout',
            [
                'label' => esc_html__('Direction','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'up' => [
                        'title' => esc_html__('Up','wts-eae'),
                        'icon' => 'eicon-arrow-up',
                    ],
                    
                    'down' =>[
                        'title' => esc_html__('Down','wts-eae'),
                        'icon' => 'eicon-arrow-down',
                    ] , 
                    'right' =>[
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-arrow-right',
                    ]
                ],
                'selectors_dictionary' => [
                    'left' => 'row',
                    'up' => 'column',
                    'right' => 'row-reverse',
                    'down' => 'column-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-category-content .eae-child-category' => 'flex-direction: {{VALUE}}',
                ],
				'condition'=>[
                    'show_child_cate' => 'yes'
				]
            ]
        );

		$this->add_control(
            'child_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-category-content .eae-child-category a' => 'color: {{VALUE}};',
                ],
				'condition'=>[
                    'show_child_cate' => 'yes'
				]
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_child',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-category-content .eae-child-category',
				'condition'=>[
                    'show_child_cate' => 'yes'
				]
            ]
        );

		$this->add_control(
			'button_heading',
			[
				'label'     => __( 'Button', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
                    'show_button' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
            'btn_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons' => 'width:{{SIZE}}{{UNIT}};',
                ],
				'condition'=>[
                    'show_button' => 'yes'
				]
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_btn',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons',
				'condition'=>[
                    'show_button' => 'yes'
				]
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'btn_box_shadow',
				'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons',
				'condition'=>[
                    'show_button' => 'yes'
				]
			]
		);

		
		$this->start_controls_tabs( 'wc_tabs_button_style', [
			'condition' => [
				'show_button' => 'yes'
			],
		] );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
				'condition' => [
                    'show_button' => 'yes'
				],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'show_button' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background_btn',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons',
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
				'condition' => [
                    'show_button' => 'yes'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
				'condition' => [
                    'show_button' => 'yes'
				],
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'show_button' => 'yes'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'btn_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons:hover',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
				'condition' => [
                    'show_button' => 'yes'
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
                    'show_button' => 'yes'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btn_border',
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .eae-category-card .eae-category-content .eae-buttons',
				'condition'=>[
                    'show_button' => 'yes'
				]
			]
		);

		

		$this->add_responsive_control(
            'button_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card .eae-category-content .eae-buttons' => 'margin-top:{{SIZE}}{{UNIT}};',
                ],
				'condition'=>[
                    'show_button' => 'yes'
				]
            ]
        );

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-cat-wrapper .eae-category-card .eae-category-content .eae-buttons' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
                    'show_button' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'btn_alignment',
			[
				'label' => esc_html__( 'Align Self', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Start', 'wta-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-h',
					],
					'end' => [
						'title' => esc_html__( 'End', 'wta-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'center' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-cat-wrapper .eae-category-card .eae-category-content .eae-buttons' => 'align-self: {{VALUE}}',
                ],
				'condition'=>[
                    'show_button' => 'yes'
				]
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'=>[
                    'show_button' => 'yes'
				]
			]
		);

		Helper::global_icon_style_controls(
            $this,
            [
                'name'            => 'eae_wc_icon_style',
                'selector'        => '.eae_wc_icon',	
                'is_repeater'     => false,
				'hover_selector'  => '.eae-buttons:hover .eae_wc_icon',
				'conditions'     => [
					[
						'key'   => 'show_button',
						'value' => 'yes',
					],
				]
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
            'slider_style',
            [
                'label' => esc_html__( 'Slider', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout!' => 'grid',
                ]
            ]
        );
            Swiper_helper::carousel_style_section($this);
		
        $this->end_controls_section();
    }
	
    public function render(){
		$settings = $this->get_settings_for_display();
		
		$wid = $this->get_id();
		if($settings['show_content'] == 'yes'){
			$title_tag = Helper::validate_html_tag($settings['title_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ], 'h2');
		}
        $layout = isset($settings['layout']) ? $settings['layout'] : 'grid';

		$root_classes = ['eae-woo-cat-wrapper',$wid];
		$categories = $this->get_category_query($settings);
		$category_class = ['eae-category'];

		if($layout === 'grid'){
            $category_class[] = 'eae-grid-layout';
            $root_data = 'eae-grid';
			$this->add_render_attribute('_root','data',$root_data);
        }
        else{

			$swiper_data = Swiper_helper::get_swiper_data($settings);
			$this->add_render_attribute('_root','class','eae-woo-category-swiper');
			$this->add_render_attribute('_root','class','eae-swiper-outer-wrapper');
			
			$this->add_render_attribute('swiper_container','class','eae-swiper');
			
			if($settings['arrows_layout'] == 'inside'){
				$this->add_render_attribute('_root','class','eae-hpos-' . $settings['arrow_horizontal_position']);
				$this->add_render_attribute('_root','class','eae-vpos-' . $settings['arrow_vertical_position']);
			}

			$this->add_render_attribute('_root','data-swiper-settings', wp_json_encode( $swiper_data ) );

			$this->add_render_attribute( 'swiper_container', 'class', 'eae-swiper-container ' );

			$slider_id = rand(99,9999);
			$this->add_render_attribute('swiper_container','data-eae-slider-id',$slider_id);

			$this->add_render_attribute('swiper_container','class','eae-slider-id-'. $slider_id);
			if($settings['arrows_layout'] == 'outside'){
				$this->add_render_attribute('_root','class','eae-slider-id-'. $slider_id);
			}
			if( $settings['auto_height'] !== 'yes'){
				$this->add_render_attribute('_root','class','eae-height-100');
			}

			$this->add_render_attribute('swiper_wrapper','class','eae-wp-carousel-wrapper eae-woo-category-swiper');

			$this->add_render_attribute('swiper_wrapper','class', 'eae-swiper-wrapper');
			$root_classes[] .= 'eae-wp-slider';
        }
		$category_id = [];
		if(isset($settings['pre_layout'])){
            switch ($settings['pre_layout']){
                case 'pre1' :
                $category_id = ['eae-style1'];
                break;
                case 'pre2' :
                    $category_id = ['eae-style2'];
                break;
                case 'pre3' :
                    $category_id = ['eae-style3'];
                break;
            }
        }
		
		$this->add_render_attribute('_root','class',$root_classes);
		$this->add_render_attribute('_root','data-stacked',$settings['stacked_below']);
		$this->add_render_attribute('category','class',$category_class);
		$this->add_render_attribute('category','class',$category_id);

		?>
		<div <?php echo  $this->get_render_attribute_string('_root') ?>> <?php
			if(!empty($categories)){
				
				if ( $layout === 'slider' ) { ?>
					<div <?php echo  $this->get_render_attribute_string('swiper_container') ?>> <?php		
					$this->add_render_attribute('category','class','eae-swiper-wrapper');
				}?>	

					<div <?php echo  $this->get_render_attribute_string('category') ?>> <?php
						if(! empty( $categories ) ){

							foreach($categories as $index => $category){
								$category_card = ['eae-category-card'];

								if(isset($settings['hover_animation'])){
									$category_card[] = 'eae-hvr-'.$settings['hover_animation'];
								}
								if($layout == 'slider'){
									$category_card []= 'eae-swiper-slide swiper-slide eae-category-card';
								}

								$this->add_render_attribute("cat-{$index}", 'class',$category_card);

								$name = $category->name;
								$count = $category->count;
								$id = $category->term_id;
								$thumb_id    = get_term_meta( $id, 'thumbnail_id', true );
								$link = get_term_link($id);
								$image_size = $settings['image_size'] ?? 'thumbnail';
								$image = wp_get_attachment_image($thumb_id, $image_size);
								$description = $category->description;
								$img_src = Utils::get_placeholder_image_src();
								$img_wrp = 'eae-image';
								if($settings['scale_on_hover'] == 'yes'){
									$img_wrp .= ' enable-scale';
								}
								$this->add_render_attribute("image-wrp", 'class',$img_wrp);

								if(empty($image)){
									$image = "<img src= '".esc_url($img_src)."' >";
								} ?>
								

								<div <?php echo  $this->get_render_attribute_string("cat-{$index}") ?>> <?php

									if($settings['show_feature_img'] == 'yes' || $settings['pre_layout'] != 'pre1'){ ?>
										<div <?php echo  $this->get_render_attribute_string("image-wrp") ?>>
											<a href= <?php echo esc_url($link) ?>>
												<?php echo $image ?>	
											</a>				
											<?php
											if(($settings['pre_layout'] === 'pre2' || $settings['pre_layout'] === 'pre3') && $settings['show_content'] == 'yes'){
												$this->render_woo_content($settings, $id, $index, $title_tag, $category, $link);
											}
											?>
										</div><?php
									}
									
									if($settings['pre_layout'] === 'pre1' && $settings['show_content'] == 'yes'){
										$this->render_woo_content($settings, $id, $index, $title_tag, $category, $link);
									} ?>

								</div><?php
							}
							
						} ?>
					</div> <?php
					if($layout === 'slider'){
						Swiper_helper::get_swiper_pagination($settings);
						if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
							Swiper_helper::get_swiper_arrows($settings);
						}
						Swiper_helper::get_swiper_scrolbar($settings);
						
					}
				if ( $layout === 'slider' ) {  ?>
					</div> <?php
					if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
						Swiper_Helper::get_swiper_arrows($settings);
					}	
				}

			}
			else{ ?>
				<div class='eae-vg-error message'>
					<span class='elementor-alert elementor-alert-warning'> <?php echo  __('No category were found matching your selection.','wts-eae') ?> </span>
				</div>
				<?php
			} ?>
		</div> <?php
	}

	public function get_category_query($settings){

        $parent = '';
        $include = '';
        $child_of = '';
        $filterby = $settings['filter_by'] ?? 'all';

        if(!empty($filterby)){

            switch ($filterby){
                case 'all' :
                   $parent = '';
                    break;
                case 'by_parent' :
                    $child_of = !empty($settings['parent']) ? $settings['parent'] : '';
                    break;
                case 'by_id' :
                    $include = !empty($settings['include']) ? implode( ',', $settings['include'] ) : '';
                    break;
                case 'current_cat' :
                    $parent = get_queried_object_id();
                    break;
            }
        }

        if($settings['only_parent_cat'] == 'yes'){
            $parent = 0;
        }
	
        $orderby = !empty($settings['order_by']) ? $settings['order_by'] : 'name';
        $order = !empty($settings['order']) ? $settings['order'] : 'DESC';
        $exclude = !empty($settings['exclude']) ? $settings['exclude'] : '';
        $number = !empty($settings['category_count']) ? $settings['category_count'] : '';
        $args = [
            'taxonomy'      => 'product_cat',
            'parent'        => $parent,
            'child_of'      => $child_of,
            'include'       => $include,
            'exclude'       => $exclude,
            'number'        => $number,
            'orderby'       => $orderby,
            'order'         => $order,
            'hide_empty'    => !empty($settings['hide_empty']) ? true : false,
        ];
		if($settings['exclude_child']){
            $args['exclude']    = '';
            $args['exclude_tree'] = $exclude;
        }

        $categories = get_terms($args);
        return $categories;
    }

	public function get_category_name(){
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ) );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $cat=[];

            foreach($terms as $key => $term){
                $name = $term->name;
                $catslug = $term->term_id;
                $cat[$catslug] = $name;           
            }
            return $cat;
        } 
    }
	public function render_woo_content($settings, $id, $index, $title_tag, $category, $link){
		$name = $category->name;
        $count = $category->count;
        $description = $category->description;
		if(isset($settings['show_content'])){
			$content_class = ['eae-category-content'];
		}

		$this->add_render_attribute("content-$index", 'class', $content_class);
		?>
		<div <?php echo  $this->get_render_attribute_string("content-$index") ?>> <?php
			if(isset($settings['count_position']) && $settings['count_position'] === 'inline'){
				$head_class = ['eae-heading-wrap'];
				if(isset($settings['count_align'])){
					$head_class[] = 'eae-pst-'.$settings['count_align'];
				}
				$this->add_render_attribute("head-$index",'class',$head_class); ?>
				<div <?php echo $this->get_render_attribute_string("head-$index") ?>> <?php
			} 

			if($settings['show_title'] == 'yes'){
                $link = get_term_link($id);
                $this->add_render_attribute( "title-{$index}", 'class', 'eae-title');
				$this->add_render_attribute( "link-{$index}", 'href',  $link );
				$title = sprintf( '<a %1$s>%2$s</a>',$this->get_render_attribute_string( "link-{$index}" ), $name );
				$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $title_tag ), $this->get_render_attribute_string( "title-{$index}" ), $title );
				echo $title_html;
            }

			if($settings['show_count'] == 'yes'){ ?>
                <span class='eae-count'><?php echo '('.$count.')' ?></span> <?php
            }

            if(isset($settings['count_position']) && $settings['count_position'] === 'inline'){
                ?> </div> <?php
            }
			
			if($settings['show_child_cate'] == 'yes'){
                $parent_id =  $category->parent;
                if($parent_id === 0){
                    $child_category_ids = get_term_children( $id, 'product_cat' );
                    $sept = "";
                        if(!empty($settings['child_separator'])){
                            $sept  = Helper::eae_wp_kses($settings['child_separator']);
                        }
                    if(!empty($child_category_ids)){?>
                        <ul class = 'eae-child-category'> <?php
                        $lastElement = end($child_category_ids);
                        foreach ( $child_category_ids as $index => $child_category_id ) {
                            $child_category = get_term_by( 'id', $child_category_id, 'product_cat' );
                            $childLink = get_term_link($child_category_id);
                            $childName = $child_category->name;
                            $childCat_class = ['eae-child-category-li'];
							$this->add_render_attribute( "child-$index", "class",$childCat_class); ?>
							<li <?php echo $this->get_render_attribute_string("child-$index") ?> >
								<a href=<?php echo $childLink ?>>
									<?php echo $childName; 
										if($child_category_id != $lastElement){
											echo $sept;
										} 
									?>
								</a> 
							</li> <?php
                        } ?>
                        </ul> <?php
                    }
                }
            }

			if($settings['show_des'] == 'yes'){
                if(!empty($settings['word_limit'])){
                    if(!empty($description)){
						?> <div class = 'eae-description'> <?php echo wp_trim_words( wc_format_content($description), $settings['word_limit']) ?> </div> <?php
                    }
                }
                else{
                    if(!empty($description)){
						?> <div class = 'eae-description'> <?php echo wc_format_content($description)?> </div> <?php
                    }
                }
            }

			if(!empty($settings['show_button'])){
                $button_class = ['eae-buttons'];
                $this->add_render_attribute("button-$index",'class', $button_class);
                $this->add_render_attribute("button-$index",'href', $link);
				?>
				<a <?php echo $this->get_render_attribute_string("button-$index") ?>> 
				<?php 
					if(!empty($settings['btn_text'])){
						echo Helper::eae_wp_kses($settings['btn_text']);
					}
					else{
						echo __('Explore','wts-eae');
					}
					Helper::render_icon_html($settings,$this,'eae_wc_button_icon','eae_wc_icon');
				?>
				</a><?php
            }?>
		</div><?php
	}
}


