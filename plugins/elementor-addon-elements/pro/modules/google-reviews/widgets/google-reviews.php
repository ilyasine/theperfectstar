<?php 
    namespace WTS_EAE\Pro\Modules\GoogleReviews\Widgets;

    use WTS_EAE\Base\EAE_Widget_Base;
    use Elementor\Controls_Manager;
    use Elementor\Utils;
    use Elementor\Icons_Manager;
    use WTS_EAE\Classes\Helper;
    use WTS_EAE\Classes\Swiper_helper;
    use Elementor\Group_Control_Typography;
    use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
    use Elementor\Group_Control_Text_Shadow;
    use Elementor\Group_Control_Text_Stroke;
    use Elementor\Group_Control_Box_Shadow;
    use Elementor\Group_Control_Background;
    use Elementor\Repeater;
    use Elementor\Group_Control_Border;
    use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

    if( ! defined('ABSPATH')){
        exit;
    }

    class GoogleReviews extends EAE_Widget_Base{
        
        public function get_name(){
            return 'eae-google-reviews';
        }

        public function get_title(){
            return __('Google Reviews','wts-eae');
        }

        public function get_categories(){
            return ['wts-eae'];
        }

        public function get_icon() {
            return 'eae-icon eae-google-review';
        }

        protected function register_controls(){
            $this->get_general_section();

            $this->get_layout_section();

            $this->get_carousel_section();

            $this->get_order_section();

            $this->get_header_style_section();

            $this->get_review_style_section();

            $this->get_carousel_style_section();

            $this->get_icon_style_section();
        }

        public function get_order_section(){
            $this->start_controls_section(
                'order_section',
                [
                    'label' => esc_html__('Order','wts-eae'),
                    'condition' => [
                        'style_preset' => 'style-1',
                    ]
                ]
            );

            $this->add_control(
                'image_order',
                [
                    'label' => esc_html__('Image','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 1,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-image-container' => 'order:{{VALUE}};'
                    ]
                ]
            );

            $this->add_control(
                'name_order',
                [
                    'label' => esc_html__('Name','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 2,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-name' => 'order:{{VALUE}};',
                        '{{WRAPPER}} .eae-rw-name-wrap' => 'order:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'icon_rating_order',
                [
                    'label' => esc_html__('Icon Rating','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 3,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-star-container' => 'order:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'review_date_order',
                [
                    'label' => esc_html__('Review Date','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 4,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-date, {{WRAPPER}} .eae-rw-rating-num' => 'order:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'review_separator_order',
                [
                    'label' => esc_html__('Separator','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 5,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-separator' => 'order:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'review_text_order',
                [
                    'label' => esc_html__('Text','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 6,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-review-text-container' => 'order:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'review_button_order',
                [
                    'label' => esc_html__('Button','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 7,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button' => 'order:{{VALUE}};',
                    ]
                ]
            );

            $this->end_controls_section();
        }

        public function get_header_style_section(){

            $this->start_controls_section(
                'header_style_section',
                [
                    'label' => esc_html__('Header','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'enable_header' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'heading_style_head',
                [
                    'label' => esc_html__('Heading','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'heading_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-title' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'heading_hover_color',
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-title:hover' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'rw_heading_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                    ],
                    'selector' => '{{WRAPPER}} .eae-rw-header-title',
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'rw_heading_text_shadow',
                    'selector' => '{{WRAPPER}} .eae-rw-header-title',
                ]
            );

            $this->add_responsive_control(
                'heading_text_alignment',
                [
                    'label' => esc_html__('Text Alignment','wts-eae'),
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
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'left',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-title' => 'text-align:{{VALUE}};'
                    ],
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'heading_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'rating_style_heading',
                [
                    'label' => esc_html__('Rating','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'enable_header_rating' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'rating_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-rating' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'enable_header_rating' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'rating_hover_color',
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-rating:hover' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'enable_header_rating' => 'yes'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'rating_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-rating',
                    'condition' => [
                        'enable_header_rating' => 'yes'
                    ]
                ]
            );

            $this->add_responsive_Control(
                'header_rating_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'condition' => [
                        'enable_header_rating' => 'yes'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-rating' => 'margin-right:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'rating_icon_style_heading',
                [
                    'label' => esc_html__('Rating Icon','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'marked_icon_color',
                [
                    'label' => esc_html__('Marked Icon Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-marked i' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-marked svg' => 'fill:{{VALUE}};'
                    ] 
                ]
            );

            $this->add_control(
                'unmarked_icon_color',
                [
                    'label' => esc_html__('Unmarked Icon Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-unmarked i' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-unmarked svg' => 'fill:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'header_icon_size',
                [
                    'label' => esc_html__('Icon Size','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-marked i' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-unmarked i' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-marked svg' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-rw-header-star-container .eae-rw-e-unmarked svg' => 'font-size:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'header_icon_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-star-container' => 'gap: {{SIZE}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_responsive_Control(
                'header_rating_icon_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-star-container' => 'margin-right:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'header_text_heading',
                [
                    'label' => esc_html__('Text','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'text_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-text' => 'color:{{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'text_hover_color',
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-text:hover' => 'color:{{VALUE}};',
                    ],
                ]
            );
            
            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'header_text_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-header-text',
                ]
            );

            $this->add_responsive_control(
                'header_rating_aling',
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
                        ]
                    ],
                    'toggle' => false,
                    'selectors_dictionary' => [
                        'left' => 'start',
                        'center' => 'center',
                        'right' => 'end', 
                    ],
                    'default' => 'left',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-review-details-container' => 'justify-content:{{VALUE}};'
                    ]
                ]
            );

            $this->add_control(
                'header_button_style_heading',
                [
                    'label' => esc_html__('Button','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'button_text_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-header-button',
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                ]
            );

            $this->start_controls_tabs('header_button_style');

            $this->start_controls_tab(
                'header_button_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                ]
            );

            $this->add_control(
                'button_text_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-button' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'header_button_background',
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
                        'enable_header_button' => 'yes'
                    ],
                    'selector' => '{{WRAPPER}} .eae-rw-header-button',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'header_button_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                ]
            );

            $this->add_control(
                'button_text_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-button:hover' => 'color:{{VALUE}};'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'header_button_hover_background',
                    'selector' => '{{WRAPPER}} .eae-rw-header-button:hover',
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'header_button_border_radiu',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'header_button_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'condition' => [
                        'enable_header_button' => 'yes'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'header_style_control_divider',
                [
                    'type' => Controls_Manager::DIVIDER,
                ]
            );

            $this->add_control(
                'header_container_style_heading',
                [
                    'label' => esc_html__('Container','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->start_controls_tabs('header_style_tabs');

            $this->start_controls_tab(
                'header_normal_style_tab',
                [
                    'label' => esc_html__('Normal','wts-ea'),
                ]
            );

            $this->add_group_control(
                Group_Control_background::get_type(),
                [
                    'name' => 'header_background',
                    'selector' => '{{WRAPPER}} .eae-rw-header',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'header_border',
                    'selector' => '{{WRAPPER}} .eae-rw-header',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'header_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-rw-header',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'header_hover_style_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_group_control(
                Group_Control_background::get_type(),
                [
                    'name' => 'header_hover_background',
                    'selector' => '{{WRAPPER}} .eae-rw-header:hover',
                ]
            );

            $this->add_control(
                'header_border_hover_color',
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header:hover' => 'border-color:{{VALUE}};'
                    ] 
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'header_hover_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-rw-header:hover',
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'header_horizontal_alignment',
                [
                    'label' => esc_html__('Horizontal Alignment','wts-eae'),
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
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            [
                                'name' => 'button_postion',
                                'operator' => '==',
                                'value' => 'row',
                            ],
                            [
                                'name' => 'enable_header_button',
                                'operator' => '!==',
                                'value'=>'yes',
                            ],
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header' => 'justify-content:{{VALUE}};'
                    ]
                ]
            );

            $this->add_responsive_control(
                'header_horizontal_alig',
                [
                    'label' => esc_html__('Horizontal Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
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
                    ],
                    'toggle' => false,
                    'selectors_dictionary' => [
                        'left' => 'start',
                        'center' => 'center',
                        'right' => 'end', 
                    ],
                    'default' => 'left',
                    'tablet_default' => 'left',
                    'mobile_default' => 'left',
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'button_postion',
                                'operator' => '==',
                                'value' => 'column',
                            ],
                            [
                                'name' => 'enable_header_button',
                                'operator' => '==',
                                'value'=> 'yes',
                            ],
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header' => 'align-items:{{VALUE}};'
                    ]
                ]
            );

            $this->add_responsive_control(
                'header_content_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ]
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 10,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header' => 'gap:{{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'header_content_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-container' => 'gap:{{SIZE}}{{UNIT}};',                           
                    ]
                ]
            );

            $this->add_responsive_control(
                'header_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'header_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->end_controls_section();
        }

        public function get_icon_style_section(){

            $this->start_controls_section(
                'icon_style_section',
                [
                    'label' => esc_html__('Icon','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            Helper::global_icon_style_controls(
                $this,
                [
                    'name'          => 'review_icon',
                    'selector'      => '.eae-rw-icon',
                    'hover_selector'      => '.eae-rw-item:hover .eae-rw-icon',
                    'is_repeater'   => false, 
                    'is_parent_hover' => true,
                ]
            );

            $this->end_controls_section();
        }

        public function get_review_style_section(){
            $this->start_controls_section(
                'review_style_section',
                [
                    'label' => esc_html__('Review','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'image_style_heading',
                [
                    'label' => esc_html__('Image','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [
                        'show_image' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'image_alignment',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'start' => [
                            'title' => esc_html__('Start','wts-eae'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                        'center' => [
                            'title' => esc_html__('Center','wts-eae'),
                            'icon' => 'eicon-justify-center-h',
                        ],
                        'end' => [
                            'title' => esc_html__('End','wts-eae'),
                            'icon' => 'eicon-justify-end-h',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'center',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-image-container' => 'align-self:{{VALUE}};'
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'show_image',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'style_preset',
                                'operator' => '==',
                                'value'=> 'style-1',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_control(
                'image_alignment_style_2',
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
                        '{{WRAPPER}} .eae-rw-image-container' => 'align-self:{{VALUE}};'
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'show_image',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'style_preset',
                                'operator' => '==',
                                'value'=> 'style-2',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_control(
                'image_position',
                [
                    'label' => esc_html__('Position','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left','wts-eae'),
                            'icon' => 'eicon-order-start',
                        ],
                        'right' => [
                            'title' => esc_html__('Right','wts-eae'),
                            'icon' => 'eicon-order-end',
                        ]
                    ],
                    'toggle' => false,
                    'selectors_dictionary' => [
                        'left' => 'row',
                        'right' => 'row-reverse',
                    ],
                    'default' => 'left',
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'show_image',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'style_preset',
                                'value'=> 'style-2',
                            ],
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-data-wrapper' => 'flex-direction:{{VALUE}};'
                    ]
                ]
            );

            $this->add_responsive_control(
                'image_size',
                [
                    'label' => esc_html__('Size','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 85,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-image-container img' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'show_image' => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'image_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 10,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-image-container' => 'margin-bottom:{{SIZE}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'show_image',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'style_preset',
                                'operator' => '==',
                                'value'=> 'style-1',
                            ],
                        ]
                    ]
                ]
            );

            $this->add_responsive_control(
                'image_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-data-wrapper' => 'gap:{{SIZE}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'show_image',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'style_preset',
                                'operator' => '==',
                                'value'=> 'style-2',
                            ],
                        ]
                    ]
                ]
            );

            $this->add_control(
                'name_style_heading',
                [
                    'label' => esc_html__('Name','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'name_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-name' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'name_hover_color',
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-item:hover .eae-rw-name' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );  

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'reviewer_name_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-name',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                    ],
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'reviewer_name',
                    'selector' => '{{WRAPPER}} .eae-rw-name',
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Stroke::get_type(),
                [
                    'name' => 'reviewer_name_text_stroke',
                    'selector' => '{{WRAPPER}} .eae-rw-name',
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'name_text_alignment',
                [
                    'label' => esc_html__('Text Alignment','wts-eae'),
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
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'center',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-name' => 'text-align:{{VALUE}};'
                    ],
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'name_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px',
                        'size ' => '5',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'review_date_style_heading',
                [
                    'label' => esc_html__('Review Date','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'review_date' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'review_date_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-date' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'review_date' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'review_date_hover_color',
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-item:hover .eae-rw-date' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'review_date' => 'yes'
                    ]
                ]
            );  

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'review_date_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-date',
                    'condition' => [
                        'review_date' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'review_date_alignment',
                [
                    'label' => esc_html__('Text Alignment','wts-eae'),
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
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'center',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-date' => 'text-align:{{VALUE}};'
                    ],
                    'condition' => [
                        'review_date' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'review_date_spaceing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size ' => '5',
                    ],
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-date' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'review_date' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'review_text_style_heading',
                [
                    'label' => esc_html__('Review Text','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'review_text' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_text_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-review-text-container' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'review_text' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_text_hover_color',
                [
                    'label' => esc_html__('Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-item:hover .eae-rw-review-text-container' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'review_text' => 'yes',
                    ]
                ]
            );  

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'review_text_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-review-text-container',
                    'condition' => [
                        'review_text' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_text_alignment',
                [
                    'label' => esc_html__('Text Alignment','wts-eae'),
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
                        ],
                    ],
                    'default' => 'center',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-review-text-container' => 'text-align:{{VALUE}};'
                    ],
                    'condition' => [
                        'review_text' => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'review_text_spaceing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size ' => '5',
                    ],
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-review-text-container' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'review_text' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'rating_icon_style_review',
                [
                    'label' => esc_html__('Rating Icon','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'enable_star_rating' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'rating_marked_icon_color',
                [
                    'label' => esc_html__('Marked Icon Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-star-container .eae-rw-e-marked i' => 'color:{{VALUE}};'
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'icon',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_control(
                'rating_icon_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-rating-num' => 'color:{{VALUE}};'
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'numeric',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'rating_numeric_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-rating-num',
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'numeric',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_background::get_type(),
                [
                    'name' => 'rating_numeric_backgriund',
                    'selector' => '{{WRAPPER}} .eae-rw-rating-num',
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
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'numeric',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'rating_numeric_border',
                    'selector' => '{{WRAPPER}} .eae-rw-rating-num',
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'numeric',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_control(
                'rating_unmarked_icon_color',
                [
                    'label' => esc_html__('Unmarked Icon Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-star-container .eae-rw-e-unmarked i' => 'color:{{VALUE}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'icon',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_control(
                'rating_icon_size',
                [
                    'label' => esc_html__('Icon Size','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-star-container .eae-rw-e-marked i' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-rw-star-container .eae-rw-e-unmarked i' => 'font-size:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-rw-rating-num i' => 'font-size:{{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'enable_star_rating' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'rating_icon_alignment',
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
                        ]
                    ],
                    'default' => 'center',
                    'selectors_dictionary' => [
                        'left' => 'start',
                        'center' => 'center',
                        'right' => 'end',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-star-container' => 'justify-content:{{VALUE}};',
                        '{{WRAPPER}} .eae-rw-rating-num' => 'align-self:{{VALUE}};'
                    ],
                    'condition' => [
                        'enable_star_rating' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'review_icon_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-star-container' => 'gap: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-rw-rating-num' => 'gap: {{SIZE}}{{UNIT}};'
                    ],
                    'condition' => [
                        'enable_star_rating' => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'review_icon_spaceing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px',
                        'size ' => '5',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-star-container' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-rw-rating-num' => 'margin-bottom:{{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'enable_star_rating' => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'review_numeric_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-rating-num' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'numeric',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_responsive_control(
                'review_numeric_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-rating-num' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_star_rating',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'star_rating_type',
                                'operator' => '==',
                                'value'=> 'numeric',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_control(
                'gr_separator_style_heading',
                [
                    'label' => esc_html__('Separator','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'gr_separator' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'gr_separator_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'gr_separator' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-separator' => 'border-color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'gr_separator_type',
                [
                    'label' => esc_html__('Separator Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'default',
                    'options' => [
                        'default' => esc_html__('Default','wts-eae'),
                        'solid' => esc_html__('Solid','wts-eae'),
                        'double' => esc_html__('Double','wts-eae'),
                        'dotted' => esc_html__('Dotted','wts-eae'),
                        'dashed' => esc_html__('Dashed','wts-eae'),
                        'groove' => esc_html__('Groove','wts-eae'),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-separator' => 'border-style:{{VALUE}};'
                    ],
                    'condition' => [
                        'gr_separator' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'gr_separator_size',
                [
                    'label' => esc_html__('Size','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-separator' => 'border-width: {{SIZE}}{{UNIT}} 0 0 0;',
                    ],
                    'condition' => [
                        'gr_separator' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'gr_separator_width',
                [
                    'label' => esc_html__('Width','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-separator' => 'width: {{SIZE}}{{UNIT}};'
                    ],
                    'condition' => [
                        'gr_separator' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'gr_separator_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px',
                        'size ' => '5',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-separator' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'gr_separator' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'review_button_style_heading',
                [
                    'label' => esc_html__('Button','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_button_alignment',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
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
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'center',
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button' => 'align-self:{{VALUE}};'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'button_typography',
                    'selector' => '{{WRAPPER}} .eae-rw-button',
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'button_spacing',
                [
                    'label' => esc_html__('Spacing','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'default' => [
                        'unit' => 'px',
                        'size ' => '5',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->start_controls_tabs('review_button');

            $this->start_controls_tab(
                'review_button_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_button_text_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_background::get_type(),
                [
                    'name' => 'review_button_background',
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
                    'selector' => '{{WRAPPER}} .eae-rw-button',
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'review_button_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-rw-button',
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'review_button_border',
                    'selector' => '{{WRAPPER}} .eae-rw-button',
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'review_button_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_button_hover_text_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button:hover' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_background::get_type(),
                [
                    'name' => 'review_hover_button_background',
                    'selector' => '{{WRAPPER}} .eae-rw-button:hover',
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'review_button_hover_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-rw-button:hover',
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'review_button_border_hover_color',
                [
                    'label' => esc_html__('Border Hover Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button:hover' => 'border-color: {{VALUE}};'
                    ],
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'review_button_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'separator' => 'before',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'review_button_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'enable_button' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'review_style_control_divider',
                [
                    'type' => Controls_Manager::DIVIDER,
                ]
            );

            $this->add_control(
                'review_container_style_heading',
                [
                    'label' => esc_html__('Container','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_responsive_control(
                'review_data_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                        'size' => 3,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-wrapper .eae-rw-item' => 'gap:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->start_controls_tabs('review_style_tabs');

            $this->start_controls_tab(
                'review_normal_style_tab',
                [
                    'label' => esc_html__('Normal','wts-ea'),
                ]
            );

            $this->add_group_control(
                Group_Control_background::get_type(),
                [
                    'name' => 'review_background',
                    'selector' => '{{WRAPPER}} .eae-rw-item',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'review_border',
                    'selector' => '{{WRAPPER}} .eae-rw-item',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'review_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-rw-item',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'review_hover_style_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_group_control(
                Group_Control_background::get_type(),
                [
                    'name' => 'review_hover_background',
                    'selector' => '{{WRAPPER}} .eae-rw-item:hover',
                ]
            );

            $this->add_control(
                'review_border_hover_color',
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type'  => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-item:hover' => 'border-color:{{VALUE}};'
                    ] 
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'review_hover_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-rw-item:hover',
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'review_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'separator' => 'before',
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'review_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->end_controls_section();
        }

        public function get_carousel_style_section(){
            $this->start_controls_section(
                'carousel_style_section',
                [
                    'label' => esc_html__('Carousel','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'layout',
                                'operator' => '===',
                                'value' => 'carousel',
                            ],
                            [
                                'relation' => 'or',
                                'terms' => [
                                    [
                                        'name' => 'navigation_button',
                                        'operator' => '===',
                                        'value' => 'yes',
                                    ],
                                    [
                                        'name' => 'ptype',
                                        'operator' => '!==',
                                        'value' => '',
                                    ],
                                    [
                                        'name' => 'scrollbar',
                                        'operator' => '===',
                                        'value' => 'yes',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );

            Swiper_helper::carousel_style_section($this);

            $this->end_controls_section();
        }

        public function get_carousel_section(){
            $this->start_controls_section(
                'carousel_setting_section',
                [
                    'label' => esc_html__('Slider Options','wts-eae'),
                    'condition' => [
                        'layout' => 'carousel',
                    ]
                ]
            );

            Swiper_helper::carousel_controls($this);

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
                'layout',
                [
                    'label' => esc_html__('Skin','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'grid' => esc_html__('Grid','wts-eae'),
                        'carousel' => esc_html__('Carousel','wts-eae'),
                    ],
                    'default' => 'grid',
                ]
            );
            
            $this->add_control(
                'style_preset',
                [
                    'label' => esc_html__('Preset','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'style-1' => esc_html__('Style 1','wts-eae'),
                        'style-2' => esc_html__('Style 2','wts-eae'),
                    ],
                    'default' => 'style-1',
                    'condition' => [
                        'layout!' => 'badge',
                    ]
                ]
            );

            $this->add_control(
                'head_position',
                [
                    'label' => esc_html__('Head Position','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'top' => [
                            'title' => esc_html__('Top','wts-eae'),
                            'icon' => 'eicon-justify-start-v',
                        ],
                        'bottom' => [
                            'title' => esc_html__('Bottom','wts-eae'),
                            'icon' => 'eicon-justify-end-v',
                        ]
                    ],
                    'default' => 'top',
                    'condition' => [
                        'style_preset' => 'style-2',
                    ]
                ]
            );

            $this->add_responsive_control(
                'column',
                [
                    'label' => esc_html__('Column','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 3,
                    'tablet_default' => 2,
                    'mobile_default' => 1,
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-wrapper' => 'grid-template-columns : repeat({{VALUE}}, 1fr);'
                    ],
                    'condition' => [
                        'layout' => 'grid',
                    ]
                ]
            );

            $this->add_responsive_control(
                'row_gap',
                [
                    'label' => esc_html__('Row Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-wrapper' => 'row-gap: {{SIZE}}{{UNIT}};'
                    ],
                    'condition' => [
                        'layout' => 'grid',
                    ]
                ]
            );

            $this->add_responsive_control(
                'column_gap',
                [
                    'label' => esc_html__('Column Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'condition' => [
                        'layout' => 'grid',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-wrapper' => 'column-gap: {{SIZE}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_control(
                'header_data_heading' ,
                [
                    'label' => esc_html__('Header','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'enable_header' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'heading_type' ,
                [
                    'label' => esc_html__('Heading Render Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'dynamic' => esc_html__('Dynamic','wts-eae'),
                        'custom' => esc_html__('Custom','wts-eae'),
                    ],
                    'default' => 'dynamic',
                    'condition' => [
                        'enable_header' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'heading',
                [
                    'label' => esc_html__('Heading Custom Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => 'Reviews',
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_header',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'heading_type',
                                'operator' => '==',
                                'value'=> 'custom',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_control(
                'heading_html_tag',
                [
                    'label' => esc_html__('Heading HTML Tag','wts-eae'),
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
                    'condition' => [
                        'enable_header' => 'yes',
                    ],
                ]
            );


            $this->add_control(
                'enable_header_rating',
                [
                    'label' => esc_html__('Enable Rating','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'enable_header' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'header_text',
                [
                    'label' => esc_html__('Text','wts-eae'),
                    'type' => Controls_Manager::TEXTAREA,
                    'default' => 'Over %rating% Reviews.',
                    'description' => __('To add overall rating use %rating%', 'wts-eae'),
                    'condition' => [
                        'enable_header' => 'yes',
                    ],

                ]
            );

            $this->add_control(
                'header_rating_icon',
                [
                    'label' => esc_html__('Rating Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                    'skin' => 'inline',
                    'label_block' => false,
                    'default' => [
                        'value' => 'fas fa-star',
                        'library' => 'fa-solid',
                    ],
                    'condition' => [
                        'enable_header' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'enable_header_button',
                [
                    'label' => esc_html__('Enable Button','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'condition' => [
                        'enable_header' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'header_button_text',
                [
                    'label' => esc_html__('Button Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => 'Write a Review',
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_header_button',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'enable_header',
                                'operator' => '==',
                                'value'=>'yes',
                            ],
                        ]
                    ],
                ]
            );

            $this->add_responsive_control(
                'button_postion',
                [
                    'label' => esc_html__('Position','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'default' => 'row',
                    'tablet_default' => 'row',
                    'mobile_default' => 'column',
                    'options' => [
                        'row' => [
                            'title' => esc_html__('Inline','wts-eae'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                        'column' => [
                            'title' => esc_html__('Block','wts-eae'),
                            'icon' => 'eicon-justify-end-v',
                        ],
                    ],
                    'toggle' => false,
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'enable_header_button',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'enable_header',
                                'operator' => '==',
                                'value'=>'yes'
                            ],
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-header' => 'flex-direction: {{VALUE}};'
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

            $google_map_api_key = get_option('wts_eae_gmap_key');
            
            $message = '';
            if(empty($google_map_api_key)){
                $message = sprintf(esc_html__('Add your Map api key in %s','wts-eae'),'<a href="'.admin_url('admin.php?page=eae-settings#eae-config').'">'.esc_html__('Settings','wts-eae').'</a>');
            
                $this->add_control(
                    'google_map_api_key_notice',
                    array(
                        'type'            => Controls_Manager::RAW_HTML,
                        'raw'             =>  __('<div class="eae-notice">'.$message.'</div>', 'wts-eae' ),
                        'separator'       => 'none',
                    )
                );
            }

            $this->add_control(
                'place_id',
                [
                    'label' => esc_html__('Place Id','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'description' => 'Get Google Place ID from <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank" >here</a>',
                ]
            );

            $this->add_control(
                'number_of_reviews',
                [
                    'label' => esc_html__('Number Of Reviews','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 5,
                    'min' => 1,
                    'max' => 5,
                ]
            );

            $this->add_control(
                'language_code',
                [
                    'label' => esc_html__('Language Code','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'en',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $this->add_control(
                'reload_review_time',
                [
                    'label' => esc_html__('Reload Review (Minutes)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => '120'
                ]
            );

            $this->add_control(
                'filter_by_rating',
                [
                    'label' => esc_html__('Filter By Rating','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        '1' => esc_html__('1 star','wts-eae'),
                        '2' => esc_html__('2 star','wts-eae'),
                        '3' => esc_html__('3 star','wts-eae'),
                        '4' => esc_html__('4 star','wts-eae'),
                        '5' => esc_html__('5 star','wts-eae'),
                    ],
                    'default' => '5',
                ]
            );

            $this->add_control(
                'show_image',
                [
                    'label' => esc_html__('Show Image','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'separator' => 'after',
                ]
            );

            $this->add_control(
                'enable_name',
                [
                    'label' => esc_html__('Name','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_type' => 'yes',
                    'default' => 'yes',
                    'label_on' => esc_html__('Show','wts-eae'),
                    'label_off' => esc_html__('Hide','wts-eae'), 
                ]
            );

            $this->add_control(
                'link_name',
                [
                    'label' => esc_html__('Link Name','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'enable_name' => 'yes',
                    ],
                    'default' => 'yes',
                ]
            );

            $this->add_control(
                'name_html_tag',
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
                    'default' => 'h3',
                    'condition' => [
                        'enable_name' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'enable_star_rating',
                [
                    'label' => esc_html__('Star Rating','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_type' => 'yes',
                    'separator' => 'before',
                    'default' => 'yes',
                    'label_on' => esc_html__('Show','wts-eae'),
                    'label_off' => esc_html__('Hide','wts-eae'),
                ]
            );

            $this->add_control(
                'star_rating_type',
                [
                    'label' => esc_html__('Star Rating Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'numeric' => esc_html__('Numeric','wts-eae'),
                        'icon' => esc_html__('Icon','wts-eae'),
                    ],
                    'default' => 'icon',
                    'condition' => [
                        'enable_star_rating' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'reviews_rating_icon',
                [
                    'label' => esc_html__('Rating Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                    'skin' => 'inline',
                    'label_block' => false,
                    'default' => [
                        'value' => 'fas fa-star',
                        'library' => 'fa-solid',
                    ],
                    'condition' => [
                        'enable_star_rating' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_date',
                [
                    'label' => esc_html__('Review Date','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_type' => 'yes',
                    'separator' => 'before',
                    'default' => 'yes',
                    'label_on' => esc_html__('Show','wts-eae'),
                    'label_off' => esc_html__('Hide','wts-eae'),
                ]
            );

            $this->add_control(
                'review_date_type',
                [
                    'label' => esc_html__('Review Date Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'numeric' => esc_html__('Numeric','wts-eae'),
                        'relative' => esc_html__('Relative','wts-eae'),
                    ],
                    'default' => 'relative',
                    'condition' => [
                        'review_date' => 'yes'
                    ]
                ]
            );

            $this->add_control(
                'review_text',
                [
                    'label' => esc_html__('Show Review Text','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'separator' => 'before',
                    'label_on' => esc_html__('Show','wts-eae'),
                    'label_off' => esc_html__('Hide','wts-eae'),
                ]
            );

            $this->add_control(
                'text_length',
                [
                    'label' => esc_html__('Text Length','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 20,
                    'condition' => [
                        'review_text' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'gr_separator',
                [
                    'label' => esc_html__('Separator','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_type' => 'yes',
                    'default' => 'yes',
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'enable_button',
                [
                    'label' => esc_html__('Enable Button','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_type' => 'yes',
                    'separator' => 'before',
                    'default' => 'yes',
                    'label_on' => esc_html__('Show','wts-eae'),
                    'label_off' => esc_html__('Hide','wts-eae'),
                ]
            );

            $this->add_control(
                'button_text',
                [
                    'label' => esc_html__('Button Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'Read More',
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'enable_button' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'review_icon_divider',
                [
                    'type' => Controls_Manager::DIVIDER,
                ]
            );

            Helper::eae_media_controls(
                $this,
                [
                    'name' => 'review_icon',
                    'icon' => true,
                    'image'	=> true,
                    'lottie' => true,
                ]
            );

            $this->add_control(
                'icon_horizontal_poisition',
                [
                    'label' => esc_html__('Horizontal Position','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'condition' => [
                        'review_icon_graphic_type!' => 'none',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-icon' => 'right:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'icon_vertical_position',
                [
                    'label' => esc_html__('Vertical Position','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','em','rem','%','deg','vh','custom'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'condition' => [
                        'review_icon_graphic_type!' => 'none',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-rw-icon' => 'top: {{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'enable_header',
                [
                    'label' => esc_html__('Enable Header','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'separator' => 'before',
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'label_on' => esc_html__('Show','wts-eae'),
                    'label_off' => esc_html__('Hide','wts-eae'),
                ]
            );

            $this->end_controls_section();
        }

        public function get_rating_icon($settings, $control_name, $css_class, $rating){
            $width = 0;
            $rating_scale = 5;
            $this->set_render_attribute('rating_container','class',$css_class);
            if($rating != '' && $settings[$control_name . '_rating_icon']['value'] != '' && $rating_scale != ''){ ?>
                <div <?php echo $this->get_render_attribute_string('rating_container'); ?>> <?php
                    for($i = 0; $i < $rating_scale; $i++){ ?>
                        <?php 
                            if(($rating - $i) >= 1){
                                $width = 1 * 100;
                            }else if($rating > $i){
                                $width = ($rating - $i) * 100;
                            }else{
                                $width = 0;
                            }
                        ?>
                        <div class="eae-rw-e-icon-container">
                            <div class="eae-rw-e-unmarked">
                                <?php 
                                    Icons_Manager::render_icon( $settings[$control_name . '_rating_icon'], [ 'aria-hidden' => 'true' ] );
                                ?>
                            </div>
                            <div class="eae-rw-e-marked" style="width:<?php echo $width ?>%;">
                                <?php 
                                    Icons_Manager::render_icon( $settings[$control_name . '_rating_icon'], [ 'aria-hidden' => 'true' ] );
                                ?>
                            </div>
                        </div>
                        <?php
                    } ?>
                </div><?php
            }
        }

        public function get_header($settings,$place_data){
            $this->add_render_attribute('heading','class','eae-rw-heading');
            $this->add_render_attribute('header-button','href',$place_data->googleMapsUri);
            $this->add_render_attribute('header-title','class','eae-rw-header-title');
            if($settings['enable_header'] == 'yes'){
                ?>
                    <div class="eae-rw-header">
                        <div class="eae-rw-header-content">
                            <?php
                                $heading = '';
                                if($settings['heading_type'] == 'custom'){
                                    $heading = Helper::eae_wp_kses($settings['heading']);
                                }else if($settings['heading_type'] == 'dynamic'){
                                    $heading = $place_data->displayName->text;
                                }

                                if($heading != ''){
                                    $heading = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['heading_html_tag'] ), $this->get_render_attribute_string( 'header-title' ), $heading); 
                                    echo $heading;
                                }
                            ?>
                            <div class="eae-rw-review-details-container">
                                <?php if($settings['enable_header_rating'] == 'yes'){ ?>
                                    <span class="eae-rw-rating"><?php echo $place_data->rating; ?></span>    
                                <?php } ?>
                                
                                <?php
                                    $this->get_rating_icon($settings, 'header', 'eae-rw-header-star-container', $place_data->rating);

                                    if($settings['header_text'] !== ''){
                                        $header_text = Helper::eae_wp_kses($settings['header_text']);
                                        $header_text = str_replace('%rating%',$place_data->userRatingCount,$header_text);
                                        ?>
                                            <span class="eae-rw-header-text"><?php echo $header_text; ?></span>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <?php if($settings['enable_header_button'] == 'yes' && $settings['header_button_text'] != ''){ ?> 
                            <a <?php echo $this->get_render_attribute_string('header-button'); ?>  target="_blank" class="eae-rw-header-button"><?php echo Helper::eae_wp_kses($settings['header_button_text']); ?></a>
                        <?php } ?>
                    </div>
                <?php
            }
        }

        public function render(){
            $settings = $this->get_settings_for_display();

            $api_key = get_option( 'wts_eae_gmap_key' );
            $place_id = $settings['place_id'];
            if($place_id !== '' && $api_key !== ''){
                $apiUrl = 'https://places.googleapis.com/v1/places/' . $place_id . '?fields=*&key=' . $api_key;
                $expiration = $settings['reload_review_time'];

                // $place_data = '';
                $transient_name = md5($apiUrl);
                
                if( false === get_transient($transient_name)){
                    $place_data = json_decode(file_get_contents($apiUrl));
                    set_transient($transient_name,$place_data,60 * $expiration);
                }else{
                    $place_data = get_transient($transient_name);   
                }

                $num_of_review = ($settings['number_of_reviews'] != '' ) ? $settings['number_of_reviews'] - 1 : 0;

                if($place_data !== '' && $place_data !== null){

                    $reviews = $place_data->reviews;

                    if($settings['layout'] == 'grid' || $settings['layout'] == 'carousel'){
                        $this->add_render_attribute('container','class','eae-rw-container');
                        $this->add_render_attribute('wrapper','class','eae-rw-wrapper');
                        $this->add_render_attribute('rw-item','class','eae-rw-item');
                        if($settings['style_preset'] == 'style-2' && $settings['head_position'] == 'bottom'){
                            $this->add_render_attribute('wrapper','class','eae-rw-preset-' . $settings['head_position']);
                        }
                        if($settings['layout'] == 'carousel'){
                            $swiper_data = Swiper_helper::get_swiper_data($settings);
                            $this->add_render_attribute('container','class','eae-rw-swiper');
            
                            $this->add_render_attribute('container','class','eae-swiper-outer-wrapper');
                            $this->add_render_attribute('wrapper','class','eae-swiper');
            
                            if($settings['arrows_layout'] == 'inside'){
                                $this->add_render_attribute('container','class','eae-hpos-' . $settings['arrow_horizontal_position']);
                                $this->add_render_attribute('container','class','eae-vpos-' . $settings['arrow_vertical_position']);
                            }
            
                            $this->add_render_attribute('container','data-swiper-settings', wp_json_encode( $swiper_data ) );
            
                            // $this->add_render_attribute( 'wrapper', 'class', 'eae-swiper-container swiper' );
                            $this->add_render_attribute( 'wrapper', 'class', 'eae-swiper-container ' );
            
                            $this->add_render_attribute('carousel_wrapper','class', 'eae-swiper-wrapper swiper-wrapper');
            
                            $this->add_render_attribute('rw-item','class','eae-swiper-slide swiper-slide');
                            
                            $slider_id = rand(99,9999);
                            $this->add_render_attribute('wrapper','data-eae-slider-id',$slider_id);

                            $this->add_render_attribute('wrapper','class','eae-slider-id-'. $slider_id);
            
                            if( $settings['auto_height'] !== 'yes'){
                                $this->add_render_attribute('container','class','eae-height-100');
                            }
                            if($settings['arrows_layout'] == 'outside'){
                                $this->add_render_attribute('container','class','eae-slider-id-'. $slider_id);
                            }
            
                            $this->add_render_attribute('carousel_wrapper','class','eae-rw-carousel-wrapper');
                        }

                        if($settings['enable_header'] == 'yes' || $settings['layout'] == 'carousel' ){ ?>
                            <div <?php echo $this->get_render_attribute_string('container'); ?> >
                                <?php
                                    $this->get_header($settings,$place_data);
                                ?>        
                        <?php }
                                $this->add_render_attribute('name','class','eae-rw-name'); ?>
                                <div <?php echo $this->get_render_attribute_string('wrapper'); ?> ><?php
                                    if($settings['layout'] == 'carousel' ){ ?>
                                        <div <?php echo $this->get_render_attribute_string('carousel_wrapper'); ?>>
                                            <?php }
                                            foreach( $reviews as $index => $item ){ ?>
                                                <?php

                                                    if($settings['enable_button'] == 'yes'){
                                                        $author_url = $item->authorAttribution->uri;
                                                        $author_url = explode('/reviews',$author_url); 
                                                        $review_url = $author_url[0] . '/place/' . $place_id;
                                                    }
                                                    $language_code = '';
                                                    $flag = 0;
                                                    if($settings['language_code'] !== '' || $settings['filter_by_rating'] !== ''){
                                                        $language_code = $item->originalText->languageCode;
                                                        $start_rating = $item->rating;
                                                        if($language_code == $settings['language_code'] && $item->rating >= $settings['filter_by_rating']){
                                                            $flag = 1;
                                                        }
                                                        if($settings['language_code'] == '' && $item->rating >= $settings['filter_by_rating']){
                                                            $flag = 1;
                                                        }
                                                    }else{
                                                        $flag = 1;
                                                    }
                                                    
                                                ?>
                                                <?php if($flag == 1) { ?>
                                                    <div <?php echo $this->get_render_attribute_string('rw-item'); ?> >
                                                        <?php
                                                            if($settings['review_icon_graphic_type'] != 'none'){
                                                                Helper::render_icon_html($settings,$this,'review_icon','eae-rw-icon');
                                                            }
                                                        ?>
                                                        <?php if($settings['style_preset'] == 'style-2'){ ?>
                                                            <div class="eae-rw-data-wrapper">
                                                        <?php } ?>
                                                                <?php if($settings['show_image'] == 'yes'){ ?>
                                                                    <div class="eae-rw-image-container">
                                                                        <img src='<?php echo esc_url($item->authorAttribution->photoUri); ?>' alt="">
                                                                    </div>
                                                                <?php } ?>
                                                                <?php
                                                                    if($settings['style_preset'] == 'style-2'){ ?>
                                                                    <div class="eae-rw-details-wrapper"><?php
                                                                    }
                                                                        if($settings['enable_name'] == 'yes'){
                                                                            if($settings['link_name'] == 'yes'){
                                                                                $this->add_render_attribute('name-link-' . $index,'class','eae-rw-name-wrap');
                                                                                $this->add_render_attribute('name-link-' . $index,'href',$item->authorAttribution->uri);
                                                                                ?>
                                                                                    <a <?php echo $this->get_render_attribute_string('name-link-' . $index); ?> target="_blank" >
                                                                                <?php
                                                                            }
                                                                            if($item->authorAttribution->displayName != ''){
                                                                                $name = sprintf('<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag($settings['name_html_tag']), $this->get_render_attribute_string('name'), $item->authorAttribution->displayName );
                                                                                echo $name;
                                                                            }
                                                                            if($settings['link_name'] == 'yes'){
                                                                                ?>
                                                                                    </a>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        if($settings['enable_star_rating'] == 'yes'){
                                                                            if($settings['star_rating_type'] == 'icon'){
                                                                                $this->get_rating_icon($settings,'reviews','eae-rw-star-container',$item->rating);
                                                                            }else{
                                                                                ?>
                                                                                    <div class="eae-rw-rating-num">
                                                                                        <?php 
                                                                                            echo $item->rating;
                                                                                            Icons_Manager::render_icon( $settings['reviews_rating_icon'], [ 'aria-hidden' => 'true' ] ); 
                                                                                        ?>
                                                                                    </div>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        if($settings['review_date'] == 'yes'){
                                                                            ?>
                                                                                <span class="eae-rw-date">
                                                                                    <?php 
                                                                                        if($settings['review_date_type'] == 'numeric'){
                                                                                            $date = date_create($item->publishTime);
                                                                                            $date = date_format($date,"d-m-Y");
                                                                                            echo $date;
                                                                                        }else{
                                                                                            echo $item->relativePublishTimeDescription;
                                                                                        }
                                                                                    ?>
                                                                                </span>
                                                                            <?php
                                                                        }

                                                                        if($settings['text_length'] != ''){
                                                                            $text = wp_strip_all_tags($item->originalText->text);
                                                                            $text_length = ($settings['text_length'] != '') ? $settings['text_length']: 20;
                                                                            $words = explode(' ',$text, $text_length + 1 );
                                                                            if( count($words) > $text_length){
                                                                                array_pop($words);
                                                                                $text = implode(' ',$words);
                                                                                $text .= '...';
                                                                            }
                                                                        }else{
                                                                            $text = wp_strip_all_tags($item->originalText->text);
                                                                        }
                                                                    if($settings['style_preset'] == 'style-2'){ ?>
                                                                    </div><?php
                                                                    }
                                                                ?>
                                                                <?php if($settings['style_preset'] == 'style-2'){ ?>
                                                            </div> <?php
                                                                } 
                                                        if($settings['gr_separator'] == 'yes'){ ?>
                                                            <span class="eae-rw-separator"></span>
                                                        <?php }
                                                            if($settings['review_text'] == 'yes'){
                                                                ?><span class="eae-rw-review-text-container"><?php echo wp_kses_post($text); ?></span><?php
                                                            }
                                                        ?>
                                                        <?php if($settings['enable_button'] == 'yes' && $settings['button_text'] != ''){
                                                            $button = '<a href="'. $review_url .'" target="_blank" class="eae-rw-button">' . Helper::eae_wp_kses($settings['button_text']) . '</a>';
                                                            echo $button; 
                                                        } ?> 
                                                    </div>
                                                <?php }

                                                if($num_of_review == $index){
                                                    break;
                                                }
                                            } 
                                            if($settings['layout'] == 'carousel'){ 
                                            ?>
                                        </div>
                                        <?php 
                                        Swiper_helper::get_swiper_pagination($settings);
                                        // Arrows Indside
                                        if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
                                            Swiper_helper::get_swiper_arrows($settings);
                                        }
            
                                        Swiper_helper::get_swiper_scrolbar($settings); 
                                        ?>
                                    <?php } ?>

                                </div><?php
                                if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
                                    /** Arrows Outside **/
                                    Swiper_Helper::get_swiper_arrows($settings);
                                }
                        if($settings['enable_header'] == 'yes' || $settings['layout'] == 'carousel'){ ?>
                            </div>
                        <?php }
                    }
                }else if($place_data == ''){
                    if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                        echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-warning'>Invalid Place ID. Please check and try again</p></div>";
                        return;
                    }
                } 
            }else{
                if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                    if(empty($api_key)){
                        echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-warning'>Please add Map api key in dashboard > Elementor Addons Elements > Configuration</p></div>";
                        return;
                    }else{
                        if(empty($place_id)){
                            echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-warning'>Please Add Place ID</p></div>";
                            return;
                        }
                    }
                }
            }
        }
    }

?>