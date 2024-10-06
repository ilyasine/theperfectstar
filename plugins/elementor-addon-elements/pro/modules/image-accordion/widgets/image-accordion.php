<?php

namespace WTS_EAE\Pro\Modules\ImageAccordion\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ImageAccordion extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-image-accordion';
	}

	public function get_title() {
		return __( 'Image Accordion', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-image-accordion';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'image', 'slider', 'accordion', 'panel slider'];
	}

	public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

	protected function register_controls() {
        $this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

        $this->add_control(
            'skin',
            [
                'label' => __('Skin', 'wts-eae'),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    'transparent' => __('Transparent', 'wts-eae'),
                    'panel' => __('Panel', 'wts-eae'),
                ],
                'default' => 'transparent',
            ]
        );

        $repeater =  new Repeater();
        
        $repeater->start_controls_tabs(
			'item_tabs'
		);

        $repeater->start_controls_tab(
			'item_content_tab',
			[
				'label' => esc_html__( 'Content', 'wts-eae' ),
			]
		);

        

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'wts-eae'),
                'type'  => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'  => ''
                ],
                'separator' => 'before'
            ]
        );

        $repeater->add_control(
            'title', 
            [
                'label' => __('Title', 'wts-eae'),
                'type'  => Controls_Manager::TEXT,
                'default' => __('This is title', 'wts-eae')
            ]
        );

        $repeater->add_control(
            'title_tag', 
            [
                'label' => __('Title Tag', 'wts-eae'),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __('H1', 'wts-eae'),
                    'h2' => __('H2', 'wts-eae'),
                    'h3' => __('H3', 'wts-eae'),
                    'h4' => __('H4', 'wts-eae'),
                    'h5' => __('H5', 'wts-eae'),
                    'h6' => __('H6', 'wts-eae'),
                    'div' => __('Div', 'wts-eae'),
                    'span' => __('span', 'wts-eae'),
                    'p' => __('p', 'wts-eae'),
                ],
                'default' => 'h3'
            ]
        );

        $repeater->add_control(
            'description', 
            [
                'label' => __('Description', 'wts-eae'),
                'type'  => Controls_Manager::TEXTAREA,
            ]
        );

        
        $repeater->add_control(
			'description_tag',
			[
				'label' => esc_html__( 'Description Tag', 'wts-eae' ),
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
				'default' => 'p',
			]
		);

        $repeater->add_control(
            'link_text',
            [
                'label' => __('Button Text', 'wts-eae'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Read More', 'wts-eae'),
                'default' => __('Click Here', 'wts-eae')
            ]
        );


        $repeater->add_control(
            'link',
            [
                'label' => __('Link', 'wts-eae'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://google.com', 'wts-eae'),
                'default' => [
					'url' => '#',
				],
            ]
        );

		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'wts-eae' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
                'skin' => 'inline'
			]
		);

        $repeater->add_control(
			'icon_position',
			[
				'label' => esc_html__( 'Icon Postition', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'before' => 'Before',
					'after' => 'After',
				],
				'default' => 'after',
			]
		);


        $repeater->add_control(
            'active_on_load',
            [
                'label' => __('Active on Load', 'wts-eae'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'wts-eae' ),
				'label_off' => esc_html__( 'Hide', 'wts-eae' ),
				'return_value' => 'yes',
            ]
        );

    
        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'item_icon',
            [
				'label' => esc_html__( 'Icon', 'wts-eae' ),
			]
        );

        $repeater->add_control(
            'panel_icon_heading',
            [
                'label' => __('Icon', 'wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        Helper::eae_media_controls(
            $repeater,[
                'name' => 'panel',
                'label' => 'Icon',
                'icon'			=> true,
                'image'			=> true,
				'lottie'		=> true,
            ]
        );

       

        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
            'item_style_tab',
            [
				'label' => esc_html__( 'Style', 'wts-eae' ),
			]
        );

        $repeater->add_control(
			'icon_style_options',
			[
				'label' => esc_html__( 'Icon', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        Helper::repeater_icon_style_controls(
            $repeater,[
                'name' => 'panel',
                'selector'      => '.eae-ia-panel-icon',
            ]   
        );

        $repeater->add_control(
			'panel_options',
			[
				'label' => esc_html__( 'Panel', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'panel_item_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .eae-img-acc-wrapper.eae-ia-skin-panel {{CURRENT_ITEM}}.eae-img-panel',
			]
		);

        $repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'panel_item_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.eae-img-panel',
			]
		);

        

        $repeater->add_responsive_control(
			'panel_item_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .eae-img-acc-wrapper.eae-ia-skin-panel {{CURRENT_ITEM}}.eae-img-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        

        $repeater->add_control(
			'content_options',
			[
				'label' => esc_html__( 'Content', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

        $repeater->add_control(
			'content_horizontal_position',
			[
				'label'        => __( 'Horizontal Position', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'flex-start' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-conent' => 'align-items : {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'slides_vertical_position',
			[
				'label'        => __( 'Vertical Position', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'flex-start' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-conent' => 'justify-content : {{VALUE}};',
				],
			]

		);  
        
        $repeater->add_responsive_control(
			'content_align',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-content-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);
        
        $repeater->add_responsive_control(
			'panel_content_width',
			[
				'label'          => __( 'Content Width', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-conent .eae-panel-content-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cotnent_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-content-wrap',
			]
		);

        $repeater->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-content-wrap',
			]
		);

        $repeater->add_responsive_control(
			'content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $repeater->add_responsive_control(
			'content_spacing',
			[
				'label'          => __( 'Spacing', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
                'separator'      => 'after',
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);   
        
        // Title Cutom Style
    

        $repeater->add_control(
			'title_style',
			[
				'label' => esc_html__( 'Title', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before'
			]
		);

        $repeater->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-title',
			]
		);

        $repeater->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-title',
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-title',
               
			]
		);

		$repeater->add_control(
			'blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Normal', 'wts-eae' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-title' => 'mix-blend-mode: {{VALUE}}',
				],
               
			]
		); 

        
        // Add Desc Headin

        $repeater->add_control(
			'desc_style',
			[
				'label' => esc_html__( 'Description', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
                
                'separator' => 'before'
			]
		);

        $repeater->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
                
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-desc' => 'color: {{VALUE}};',
				],
               
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-desc',
               
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'desc_text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-pane-item-desc',
               
			]
		);

        $this->item_button_style_controls($repeater);
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();
        
        

        $this->add_control(
			'items',
			[
				'label' => esc_html__( 'Items', 'wts-eae' ),
				'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
				'default' => [
					[
                        'title' => __('Image Accordion Title', 'wts-eae'),
                        'link_text' => __('Click Here', 'wts-eae'),
                        'active_on_load' => 'yes'
					],
                    [
                        'title' => __('Image Accordion Title', 'wts-eae'),
                        'link_text' => __('Click Here', 'wts-eae'),
                        'link' => [ 'url' => '#' ],
					],
                    [
                        'title' => __('Image Accordion Title', 'wts-eae'),
                        'link_text' => __('Click Here', 'wts-eae'),
					],
				],
				'title_field' => '{{{ title }}}',
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
                'name' => 'ia_gbl',
                'label' => 'Icon',
                'icon'			=> true,
                'image'			=> true,
				'lottie'		=> true,
                'defaults'      => [
                    'graphic_type_default' => 'icon',
                    'graphic_icon_default' => [
                        'value' => 'fas fa-star-half-alt',
                        'library' => 'fa-solid'
                    ],
                    'view_default' => 'framed',
                 ]
            ]
        );

        $this->end_controls_section();

        
        
        $this->start_controls_section(
            'settings',
            [
                'label' => __('Settings', 'wts-eae')
             ]
        );




        $this->add_responsive_control(
			'panel_width',
			[
				'label' => esc_html__( 'Width', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' , 'px'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-img-acc-wrapper' => '--eae-panel-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'panel_min_height',
			[
				'label' => esc_html__( 'Height', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
                'mobile_default' => [
                    'unit' => 'px',
					'size' => 300,
                ],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],	
				],
				'selectors' => [
					'{{WRAPPER}} .eae-img-panel' => 'height : {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'panel_gap',
			[
				'label' => esc_html__( 'Gap', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' =>  15,
				],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],	
				],
				'selectors' => [
					'{{WRAPPER}} .eae-img-acc-wrapper.eae-ia-skin-panel' => 'column-gap : {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-img-acc-wrapper.enable-stacked' => 'row-gap : {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'skin' => 'panel'
                ]
			]
		);

        $this->add_control(
            'action_behaviour',
            [
                'label' => __('Trigger Action', 'wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'hover' => __('Hover', 'wts-eae'),
                    'click' => __('Click', 'wts-eae')
                ],
                'default' => 'hover'
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
        $defualt_device = array_key_first($break_value_arr);
        
        $this->add_control(
            'stacked_below',
            [
                'label' => __('Stacked Device', 'wts-eae'),
                'type' => Controls_Manager::SELECT,
                'options' => $break_value_arr,
                'default' => $defualt_device
            ]
        );

        $this->add_control(
			'show_counter',
			[
				'label' => esc_html__( 'Show Counter', 'wts-eae' ),
				'type' =>  Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'wts-eae' ),
				'label_off' => esc_html__( 'Hide', 'wts-eae' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_control(
			'counter_style',
			[
				'label' => esc_html__( 'Counter Style', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'number-normal',
				'options' => [								
					'number-normal'  => esc_html__( 'Normal', 'wts-eae' ),
					'decimal-leading-zero'  => esc_html__( 'Decimal Leading Zero', 'wts-eae' ),
					'upper-alpha'  => esc_html__( 'Upper Alpha', 'wts-eae' ),
					'lower-alpha'  => esc_html__( 'Lower Alpha', 'wts-eae' ),
					'lower-roman'  => esc_html__( 'Lower Roman', 'wts-eae' ),
					'upper-roman'  => esc_html__( 'Upper Roman', 'wts-eae' ),
					'lower-greek'  => esc_html__( 'Lower Greek', 'wts-eae' ),
				],
				'selectors' => [
					'{{WRAPPER}} .eae-img-acc-wrapper.show_counter' => 'counter-reset: section;',
					'{{WRAPPER}} .eae-img-acc-wrapper.show_counter .eae-img-panel .eae-panel-conent:before' => 'content: counter(section, {{VALUE}}); counter-increment: section;',
				],
				'condition'    => [
					'show_counter' => 'yes',
				],
			]
		);
        $this->end_controls_section();

        $this->start_controls_section(
            'panel_style',
            [
                'label' => __('Panel', 'wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'panel_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .eae-img-acc-wrapper',
			]
		);

        $this->add_responsive_control(
			'panel_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
                'default'    => [
					'top'    => 12,
					'right'  => 12,
					'bottom' => 12,
					'left'   => 12,
					'unit'   => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-panel-conent' => 'padding : {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'panel_border',
				'selector' => '{{WRAPPER}} .eae-img-panel',
			]
		);

        $this->add_responsive_control(
			'panel_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .eae-img-acc-wrapper.eae-ia-skin-panel .eae-img-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'skin' => 'panel'
                ]
			]
		);

        $this->add_control(
			'content_options',
			[
				'label' => esc_html__( 'Content Options', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'content_horizontal_position',
			[
				'label'        => __( 'Horizontal Position', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'center',
				'options'      => [
					'flex-start' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
                'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .eae-panel-conent' => 'align-items : {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slides_vertical_position',
			[
				'label'        => __( 'Vertical Position', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'flex-start' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .eae-panel-conent' => 'justify-content : {{VALUE}};',
				],
			]
		);  
        
        $this->add_responsive_control(
			'content_align',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .eae-panel-content-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'panel_content_width',
			[
				'label'          => __( 'Content Width', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'size' => '66',
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors'      => [
					'{{WRAPPER}} .eae-panel-content-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cotnent_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .eae-panel-content-wrap',
			]
		);

        $this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .eae-panel-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'selector' => '{{WRAPPER}} .eae-panel-content-wrap',
			]
		);

        $this->add_responsive_control(
			'content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .eae-panel-content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'content_spacing',
			[
				'label'          => __( 'Spacing', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'size' => '10',
					'unit' => 'px',
				],
				'selectors'      => [
					'{{WRAPPER}} .eae-pane-item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-pane-item-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'title_style',
            [
                'label' => __('Title', 'wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-pane-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .eae-pane-item-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .eae-pane-item-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .eae-pane-item-title',
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Normal', 'wts-eae' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-pane-item-title' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);  

        $this->end_controls_section();

        $this->start_controls_section(
            'desc_style',
            [
                'label' => __('Description', 'wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
			'desc_align',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'wts-eae' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-pane-item-desc' => 'text-align: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-pane-item-desc' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
                'selector' => '{{WRAPPER}} .eae-pane-item-desc'
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'desc_text_shadow',
				'selector' => '{{WRAPPER}} .eae-pane-item-desc',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'button_style',
            [
                'label' => __('Button', 'wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eae_panle_link_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .eae-panel-link',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'button_text_shadow',
				'selector' => '{{WRAPPER}} .eae-panel-link',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style', [
		] );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-panel-link' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .eae-panel-link',
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

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'wts-eae' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-panel-link:hover, {{WRAPPER}} .eae-panel-link:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eae-panel-link:hover svg, {{WRAPPER}} .eae-panel-link:focus svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .eae-panel-link:hover, {{WRAPPER}} .eae-panel-link:focus',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-panel-link:hover, {{WRAPPER}} .eae-panel-link:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'wts-eae' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .eae-panel-link',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .eae-panel-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]				
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .eae-panel-link',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .eae-panel-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

				'separator' => 'before',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'conter_style_section',
            [
                'label' => __('Counter', 'wts-eae'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_counter' => 'yes'
                ]
            ]
        );

        $this->add_responsive_control(
            'counter_poition_top',
            [
                'label' => esc_html__( 'Top', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['px'],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 10,
						'step'=>1,
                    ],
                ],
				'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-img-acc-wrapper .eae-img-panel .eae-panel-conent:before' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'counter_poition_left',
            [
                'label' => esc_html__( 'Left', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['px'],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 10,
						'step'=>1,
                    ],
                ],
				'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-img-acc-wrapper .eae-img-panel .eae-panel-conent:before' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );



        $this->start_controls_tabs(
            'counter_style_tabs'
        );

        $this->start_controls_tab(
			'counter_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'wts-eae' ),
			]
		);

		$this->add_control(
            'conter_color',
            [
                'label' => __('Color', 'wts-eaa'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-img-acc-wrapper .eae-img-panel .eae-panel-conent:before' => 'color : {{VALUE}};',
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'counter_typography',
				'selector' => '{{WRAPPER}} .eae-img-acc-wrapper .eae-img-panel .eae-panel-conent:before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'counter_style_hover_tab',
			[
				'label' => esc_html__( 'Active/Hover', 'wts-eae' ),
			]
		);

		$this->add_control(
            'conter_color_hover',
            [
                'label' => __('Color', 'wts-eaa'),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-img-acc-wrapper .eae-img-panel .eae-panel-conent:hover:before' => 'color : {{VALUE}};',
					'{{WRAPPER}} .eae-img-acc-wrapper .eae-img-panel.active .eae-panel-conent:before' => 'color : {{VALUE}};',
                ]
            ]
        );

		$this->end_controls_tab();

		$this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'wts-eae' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);



        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'ia_gbl',
                'selector'      => '.eae-ia-panel-icon',
            ]
        );

        $this->end_controls_section();
    }

    public function render(){
        $setttings = $this->get_settings_for_display();
            $this->render_transparent($setttings);
    }

    public function content_template(){
        ?>
        <#
        let renderIconHtml = function(sett, control_name, wClass = '', index='') {
                    var icon_class = '';
                    let imageHtml = '';
                    let lottie_data = [];
                    if(sett[control_name+'_graphic_type'] != 'none' ){
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
            let globalIconName = 'ia_gbl';
            let itemIconName = 'panel';
            var getActiveItem = function(list){
                var aItem = '';
                _.each( list, function( listItem, index ) {
                    if(listItem.active_on_load == 'yes'){
                        aItem = index + 1;
                    }
                })
                if(aItem == ''){
                    aItem = 1;
                }
                return aItem;
            }
            
            let activeItem = getActiveItem(settings.items);
            view.addRenderAttribute( 'wrapper', 'class', 'eae-img-acc-wrapper' );
            view.addRenderAttribute( 'wrapper', 'data-stacked', settings.stacked_below );
			if ( 'panel' == settings.skin ) {
				view.addRenderAttribute( 'wrapper', 'class', 'eae-ia-skin-panel' );
			}
            if ( 'yes' == settings.show_counter ) {
				view.addRenderAttribute( 'wrapper', 'class', 'show_counter' );
			}
            view.addRenderAttribute( 'wrapper', 'data-defult-panel', activeItem );
            view.addRenderAttribute( 'wrapper', 'data-action', settings.action_behaviour );
            view.addRenderAttribute( 'wrapper', 'data-items', settings.items.length );

        #>
        <div {{{view.getRenderAttributeString( 'wrapper' )}}}>
            <#
            _.each( settings.items, function( item, index ) {
                var imageHtml = '';
                let panel_classes = "eae-img-panel elementor-repeater-item-"+ item._id;
                if(activeItem == (index + 1)){
                    panel_classes += ' active';
                }
                view.addRenderAttribute( 'panel-'+index, 'class', panel_classes);
                view.addRenderAttribute( 'panel-title-'+index, 'class', 'eae-pane-item-title');
		        view.addInlineEditingAttributes( 'panel-title-'+index );
                view.addRenderAttribute( 'panel-desc-'+index, 'class', 'eae-pane-item-desc');
                view.addInlineEditingAttributes( 'panel-desc-'+index );
                view.addRenderAttribute( 'panel-link-'+index, 'class', 'eae-panel-link');
                if(item.icon.value != ''){
                    view.addRenderAttribute( 'panel-link-'+index, 'class', 'eae-link-icon-pos-'+item.icon_position);
                }
                if(item.link.url != ''){
                    view.addRenderAttribute( 'panel-link-'+index, 'href', _.escape(item.link.url));
                }
                #>
                <div {{{ view.getRenderAttributeString( 'panel-'+index ) }}}>
                    <#
                    if ( item.image.url ) {
                        var image = {
                            id: item.image.id,
                            url: item.image.url,
                            size: settings.image_size,
                            dimension: settings.image_custom_dimension,
                            model: view.getEditModel()
                        };
			            var image_url = elementor.imagesManager.getImageUrl( image );
			            imageHtml = '<img src="' + image_url + '" class="eae-panel-img" />';
		            }#>
                    {{{imageHtml}}}
                    <div class="eae-panel-conent">
                        <div class="eae-panel-content-wrap">
                        <#
                            if(item[itemIconName+'_graphic_type'] != 'none'){
                                    iconHtml = window.renderIconHtml(view,elementor,item, itemIconName, 'eae-ia-panel-icon', index);
                                    if(iconHtml != ''){
                                        print(iconHtml);
                                    }
                            }else{
                                if(settings[globalIconName+'_graphic_type'] != 'none'){
                                    iconHtml = window.renderIconHtml(view,elementor,settings, globalIconName, 'eae-ia-panel-icon', index);
                                    if(iconHtml != ''){
                                        print(iconHtml);
                                    }
                                }           
                            }
                            if(item.title != ''){
								let title_tag = window.eae.validateHTMLTag( item.title_tag, null, 'h3' );
                                #>
                                 <{{{title_tag}}} {{{view.getRenderAttributeString( 'panel-title-'+index ) }}}>{{{item.title}}} </{{{title_tag}}}>
                                <#
                            }
                            if(item.description != ''){
								let desc_tag = window.eae.validateHTMLTag( item.description_tag, null, 'p' );
                                #>
                                <{{{desc_tag}}} {{{view.getRenderAttributeString( 'panel-desc-'+index ) }}}>{{{item.description}}} </{{{ desc_tag }}}>
                                <#
                            }
                            if(item.link_text != '' ){
                                var buttonIconHtml = '';
                                if(item.icon.value != ''){
                                    var Buttonicon = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i' , 'object' );
                                    buttonIconHtml = Buttonicon.value;
                                }
                                
                                #>
                                <a {{{view.getRenderAttributeString( 'panel-link-'+index )}}}>
                                     {{{item.link_text}}} {{{buttonIconHtml}}}
                                </a>
                                <#  
                            }
                        #>
                        </div>
                    </div>
                </div>
                <#
            })
            #>

        </div>
        <#
        #>
        <?php
    }

    

    
    public function render_transparent($setttings){
        $helper = new Helper();
        $items = $setttings['items'];
        $item_count = count($items);
        
        $active_item = '' == $this->get_active_item($items) ? 1 : $this->get_active_item($items);
        $this->add_render_attribute('wrapper', 'class', 'eae-img-acc-wrapper');
        $this->add_render_attribute('wrapper', 'data-stacked', $setttings['stacked_below']);
        if($setttings['skin'] == 'panel'){
            $this->add_render_attribute('wrapper', 'class', 'eae-ia-skin-panel');
        }
        $this->add_render_attribute('wrapper', 'data-defult-panel', $active_item);
        $this->add_render_attribute('wrapper', 'data-action', $setttings['action_behaviour']);
        $this->add_render_attribute('wrapper', 'data-items', $item_count);
        if($setttings['show_counter'] == 'yes'){
            $this->add_render_attribute('wrapper', 'class', 'show_counter');
        }
        ?>
        <div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
            <?php 
                $attr = [
                    'class' => 'eae-panel-img'
                ];
                foreach($items as $key => $item){
                    $panel_classes = [
                        'eae-img-panel',
                        'elementor-repeater-item-' . $item['_id']
                    ];
                    
                    
                    if($active_item == ($key + 1)){
                        $panel_classes[] = 'active';
                    }
                    $this->set_render_attribute('panel', 'class', $panel_classes);
                    $this->set_render_attribute('panel-title', 'class', ['eae-pane-item-title']);
                    $this->set_render_attribute('panel-desc', 'class', ['eae-pane-item-desc']);
                    $this->add_render_attribute('link-'.$key,'class', 'eae-panel-link');
                    if(!empty($item['icon']['value'])){
                        $this->add_render_attribute('link-'.$key,'class', 'eae-link-icon-pos-'.$item['icon_position']);
                    }
                    if($setttings['hover_animation'] != 'none'){
                        $this->add_render_attribute('link-'.$key,'class', 'elementor-animation-'. $setttings['hover_animation']);
                    }
                    $this->add_link_attributes('link-'.$key, $item['link']);
                    ?>
                    <div <?php echo $this->print_render_attribute_string('panel');?>>
                        <?php   
                            if(!empty($item['image']['id'])){
                                $imgHtml = wp_get_attachment_image($item['image']['id'], $setttings['image_size'], false, $attr);
                                echo $imgHtml;
                            }else{
                                if(!empty($item['image']['url'])){
                                    $imgUrl = esc_url($item['image']['url']);
                                    echo "<img src={$imgUrl} class='eae-panel-img'/>";
                                }
                            }
                        ?>
                        <div class="eae-panel-conent">
                           
                            <div class="eae-panel-content-wrap">
                                <?php
                                    if($item['panel_graphic_type'] != 'none'){ 
                                        Helper::render_icon_html($item, $this, 'panel','eae-ia-panel-icon');
                                    }else{
                                        if($setttings['ia_gbl_graphic_type'] != 'none'){
                                            Helper::render_icon_html($setttings, $this, 'ia_gbl','eae-ia-panel-icon');
                                        }
                                    }
                                    
                                ?>
                                <?php if(!empty($item['title'])){
                                    $title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $item['title_tag'] ), $this->get_render_attribute_string('panel-title'), Helper::eae_wp_kses($item['title'] ));
                                    echo $title_html;
                                }?>
                                <?php if(!empty($item['description'])){
                                    $desc_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $item['description_tag'] ), $this->get_render_attribute_string('panel-desc'), Helper::eae_wp_kses($item['description'] ));
                                    echo $desc_html;
                                }?>
                                <?php if(!empty($item['link_text']) || !empty($item['icon']['value'])){?> 
                                    <a <?php echo $this->get_render_attribute_string('link-'.$key); ?>>
                                        <?php if((!empty($item['icon']['value']))){
                                            Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
                                        }?>
                                        <?php
                                            if(!empty($item['link_text'])){
                                                echo Helper::eae_wp_kses($item['link_text']);
                                            } 
                                        ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
        <?php
    }

    
    
    public function get_active_item($items){
        $active_item = '';
        foreach($items as $key => $item){
            if($item['active_on_load'] == 'yes'){
                $active_item = $key + 1;
            }
        }
        return $active_item;
    }


    public function item_button_style_controls($repeater){

        
        $repeater->add_control(
			'button_style',
			[
				'label' => esc_html__( 'Button', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
               
			]
		);

        $repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eae_panle_link_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
                
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link',
			]
		);

        $repeater->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
                
			]
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'eae_panel_text_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link',
                
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'link_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link',
                
			]
		);

        $repeater->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link',
                
			]
		);

		$repeater->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                			
			]
		);

		$repeater->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link',
                
			]
		);

		$repeater->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                
			]
		);

        $repeater->add_control(
			'button_hover_style',
			[
				'label' => esc_html__( 'Button Hover', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                
			]
		);


		$repeater->add_control(
			'hover_text_color',
			[
				'label' => esc_html__( 'Text Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link:hover, {{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}}  {{CURRENT_ITEM}} .eae-panel-link:hover svg, {{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link:focus svg' => 'fill: {{VALUE}};',
				],
                
			]
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link:hover, {{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link:focus',
                
			]
		);

		$repeater->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link:hover, {{WRAPPER}} {{CURRENT_ITEM}} .eae-panel-link:focus' => 'border-color: {{VALUE}};',
				],
                
			]
		);

    }

    public function item_icon_style_controls($repeater){

        $repeater->add_control(
			'icon_style',
			[
				'label' => esc_html__( 'Icon', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'enable_custom_style' => 'yes'
                ]
			]
		);

        

    }



    public function add_global_icon_controls($repeater){
        $repeater->add_control(
            'icon_heading',
            [
                'label' => __('Icon', 'wts-eae'),
                'type' => Controls_Manager::HEADING
            ]
        );

        $repeater->add_control(
			'graphic_type',
			[
				'label'       => __( 'Icon Type', 'wts-eae' ),
				'label_block' => false,
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'none' => [
						'title' => __( 'None', 'wts-eae' ),
						'icon'  => 'eicon-ban',
					],
					'icon' => [
						'title' => __( 'Icon', 'wts-eae' ),
						'icon'  => 'eicon-star',
					],
					'image' => [
						'title' => __( 'Image', 'wts-eae' ),
						'icon'  => 'eicon-image-bold',
					],
					'animation' => [
						'title' => __( 'Lottie Animation', 'wts-eae' ),
						'icon'  => 'eicon-lottie',
					],
				],
				'default'     => 'none',
			]
		);

        $repeater->add_control(
			'graphic_icon',
			[
				'label'            => __( 'Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => true,
				'fa4compatibility' => 'head_icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'solid',
				],
				'condition'        => [
					'graphic_type'         => 'icon',
				],
			]
		);

        $repeater->add_control(
			'graphic_image',
			[
				'label'       => __( 'Image', 'wts-eae' ),
				'label_block' => true,
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'   => [
					'graphic_type'  => 'image',
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'graphic_image',
				'label'     => __( 'Image Size', 'wts-eae' ),
				'default'   => 'full',
				'exclude'   => [ 'custom' ],
				'condition' => [
					'graphic_type'         => 'image',
				],
			]
		);

        
        $repeater->add_control(
			'source',
			[
				'label' => esc_html__( 'Source', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media_file',
				'options' => [
					'media_file' => esc_html__( 'Media File', 'elementor-pro' ),
					'external_url' => esc_html__( 'External URL', 'elementor-pro' ),
				],
				'frontend_available' => true,
                'condition'   => [
					'graphic_type'         => 'animation',
				],
			]
		);

        $repeater->add_control(
			'lottie_animation_url',
			[
				'label'       => __( 'Animation JSON URL', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => [
                    'source' => 'external_url',
					'graphic_type'         => 'animation',
				],
			]
		);

		
		$repeater->add_control(
			'source_json',
			[
				'label' => esc_html__( 'Upload JSON File', 'elementor-pro' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'frontend_available' => true,
				'condition' => [
					'source' => 'media_file',
                    'graphic_type'         => 'animation',
				],
			]
		);

		$repeater->add_control(
			'lottie_animation_loop',
			[
				'label'        => __( 'Loop', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'graphic_type'         => 'animation',
				],
			]
		);
        
		$repeater->add_control(
			'lottie_animation_reverse',
			[
				'label'        => __( 'Reverse', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'graphic_type' => 'animation',
				],
			]
		);
        
        $repeater->add_control(
			'view',
			[
				'label' => esc_html__( 'View', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'wts-eae' ),
					'stacked' => esc_html__( 'Stacked', 'wts-eae' ),
					'framed' => esc_html__( 'Framed', 'wts-eae' ),
				],
				'default' => 'stacked',
                'condition'    => [
					'graphic_type!' => 'none',
				],
			]
		);

		$repeater->add_control(
			'shape',
			[
				'label' => esc_html__( 'Shape', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => esc_html__( 'Circle', 'wts-eae' ),
					'square' => esc_html__( 'Square', 'wts-eae' ),
				],
				'default' => 'circle',
				'condition' => [
					'view!' => 'default',
                    'graphic_type!' => 'none',
				],
			]
		);

    }

}    