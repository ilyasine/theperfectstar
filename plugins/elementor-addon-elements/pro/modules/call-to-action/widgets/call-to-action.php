<?php

namespace WTS_EAE\Pro\Modules\CallToAction\Widgets;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class CallToAction extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-call-to-action';
	}

	public function get_title() {
		return __( 'Call To Action', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-call-to-action';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'call to action'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

    protected function register_controls(){

        $this->start_controls_section(
			'eae_cta_content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);
        

        $this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'split' => 'Split',
					'cover' => 'Cover',
				],

                'prefix_class' => 'eae-cta-layout-',
				'default' => 'cover',
			]
		);

        $this->add_responsive_control(
            'image_position',
            [
                'label' => esc_html__('Image Position','wts-eae'),
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
                'default' => 'up',
                'toggle' => false,
                'selectors_dictionary' => [
                    'left' => 'flex-direction:row',
                    'up' => 'flex-direction:column',
                    'right' => 'flex-direction:row-reverse',
                    'down' => 'flex-direction:column-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-wrapper' => '{{VALUE}}',
                ],
                'prefix_class' => 'eae-cta-position-',
                'condition' => [
                   'layout' => 'split', 
                ]
            ]
        );

        $this->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'separator' => 'none',
			]
		);



        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'cta_icon',
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
            ]
        );

        $this->add_control(
			'title',
			[
				'label'			 => esc_html__( 'Title', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'label_block'	 => true,
				'default'		 => esc_html__( 'This is the Title', 'wta-eae' ),
			]
		);

        $this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title Tag', 'wta-eae' ),
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

        $this->add_control(
			'Sub_title',
			[
				'label'			 => esc_html__( 'Sub Title', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'label_block'	 => true,
				'default'		 => esc_html__( 'This is the Sub Title', 'wta-eae' ),
			]
		);

        $this->add_control(
			'sub_title_tag',
			[
				'label' => esc_html__( 'Sub Title Tag', 'wta-eae' ),
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
				'default' => 'h5',
			]
		);

        $this->add_control(
            'description',
            [
                'label' => esc_html__('Description' , 'wts-eae'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Lorem ipsum dolor sit amet consectetur adipiscing elit dolor',
                'dynamic' => [
                    'active' => true,
                ],
                'label_block'	 => true,
            ]
        );

        $this->add_control(
			'ribbon',
			[
				'label' => esc_html__( 'Ribbon', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

        $this->add_control(
			'ribbon_position',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => 'Left',
					'right' => 'Right',
				],
				// 'selectors' => [
				// 	'{{WRAPPER}} .cta-pri-btn' => 'flex-direction: {{VALUE}}',						
				// ],
                'condition'=>[
                    'ribbon'=> 'yes'
                ],
				'default' => 'left',
			]
		);
        $this->add_control(
			'ribbon_text',
			[
				'label'			 => esc_html__( 'Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'default'		 => esc_html__( 'Sale Now', 'wta-eae' ),
                'condition'=>[
                    'ribbon'=> 'yes'
                ],
			]
		);

        $this->add_control(
			'button_heading',
			[
				'label'     => __( 'Button', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'pri_btn_title',
			[
				'label'			 => esc_html__( 'Primary Button', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'label_block'	 => true,
				'default'		 => esc_html__( 'Buy Now', 'wta-eae' ),
			]
		);

        $this->add_control(
			'pri_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '#',
				],
			]
		);

        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'pri_btn_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> true,
            ]
        );

        $this->add_control(
			'pri_btn_position',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'row-reverse' => 'Before',
					'row' => 'After',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-cta-pri-btn' => 'flex-direction: {{VALUE}}',						
				],
				'default' => 'row',
			]
		);


        $this->add_control(
			'sec_btn_title',
			[
				'label'			 => esc_html__( 'Secondary Button', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
                'separator' => 'before',
				'label_block'	 => true,
				'default'		 => esc_html__( 'Buy Now', 'wta-eae' ),
			]
		);

        $this->add_control(
			'sec_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '#',
				],
			]
		);

        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'sec_btn_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> true,
            ]
        );

        $this->add_control(
			'sec_btn_position',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'row-reverse' => 'Before',
					'row' => 'After',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-cta-sec-btn' => 'flex-direction: {{VALUE}}',						
				],
				'default' => 'row',
			]
		);

        $this->end_controls_section();

		$this->start_controls_section(
            'eae_cta_box_style',
            [
                'label' => esc_html__( 'Box', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 400,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}}  .eae-cta-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.eae-cta-layout-split  .eae-cta-wrapper .eae-cta-content ' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_border',
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selector'=> '{{WRAPPER}} .eae-cta-wrapper',
            ]
        );

        $this->add_responsive_control(
			'box_br',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .eae-cta-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .eae-cta-wrapper',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'eae_cta_image_style',
            [
                'label' => esc_html__( 'Image', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

       
        $this->add_responsive_control(
            'img_height',
            [
                'label' => esc_html__( 'Height', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 500,
                    'unit' => 'px',
                ],
                'condition' => [
                    'layout'=>'split'
                ],
                'selectors' => [
                    '{{WRAPPER}}.eae-cta-layout-split .eae-cta-img' => 'max-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'img_width',
            [
                'label' => esc_html__( 'Width', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'condition' => [
                    'layout'=>'split'
                ],
                'selectors' => [
                    '{{WRAPPER}}.eae-cta-layout-split .eae-cta-img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'img_position',
            [
                'label' => esc_html__('Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top','wts-eae'),
                        'icon' => 'eicon-justify-start-v',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-justify-space-between-v',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom','wts-eae'),
                        'icon' => 'eicon-justify-end-v',
                    ]
                ],
                'selectors_dictionary' => [
                    'top' => 'baseline',
                    'center' => 'center',
                    'bottom' => 'flex-end',
                ],
                'condition' => [
                    'layout' => 'split', 
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-img' => 'align-self: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'img_background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}}  .eae-cta-img img',
            ]
        );

        $this->add_control(
			'object-fit',
			[
				'label' => esc_html__( 'Object Fit', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Default', 'wts-eae' ),
					'fill' => esc_html__( 'Fill', 'wts-eae' ),
					'cover' => esc_html__( 'Cover', 'wts-eae' ),
				],
				'default' => 'cover',
				'selectors' => [
					'{{WRAPPER}} .eae-cta-img img' => 'object-fit: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'object-position',
			[
				'label' => esc_html__( 'Object Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'center center' => esc_html__( 'Center Center', 'wts-eae' ),
					'center left' => esc_html__( 'Center Left', 'wts-eae' ),
					'center right' => esc_html__( 'Center Right', 'wts-eae' ),
					'top center' => esc_html__( 'Top Center', 'wts-eae' ),
					'top left' => esc_html__( 'Top Left', 'wts-eae' ),
					'top right' => esc_html__( 'Top Right', 'wts-eae' ),
					'bottom center' => esc_html__( 'Bottom Center', 'wts-eae' ),
					'bottom left' => esc_html__( 'Bottom Left', 'wts-eae' ),
					'bottom right' => esc_html__( 'Bottom Right', 'wts-eae' ),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .eae-cta-img img' => 'object-position: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
            'img_padding',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-img img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );


        $this->add_control(
			'hover_effects',
			[
				'label'     => __( 'Hover Effects', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
                    'none' => esc_html__( 'None', 'wts-eae' ),
                    'zoom-in' => esc_html__( 'Zoom-In', 'wts-eae' ),
                    'zoom-out' => esc_html__( 'Zoom-Out', 'wts-eae' ),
                    'move-left' => esc_html__( 'Move-Left', 'wts-eae' ),
                    'move-right' => esc_html__( 'Move-Right', 'wts-eae' ),
                    'move-up' => esc_html__( 'Move-Up', 'wts-eae' ),
                    'move-down' => esc_html__( 'Move-down', 'wts-eae' ),
                ],
                'prefix_class' => 'eae-cta-img-ani-',
				'default' => 'zoom-in',
			]
		);

        $this->add_control(
            'hover_animation_duration',
            [
                'label' => esc_html__( 'Transition Duration' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default'     => 1,
				'min'         => 1,
				'max'         => 10,
                'selectors'=>[
                    '{{WRAPPER}} .eae-cta-img img'    => 'transition-duration: {{value}}s',
                ],
                'condition'=>[
                    'hover_animation!' => 'none'
                ],
            ]
        );

    
        $this->add_control(
			'img_overlay',
			[
				'label'     => __( 'Overlay', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
				
			]
		);

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-wrapper .eae-cta-overlay' => 'background-color:{{VALUE}}',
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .eae-cta-wrapper img',
			]
		);

        $this->add_control(
            'hover_overlay_color',
            [
                'label' => esc_html__('Hover Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-wrapper:hover .eae-cta-overlay' => 'background-color:{{VALUE}}',
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image-css-filters-hover',
                'label' => esc_html__('Hover CSS Filter','wts-eae'),
                'selector' => '{{WRAPPER}} .eae-cta-wrapper:hover img',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'eae_cta_content_style',
            [
                'label' => esc_html__( 'Content', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}}  .eae-cta-content',
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
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
                        'icon' => 'eicon-justify-space-between-h',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-justify-end-h',
                       
                    ]
                ],
                'selectors_dictionary' => [
                    'left' => 'start',
                    'center' => 'center',
                    'right' => 'end',
                ],
                'default' => 'center',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-content' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'text_alignment',
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
					'{{WRAPPER}} .eae-cta-content' => 'text-align: {{VALUE}}',						
				],
			]
		);


        $this->add_control(
            'content_v_alignment',
            [
                'label' => esc_html__('Vertical Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top','wts-eae'),
                        'icon' => 'eicon-justify-start-v',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-justify-space-between-v',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom','wts-eae'),
                        'icon' => 'eicon-justify-end-v',
                    ]
                ],
                'selectors_dictionary' => [
                    'top' => 'start',
                    'center' => 'center',
                    'bottom' => 'end',
                ],
                'condition' => [
                    'layout' => 'split', 
                ],
                'default' => 'center',
               
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-content' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_gap',
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
                    '{{WRAPPER}} .eae-cta-content' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selector'=> '{{WRAPPER}} .eae-cta-content',
            ]
        );


        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'con_box_shadow',
				'selector' => '{{WRAPPER}} .eae-cta-content',
			]
		);

        $this->add_responsive_control(
			'content_br',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .eae-cta-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .eae-cta-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );

        $this->add_responsive_control(
            'content_margin',
            [
                'label' => esc_html__( 'Margin', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],               
            ]
        );



        $this->add_control(
			'title_heading',
			[
				'label'     => __( 'Title', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .eae-cta-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .eae-cta-title',
            ]
        );

       
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-title' => 'color:{{VALUE}}',
                ]
            ]
        );

       
        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__('Hover Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-title:hover ' => 'color: {{VALUE}};', 
                ]
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => __( 'Spacing  (px)', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

    
        //  Sub TITLE

        $this->add_control(
			'sub_title_heading',
			[
				'label'     => __( 'Sub Title', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);



        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'selector' => '{{WRAPPER}} .eae-cta-sub-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_sub',
                'selector' => '{{WRAPPER}} .eae-cta-sub-title',
            ]
        );


        $this->add_control(
            'sub_title_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-sub-title' => 'color:{{VALUE}}',
                ]
            ]
        );

       
        $this->add_control(
            'sub_title_hover_color',
            [
                'label' => esc_html__('Hover Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-sub-title:hover ' => 'color: {{VALUE}};', 
                ]
            ]
        );


        $this->add_responsive_control(
            'sub_title_spacing',
            [
                'label' => __( 'Spacing  (px)', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-sub-title' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Description

        $this->add_control(
			'description_heading',
			[
				'label'     => __( 'Description', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);



        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .eae-cta-description',
            ]
        );

       

      
        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-description' => 'color:{{VALUE}}',
                ]
            ]
        );

        $this->add_control(
            'description_hover_color',
            [
                'label' => esc_html__('Hover Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-description:hover ' => 'color: {{VALUE}};', 
                ]
            ]
        );

        $this->add_responsive_control(
            'des_spacing',
            [
                'label' => __( 'Spacing  (px)', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-description' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
			'icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        Helper::global_icon_style_controls($this,[
			'name' => 'cta_icon',
			'selector' => '.eae-cta-icon'
	    ]);

        $this->add_control(
			'hov_animation_content',
			[
				'label'     => __( 'Hover Effects', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
                'condition' => [
                    'layout' => 'cover', 
                ]
			]
		);


        $this->add_control(
			'hover_animation_content',
			[
				'label' => esc_html__( 'Hover Animation', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options'=>[ 
                    'none'=>esc_html__('None','wpv-bu'),
                    'grow'=>esc_html__('Grow','wpv-bu'),
                    'shrink'=>esc_html__('Shrink','wpv-bu'),
                    'move-right'=>esc_html__('Move Right','wpv-bu'),
                    'move-left'=>esc_html__('Move Left','wpv-bu'),
                    'move-up'=>esc_html__('Move Up','wpv-bu'),
                    'move-down'=>esc_html__('Move Down','wpv-bu'),
                    'slide-out-right'=>esc_html__('Slide Out Right','wpv-bu'),
                    'slide-out-left'=>esc_html__('Slide Out Left','wpv-bu'),
                    'slide-out-up'=>esc_html__('Slide Out Up','wpv-bu'),
                    'slide-out-down'=>esc_html__('Slide Out Down','wpv-bu'),
                    'zoom-in'=>esc_html__('Zoom In','wpv-bu'),
                    'zoom-out'=>esc_html__('Zoom Out','wpv-bu'),
                    'fade-out'=>esc_html__('Fade Out','wpv-bu'),
                    'slide-in-right'=>esc_html__('Slide In Right','wpv-bu'),
                    'slide-in-left'=>esc_html__('Slide In Left','wpv-bu'),
                    'slide-in-up'=>esc_html__('Slide In Up','wpv-bu'),
                    'slide-in-down'=>esc_html__('Slide In Down','wpv-bu'),
                    'fade-in'=>esc_html__('Fade In','wpv-bu'),
                ], 
                // 'prefix_class' => '',
				'default' => 'none',
                'condition' => [
                    'layout' => 'cover', 
                ]
			]
		);

        $this->add_control(
            'hover_animation_duration_content',
            [
                'label' => esc_html__( 'Transition Duration' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default'     => 1,
				'min'         => 1,
				'max'         => 10,
                'selectors'=>[
                    '{{WRAPPER}} .eae-cta-content'   => 'transition-duration: {{value}}s',
                ],
                'condition' => [
                    'layout' => 'cover', 
                    'hover_animation_content!' => 'none'

                 ]
            ]
        );


        $this->end_controls_section();

      

        $this->start_controls_section(
            'eae_cta_button_style',
            [
                'label' => esc_html__( 'Button', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_spacing',
            [
                'label' => esc_html__( 'Spacing', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}} .eae-cta-button' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-pri-btn ,{{WRAPPER}} .eae-cta-sec-btn ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],   
                  'default' => [
                    'top' => '5',
                    'right' => '15',
                    'bottom' => '5',
                    'left' => '15',
                    'unit' => 'px',
                ], 
            ]
        );

        $this->add_responsive_control(
            'btn_position',
            [
                'label' => esc_html__('Position','wts-eae'),
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
            
                'toggle' => false,
                'selectors_dictionary' => [
                    'left' => 'row',
                    'up' => 'column',
                    'right' => 'row-reverse',
                    'down' => 'column-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-button' => 'flex-direction: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
			'Primary_Button_heading',
			[
				'label'     => __( 'Primary Button', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pri_btn_typography',
                'selector' => '{{WRAPPER}} .eae-cta-pri-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_pri_btn',
                'selector' => '{{WRAPPER}} .eae-cta-button .eae-cta-pri-btn',
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
            'pri_btn_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-pri-btn' => 'color:{{VALUE}}',
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pri_btn_background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}}  .eae-cta-pri-btn',
            ]
        );


        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pri_box_shadow',
				'selector' => '{{WRAPPER}} .eae-cta-pri-btn',
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pri_btn_border',
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selector'=> '{{WRAPPER}} .eae-cta-pri-btn',
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
            'pri_btn_hov_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-pri-btn:hover' => 'color:{{VALUE}}',
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'pri_btn_hov_background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}}  .eae-cta-pri-btn:hover',
            ]
        );


        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pri_box_hover_shadow',
				'selector' => '{{WRAPPER}} .eae-cta-pri-btn:hover',
			]
		);

        $this->add_control(
			'pri_btn_hover_border',
			[
				'label' => esc_html__( 'Border Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-cta-pri-btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);




        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
			'pri_box_br',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .eae-cta-pri-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

        $this->add_control(
			'pri_Btn_icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);


       
       

        Helper::global_icon_style_controls($this,[
			'name' => 'pri_btn_icon',
			'selector' => '.eae-cta-pri-icon'
	    ]);
		

       



        $this->add_control(
			'secondary_Button_heading',
			[
				'label'     => __( 'Secondary Button', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sec_btn_typography',
                'selector' => '{{WRAPPER}} .eae-cta-sec-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_sec_btn',
                'selector' => '{{WRAPPER}} .eae-cta-button .eae-cta-sec-btn',
            ]
        );


        $this->start_controls_tabs(
            'sec_style_tabs'
        );
        $this->start_controls_tab(
            'sec_style_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'wts-eae' ),
            ]
        );



        $this->add_control(
            'sec_btn_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-sec-btn' => 'color:{{VALUE}}',
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'sec_btn_background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}}  .eae-cta-sec-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'sec_btn_border',
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
                'selector'=> '{{WRAPPER}} .eae-cta-sec-btn',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'sec_box_shadow',
				'selector' => '{{WRAPPER}} .eae-cta-sec-btn',
			]
		);


        $this->end_controls_tab();
        $this->start_controls_tab(
            'sec_style_hover_tab',
            [
                'label' => esc_html__( 'Hover', 'wts-eae' ),
            ]
        );

        $this->add_control(
            'sec_btn_hov_color',
            [
                'label' => esc_html__('Color','wts-eae'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .eae-cta-sec-btn:hover' => 'color:{{VALUE}}',
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'sec_btn_hov_background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}}  .eae-cta-sec-btn:hover',
            ]
        );

        $this->add_control(
			'sec_btn_hover_border',
			[
				'label' => esc_html__( 'Border Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-cta-sec-btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'sec_box_hover_shadow',
				'selector' => '{{WRAPPER}} .eae-cta-sec-btn:hover',
			]
		);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control(
			'sec_box_br',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'{{WRAPPER}} .eae-cta-sec-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

        $this->add_control(
			'sec_Btn_icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);



       
        Helper::global_icon_style_controls($this,[
			'name' => 'sec_btn_icon',
			'selector' => '.eae-cta-sec-icon'
	    ]);
		
        $this->end_controls_section();


        $this->start_controls_section(
            'eae_cta_ribbon_style',
            [
                'label' => esc_html__( 'Ribbon Style', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'ribbon'=>'yes',
                ]

            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ribbon_background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}}  .eae-cta-ribbon p',
                'condition'=>[
                    'ribbon'=>'yes',
                ]
            ]
        );

        $this->add_control(
			'ribbon_txt_color',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-cta-ribbon p' => 'color: {{VALUE}};',
				],
                'condition'=>[
                    'ribbon'=>'yes',
                ]   
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ribbon_typography',
                'selector' => '{{WRAPPER}} .eae-cta-ribbon p',
                'condition'=>[
                    'ribbon'=>'yes',
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ribbon_shadow',
				'selector' => '{{WRAPPER}} .eae-cta-ribbon p',
                'condition'=>[
                    'ribbon'=>'yes',
                ]
			]
		);

        $this->add_responsive_control(
            'ribbon_size',
            [
                'label' => __( 'Distance', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cta-ribbon p' => 'margin-top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
                    'ribbon'=>'yes',
                ]
            ]
        );
        $this->end_controls_section();

    }
    public function render(){
        $settings = $this->get_settings_for_display(); 
        $this->add_render_attribute('img_wrapper', 'class', 'eae-cta-img');
        $this->add_link_attributes('pri_btn_link', $settings['pri_link']);
        $this->add_render_attribute('pri_btn_link', 'class', 'eae-cta-pri-btn');
        $this->add_link_attributes('sec_btn_link', $settings['sec_link']);
        $this->add_render_attribute('sec_btn_link', 'class', 'eae-cta-sec-btn');
        $this->add_render_attribute('eae-cta-content', 'class', 'eae-cta-content ');
        $attr = [
            'class' => 'cta-img'
        ];
        if($settings['layout']=='cover'){
            $this->add_render_attribute('eae-cta-content', 'class', 'eae-cta-con-ani-'.$settings['hover_animation_content']);
        }

        echo  "<div class='eae-cta-wrapper'>"; 
            ?> <div <?php echo  $this->get_render_attribute_string( 'img_wrapper' ) ?> > <?php
                $imgUrl =  $settings['image']['url'];
                echo "<span class='eae-cta-overlay'></span>";   
                    if(!empty($settings['image']['id'])){
                        $imgHtml = wp_get_attachment_image($settings['image']['id'], $settings['image_size'], false, $attr);
                        echo $imgHtml;
                    }else{
                        if(!empty($settings['image']['url'])){
                            $imgUrl = esc_url($settings['image']['url']);
                            echo "<img src={$imgUrl} class='cta-img'/>";
                        }
                    }
                echo "</div>";   
            if($settings['ribbon']=='yes'){
                ?>
                <div class="eae-cta-ribbon position-<?php echo esc_attr($settings['ribbon_position']);?>">
                    <p><?php echo Helper::eae_wp_kses($settings['ribbon_text']);?> </p>
                </div>
                <?php
            } 

            ?> <div <?php echo  $this->get_render_attribute_string( 'eae-cta-content' ) ?> > <?php
                Helper::render_icon_html($settings,$this,'cta_icon','eae-cta-icon');
                if(!empty($settings['title'])){
                    printf(
                        '<%1$s class=%2$s>%3$s</%1$s>',
                        Helper::validate_html_tag( $settings['title_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ], 'h2' ),
                        'eae-cta-title',
                        $settings['title'],
                    );
                }    
                if(!empty($settings['Sub_title'])){
                    printf(
                        '<%1$s class=%2$s>%3$s</%1$s>',
                        Helper::validate_html_tag( $settings['sub_title_tag'], [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ], 'h5' ),
                        'eae-cta-sub-title',
                        $settings['Sub_title'],
                    );
                }
                if(!empty($settings['description'])){
                    ?>
                    <p class="eae-cta-description">
                        <?php echo Helper::eae_wp_kses($settings['description']); ?>
                    </p>
                    <?php
                }
                echo "<div class='eae-cta-button'>"; ?>

                <?php if($settings['pri_btn_title'] != ''){ ?>
                <a <?php echo $this->get_render_attribute_string('pri_btn_link') ?> > <?php ; 
                        echo Helper::eae_wp_kses($settings['pri_btn_title']);
                        Helper::render_icon_html($settings,$this,'pri_btn_icon','eae-cta-pri-icon');
                ?>  </a> <?php } ?>
                <?php if($settings['sec_btn_title'] != ''){ ?>
                <a <?php echo $this->get_render_attribute_string('sec_btn_link') ?> > <?php ; 
                        echo Helper::eae_wp_kses($settings['sec_btn_title']);
                        Helper::render_icon_html($settings,$this,'sec_btn_icon','eae-cta-sec-icon');
                ?>  
                </a>
                <?php
                }
                echo "</div>"; 
            echo "</div>"; 
        echo "</div>";
       
    }
}