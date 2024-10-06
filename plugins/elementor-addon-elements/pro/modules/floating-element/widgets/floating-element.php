<?php

namespace WTS_EAE\Pro\Modules\FloatingElement\Widgets;

use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class FloatingElement extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-floating-element';
	}

	public function get_title() {
		return __( 'Floating Element', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-floating-elements';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'floating element'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie', 'eae-keyframes' ];
	}
    protected function register_controls(){
        $this->start_controls_section(
			'eae-floating-element-content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);
        $repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => esc_html__( 'Title', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Element title', 'wts-eae' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

        $repeater->add_control(
			'content_type',
			[
				'label' => esc_html__( 'Type', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'options' => [
					'none' => [
						'title' => esc_html__( 'None', 'wts-eae' ),
						'icon' => 'eicon-ban',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'wts-eae' ),
						'icon' => 'eicon-star',
					],
                    'lottie-animation' => [
						'title' => esc_html__( 'Lottie Animation', 'wts-eae' ),
						'icon' => 'eicon-lottie',
					],
					
					'image' => [
						'title' => esc_html__( 'Image', 'wts-eae' ),
						'icon' => 'eicon-image-bold',
					],
                    'text' => [
						'title' => esc_html__( 'Text', 'wts-eae' ),
						'icon' => 'eicon-animation-text',
					],
					
				],
                'default' => 'image',
			]
		);

        $repeater->start_controls_tabs('general_tab');

        $repeater->start_controls_tab(
			'item_content_tab',
			[
				'label' => esc_html__( 'Content', 'wts-eae' ),
               
			]
		);


        //   Image



        $repeater->add_control(
            'content_image',
            [
                'label' => esc_html__( 'Choose Image', 'wts-eae' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'  => ''
                ],
                'condition' => [
                    'content_type' =>'image'
                ]
            ]
		);

        $repeater->add_control(
			'content_title_control',
			[
				'label' => esc_html__( 'Title', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => 'None',
                    'title' => 'Title',
					'caption' => 'Caption',
					'custom' => 'Custom',
				],
				'default' => 'none',
                'condition' => [
                    'content_type' =>'image'
                ]
			]
		);
        $repeater->add_control(     
            'content_title_custom',
            [
                'label' => esc_html__( 'Text', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'content_title_control' =>'custom',
                    'content_type' =>'image'
                ],
                
            ]
        );

        $repeater->add_control(
			'content_link_control',
			[
				'label' => esc_html__( 'Link', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => 'None',
                    'media' => 'Media File',
					'custom' => 'Custom URL',
				],
				'default' => 'none',
                'condition' => [
                    'content_type' =>'image'
					
                ]
			]
		);

        $repeater->add_control(
			'content_custom_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
				'condition' => [
					'content_link_control' => 'custom',
					'content_type' =>'image'
				],
				'show_label' => false,
			]
		);
       
        $repeater->add_control(
			'content_lightbox_control',
			[
				'label' => esc_html__( 'Lightbox', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'condition' => [
                    'content_link_control' =>'media'          
                ]
			]
		);

        //  Icon



        $repeater->add_control(
			'content_type_icon',
			[
				'label' => esc_html__( 'Icon', 'wta-eae' ),
				'type' => Controls_Manager::ICONS,				
				'skin' => 'inline',
				'label_block' => false,
                'condition' => [
                    'content_type' =>'icon'
                ],
                'default' => [
                    'value' => 'fas fa-bolt',
                    'library' => 'fa-solid',
                ],
			]
		);

        $repeater->add_control(
			'content_custom_link_icon',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
                'condition' => [
                    'content_type' =>'icon'
                ],
				
				
			]
		);

        //  Text

        $repeater->add_control(
			'content_type_text',
			[
				'label'			 => esc_html__( 'Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
                'condition' => [
                    'content_type' =>'text'
                ],
                'default'=>'Text',
			]
		);
        $repeater->add_control(
			'content_custom_link_text',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
                'condition' => [
                    'content_type' =>'text'
                ],
				
				
			]
		);

        // lottie 

        $repeater->add_control(
			'content_type_lottie_option',
			[
				'label' => esc_html__( 'Source', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media_file',
				'options' => [
					'media_file' => esc_html__( 'Media File', 'wts-eae' ),
					'external_url' => esc_html__( 'External URL', 'wts-eae' ),
				],
                'condition' => [
                    'content_type' =>'lottie-animation'
                ]
			]
		);

       
        $repeater->add_control(
			'content_type_lottie_animation_json',
			[
				'label'       => __( 'Animation JSON URL', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
                'condition' => [
                    'content_type' =>'lottie-animation',
                    'content_type_lottie_option' =>'external_url',
                ]
			]
		);

        $repeater->add_control(
			'content_type_lottie_upload_json_file',
			[
				'label' => esc_html__( 'Upload JSON File', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'frontend_available' => true,
                'condition' => [
                    'content_type' =>'lottie-animation',
                    'content_type_lottie_option' =>'media_file',
                ]
				
			]
		);

        $repeater->add_control(
			'content_custom_link_lottie-animation',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
                'condition' => [
                    'content_type' =>'lottie-animation'
                ],
				
				
			]
		);




        $repeater->add_control(
			'content_type_lottie_loop',
			[
				'label' => esc_html__( 'Loop', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'condition' => [
                    'content_type' =>'lottie-animation',                    
                ]
			]
		);

        $repeater->add_control(
			'content_type_lottie_reverse',
			[
				'label' => esc_html__( 'Reverse', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'condition' => [
                    'content_type' =>'lottie-animation',
                ]
			]
		);


        //  common 

        $repeater->add_control(
			'content_type_icon_view',
			[
				'label' => esc_html__( 'View', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'wts-eae' ),
					'stacked' => esc_html__( 'Stacked', 'wts-eae' ),
					'framed' => esc_html__( 'Framed', 'wts-eae' ),
				],
				'default' => 'default',
                'condition' => [
                    'content_type'=>['icon','lottie-animation','text'],
				],	
			]
		);

		$repeater->add_control(
			'content_type_icon_shape',
			[
				'label' => esc_html__( 'Shape', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => esc_html__( 'Circle', 'wts-eae' ),
					'square' => esc_html__( 'Square', 'wts-eae' ),
				],
				'default' => 'circle',
				'condition' => [
					'content_type_icon_view!' => 'default',
                    // 'content_type'=>'icon',
                    // 'content_type'=>'lottie-animation',
                    'content_type'=>['icon','lottie-animation','text'],
                    
				],			
			]
		);


		$repeater->add_responsive_control(
            'content_style_image_vertical_position',
            [
                'label' => esc_html__( 'Vertical Position', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['%','px'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
						'step'=>1,
                    ],
					'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
				'default' => [
					'unit' => '%',
					'size' => 20,
				],
                'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.wts-eae-image' => 'top: {{SIZE}}{{UNIT}};',
				]
            ]
        );

        $repeater->add_responsive_control(
            'content_style_image_Horizontal_position',
            [
                'label' => esc_html__( 'Horizontal Position', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['%','px'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
						'step'=>1,
                    ],
					'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
				'default' => [
					'unit' => '%',
					'size' => 30,
				],
                'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.wts-eae-image' => 'left: {{SIZE}}{{UNIT}};',
				]
            ]
        );

      
		$repeater->add_responsive_control(
            'content_style_size_Horizontal_position',
            [
                'label' => esc_html__( 'Size', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  {{CURRENT_ITEM}}.wts-eae-image.image img' => 'width: {{SIZE}}{{UNIT}}; height:auto; min-width : {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}  {{CURRENT_ITEM}}.wts-eae-image.text' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}  {{CURRENT_ITEM}}.wts-eae-image.lottie-animation .eae-lottie-animation ' => 'width: {{SIZE}}{{UNIT}}; height : auto; min-width :  {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}  {{CURRENT_ITEM}}.wts-eae-image.icon' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}  {{CURRENT_ITEM}}.wts-eae-image.icon svg' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ],
                
            ]
        );

        $repeater->end_controls_tab();
        $repeater->start_controls_tab(
			'item_content_tab_effect',
			[
				'label' => esc_html__( 'Effects', 'wts-eae' ),
               
			]
		);

		$repeater->add_control(
			'popover-toggle-translate',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Translate', 'wts-eae' ),
				'label_off' => esc_html__( 'Default', 'wts-eae' ),
				'label_on' => esc_html__( 'Custom', 'wts-eae' ),
				'return_value' => 'yes',
				'default' => 'yes'
			]
		);
		
		$repeater->start_popover();

		$repeater->add_control(
			'eae_floating_translate_x',
			[
				'label' => __( 'Translate X', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 5,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'eae_floating_translate_y',
			[
				'label' => __( 'Translate Y', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 5,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',				
				'frontend_available' => true,
			]
		);
		$repeater->end_popover();

		$repeater->add_control(
			'popover-toggle-rotate',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Rotate', 'wts-eae' ),
				'label_off' => esc_html__( 'Default', 'wts-eae' ),
				'label_on' => esc_html__( 'Custom', 'wts-eae' ),
				'return_value' => 'yes',
				'default' => 'yes'
			]
		);

		$repeater->start_popover();

		$repeater->add_control(
			'eae_rotate_x',
			[
				'label' => __( 'Rotate X', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 0,
					],
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'eae_rotate_y',
			[
				'label' => __( 'Rotate Y', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 0,
					],
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'eae_rotate_z',
			[
				'label' => __( 'Rotate Z', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 0,
					],
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$repeater->end_popover();

		$repeater->add_control(
			'eae_scale_toggle',
			[
				'label' => __( 'Scale', 'wts-eae' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'frontend_available' => true,
				
			]
		);

		$repeater->start_popover();

		$repeater->add_control(
			'eae_scale_x',
			[
				'label' => __( 'Scale X', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 1,
						'to' => 1,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$repeater->add_control(
			'eae_scale_y',
			[
				'label' => __( 'Scale Y', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 1,
						'to' => 1,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$repeater->end_popover();
		
		$repeater->add_control(
			'eae-floating_duration',
			[
				'label' => __( 'Duration', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Duration of the animation in milliseconds', 'wts-eae' ),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1000,
						'max' => 20000,
						'step' => 100
					]
				],
				'default' => [
					'size' => 1000,
				],				
			]
		);

		$repeater->add_control(
			'eae-floating_delay',
			[
				'label' => __( 'Delay', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Delay of the animation in milliseconds', 'wts-eae' ),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 100
					]
				],
			]
		);

		//add select control animation direction
		$repeater->add_control(
			'floating_animation_direction',
			[
				'label' => __( 'Animation Direction', 'wts-eae' ),
				'type'  => Controls_Manager::SELECT,
				'default' => 'alternate',
				'options' => [
					'normal' => __( 'Normal', 'wts-eae' ),
					'alternate' => __( 'Alternate', 'wts-eae' ),
					'reverse' => __( 'Reverse', 'wts-eae' ),
					'alternate-reverse' => __( 'Alternate Reverse', 'wts-eae' ),
				]
			]
		);



        $repeater->end_controls_tab();        
        $repeater->end_controls_tabs();
        $this->add_control(
            'content_images',
            [
                'label' => esc_html__( 'Floating Items', 'wts-eae' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls() ,
                'default' => [
					[
                        'content_image'=>'',
						'tab_title'=>esc_html__( 'Item #1', 'wts-eae' ),
						'content_style_image_vertical_position' => [
							'unit' => '%',
							'size' => 20,
						],
						'content_style_image_Horizontal_position' => [
							'unit' => '%',
							'size' => 30,
						],

					],
                    [
                        'content_image'=>'',
						'tab_title'=>esc_html__( 'Item #2', 'wts-eae' ),
						'content_style_image_vertical_position' => [
							'unit' => '%',
							'size' => 40,
						],
						'content_style_image_Horizontal_position' => [
							'unit' => '%',
							'size' => 80,
						],
                     
					],
                    [
                        'content_image'=>'',
						'tab_title'=>esc_html__( 'Item #3', 'wts-eae' ),
						'content_style_image_vertical_position' => [
							'unit' => '%',
							'size' => 80,
						],
						'content_style_image_Horizontal_position' => [
							'unit' => '%',
							'size' => 20,
						],
					],
				],
				'title_field' => '{{{ tab_title }}}',

            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
			'eae-floating-element-settings',
			[
				'label' => __( 'Settings', 'wts-eae' ),
			]
		);
       
        $this->add_control(
            'content_floating_settings',
            [
                'label'     => __( 'Floating Settings', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                
            ]
        );

        $this->add_control(
			'content_scale_control',
			[
				'label' => esc_html__( 'Scale', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

        $this->add_control(
            'content_scale_control_number',
            [
                'label'       => __( 'Scale Value', 'wts-eae' ),
                'type'        => Controls_Manager::NUMBER,
                'min'       => 0,
				'max'       => 2,
				'step'      => .1,
				'default'   => 1.1,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-image.image:hover .eae-floating-img' => 'transform: scale({{VALUE}});',
                    
                ],
                'condition' => [
					'content_scale_control' => 'yes',
				],

            ]
        );


        

		$this->add_control(
			'popover-toggle',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Translate', 'wts-eae' ),
				'label_off' => esc_html__( 'Default', 'wts-eae' ),
				'label_on' => esc_html__( 'Custom', 'wts-eae' ),
				'return_value' => 'yes',
			]
		);
		
		$this->start_popover();

		$this->add_control(
			'floating_translate_x',
			[
				'label' => __( 'Translate X', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 5,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'floating_translate_y',
			[
				'label' => __( 'Translate Y', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 5,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',				
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		// $this->add_control(
		// 	'eae-floating_duration',
		// 	[
		// 		'label' => __( 'Duration', 'wts-eae' ),
		// 		'type' => Controls_Manager::SLIDER,
		// 		'size_units' => ['px'],
		// 		'range' => [
		// 			'px' => [
		// 				'min' => 0,
		// 				'max' => 10000,
		// 				'step' => 100
		// 			]
		// 		],
		// 		'default' => [
		// 			'size' => 1000,
		// 		],				
		// 	]
		// );

		// $this->add_control(
		// 	'eae-floating_delay',
		// 	[
		// 		'label' => __( 'Delay', 'wts-eae' ),
		// 		'type' => Controls_Manager::SLIDER,
		// 		'size_units' => ['px'],
		// 		'range' => [
		// 			'px' => [
		// 				'min' => 0,
		// 				'max' => 5000,
		// 				'step' => 100
		// 			]
		// 		],
		// 	]
		// );


		
		$this->end_popover();

		$this->add_control(
			'popover-rotate',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Rotate', 'wts-eae' ),
				'label_off' => esc_html__( 'Default', 'wts-eae' ),
				'label_on' => esc_html__( 'Custom', 'wts-eae' ),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_control(
			'global_rotate_x',
			[
				'label' => __( 'Rotate X', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 0,
					],
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'global_rotate_y',
			[
				'label' => __( 'Rotate Y', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 0,
					],
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'global_rotate_z',
			[
				'label' => __( 'Rotate Z', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 0,
						'to' => 0,
					],
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'min' => -360,
						'max' => 360,
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$this->end_popover();

		$this->add_control(
			'scale_toggle',
			[
				'label' => __( 'Scale', 'wts-eae' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'frontend_available' => true,
				
			]
		);

		$this->start_popover();

		$this->add_control(
			'scale_x',
			[
				'label' => __( 'Scale X', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 1,
						'to' => 1,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'scale_y',
			[
				'label' => __( 'Scale Y', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'sizes' => [
						'from' => 1,
						'to' => 1,
					],
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'labels' => [
					__( 'From', 'wts-eae' ),
					__( 'To', 'wts-eae' ),
				],
				'scales' => 1,
				'handles' => 'range',
				'frontend_available' => true,
			]
		);

		$this->end_popover();
		
		$this->add_control(
			'floating_duration',
			[
				'label' => __( 'Duration', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Duration of the animation in milliseconds', 'wts-eae' ),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1000,
						'max' => 20000,
						'step' => 100
					]
				],
				'default' => [
					'size' => 1000,
				],				
			]
		);

		$this->add_control(
			'floating_delay',
			[
				'label' => __( 'Delay', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Delay of the animation in milliseconds', 'wts-eae' ),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 100
					]
				],
			]
		);

		$this->add_control(
			'global_floating_animation_direction',
			[
				'label' => __( 'Animation Direction', 'wts-eae' ),
				'type'  => Controls_Manager::SELECT,
				'default' => 'alternate',
				'options' => [
					'normal' => __( 'Normal', 'wts-eae' ),
					'alternate' => __( 'Alternate', 'wts-eae' ),
					'reverse' => __( 'Reverse', 'wts-eae' ),
					'alternate-reverse' => __( 'Alternate Reverse', 'wts-eae' ),
				]
			]
		);

        $this->add_control(
            'floating-image-overlay',
            [
                'label'     => __( 'Overlay', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator'=>'before',
                
            ]
        );
       


        $this->add_control(
            'content_style_title_control', 
            [
                'label' => __('Position', 'wts-eae'),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    'inside' => __('Inside', 'wts-eae'),
                    'outside' => __('Outside', 'wts-eae'),
                    
                ],
                'condition'=>[
                   
                    
                ],
                'default' => 'inside'
            ]
        );
        $this->add_control(
			'show_overlay',
			[
				'label'        => __( 'Show Overlay', 'wts-eae' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'hover'         => __( 'On Hover', 'wts-eae' ),
					'always'        => __( 'Always', 'wts-eae' ),
					'never'         => __( 'Never', 'wts-eae' ),
					'hide-on-hover' => __( 'Hide on Hover', 'wts-eae' ),
				],
				'default'      => 'always',
				'render_type'  => 'template',
				'prefix_class' => 'eae-overlay-',
                'condition'=>[
                  
                    'content_style_title_control'=>'inside',
                ],
			]
		);
            
        $this->add_control(
            'content_style_title_inside', 
            [
                'label' => __('Alignment', 'wts-eae'),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    'top' => __('Top', 'wts-eae'),
                    'center' => __('Center', 'wts-eae'),
                    'bottom' => __('Bottom', 'wts-eae'),
                    
                ],
                'condition'=>[
                  
                    'content_style_title_control'=>'inside',
                ],
                'default' => 'center'
            ]
        );

        $this->add_control(
            'content_style_title_outside', 
            [
                'label' => __('Alignment', 'wts-eae'),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    'column-reverse' => __('Top', 'wts-eae'),
                    'column' => __('Bottom', 'wts-eae'),
                    
                ],
                'condition'=>[
                     
                    'content_style_title_control'=>'outside',
                ],
                'default' => 'column-reverse',
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-image.image a' => 'flex-direction:{{SIZE}};',
                    '{{WRAPPER}} .wts-eae-image.image' => 'flex-direction:{{SIZE}};',
                ],

            ]
        );



        $this->end_controls_section();

		$this->start_controls_section(
            'floating-image-style',
            [
                'label' => esc_html__( 'Icon', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
			$this->start_controls_tabs( 'floating-image-icon-style');

			$this->start_controls_tab(
				'floating-image-icon_normal',
				[
					'label' => esc_html__( 'Normal', 'wts-eae' ),
				
				]
			);

			$this->add_control(
				'primary_color',
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon ' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.icon svg' => 'fill: {{VALUE}};',
						
					],
				]
			);

			$this->add_control(
				'secondary_color',
				[
					'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'icon_colors_hover',
				[
					'label' => esc_html__( 'Hover', 'wts-eae' ),
				]
			);

			$this->add_control(
				'hover_primary_color',
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon:hover ' => 'color: {{VALUE}}; border-color: {{VALUE}};',					
						'{{WRAPPER}} .wts-eae-image.icon:hover svg' => 'fill: {{VALUE}};',					
					],
				]
			);

			$this->add_control(
				'hover_secondary_color',
				[
					'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon:hover ' => 'background-color: {{VALUE}};',					
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'size',
				[
					'label' => esc_html__( 'Size', 'wts-eae' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
					'range' => [
						'px' => [
							'min' => 6,
							'max' => 300,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .wts-eae-image.icon svg' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'icon_padding',
				[
					'label' => esc_html__( 'Padding', 'wts-eae' ),
					'type' => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon' => 'padding: {{SIZE}}{{UNIT}};',
					],
					'range' => [
						'em' => [
							'min' => 0,
							'max' => 5,
						],
					],
				
				]
			);

			$this->add_responsive_control(
				'rotate',
				[
					'label' => esc_html__( 'Rotate', 'wts-eae' ),
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
						'{{WRAPPER}} .wts-eae-image.icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
						'{{WRAPPER}} .wts-eae-image.icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
					],
				
					
				]
			);

			$this->add_control(
				'border_width',
				[
					'label' => esc_html__( 'Border Width', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'eae_floating_element_icon_box_shadow',
					'selector' => '{{WRAPPER}} .wts-eae-image.icon',
				
					
				]
			);
		$this->end_controls_section();

		$this->start_controls_section(
            'floating-lottie-style-tag',
            [
                'label' => esc_html__( 'Lottie', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


			$this->add_control(
				'primary_color_lottie',
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-circle' => 'border-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-square' => 'border-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-stacked-circle' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-stacked-square' => 'background-color: {{VALUE}};',
						
					],
				]
			);

			$this->add_control(
				'secondary_color_lottie_Secondary_Color',
				[
					'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-circle' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-square' => 'background-color: {{VALUE}};',
						
					],
				]
			);

			$this->add_control(
				'primary_color_hover_lottie',
				[
					'label' => esc_html__( 'Primary Hover Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-circle:hover' => 'border-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-square:hover' => 'border-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-stacked-circle:hover' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-stacked-square:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'secondary_color_hover_lottie',
				[
					'label' => esc_html__( 'Secondary Hover Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-circle:hover' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.lottie-animation.fa-element-view-framed-square:hover' => 'background-color: {{VALUE}};',
						
					],
					
				]
			);
			$this->add_responsive_control(
				'floating-image-style-lottie-size',
				[
					'label' => esc_html__( 'Size', 'wts-eae' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'rem' ],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.lottie-animation .eae-lottie-animation' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'eae_floating_element_lottie-animation_box_shadow',
					'selector' => '{{WRAPPER}} .wts-eae-image.lottie-animation',
				]
			);

			$this->add_responsive_control(
				'floating-image-style-rp-lottie-animation_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.lottie-animation' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				
				]
			);

			$this->add_responsive_control(
				'floating-image-style-rp-lottie-animation_padding',
				[
					'label' => esc_html__( 'Padding', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.lottie-animation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'floating-image-tab-style',
			[
				'label' => esc_html__( 'Image', 'wts-eae' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			// $this->add_responsive_control(
			// 	'floating-image-style-image_height',
			// 	[
			// 		'label' => esc_html__( 'Height', 'wts-eae' ),
			// 		'type' => Controls_Manager::SLIDER,
			// 		'size_units' => [ 'px', '%', 'em', 'rem' ],
			// 		'range' => [
			// 			'px' => [
			// 				'min' => 0,
			// 				'max' => 1000,
			// 			],
			// 		],
			// 		'selectors' => [
			// 			'{{WRAPPER}}  .wts-eae-image.image .eae-floating-img ' => 'height: {{SIZE}}{{UNIT}};',
			// 		],
			// 	]
			// );
			$this->add_responsive_control(
				'floating-image-style-image_width',
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
					'default' => [
						'unit' => 'px',
						'size' => 150,
					],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image .eae-floating-img ' => 'width: {{SIZE}}{{UNIT}}; height: auto; min-width:{{SIZE}}{{UNIT}};',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'floating-image-style-image_background',
					'types' => [ 'classic', 'gradient' ,'image'],
					'selector' => '{{WRAPPER}} .wts-eae-image.image',
				]
			);



			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'           => 'floating-image-style-image_border',
					'selector' => '{{WRAPPER}} .wts-eae-image.image.eae-content-outside .fe-outside-wrapper, {{WRAPPER}} .wts-eae-image.image.eae-content-inside',
					'name' => 'border_image',
				]
			);



			$this->add_responsive_control(
				'floating-image-style-image_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image.eae-content-outside .fe-outside-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .wts-eae-image.image.eae-content-inside' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'floating-image-style-image_Box_shadow',
					'selector' => '{{WRAPPER}} .wts-eae-image.image.eae-content-outside .fe-outside-wrapper, {{WRAPPER}} .wts-eae-image.image.eae-content-inside'
					
				]
			);

			$this->add_responsive_control(
				'floating-image-style-image_padding',
				[
					'label' => esc_html__( 'Padding', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


		

			$this->add_control(
				'floating-image-style-image-overlay',
				[
					'label'     => __( 'Overlay', 'wts-eae' ),
					'type'      => Controls_Manager::HEADING,
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'floating-image-style-rp-image_overlay_text_typo',
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-inside .overlay_text',
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-outside .overlay_text',
					],
				]
			);
			$this->add_control(
				'floating-image-style-rp-image_overlay_text_color',
				[
					'label' => esc_html__( 'Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default'=>'#fff',
					
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-inside .overlay_text' => 'color:{{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-outside .overlay_text' => 'color:{{VALUE}};',
					],
				]
			);

			$this->add_control(
				'floating-image-style-rp-image_overlay_text_hover_color',
				[
					'label' => esc_html__( 'Hover Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-inside:hover .overlay_text' => 'color:{{VALUE}};',
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-outside:hover .overlay_text' => 'color:{{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'overlay_color_fi',
					'label'     => __( 'Color', 'wts-eae' ),
					'types'     => [ 'none', 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .wts-eae-image.image.eae-content-outside .wts-eae-image-title-outside .overlay_text, {{WRAPPER}} .wts-eae-image.image.eae-content-inside .overlay_text',
					'condition' => [
						'show_overlay!' => 'never',
					],
				]
			);

			$this->add_control(
				'eae_animation_fi',
				[
					'label'     => __( 'Animation', 'wts-eae' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						''                  => __( 'None', 'wts-eae' ),
						'pulse'             => __( 'Pulse', 'wts-eae' ),
						'headShake'         => __( 'Head Shake', 'wts-eae' ),
						'tada'              => __( 'Tada', 'wts-eae' ),
						'fadeIn'            => __( 'Fade In', 'wts-eae' ),
						'fadeInDown'        => __( 'Fade In Down', 'wts-eae' ),
						'fadeInLeft'        => __( 'Fade In Left', 'wts-eae' ),
						'fadeInRight'       => __( 'Fade In Right', 'wts-eae' ),
						'fadeInUp'          => __( 'Fade In Up', 'wts-eae' ),
						'rotateInDownLeft'  => __( 'Rotate In Down Left', 'wts-eae' ),
						'rotateInDownRight' => __( 'Rotate In Down Right', 'wts-eae' ),
						'rotateInUpLeft'    => __( 'Rotate In Up Left', 'wts-eae' ),
						'rotateInUpRight'   => __( 'Rotate In Up Right', 'wts-eae' ),
						'zoomIn'            => __( 'Zoom In', 'wts-eae' ),
						'zoomInDown'        => __( 'Zoom In Down', 'wts-eae' ),
						'zoomInLeft'        => __( 'Zoom In Left', 'wts-eae' ),
						'zoomInRight'       => __( 'Zoom In Right', 'wts-eae' ),
						'zoomInUp'          => __( 'Zoom In Up', 'wts-eae' ),
						'slideInLeft'       => __( 'Slide In Left', 'wts-eae' ),
						'slideInRight'      => __( 'Slide In Right', 'wts-eae' ),
						'slideInUp'         => __( 'Slide In Up', 'wts-eae' ),
						'slideInDown'       => __( 'Slide In Down', 'wts-eae' ),
					],
					'default'   => 'fadeIn',
					'condition' => [
						'show_overlay'           => [ 'hover', 'hide-on-hover' ],
					],
				]
			);

			$this->add_responsive_control(
				'floating-image-style-image_overlay_padding',
				[
					'label' => esc_html__( 'Padding', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-inside .overlay_text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					
				]
			);


			$this->add_responsive_control(
				'floating-image-style-image_overlay_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.image .wts-eae-image-title-inside .overlay_text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
            'floating-Text-style',
            [
                'label' => esc_html__( 'Text', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
			$this->add_control(
				'floating-image-style-Text',
				[
					'label'     => __( 'Text', 'wts-eae' ),
					'type'      => Controls_Manager::HEADING,
					'separator'=>'before',
					
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'floating-image-style-Text_typo',
					'selector' => '{{WRAPPER}} .wts-eae-image.text',
				]
			);

			$this->add_control(
				'floating-image-style-Text_color',
				[
					'label' => esc_html__( 'Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.text' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'floating-image-style-Text_hover_color',
				[
					'label' => esc_html__( 'Hover Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .wts-eae-image.text:hover.text' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'floating-image-style-Text_background_row',
					'types' => [ 'classic', 'gradient' ,'image'],
					'selector' => '{{WRAPPER}} .wts-eae-image.text',
				]
			);

			$this->add_responsive_control(
				'floating-image-style-Text_padding',
				[
					'label' => esc_html__( 'Padding', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}}  .wts-eae-image.text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'floating-image-style-Text_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'wts-eae' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'selectors' => [
						'{{WRAPPER}}  .wts-eae-image.text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'           => 'floating-image-style-Text_border',
					'selector'       => '{{WRAPPER}} .wts-eae-image.text',
				]
			);

        $this->end_controls_section();



    
    }

    public function render(){
        $settings = $this->get_settings_for_display();
        // echo '<pre>';  print_r($settings); echo '</pre>';
		// die('dfaf');
        $this->render_images($settings);
       


    }
    public function get_caption( $item ) {
        
        $caption = '';        
        switch ( $item['content_title_control'] ) {
            case 'caption':
                $caption = wp_get_attachment_caption( $item['content_image']['id'] );
                break;
            case 'title':
                $caption = get_the_title( $item['content_image']['id'] );
                break;
            case 'custom':
                $caption = $item['content_title_custom'];
                break;
        }
        return $caption;
    }



    public function render_images($settings){
        ?>
        <div class='wta-eae-floating-image-wrapper'>          
            <?php
          

            if($settings['content_style_title_control']=='outside'){
                $textContent=' eae-content-outside ';
            }
            else{
                $textContent=' eae-content-inside ';
            }    

			$preClass=[];


			if($settings['content_style_title_control']=='outside'){
				$preClass[]='wts-eae-image-title-outside '.$settings['eae_animation_fi'];
			}
			else{
				$preClass[]='wts-eae-image-title-inside '.$settings['eae_animation_fi'];
			}         
			if($settings['content_style_title_inside']=='top'){
				$preClass[]=' title-top';
			}
			if($settings['content_style_title_inside']=='center'){
				$preClass[]=' title-center';
			}
			if($settings['content_style_title_inside']=='bottom'){
				$preClass[]=' title-bottom';
			}

			$animation_settings = [];

			// echo '<pre>';  print_r($settings['popover-toggle']); echo '</pre>';
		  
			$flag = 0;
            foreach( $settings['content_images'] as $key => $item){

                $Current_class='elementor-repeater-item-'.$item['_id'];
				$this->set_render_attribute('preClasses','class',$preClass);
				
				
                $iconClass='';
                if($item['content_type']=='icon'||$item['content_type']=='lottie-animation'||$item['content_type']=='text'){
                    if($item['content_type_icon_view']=='stacked'){
                        $iconClass=' fa-element-view-stacked-';
                    }
                    else if ($item['content_type_icon_view']=='framed'){
                        $iconClass=' fa-element-view-framed-';
                    }
                    if ($item['content_type_icon_view']!='default'){
                        if($item['content_type_icon_shape']=='square'){
                            $iconClass .= 'square';
                        }
                        else{
                            $iconClass .= 'circle';
                        }
                    }
                    
                }
               
                
            //  light box 

                if( $item['content_lightbox_control']=='yes'){
                    $link = $this->get_link( $item );                                  
            
                    if ( $link ) {                   
                        $this->add_link_attributes( 'link_'.$key, $link );

                        $this->add_render_attribute( 'link_'.$key,[
                            'class' => 'elementor-clickable'.$key,
                        ]);
            
                        if ( $item['content_link_control']!='custom' ) {
                            $this->add_lightbox_data_attributes( "link_".$key, $item['content_image']['id'] );
                        }
                    } 

                }  

					
					
					if(($settings['popover-toggle'] == 'yes' || $settings['popover-rotate'] == 'yes' || $settings['scale_toggle'] == 'yes') && ($item['popover-toggle-translate'] == '' && $item['popover-toggle-rotate'] == '' && $item['eae_scale_toggle'] == '' )){						
						
						if($flag == 0 ){		
											
							if($settings['popover-toggle'] == 'yes'){
								$animation_settings['isTranslate'] = $settings['popover-toggle'];
								$animation_settings['translateX'] = $settings['floating_translate_y']['sizes']; 
								$animation_settings['translateY'] = $settings['floating_translate_y']['sizes']; 
							}					
		
							if($settings['scale_toggle']=='yes'){
								$animation_settings['isScale'] = $settings['scale_toggle'];
								$animation_settings['scaleX'] = $settings['scale_x']['sizes']; 
								$animation_settings['scaleZ'] = $settings['scale_y']['sizes'];
							}
							
							if($settings['popover-rotate']=='yes'){
								$animation_settings['isRotate'] = $settings['popover-rotate'];
								$animation_settings['rotateX'] = $settings['global_rotate_x']['sizes']; 
								$animation_settings['rotateY'] = $settings['global_rotate_y']['sizes']; 
								$animation_settings['rotateZ'] = $settings['global_rotate_z']['sizes']; 
							}
							$animation_settings['Duration'] = $settings['floating_duration']['size']; 
							$animation_settings['Delay'] = $settings['floating_delay']['size']; 
							$animation_settings['animationDirection'] = $settings['global_floating_animation_direction'];
							$flag ++;
						}							
					}
					else{
						$animation_settings = [];
						if($item['popover-toggle-translate'] == 'yes'){
							$animation_settings['isTranslate'] = $item['popover-toggle-translate'];
							$animation_settings['translateX'] = $item['eae_floating_translate_x']['sizes']; 
							$animation_settings['translateY'] = $item['eae_floating_translate_y']['sizes']; 
						}					
	
						if($item['eae_scale_toggle']=='yes'){
							$animation_settings['isScale'] = $item['eae_scale_toggle'];
							$animation_settings['scaleX'] = $item['eae_scale_x']['sizes']; 
							$animation_settings['scaleZ'] = $item['eae_scale_y']['sizes'];
						}
						 
						if($item['popover-toggle-rotate']=='yes'){
							$animation_settings['isRotate'] = $item['popover-toggle-rotate'];
							$animation_settings['rotateX'] = $item['eae_rotate_x']['sizes']; 
							$animation_settings['rotateY'] = $item['eae_rotate_y']['sizes']; 
							$animation_settings['rotateZ'] = $item['eae_rotate_z']['sizes']; 
						}

						$animation_settings['Duration'] = $item['eae-floating_duration']['size']; 
						$animation_settings['Delay'] = $item['eae-floating_delay']['size']; 
						$animation_settings['animationDirection'] = $item['floating_animation_direction'];
				}
				$this->set_render_attribute('wrapper', [    
					'class' => ['wts-eae-image ',$item['content_type'] , $Current_class, $iconClass , $textContent]  ,
					'data-settings' => json_encode($animation_settings),
				]);
				
                    echo "<div {$this->get_render_attribute_string('wrapper')} >";
                        if($item['content_type']=='image'){
                            $imgUrl = $item['content_image']['url'];       
                            if($item['content_link_control']!='none'){
                                ?> <a <?php $this->print_render_attribute_string( "link_".$key ); ?>> <?php
                            }
                            if($item['content_link_control']=='custom'){
								$this->add_link_attributes('link_'.$key,$item['content_custom_link']);
                                ?> <a <?php echo $this->get_render_attribute_string('link_'.$key); ?>> <?php
                            }

							if($settings['content_style_title_control']=='outside'){ echo  "<div class='fe-outside-wrapper'>";	}
							?> <img src=<?php echo esc_url($imgUrl) ?> class='eae-floating-img '/> <?php
							if($settings['content_style_title_control']=='outside'){ echo  "</div>";	}

                            if($item['content_title_control']!='none'){
                                echo  "<div {$this->get_render_attribute_string( 'preClasses' )} >";
                                    echo  "<span class='overlay_text'>";
                                        echo  $this->get_caption( $item ) ;
                                    echo "</span>";
                                echo '</div>';
                                } 
                        ?> </a> <?php
                        };
                        if($item['content_type']=='text'){
                            if($item['content_custom_link_text']['url']!=''){
								$this->add_link_attributes('link_'.$key, $item['content_custom_link_text']); ?>
                                <a <?php echo $this->get_render_attribute_string('link_'.$key); ?> >
                            <?php }
                                echo Helper::eae_wp_kses($item['content_type_text']);
                            if($item['content_custom_link_text']['url']!=''){?>
                                </a>
                            <?php }
                        }
                        if($item['content_type']=='icon'){
                            
                            if($item['content_custom_link_icon']['url']!=''){
								$this->add_link_attributes('link_'.$key, $item['content_custom_link_icon']); ?>
                                <a <?php echo $this->get_render_attribute_string('link_'.$key); ?>>
                            <?php }
                            Icons_Manager::render_icon( $item['content_type_icon']);
                            if($item['content_custom_link_icon']['url']!=''){?>
                                </a>
                            <?php }
                        }
						
                        if($item['content_type']=='lottie-animation'){
                            $icon_class[] = 'eae-lottie-animation';
                            $icon_class[] = 'eae-lottie';
                        
                            $lottie_data = [
                                'loop'    => ( $item['content_type_lottie_loop'] === 'yes' ) ? true : false,
                                'reverse' => ( $item['content_type_lottie_reverse'] === 'yes' ) ? true : false,
                            ];
                            if($item['content_type_lottie_option'] == 'media_file' && !empty($item['content_type_lottie_upload_json_file']['url'])){
                                $lottie_data['url'] = $item['content_type_lottie_upload_json_file']['url'];
                            }else{
                                $lottie_data['url'] = $item['content_type_lottie_animation_json'];
                            }                      
                            $this->set_render_attribute('panel-icon', 'data-lottie-settings', wp_json_encode( $lottie_data ));
                            $this->set_render_attribute('panel-icon', 'class', $icon_class);
                            if($item['content_custom_link_lottie-animation']['url']!=''){ 
									$this->add_link_attributes('panel-link-'.$key ,$item['content_custom_link_lottie-animation']);
								?>
                                <a <?php echo $this->get_render_attribute_string('panel-link-'.$key); ?>>
                            <?php }
                                ?> <div <?php echo $this->get_render_attribute_string('panel-icon');?>></div><?php
                            if($item['content_custom_link_lottie-animation']['url']!=''){?>
                                </a>
                            <?php }
                        }

                    echo '</div>';
            }
            ?>
        </div>
            <?php
    }

    public function get_link( $item ) {
		if ( 'none' === $item['content_link_control'] ) {
			return false;
		}

		if ( 'custom' === $item['content_link_control'] ) {
			return $item['content_custom_link']['url'];
		}

        if ( 'media' === $item['content_link_control'] ) {
            return [
                'url' => $item['content_image']['url'],
            ];
        }
	}


} 
