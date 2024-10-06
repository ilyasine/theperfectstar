<?php 
    namespace WTS_EAE\Pro\Modules\AdvancedList\Widgets;

    use Elementor\Controls_Manager;
    use Elementor\Group_Control_Background;
    use Elementor\Group_Control_Border;
    use Elementor\Group_Control_Box_Shadow;
    use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
    use Elementor\Group_Control_Text_Shadow;
    use Elementor\Group_Control_Typography;
    use Elementor\Utils;
    use Elementor\Repeater;
    use WTS_EAE\Base\EAE_Widget_Base;
    use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
    use Elementor\Group_Control_Base;
    use WTS_EAE\Classes\Helper;

    if( ! defined( 'ABSPATH' ) ){
        exit;
    }

    class AdvancedList extends EAE_Widget_Base {
        public function get_name(){
            return 'eae-advanced-list';
        }

        public function get_title(){
            return esc_html__('Advanced List','wts-eae');        
        }

		public function get_icon() {
			return 'eae-icon eae-advanced-list';
		}

        public function get_categories()
        {
            return [ 'wts-eae' ];
        }

        public function get_script_depends() {
            return [ 'eae-lottie' ];
        }

        public function get_keywords() {
            return [ 'list', 'stylish-list', 'advanced-list', 'vertical-list', 'horizontal-list', 'icon-list', 'lottie'];
        }

        protected function register_controls()
        {
            $this->start_controls_section(
                'section_general',
                [
                    'label' => esc_html__('General','wts-eae'),
                ]
            );

            $repeater = new Repeater();

            $repeater->start_controls_tabs('general_tab');

            $repeater->start_controls_tab(
                'content_tab',
                [
                    'label' => esc_html__('Content','wts-eae'),
                ]
            );

            $repeater->add_control(
                'list_title',
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'default' => 'List Item',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater->add_control(
                'list_description',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::TEXTAREA,
                    'default' => 'Lorem ipsum dolor sit amet consectetur adipiscing elit dolor',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater->add_control(
                'list_badge',
                [
                    'label' => esc_html__('Show Badge','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $repeater->add_control(
                'list_badge_text',
                [
                    'label' => esc_html__('Badge Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'condition'=> [
                        'list_badge' => 'yes'
                    ],
                    'default' => 'New',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater->add_control(
                'list_item_link_url',
                [
                    'label' => esc_html__('Link','wts-eae'),
                    'type' => Controls_Manager::URL,
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater->end_controls_tab();

            $repeater->start_controls_tab(
                'item_icon',
                [
                    'label' => esc_html__( 'Icon', 'wts-eae' ),
                ]
            );
            
            Helper::eae_media_controls(
                $repeater,
                [
                    'name' => 'list',
                    'label' => 'Icon',
                    'icon'			=> true,
                    'image'			=> true,
                    'lottie'		=> true,
                ]
            );

            $repeater->end_controls_tab();

            $repeater->start_controls_tab(
                'content_style',
                [
                    'label' => esc_html__('Style','wts-eae'),
                ]
            );

            $repeater->add_control(
                'list_custom_style',
                [
                    'label' => esc_html__('Custom Style','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Show','wts-eae'),
                    'label_off' => esc_html__('Hide','wts-eae'),
                ]
            );

            $repeater->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'list_element_background',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.eae-list-item',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'list_element_border',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.eae-list-item',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_item_hover_heading',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'list_element_hover_background',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.eae-list-item:hover',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_element_hover_border',
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}}.eae-list-item:hover' => 'border-color:{{VALUE}};',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'counter_custom_style',
                [
                    'label' => esc_html__('Counter','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'al_counter_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-item-count' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'counter_background',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-item-count',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'title_custom_style',
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_title_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-title' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'list_title_shadow',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-title',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

           $repeater->add_control(
                'list_title_hover_heading',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
           );

           $repeater->add_control(
                'list_title_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-text-wrapper:hover .eae-list-title' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'list_title_hover_shadow',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-text-wrapper:hover .eae-list-title',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'description_custom_style',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_description_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-description' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_description_hover_heading',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_description_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-text-wrapper:hover .eae-list-description' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'badge_custom_style',
                [
                    'label' => esc_html__('Badge','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_badge_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-badge span' => 'color:{{VALUE}}',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'badge_background',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-badge span', 
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_pint_hint_hover_heading',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'list_badge_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-badge:hover span' => 'color:{{VALUE}};',
                    ],
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'badge_hover_background',
                    'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-list-badge:hover span',
                    'separator' => 'after',
                    'condition' => [
                        'list_custom_style' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'icon_style_heading',
                [
                    'label' => esc_html__('Icon','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            Helper::repeater_icon_style_controls(
                $repeater,[
                    'name' => 'list',
                    'selector'      => '.eae-al-icon',
                    'hover_selector'      => '.eae-list-item:hover .eae-al-icon',
                    'is_parent_hover' => true,
                ]   
            );

            $repeater->end_controls_tabs();

            $this->add_control(
                'list_items', 
                [
                    'label' => esc_html__('List','wts-eae'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' => [
                        [
                            'list_title' => esc_html__('List Item 1','wts-eae'),
                            'list_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae')
                        ],
                        [
                            'list_title' => esc_html__('List Item 2','wts-eae'),
                            'list_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae')
                        ],
                        [
                            'list_title' => esc_html__('List Item 3','wts-eae'),
                            'list_description' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor','wts-eae')
                        ]
                    ],
                    'title_field' => '{{{list_title}}}',
                ]
            );

            Helper::eae_media_controls(
                $this,
                [
                    'name' => 'list',
                    'label' => 'Icon',
                    'icon'			=> true,
                    'image'			=> true,
                    'lottie'		=> true,
                ]
            );

            $this->add_control(
                'list_direction',
                [
                    'label' => esc_html__('List Direction','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'column' => esc_html__('Vertical','wts-eae'),
                        'row' => esc_html__('Horizontal','wts-eae'),
                    ],
                    'default' => 'column',
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-wrapper' => 'flex-direction:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'list_counter',
                [
                    'label' => esc_html__('Counter','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_control(
                'list_grid_view',
                [
                    'label' => esc_html__('Grid View','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_responsive_control(
                'list_grid_column',
                [
                    'label' => esc_html__('Column','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 2,
                    'condition' => [
                        'list_grid_view' => 'yes',
                    ]
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'settings_section',
                [
                    'label' => esc_html__('Settings','wts-eae'),
                ]
            );

            $this->add_control(
                'title_html_tag',
                [
                    'label' => esc_html__('Title HTML Tag','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'h1' => esc_html__('H1','wts-eae'),
                        'h2' => esc_html__('H2','wts-eae'),
                        'h3' => esc_html__('H3','wts-eae'),
                        'h4' => esc_html__('H4','wts-eae'),
                        'h5' => esc_html__('H5','wts-eae'),
                        'h6' => esc_html__('H6','wts-eae'),
                    ],
                    'default' => 'h4',
                ]
            );

            $this->add_control(
                'list_item_link_position',
                [
                    'label' => esc_html__('Link','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'icon' => esc_html__('Link On Icons','wts-eae'),
                        'list' => esc_html__('Link On List Item','wts-eae'),
                    ],
                    'default' => 'list',
                ]
            );

            $this->add_control(
                'text_aling',
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
                            'icon' => 'eicon-text-align-justify',
                        ],
                        'right' => [
                            'title' => esc_html__('Right','wts-eae'),
                            'icon' => 'eicon-text-align-right',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'left',
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-text' => 'text-align: {{VALUE}};',
                    ],
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            [
                                'name' => 'list_direction',
                                'value' => 'row',
                            ],
                            [
                                'name' => 'list_grid_view',
                                'value'=>'yes'
                            ],
                        ]
                    ]
                ]
            );

            $this->add_responsive_control(
                'list_item_gap',
                [
                    'label' => esc_html__('List Item Row Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 10,
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => 5,
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => 5,
                        'unit' => 'px',
                    ],
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            [
                                'name' => 'list_direction',
                                'value' => 'column',
                            ],
                            [
                                'name' => 'list_grid_view',
                                'value'=>'yes'
                            ],
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-wrapper' => 'row-gap:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'list_column_gap',
                [
                    'label' => esc_html__('List Item Column Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 10,
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => 5,
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => 5,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-wrapper' => 'column-gap:{{SIZE}}{{UNIT}};',
                    ],
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'list_direction',
                                'value' => 'row',
                            ],
                            [
                                'name' => 'list_grid_view',
                                'value'=>''
                            ],
                        ]
                    ],
                ]
            );

            $this->add_responsive_control(
                'list_item_column_gap',
                [
                    'label' => esc_html__('List Item Column Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 10,
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => 5,
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => 5,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-grid-view' => 'column-gap:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-list-grid-item' => 'width: calc((100% - (({{list_grid_column.valu}} - 1) * {{SIZE}}{{UNIT}})) / {{list_grid_column.valu}});',
                    ],
                    'condition' => [
                        'list_grid_view' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'List_counter_heading',
                [
                    'label' => esc_html__('Counter','wts-eae'),
                    'type' => Controls_Manager::HEADING, 
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'separator' => 'before',
                ]
            );

            $this->add_control(
                'list_counter_style',
                [
                    'label' => esc_html__('Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'upper-alpha' => esc_html__('Upper Alpha','wts-eae'),
                        'lower-alpha' => esc_html__('Lower Alpha','wts-eae'),
                        'upper-roman' => esc_html__('Upper Roman','wts-eae'),
                        'lower-roman' => esc_html__('Lower Roman','wts-eae'),
                        'number' => esc_html__('Number','wts-eae'),
                        'decimal-leading-zero'  => esc_html__( 'Decimal Leading Zero', 'wts-eae' ),
                        'lower-greek'  => esc_html__( 'Lower Greek', 'wts-eae' ),
                    ],
                    'default' => 'number',
                    'selectors' => [    
                        '{{WRAPPER}} .eae-item-count::before' => 'counter-increment: list-item; content: counter(list-item,{{VALUE}} ) "{{list_counter_suffix.value}}";',
                    ],
                    'condition' => [
                        'list_counter' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'list_counter_suffix',
                [
                    'label' => esc_html__('Suffix','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        ' ' => esc_html__('None','wts-eae'),
                        ':' => esc_html__('Colon','wts-eae'),
                        ')' => esc_html__('Bracket','wts-eae'),
                        '.' => esc_html__('Dot','wts-eae'),
                    ],

                    'default' => ' ',
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                ]   
            );

            $this->add_control(
                'counter_position',
                [
                    'label' => esc_html__('Position','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'vertical' => [
                            'title' => esc_html__('Vertical','wts-eae'),
                            'icon' => 'eicon-justify-start-v',
                        ],
                        'horizontal' => [
                            'title' => esc_html__('Horizontal','wts-eae'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                    ],
                    'toggle' => false,
                    'selectors_dictionary' => [
                        'vertical' => 'column',
                        'horizontal' => 'row',
                    ],
                    'default' => 'horizontal',
                    'conditions' => [
                        'relation' => 'and',
                        'terms'    => [
                            [
                                'name' => 'list_counter',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'list_direction',
                                'value'=> 'row'
                            ],
                        ]
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item' => 'flex-direction: {{VALUE}};',
                    ],
                ]
            );
            
            $this->add_control(
                'counter_aling',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'start' => [
                            'title' => esc_html__('Top','wts-eae'),
                            'icon' => 'eicon-justify-start-v',
                        ],
                        'center' => [
                            'title' => esc_html__('Center','wts-eae'),
                            'icon' => 'eicon-justify-center-v',
                        ],
                        'end' => [
                            'title' => esc_html__('Bottom','wts-eae'),
                            'icon' => 'eicon-justify-end-v',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'center',
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item' => 'align-items: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'counter_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 10,
                        'unit' => 'px',
                    ],
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item' => 'gap:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'list_icon_setting_heading',
                [
                    'label' => esc_html__('Icon','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'icon_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
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
                        '{{WRAPPER}} .eae-list-text-wrapper' => 'gap:{{SIZE}}{{UNIT}};'
                    ]
                ]
            );

            $this->add_responsive_control(
                'icon_position_row',
                [
                    'label' => esc_html__('Position','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left','wts-eae'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                        'right' => [
                            'title' => esc_html__('Right','wts-eae'),
                            'icon' => 'eicon-justify-end-h',
                        ]
                    ],
                    'selectors_dictionary' => [
                        'left' => 'row',
                        'right' => 'row-reverse',
                    ],
                    'default' => 'left',
                    'toggle' => false,
                    'condition' => [
                        'list_direction'  => 'column',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-text-wrapper' => 'flex-direction: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'icon_position_column',
                [
                    'label' => esc_html__('Position','wts-eae'),
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
                    'selectors_dictionary' => [
                        'top' => 'column',
                        'bottom' => 'column-reverse',
                    ],
                    'default' => 'top',
                    'condition' => [
                        'list_direction' => 'row',
                    ],
                    'toggle' => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-text-wrapper' => 'flex-direction: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'icon_aling_row',
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
                    'default' => 'left',
                    'selectors_dictionary' => [
                        'left' => 'start',
                        'center' => 'center',
                        'right' => 'end',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-text-wrapper' => 'align-items: {{VALUE}};',
                    ],
                    'condition' => [
                        'list_direction' =>'row'
                    ]
                ]
            );

            $this->add_responsive_control(
                'icon_aling_column',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'top' => [
                            'title' => esc_html__('Top','wts-eae'),
                            'icon' => 'eicon-justify-start-v',
                        ],
                        'center' => [
                            'title' => esc_html__('Center','wts-eae'),
                            'icon' => 'eicon-justify-center-v',
                        ],
                        'bottom' => [
                            'title' => esc_html__('Bottom','wts-eae'),
                            'icon' => 'eicon-justify-end-v',
                        ]
                    ],
                    'toggle' => false,
                    'default' => 'top',
                    'selectors_dictionary' => [
                        'top' => 'start',
                        'center' => 'center',
                        'bottom' => 'end',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-text-wrapper' => 'align-items: {{VALUE}};',
                    ],
                    'condition' => [
                        'list_direction' =>'column'
                    ]
                ]
            );

            $this->add_control(
                'badge_heading',
                [
                    'label' => esc_html__('Badge','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'badge_position',
                [
                    'label' => esc_html__('Position','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => esc_html__('Left','wts-eae'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                        'right' => [
                            'title' => esc_html__('Right','wts-eae'),
                            'icon' => 'eicon-justify-end-h',
                        ]
                    ],
                    'selectors_dictionary' => [
                        'left' => 'reverse',
                        'right' => ' ',
                    ],
                    'toggle' => false,
                    'default' => 'right',
                    'selectors' => [
                        '{{WRAPPER}} .eae-advance-list-container' => 'flex-direction: row-{{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'badge_alignment',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'top' => [
                            'title' => esc_html__('Top','wts-eae'),
                            'icon' => 'eicon-justify-start-v',
                        ],
                        'center' => [
                            'title' => esc_html__('Center','wts-eae'),
                            'icon' => 'eicon-justify-center-v'
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
                    'toggle' => false,
                    'default' => 'top',
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-badge' => 'align-items: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'badge_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 10,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-advance-list-container' => 'gap: {{SIZE}}{{UNIT}};'
                    ]
                ]
            );

            $this->end_controls_section();
        
            $this->start_controls_section(
                'list_content_style_secition',
                [
                    'label' => esc_html__('General','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'container_background_color',
                    'selector' => '{{WRAPPER}} .eae-list-wrapper',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'container_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-wrapper',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'container_border',
                    'selector' => '{{WRAPPER}} .eae-list-wrapper',
                ]
            );

            $this->add_responsive_control(
                'container_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'container_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'container_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'list_item_style_section',
                [
                    'label' => esc_html__('List Item','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->start_controls_tabs('list_item_tab');

            $this->start_controls_tab(
                'item_normal',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'list_item_background',
                    'selector' => '{{WRAPPER}} .eae-list-item',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'item_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-item',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'list_item_border',
                    'selector' => '{{WRAPPER}} .eae-list-item',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'item_hover',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'item_blur',
                [
                    'label' => esc_html__('Blur Effect','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'list_item_hover_background',
                    'selector' => '{{WRAPPER}} .eae-list-item:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'item_box_shadow_hover',
                    'selector' => '{{WRAPPER}} .eae-list-item:hover',
                ]
            );

            $this->add_control(
                'list_item_hover_border_color',
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'list_item_border_border!' => ['none',''],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item:hover' => 'border-color: {{VALUE}};'
                    ]
                ]
            );

            $this->add_control(
                'item_hover_animation',
                [
                    'label' => esc_html__('Hover Animation','wts-eae'),
                    'type' => Controls_Manager::HOVER_ANIMATION,
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'item_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'list_item_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'sie_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', 
                    ],
                ]
            );

            $this->add_responsive_control(
                'list_item_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNiT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'counter_style_heading',
                [
                    'label' => esc_html__('Counter','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                ]
            );

            $this->add_responsive_control(
                'counter_height',
                [
                    'label' => esc_html__('Box Height','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 20,
                        'unit' => 'px',
                    ],
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-item-count' => 'height:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'counter_width',
                [
                    'label' => esc_html__('Box Width','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 500,
                            'step' => 5,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 20,
                        'unit' => 'px',
                    ],
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-item-count' => 'width:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'counter_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selector' => '{{WRAPPER}} .eae-item-count',
                ]
            );

            $this->start_controls_tabs('counter_tab');

            $this->start_controls_tab(
                'counter_normal',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'counter_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-item-count' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'counter_background',
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selector' => '{{WRAPPER}} .eae-item-count',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'counter_border',
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selector' => '{{WRAPPER}} .eae-item-count',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'counter_hover',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'counter_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item:hover .eae-item-count' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'counter_hover_background',
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selector' => '{{WRAPPER}} .eae-list-item:hover .eae-item-count',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'counter_hover_border',
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selector' => '{{WRAPPER}} .eae-list-item:hover .eae-item-count',
                ]
            );

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'counter_border_radius',
                [
                    'label' => esc_html__('Border Radious','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-item-count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'counter_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'condition' => [
                        'list_counter' => 'yes',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-item-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'title_style_heading',
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->start_controls_tabs( 'title_style_tab' );

            $this->start_controls_tab(
                'title_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                ]
            );

            $this->add_control(
                'title_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-title' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'title_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-title',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'title_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'title_hover_color',
                [
                    'label' => esc_html__('Color','wts_eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item:hover .eae-list-title' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'title_hover_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-item:hover .eae-list-title',
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'selector' => '{{WRAPPER}} .eae-list-title',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                    ],
                ]
            );

            $this->add_control(
                'description_heading',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->start_controls_tabs('description_style_tab');

            $this->start_controls_tab(
                'description_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                ]
            );

            $this->add_control(
                'description_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-description' => 'color:{{VALUE}};',
                    ] 
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'description_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-description',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'description_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'description_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-text-wrapper:hover .eae-list-description' => 'color:{{VALUE}}',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'description_hover_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-text-wrapper:hover .eae-list-description',
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'description_typography',
                    'selector' => '{{WRAPPER}} .eae-list-description',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                ]
            );

            $this->add_responsive_control(
                'description_padding',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-description' => 'margin: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};'
                    ]
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'list_icon_style_section',
                [
                    'label' => esc_html__('Icon','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
            
            Helper::global_icon_style_controls(
                $this,
                [
                    'name'          => 'list',
                    'selector'      => ' .eae-al-icon',
                    'hover_selector'      => '.eae-list-item:hover .eae-al-icon',
                    'is_repeater'   => false, 
                    'is_parent_hover' => true,
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'badge',
                [
                    'label' => esc_html__('Badge','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'badge_typography',
                    'selector' => '{{WRAPPER}} .eae-list-badge span',
                ]
            );

            $this->start_controls_tabs('badge_tab');

            $this->start_controls_tab(
                'badge_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae'),
                ]
            );

            $this->add_control(
                'badge_text_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR, 
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-badge span' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'badge_background_color',
                    'selector' => '{{WRAPPER}} .eae-list-badge span',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'badge_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-badge span',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'badge_box_border',
                    'selector' => '{{WRAPPER}} .eae-list-badge span',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'badge_hover_tab',
                [
                    'label' => esc_html__('Hover','wts-eae'),
                ]
            );

            $this->add_control(
                'badge_text_hover_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR, 
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-badge span:hover' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'badge_hover_background_color',
                    'selector' => '{{WRAPPER}} .eae-list-badge span:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'badge_hover_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-list-badge span:hover',
                ]
            );

            $this->add_control(
                'badge_hover_border_color',
                [
                    'label' => esc_html__('Border Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'badge_box_border_border!' => ['none',''],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-item:hover span' => 'border-color: {{VALUE}};'
                    ]
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'badge_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ] 
                ]
            );

            $this->add_responsive_control(
                'badge_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ] 
                ]
            );

            $this->add_responsive_control(
                'badge_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-list-badge span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->end_controls_section();
        }

        public function render(){
            $settings = $this->get_settings_for_display();
            $this->add_render_attribute('list-item-wrapper','class','eae-list-wrapper');
            $this->add_render_attribute('list-title','class','eae-list-title');
            $this->add_render_attribute('list-item','class','eae-list-item');
            $grid = '';
            if($settings['list_grid_view'] === 'yes'){
                $this->add_render_attribute('list-item-wrapper','class','eae-list-grid-view');
                $grid = 'eae-list-grid-item';
            }
            $hover = '';
            $blur =  '';
            if ( ! empty( $settings['item_hover_animation'] ) ) {
                $hover = 'elementor-animation-' . $settings['item_hover_animation'];
            }
            if($settings['list_counter'] === 'yes')
            {
                $this->add_render_attribute('list-item-wrapper','class','eae-counter');
                
            }
            if($settings['item_blur'] === 'yes')
            {
                $this->add_render_attribute('list-item-wrapper','class','eae-list-wrapper-blur');
                $blur = 'eae-list-item-blur';
            }
            $html_tag='';
            ?>
                <div  <?php echo $this->get_render_attribute_string('list-item-wrapper'); ?> >
                    <?php foreach ( $settings['list_items'] as $index => $item ): 
                        $this->add_render_attribute('list-item-'.$index,'class',['elementor-repeater-item-'.$item['_id'],'eae-list-item',$hover,$blur,$grid]); 
                        $this->add_link_attributes('list-item-'.$index,$item['list_item_link_url']);
                        $this->add_link_attributes('list_'.$index,$item['list_item_link_url']); 
                        $htmlTag = '';
                        if($settings['list_item_link_position'] === 'list' && !empty($item['list_item_link_url']['url']) ){
                            $htmlTag = 'a ';
                            if($item['list_title'] != ''){
                                $this->add_render_attribute('list-item-'.$index,'title',$item['list_title']);
                            }
                        }else{
                            $htmlTag = 'div ';
                        }
                        ?>
                        <<?php echo $htmlTag;?><?php  echo $this->get_render_attribute_string('list-item-'.$index); ?> >
                            <?php if($settings['list_counter'] === 'yes'){    ?>            
                                <div class="eae-item-count"></div>
                            <?php } ?>
                            <div class="eae-advance-list-container">
                                <div class="eae-list-text-wrapper">
                                    <?php if($settings['list_item_link_position'] === 'icon' && !empty($item['list_item_link_url']['url']) && ($settings['list_graphic_type'] !== 'none' || $item['list_graphic_type'] !== 'none')) {
                                            ?><a <?php echo $this->get_render_attribute_string('list_'.$index); ?>><?php
                                            $html_tag = '</a>';
                                        } 
                                        if($settings['list_graphic_type'] !== 'none')
                                        {
                                            if($item['list_graphic_type'] !== 'none' ){
                                                Helper::render_icon_html($item,$this, 'list', 'eae-al-icon');
                                            }else{
                                                Helper::render_icon_html($settings,$this, 'list', 'eae-al-icon');
                                            }   
                                        }else{
                                            if($item['list_graphic_type'] !== 'none' ){
                                                Helper::render_icon_html($item,$this, 'list', 'eae-al-icon');
                                            }
                                        }
                                        echo $html_tag;
                                    ?>    
                                    <div class="eae-list-text">
                                        <?php
                                            if($item['list_title'] !== '' ){
                                                $list_title = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag($settings['title_html_tag'] ), $this->get_render_attribute_string( 'list-title' ),  Helper::eae_wp_kses($item['list_title'])); 
                                                echo $list_title;
                                            }
                                            if($item['list_description'] !== ''){
                                                ?>
                                                    <span class="eae-list-description" ><?php echo Helper::eae_wp_kses($item['list_description']); ?></span>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php if($item['list_badge'] !== ''){
                                    ?>
                                        <div class="eae-list-badge">
                                            <span><?php echo Helper::eae_wp_kses($item['list_badge_text']); ?></span>
                                        </div>
                                    <?php
                                } ?>
                            </div>   
                        </<?php echo $htmlTag; ?>>
                    <?php endforeach; ?>
                </div>   
            <?php
        }

        /**
        * Render icon list widget output in the editor.
        *
        * Written as a Backbone JavaScript template and used to generate the live preview.
        *
        * @since 2.9.0
        * @access protected
        */
        protected function content_template() {
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

                    view.addRenderAttribute('list-item-wrapper','class','eae-list-wrapper');
                    view.addRenderAttribute('list-title','class','eae-list-title');
                    view.addRenderAttribute('list-item','class','eae-list-item');
                    let grid = '';
                    if(settings.list_grid_view === 'yes'){
                        view.addRenderAttribute('list-item-wrapper','class','eae-list-grid-view')
                        grid = 'eae-list-grid-item';
                    }
                    let hover = '';
                    let blur = '';
                    if(settings.item_hover_animation !== '')
                    {
                        hover = 'elementor-animation-' + settings.item_hover_animation ;
                    }
                    if(settings.list_counter == 'yes'){
                        view.addRenderAttribute('list-item-wrapper','class','eae-counter');
                    }
                    if(settings.item_blur == 'yes'){
                        view.addRenderAttribute('list-item-wrapper','class','eae-list-wrapper-blur');
                        blur = 'eae-list-item-blur';
                    }
                    let html_tag = '';
                #>
                <div {{{ view.getRenderAttributeString( 'list-item-wrapper' ) }}} >
                    <#
                    var controlName = 'list'; 
                    _.each( settings.list_items, function ( item, index ){
                        
                        let liteItemTag = '';
                        let href = '';
                        let titleAtt = '';
                        let titleText = '';
                        if(settings.list_item_link_position == 'list' && (item.list_item_link_url.url != '')){
                            liteItemTag = 'a';
                            titleText = item.list_title;
                        }else{
                            liteItemTag = 'div';
                        }
                        view.addRenderAttribute('list-item-'+index, 'class', ['elementor-repeater-item-' + item._id]);
                        view.addRenderAttribute('list-item-'+index, 'class', ['eae-list-item']);
                        view.addRenderAttribute('list-item-'+index, 'href', _.escape(item.list_item_link_url.url)); 
                        view.addRenderAttribute('list-item-'+index, 'title', titleText);
                        #>
                        <{{liteItemTag}} {{{view.getRenderAttributeString( 'list-item-'+index )}}}>
                            <# if(settings.list_counter == 'yes'){ #>
                                <div class="eae-item-count"></div>
                            <# } #>
                            <div class="eae-advance-list-container">
                                <div class="eae-list-text-wrapper">
                                    <# if( settings.list_item_link_position == 'icon' && (item.list_item_link_url.url != '') && ( settings.list_graphic_type != 'none' || item.list_graphic_type != 'none' )){                                         
                                        view.addRenderAttribute('list_'+index, 'href', _.escape(item.list_item_link_url.url));
                                        #>
                                       <a {{{view.getRenderAttributeString('list_'+index) }}}>
                                       <# html_tag = "</a>";
                                    } 
                                    let imageHtml ='';
		                            let lottie_data = [];
                                    let lottie_settings_data = '';
                                        if(item[controlName+'_graphic_type'] != 'none'){
                                            iconHtml = window.renderIconHtml(view,elementor,item, 'list', 'eae-al-icon', index);
                                            if(iconHtml != ''){
                                                print(iconHtml);
                                            }
                                        }else{
                                            if(settings[controlName+'_graphic_type'] != 'none'){
                                                iconHtml = window.renderIconHtml(view,elementor,settings, 'list', 'eae-al-icon', index);
                                                if(iconHtml != ''){
                                                    print(iconHtml);
                                                }
                                            }
                                            
                                        }
                                    #>
                                    {{{ html_tag }}}
                                    <div class="eae-list-text">
                                        <# if( item.list_title != '' ){
                                            var title = item.list_title ;
                                            var title_tag = window.eae.validateHTMLTag( settings.title_html_tag, null, 'h4' );
                                            let list_title = '<' + title_tag + ' ' + view.getRenderAttributeString( 'list-title' ) + '>' + title + '</' + title_tag + '>';
                                            print( list_title );
                                        } 
                                        if(item.list_description != ''){ #>
                                            <span class="eae-list-description" >{{{ item.list_description }}}</span>
                                        <# } #>
                                    </div> 
                                </div>
                                <# if(item.list_badge != ''){ #>
                                    <div class="eae-list-badge">
                                        <span>{{{item.list_badge_text}}}</span>
                                    </div>
                                <# } #>
                            </div>
                        </{{liteItemTag}}>
                       
                   <# }); #>
                </div>
            <?php
        }
        
    }
?>