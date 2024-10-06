<?php

namespace WTS_EAE\Pro\Modules\AddToCalendar\Widgets;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DateTime;
use Elementor\Group_Control_Text_Shadow;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Spatie\CalendarLinks\Link;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class AddToCalendar extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-add-to-calendar';
	}

	public function get_title() {
		return __( 'Add To Calendar', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-add-to-calendar';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'add to calendar' , 'google', 'calendar', 'ics', 'apple-calendar', 'reminder'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

	
	
    public function get_timeZoneList(){
        $tz_identifiers = timezone_identifiers_list(); 
        $zones = [];
        foreach ( $tz_identifiers as $zone ) {
            $zones[$zone] = $zone;
        }
        return $zones;
    }
    protected function register_controls(){

        $this->start_controls_section(
			'eae_calendar_section',
			[
				'label' => __( 'Calender', 'wts-eae' ),
			]
		);

		$this->add_control(
			'eae_calendar_type',
			[
				'label' => esc_html__( 'Type', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'google' => 'Google', 
					'yahoo' => 'Yahoo', 
					'microsoft' => 'Microsoft', 
					'ics' => 'ICS', 
				],
				'default' => 'google',
			]
		);

        $this->add_control(
			'eae_calendar_title',
			[
				'label'			 => esc_html__( 'Title', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
                'label_block'	 => \true,

			]
		);
        $this->add_control(
			'eae_calendar_address',
			[
				'label'			 => esc_html__( 'Address', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
                'label_block'	 => \true,
			]
		);

        $this->add_control(
            'eae_calendar_datetime', 
            [
                'label' => __('Date Field',
                'wts-eae'),
                'type' => Controls_Manager::CHOOSE, 
                'options' => [
                'picker' => 
                [
                    'title' => __('Date Time Picker', 'wts-eae'), 
                    'icon' => 'eicon-date'
                ], 
                'string' => 
                [
                    'title' => __('Dynamic String', 'wts-eae'), 
                    'icon' => 'fa fa-i-cursor'
                ]], 
                'default' => 'picker', 'toggle' => \false
            ]);
        $this->add_control(
            'eae_calendar_datetime_picker_start',
            [
                'label' => __('Date Time Start', 'wts-eae'),
                'type' => Controls_Manager::DATE_TIME, 
                'label_block' => \true,
                'dynamic'		 => [
					'active' => true,
				],
                'default'=>date('Y-m-d H:i'), 
                'condition' => ['eae_calendar_datetime' => 'picker']
            ]);
        
        $this->add_control(
            'eae_calendar_datetime_picker_end', 
            [
                'label' => __('Date Time End','wts-eae'), 
                'type' => Controls_Manager::DATE_TIME, 
                'label_block' => \true, 
                'dynamic'=> [
					'active' => true,
				],
                'default'=>date('Y-m-d H:i',strtotime("+1 day")), 
                'condition' => ['eae_calendar_datetime' => 'picker'],
        ]);

        $this->add_control(
            'eae_calendar_datetime_string_start',
            [
                'label' => __('DateTime Start', 'wts-eae'),   
                'type' => Controls_Manager::TEXT, 
                'label_block' => \true, 
                'default'=>date('Y-m-d H:i'), 
                'description' => __( 'Use this Formate Y-m-d H:i example (2023-04-17 12:00)', 'wts-eae' ),
                'dynamic'=> [
					'active' => true,
				],
                'condition' => ['eae_calendar_datetime' => 'string']
            ]);
        $this->add_control(
            'eae_calendar_datetime_string_end',
            [
                'label' => __('DateTime End', 'wts-eae'),   
                'type' => Controls_Manager::TEXT, 
                'label_block' => \true, 
                'default'=>date('Y-m-d H:i',strtotime("+1 day")), 
                'dynamic'=> [
					'active' => true,
				],
                'condition' => ['eae_calendar_datetime' => 'string'],
                'description' => __( 'Use this Formate Y-m-d H:i example (2023-04-18 12:00)', 'wts-eae' ),

            ]
        );

        // Add Elementor text field control for the filename    
        $this->add_control(
            'filename', 
            [
                'label' => __('Filename', 'wts-eae'),
                'type' => Controls_Manager::TEXT, 
                'label_block' => true, 
                'condition' => ['eae_calendar_type' => 'ics']
            ]
        ); 
    
        $this->add_control(
            'eae_calendar_description', 
            [
                'label' => __('Description', 'wts-eae'), 
                'type' => Controls_Manager::WYSIWYG
            ]);
		$this->end_controls_section();

        $this->start_controls_section(
			'eae_calendar_button_section',
			[
				'label' => __( 'Button', 'wts-eae' ),
			]
		);

        $this->add_control(
			'eae_calendar_button_text',
			[
				'label'			 => esc_html__( 'Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
                'default' => esc_html__( 'Add To Calendar', 'wts-eae' ),
			]
		);
   
        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
                'defaults'      => [
                    'graphic_type_default' => 'icon',
                    'graphic_icon_default' => [
                        'value' => 'fas fa-star-half-alt',
                        'library' => 'fa-solid'
                    ],
                 ]
            ]
        );
        $this->add_control(
			'eae_calendar_button_icon_position',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'row' => 'Before',
					'row-reverse' => 'After',
				],
				'default' => 'row',
                'selectors' => [
					'{{WRAPPER}} .eae-calendar-wrapper_link' => 'flex-direction:{{VALUE}}',						
				],
			]
		);
        $this->add_responsive_control(
			'eae_calendar_button_align',
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
					'justify' => [
						'title' => esc_html__( 'Justified', 'wta-eae' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
                'selectors_dictionary' => [
                    'left' => 'text-align:left',
                    'center' => 'text-align:center',
                    'right' => 'text-align:right',
                    'justify' => 'width:100%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container' => '{{VALUE}}',
                    '{{WRAPPER}} .eae-calendar-wrapper_link ' => '{{VALUE}}',
                ],
			]
		);
        $this->end_controls_section();

       
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__( 'Button', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_separator_text',
                'selector' => '{{WRAPPER}} .eae-calendar-wrapper_link',
            ]
        );
        $this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .eae-calendar-wrapper_link',
			]
		);
        $this->start_controls_tabs(
            'section_button_style_tab'
        );
        $this->start_controls_tab(
            'section_button_style_tab_normal',
            [
                'label' => esc_html__( 'Normal', 'wts-eae' ),
            ]
        );
        $this->add_control(
            'eae_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-calendar-wrapper_link' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-calendar-wrapper_link',
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
            'section_button_style_tab_hover',
            [
                'label' => esc_html__( 'Hover', 'wts-eae' ),
            ]
        );
        $this->add_control(
            'eae_button_text_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-calendar-wrapper_link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-calendar-wrapper_link:hover',
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

        $this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-calendar-wrapper_link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

        $this->add_responsive_control(
			'border_radius_hover',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-calendar-wrapper_link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}}  .eae-calendar-wrapper_link:hover',
				
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}}  .eae-calendar-wrapper_link',
				'separator' => 'before',
				
			]
		);
        $this->add_responsive_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-calendar-wrapper_link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}}  .eae-calendar-wrapper_link',
				
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-calendar-wrapper_link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
       
        $this->add_control(
			'eae_heading_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        Helper::global_icon_style_controls(
            $this,
            [
                'name' => 'icon',
                'selector' => '.eae-act-button-icon',
                'default'       => [
					'custom_primary_color' => true,
					'primary_color' => '#ffffff',
				],
                'is_repeater' => false,
                'hover_selector'      => '.eae-calendar-wrapper_link:hover .eae-act-button-icon',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
            ]
        );
        $this->end_controls_section();

       
    }
    public function render(){
        $settings = $this->get_settings_for_display();
        $fromDate = $settings['eae_calendar_datetime'] != 'string' ? $settings['eae_calendar_datetime_picker_start'] : $settings['eae_calendar_datetime_string_start'];
        $toData = $settings['eae_calendar_datetime'] != 'string' ? $settings['eae_calendar_datetime_picker_end'] : $settings['eae_calendar_datetime_string_end'];
        $format = 'Y-m-d H:i';
        $dateTimeFrom = DateTime::createFromFormat($format, $fromDate);
        $dateTimeEnd = DateTime::createFromFormat($format, $toData);
        $link = $this->prepare_data();
        if(empty($link)){
            return;
        }
            $this->add_render_attribute('eae-calendar-wrapper_link', 'href', $link);
            $this->add_render_attribute('eae-calendar-wrapper_link', 'class', 'eae-calendar-wrapper_link');
            $this->add_render_attribute('eae-calendar-wrapper_link', 'title', $settings['eae_calendar_title']);
            $this->add_render_attribute('eae-calendar-wrapper_link', 'target', '_blank');
            if($settings['eae_calendar_type'] == 'ics'){
                $this->add_render_attribute('eae-calendar-wrapper_link', 'download', sanitize_file_name($settings['filename']));
            }
            ?>
            <a <?php echo $this->get_render_attribute_string('eae-calendar-wrapper_link'); ?>> 
                <?php
                    Helper::render_icon_html($settings, $this,'icon','eae-act-button-icon '. $settings['eae_calendar_button_icon_position'], 'test');?>
                <?php echo Helper::eae_wp_kses($settings['eae_calendar_button_text']); ?>
            </a>             
           <?php

    }
    
    public function prepare_data(){
        $settings = $this->get_settings_for_display();
		$date_format = 'Y-m-d H:i';
        $fromDate = $settings['eae_calendar_datetime'] != 'string' ? $settings['eae_calendar_datetime_picker_start'] : $settings['eae_calendar_datetime_string_start'];
		//if timestamp passed
		if(is_numeric($fromDate)){
			$fromDate = date($date_format, $fromDate);
		}

        if(empty($fromDate)){
            if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-warning'>Please enter the Start Date</p></div>";
            }
            return;   
        }
        $toData = $settings['eae_calendar_datetime'] != 'string' ? $settings['eae_calendar_datetime_picker_end'] : $settings['eae_calendar_datetime_string_end'];
		//if timestamp passed
		if(is_numeric($toData)){
			$toData = date($date_format, $toData);
		}

        $address=! empty( $settings['eae_calendar_address'] ) ? $settings['eae_calendar_address'] : ''; 
        $description=! empty( $settings['eae_calendar_description'] ) ? $settings['eae_calendar_description'] : '';
        $title=! empty( $settings['eae_calendar_title'] ) ? $settings['eae_calendar_title'] : '';
        $type=$settings['eae_calendar_type'];

        
        // $from = DateTime::createFromFormat('Y-m-d H:i', $fromDate);
        $date_format = 'Y-m-d H:i';
        $from = \DateTime::createFromFormat($date_format, $fromDate, new \DateTimeZone(wp_timezone_string()));
        if(empty($toData)){
            $to = $from = \DateTime::createFromFormat($date_format, $fromDate, new \DateTimeZone(wp_timezone_string()));
            $to = $to->modify('+1 day');
        }else{
            $to = \DateTime::createFromFormat($date_format, $toData, new \DateTimeZone(wp_timezone_string()));
        }
        if($from > $to){
            if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-danger'>
                The end date should be greater than the start date.
                </p></div>";
            }
            return;
        }        
            $link = Link::create($title, $from, $to)
            ->description($description)
            ->address($address);
            switch ($type) {
                case "google":
                  return   $link->google();
                break;
              case "ics":
                    if (current_user_can('administrator') && \strpos(wp_timezone_string(), ':')) {
                        echo "<div class='eae-vg-error message'>";
                        echo "<p class='elementor-alert elementor-alert-danger'>" . 
                            esc_html__('The ICS file might be considered invalid. If the Timezone in WordPress settings is defined as an offset, like UTC+1, it is not supported. To resolve this issue, please set the Timezone using a city name, such as "New York".', 'wts-eae');
                            echo "<br/>To Change this <strong> Go to Dashboard > Settings > TimeZone<strong>";
                        echo '</p> </div>';
                    }
                  return  $link->ics();
                break;
              case "yahoo":
                  return  $link->yahoo();
                break;
              case "microsoft":
                  return  $link->webOutlook();
                break;       
          }
        
    }

}

