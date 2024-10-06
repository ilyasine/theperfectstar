<?php

namespace WTS_EAE\Pro\Modules\Testimonial\Widgets;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use WTS_EAE\Classes\Swiper_helper;
use Elementor\Utils;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use WTS_EAE\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class testimonial extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-testimonial';
	}

	public function get_title() {
		return __( 'Testimonial', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-testimonial-slider';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'testimonial'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}
    
    protected function register_controls(){

        $this->start_controls_section(
			'eae_testimonial_section',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

        $this->add_control(
			'pre_layout',
			[
				'label' => esc_html__( 'Preset Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'pre1' => __('Preset 1', 'wts-eae'),
					'pre2' => __('Preset 2', 'wts-eae'),
					'pre3' => __('Preset 3', 'wta-eae'),
				],
                'default' => 'pre1'
			]
		);
        $repeater = new Repeater();

        $repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Avatar', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
        $repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', 
				'default' => 'large',
			]
		);

        $repeater->add_control(
			'additional_img',
			[
				'label' => esc_html__( 'Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'description' => __('Applicable for Preset 2 and Preset 3 layout', 'wts-eae')
                // 'description' => __('', 'wts-eae')
			]
		);
        $repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'additional_image',
				'default' => 'large',
                
			]
		);

        $repeater->add_control(
            'author',
            [
                'label' => esc_html__( 'Author', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'dynamic'		 => [
					'active' => true,
				],
            ]
        );

        $repeater->add_control(
            'designation',
            [
                'label' => esc_html__( 'Designation', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'CEO',	
                'dynamic'		 => [
					'active' => true,
				],
            ]
        );

        $repeater->add_control(
            'company_name',
            [
                'label' => esc_html__( 'Company Name', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Google',
                'dynamic'		 => [
					'active' => true,
				],	
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => esc_html__( 'Description', 'wts-eae' ),
                'type' => Controls_Manager::TEXTAREA,
                'dynamic'		 => [
					'active' => true,
				],
                'default' =>__( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 'wts-eae' ),
            ]
        );

        $repeater->add_control(
            'rating',
            [
                'label'       => __( 'Rating', 'wts-eae' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 4,
                'max' => 5,
                'min' => 0,
                'step' => 1
            ]
        );

        $this->add_control(
            'testimonial_data',
            [
                'label' => esc_html__( 'Content List', 'wts-eae' ),
                'type' => Controls_Manager::REPEATER,
                'seperator' => 'before',
            
                'fields'      =>  $repeater->get_controls() ,
                'default' => [
                    [
                        'author' => esc_html__( 'Aida Bugg','wts-eae' ),
                    ],
                    [
                        'author' => esc_html__( 'Jhon Bugg','wts-eae' ),
                    ],
                    [
                        'author' => esc_html__( 'Ronald Bugg','wts-eae' ),
                    ],
                ],
                'title_field' => '{{{ author }}}',
            ]
        );

        $this->start_controls_tabs( 'tabs_content', [
            'condition' => [
                'pre_layout' => 'pre3',
            ],
		] );

		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => esc_html__( 'Content', 'wts-eae' ),
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->add_control( 
			'show_rating',
			[
				'label'        => __( 'Show Rating', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'yes',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->add_control( 
			'show_author',
			[
				'label'        => __( 'Show Author', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'yes',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);
        
        $this->add_control( 
			'show_designation',
			[
				'label'        => __( 'Show Designation', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'yes',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->add_control( 
			'show_c_name',
			[
				'label'        => __( 'Show Company Name', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => esc_html__( 'Overlay', 'wts-eae' ),
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->add_control( 
			'overlay_rating',
			[
				'label'        => __( 'Show Rating', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->add_control( 
			'overlay_author',
			[
				'label'        => __( 'Show Author', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'yes',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);
        
        $this->add_control( 
			'overlay_designation',
			[
				'label'        => __( 'Show Designation', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'yes',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->add_control( 
			'overlay_c_name',
			[
				'label'        => __( 'Show Company Name', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'yes',
                'condition' => [
					'pre_layout' => 'pre3',
				],
			]
		);

        $this->end_controls_tab();

		$this->end_controls_tabs();

        

        $this->end_controls_section();

        $this->start_controls_section(
			'eae_testimonial_rating',
			[
				'label' => __( 'Rating', 'wts-eae' ),
			]
		);

            $this->add_control(
                'filled_icon',
                [
                    'label'            => __( 'Filled Icon', 'wts-eae' ),
                    'type'             => Controls_Manager::ICONS,
                    'default'          => [
                        'value'   => 'fas fa-star',
                        'library' => 'solid',
                    ],
                ]
            );

            $this->add_control(
                'half_fill_icon',
                [
                    'label'            => __( 'Half Filled Icon', 'wts-eae' ),
                    'type'             => Controls_Manager::ICONS,
                    'default'          => [
                        'value'   => 'fas fa-star-half-alt',
                        'library' => 'solid',
                    ],
                ]
            );

            $this->add_control(
                'unmarked_icon',
                [
                    'label'            => __( 'Unmarked Icon', 'wts-eae' ),
                    'type'             => Controls_Manager::ICONS,
                    'default'          => [
                        'value'   => 'far fa-star',
                        'library' => 'solid',
                    ],
                ]
            );
        
        $this->end_controls_section();

        $this->start_controls_section(
			'eae_testimonial_slider',
			[
				'label' => __( 'Slider Options', 'wts-eae' ),
			]
		);
            $args = [
                'slides_per_view' => [
                    'desktop' => 1,
                    'tablet' => 1,
                ],

            ];
            Swiper_helper::carousel_controls($this, $args);

        $this->end_controls_section();

        $this->start_controls_section(
			'eae_ts_order',
			[
				'label' => __( 'Order', 'wts-eae' ),
			]
		);

            $this->add_responsive_control(
                'des_order',
                [
                    'label'			 => esc_html__( 'Description ', 'wts-eae' ),
                    'type'			 => Controls_Manager::TEXT,
                    'label_block'	 => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-content-desc' => 'order:{{VALUE}}'
                    ],
                ]
            );

            $this->add_responsive_control(
                'img_order',
                [
                    'label'			 => esc_html__( 'Image', 'wts-eae' ),
                    'type'			 => Controls_Manager::TEXT,				
                    'label_block'	 => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-content-img' => 'order:{{VALUE}}'
                    ],
                    'condition' => [
                        'pre_layout' => 'pre1'
                    ],
                ]
            );

            $this->add_responsive_control(
                'author_order',
                [
                    'label'			 => esc_html__( 'Author', 'wts-eae' ),
                    'type'			 => Controls_Manager::TEXT,
                    'label_block'	 => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-name' => 'order:{{VALUE}}'
                    ],
                ]
            );

            $this->add_responsive_control(
                'designation_order',
                [
                    'label'			 => esc_html__( 'Designation', 'wts-eae' ),
                    'type'			 => Controls_Manager::TEXT,
                    'label_block'	 => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-designation' => 'order:{{VALUE}}'
                    ],
                    'condition' => [
                        'pre_layout' => 'pre1'
                    ],
                ]
            );

            $this->add_responsive_control(
                'c_name_order',
                [
                    'label'			 => esc_html__( 'Company Name', 'wts-eae' ),
                    'type'			 => Controls_Manager::TEXT,
                    'label_block'	 => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-company-name' => 'order:{{VALUE}}'
                    ],
                    'condition' => [
                        'pre_layout' => 'pre1'
                    ],
                ]
            );

            $this->add_responsive_control(
                'rating_order',
                [
                    'label'			 => esc_html__( 'Rating', 'wts-eae' ),
                    'type'			 => Controls_Manager::TEXT,
                    'label_block'	 => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-content-rating' => 'order:{{VALUE}}'
                    ],
                ]
            );

		$this->end_controls_section();

        $this->start_controls_section(
            'testimonial_preset_style',
            [
                'label' => esc_html__( 'Preset', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_control(
                'box_style',
                [
                    'label' => esc_html__('Box','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_background',
                    'types' => [ 'classic', 'gradient' ,'image' ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                        'color' => [
                                'default' => '#ebebeb',
                        ],
                    ],
                ]
            );

            $this->add_responsive_control(
                'box_alignment',
                [
                    'label' => esc_html__( 'Alignment', 'wta-eae' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left'    => [
                            'title' => esc_html__( 'Left', 'wta-eae' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'wta-eae' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'wta-eae' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'default' => 'center',
                    'selectors_dictionary' => [
                        'left' => 'flex-start',
                        'center' => 'center',
                        'right' => 'flex-end',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content' => 'align-items: {{VALUE}}',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre1'
                    ]
                ]
            );


            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content',
                    
                ]
            );


            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'box_border',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content',
                ]
            );

            $this->add_responsive_control(
                'box_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'box_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'content_style',
                [
                    'label' => esc_html__('Content','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pre_layout!' => 'pre1'
                    ] 
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'content_background',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-ts-content-section',
                    'condition' => [
                        'pre_layout!' => 'pre1'
                    ] 
                ]
            );

            $this->add_responsive_control(
                'content_hor_alignment',
                [
                    'label' => esc_html__( 'Horizontal Alignment', 'wta-eae' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left'    => [
                            'title' => esc_html__( 'Left', 'wta-eae' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'wta-eae' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'wta-eae' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'selectors_dictionary' => [
                        'left' => 'align-items: flex-start; text-align: left',
                        'center' => 'align-items: center; text-align: center',
                        'right' => 'align-items: flex-end; text-align: right',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-content-section' => '{{VALUE}}',
                    ],
                    'condition' => [
                        'pre_layout!' => 'pre1'
                    ]
                ]
            );

            $this->add_responsive_control(
                'content_ver_alignment',
                [
                    'label' => esc_html__( 'Vertical Alignment', 'wta-eae' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'top'    => [
                            'title' => esc_html__( 'Top', 'wta-eae' ),
                            'icon' => 'eicon-v-align-top',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'wta-eae' ),
                            'icon' => 'eicon-v-align-middle',
                        ],
                        'end' => [
                            'title' => esc_html__( 'Bottom', 'wta-eae' ),
                            'icon' => 'eicon-v-align-bottom',
                        ],
                    ],
                    'selectors_dictionary' => [
                        'top' => 'flex-start',
                        'center' => 'center',
                        'end' => 'flex-end',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-content-section' => 'justify-content: {{VALUE}}',
                    ],
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                ]
            );

            $this->add_responsive_control(
                'content_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
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
                        '{{WRAPPER}} .eae-testimonial-content' => 'gap:{{SIZE}}{{UNIT}};',
                    ],
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                ]
            );

            $this->add_responsive_control(
                'content_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-ts-content-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                ]
            );

            $this->add_control(
                'avatar_style',
                [
                    'label' => esc_html__('Avatar','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $this->add_responsive_control(
                'avatar_width',
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
                        '{{WRAPPER}} .eae-testimonial-content .eae-content-img' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            $this->add_control(
                'avatar_position',
                [
                    'label' => esc_html__( 'Position', 'wta-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'row' => __('Left','wts-eae'),
                        'row-reverse' => __('Right','wts-eae'),
                    ],
                    'default' => 'row',
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-content-section .eae-author-info-wrapper' => 'flex-direction:{{VALUE}}',						
                    ],
                    'condition' => [
                        'pre_layout!' => 'pre1'
                    ]
                ]
            );

            $this->add_responsive_control(
                'avatar_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => 'px',
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 1,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-content-section .eae-author-info-wrapper' => 'gap:{{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'pre_layout!' => 'pre1'
                    ]
                ]
            );


            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'avatar_border',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content .eae-content-img .eae-avatar-image',
                ]
            );


            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'avatar_box_shadow',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content .eae-content-img .eae-avatar-image',
                    
                ]
            );

            $this->add_responsive_control(
                'avatar_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-content-img .eae-avatar-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'avatar_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-content-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'pre_layout!' => 'pre1'
                    ]
                ]
            );

            $this->add_control(
                'image_style',
                [
                    'label' => esc_html__('Image','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pre_layout!' => 'pre1'
                    ]
                ]
            );

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
            $default_device = array_key_first($break_value_arr);
            
            $this->add_control(
                'hide_below',
                [
                    'label' => __('Hide Image Below', 'wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $break_value_arr,
                    'default' => $default_device,
                    'condition' => [
                        'pre_layout' => 'pre2'
                    ]
                ]
            );

            $this->add_control(
                'img_position',
                [
                    'label' => esc_html__( 'Position', 'wta-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'row' => __('Left','wts-eae'),
                        'row-reverse' => __('Right','wts-eae'),
                    ],
                    'default' => 'row',
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content' => 'flex-direction:{{VALUE}}',						
                    ],
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                ]
            );


            $this->add_responsive_control(
                'img_width',
                [
                    'label' => esc_html__('Width','wts-eae'),
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
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-img-wrapper' => 'width:{{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .eae-testimonial-content .eae-additional-image.eae-preset-2' => 'width:{{SIZE}}{{UNIT}}',
                    ],
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'img_border',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content .eae-ts-img-wrapper, {{WRAPPER}} .eae-testimonial-content .eae-additional-image.eae-preset-2',
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'img_box_shadow',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content .eae-ts-img-wrapper,{{WRAPPER}} .eae-testimonial-content .eae-additional-image.eae-preset-2',
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                    
                ]
            );

            $this->add_responsive_control(
                'img_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-img-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-testimonial-content .eae-additional-image.eae-preset-2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition'=>[
                        'pre_layout!' => 'pre1'
                    ],
                ]
            );


           

            $this->add_control(
                'author_style',
                [
                    'label' => esc_html__('Author','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'author_background',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-name',
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'author_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-name',
                ]
            );

            $this->add_control(
                'author_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-name' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'author_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'author_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'designation_style',
                [
                    'label' => esc_html__('Designation','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'desig_background',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-designation',
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'desig_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-designation',
                ]
            );

            $this->add_control(
                'desig_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-designation' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'desig_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-designation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'desig_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-designation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'c_name_style',
                [
                    'label' => esc_html__('Company Name','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );


            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'c_name_background',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-company-name',
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'c_name_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-company-name',
                ]
            );

            $this->add_control(
                'c_name_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-company-name' => 'color: {{VALUE}};',
                    ],
                ]
            );


            $this->add_responsive_control(
                'c_name_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-company-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'c_name_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-company-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'des_style',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before'
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'des_background',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-content-desc',
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'des_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-content-desc',
                ]
            );

            $this->add_control(
                'des_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-content-desc' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'des_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-content-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'des_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-content-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();

        $this->start_controls_section(
            'testimonial_rating_style',
            [
                'label' => esc_html__( 'Rating', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


            $this->add_responsive_control(
                'rating_size',
                [
                    'label' => esc_html__('Icon Size','wts-eae'),
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
                    'default' => [
                        'unit' => 'px',
                        'size' => 14,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-rating i' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-testimonial-content .eae-rating svg' => 'height:{{SIZE}}{{UNIT}}; width:{{SIZE}}{{UNIT}}',
                    ],
                ]
            );
            
            $this->add_responsive_control(
                'rating_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
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
                        '{{WRAPPER}} .eae-testimonial-content .eae-rating' => 'gap:{{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'marked_color',
                [
                    'label' => esc_html__( 'Marked Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#FFC61A',
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-rating .eae-ts-filled-icon' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .eae-testimonial-content .eae-rating .eae-ts-filled-icon svg' => 'fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'unmarked_color',
                [
                    'label' => esc_html__( 'Unmarked Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#FFC61A',
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-rating .eae-ts-unfilled-icon' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .eae-testimonial-content .eae-rating .eae-ts-unfilled-icon svg' => 'fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'rating_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();

        $this->start_controls_section(
            'ts_slider_style',
            [
                'label' => esc_html__( 'Slider', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $args = [
            'navigation_icon_size' => 20
        ];

            Swiper_helper::carousel_style_section($this, $args);

        $this->end_controls_section();

        $this->start_controls_section(
            'ts_overlay_style',
            [
                'label' => esc_html__( 'Overlay', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pre_layout' => 'pre3'
                ]
            ]
        );

            $this->add_control(
                'author_overlay',
                [
                    'label' => esc_html__('Author','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_author' => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'author_overlay_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-overlay-name',
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_author' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'author_overlay_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-overlay-name' => 'color: {{VALUE}};',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_author' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'desig_overlay',
                [
                    'label' => esc_html__('Designation','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_designation' => 'yes'
                    ]
                ]
            );


            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'desig_overlay_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-overlay-designation',
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_designation' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'desig_overlay_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-overlay-designation' => 'color: {{VALUE}};',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_designation' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'c_name_overlay',
                [
                    'label' => esc_html__('Company Name','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_c_name' => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'c_name_overlay_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-overlay-company-name',
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_c_name' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'c_name_overlay_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-overlay-company-name' => 'color: {{VALUE}};',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_c_name' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'rating_overlay',
                [
                    'label' => esc_html__('Rating','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_rating' => 'yes'
                    ]
                ]
            );

            $this->add_responsive_control(
                'overlay_rating_size',
                [
                    'label' => esc_html__('Icon Size','wts-eae'),
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
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-rating i' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-rating svg' => 'height:{{SIZE}}{{UNIT}}; width:{{SIZE}}{{UNIT}}',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_rating' => 'yes'
                    ]
                ]
            );
            
            $this->add_responsive_control(
                'overlay_rating_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
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
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-rating' => 'gap:{{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_rating' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'overlay_marked_color',
                [
                    'label' => esc_html__( 'Marked Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-rating .eae-ts-filled-icon' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-rating .eae-ts-filled-icon svg' => 'fill: {{VALUE}};',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_rating' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'overlay_unmarked_color',
                [
                    'label' => esc_html__( 'Unmarked Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-rating .eae-ts-unfilled-icon' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-rating .eae-ts-unfilled-icon svg' => 'fill: {{VALUE}};',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_rating' => 'yes'
                    ]
                ]
            );

            $this->add_responsive_control(
                'overlay_rating_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-ts-overlay-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_rating' => 'yes'
                    ]
                ]
            );


            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'overlay_background',
                    'types' => [ 'classic', 'gradient' , 'image' ],
                    'separator' => 'before',
                    'selector' => '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info',
                    'condition' => [
                        'pre_layout' => 'pre3'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'overlay_border',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info',
                    'condition' => [
                        'pre_layout' => 'pre3'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'overlay_box_shadow',
                    'selector' => '{{WRAPPER}}  .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info',
                    'condition' => [
                        'pre_layout' => 'pre3'
                    ]
                ]
            );

            $this->add_responsive_control(
                'overlay_direction',
                [
                    'label' => esc_html__('Direction','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Row - horizontal','wts-eae'),
                            'icon' => 'eicon-arrow-right',
                        ],
                        'up' => [
                            'title' => esc_html__('Column - vertical','wts-eae'),
                            'icon' => 'eicon-arrow-down',
                        ],
                        
                        'right' =>[
                            'title' => esc_html__('Row - reversed','wts-eae'),
                            'icon' => 'eicon-arrow-left',
                        ] , 
                        'down' =>[
                            'title' => esc_html__('Column - reversed','wts-eae'),
                            'icon' => 'eicon-arrow-up',
                        ]
                    ],
                    'default' => 'up',
                    'toggle' => false,
                    'selectors_dictionary' => [
                        'left' => 'row',
                        'up' => 'column',
                        'right' => 'row-reverse',
                        'down' => 'column-reverse',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info' => 'flex-direction: {{VALUE}}',
                    ],
                ]
            );


            $this->add_responsive_control(
                'overlay_row_alignment',
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
                            'icon' => 'eicon-justify-center-h',
                        ],
                        'end' => [
                            'title' => esc_html__( 'Bottom', 'wta-eae' ),
                            'icon' => 'eicon-justify-end-h',
                        ],
                        'spaceBetween' => [
                            'title' => esc_html__( 'Space between', 'wta-eae' ),
                            'icon' => 'eicon-justify-space-between-h',
                        ],
                        'spaceAround' => [
                            'title' => esc_html__( 'Space around', 'wta-eae' ),
                            'icon' => 'eicon-justify-space-around-h',
                        ],
                        'spaceEvenly' => [
                            'title' => esc_html__( 'Space evenly', 'wta-eae' ),
                            'icon' => 'eicon-justify-space-evenly-h',
                        ],
                    ],
                    'toggle' => false,
                    'selectors_dictionary' => [
                        'start' => 'start',
                        'center' => 'center',
                        'end' => 'end',
                        'spaceBetween' => 'space-between',
                        'spaceAround' => 'space-around',
                        'spaceEvenly' => 'space-evenly',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info' => 'justify-content: {{VALUE}}',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_direction' => ['left','right']
                    ],
                ]
            );


            $this->add_responsive_control(
                'column_alignment',
                [
                    'label' => esc_html__( 'Alignment', 'wta-eae' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left'    => [
                            'title' => esc_html__( 'Left', 'wta-eae' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'wta-eae' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'wta-eae' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'selectors_dictionary' => [
                        'left' => 'flex-start',
                        'center' => 'center',
                        'right' => 'flex-end',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-container .eae-ts-overlay-block .eae-ts-info' => 'align-items: {{VALUE}}',
                        '{{WRAPPER}} .eae-testimonial-container .eae-ts-overlay-block .eae-ts-info .eae-ts-info-container' => 'align-items: {{VALUE}}',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_direction!' => ['left','right']
                    ],
                ]
            );

            $this->add_responsive_control(
                'row_text_alignment',
                [
                    'label' => esc_html__( 'Content position', 'wta-eae' ),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left'    => [
                            'title' => esc_html__( 'Left', 'wta-eae' ),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__( 'Center', 'wta-eae' ),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'right' => [
                            'title' => esc_html__( 'Right', 'wta-eae' ),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'default'=>'center',
                    'selectors_dictionary' => [
                        'left' => 'flex-start',
                        'center' => 'center',
                        'right' => 'flex-end',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-testimonial-container .eae-ts-overlay-block .eae-ts-info-container' => 'align-items: {{VALUE}}',
                    ],
                    'condition' => [
                        'pre_layout' => 'pre3',
                        'overlay_direction' => ['left','right']
                    ],
                ]
            );

            $this->add_responsive_control(
                'overlay_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
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
                        '{{WRAPPER}} .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info-container' => 'gap:{{SIZE}}{{UNIT}};',
                    ],
                    'condition'=>[
                        'pre_layout' => 'pre3',
                    ],
                ]
            );

            $this->add_responsive_control(
                'overlay_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition'=>[
                        'pre_layout' => 'pre3',
                    ],
                ]
            );

            $this->add_responsive_control(
                'overlay_margin',
                [
                    'label' => esc_html__( 'Margin', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', 'em', '%' ],
                    'selectors' => [
                        '{{WRAPPER}}  .eae-testimonial-content .eae-ts-overlay-block .eae-ts-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition'=>[
                        'pre_layout' => 'pre3',
                    ],
                ]
            );

        $this->end_controls_section();

        $this->start_controls_section(
            'ts_bg_overlay',
            [
                'label' => esc_html__( 'Background Overlay', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-testimonial-container .eae-ts-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'opacity',
			[
				'label'                 => __( 'Opacity', 'wts-eae' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .eae-testimonial-container .eae-ts-overlay' => 'opacity: {{SIZE}};',
				],
			]
		);
        $this->end_controls_section();
    }
    public function set_swiper_default_value($swiper_data){
        $swiper_data['slidesPerView']['tablet_extra'] = 1;
        $swiper_data['slidesPerView']['mobile_extra'] = 1;
        return $swiper_data;
    }

    public function render(){
        $settings = $this->get_settings_for_display();
        $settings['wid'] = $this->get_id();
        add_filter("eae_swiper_data/{$this->get_id()}", [$this,'set_swiper_default_value'], 10, 1); 
        $swiper_data = Swiper_helper::get_swiper_data($settings);
        $root_class = ['eae-testimonial-wrapper'];
        $container = [];
        $container []= 'eae-testimonial-container eae-swiper-container eae-swiper';
        $container []= 'eae-'.$settings['pre_layout'];
        $root_class [] = 'eae-testimonial-slider eae-swiper-outer-wrapper';
        $slider_id = rand(99,9999);
        if($settings['arrows_layout'] == 'inside'){
            $this->add_render_attribute('_root','class','eae-hpos-' . $settings['arrow_horizontal_position']);
            $this->add_render_attribute('_root','class','eae-vpos-' . $settings['arrow_vertical_position']);
        }
        if($settings['arrows_layout'] == 'outside'){
            $this->add_render_attribute('_root','class','eae-slider-id-'. $slider_id);
        }
        if( $settings['auto_height'] !== 'yes'){
            $this->add_render_attribute('_root','class','eae-height-100');
        }

        $this->add_render_attribute('container', 'class', $container);
        $this->add_render_attribute('container','data-eae-slider-id',$slider_id);
        $this->add_render_attribute('container','class','eae-slider-id-'. $slider_id);
        $this->add_render_attribute('_root','data-stacked',$settings['hide_below']);
        $this->add_render_attribute('_root', 'class', $root_class);
        $this->add_render_attribute('_root','data-swiper-settings', wp_json_encode( $swiper_data ));
        $this->add_render_attribute('content', 'class', 'eae-testimonial-content eae-swiper-slide swiper-slide');
        $this->add_render_attribute('swiper_wrapper', 'class', 'eae-testimonial-content-wrapper eae-swiper-wrapper swiper-wrapper');
        ?>
		<div <?php echo  $this->get_render_attribute_string('_root') ?>> 
		    <div <?php echo  $this->get_render_attribute_string('container') ?>> 
                <div <?php echo  $this->get_render_attribute_string('swiper_wrapper') ?> >
                    <?php
                    foreach ($settings['testimonial_data'] as $testimonial_item) { ?>
                        <div <?php echo $this->get_render_attribute_string('content')?>>
                            <?php
                            switch ($settings['pre_layout']) {
                                case 'pre1':
                                    $this->preset1($testimonial_item,$settings);
                                break;
                                case 'pre2':
                                    $this->preset2($testimonial_item,$settings);
                                break;
                                case 'pre3':
                                    $this->preset3($testimonial_item,$settings);
                                break; 
                            }
                            ?>
                              <div class="eae-ts-overlay"></div>
                        </div >
                    <?php } ?> 
                </div>
                <?php
                Swiper_helper::get_swiper_pagination($settings);
                if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
                    Swiper_helper::get_swiper_arrows($settings);
                }
                Swiper_helper::get_swiper_scrolbar($settings); ?>
            </div>
            <?php 
            if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
                Swiper_Helper::get_swiper_arrows($settings);
            }	
            ?>
        </div><?php        
    }

    public function preset1($testimonial_item,$settings) {
        $size  = $testimonial_item['image_size'];
        $image_class = '';
        $image_class .= 'eae-content-img ';
        $this->add_render_attribute('image', 'class', $image_class);

        if (!empty($testimonial_item['description'])) { ?>
            <div class="eae-content-desc"><?php echo Helper::eae_wp_kses($testimonial_item['description']); ?></div><?php 
        } 

        if (!empty($testimonial_item['image']['url'])) { 
            ?> <div <?php echo  $this->get_render_attribute_string('image') ?>> <?php
            if (!empty($testimonial_item['image']['id'])) {
                echo wp_get_attachment_image($testimonial_item['image']['id'], $size, false, array('class' => 'eae-avatar-image'));
            } 
            else {
                $imgUrl = esc_url($testimonial_item['image']['url']);?> 
            
                <img src="<?php echo $imgUrl; ?>" class="eae-avatar-image"> <?php
            }
            ?> </div> <?php
        }

        if (!empty($testimonial_item['author'])) { ?>
            <div class="eae-name"><?php echo Helper::eae_wp_kses($testimonial_item['author']); ?></div><?php 
        } 

        if (!empty($testimonial_item['designation'])) { ?>
            <div class="eae-designation"><?php echo Helper::eae_wp_kses($testimonial_item['designation']); ?></div><?php 
        } 

        if (!empty($testimonial_item['company_name'])) { ?>
            <div class="eae-company-name"><?php echo Helper::eae_wp_kses($testimonial_item['company_name']); ?></div><?php 
        } 

        if (isset($testimonial_item['rating'])) {?> 
            <div class="eae-rating eae-content-rating"><?php
                $this->render_rating($testimonial_item['rating']);?>
            </div><?php
        } 
    }

    public function preset2($testimonial_item,$settings) {
        $size  = $testimonial_item['image_size'];
        $additional_image = $testimonial_item['additional_image_size'];
        $this->add_render_attribute('image', 'class','eae-additional-image eae-preset-2 ');
        $this->add_render_attribute('section', 'class', 'eae-ts-content-section');
        $this->add_render_attribute('wrapper', 'class', 'eae-author-info-wrapper');

        if (!empty($testimonial_item['additional_img']['url'])) { 
            ?> <div <?php echo  $this->get_render_attribute_string('image') ?>> <?php
            if (!empty($testimonial_item['additional_img']['id'])) {
                echo wp_get_attachment_image($testimonial_item['additional_img']['id'], $additional_image, false, array('class' => 'eae-ts-content-image'));
            } 
            else {
                $imgUrl = $testimonial_item['additional_img']['url'];?> 
                <img src="<?php echo $imgUrl; ?>" class="eae-ts-content-image"> <?php
            }
            ?> </div> <?php
        }?> 
        <div <?php echo  $this->get_render_attribute_string('section') ?>> <?php

            if (isset($testimonial_item['rating'])) {?> 
                <div class="eae-rating eae-content-rating"><?php
                    $this->render_rating($testimonial_item['rating']);?>
                </div><?php
            } 

            if (!empty($testimonial_item['description'])) { ?>
                <div class="eae-content-desc"><?php echo Helper::eae_wp_kses($testimonial_item['description']); ?></div><?php 
            } ?>
            
            <div <?php echo $this->get_render_attribute_string('wrapper')?>><?php
                if (!empty($testimonial_item['image']['url'])) {
                    ?>
                    <div class="eae-content-img"><?php
                        if (!empty($testimonial_item['image']['id'])) {
                            echo wp_get_attachment_image($testimonial_item['image']['id'], $size, false, array('class' => 'eae-avatar-image'));
                        } 
                        else {
                            $imgUrl = esc_url($testimonial_item['image']['url']);?> 
                            <img src="<?php echo $imgUrl; ?>" class="eae-avatar-image"> <?php
                        }?>
                    </div><?php
                }
                if(!empty($testimonial_item['author']) || !empty($testimonial_item['designation']) || !empty($testimonial_item['company_name'])){?> 
                    <div class="eae-ts-author-info"><?php
                        if (!empty($testimonial_item['author'])) {
                            ?>
                            <div class="eae-name"><?php echo Helper::eae_wp_kses($testimonial_item['author']); ?></div>
                            <?php
                        }

                        if (!empty($testimonial_item['designation'])) {
                            ?>
                            <div class="eae-designation"><?php echo Helper::eae_wp_kses($testimonial_item['designation']); ?></div>
                            <?php
                        }

                        if (!empty($testimonial_item['company_name'])) {
                            ?>
                            <div class="eae-company-name"><?php echo Helper::eae_wp_kses($testimonial_item['company_name']); ?></div>
                            <?php
                        }?>
                    </div>
                <?php } ?>
            </div>
        </div>   <?php   
    }  
    public function preset3($testimonial_item,$settings) {
        $additional_image = $testimonial_item['additional_image_size'];
        $size  = $testimonial_item['image_size'];
        $this->add_render_attribute('overlay_layout','class','eae-ts-info');
        $this->add_render_attribute('wrapper','class','eae-author-info-wrapper');
        $this->add_render_attribute('section', 'class', 'eae-ts-content-section'); ?>

        <div <?php echo  $this->get_render_attribute_string('section') ?>> <?php
            if (isset($testimonial_item['rating']) && !empty($settings['show_rating'])) {?> 
                <div class="eae-rating eae-content-rating"><?php
                    $this->render_rating($testimonial_item['rating']);?>
                </div><?php
            } 
            if (!empty($testimonial_item['description'])) { ?>
                <div class="eae-content-desc"><?php echo Helper::eae_wp_kses($testimonial_item['description']); ?></div><?php 
            } ?>

            <div <?php echo $this->get_render_attribute_string('wrapper')?>><?php
                if (!empty($testimonial_item['image']['url'])) {
                    ?>
                    <div class="eae-content-img"><?php
                        if (!empty($testimonial_item['image']['id'])) {
                            echo wp_get_attachment_image($testimonial_item['image']['id'], $size, false, array('class' => 'eae-avatar-image'));
                        } 
                        else {
                            $imgUrl = esc_url($testimonial_item['image']['url']);?> 
                        
                            <img src="<?php echo $imgUrl; ?>" class="eae-avatar-image"> <?php
                        }?>
                    </div><?php
                }   ?> 
                <div class="eae-ts-author-info"><?php
                    if(!empty($settings['show_author'])){
                        if (!empty($testimonial_item['author'])) {
                            ?>
                            <div class="eae-name"><?php echo  Helper::eae_wp_kses($testimonial_item['author']); ?></div>
                            <?php
                        }
                    }
                    if(!empty($settings['show_designation'])){
                        if (!empty($testimonial_item['designation'])) {
                            ?>
                            <div class="eae-designation"><?php echo Helper::eae_wp_kses($testimonial_item['designation']); ?></div>
                            <?php
                        }
                    }
                    if(!empty($settings['show_c_name'])){
                        if (!empty($testimonial_item['company_name'])) {
                            ?>
                            <div class="eae-company-name"><?php echo Helper::eae_wp_kses($testimonial_item['company_name']); ?></div>
                            <?php
                    }
                    }?>
                </div>
            </div> 
        </div>
        <div class="eae-ts-img-wrapper"><?php
            if (!empty($testimonial_item['additional_img']['url'])) { 
                ?> <div class="eae-additional-image"> <?php
                if (!empty($testimonial_item['additional_img']['id'])) {
                    echo wp_get_attachment_image($testimonial_item['additional_img']['id'], $additional_image, false, array('class' => 'eae-ts-content-image'));
                } 
                else {
                    $imgUrl = esc_url($testimonial_item['additional_img']['url']);?> 
                
                    <img src="<?php echo $imgUrl; ?>" class="eae-ts-content-image"> <?php
                }
                ?> </div> <?php
                if(!empty($settings['overlay_author']) || !empty($settings['overlay_designation']) || !empty($settings['overlay_c_name']) || !empty($settings['overlay_rating'])){?>
                    <div class="eae-ts-overlay-block">
                        <div  <?php echo $this->get_render_attribute_string('overlay_layout')?>>
                            <div class=eae-ts-info-container>
                                <?php
                                if(!empty($settings['overlay_author'])){    
                                    if (isset($testimonial_item['author'])) {
                                        ?>
                                        <div class="eae-ts-overlay-name"><?php echo Helper::eae_wp_kses($testimonial_item['author']); ?></div>
                                        <?php
                                    }
                                }
                                if(!empty($settings['overlay_designation'])){
                                    if (isset($testimonial_item['designation'])) {
                                        ?>
                                        <div class="eae-ts-overlay-designation"><?php echo Helper::eae_wp_kses($testimonial_item['designation']); ?></div>
                                        <?php
                                    }
                                }
                                if(!empty($settings['overlay_c_name'])){
                                    if (isset($testimonial_item['company_name'])) {
                                        ?>
                                        <div class="eae-ts-overlay-company-name"><?php echo Helper::eae_wp_kses($testimonial_item['company_name']); ?></div>
                                        <?php
                                    }
                                }?>
                            </div> <?php 
                            if (!empty($settings['overlay_rating'])){?>
                                <div class=eae-ts-rating-container><?php
                                    if (isset($testimonial_item['rating'])) {
                                        ?>
                                        <div class="eae-ts-overlay-rating eae-rating">
                                            <?php $this->render_rating($testimonial_item['rating'])?>
                                        </div>
                                        <?php
                                    }?> 
                                </div> <?php
                            }?>  
                        </div>
                    </div><?php
                }
            }
            ?>
        </div>
        <?php
    }

    public function render_rating($rating) {
        $settings = $this->get_settings_for_display(); 
        $filled_icon = '';
        $half_fill_icon = '';
        $unmarked_icon = '';
        
        if (isset($settings['filled_icon']) && is_array($settings['filled_icon']) && count($settings['filled_icon']) > 0) {
            $filled_icon = $settings['filled_icon'];
        }
        $class = 'eae-ts-filled-icon';
        $filled_attr = [ 
            'class' => [$class]
        ];
        if (isset($settings['half_fill_icon']) && is_array($settings['half_fill_icon']) && count($settings['half_fill_icon']) > 0) {
            $half_fill_icon = $settings['half_fill_icon'];
        }
        $class = 'eae-ts-filled-icon';
        $half_fill_attr = [
                'class' => [$class]
        ];

        if (isset($settings['unmarked_icon']) && is_array($settings['unmarked_icon']) && count($settings['unmarked_icon']) > 0) {
            $unmarked_icon = $settings['unmarked_icon'];
        }
        $class = 'eae-ts-unfilled-icon';
        $unmarked_attr = [
            'class' => [$class]
        ];
    
        // Calculate the number of filled and half-filled stars
        $numFilledStars = floor($rating);
        $hasHalfFilledStar = $rating - $numFilledStars > 0;

        // Render filled stars
        for ($i = 1; $i <= $numFilledStars; $i++) {
            ?> <span class="eae-ts-icon eae-ts-filled-icon"> <?php
                Icons_Manager::render_icon( $filled_icon, $filled_attr);
            ?> </span> <?php
        }
    
        // Render half-filled icon if necessary
        if ($hasHalfFilledStar) {
            ?> <span class="eae-ts-icon eae-ts-filled-icon"> <?php
                Icons_Manager::render_icon( $half_fill_icon, $half_fill_attr);
            ?> </span> <?php
            $numEmptyStars = 5 - $numFilledStars - 1; 
        } else {
            $numEmptyStars = 5 - $numFilledStars;
        }
    
        // Render unfilled stars
        for ($i = 1; $i <= $numEmptyStars; $i++) {
            ?> <span class="eae-ts-icon eae-ts-unfilled-icon"> <?php
                Icons_Manager::render_icon( $unmarked_icon, $unmarked_attr);
            ?> </span> <?php
        }
    }
}
