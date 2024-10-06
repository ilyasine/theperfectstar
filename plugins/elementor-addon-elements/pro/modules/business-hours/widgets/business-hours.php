<?php

namespace WTS_EAE\Pro\Modules\BusinessHours\Widgets;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class BusinessHours extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-business-hours';
	}

	public function get_title() {
		return __( 'Business Hours', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-business-hours';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'business hours'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}
    public function ae_get_date_format() {
        $date_format = [
            'F j, Y g:i a'     => gmdate( 'F j, Y g:i a' ),
            'F j, Y'           => gmdate( 'F j, Y' ),
            'F, Y'             => gmdate( 'F, Y' ),
            'g:i a'            => gmdate( 'g:i a' ),
            'g:i:s a'          => gmdate( 'g:i:s a' ),
            'l, F jS, Y'       => gmdate( 'l, F jS, Y' ),
            'M j, Y @ G:i'     => gmdate( 'M j, Y @ G:i' ),
            'Y/m/d \a\t g:i A' => gmdate( 'Y/m/d \a\t g:i A' ),
            'Y/m/d \a\t g:ia'  => gmdate( 'Y/m/d \a\t g:ia' ),
            'Y/m/d g:i:s A'    => gmdate( 'Y/m/d g:i:s A' ),
            'Y/m/d'            => gmdate( 'Y/m/d' ),
            'Y-m-d \a\t g:i A' => gmdate( 'Y-m-d \a\t g:i A' ),
            'Y-m-d \a\t g:ia'  => gmdate( 'Y-m-d \a\t g:ia' ),
            'Y-m-d g:i:s A'    => gmdate( 'Y-m-d g:i:s A' ),
            'Y-m-d'            => gmdate( 'Y-m-d' ),
            'custom'           => __( 'Custom', 'ae-pro' ),
        ];
        return $date_format;
    }
    

    protected function register_controls(){

        $this->start_controls_section(
			'eae_heading_section_content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

        $this->add_control(
			'eae_business_hours_layout',
			[
				'label' => esc_html__( 'Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'predefined' => 'Predefined',
					'custom' => 'Custom',
					
				],
				'default' => 'predefined',
			]
		);
        $this->add_control(
			'eae_business_custom_layout_info',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note: Live Indicator Will Not Work in Custom Layout (Opening Warning Text, Closing Warning Text, Label) ', 'wts-eae' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

        $repeater = new Repeater();

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
            'list_content',
            [
                'label' => esc_html__( 'Day', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Add List ' , 'wts-eae' ),
                'placeholder'=>("Add Some Text"),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'eae_list_content_closed',
            [
                'label'        => __( 'Closed', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
            ]
        );
        $repeater->add_control(     
            'eae_list_content_closed_text',
            [
                'label' => esc_html__( 'Closed Text', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default'=>'Closed',
                'condition'=>[
                    'eae_list_content_closed'=>'yes',
                ]
            ]
        );
        $repeater->add_control(
			'eae_number_of_slots',
			[
				'label'       => __( 'No. of Slots', 'wts-eae' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'min'         => 1,
				'max'         => 5,
                'condition'=>[
                    'eae_list_content_closed!'=>'yes',
                ]
			]
		);
        for ( $i = 1; $i < 6; $i++ ) {

            $repeater->add_control(
                'eae_business_heading'.$i,
                [
                    'label'     => __( 'Slot '.$i, 'wts-eae' ),
                    'type'      => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
						'eae_number_of_slots' => $this->add_condition_value( $i ),
                        'eae_list_content_closed!'=>'yes',
					],
                ]
            );
            $repeater->add_control(
                'eae_business_opening_' . $i,
                [
                    'label' => esc_html__( 'Opening Time', 'wts-eae' ),
                    'type' => Controls_Manager::DATE_TIME,
                    'default'=>'09:00',
                    'enableTime'=>false,
                    'label_block'=>false,
                    'picker_options' => [
                        'noCalendar' =>  true,
                        'dateFormat'=> "H:i",
                        'time_24hr'=> false,
                        
                    ],
                    'condition' => [
                        'eae_number_of_slots' => $this->add_condition_value( $i ),
                        'eae_list_content_closed!'=>'yes',
                    ],
                ]
            );
    
            $repeater->add_control(
                'eae_business_closing_' . $i,
                [
                    'label' => esc_html__( 'Closing Time', 'wts-eae' ),
                    'type' => Controls_Manager::DATE_TIME,
                    'default'=>'21:00',
                    'enableTime'=>false,
                    'label_block'=>false,
                    'picker_options' => [
                        'noCalendar' =>  true,
                        'dateFormat'=> "H:i",
                        'time_24hr'=> false,
                        
                    ],
                    'condition' => [
                        'eae_number_of_slots' => $this->add_condition_value( $i ),
                        'eae_list_content_closed!'=>'yes',
                    ],
                ]
            );
            $repeater->add_control(
                'eae_business_label_' . $i,
                [
                    'label'       => __( 'Label', 'wts-eae' ),
                    'type'        => Controls_Manager::TEXT,
                    'dynamic'     => [
                        'active' => true,
                    ],
                    'condition' => [
						'eae_number_of_slots' => $this->add_condition_value( $i ),
                        'eae_list_content_closed!'=>'yes',
					],
                ]
            );
        }
        
        $repeater->add_control(
                'eae_business_label_' . $i,
                [
                    'label'       => __( 'Label', 'wts-eae' ),
                    'type'        => Controls_Manager::TEXT,
                    'dynamic'     => [
                        'active' => true,
                    ],
                    'condition' => [
						'eae_number_of_slots' => $this->add_condition_value( $i ),
                        'eae_list_content_closed!'=>'yes',
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
            $repeater,[
                'name' => 'pre_icon',
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
            'eae_business_list_day_color_heading',
            [
                'label'     => __( 'Day', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                
            ]
        );

        $repeater->add_control(
                'eae_business_day_text_color',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-business-weekdays' => 'color: {{VALUE}};',
                    ],
                ]
        );

        $repeater->add_control(
            'eae_business_time_heading',
            [
                'label'     => __( 'Time', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
                
            ]
        );
        
        $repeater->add_control(
            'eae_business_time_text_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .eae-business-weekdays-time' => 'color: {{VALUE}};',
                ],
            ]
        );


        $repeater->add_control(
            'eae_business_label_heading',
            [
                'label'     => __( 'Label', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        
        
        $repeater->add_control(
            'eae_business_label_text_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bultr-bh-label' => 'color: {{VALUE}};',
                ],
            ]
        );

       
        $repeater->add_control(
            'eae_business_list_icon_color_heading',
            [
                'label'     => __( 'Icon', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        Helper::repeater_icon_style_controls(
            $repeater,[
                'name' => 'pre_icon',
                'selector' => '.eae-glb-panel-icon',
                'show_hover_controls' => false
            ]   
        );

        $repeater->add_control(
            'eae_business_row_heading',
            [
                'label'     => __( 'Row', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_row',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}} .wts-eae-business-days {{CURRENT_ITEM}}.eae-business-weekdays-wrapper',
            ]
        );

        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();

        $this->add_control(
            'content_list_add',
            [
                'label' => esc_html__( 'Days', 'wts-eae' ),
                'type' => Controls_Manager::REPEATER,
                'item_actions' => [
                    'add' => false,
                    'duplicate' => false,
                    'remove' => false,
                    'sort' => false,
                ],
                
                
                'fields'      =>  $repeater->get_controls() ,
                'default' => [
                    [
                        'list_content' => esc_html__( 'Monday','wts-eae' ),
                        'eae_list_content_closed'=>'',
                    ],
                    [   
                        'list_content' => esc_html__( 'Tuesday','wts-eae' ),
                        'eae_list_content_closed'=>'',
                    ],
                    [   
                        'list_content' => esc_html__( 'Wednesday','wts-eae' ),
                        'eae_list_content_closed'=>'',
                    ],
                    [   
                        'list_content' => esc_html__( 'Thursday','wts-eae' ),
                        'eae_list_content_closed'=>'',
                    ],
                    [   
                        'list_content' => esc_html__( 'Friday','wts-eae' ),
                        'eae_list_content_closed'=>'',
                    ],
                    [   
                        'list_content' => esc_html__( 'Saturday','wts-eae' ),
                        'eae_list_content_closed'=>'',
                    ],
                    [   
                        'list_content' => esc_html__( 'Sunday','wts-eae' ),
                        'eae_list_content_closed'=> 'yes',
                    ],
                
                ],
                'title_field' => '{{{ list_content }}}',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );

        // ----------------------------------------------

        $customRepeater = new Repeater();

        $customRepeater->start_controls_tabs(
			'item_tabs'
		);

        $customRepeater->start_controls_tab(
			'item_content_tab',
			[
				'label' => esc_html__( 'Content', 'wts-eae' ),
               
			]
		);

        $customRepeater->add_control(     
            'list_content_cus',
            [
                'label' => esc_html__( 'Day', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Add List ' , 'wts-eae' ),
                'placeholder'=>("Add Some Text"),
                'label_block' => true,
            ]
        );
        $customRepeater->add_control(
            'eae_list_content_closed_cus',
            [
                'label'        => __( 'Closed', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
            ]
        );
        $customRepeater->add_control(     
            'eae_list_content_closed_text_cus',
            [
                'label' => esc_html__( 'Closed Text', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default'=>'Closed',
                'condition'=>[
                    'eae_list_content_closed_cus'=>'yes',
                ]
            ]
        );
        $customRepeater->add_control(
			'eae_number_of_slots_cus',
			[
				'label'       => __( 'No. of Slots', 'wts-eae' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'min'         => 1,
				'max'         => 5,
                'condition'=>[
                    'eae_list_content_closed_cus!'=>'yes',
                ]
			]
		);
        for ( $i = 1; $i < 6; $i++ ) {

            $customRepeater->add_control(
                'eae_business_heading_cus'.$i,
                [
                    'label'     => __( 'Slot '.$i, 'wts-eae' ),
                    'type'      => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
						'eae_number_of_slots_cus' => $this->add_condition_value( $i ),
                        'eae_list_content_closed_cus!'=>'yes',
					],
                ]
            );
            $customRepeater->add_control(
                'eae_business_opening_cus' . $i,
                [
                    'label' => esc_html__( 'Opening Time', 'wts-eae' ),
                    'type' => Controls_Manager::DATE_TIME,
                    'default'=>'09:00',
                    'enableTime'=>false,
                    'label_block'=>false,
                    'picker_options' => [
                        'noCalendar' =>  true,
                        'dateFormat'=> "H:i",
                        'time_24hr'=> false,
                        
                    ],
                    'condition' => [
                        'eae_number_of_slots_cus' => $this->add_condition_value( $i ),
                        'eae_list_content_closed_cus!'=>'yes',
                    ],
                ]
            );
    
            $customRepeater->add_control(
                'eae_business_closing_cus' . $i,
                [
                    'label' => esc_html__( 'Closing Time', 'wts-eae' ),
                    'type' => Controls_Manager::DATE_TIME,
                    'default'=>'09:00',
                    'enableTime'=>false,
                    'label_block'=>false,
                    'picker_options' => [
                        'noCalendar' =>  true,
                        'dateFormat'=> "H:i",
                        'time_24hr'=> false,
                        
                    ],
                    'condition' => [
                        'eae_number_of_slots_cus' => $this->add_condition_value( $i ),
                        'eae_list_content_closed_cus!'=>'yes',
                    ],
                ]
            );
            $customRepeater->add_control(
                'eae_business_label_cus' . $i,
                [
                    'label'       => __( 'Label', 'wts-eae' ),
                    'type'        => Controls_Manager::TEXT,
                    'dynamic'     => [
                        'active' => true,
                    ],
                    'condition' => [
						'eae_number_of_slots_cus' => $this->add_condition_value( $i ),
                        'eae_list_content_closed_cus!'=>'yes',
					],
                ]
            );
        }
        $customRepeater->end_controls_tab();
        $customRepeater->start_controls_tab(
            'item_icon',
            [
				'label' => esc_html__( 'Icon', 'wts-eae' ),
			]
        );

        Helper::eae_media_controls(
            $customRepeater,[
                'name' => 'eae_cus_icon',
                'label' => 'Icon',
                'icon'			=> true,
                'image'			=> true,
				'lottie'		=> true,
                
            ]
        );
        $customRepeater->end_controls_tab();
        $customRepeater->start_controls_tab(
            'item_style_tab',
            [
				'label' => esc_html__( 'Style', 'wts-eae' ),
			]
        );

        $customRepeater->add_control(
            'eae_business_list_day_color_heading_cus',
            [
                'label'     => __( 'Day', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                
            ]
        );
        $customRepeater->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_business_list_day_color_typo_cus',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .eae-business-weekdays',
            ]
        );
        $customRepeater->add_control(
                'eae_business_day_text_color_cus',
                [
                    'label' => esc_html__( 'Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_ITEM}} .eae-business-weekdays' => 'color: {{VALUE}};',
                    ],
                ]
        );


        $customRepeater->add_control(
            'eae_business_time_heading_cus',
            [
                'label'     => __( 'Time', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
                
            ]
        );
        $customRepeater->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_business_list_time_color_typo_cus',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .bultr-bh-label-wrap',
            ]
        );
        $customRepeater->add_control(
            'eae_business_time_text_color_cus',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bultr-bh-label-wrap' => 'color: {{VALUE}};',
                ],
            ]
        );


        $customRepeater->add_control(
            'eae_business_label_heading_cus',
            [
                'label'     => __( 'Label', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $customRepeater->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_business_list_label_color_typo_cus',
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .bultr-bh-label',
            ]
        );
        
        $customRepeater->add_control(
            'eae_business_label_text_color_cus',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .bultr-bh-label' => 'color: {{VALUE}};',
                ],
            ]
        );
       

        $customRepeater->add_control(
            'eae_business_icon_heading_cus',
            [
                'label'     => __( 'Icon', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        Helper::repeater_icon_style_controls(
            $customRepeater,[
                'name' => 'eae_cus_icon',
                'selector'      => '.eae-glb-panel-icon',
                'show_hover_controls' => false
            ]   
        );

        $customRepeater->add_control(
            'eae_business_row_heading_cus',
            [
                'label'     => __( 'Row', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $customRepeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_row_cus',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.eae-business-weekdays-wrapper',
            ]
        );
        

        $customRepeater->end_controls_tab();
        $customRepeater->end_controls_tabs();
    
        $this->add_control(
            'content_list_custom',
            [
                'label' => esc_html__( 'Days', 'wts-eae' ),
                'type' => Controls_Manager::REPEATER,
                'fields'      =>  $customRepeater->get_controls() ,
                'default' => [
                    [
                        'list_content_cus' => esc_html__( 'Monday - Friday','wts-eae' ),
                    ],
                    [   
                        'list_content_cus' => esc_html__( 'Saturday','wts-eae' ),
                    ],
                    [   
                        'list_content_cus' => esc_html__( 'Sunday','wts-eae' ),
                        'eae_list_content_closed_cus' => 'yes'
                    ],
                ],
                'title_field' => '{{{ list_content_cus }}}',
                'condition'=>[
                    'eae_business_hours_layout'=>'custom',
                ]
            ]
        );

        Helper::eae_media_controls(
            $this,[
                'name' => 'pre_icon_global',
                'label' => 'Icon',
                'icon'			=> true,
                'image'			=> true,
				'lottie'		=> true,
                'defaults'      => [
                    'graphic_type_default' => 'icon',
                    'graphic_icon_default' => [
                        'value' => 'far fa-clock',
                        'library' => 'fa-regular'
                    ],
                ]
            ]
        );

        $this->add_control(
            'eae_list_content_show_current_only',
            [
                'label'        => __( 'Show Current Day Only', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'separator' => 'before',
                'return_value' => 'yes',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );
        $this->add_control(
            'eae_list_content_highlight_current_only',
            [
                'label'        => __( 'HightLight Current Day', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );
        $this->add_control(
            'eae_list_content_24hour_format',
            [
                'label'        => __( '24 Hour Format', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
            ]
        );

        $this->add_control(     
            'eae_list_content_separator',
            [
                'label' => esc_html__( 'Separator', 'wts-eae' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => false,
                'default'=>'-'
            ]
        );

        $this->add_control(
			'eae_list_content_day_format',
			[
				'label' => esc_html__( 'Day Format', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'long' => 'Long',
					'short' => 'Short',
				],
				'default' => 'long',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                ]
			]
		);
        $this->add_control(
            'eae_list_content_show_indicator',
            [
                'label'        => __( 'Show Business Indicator', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
                'default'=>'yes',
                
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'eae_heading_section_indicators',
			[
				'label' => __( 'Indicators', 'wts-eae' ),
                'condition'=>[
                    'eae_list_content_show_indicator'=>'yes',
                ]
			]
		);

        $this->add_control(
            'eae_heading_indicators_heading',
            [
                'label'       => __( 'Title', 'wts-eae' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'default'=>'Business Hours'
            ]
        );

        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'eae_heading_indicators_heading_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
            ]
        );
       

        $this->add_control(
            'eae_heading_indicators_date',
            [
                'label'        => __( 'Date', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
            ]
        );
        

        $date_format = [
            'F j, Y'           => gmdate( 'F j, Y' ),
            'F, Y'             => gmdate( 'F, Y' ),
            'l, F jS, Y'       => gmdate( 'l, F jS, Y' ),
            'Y/m/d'            => gmdate( 'Y/m/d' ),
            'Y-m-d'            => gmdate( 'Y-m-d' ),
            'custom'           => __( 'Custom', 'ae-pro' ),
        ];


		$this->add_control(
			'eae_heading_indicators_date_format',
			[
				'label'       => __( 'Date format', 'ae-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => $date_format,
				'default'     => 'F j, Y',
				'description' => '<a href="https://wordpress.org/documentation/article/customize-date-and-time-format/" target="_blank"> Click here</a> for documentation on date and time formatting.',
                'condition'=>[
                    'eae_heading_indicators_date'=>'yes'
                    
                ]
			]
		);
        $this->add_control(
			'eae_heading_indicators_date_custom_format',
			[
				'label'       => __( 'Date Format', 'ae-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Date Format', 'ae-pro' ),
				'default'     => 'y:m:d',
                
				'condition'   => [
					'eae_heading_indicators_date_format' => 'custom',
				],
			]
		);

        $this->add_control(
            'eae_heading_indicators_Time',
            [
                'label'        => __( 'Time', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
            ]
        );

        
        $this->add_control(
			'eae_heading_indicators_time_format',
			[
				'label' => esc_html__( 'Format', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'12_hours' => '12 Hours', 
					'24_hours' => '24 Hours', 
				],
				'default' => '12_hours',
                'condition'=>[
                    'eae_heading_indicators_Time'=>'yes'
                ]
			]
		);

        


        $this->add_control(
            'eae_heading_indicators_opening_warning_text',
            [
                'label'        => __( 'Opening Warning Text', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'separator' => 'before',
                'return_value' => 'yes',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );

        $this->add_control(
            'eae_heading_indicators_opening_warning_text_minutes',
            [
                'label'       => __( 'Minutes', 'wts-eae' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'description' => 'Before how many minutes you want to display Opening Warning?',
                'condition'=>[
                    'eae_heading_indicators_opening_warning_text'=>'yes',
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );
        $this->add_control(
            'eae_heading_indicators_opening_warning_text_enter',
            [
                'label'       => __( 'Opening Warning Text', 'wts-eae' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block'	 => true,
                'condition'=>[
                    'eae_heading_indicators_opening_warning_text'=>'yes',
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );

        $this->add_control(
            'eae_heading_indicators_closing_warning_text',
            [
                'label'        => __( 'Closing Warning Text', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'separator' => 'before',
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );

        $this->add_control(
            'eae_heading_indicators_closing_warning_text_minutes',
            [
                'label'       => __( 'Minutes', 'wts-eae' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'description' => 'Before how many minutes you want to display Closing Warning?',
                'condition'=>[
                    'eae_heading_indicators_closing_warning_text'=>'yes',
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );
        
        $this->add_control(
            'eae_heading_indicators_closing_warning_text_enter',
            [
                'label'       => __( 'Closing Warning Text', 'wts-eae' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block'	 => true,
                'condition'=>[
                    'eae_heading_indicators_closing_warning_text'=>'yes',
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );

        $this->add_control(
            'eae_heading_indicators_button_label',
            [
                'label'        => __( 'Label', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                ],
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eae_heading_indicators_label_opening_text',
            [
                'label'       => __( 'Opening Text', 'wts-eae' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block'	 => true,
                'condition'=>[
                    'eae_heading_indicators_button_label'=>'yes',
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );
        $this->add_control(
            'eae_heading_indicators_label_closing_text',
            [
                'label'       => __( 'Closing Text', 'wts-eae' ),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active' => true,
                ],
                'label_block'	 => true,
                'condition'=>[
                    'eae_heading_indicators_button_label'=>'yes',
                    'eae_business_hours_layout'=>'predefined',
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__( 'Content', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Day Style

        $this->add_control(
            'eae_content_day_style_heading',
            [
                'label'     => __( 'Day ', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eae_content_row_day_style_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-business-weekdays' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_row_day_style_typography',
                'selector' => '{{WRAPPER}} .eae-business-weekdays',
            ]
        );



        // Time Style

        $this->add_control(
            'eae_content_time_style_heading',
            [
                'label'     => __( 'Time ', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'eae_content_row_time_style_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bultr-bh-label-wrap' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_row_time_style_typography',
                'selector' => '{{WRAPPER}} .bultr-bh-label-wrap , {{WRAPPER}} .eae-business-weekdays-time',
            ]
        );


        // Title Label

        $this->add_control(
            'eae_content_row_label_style_heading',
            [
                'label'     => __( 'Slot Label', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_row_label_style_typography',
                'selector' => '{{WRAPPER}} .bultr-bh-label',
            ]
        );

        
        $this->add_control(
            'eae_content_row_label_style_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bultr-bh-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_row_label',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .bultr-bh-label',
            ]
        );
       
        $this->add_responsive_control(
            'eae_content_row_label_style_gap',
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
                    '{{WRAPPER}} .bultr-bh-label-wrap' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'eae_content_row_label_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .bultr-bh-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


        $this->add_responsive_control(
			'eae_content_row_label_style_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .bultr-bh-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

        $this->add_control(
            'eae_content_row_style_heading_global_icon',
            [
                'label'     => __( 'Icon', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        // add tabs controls elementor
        $this->start_controls_tabs(
            'icon_style_tabs'
        );
        
        $this->start_controls_tab(
            'row_icon_normal_tab',
            [
                'label' => esc_html__( 'Open', 'wts-eae' ),
            ]
        );

        Helper::global_icon_style_controls($this,[
			'name' => 'pre_icon_global',
			'selector' => '.eae-glb-panel-icon',
            'show_hover_controls' => false
            // 'is_active_tab' => [
            //     'label'		=> 'Closed',
            //     'selector'  => '.eae-day-close'
            // ]
	    ]);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'row_icon_closed_tab',
            [
                'label' => esc_html__( 'Closed', 'wts-eae' ),
            ]
        );

        //Add Here
        $wrapper_selector = '{{WRAPPER}} .eae-business-weekdays-wrapper.eae-day-close .eae-glb-panel-icon';
        $this->add_control(
            'closed_row_icon_primary_color',
            [
                'label' => esc_html__( 'Primary Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff0000',
                'selectors' => [
                    $wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
                    $wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    $wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'closed_row_icon_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
				],
			]
		);

        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        
       
        $this->add_control(
            'eae_content_Box_style',
            [
                'label'     => __( 'Box', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_content',
                'types' => [ 'classic', 'gradient','image' ],
                'selector' => '{{WRAPPER}} .wts-eae-business-days',
            ]
        );
       

        $this->add_responsive_control(
			'eae_content_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wta-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .wts-eae-business-days' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_content',
				'selector' => '{{WRAPPER}}  .wts-eae-business-days',
			]
		);
        $this->add_responsive_control(
			'border_radius_content',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .wts-eae-business-days' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow_content',
				'selector' => '{{WRAPPER}}  .wts-eae-business-days',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'eae_content_row_style_section',
            [
                'label' => esc_html__( 'Row Style', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'eae_content_row_style_heading',
            [
                'label'     => __( 'Row ', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_row_style_typography',
                'selector' => '{{WRAPPER}} .eae-business-weekdays-wrapper',
            ]
        );

        $this->add_control(
            'eae_content_row_style_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-business-weekdays-wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );

        // add background type control
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'eae_content_row_style_background_color',
                'types' => [ 'classic', 'gradient','image' ],
                'selector' => '{{WRAPPER}}  .eae-business-weekdays-wrapper',
            ]
        );

        // Add Alternate Heading
        $this->add_control(
            'eae_content_row_style_alternate_heading',
            [
                'label'     => __( 'Alternate', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'eae_content_row_style_alternate_color',
            [
                'label' => esc_html__( 'Alternate Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wts-eae-business-days .eae-business-weekdays-wrapper:nth-child(even) .eae-business-weekdays' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wts-eae-business-days .eae-business-weekdays-wrapper:nth-child(even) .eae-business-weekdays-time' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'eae_content_row_style_alternate_background_color',
                'types' => [ 'classic', 'gradient','image' ],
                'selector' => '{{WRAPPER}} .eae-business-weekdays-wrapper:nth-child(even)',
                'separator' => 'after'
            ]
        );
         
       
        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'eae_content_row_style_text_shadow',
				'selector' => '{{WRAPPER}} .eae-business-weekdays-wrapper',
                
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eae_content_row_style_box_shadow_wrapper',
				'selector' => '{{WRAPPER}}  .eae-business-weekdays-wrapper',
				
			]
		);

        
        $this->add_responsive_control(
            'eae_content_row_style_gap',
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
                    '{{WRAPPER}} .wts-eae-business-days' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eae_content_row_style_border_wrapper',
				'selector' => '{{WRAPPER}}  .eae-business-weekdays-wrapper',
			]
		);

        $this->add_responsive_control(
			'eae_content_row_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-business-weekdays-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'eae_content_row_style_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
                'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}}  .eae-business-weekdays-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

        // Closed Row Style
    
        $this->add_control(
            'eae_content_closed_row_style_heading',
            [
                'label'     => __( 'Closed ', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_wrapper_closed_row',
                'types' => [ 'classic', 'gradient', 'image' ],
            
                'selector' => '{{WRAPPER}} .eae-business-weekdays-wrapper.eae-day-close',
            ]
        );

        $this->add_control(
            'eae_content_closed_row_date_style_color',
            [
                'label' => esc_html__( 'Day', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-business-weekdays-wrapper.eae-day-close .eae-business-weekdays' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'eae_content_closed_row_time_style_color',
            [
                'label' => esc_html__( 'Text', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-business-weekdays-wrapper.eae-day-close .eae-business-weekdays-time' => 'color: {{VALUE}};',
                ],
            ]
        );
        

        $this->add_control(
            'eae_content_highlighted_row_style_heading',
            [
                'label'     => __( 'Highlighted Day', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'eae_list_content_highlight_current_only'=>'yes',
                ]
                
            ]
        );

        $this->add_control(
            'eae_content_highlighted_row_style_heading_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-business-weekdays-wrapper.highlightDay .eae-business-weekdays' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .eae-business-weekdays-wrapper.highlightDay .eae-business-weekdays-time' => 'color: {{VALUE}} !important;',
                ],
                'condition'=>[
                    'eae_list_content_highlight_current_only'=>'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_content_highlighted_row_style_heading_typo',
                'selector' => '{{WRAPPER}} .eae-business-weekdays-wrapper.highlightDay',
                'condition'=>[
                    'eae_list_content_highlight_current_only'=>'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'eae_content_highlighted_row_style_heading_background',
                'types' => [ 'classic', 'gradient', 'image' ],
                'selector' => '{{WRAPPER}} .wts-eae-business-days .eae-business-weekdays-wrapper.highlightDay',
                'condition'=>[
                    'eae_list_content_highlight_current_only'=>'yes',
                ]
            ]
        );

        $wrap_selector = '{{WRAPPER}} .wts-eae-business-days .eae-business-weekdays-wrapper.highlightDay .eae-glb-panel-icon';
        $this->add_control(
            'highlight_row_icon_primary_color',
            [
                'label' => esc_html__( 'Primary Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    $wrap_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
                    $wrap_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrap_selector.'.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    $wrap_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrap_selector.'.eae-graphic-view-default svg' => 'fill: {{VALUE}};',
                ],
                'condition'=>[
                    'eae_list_content_highlight_current_only'=>'yes',
                ]
            ]
        );

        $this->add_control(
			'hightlight_row_icon_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$wrap_selector .'.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
					$wrap_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
					$wrap_selector .'.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
				],
                'condition'=>[
                    'eae_list_content_highlight_current_only'=>'yes',
                ]
			]
		);


        $this->end_controls_section();

        $this->start_controls_section(
            'section_indicator_style',
            [
                'label' => esc_html__( 'Indicators', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'eae_list_content_show_indicator'=>'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_wrapper',
                'types' => [ 'classic', 'gradient', 'image' ],
            
                'selector' => '{{WRAPPER}} .wts-eae-business-indicators',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_wrapper',
				'selector' => '{{WRAPPER}}  .wts-eae-business-indicators',
			]
		);
        $this->add_responsive_control(
			'border_radius_wrapper',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .wts-eae-business-indicators' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow_wrapper',
				'selector' => '{{WRAPPER}}  .wts-eae-business-indicators',
				
			]
		);

        $this->add_responsive_control(
			'eae_indicators_wrapper_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wta-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
					'top'    => 10,
					'right'  => 10,
					'bottom' => 10,
					'left'   => 10,
					'unit'   => 'px',
				],
				'selectors' => [
					'{{WRAPPER}}  .wts-eae-business-indicators' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'eae_content_top_row_style_gap',
            [
                'label' => esc_html__( 'Spacing', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wta-eae-business-heading-wrapper ' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        

        // Title Style

        $this->add_control(
            'eae_indicator_label_style_heading',
            [
                'label'     => __( 'Title', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_indicator_label_style_typography',
                'selector' => '{{WRAPPER}} .eae-indicator-title',
            ]
        );
        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .eae-indicator-title',
			]
		);
        $this->add_control(
            'eae_indicator_label_style_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-indicator-title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-indicator-title',
            ]
        );
        
        

        
        $this->add_responsive_control(
			'eae_indicator_label_style_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
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
					'{{WRAPPER}} .eae-indicator-title' => 'justify-content: {{VALUE}}',										
				],
			]
		);
		$this->add_responsive_control(
			'eae_indicator_label_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
                // 'separator'=>'after',
				'selectors' => [
					'{{WRAPPER}}  .eae-indicator-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


        $this->add_responsive_control(
            'eae_heading_indicators_title_icon_gap',
            [
                'label' => esc_html__( 'Icon gap', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-indicator-title' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'eae_heading_indicators_title_icon',
            [
                'label'     => __( 'Icon', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                
            ]
        );
        Helper::global_icon_style_controls(
            $this,
            [
                'name' => 'eae_heading_indicators_heading_icon',
                'selector' => '.eae-tile-icon',	
                'is_repeater' => false,
                'show_hover_controls' => false
            ]
        );

        //  Date Style

        $this->add_control(
            'eae_indicator_date_style_heading',
            [
                'label'     => __( 'Date', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'eae_heading_indicators_date'=>'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_indicator_date_style_typography',
                'selector' => '{{WRAPPER}} .eae-indicator-date',
                'condition'=>[
                    'eae_heading_indicators_date'=>'yes'
                ]
            ]
        );
        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow_date',
				'selector' => '{{WRAPPER}} .eae-indicator-date',
                'condition'=>[
                    'eae_heading_indicators_date'=>'yes'
                ]
			]
		);
        
        $this->add_control(
            'eae_indicator_date_style_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-indicator-date' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'eae_heading_indicators_date'=>'yes'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_date',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-indicator-date',
                'condition' => [
                    'eae_heading_indicators_date'=>'yes'
                ],
            ]
        );
        
        

        $this->add_responsive_control(
			'eae_indicator_date_style_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
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
					'{{WRAPPER}} .eae-indicator-date' => 'justify-content: {{VALUE}}',										
				],
                'condition' => [
                    'eae_heading_indicators_date'=>'yes'
                ],
			]
		);
		$this->add_responsive_control(
			'eae_indicator_date_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
                'separator'=>'after',
				'selectors' => [
					'{{WRAPPER}}  .eae-indicator-date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'eae_heading_indicators_date'=>'yes'
                ],
			]
		);
        // Time Style

        $this->add_control(
            'eae_indicator_time_style_heading',
            [
                'label'     => __( 'Time', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'eae_heading_indicators_Time'=>'yes'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_indicator_time_style_typography',
                'selector' => '{{WRAPPER}} .eae-indicator-time',
                'condition' => [
                    'eae_heading_indicators_Time'=>'yes'
                ],
            ]
        );
        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow_time',
				'selector' => '{{WRAPPER}} .eae-indicator-time',
                'condition' => [
                    'eae_heading_indicators_Time'=>'yes'
                ],
			]
		);
        $this->add_control(
            'eae_indicator_time_style_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-indicator-time' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'eae_heading_indicators_Time'=>'yes'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_time',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-indicator-time',
                'condition' => [
                    'eae_heading_indicators_Time'=>'yes'
                ],
            ]
        );
        
        
        

        $this->add_responsive_control(
			'eae_indicator_time_style_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
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
					'{{WRAPPER}} .eae-indicator-time' => 'justify-content: {{VALUE}}',										
				],
                'condition' => [
                    'eae_heading_indicators_Time'=>'yes'
                ],
			]
		);
		$this->add_responsive_control(
			'eae_indicator_time_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
                'separator'=>'after',
				'selectors' => [
					'{{WRAPPER}}  .eae-indicator-time' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'eae_heading_indicators_Time'=>'yes'
                ],
			]
		);

        // Label Style

        $this->add_control(
            'eae_indicator_style_heading_label',
            [
                'label'     => __( 'Label', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );

        $this->add_control(
			'eae_indicator_style_heading_label_position',
			[
				'label' => esc_html__( 'Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'fixed' => 'Fixed', 
					'relative' => 'Relative', 
				],
				'default' => 'fixed',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
			]
		);

        $this->add_responsive_control(
            'eae_indicator_style_heading_label_position_top',
            [
                'label' => esc_html__( 'Top', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
						'step'=>1,
                    ],
                ],
				'default' => [
                    'unit' => '%',
                    'size' => 22,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bultr-labelss' => 'top: {{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
                    'eae_indicator_style_heading_label_position'=>'fixed',
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );
		$this->add_responsive_control(
            'eae_indicator_style_heading_label_position_left',
            [
                'label' => esc_html__( 'Right', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
						'step'=>1,
                    ],
                ],
				'default' => [
                    'unit' => '%',
                    'size' =>3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bultr-labelss' => 'right: {{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
                    'eae_indicator_style_heading_label_position'=>'fixed',
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_indicator_style_typography_label',
                'selector' => '{{WRAPPER}} .bultr-labelss',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );
        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow__label',
				'selector' => '{{WRAPPER}} .bultr-labelss',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
			]
		);
        
        $this->start_controls_tabs( 'section_indicator_style_tab_label', [
			'condition' => [
				'eae_business_hours_layout' => 'predefined',
                'eae_heading_indicators_button_label'=>'yes',
			],
		] );

        $this->start_controls_tab(
            'eae_indicator_style_normal_tab_label__yes',
            [
                'label' => esc_html__( 'Normal', 'wts-eae' ),
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );
        $this->add_control(
            'eae_indicator_style_text_color_label',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bultr-labelss' => 'color: {{VALUE}};',
                ],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background__label',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .bultr-labelss',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'eae_indicator_style_tab_closed_label__yes',
            [
                'label' => esc_html__( 'Closed', 'wts-eae' ),
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );
        $this->add_control(
            'eae_indicator_style_color_closed_label',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bultr-labelss.bultr-lbl-close' => 'color: {{VALUE}};',
                ],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_closed__label',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .bultr-labelss.bultr-lbl-close',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
			'eae_indicator_style_alignment_label',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
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
					'{{WRAPPER}} .bultr-labelss' => 'justify-content: {{VALUE}}',										
				],
                'condition'=>[
                    'eae_indicator_style_heading_label_position'=>'relative',
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
               
			]
		);
		$this->add_responsive_control(
			'eae_indicator_style_padding_label',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .bultr-labelss' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}}  .bultr-labelss',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
				
			]
		);
        $this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .bultr-labelss' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}}  .bultr-labelss',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_button_label'=>'yes',
                ]
			]
		);

        //  Opening warning Text

        $this->add_control(
            'eae_indicator_style_opening_warning_text',
            [
                'label'     => __( 'Opening Warning Text', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_indicator_style_opening_warning_text_typography',
                'selector' => '{{WRAPPER}} .eae-bh-bi-open-wmsg',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ]
            ]
        );


        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow_opening_warning_text',
				'selector' => '{{WRAPPER}} .eae-bh-bi-open-wmsg',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ]
			]
		);
        // $this->start_controls_tabs(
        //     'section_indicator_opening_warning_text_style_tab',
        // );

        
        $this->add_control(
            'eae_indicator_opening_warning_text_style_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-bh-bi-open-wmsg' => 'color: {{VALUE}};',
                ],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_opening_warning_text',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-bh-bi-open-wmsg',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ]
            ]
        );
        

        $this->add_responsive_control(
			'eae_indicator_opening_warning_text_style_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
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
					'{{WRAPPER}} .eae-bh-bi-open-wmsg' => 'justify-content: {{VALUE}}',										
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ]
			]
		);
		$this->add_responsive_control(
			'eae_indicator_opening_warning_text_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
                'separator'=>'after',
				'selectors' => [
					'{{WRAPPER}}  .eae-bh-bi-open-wmsg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ]
			]
		);
        //  Closing warning Text

        $this->add_control(
            'eae_indicator_style_closing_warning_text',
            [
                'label'     => __( 'Closing Warning Text', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'eae_indicator_style_closing_warning_text_typography',
                'selector' => '{{WRAPPER}} .eae-bh-bi-close-wmsg',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow_closing_warning_text',
				'selector' => '{{WRAPPER}} .eae-bh-bi-close-wmsg',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ]
			]
		);
       
        $this->add_control(
            'eae_indicator_closing_warning_text_style_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-bh-bi-close-wmsg' => 'color: {{VALUE}};',
                ],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_closing_warning_text',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-bh-bi-close-wmsg',
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ]
            ]
        );

        $this->add_responsive_control(
			'eae_indicator_closing_warning_text_style_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
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
					'{{WRAPPER}} .eae-bh-bi-close-wmsg' => 'justify-content: {{VALUE}}',										
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ]
			]
		);
		$this->add_responsive_control(
			'eae_indicator_closing_warning_text_style_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
                'separator'=>'after',
				'selectors' => [
					'{{WRAPPER}}  .eae-bh-bi-close-wmsg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ]
			]
		);

        //  Order 

        $this->add_control(
            'eae_indicator_style_order',
            [
                'label'     => __( 'Order', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'eae_heading_indicators_heading',
							'operator' => '=',
							'value'    => 'yes',
						],
						[
							'name'     => 'eae_heading_indicators_date',
							'operator' => '=',
							'value'    => 'yes',
						],
						[
							'name'     => 'eae_heading_indicators_Time',
							'operator' => '=',
							'value'    => 'yes',
						],
                        [
							'name'     => 'eae_heading_indicators_heading',
							'operator' => '!==',
							'value'    => '',
						],
                       
					],
				],
            ]
        );
       
        $this->add_responsive_control(
			'eae_indicator_style_order_title',
			[
				'label'			 => esc_html__( 'Title ', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'selectors' => [
					'{{WRAPPER}} .eae-indicator-title' => 'order:{{VALUE}}'
				],
                'condition'=>[
                    'eae_heading_indicators_heading!'=> ''
                ],
			]
		);

		$this->add_responsive_control(
			'eae_indicator_style_order_date',
			[
				'label'			 => esc_html__( 'Date', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,				
				'label_block'	 => false,
				'selectors' => [
					'{{WRAPPER}} .eae-indicator-date' => 'order:{{VALUE}}'
				],
                'condition'=>[
                    'eae_heading_indicators_date'=>'yes'
                ],
			]
		);
		$this->add_responsive_control(
			'eae_indicator_style_order_time',
			[
				'label'			 => esc_html__( 'Time', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'selectors' => [
					'{{WRAPPER}} .eae-indicator-time' => 'order:{{VALUE}};',
				],
                'condition'=>[
                    'eae_heading_indicators_Time'=>'yes'
                ],
			]
		);
		$this->add_responsive_control(
			'eae_indicator_style_order_opening_text',
			[
				'label'			 => esc_html__( 'Opening Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'selectors' => [
					'{{WRAPPER}} .eae-bh-bi-open-wmsg' => 'order:{{VALUE}};',
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_opening_warning_text'=>'yes'
                ],
			]
		);
		$this->add_responsive_control(
			'eae_indicator_style_order_closing_text',
			[
				'label'			 => esc_html__( 'Closing Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'selectors' => [
					'{{WRAPPER}} .eae-bh-bi-close-wmsg' => 'order:{{VALUE}};',
				],
                'condition'=>[
                    'eae_business_hours_layout'=>'predefined',
                    'eae_heading_indicators_closing_warning_text'=>'yes'
                ],
			]
		);
		$this->add_responsive_control(
			'eae_indicator_style_order_label_text',
			[
				'label'			 => esc_html__( 'Label', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'selectors' => [
					'{{WRAPPER}} .bultr-labelss' => 'order:{{VALUE}};',
				],
                'condition'=>[
                    'eae_business_hours_layout' => 'predefined',
                    'eae_indicator_style_heading_label_position' => 'relative'
                ],
			]
		);


        $this->end_controls_section();

        $this->start_controls_section(
            'section_box_style',
            [
                'label' => esc_html__( 'Box', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_box',
                'types' => [ 'classic', 'gradient','image' ],
                'selector' => '{{WRAPPER}} .wta-eae-business-heading-wrapper',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_box',
				'selector' => '{{WRAPPER}}  .wta-eae-business-heading-wrapper',
			]
		);
        $this->add_responsive_control(
			'border_radius_box',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .wta-eae-business-heading-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow_box',
				'selector' => '{{WRAPPER}}  .wta-eae-business-heading-wrapper',
				
			]
		);

        $this->add_control(
            'eae_business_box_overlay_color',
            [
                'label' => esc_html__( 'Overlay Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wta-eae-business-heading-wrapper::before' => 'background: {{VALUE}};',
                ],
            ]
        );

        // Add Padding Control
        $this->add_responsive_control(
            'eae_business_box_padding',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .wta-eae-business-heading-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
    public function bu_get_timeZone(){
        $offset  = (float) get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - $hours );
        $sign      = ( $offset < 0 ) ? '-' : '+';
        $abs_hour  = abs( $hours );
        $abs_mins  = abs( $minutes * 60 );
        $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
        return $tz_offset;
    }

    public function render(){
        $settings = $this->get_settings_for_display();
        $root_classes='wta-eae-business-heading-wrapper';
        $timezone = $this->bu_get_timeZone();
        $timeCurrent = date_i18n('h:i A');
        $options =[];
        if($settings['eae_heading_indicators_time_format']=='24_hours'){
            $timeFormat = "false";
        }
        else{
            $timeFormat = "true";
        }

        $options=[
            'businessIndicator' => isset($settings['eae_list_content_show_indicator']) && $settings['eae_list_content_show_indicator'] ? $settings['eae_list_content_show_indicator']: false ,
            'openWrnMsg'        => isset($settings['eae_heading_indicators_opening_warning_text']) && $settings['eae_heading_indicators_opening_warning_text'] ? $settings['eae_heading_indicators_opening_warning_text'] : false,
            'openMints'         => !empty($settings['eae_heading_indicators_opening_warning_text_minutes']) ? $settings['eae_heading_indicators_opening_warning_text_minutes'] : 5,
            'openWrnMsgTxt'     => !empty($settings['eae_heading_indicators_opening_warning_text_enter'])? $settings['eae_heading_indicators_opening_warning_text_enter']: __("we are opening",'wpv-bu'),
            'closeWrnMsg'       => isset($settings['eae_heading_indicators_closing_warning_text']) && $settings['eae_heading_indicators_closing_warning_text']? $settings['eae_heading_indicators_closing_warning_text'] : false,
            'closeMints'        => !empty($settings['eae_heading_indicators_closing_warning_text_minutes']) ? $settings['eae_heading_indicators_closing_warning_text_minutes'] : 5,
            'closeWrnMsgText'   => !empty($settings['eae_heading_indicators_closing_warning_text_enter'])? $settings['eae_heading_indicators_closing_warning_text_enter']: __("we are closing",'wpv-bu'),
            'indctLabel'         => isset($settings['eae_heading_indicators_button_label']) && $settings['eae_heading_indicators_button_label']? $settings['eae_heading_indicators_button_label'] : false,
            'openLableTxt'      => !empty($settings['eae_heading_indicators_label_opening_text'])? $settings['eae_heading_indicators_label_opening_text']: __("Open",'wpv-bu'),
            'closeLabelTxt'     => !empty($settings['eae_heading_indicators_label_closing_text'])? $settings['eae_heading_indicators_label_closing_text']: __("Closed",'wpv-bu'),
        ];

        $this->add_render_attribute('_root','class',$root_classes);
        $this->add_render_attribute('_root','data-timezone',$timezone);
        $this->add_render_attribute('_root','data-time',$timeCurrent);
        $this->add_render_attribute('_root','data-format',$timeFormat);
        $this->add_render_attribute('_root', 'data-settings', wp_json_encode($options) );
        
        ?>
        
        <div <?php echo $this->get_render_attribute_string( '_root' ); ?> >
            <div class="wts-eae-business-indicators">
                <?php echo self::render_indicator() ?>
            </div>
            <div class="wts-eae-business-days">
            <?php 
                if($settings['eae_business_hours_layout']=='predefined'){
                    foreach($settings['content_list_add'] as $index => $item){
                        self::render_predefined_weekdays($item);
                    }
                }
                else{
                    foreach($settings['content_list_custom'] as $index => $item){
                        self::render_custom_weekdays($item);
                    }
                }  
            ?>
            </div>
        </div>
        
        <?php
    }
    public function render_indicator(){

        $settings = $this->get_settings_for_display();
        
        //  Title
        if(!empty($settings['eae_heading_indicators_heading'])){
            echo '<span class="eae-indicator-title">';
                Helper::render_icon_html($settings,$this,'eae_heading_indicators_heading_icon','eae-tile-icon');
                echo  Helper::eae_wp_kses($settings['eae_heading_indicators_heading']);
            echo '</span>';
        }

        //  Date 
        if($settings['eae_heading_indicators_date']=='yes'){
            echo "<span class ='eae-indicator-date'>";
            // eae_heading_indicators_date_custom_format
            if($settings['eae_heading_indicators_date_format']=='custom'){
                echo Helper::eae_wp_kses(date_i18n($settings['eae_heading_indicators_date_custom_format']));  
            }
            else{
                echo Helper::eae_wp_kses(date_i18n($settings['eae_heading_indicators_date_format']));
            }
            echo "</span>";
          
        }
        
        //time display
        if($settings['eae_heading_indicators_Time']=='yes'){
            echo "<span class ='eae-indicator-time' >";
            if($settings['eae_heading_indicators_time_format'] == '12_hours'){ 
                echo date_i18n('h:i:s A'); 
            }
            else{
                echo date_i18n('G:i:s');
            }
            echo "</span>";
        }


        if($settings['eae_business_hours_layout']== 'predefined'){
          
            foreach($settings['content_list_add'] as $index => $item){

                    $day = date('D');
                    $currentDay = ucfirst(substr($item['list_content'],0,3));
                    if($currentDay == $day){

                        $slots = $item['eae_number_of_slots'];
                        $count = 1;
                        for($i = 1; $i<=$slots; $i++){
                            $openingTime= strtotime(date("h:i A", strtotime($item['eae_business_opening_'.$i])));
                            $currentTime = strtotime(date_i18n("H:i:s"));
                            $closingTime = strtotime(date("h:i A", strtotime($item['eae_business_closing_'.$i])));
                          
                            
                            
                            if($openingTime > $currentTime ){
                                $min = ceil(($openingTime - $currentTime) / 60);
                                $openMints = !empty($settings['eae_heading_indicators_opening_warning_text_minutes']) ? $settings['eae_heading_indicators_opening_warning_text_minutes'] : 5;
                                if($min <= $openMints){
                                    if($settings['eae_heading_indicators_opening_warning_text']== 'yes'){
                                        $openingTxt = !empty($settings['eae_heading_indicators_opening_warning_text_enter']) ? $settings['eae_heading_indicators_opening_warning_text_enter'] : __("We are opening in ",'wta-eae');
                                        echo "<span class='eae-bh-bi-open-wmsg'>". Helper::eae_wp_kses($openingTxt) ." ". abs($min)." Minutes</span>"; 
                                    }
                                }
                            }
                            if($currentTime > $openingTime || $currentTime < $closingTime){
                                if($settings['eae_heading_indicators_closing_warning_text']== 'yes'){
                                    $nStart = strtotime(date("h:i A", strtotime($item['eae_business_closing_'.$i])));
                                    $nEnd = strtotime(date_i18n("H:i"));
                                    $mins = ceil(($nStart - $nEnd)/60);
                                    $closeMint = !empty($settings['eae_heading_indicators_closing_warning_text_minutes']) ? $settings['eae_heading_indicators_closing_warning_text_minutes'] : 5;
                                    if($mins <= $closeMint && $mins>0){
                                        $closingTxt = !empty($settings['eae_heading_indicators_closing_warning_text_enter']) ? $settings['eae_heading_indicators_closing_warning_text_enter'] : __("We are opening in ",'wts-eae');
                                        echo "<span class='eae-bh-bi-close-wmsg'>". Helper::eae_wp_kses($closingTxt) ." ".$mins." Minutes</span>"; 
                                    }
                                }
                            }
                        }

                        if($settings['eae_heading_indicators_button_label']=='yes'){
                            for($k=1; $k<=$slots; $k++){

                                $checkClose = isset($item['eae_list_content_closed']) && $item['eae_list_content_closed'] ? $item['eae_list_content_closed'] : false;
                                $currentTime = strtotime(date_i18n("H:i:s"));
                                $closingTime = strtotime(date("h:i A", strtotime($item['eae_business_closing_'.$k])));
                                $openingTime =strtotime(date("h:i A", strtotime($item['eae_business_opening_'.$k])));
                                    $position='';
                                if($settings['eae_indicator_style_heading_label_position'] == 'fixed'){
                                    $position = ' position_fixed';
                                }

                                if($checkClose){
                                    $data= !empty($settings['eae_list_content_closed_text']) ? $settings['eae_list_content_closed_text']  : "Closed";
                                    echo '<span class="bultr-labelss '.$position.'"">'.Helper::eae_wp_kses($data).'</span>';
                                }
                                else {
                                    if($k==1){
                                        if((int)$currentTime <= (int)$closingTime && (int)$currentTime >= (int)$openingTime){
                                            $data = !empty($settings['eae_heading_indicators_label_opening_text']) ? $settings['eae_heading_indicators_label_opening_text']  : "Open";
                                            echo '<span class="bultr-labelss open '.$position.'" >'. Helper::eae_wp_kses($data) .'</span>';
                                        }
                                        else{
                                            $data = !empty($settings['eae_heading_indicators_label_closing_text']) ? $settings['eae_heading_indicators_label_closing_text']  : "Closed";
                                            echo '<span class="bultr-labelss close '.$position.'">'.Helper::eae_wp_kses($data).'</span>';
                                        }
                                    }
                                }
                                if($count != $slots){
                                    $count++;
                                }else{
                                    break;
                                }
                            }
                        }
                    }
                }
        }
    }
    public function render_predefined_weekdays($item){
        
        $settings = $this->get_settings_for_display();
        $separator = !empty($settings['eae_list_content_separator']) ? $settings['eae_list_content_separator'] : '-';
        $day = date('D');
        $date = date('F d,Y');

        $showDay = $settings['eae_list_content_show_current_only'];
        
        
        $preClass[]='eae-business-weekdays-wrapper';
       
        if($showDay == 'yes'){
            if(ucfirst(substr($item['list_content'],0,3)) === $day){
                $preClass[]= 'currentDay-show';
            }
            else{
                $preClass[]='currentDay-hide';
                
            } 
        }
        if($settings['eae_list_content_highlight_current_only']=='yes'){
            if(ucfirst(substr($item['list_content'],0,3)) === $day){
                $preClass[] = ' highlightDay';
            }
            else{
                $preClass[] = '';
            }
        }
        
        if(ucfirst(substr($item['list_content'],0,3)) === $day){
                $preClass[] = ' currentday';
        }

        if($item['eae_list_content_closed']=='yes'){
            $preClass[] = ' eae-day-close';
        }
        $preClass[]= 'elementor-repeater-item-'.$item['_id'];

        $this->set_render_attribute('preClasses','class',$preClass);
        
        ?>
        <div <?php echo $this->get_render_attribute_string( 'preClasses' ); ?>"  >
            <div class="eae-business-weekdays">
                    <?php
                        if($item['pre_icon_graphic_type'] != 'none'){ 
                            Helper::render_icon_html($item,$this,'pre_icon','eae-glb-panel-icon');
                        }else{
                            if($settings['pre_icon_global_graphic_type'] != 'none'){
                                Helper::render_icon_html($settings, $this, 'pre_icon_global','eae-glb-panel-icon');
                            }
                        }
                        //  Icon
                        
                        if(isset($item['list_content'])){
                            if(isset($settings['eae_list_content_day_format']) && $settings['eae_list_content_day_format']==='short'){
                                echo substr($item['list_content'],0,3);
                            }
                            else{
                                echo Helper::eae_wp_kses($item['list_content']);
                            }
                        }
                        else{
                            echo __("Monday",'wpv-bu');
                        }
                    ?>
            </div>

            <div class="eae-business-weekdays-time">
            <?php
            
                if($item['eae_list_content_closed']==''){
                    for($i=1; $i<=$item['eae_number_of_slots'];$i++){
                        if($settings['eae_list_content_24hour_format']=='yes'){
                            $openTime = $item['eae_business_opening_'.$i];
                            $closingTime = $item['eae_business_closing_'.$i];
                        }
                        else{
                            $openTime = date("h:i A", strtotime($item['eae_business_opening_'.$i]));
                            $closingTime = date("h:i A", strtotime($item['eae_business_closing_'.$i]));
                        }
                        $dataOpen=strtotime($openTime);
                        $dataClose=strtotime($closingTime);
                        $slotLabel = !empty($item['eae_business_label_'.$i]) ? "<span class='bultr-bh-label'>{$item['eae_business_label_'.$i]}</span>" : '';
                        $dateData=Helper::eae_wp_kses($slotLabel.$openTime).' '. $separator.' '.$closingTime;
                        echo '<div class=bultr-bh-label-wrap  data-open='.$dataOpen.'  data-close='.$dataClose.'>';
                            echo $dateData;
                        echo '</div>';
                    }
                }
                else{
                    echo Helper::eae_wp_kses($item['eae_list_content_closed_text']);
                }
                ?>
            </div>
        </div>
        <?php       
    }

    public function render_custom_weekdays($item){
        $settings = $this->get_settings_for_display();
        $separator = !empty($settings['eae_list_content_separator']) ? Helper::eae_wp_kses($settings['eae_list_content_separator']) : '-';
        $day = date('D');
        $preClass='';
        $preClass = 'elementor-repeater-item-'.$item['_id'];
        if($item['eae_list_content_closed_cus'] == 'yes'){
            $preClass .= ' eae-day-close';
        }
        ?>
        <div class="eae-business-weekdays-wrapper <?php echo $preClass ?>">
            <div class="eae-business-weekdays"><?php
                if($item['eae_cus_icon_graphic_type'] != 'none'){ 
                    Helper::render_icon_html($item,$this,'eae_cus_icon','eae-cus-days-icon');
                }else{
                    if($settings['pre_icon_global_graphic_type'] != 'none'){
                        Helper::render_icon_html($settings, $this, 'pre_icon_global','eae-glb-panel-icon');
                    }
                }

                if(isset($item['list_content_cus'])){
                    echo Helper::eae_wp_kses($item['list_content_cus']);
                }
                else{
                    echo __("Monday - Friday",'wts-eae');
                }
            ?>
            </div>
            <div class="eae-business-weekdays-time">
        <?php
            if($item['eae_list_content_closed_cus']==''){
                for($i=1; $i<=$item['eae_number_of_slots_cus'];$i++){
                    if($settings['eae_list_content_24hour_format']=='yes'){
                        $openTime = $item['eae_business_opening_cus'.$i];
                        $closingTime = $item['eae_business_closing_cus'.$i];
                    }
                    else{
                        $openTime = date("h:i A", strtotime($item['eae_business_opening_cus'.$i]));
                        $closingTime = date("h:i A", strtotime($item['eae_business_closing_cus'.$i]));
                    }
                    $slotLabel = !empty($item['eae_business_label_cus'.$i]) ? "<span class='bultr-bh-label'>{$item['eae_business_label_cus'.$i]}</span>" : '';
                    $dateData=Helper::eae_wp_kses($slotLabel).$openTime.' '.$separator.' '.$closingTime;
                    echo '<div class="bultr-bh-label-wrap">';
                        echo $dateData;
                    echo '</div>';
                }
            }
            else{
                echo Helper::eae_wp_kses($item['eae_list_content_closed_text_cus']);
            }
            ?>
            </div>
        </div>
        <?php
    }
}
