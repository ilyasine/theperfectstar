<?php 
    namespace WTS_EAE\Pro\Modules\TeamMember\Widgets;

    use Elementor\Controls_Manager;
    use Elementor\Group_Control_Background;
    use WTS_EAE\Base\EAE_Widget_Base;
    use Elementor\Group_Control_Image_Size;
    use Elementor\Repeater;
    use Elementor\Group_Control_Border;
    use Elementor\Group_Control_Box_Shadow;
    use Elementor\Group_Control_Css_Filter;
    use Elementor\Group_Control_Text_Shadow;
    use Elementor\Group_Control_Typography;
    use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
    use Elementor\Utils;
    use Elementor\Icons_Manager;
    use WTS_EAE\Classes\Swiper_helper;
    use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use WTS_EAE\Classes\Helper;

    if(! defined('ABSPATH')){
        exit;
    }

    class TeamMember extends EAE_Widget_Base{
        public function get_name(){
            return 'eae-team-member';
        }

        public function get_title()
        {
            return __('Team Member','wts-eae');
        }

        public function get_categories() {
            return [ 'wts-eae' ];
        }
        
        public function get_keywords() {
            return [ 'team Member'];
        }

        public function get_icon()
        {
            return 'eae-icon eae-team-members';
        }

        protected function register_controls()
        {
            $this->start_controls_section(
                'tm_general',
                [
                    'label' => esc_html__('General','wts-eae'),
                ]
            );

            $this->add_control(
                'tm_layout',
                [
                    'label' => esc_html__('Layout','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'grid' => esc_html__('Grid','wts-eae'),
                        'carousel' => esc_html__('Carousel','wts-eae'),
                    ],
                    'default' =>'grid',
                ]
            );

            $this->add_control(
                'style_preset',
                [
                    'label' => esc_html__('Style Preset','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'default' =>'style-1',
                    'options' => [
                        'style-1' => esc_html__('Style 1', 'wts-eae'),
                        'style-2' => esc_html__('Style 2','wts-eae'),
                        'style-3' => esc_html__('Style 3','wts-eae'), 
                        'style-4' => esc_html__('Style 4','wts-eae'),
                        'style-5' => esc_html__('Style 5','wts-eae'),
                    ],
                    'render_type' => 'template',
                ]
            );

            $repeater = new Repeater();

            $repeater->start_controls_tabs('tm_content_tab');

            $repeater->start_controls_tab(
                'tm_content',
                [
                    'label' => esc_html__('Content','wts-eae'),
                ]
            );

            $repeater->add_control(
                'image',
                [
                    'label' => esc_html__('Select Image','wts-eae'),
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
                    'default' => 'medium_large',
                ] 
            );

            $repeater->add_Control(
                'tm_name',
                [
                    'label' => esc_html__('Name','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'default' => esc_html__('John Doe','wts-eae'),
                    'separator' => 'before',
                    'dynamic' => [
                        'active' => true,
                    ],
                    'placeholder' => esc_html__('Name','wts-eae'),
                ]
            );

            $repeater->add_control(
                'name_tag',
                [
                    'label' => esc_html__('Name HTML Tag','wts-eae'),
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

            $repeater->add_Control(
                'tm_designation',
                [
                    'label' => esc_html__('Designation','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'default' => esc_html__('Designation','wts-eae'),
                    'dynamic' => [
                        'active' => true
                    ],
                    'placeholder' => esc_html__('Designation','wts-eae'),
                ]
            );

            $repeater->add_control(
                'tm_description',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::TEXTAREA,
                    'label_block' => true,
                    'default' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae'),
                    'placeholder' => esc_html('Description','wts-eae'),
                ]
            );

            $repeater->add_control(
                'tm_button',
                [
                    'label' => esc_html__('Button','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $repeater->add_control(
                'tm_button_text',
                [
                    'label' => esc_html__('Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'placeholder' => esc_html__( 'Button Text', 'wts-eae' ),
                ]
            );

            $repeater->add_control(
                'tm_button_link',
                [
                    'label' => esc_html__('Link','wts-eae'),
                    'type' => Controls_Manager::URL,
                ]
            );

            $repeater->end_controls_tab();

            $repeater->start_controls_tab(
                'tm_social_icon_tab',
                [
                    'label' => esc_html__('Social','wts-eae'),
                ]
            );

            $repeater->add_control(
                'social_icon_1',
                [
                    'label' => esc_html__('Social Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fab fa-facebook-f ',
                        'library' => 'fa-solid',
                    ],
                ]
            );

            $repeater->add_control(
                'social_icon_1_link',
                [
                    'label' => esc_html__('Social Icon Link','wts-eae'),
                    'type' => Controls_Manager::URL,
                ]
            );

            $repeater->add_control(
                'social_icon_2',
                [
                    'label' => esc_html__('Social Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fab fa-instagram ',
                        'library' => 'fa-solid',
                    ],
                ]
            );

            $repeater->add_control(
                'social_icon_2_link',
                [
                    'label' => esc_html__('Social Icon Link','wts-eae'),
                    'type' => Controls_Manager::URL,
                ]
            );

            $repeater->add_control(
                'social_icon_3',
                [
                    'label' => esc_html__('Social Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fab fa-twitter ',
                        'library' => 'fa-solid',
                    ],
                ]
            );

            $repeater->add_control(
                'social_icon_3_link',
                [
                    'label' => esc_html__('Social Icon Link','wts-eae'),
                    'type' => Controls_Manager::URL,
                ]
            );

            $repeater->add_control(
                'social_icon_4',
                [
                    'label' => esc_html__('Social Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value' => 'fab fa-linkedin ',
                        'library' => 'fa-solid',
                    ],
                ]
            );

            $repeater->add_control(
                'social_icon_4_link',
                [
                    'label' => esc_html__('Social Icon Link','wts-eae'),
                    'type' => Controls_Manager::URL,
                ]
            );

            $repeater->add_control(
                'social_icon_5',
                [
                    'label' => esc_html__('Social Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                ]
            );

            $repeater->add_control(
                'social_icon_5_link',
                [
                    'label' => esc_html__('Social Icon Link','wts-eae'),
                    'type' => Controls_Manager::URL,
                ]
            );

            $repeater->end_controls_tab();


            $repeater->end_controls_tabs();

            $this->add_control(
                'tm_content',
                [
                    'label' => esc_html__('Team Members','wts-eae'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        [
                            'tm_name' => esc_html__('John Doe', 'wts-eae'),
                            'tm_designation' => esc_html__('CEO','wts-eae'),
                            'tm_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae'),
                        ],
                        [
                            'tm_name' => esc_html__('John Doe', 'wts-eae'),
                            'tm_designation' => esc_html__('Designer','wts-eae'),
                            'tm_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae'),
                        ],
                        [
                            'tm_name' => esc_html__('John Doe', 'wts-eae'),
                            'tm_designation' => esc_html__('Manager','wts-eae'),
                            'tm_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae'),
                        ],
                    ],
                    'title_field'=>'{{{ tm_name }}}',
                ]
            );

            $this->add_control(
				'preview_details_on_hover',
				array(
					'label'        => __( 'Preview Overlay Content', 'wts-eae' ),
					'description'  => __('It is only for editor preview. Its helps you to design your layout properly', 'wts-eae'),
 					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'wts-eae' ),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'default'      => 'no',
					'return_value' => 'yes',
					'condition' => [
						'style_preset!' => 'style-1'
					],
				)
			);

            $this->end_controls_section();

            $this->start_controls_section(
                'tm_setting_section',
                [
                    'label' => esc_html__('Grid','wts-eae'),
                    'condition' => [
                        'tm_layout' => 'grid',
                    ]
                ]
            );

            $this->add_responsive_control(
                'tm_columns_gap',
                [
                    'label' => esc_html__('Column Gap (px)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 1,
                    'selectors' => [
                        '{{WRAPPER}} .eae-team-member-grid-wrapper' => 'column-gap:{{VALUE}}px;',
                    ],
                    'default' => 20,
                    'tablet_default' => 10,
                    'mobile_default' => 10,
                    'condition' => [
                        'tm_layout' => 'grid',
                    ]
                ]
            );

            $this->add_responsive_control(
                'tm_row_gap',
                [
                    'label' => esc_html__('Row Gap (px)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 20,
                    'tablet_default' => 10,
                    'mobile_default' => 10,
                    'selectors' => [
                        '{{WRAPPER}} .eae-team-member-grid-wrapper' => 'row-gap:{{VALUE}}px;',
                    ],
                    'condition' => [
                        'tm_layout' => 'grid',
                    ]
                ]
            );

            $this->add_responsive_control(
                'tm_columns',
                [
                    'label' => esc_html__('Columns','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 3,
                    'tablet_default' => 2,
                    'mobile_default' => 1,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-item-wrapper' => 'width : calc((100% -  ( ({{VALUE}} - 1)  * {{tm_columns_gap.value}}px)) / {{VALUE}} );'
                        //calc( (100% - ((--bu-columns - 1) * --bu-spacing) / --bu-columns)
                    ]
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'tm_settings',
                [
                    'label' => esc_html__('Settings','wts-eae'),
                ]
            );


            $this->add_control(
                'tm_image_heading',
                [
                    'label' => esc_html__('Image','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'image_box_height',
                [
                    'label' => esc_html__('Height (px)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-image-container img' => 'height:{{VALUE}}px;',
                    ],
                ]
            );

            $this->add_responsive_control(
                'image_box_width',
                [
                    'label' => esc_html__('Width (px)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-image-container img' => 'width:{{VALUE}}px;',
                    ],
                    'condition' => [
                        'style_preset' => 'style-1',
                    ]
                ]
            );

            $this->add_responsive_control(
                'image_alignment',
                [
                    'label' => esc_html__('Image Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'start' =>[
                            'title' => esc_html__('Start','wts-eae'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                        'center' =>[
                            'title' => esc_html__('Center','wts-eae'),
                            'icon' => 'eicon-justify-center-h',
                        ],
                        'end' =>[
                            'title' => esc_html__('End','wts_eae'),
                            'icon' => 'eicon-justify-end-h',
                        ]
                    ],
                    'default' => 'center',
                    'toggle' => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-image-container' => 'justify-content:{{VALUE}};', 
                    ], 
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
                        'right' =>[
                            'title' => esc_html__('Right','wts-eae'),
                            'icon' => 'eicon-arrow-right',
                        ]   
                    ],
                    'default' => 'up',
                    'toggle' => false,
                    'selectors_dictionary' => [
                        'left' => 'row',
                        'up' => 'column',
                        'right' => 'row-reverse',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container' => 'flex-direction: {{VALUE}};',
                    ],
                    'condition' => [
                       'style_preset' => 'style-1', 
                    ]
                ]
            );

            $this->add_control(
                'tm_content_heading',
                [
                    'label' => esc_html__('Content','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'content_text_alignment',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' =>Controls_Manager::CHOOSE,
                    'default' => 'center',
                    'options' => [
                        'start' => [
                            'title' => esc_html__('Left','wts-eae'),
                            'icon' => 'eicon-text-align-left',
                        ],
                        'center' => [
                            'title' => esc_html__('Center','wts-eae'),
                            'icon' => 'eicon-text-align-center',
                        ],
                        'end' => [
                            'title' => esc_html__('Right','wts-eae'),
                            'icon' => 'eicon-text-align-right',
                        ],
                    ],
                    'toggle' => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text' => 'align-items: {{VALUE}}; text-align:{{VALUE}};',
                        '{{WRAPPER}} .eae-tm-social-icon' => 'justify-content: {{VALUE}};'
                     ]
                ]
            );

            $this->add_responsive_control(
                'content_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%','em'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 90,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 10,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text' => 'row-gap: {{SIZE}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_control(
                'tm_separator',
                [
                    'label' => esc_html__('Separator','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'style_preset',
                                'operator' => '!==',
                                'value' => 'style-4',
                            ],
                            [
                                'name' => 'style_preset',
                                'operator' => '!==',
                                'value'=>'style-5'
                            ],
                        ]
                    ]
                ]
            );
            $this->add_Control(
                'tm_separator_type',
                [
                    'label' => esc_html__('Separator Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'solid' => esc_html__( 'Solid','wts-eae'),
				        'double' => esc_html__( 'Double','wts-eae'),
				        'dotted' => esc_html__( 'Dotted','wts-eae'),
				        'dashed' => esc_html__( 'Dashed','wts-eae'),
				        'groove' => esc_html__( 'Groove','wts-eae'),
                    ],
                    'default' => 'solid',
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-separator' => 'border-style:{{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'tm_separator_size',
                [
                    'label' => esc_html__('Separator Size','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'range'      => [
                        'px' => [
                            'min' => 1,
                            'max' => 100,
                        ]
                    ], 
                    'default'=> [
                        'size' => '1',
                        'unit' => 'px',
                    ],
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-separator' => 'border-width: {{SIZE}}{{UNIT}} 0 0 0;',
                    ],
                ]
            );

            $this->add_responsive_control(
                'tm_separator_width',
                [
                    'label' => esc_html__('Separator Width','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%' ],
                    'range'=> [
                        'px' => [
                            'min' => 10,
                            'max' => 500,
                        ],
                        '%' => [
                            'min' => 1,
                            'max' => 100,
                        ],
                    ], 
                    'default'        => [
                        'size' => '200',
                        'unit' => 'px',
                    ],
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-separator' => 'width:{{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'tm_social_icon',
                [
                    'label' => esc_html__('Social Icon','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'social_icon_size',
                [
                    'label' => esc_html__('Size (px)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 20,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon' => 'font-size: {{VALUE}}px;'
                    ],
                ]
            );

            $this->add_responsive_control(
                'social_icon_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%','em'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 90,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ], 
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon' => 'column-gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();

            $this->slider_options();

            $this->start_controls_section(
                'container_style',
                [
                    'label' => esc_html__('General','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->start_controls_tabs('tm_container_tab');

            $this->start_controls_tab(
                'tm_container_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'tm-container-shadow',
                    'selector' => '{{WRAPPER}} .eae-tm-container',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'container-background',
                    'selector' => '{{WRAPPER}} .eae-tm-container',
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                        	[
                                'name' => 'style_preset',
                                'operator' => '!==',
                                'value' => 'style-3',
                            ],
                            [
                           	 	'name' => 'style_preset',
                            	'operator' => '!==',
                            	'value' => 'style-4',
                        	],
                            [
                            	'name' => 'style_preset',
                                'operator' => '!==',
                                'value'=>'style-5'
                            ],
                        ]
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'container-border',
                    'selector' => '{{WRAPPER}} .eae-tm-item-wrapper',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tm_container_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'tm-box-hover-shadow',
                    'selector' => '{{WRAPPER}} .eae-tm-container:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'container-background-hover',
                    'selector' => '{{WRAPPER}} .eae-tm-container:hover  ',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'container-hover-border',
                    'selector' => '{{WRAPPER}} .eae-tm-container:hover',
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'container_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'box_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_responsive_control(
                'box_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'image_style',
                [
                    'label' => esc_html__('Image','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'image-css-filters',
                    'selector' => '{{WRAPPER}} .eae-tm-image-container img',
                ]
            );

            $this->add_group_control(
                Group_Control_Css_Filter::get_type(),
                [
                    'name' => 'image-css-filters-hover',
                    'label' => esc_html__('Hover CSS Filter','wts-eae'),
                    'selector' => '{{WRAPPER}} .eae-tm-container:hover .eae-tm-image-container img',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'image-border',
                    'selector' => '{{WRAPPER}} .eae-tm-image-container img',
                    'condition' => [
                        'style_preset!' => 'style-2',
                    ]
                ]
            );

            $this->add_responsive_control(
                'image_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-image-container img'=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;',    
                    ],
                    'condition' => [
                        'style_preset' => 'style-1',
                    ]
                ]
            );

            $this->add_responsive_control(
                'image_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-image-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $this->add_control(
                'overlay_heading',
                [
                    'label' => esc_html__('Overlay','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [
                        'style_preset!' => 'style-1',
                    ],
                    'separator' => 'before',
                    'condition' => [
                        'style_preset!' => ['style-1','style-2'],
                    ]
                ]
            );

            $this->add_responsive_control(
                'half_overlay_style_1_initial_height',
                [
                    'label' => esc_html__('Initial Position (%)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text-overlay-half-up' => 'height:{{VALUE}}%;',
                    ],
                    'default' => 30,
                    'condition' => [
                        'style_preset' => 'style-4',
                    ]
                ]
            );

            $this->add_responsive_control(
                'half_overlay_style_1_hover_height',
                [
                    'label' => esc_html__('Max Transition Position (%)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 90,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container:hover .eae-tm-text-overlay-half-up' => 'height:{{VALUE}}%;',
                        '{{WRAPPER}} .preview-eae-team-member-info-style-4' => 'height:{{VALUE}}%',
                    ],
                    'condition' => [
                        'style_preset' => 'style-4',
                    ]
                ]
            );

            $this->add_responsive_control(
                'half_overlay_style_2_hover_height',
                [
                    'label' => esc_html__('Height (%)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 90,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text-overlay-half-up-style-2' => 'height:{{VALUE}}%;',
                    ], 
                    'condition' => [
                        'style_preset' => 'style-5',
                    ]
                ]
            );

            $this->add_responsive_control(
                'half_overlay_style_2_initial_position',
                [
                    'label' => esc_html__('Initial Position (%)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 95,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text-overlay-half-up-style-2' => 'top: {{VALUE}}%;',
                    ],
                    'condition' => [
                        'style_preset' => 'style-5',
                    ],
                ]
            );

            $this->add_responsive_control(
                'half_overlay_style_2_hover_position',
                [
                    'label' => esc_html__('Max Transition Position (%)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 10,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container:hover .eae-tm-text-overlay-half-up-style-2' => 'top:{{VALUE}}%;',
                        '{{WRAPPER}} .preview-eae-team-member-info-style-5' => 'top:{{VALUE}}%',
                    ],
                    'condition' => [
                        'style_preset' => 'style-5',
                    ]
                ]
            );

            $this->add_control(
                'overlay_hover',
                [
                    'label' => esc_html__('Hover Direction','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'default' => esc_html__('Default','wts-eae'),
                        'down' => esc_html__('Down','wts-eae'),
                        'right' => esc_html__('Right','wts-eae'),
                        'up' => esc_html__('Up','wts-eae'),
                        'left' => esc_html__('Left','wts-eae'),

                    ],
                    'default' => 'default',
                    'condition' => [
                        'style_preset' => 'style-3',
                    ],
                    'render_type' => 'template',
                ]
            );

            $this->add_control(
                'overlay_size',
                [
                    'label' => esc_html__('Size','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'range'      => [
                        'px' => [
                            'min' => 100,
                            'max' => 1000,
                        ],
                        '%'	=>	[
                            'min'	=>	10,
                            'max'	=>	100
                        ],
                    ], 
                    'size_units' => [ 'px', '%' ],
                    'default'        => [
                        'size' => '90',
                        'unit' => '%',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'style_preset',
                                'value' => 'style-3',
                            ],
                            [
                                'name' => 'overlay_hover',
                                'value'=>'default'
                            ],
                        ]    
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text-overlay' => ' height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'overlay-color',
                    'selector' => '{{WRAPPER}} .eae-tm-text-overlay, {{WRAPPER}} .eae-tm-text-up, {{WRAPPER}} .eae-tm-text-down, {{WRAPPER}} .eae-tm-text-right, {{WRAPPER}} .eae-tm-text-left, {{WRAPPER}} .eae-tm-text-half-up, {{WRAPPER}} .eae-tm-text-overlay-half-up, {{WRAPPER}} .eae-tm-text-overlay-half-up-style-2',
                    'condition' => [
                        'style_preset!' => ['style-1','style-2'],
                    ]
                ]
            );

            $this->add_control(
                'overlay_blend_mode',
                [
                    'label' => esc_html__( 'Blend Mode', 'wts-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '' => esc_html__( 'Normal', 'wts-eae' ),
                        'multiply' => esc_html__('Multiply', 'wts-eae'),
                        'screen' => esc_html__('Screen', 'wts-eae'),
                        'overlay' => esc_html__('Overlay', 'wts-eae'),
                        'darken' => esc_html__('Darken', 'wts-eae'),
                        'lighten' => esc_html__('Lighten', 'wts-eae'),
                        'color-dodge' => esc_html__('Color Dodge', 'wts-eae'),
                        'color-burn' => esc_html__('Color Burn', 'wts-eae'),
                        'hue' => esc_html__('Hue', 'wts-eae'),
                        'saturation' => esc_html__('Saturation', 'wts-eae'),
                        'color' => esc_html__('Color', 'wts-eae'),
                        'exclusion' => esc_html__('Exclusion', 'wts-eae'),
                        'luminosity' => esc_html__('Luminosity', 'wts-eae'),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text-overlay, {{WRAPPER}} .eae-tm-text-up, {{WRAPPER}} .eae-tm-text-down, {{WRAPPER}} .eae-tm-text-right, {{WRAPPER}} .eae-tm-text-left, {{WRAPPER}} .eae-tm-text-half-up, {{WRAPPER}} .eae-tm-text-overlay-half-up, {{WRAPPER}} .eae-tm-text-overlay-half-up-style-2' => 'mix-blend-mode: {{VALUE}}',
                    ],
                    'condition' => [
                        'style_preset!' => ['style-1','style-2'],
                    ]
                ]
            );

            $this->add_control(
                'overlay_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'condition' => [
                        'overlay_hover' => 'default',
                        'style_preset' => 'style-3'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'content_style',
                [
                    'label' => esc_html__('Content','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'tm_name_style_heading',
                [
                    'label' => esc_html__('Name','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->start_controls_tabs(
                'tm_name_tab'
                
            );

            $this->start_controls_tab(
                'tm_name_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                ]
            );

            $this->add_control(
                'tm_name_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-name' => 'color:{{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tm_name_hover_tab',
                [
                    'label' => esc_html('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'tm_name_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container:hover .eae-tm-name' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'name-typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                    ],
                    'selector' => '{{WRAPPER}} .eae-tm-name',  
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'title_shadow',
                    'selector' => '{{WRAPPER}} .eae-tm-name',
                ]
            );

            $this->add_control(
                'tm_name_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_control(
                'tm_designation_heading',
                [
                    'label' => esc_html__('Designation','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->start_controls_tabs('tm_designation_tab');

            $this->start_controls_tab(
                'tm_designation_normal_tab',
                [
                    'label' => esc_html('Normal','wts-eae'),
                ]
            );

            $this->add_control(
                'tm_designation_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-designation' => 'color: {{VALUE}};'
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tm_designation_hover_tab',
                [
                    'label' => esc_html('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'tm_designation_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container:hover .eae-tm-designation' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'designation-typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
                    ],
                    'selector' => '{{WRAPPER}} .eae-tm-designation',
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'designation-text-shadow',
                    'selector' => '{{WRAPPER}} .eae-tm-designation',
                ]
            );

            $this->add_control(
                'tm_designation_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-designation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_control(
                'tm_description_heading',
                [
                   'label' => esc_html__('Description','wts-eae'),
                   'type' => Controls_Manager::HEADING,
                   'separator' => 'before',  
                ]
            );

            $this->start_controls_tabs('tm_description_tab');

            $this->start_controls_tab(
                'tm_description_normal_tab',
                [
                    'label' => esc_html('Normal','wts-eae'),
                ]
            );

            $this->add_control(
                'tm_description_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_SECONDARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-description' => 'color: {{VALUE}};'
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tm_description_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'tm_description_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container:hover .eae-tm-description' => 'color:{{VALUE}};'
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'description-typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-tm-description',
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'description-text-shadow',
                    'selector' => '{{WRAPPER}} .eae-tm-description',
                ]
            );

            $this->add_control(
                'tm_description_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_control(
                'tm_separator_heading',
                [
                    'label' => esc_html__('Separator','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'tm_separator' => 'yes',
                    ]
                ]
            );

            $this->start_controls_tabs('tm_separator_tab');

            $this->start_controls_tab(
                'tm_separator_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'tm_separator_color',
                [
                    'label' => esc_html__('color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-separator' => 'border-color:{{VALUE}};',
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tm_separator_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'tm_separator_hover_color',
                [
                    'label' => esc_html__('color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-container:hover .eae-tm-separator' => 'border-color:{{VALUE}};',
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'tm_separator_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-separator' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'tm_separator' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'content-box-heading',
                [
                    'label' => 'Content',
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'content-box-shadow',
                    'selector' => '{{WRAPPER}} .eae-tm-text',
                ]
            );

            $this->add_responsive_control(
                'content_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );


            $this->add_responsive_control(
                'content_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'default' => [
                        'top' => '20',
	                    'right' => '20',
	                    'bottom' => '20',
	                    'left' => '20',
	                    'unit' => 'px',
                    ],
                    'selectors' =>[
                        '{{WRAPPER}} .eae-tm-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',     
                    ],
                    'condition' => [
                        'style_preset!' => 'style-4',
                    ]
                ]
            );

            $this->add_responsive_control(
                'content_padding_half_overlay',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'default' => [
                        'top' => '10',
	                    'right' => '10',
	                    'bottom' => '10',
	                    'left' => '10',
	                    'unit' => 'px',
                    ],
                    'selectors' =>[
                        '{{WRAPPER}} .eae-tm-text-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',     
                    ],
                    'condition' => [
                        'style_preset' => 'style-4',
                    ] 
                ]
            );

            $this->add_responsive_control(
                'content_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'social_icon_style',
                [
                    'label' => esc_html__('Social Icons','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->start_controls_tabs('tm_social_icon_tab');

            $this->start_controls_tab(
                'tm_social_icon_normal_tab',
                [
                    'label' => esc_html('Normal','wts-eae'),
                ]
            );

            $this->add_control(
                'social_icon_color',
                [
                    'label' => esc_html__('Icon Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon-tag' => 'color:{{VALUE}};',
                    ],
                ]
            );
        
            $this->add_control(
                'social_icon_background_color',
                [
                    'label' => esc_html__('Background Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon-tag' => 'background-color: {{VALUE}};',
                    ],
                    
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'social-icon-border',
                    'selector' => '{{WRAPPER}} .eae-tm-social-icon-tag',
                    
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tm_social_icon_hover_tab',
                [
                    'label' => esc_html('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'social_icon_color_hover',
                [
                    'label' => esc_html__('Icon Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon-tag:hover' => 'color:{{VALUE}};',
                    ], 
                ]
            );

            $this->add_control(
                'social_icon_background_hover_color',
                [
                    'label' => esc_html__('Background Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon-tag:hover' => 'background-color: {{VALUE}};',
                    ],
                    
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'social-icon-border-hover',
                    'selector' => '{{WRAPPER}} .eae-tm-social-icon-tag:hover', 
                    
                ]
            );

            $this->add_control(
                'social_icon_hover_animation',
                [
                    'label' => esc_html__('Hover Animation','wts-eae'),
                    'type' => Controls_Manager::HOVER_ANIMATION,
                    
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'tm-social-icon-shadow',
                    'selector' => '{{WRAPPER}} .eae-tm-social-icon-tag',
                ]
            );

            $this->add_responsive_control(
                'social_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                     
                ]
            );

            $this->add_responsive_control(
                'social_icon_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager:: DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'social_icon_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-social-icon-tag' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'tm_button',
                [
                    'label' => esc_html__('Button','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_responsive_control(
                'button_alignment',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' =>Controls_Manager::CHOOSE,
                    'default' => 'center',
                    'options' => [
                        'start' => [
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
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button-container' => 'justify-content: {{VALUE}};',
                    ],
                ]
            );

            $this->start_controls_tabs('button_normal_tab');

            $this->start_controls_tab(
                'button_normal',
                [
                    'label' => esc_html__('Normal', 'wts-eae'),
                ]
            );

            $this->add_control(
                'button_text_color',
                [
                    'label' => esc_html__('Text Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button' => 'color:{{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_backbgound_color',
                [
                    'label' => esc_html__('Background Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_ACCENT,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'button-border',
                    'selector' => '{{WRAPPER}} .eae-tm-button',   
                                      
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'button_hover',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'button_color_hover',
                [
                    'label' => esc_html('Text Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button:hover' => 'color:{{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_background_hover',
                [
                    'label' => esc_html__('Background Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'button-border-hover',
                    'selector' => '{{WRAPPER}} .eae-tm-button:hover',
                    
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'button-typography',
                    'selector' => '{{WRAPPER}} .eae-tm-button',
                    
                ]
            );

            $this->add_responsive_control(
                'button_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'button_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'  
                    ],
                ]
            );

            $this->add_responsive_control(
                'button_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'button_box_heading',
                [
                    'label' => esc_html__('Box','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'button-container-background',
                    'selector' => '{{WRAPPER}} .eae-tm-button-container',
                    
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'button-container-border',
                    'selector' => '{{WRAPPER}} .eae-tm-button-container',
                    
                ]
            );

            $this->add_control(
                'button_container_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_control(
                'button_container_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-tm-button-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $this->add_control(
                'button_container_margin',
                [
                   'label' => esc_html__('Margin','wts-eae'),
                   'type' => Controls_Manager::DIMENSIONS,
                   'size_units' => ['px','%'],
                   'selectors' => [
                        '{{WRAPPER}} .eae-tm-button-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                    ],
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'carousel_style',
                [
                    'label'     => __( 'Carousel', 'wts-eae' ),
                    'tab'       => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'tm_layout' => 'carousel',
                    ]
                ]
            );

            Swiper_helper::carousel_style_section($this);

            $this->end_controls_section();

        }

        function slider_options(){

            $this->start_controls_section(
                'tm_carousel_setting',
                [
                    'label' => esc_html__('Slider Options','wts-eae'),
                    'condition' => [
                        'tm_layout' => 'carousel',
                    ]
                ]
            );

            Swiper_helper::carousel_controls($this);

            $this->end_controls_section();
        }

        public function get_social_icons($item,$index) {
            $settings = $this->get_settings_for_display();
            ?> 
            <?php
            if($settings['social_icon_hover_animation'] !== '')
            {
                $this->add_render_attribute('tm-social-icon-link','class','elementor-animation-'.$settings['social_icon_hover_animation']);
            }    
            if($item['social_icon_1']['value'] !== '')
            {
                $tag = ($item['social_icon_1_link']['url'] !== '') ? 'a' : 'span';
                $this->add_link_attributes('tm-social-icon-link-1-'.$index,$item['social_icon_1_link']);
                ?>  
                <<?php echo $tag .' '. $this->get_render_attribute_string('tm-social-icon-link') . ' ' . $this->get_render_attribute_string('tm-social-icon-link-1-'.$index) ; ?>>
                    <?php
                        Icons_Manager::render_icon( $item['social_icon_1'] );
                    ?>
                </<?php echo $tag; ?>>

            <?php }

            if($item['social_icon_2']['value'] !== '')
            {
                $tag = ($item['social_icon_2_link']['url'] !== '') ? 'a' : 'span';
                $this->add_link_attributes('tm-social-icon-link-2-'.$index,$item['social_icon_2_link']);
                    ?>
                    <<?php echo $tag .' '. $this->get_render_attribute_string('tm-social-icon-link') . ' ' .$this->get_render_attribute_string('tm-social-icon-link-2-'.$index) ; ?>>
                        <?php
                            Icons_Manager::render_icon( $item['social_icon_2'] );
                        ?>
                    </<?php echo $tag; ?>>
            <?php }

            if($item['social_icon_3']['value'] !== '')
            {
                $tag = ($item['social_icon_3_link']['url'] !== '') ? 'a' : 'span';
                $this->add_link_attributes('tm-social-icon-link-3-'.$index,$item['social_icon_3_link']);
                    ?>
                    <<?php echo $tag .' '. $this->get_render_attribute_string('tm-social-icon-link') . ' ' .$this->get_render_attribute_string('tm-social-icon-link-3-'.$index); ?>>
                        <?php
                        Icons_Manager::render_icon( $item['social_icon_3'] );
                        ?>
                    </<?php echo $tag ;?>>
            <?php }

            if($item['social_icon_4']['value'] !== '')
            {
                $tag = ($item['social_icon_4_link']['url'] !== '') ? 'a' : 'span';
                $this->add_link_attributes('tm-social-icon-link-4-'.$index,$item['social_icon_4_link']);
                    ?>
                    <<?php echo $tag .' '. $this->get_render_attribute_string('tm-social-icon-link') . ' ' . $this->get_render_attribute_string('tm-social-icon-link-4-'.$index); ?>>
                        <?php
                            Icons_Manager::render_icon( $item['social_icon_4'] );
                        ?>
                    </<?php echo $tag; ?>>
            <?php }

            if($item['social_icon_5']['value'] !== '')
            {
                $tag = ($item['social_icon_5_link']['url'] !== '') ? 'a' : 'span';
                $this->add_link_attributes('tm-social-icon-link-5-'.$index,$item['social_icon_5_link']);
                    ?>
                    <<?php echo $tag .' '. $this->get_render_attribute_string('tm-social-icon-link') . ' ' . $this->get_render_attribute_string('tm-social-icon-link-5-'.$index);  ?> >
                    <?php
                        Icons_Manager::render_icon( $item['social_icon_5'] );
                    ?></<?php echo $tag; ?>>
            <?php }
        }

        protected function render()
        {   
            $settings = $this->get_settings_for_display();
            $tm_item_wrapper = '';
            $swiper_data = [];
            $this->add_render_attribute('tm-wrapper','class','eae-team-member-wrapper');

            if($settings['tm_layout'] === 'carousel'){
                $slider_id = rand(99,9999);
                $this->add_render_attribute('tm-container','class','eae-tm-swiper-container'); 
                $this->add_render_attribute('tm-container','class','eae-swiper-outer-wrapper');
                if ( $settings['arrows_layout'] === 'inside' ) {
                    $this->add_render_attribute('tm-container','class','eae-hpos-' . $settings['arrow_horizontal_position']);
                    $this->add_render_attribute('tm-container','class','eae-vpos-' . $settings['arrow_vertical_position']);
                }
                $this->add_render_attribute('tm-container','class','eae-swiper swiper');
                $this->add_render_attribute('tm-wrapper','class','eae-swiper-wrapper eae-post-widget-wrapper');
                $this->add_render_attribute('tm-wrapper','class','swiper-wrapper');
                $swiper_data = Swiper_helper::get_swiper_data($settings);
				$this->add_render_attribute('tm-container','data-swiper-settings', wp_json_encode( $swiper_data ) );
                $tm_item_wrapper = 'eae-swiper-slide swiper-slide';
                if($settings['arrows_layout'] == 'outside'){
                    $this->add_render_attribute('tm-container','class','eae-slider-id-'. $slider_id);
                }
            }else{
                $this->add_render_attribute('tm-wrapper','class','eae-team-member-grid-wrapper');
            }

            $this->add_render_attribute('tm-text','class','eae-tm-text');
            $this->add_render_attribute('tm-text-container','class','eae-tm-text-container');
            if($settings['style_preset'] === 'style-2'){
                $this->add_render_attribute('tm-image','class','eae-tm-image-effect');
                $this->add_render_attribute('tm-text','class','eae-tm-text-padding');
            }
            elseif($settings['style_preset'] === 'style-3'){
                $this->add_render_attribute('tm-text','class','eae-tm-text-padding');
                if($settings['overlay_hover'] === 'default'){
                    $this->add_render_attribute('tm-image','class','eae-tm-img-overlay');
                    $this->add_render_attribute('tm-text-container','class','eae-tm-text-overlay');
                    // $this->add_render_attribute('tm-');
                }
                elseif ($settings['overlay_hover'] === 'down'){
                    $this->add_render_attribute('tm-text-container','class','eae-tm-text-up');
                }
                elseif ($settings['overlay_hover'] === 'right'){
                    $this->add_render_attribute('tm-text-container','class','eae-tm-text-right');
                }
                elseif ($settings['overlay_hover'] === 'left'){
                    $this->add_render_attribute('tm-text-container','class','eae-tm-text-left');
                }
                elseif ($settings['overlay_hover'] === 'up'){
                    $this->add_render_attribute('tm-text-container','class','eae-tm-text-down');
                }
            }
            elseif($settings['style_preset'] === 'style-4'){
                $this->add_render_attribute('tm-image','class','eae-tm-img-blur');
                $this->add_render_attribute('tm-text-container','class','eae-tm-text-overlay-half-up');
                $this->add_render_attribute('tm-description','class','eae-tm-description-overlay-half-up');
                $this->add_render_attribute('tm-social-icon','class','eae-tm-social-overlay-half-up');
                $this->add_render_attribute('tm-button-container','class','eae-tm-button-overlay');
            }
            else if($settings['style_preset'] === 'style-5'){
                $this->add_render_attribute('tm-image','class','eae-tm-img-blur');
                $this->add_render_attribute('tm-text-container','class','eae-tm-text-overlay-half-up-style-2');
            }

            $this->add_render_attribute('eae-tm-container','class','eae-tm-container');
             if($settings['preview_details_on_hover'] == 'yes'){
            //     #elementor is edit mode
                if($settings['style_preset'] == 'style-2'){
                    if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                        $this->add_render_attribute( 'eae-tm-container', 'class', 'preview-eae-team-member-info-style-2');
                    }
                }else if($settings['style_preset'] == 'style-3'){
                    if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                        $this->add_render_attribute( 'tm-text-container', 'class', 'preview-eae-team-member-info-' . $settings['overlay_hover']);
                    }
                }else if($settings['style_preset'] == 'style-4'){
                    if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                        $this->add_render_attribute( 'tm-text-container', 'class', 'preview-eae-team-member-info-style-4');
                    }
                }else if($settings['style_preset'] == 'style-5'){
                    if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                        $this->add_render_attribute( 'tm-text-container', 'class', 'preview-eae-team-member-info-style-5');
                    }
                }
            }

            $this->add_render_attribute('tm-image','class','eae-tm-image-container');

            $this->add_render_attribute('tm-button-container','class','eae-tm-button-container');
            $this->add_render_attribute('tm-name','class','eae-tm-name');
            $this->add_render_attribute('tm-description','class','eae-tm-description');
            $this->add_render_attribute('tm-social-icon-overlay','class','eae-tm-social-icon');    
            $this->add_render_attribute('tm-social-icon-overlay','class','eae-tm-social-icon-overlay');
            $this->add_render_attribute('tm-social-icon','class','eae-tm-social-icon');
            $this->add_render_attribute('tm-social-icon-link','class','eae-tm-social-icon-tag'); 
            
            if($settings['tm_layout'] === 'carousel'){
            
            ?>
			<div <?php echo $this->get_render_attribute_string('tm-container'); ?> > <!--dir="<?php //echo $settings['tm_slider_direction'] ?>" -->
                <div class="eae-swiper-container eae-team-member-swiper-container eae-slider-id-<?php echo $slider_id?>" data-eae-slider-id="<?php echo $slider_id; ?>">
            <?php 
        } ?>
                    <div <?php echo $this->get_render_attribute_string('tm-wrapper'); ?> >
                        <?php
                            foreach ( $settings['tm_content'] as $index => $item ):
                                $this->add_render_attribute('tm-button-link-'. $index,'class','eae-tm-button');
                                $this->set_render_attribute('tm-item-wrapper','class',['eae-tm-item-wrapper', 'elementor-repeater-item-'.$item['_id'] , $tm_item_wrapper]);
                                ?>
                                    <div <?php echo $this->get_render_attribute_string('tm-item-wrapper');  ?>  >
                                        <div <?php echo $this->get_render_attribute_string('eae-tm-container'); ?>>
                                        
                                            <?php if($item['image']['url'] !== '' ) { 
                                                    
                                                ?>
                                                <div <?php echo $this->get_render_attribute_string('tm-image'); ?>>
                                                    <?php echo Group_Control_Image_Size::get_attachment_image_html( $item, 'image' ); 
                                                    
                                                    if($item['social_icon_1']['value'] !== '' || $item['social_icon_2']['value'] !== '' || $item['social_icon_3']['value'] !== '' || $item['social_icon_4']['value'] !== '' || $item['social_icon_5']['value'] !== ''){
                                                        if($settings['style_preset'] === 'style-2') { ?>  
                                                        <div <?php echo $this->get_render_attribute_string('tm-social-icon-overlay'); ?> >
                                                            <?php
                                                                $this->get_social_icons($item,$index);
                                                            ?>
                                                        </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <div <?php echo $this->get_render_attribute_string('tm-text-container'); ?>>
                                                <div <?php echo $this->get_render_attribute_string('tm-text') ?>>
                                                    <?php if($item['tm_name'] !== '')  { 
                                                        $tm_name = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $item['name_tag'] ), $this->get_render_attribute_string( 'tm-name' ), $item['tm_name']); 
                                                        echo $tm_name;
                                                    } 
                                                    if($item['tm_designation'] !== '') { ?>
                                                        <h4 class="eae-tm-designation" ><?php echo Helper::eae_wp_kses($item['tm_designation']); ?></h4>
                                                    <?php }
                                                    
                                                    if($settings['tm_separator'] == 'yes') { ?>
                                                        <span class="eae-tm-separator"></span>
                                                    <?php }

                                                    if($item['tm_description']) { ?>
                                                        <span <?php echo $this->get_render_attribute_string('tm-description') ?> class="eae-tm-description" ><?php echo Helper::eae_wp_kses($item['tm_description']); ?></span>
                                                    <?php } 

                                                    if($item['social_icon_1']['value'] !== '' || $item['social_icon_2']['value'] !== '' || $item['social_icon_3']['value'] !== '' || $item['social_icon_4']['value'] !== '' || $item['social_icon_5']['value'] !== ''){    
                                                        if($settings['style_preset'] !== 'style-2'){ ?>
                                                            <div <?php echo $this->get_render_attribute_string('tm-social-icon'); ?>>
                                                                <?php 
                                                                    $this->get_social_icons($item ,$index);
                                                                ?>
                                                            </div>
                                                            <?php
                                                        } 
                                                    }?>
                                                </div>
                                                <?php  if($item['tm_button_text'] !== '') {
                                                        $this->add_link_attributes('tm-button-link-'. $index,$item['tm_button_link']); ?>
                                                        <div <?php echo $this->get_render_attribute_string('tm-button-container') ;?> >
                                                            <a <?php echo $this->get_render_attribute_string('tm-button-link-'. $index); ?> ><?php echo Helper::eae_wp_kses($item['tm_button_text']); ?></a>
                                                        </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            endforeach;
                        ?>
                    </div>
                    <?php if($settings['tm_layout'] === 'carousel'){  
                        
                        Swiper_helper::get_swiper_pagination($settings);

                        /** Arrows Inside **/
                        if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
                            Swiper_helper::get_swiper_arrows($settings);
                        }

				        Swiper_helper::get_swiper_scrolbar($settings);
                        ?>

                        <!-- <div class="swiper-pagination"></div> -->
                    <?php } 
                    if($settings['tm_layout'] === 'carousel'){        
                    ?>
                </div> 
                <?php 
			if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
				/** Arrows Outside **/
				Swiper_Helper::get_swiper_arrows($settings);
			}
			?> 
            </div>
            <?php
            }
        }

        protected function content_template(){
            ?>
                <#

                    function get_social_icons(item, index){
                        if(settings.social_icon_hover_animation !== ''){
                            view.addRenderAttribute('tm-social-icon-link','class','elementor-animation-' + settings.social_icon_hover_animation);
                        }
                        if(item.social_icon_1.value != ''){
                            let tag = '';
                            tag = item.social_icon_1_link.url !== '' ? 'a' : 'span';
                            if(item.social_icon_1_link.url != ''){
                                view.addRenderAttribute('tm-social-icon-link-1-' + index,'href',_.escape(item.social_icon_1_link.url));
                            }
                            let icon_container_html = '';
                            let icon_html = elementor.helpers.renderIcon(view, item.social_icon_1 , { 'aria-hidden': true }, 'i' , 'object' );
                            icon_container_html = '<'+ tag + ' ' + view.getRenderAttributeString('tm-social-icon-link') + ' ' + view.getRenderAttributeString('tm-social-icon-link-1-' + index) + '>' + icon_html.value + '</' + tag + '>';
                            print(icon_container_html);
                        }
                        if(item.social_icon_2.value != ''){
                            let tag = '';
                            tag = item.social_icon_2_link.url !== '' ? 'a' : 'span';
                            if(item.social_icon_2_link.url != ''){
                                view.addRenderAttribute('tm-social-icon-link-2-' + index,'href',_.escape(item.social_icon_2_link.url));
                            }
                            let icon_container_html = '';
                            let icon_html = elementor.helpers.renderIcon(view, item.social_icon_2 , { 'aria-hidden': true }, 'i' , 'object' );
                            icon_container_html = '<'+ tag + ' ' + view.getRenderAttributeString('tm-social-icon-link') + ' ' + view.getRenderAttributeString('tm-social-icon-link-2-' + index) + '>' + icon_html.value + '</' + tag + '>';
                            print(icon_container_html);
                        }
                        if(item.social_icon_3.value != ''){
                            let tag = '';
                            tag = item.social_icon_3_link.url !== '' ? 'a' : 'span';
                            if(item.social_icon_3_link.url != ''){
                                view.addRenderAttribute('tm-social-icon-link-3-' + index,'href',_.escape(item.social_icon_3_link.url));
                            }
                            let icon_container_html = '';
                            let icon_html = elementor.helpers.renderIcon(view, item.social_icon_3 , { 'aria-hidden': true }, 'i' , 'object' );
                            icon_container_html = '<'+ tag + ' ' + view.getRenderAttributeString('tm-social-icon-link') + ' ' + view.getRenderAttributeString('tm-social-icon-link-3-' + index) + '>' + icon_html.value + '</' + tag + '>';
                            print(icon_container_html);
                        }
                        if(item.social_icon_4.value != ''){
                            let tag = '';
                            tag = item.social_icon_4_link.url !== '' ? 'a' : 'span';
                            if(item.social_icon_4_link.url != ''){
                                view.addRenderAttribute('tm-social-icon-link-4-' + index,'href',_.escape(item.social_icon_4_link.url));
                            }
                            let icon_container_html = '';
                            let icon_html = elementor.helpers.renderIcon(view, item.social_icon_4 , { 'aria-hidden': true }, 'i' , 'object' );
                            icon_container_html = '<'+ tag + ' ' + view.getRenderAttributeString('tm-social-icon-link') + ' ' + view.getRenderAttributeString('tm-social-icon-link-4-' + index) + '>' + icon_html.value + '</' + tag + '>';
                            print(icon_container_html);
                        }
                        if(item.social_icon_5.value != ''){
                            let tag = '';
                            tag = item.social_icon_5_link.url !== '' ? 'a' : 'span';
                            if(item.social_icon_5_link.url != ''){
                                view.addRenderAttribute('tm-social-icon-link-5-' + index,'href',_.escape(item.social_icon_5_link.url));
                            }
                            let icon_container_html = '';
                            let icon_html = elementor.helpers.renderIcon(view, item.social_icon_5 , { 'aria-hidden': true }, 'i' , 'object' );
                            icon_container_html = '<'+ tag + ' ' + view.getRenderAttributeString('tm-social-icon-link') + ' ' + view.getRenderAttributeString('tm-social-icon-link-5-' + index) + '>' + icon_html.value + '</' + tag + '>';
                            print(icon_container_html);
                        }
                    }

                    let tm_item_wrapper = '';
                    let swiper_data = [];

                    view.addRenderAttribute('tm-wrapper','class','eae-team-member-wrapper');

                    if(settings.tm_layout == 'carousel'){
                        view.addRenderAttribute('tm-container','class','eae-tm-swiper-container');
                        view.addRenderAttribute('tm-container','class','eae-swiper-outer-wrapper');
                        
                        if(settings.arrows_layout == 'inside'){
                            view.addRenderAttribute('tm-container','class','eae-hpos-' + settings.arrow_horizontal_position);
                            view.addRenderAttribute('tm-container','class','eae-vpos-' + settings.arrow_vertical_position);
                        }
                        view.addRenderAttribute('tm-container','class','eae-swiper swiper');
                        view.addRenderAttribute('tm-wrapper','class','eae-swiper-wrapper eae-post-widget-wrapper');
                        view.addRenderAttribute('tm-wrapper','class','swiper-wrapper');
                        
                        let swiper_data = window.prepareSwiperData(settings);
                        view.addRenderAttribute('tm-container','data-swiper-settings', JSON.stringify(swiper_data) );
                        tm_item_wrapper = 'eae-swiper-slide swiper-slide';
                    } else {
                        view.addRenderAttribute('tm-wrapper','class','eae-team-member-grid-wrapper');
                    }

                    view.addRenderAttribute('tm-text','class','eae-tm-text');
                    view.addRenderAttribute('tm-text-container','class','eae-tm-text-container');
                    
                    if(settings.style_preset == 'style-2'){
                        view.addRenderAttribute('tm-image','class','eae-tm-image-effect');
                        view.addRenderAttribute('tm-text','class','eae-tm-text-padding');
                    } else if(settings.style_preset == 'style-3'){
                        view.addRenderAttribute('tm-text','class','eae-tm-text-padding');
                        
                        if(settings.overlay_hover == 'default'){
                            view.addRenderAttribute('tm-image','class','eae-tm-img-overlay');
                            view.addRenderAttribute('tm-text-container','class','eae-tm-text-overlay');
                        } else if(settings.overlay_hover == 'down'){
                            view.addRenderAttribute('tm-text-container','class','eae-tm-text-up');
                        } else if(settings.overlay_hover == 'right'){
                            view.addRenderAttribute('tm-text-container','class','eae-tm-text-right');
                        } else if(settings.overlay_hover == 'up'){
                            view.addRenderAttribute('tm-text-container','class','eae-tm-text-down');
                        } else if(settings.overlay_hover == 'left'){
                            view.addRenderAttribute('tm-text-container','class','eae-tm-text-left');
                        }

                    } else if(settings.style_preset == 'style-4'){
                        view.addRenderAttribute('tm-image','class','eae-tm-img-blur');
                        view.addRenderAttribute('tm-text-container','class','eae-tm-text-overlay-half-up');
                        view.addRenderAttribute('tm-description','class','eae-tm-description-overlay-half-up');
                        view.addRenderAttribute('tm-social-icon','class','eae-tm-social-overlay-half-up');
                        view.addRenderAttribute('tm-button-container','class','eae-tm-button-overlay');
                    } else if(settings.style_preset == 'style-5'){
                        view.addRenderAttribute('tm-image','class','eae-tm-img-blur');
                        view.addRenderAttribute('tm-text-container','class','eae-tm-text-overlay-half-up-style-2');
                    }

                    view.addRenderAttribute('eae-tm-container','class','eae-tm-container');

                    if(settings.preview_details_on_hover == 'yes'){
                        if(settings.style_preset == 'style-2'){
                            view.addRenderAttribute('eae-tm-container','class','preview-eae-team-member-info-style-2');
                        }else if(settings.style_preset == 'style-3'){
                            view.addRenderAttribute('tm-text-container','class','preview-eae-team-member-info-' + settings.overlay_hover);
                        }else if(settings.style_preset == 'style-4'){
                            view.addRenderAttribute('tm-text-container','class','preview-eae-team-member-info-style-4');
                        }else if(settings.style_preset == 'style-5'){
                            view.addRenderAttribute('tm-text-container','class','preview-eae-team-member-info-style-5');
                        }
                    }

                    view.addRenderAttribute('tm-image','class','eae-tm-image-container');

                    view.addRenderAttribute('tm-button-container','class','eae-tm-button-container');
                    view.addRenderAttribute('tm-name','class','eae-tm-name');
                    view.addRenderAttribute('tm-description','class','eae-tm-description');
                    view.addRenderAttribute('tm-social-icon-overlay','class','eae-tm-social-icon');
                    view.addRenderAttribute('tm-social-icon-overlay','class','eae-tm-social-icon-overlay');
                    view.addRenderAttribute('tm-social-icon','class','eae-tm-social-icon');
                    view.addRenderAttribute('tm-social-icon-link','class','eae-tm-social-icon-tag');
                    
                    if(settings.tm_layout === 'carousel'){
                        let slider_id = Math.floor(Math.random() * 99,9999);    
                        #>
                            <div {{{ view.getRenderAttributeString( 'tm-container' ) }}} >
                                <div class="eae-swiper-container eae-team-member-swiper-container eae-slider-id-{{{slider_id}}}" data-eae-slider-id= "{{{ slider_id }}}" >
                                      
                        <#
                    } 
                        #>
                                    <div {{{ view.getRenderAttributeString( 'tm-wrapper' ) }}}>
                                        <#
                                            _.each(settings.tm_content, function(item,index){
                                                view.addRenderAttribute('tm-button-link-' + index,'class','eae-tm-button');
                                                view.addRenderAttribute('tm-item-wrapper','class',['eae-tm-item-wrapper', 'elementor-repeater-item-' + item._id , tm_item_wrapper]);
                                                let titleTag = window.eae.validateHTMLTag(item.name_tag, null, 'h2');
                                                #>
                                                    <div {{{ view.getRenderAttributeString( 'tm-item-wrapper' ) }}}>
                                                        <div {{{ view.getRenderAttributeString( 'eae-tm-container' ) }}} >
                                                            <#
                                                                if(item.image.url != ''){
                                                                    #>
                                                                        <div {{{ view.getRenderAttributeString( 'tm-image' ) }}}>
                                                                            <#
                                                                                let image = {
                                                                                    id: item.image.id,
                                                                                    url: item.image.url,
                                                                                    size: item.image_size,
                                                                                    dimension: item.image_custom_dimension,
                                                                                    model: view.getEditModel()
                                                                                };
                                                                                let image_url = elementor.imagesManager.getImageUrl(image);
                                                                                let image_html = '<img src="' + _.escape( image_url ) + '"  />'; 
                                                                            #>
                                                                            {{{ image_html }}}
                                                                            <#
                                                                                if(item.social_icon_1.value !== '' || item.social_icon_2.value !== '' || item.social_icon_3.value !== '' || item.social_icon_4.value !== '' || item.social_icon_5.value !== ''){
                                                                                    if(settings.style_preset == 'style-2'){
                                                                                        #>
                                                                                            <div {{{ view.getRenderAttributeString( 'tm-social-icon-overlay' ) }}}>
                                                                                                <#
                                                                                                    get_social_icons(item, index);
                                                                                                #>
                                                                                            </div>
                                                                                        <#
                                                                                    }
                                                                                }
                                                                            #>
                                                                        </div>
                                                                    <#
                                                                }
                                                            #>
                                                            <div {{{ view.getRenderAttributeString( 'tm-text-container' ) }}}>
                                                                <div {{{ view.getRenderAttributeString( 'tm-text' ) }}} >
                                                                    <#
                                                                        if(item.tm_name !== ''){
                                                                            #>
                                                                            <{{{titleTag}}} {{{view.getRenderAttributeString( 'tm-name' )}}}>{{item.tm_name}}   </{{{titleTag}}}>
                                                                            <#
                                                                        }
                                                                        if(item.tm_designation !== ''){
                                                                            #>
                                                                                <h4 class="eae-tm-designation" >{{{ item.tm_designation }}}</h4>
                                                                            <#
                                                                        }
                                                                        if(settings.tm_separator == 'yes') { 
                                                                            #>
                                                                                <span class="eae-tm-separator"></span>
                                                                            <#
                                                                        }
                                                                        if(item.tm_description){
                                                                            #>
                                                                                <span {{{ view.getRenderAttributeString( 'tm-description' ) }}} class="eae-tm-description">{{{ item.tm_description }}}</span>
                                                                            <#
                                                                        }

                                                                        if(item.social_icon_1.value !== '' || item.social_icon_2.value !== '' || item.social_icon_3.value !== '' || item.social_icon_4.value !== '' || item.social_icon_5.value !== ''){
                                                                            if(settings.style_preset != 'style-2'){
                                                                                #>
                                                                                    <div {{{ view.getRenderAttributeString( 'tm-social-icon' ) }}}>
                                                                                        <#
                                                                                            get_social_icons(item, index);
                                                                                        #>
                                                                                    </div>
                                                                                <#
                                                                            }
                                                                        }
                                                                    #>
                                                                </div>
                                                                <#
                                                                    if(item.tm_button_text !== ''){
                                                                        view.addRenderAttribute('tm-button-link-' + index,'href',_.escape(item.tm_button_link.url) );
                                                                        #>
                                                                            <div {{{ view.getRenderAttributeString( 'tm-button-container' ) }}} >
                                                                                <a {{{ view.getRenderAttributeString( 'tm-button-link-' + index ) }}}>
                                                                                    {{{ item.tm_button_text }}}
                                                                                </a>
                                                                            </div>
                                                                        <#
                                                                    }
                                                                #>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <#
                                            });
                                        #>
                                    </div>
                                    <#
                                        if(settings.tm_layout == 'carousel'){
                                            if(settings['ptype'] !== ''){
                                                #>
                                                    <div class = "eae-swiper-pagination swiper-pagination"></div>
                                                <#
                                            }

                                            // Arrows Inside
                                            if(settings.navigation_button == 'yes' && settings.arrows_layout == 'inside' ){
                                                if(settings.arrow_horizontal_position != 'center' && settings.arrows_layout == 'inside'){
                                                    #>
                                                        <div class="eae-swiper-button-wrapper">
                                                    <#
                                                } 
                                                #>
                                                            <div class= "eae-swiper-button-prev swiper-button-prev">
                                                                <#
                                                                    let button_prev_icon_html = elementor.helpers.renderIcon(view, settings.arrow_icon_left , { 'aria-hidden': true }, 'i' , 'object' );
                                                                #>
                                                                {{{ button_prev_icon_html.value }}}
                                                            </div>
                                                            <div class = "eae-swiper-button-next swiper-button-next">
                                                                <#
                                                                    let button_next_icon_html = elementor.helpers.renderIcon(view, settings.arrow_icon_right , { 'aria-hidden': true }, 'i' , 'object' );
                                                                #>
                                                                {{{ button_next_icon_html.value }}}
                                                            </div>
                                                <#
                                                    if(settings.arrow_horizontal_position !== 'center' && settings.arrows_layout == 'inside'){
                                                        #>
                                                            </div>
                                                        <#
                                                    }
                                                
                                            }

                                            //swiper scrolbar
                                            if(settings.scrollbar == 'yes'){
                                                #>
                                                    <div class= "eae-swiper-scrollbar swiper-scrollbar" ></div>
                                                <#
                                            }
                                            
                                        }
                                    #>
                                </div>
                                <#
                                    if(settings.navigation_button == 'yes' && settings.arrows_layout == 'outside'){
                                        #>
                                            <div class= "eae-swiper-button-prev swiper-button-prev">
                                                <#
                                                    let button_prev_icon_html = elementor.helpers.renderIcon(view, settings.arrow_icon_left , { 'aria-hidden': true }, 'i' , 'object' );
                                                #>
                                                {{{ button_prev_icon_html.value }}}
                                            </div>
                                            <div class = "eae-swiper-button-next swiper-button-next">
                                                <#
                                                    let button_next_icon_html = elementor.helpers.renderIcon(view, settings.arrow_icon_right , { 'aria-hidden': true }, 'i' , 'object' );
                                                #>
                                                {{{ button_next_icon_html.value }}}
                                            </div>
                                        <#
                                            
                                #>
                            </div> 
                        <# }
                #>
            <?php
        }
    }
?>