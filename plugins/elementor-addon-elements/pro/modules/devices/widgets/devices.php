<?php

namespace WTS_EAE\Pro\Modules\Devices\Widgets;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin as EPlugin;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class Devices extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-devices';
	}

	public function get_title() {
		return __( 'Devices', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-devices';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'devices'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

    protected function register_controls(){

        $this->start_controls_section(
			'eae_device_control',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

            $this->add_control(
            	'devices',
            	[
            		'label' => esc_html__( 'Device', 'wts-eae' ),
            		'type' => Controls_Manager::CHOOSE,
					'toggle' => false,
            		'options' => [
            			'mobile' => [
            				'title' => esc_html__( 'Mobile', 'wts-eae' ),
            				'icon' => 'eicon-device-mobile',
            			],
            			'tablet' => [
            				'title' => esc_html__( 'Tablet', 'wts-eae' ),
            				'icon' => 'eicon-device-tablet',
            			],
            			'laptop' => [
            				'title' => esc_html__( 'Laptop', 'wts-eae' ),
            				'icon' => 'eicon-device-laptop',
            			],
            			'desktop' => [
            				'title' => esc_html__( 'Desktop', 'wts-eae' ),
            				'icon' => 'eicon-device-desktop',
            			],
            			'browser' => [
            				'title' => esc_html__( 'Browser', 'wts-eae' ),
            				'icon' => 'eicon-header',
            			],
            		],
            		'default' => 'laptop',
            	]
            );

            $this->add_control(
                'tablet_option',
                [
                    'label' => esc_html__( 'Tablet Device', 'wta-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'ipad11' => 'iPad 11',
                        'ipad11pro' => 'iPad 11Pro',
                    ],
                    'default' => 'ipad11',
                    'condition'    => [
                        'devices' => 'tablet'
                    ],
                ]
            );
            $this->add_control(
                'laptop_option',
                [
                    'label' => esc_html__( 'Laptop Device', 'wta-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'macAir'    => 'MacBook Air',
                        'macPro'    => 'MacBook Pro',
                    ],
                    'default' => 'macAir',
                    'condition'    => [
                        'devices' => 'laptop'
                    ],
                ]
            );

            $this->add_control(
                'desktop_option',
                [
                    'label' => esc_html__( 'Desktop Device', 'wta-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'iMacBlack' => 'iMac Black',
                        'iMacWhite' => 'iMac White',
                    ],
                    'default' => 'iMacBlack',
                    'condition'    => [
                        'devices' => 'desktop'
                    ],
                ]
            );

            $this->add_control(
                'browser_option',
                [
                    'label' => esc_html__( 'Browser Themes', 'wta-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'chrome' => 'Light',
                        'dChrome' => 'Dark',
                    ],
                    'default' => 'chrome',
                    'condition'    => [
                        'devices' => 'browser'
                    ],
                ]
            );

			$this->add_control(
				'title',
				[
					'label' => esc_html__( 'Title', 'wts-eae' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Slide title', 'wts-eae' ),
				]
			);

			$this->add_control(
				'device_orientation',
				[
					'label' 		=> __( 'Orientation', 'wts-eae' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default'		=> 'portrait',
					'options' 		=> [
						'portrait' 	=> [
							'title' => __( 'Portrait', 'wts-eae' ),
							'icon' 	=> ' eicon-device-mobile',
						],
						'landscape' => [
							'title' => __( 'Landscape', 'wts-eae' ),
							'icon' 	=> 'eicon-device-mobile eicon-tilted',
						],
					],
					'condition'		=> [
						'devices' => [ 'mobile', 'tablet' ],
					]
				]
			);
	
			$this->add_control( 
				'orientation_control',
				[
					'label'        => __( 'Orientation Control', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'yes',
					'default'      =>'no',
					'condition'		=> [
						'devices' => [ 'mobile', 'tablet' ],
					]
				]
			);

			Helper::eae_media_controls(
				$this,[
					'name' => 'slide_icon',
					'label' => 'Icon',
					'icon'			=> true,
					'image'			=> false,
					'lottie'		=> true,
				]
			);


			$this->add_control(
				'background',
				[
					'label'   => esc_html__('Background', 'wts-eae'),
					'type'    => Controls_Manager::CHOOSE,
					'default' => 'color',
					'options' => [
						'color'   => [
							'title' => esc_html__('Color', 'wts-eae'),
							'icon'  => 'eicon-paint-brush',
						],
						'image'   => [
							'title' => esc_html__('Image', 'wts-eae'),
							'icon'  => 'eicon-image',
						],
						'video' => [
							'title' => esc_html__('Video', 'wts-eae'),
							'icon'  => 'eicon-youtube',
						],
					],
				]
			);

			$this->add_control(
				'video_option',
				[
					'label' => esc_html__( 'Type', 'wta-eae' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'youtube'    => 'Youtube',
						'vimeo'    => 'Vimeo',
						'wistia' => 'Wistia',
						'hosted'    => 'Self Hosted',
					],
					'default' => 'youtube',
					'condition'    => [
						'background' => 'video'
					],
				]
			);

			$this->add_control(
			'youtube_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your URL', 'wts-eae' ) . ' (YouTube)',
				'default' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				'label_block' => true,
				'condition' => [
					'background' => 'video',
					'video_option' => 'youtube',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'vimeo_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'Enter your URL', 'wts-eae' ) . ' (Vimeo)',
				'default' => 'https://vimeo.com/235215203',
				'label_block' => true,
				'condition' => [
					'background' => 'video',
					'video_option' => 'vimeo',
				],
				'ai' => [
					'active' => false,
				],
			]
		);


		$this->add_control(
			'insert_link',
			array(
				'label'     => __( 'External URL', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'video_option' => 'hosted',
					'background' => 'video',
				),
			)
		);

		$this->add_control(
			'hosted_link',
			array(
				'label'      => __( 'Choose File', 'wts-eae' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => array(
					'active'     => true,
					'categories' => array(
						TagsModule::MEDIA_CATEGORY,
					),
				),
				'media_type' => 'video',
				'condition'  => array(
					'video_option'  => 'hosted',
					'insert_link' => '',
					'background' => 'video',
				),
			)
		);

		$this->add_control(
			'external_link',
			array(
				'label'        => __( 'URL', 'wts-eae' ),
				'type'         => Controls_Manager::URL,
				'autocomplete' => false,
				'options'      => false,
				'label_block'  => true,
				'show_label'   => false,
				'dynamic'      => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'media_type'   => 'video',
				'placeholder'  => __( 'Enter your URL', 'wts-eae' ),
				'condition'    => array(
					'video_option'  => 'hosted',
					'insert_link' => 'yes',
					'background' => 'video',
				),
			)
		);

		$this->add_control(
			'color',
			[
				'label'     => esc_html__('Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#14ABF4',
				'condition' => [
					'background' => 'color'
				],
				'selectors' => [
					'{{WRAPPER}} .device-img-content ' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label'     => esc_html__('Image', 'wts-eae'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'background' => 'image'
				],
			]
		);
		$this->add_control(
			'start',
			array(
				'label'       => __( 'Start Time', 'wts-eae' ),
				'type'        => Controls_Manager::NUMBER,
				'dynamic'     => array(
					'active' => true,
				),
				'description' => __( 'Specify a start time (in seconds)', 'wts-eae' ),
				'condition'   => array(
					'video_option' => array( 'youtube', 'vimeo', 'hosted'),
					'background' => 'video',
				),
			)
		);

		$this->add_control(
			'end',
			array(
				'label'       => __( 'End Time', 'wts-eae' ),
				'type'        => Controls_Manager::NUMBER,
				'dynamic'     => array(
					'active' => true,
				),
				'description' => __( 'Specify an end time (in seconds)', 'wts-eae' ),
				'condition'   => array(
					'video_option' => array( 'youtube', 'hosted' ),
					'background' => 'video',    
				),
			)
		);

		$this->add_control(
			'yt_autoplay',
			array(
				'label'     => __( 'Autoplay', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				// 'description' => __('To enable autoplay, you must enable the mute option.', 'wts-eae'),
				'condition'   => array(
					'video_option' => 'youtube',
					'background' => 'video',

				),
			)
		);

		$this->add_control(
			'yt_controls',
			array(
				'label'     => __( 'Player Control', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'wts-eae' ),
				'label_on'  => __( 'Show', 'wts-eae' ),
				'default'   => 'yes',
				'condition' => array(
					'video_option' => 'youtube',
					'background' => 'video',
				),
			)
		);


		$this->add_control(
			'yt_modestbranding',
			[
				'label'       => esc_html__( 'Modest Branding', 'wts-eae' ),
				'description' => esc_html__( 'This option lets you use a YouTube player that does not show a YouTube logo.', 'wts-eae' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'video_option' => 'youtube',
					'background' => 'video',
				),
			]
		);

		$this->add_control(
			'yt_mute',
			array(
				'label'     => __( 'Mute', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'return_value' => 'yes' ,
				'condition'   => array(
					'video_option' => 'youtube',
					'background' => 'video',
				),
			)
		);

		$this->add_control(
			'vm_autoplay',
			array(
				'label'     => __( 'Autoplay', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				// 'description' => __('To enable autoplay, you must enable the mute option.', 'wts-eae'),
				'condition'   => array(
					'video_option' => 'vimeo',
					'background' => 'video',

				),
			)
		);

		

		$this->add_control(
			'vm_loop',
			[
				'label'     => esc_html__( 'Loop', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition'   => array(
					'video_option' => 'vimeo',
					'background' => 'video',
				),
			]
		);

		$this->add_control(
			'vm_muted',
			[
				'label'     => esc_html__( 'Mute', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition'   => array(
					'video_option' => 'vimeo',
					'background' => 'video',
				),
			]
		);

		$this->add_control(
			'vm_title',
			[
				'label'     => esc_html__( 'Intro Title', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'wts-eae' ),
				'label_on'  => esc_html__( 'Show', 'wts-eae' ),
				'default'   => 'yes',
				'condition'   => array(
					'video_option' => 'vimeo',
					'background' => 'video',
				),
			]
		);

		$this->add_control(
			'vm_portrait',
			[
				'label'     => esc_html__( 'Intro Portrait', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'wts-eae' ),
				'label_on'  => esc_html__( 'Show', 'wts-eae' ),
				'default'   => 'yes',
				'condition'   => array(
					'video_option' => 'vimeo',
					'background' => 'video',
				),
			]
		);

		$this->add_control(
			'vm_byline',
			[
				'label'     => esc_html__( 'Intro Byline', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'wts-eae' ),
				'label_on'  => esc_html__( 'Show', 'wts-eae' ),
				'default'   => 'yes',
				'condition'   => array(
					'video_option' => 'vimeo',
					'background' => 'video',
				),
			]
		);
		$this->add_control(
			'vm_color',
			[
				'label'     => __( 'Controls Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .eae-vimeo-title a'  => 'color: {{VALUE}}',
					'{{WRAPPER}} .eae-vimeo-byline a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eae-vimeo-title a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eae-vimeo-byline a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eae-vimeo-title a:focus' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eae-vimeo-byline a:focus' => 'color: {{VALUE}}',
				),
				'condition'   => array(
					'video_option' => 'vimeo',
					'background' => 'video',
				),
			]
		);
		$default_wistia = apply_filters( 'eae_video_default_wistia_link', '<p><a href="https://wpvwebmaster.wistia.com/medias/82tscaz1gr?wvideo=82tscaz1gr"><img src="https://embed-ssl.wistia.com/deliveries/778f315db911722d46d7ae50d4d567f23ce009c5.jpg?image_play_button_size=2x&amp;image_crop_resized=960x540&amp;image_play_button=1&amp;image_play_button_color=174bd2e0" width="400" height="225" style="width: 400px; height: 225px;"></a></p><p><a href="https://wpvwebmaster.wistia.com/medias/82tscaz1gr?wvideo=82tscaz1gr">Time Lapse of the Milky Way over the Beach</a></p>' );

		$this->add_control(
			'wistia_link',
			[
				'label' => esc_html__('Link & Thumbnail Text','wts-eae'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					]
				],
				'default'     => $default_wistia,
				'label_block' => true,
				'condition' => [
					'video_option' => 'wistia', 
					'background' => 'video',
				] 
			]
		);

		$this->add_control(
			'wistia_link_helper',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'             => sprintf( __( '<div class="eae-notice">Go to your Wistia video, right click, "Copy Link & Thumbnail" and paste here. </div>', 'wts-eae' ) ),
				'condition' => [
					'video_option' => 'wistia',
					'background' => 'video',
				],
			]
		);

		$this->add_control(
			'wistia_autoplay',
			array(
				'label'     => __( 'Autoplay', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				// 'description' => __('To enable autoplay, you must enable the mute option.', 'wts-eae'),
				'condition'   => array(
					'video_option' => 'wistia',
					'background' => 'video',
				),
			)
		);

		$this->add_control(
			'wistia_loop',
			[
				'label'     => esc_html__( 'Loop', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'video_option' => 'wistia',
					'background' => 'video',
				],
			]
		);

		$this->add_control(
			'wistia_muted',
			[
				'label'     => esc_html__( 'Mute', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'video_option' => 'wistia',
					'background' => 'video',
				],
			]
		);

		$this->add_control(
			'wistia_playbar',
			[
				'label'     => esc_html__( 'Show Playbar', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'video_option' => 'wistia',
					'background' => 'video',
				],
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			array(
				'label'     => __( 'Autoplay', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'video_option' => 'hosted',
					'background' => 'video',
				],
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'controls',
			array(
				'label'     => __( 'Player Control', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'wts-eae' ),
				'label_on'  => __( 'Show', 'wts-eae' ),
				'default'   => 'yes',
				'return_value' => 'yes',
				'condition' => [
					'video_option' => 'hosted',
					'background' => 'video',
				],
			)
		);


		$this->add_control(
			'loop',
			array(
				'label'     => __( 'Loop', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition' => [
					'video_option' => 'hosted',
					'background' => 'video',
				],
			)
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
				'default' => '169',
				'selectors' => [
					'{{WRAPPER}}  .eae-device-video-outer-wrapper img' => 'aspect-ratio: {{VALUE}}',
					'{{WRAPPER}}  .eae-device-video-outer-wrapper .eae-device-video-play iframe' => 'aspect-ratio: {{VALUE}}',
					'{{WRAPPER}}  .eae-device-video-outer-wrapper .eae-device-video-play video' => 'aspect-ratio: {{VALUE}}',
				],
				'condition' => [
					'background' => 'video',
				],
			]
		);

		

        $this->add_control( 
			'enable_scroll',
			[
				'label'        => __( 'Enable Scroll', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
				'default'      =>'no',
                'condition'		=> [
                   'background'=>'image'
                ]
			]
		);

		$this->add_control(
			'scroll_direction',
			[
				'label' => esc_html__( 'Direction', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
				],
				'default' => 'vertical',
				'condition'		=> [
					'enable_scroll'=>'yes',
					'background'=>'image'
				 ]
			]
		);

        $this->add_control(
            'scroll_speed',
            [
                'label' => esc_html__( 'Speed' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default'     => 3,
				'min'         => 1,
				'max'         => 10,
                'selectors'=>[
                    '{{WRAPPER}} .eae-devices-img'   => 'transition-duration: {{value}}s',
                ],
				'condition'		=> [
					'enable_scroll'=>'yes',
					'background'=>'image'
				 ]
            ]
        );
      

        $this->add_control(
			'scroll_trigger',
			[
				'label' => esc_html__( 'Trigger', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'hover' => 'Hover',
					'scroll' => 'Scroll',
				],
				'default' => 'hover',
				'condition'		=> [
					'enable_scroll'=>'yes',
					'background'=>'image'
				 ]
			]
		);

		$this->add_control(
			'reverse',
			[
				'label' => esc_html__( 'Reverse', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'scroll_trigger' => 'hover',
					'enable_scroll'=>'yes',
					'background'=>'image'
				],
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'devices_style_controls',
			[
				'label' => 'Device',
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_responsive_control(
            'device_width',
            [
                'label' => esc_html__( 'Width', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 475,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-device-wrapper' => 'max-width: {{SIZE}}{{UNIT}} ;',
                ],
            ]
        );

		$this->add_control(
			'device_color',
			[
				'label'                 => __( 'Device Color', 'wts-eae' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .devices-mockup-wrapper svg .eae-frame'=> 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tone',
			[
				'label'                 => __( 'Tone', 'wts-eae' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'light',
				'options'               => [
					'dark'  => __( 'Dark', 'wts-eae' ),
					'light' => __( 'Light', 'wts-eae' ),
				],
				'selectors_dictionary'  => [
					'dark'  => '#000000',
					'light' => '#efefef',
				],
				'selectors'             => [
					'{{WRAPPER}} .devices-mockup-wrapper svg .eae-frame-element' => 'fill: {{VALUE}};',
					'{{WRAPPER}}.eae-device-tone-light .device-iMacBlack .devices-mockup-wrapper svg .eae-frame-element' => 'fill : #525252'
				],
				'prefix_class'          => 'eae-device-tone-',
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
					'{{WRAPPER}} .devices-mockup-wrapper svg .eae-frame-element' => 'fill-opacity: {{SIZE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'devices_image_controls',
			[
				'label' => 'Image',
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'=>[
					'background' => 'image'
				]
			]
		);


		$this->add_responsive_control(
			'object-fit',
			[
				'label' => esc_html__( 'Object Fit', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Default', 'wts-eae' ),
					'fill' => esc_html__( 'Fill', 'wts-eae' ),
					'cover' => esc_html__( 'Cover', 'wts-eae' ),
					'contain' => esc_html__( 'Contain', 'wts-eae' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-devices-img' => 'object-fit: {{VALUE}};',
				],
				'condition'=>[
					'background' => 'image'
				]
			]
		);


		$this->add_responsive_control(
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
					'{{WRAPPER}} .eae-devices-img' => 'object-position: {{VALUE}};',
				],
				'condition'=>[
					'background' => 'image'
				]
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'devices_text_controls',
			[
				'label' => 'Content',
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
            'text_heading',
            [
                'label'     => __( 'Text', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .device-text',
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
            'heading_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .device-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .device-text',
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
            'heading_text_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .device-text:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

      
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_hover',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .device-text:hover',
            ]
        );      
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .device-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .device-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],      
            ]
        );

		$this->add_responsive_control(
            'text_gap',
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
                    '{{WRAPPER}} .device-text' => 'gap: {{SIZE}}{{UNIT}};',
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
			'name' => 'slide_icon',
			'selector' => '.device-title-icon',

	    ]);

		$this->add_control(
            'Orientation_heading',
            [
                'label'     => __( 'Orientation', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


		$this->add_responsive_control(
            'ori_size',
            [
                'label' => esc_html__( 'Size', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}}  .orientation i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                
            ]
        );


		$this->add_control(
			'ori_color',
			[
				'label'                 => __( 'Color', 'wts-eae' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}}  .orientation i'=> 'color: {{VALUE}};',
				],
			]
		);







		$this->end_controls_section();

    }
	public function get_prepared_data($settings){
		$data = [];

		
		
		
		$pre_data = [
			'title' => $settings['title'],
			'background' => $settings['background'],
			'image'      => $settings['image'],
			'video_option' =>  $settings['video_option'],
			'youtube_link' => $settings['youtube_link'],
			'vimeo_link' => $settings['vimeo_link'],
			'insert_link' => $settings['insert_link'],
			'hosted_link' => $settings['hosted_link'],
			'external_link' => $settings['external_link'],
			'start' => $settings['start'],
			'end' => $settings['end'],
			'yt_autoplay' => $settings['yt_autoplay'],
			'yt_modestbranding' => $settings['yt_modestbranding'],
			'yt_mute' => $settings['yt_mute'],
			'vm_autoplay' => $settings['vm_autoplay'],
			'vm_loop' => $settings['vm_loop'],
			'vm_muted' => $settings['vm_muted'],
			'vm_title' => $settings['vm_title'],
			'vm_portrait' => $settings['vm_portrait'],
			'vm_byline' => $settings['vm_byline'],
			'wistia_link' => $settings['wistia_link'],
			'wistia_autoplay' => $settings['wistia_autoplay'],
			'wistia_loop' => $settings['wistia_loop'],
			'wistia_muted' => $settings['wistia_muted'],
			'wistia_playbar' => $settings['wistia_playbar'],
			'autoplay' => $settings['autoplay'],
			'loop' => $settings['loop'],
			'autoplay' => $settings['autoplay'],
			'controls' => $settings['controls'],
			
			

		];
		$data[] = $pre_data;
		return $data;
	}

    public function render(){
        $settings = $this->get_settings_for_display();
		$data = $this->get_prepared_data($settings);
        $svgFile = '';

		if($settings['enable_scroll']=='yes'){
			$scrollData = [];
			$scrollData['direction'] = $settings['scroll_direction'];
			$scrollData['trigger'] = $settings['scroll_trigger'];
			$scrollData['reverse'] = $settings['reverse'];
		}
		
        if($settings['devices']== 'mobile' ){
			$svgFile = 'iphoneSe';
           
        }else{
			$svgFile = $settings[$settings['devices'].'_option'];
		}
		
        $this->add_render_attribute('eae-device-wrapper' , 'class', 'eae-device-wrapper ');
        $this->add_render_attribute('eae-device-wrapper' , 'class', 'eae-'.$settings['devices']);
        $this->add_render_attribute('eae-device-wrapper' , 'class', ' device-'.$svgFile);
		$this->add_render_attribute( 'eae-video-thumb', 'class', 'eae-video-thumb' );


		if($settings['enable_scroll']=='yes' && $settings['background']=='image'){
			$trigger =$settings['scroll_trigger'];
			$this->add_render_attribute('eae-device-swiper-wrapper', [    
				'class' => 'device-content '.$trigger,
				'data-settings' => json_encode($scrollData),
			]);
		}
		else{
			$this->add_render_attribute('eae-device-swiper-wrapper' , 'class', 'device-content');
		}

        $ori='';
        $ori = $settings['device_orientation'];
		$scrollDirection="";
		$scrollDirection = $settings["scroll_direction"];
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);
		
		
		echo "<div class='eae-device-container'>"; 
            
			?> <div <?php echo wp_kses_post( $this->get_render_attribute_string( 'eae-device-wrapper' ) ); ?>> 
				<div class="<?php echo esc_attr("eae-wrapper {$ori}") ?>">
			<?php
					echo "<div class='devices-mockup-wrapper'>";
						echo file_get_contents(EAE_URL . 'pro/assets/img/devices/'.$svgFile.'.svg', false, stream_context_create($arrContextOptions) );

					echo "</div>";
					
				
					?> <div <?php echo  $this->get_render_attribute_string( 'eae-device-swiper-wrapper' ) ?> > <?php

						echo "<div class='device-inner-container eae-swiper-wrapper '>";
						// print_r($data);
								foreach( $data as $key => $item){?>
									<div class="device-img-content <?php echo "{$scrollDirection}";?> ">
								<?php
									echo "";
									switch ($item['background']) {
										case "image":
												$imgUrl =  esc_url($item['image']['url']);
												echo "<img src='".$imgUrl."' class='eae-devices-img' />";
										break;
										case "video":
											$this->get_video_functionality( $item['video_option']  , $item);
										break;
									}
									echo "<span class='device-text'>";
									
										echo Helper::eae_wp_kses($item['title']);
										Helper::render_icon_html($settings,$this,'slide_icon','device-title-icon'); 
									echo "</span>";
									echo "</div>";
								}						
						echo "</div>";
					echo "</div>";			
					echo "</div>";
					$class='rotate';
					if($settings['device_orientation']=='landscape'){
						$class='';
					}
					if ( $settings['orientation_control']=='yes'){     
					echo "<span class='orientation '>";
						echo "<i class='eicon-device-mobile $class' aria-hidden='true'></i>";      
					echo "</span>";
					}
				
			echo "</div>";

		echo "</div>"; 

    }

    public function get_swiper_data($settings){
        
        if ( $settings['speed']['size'] ) {
			$swiper_data['speed'] = $settings['speed']['size'];
		} else {
			$swiper_data['speed'] = 1000;
		}

		if ( $settings['autoplay'] === 'yes' ) {
			$swiper_data['autoplay']['delay'] = $settings['duration']['size'];
		} else {
			$swiper_data['autoplay'] = false;
		}

		if ( $settings['pause_on_hover'] === 'yes' ) {
			$swiper_data['pause_on_hover'] = $settings['pause_on_hover'];
		}

		if ( $settings['keyboard'] === 'yes' ) {
			$swiper_data['keyboard'] = $settings['keyboard'];
		}
		$swiper_data['effect'] = $settings['effect'];

		$swiper_data['loop']       = $settings['loop'];
		$height                    = $settings['auto_height'];
		$swiper_data['autoHeight'] = ( $height === 'yes' ) ? true : false;
		$ele_breakpoints           = EPlugin::$instance->breakpoints->get_active_breakpoints();
		$active_devices            = EPlugin::$instance->breakpoints->get_active_devices_list();
		$active_breakpoints        = array_keys( $ele_breakpoints );
		$break_value               = [];
		foreach ( $active_devices as $active_device ) {
			$min_breakpoint                = EPlugin::$instance->breakpoints->get_device_min_breakpoint( $active_device );
			$break_value[ $active_device ] = $min_breakpoint;
		}

		if ( $settings['effect'] === 'fade' || $settings['effect'] === 'flip' ) {
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['spaceBetween'][ $active_device ] = 0;
			}
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['slidesPerView'][ $active_device ] = 1;
			}
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['slidesPerGroup'][ $active_device ] = 1;
			}
		} else {

			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['spaceBetween'][ $active_device ] = intval( $settings['space_' . $active_device]['size'] !== '' ? $settings['space_' . $active_device]['size'] : 5 );
							break;
						case 'tablet':
							$swiper_data['spaceBetween'][ $active_device ] = intval( $settings['space_' . $active_device]['size'] !== '' ? $settings['space_' . $active_device]['size'] : 10 );
							break;
						case 'desktop':
							$swiper_data['spaceBetween']['default'] = intval( $settings['space']['size'] !== '' ? $settings['space']['size'] : 15 );
							break;
					}
				} else {
					$swiper_data['spaceBetween'][ $active_device ] = intval( $settings['space_' . $active_device]['size'] !== '' ? $settings['space_' . $active_device]['size'] : 15 );
				}
			}
			//SlidesPerView
			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['slidesPerView'][ $active_device ] = intval( $settings['slide_per_view_' . $active_device] !== '' ? $settings['slide_per_view_' . $active_device] : 1 );
							break;
						case 'tablet':
							$swiper_data['slidesPerView'][ $active_device ] = intval( $settings['slide_per_view_' . $active_device] !== '' ? $settings['slide_per_view_' . $active_device] : 2 );
							break;
						case 'desktop':
							$swiper_data['slidesPerView']['default'] = intval( $settings['slide_per_view'] !== '' ? $settings['slide_per_view'] : 3 );
							break;
					}
				} else {
					$swiper_data['slidesPerView'][ $active_device ] = intval( $settings['slide_per_view_' . $active_device] !== '' ? $settings['slide_per_view_' . $active_device] : 2 );
				}
			}

			// SlidesPerGroup
			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['slidesPerGroup'][ $active_device ] = $settings['slides_per_group_' . $active_device] !== '' ? $settings['slides_per_group_' . $active_device] : 1;
							break;
						case 'tablet':
							$swiper_data['slidesPerGroup'][ $active_device ] = $settings['slides_per_group_' . $active_device] !== '' ? $settings['slides_per_group_' . $active_device] : 1;
							break;
						case 'desktop':
							$swiper_data['slidesPerGroup']['default'] = $settings['slides_per_group'] !== '' ? $settings['slides_per_group'] : 1;
							break;
					}
				} else {
					$swiper_data['slidesPerGroup'][ $active_device ] = $settings['slides_per_group_' . $active_device] !== '' ? $settings['slides_per_group_' . $active_device] : 1;
				}
			}
		
			
		}

		if ( $settings['ptype'] !== '' ) {
			$swiper_data['ptype'] = $settings['ptype'];
		}
		$swiper_data['breakpoints_value'] = $break_value;
		$clickable                        = $settings['clickable'];
		$swiper_data['clickable']         = isset( $clickable ) ? $clickable : false;
		$swiper_data['navigation']        = $settings['navigation_button'];
		$swiper_data['scrollbar']         = $settings['scrollbar'];

		return $swiper_data;

       

    }

    

    public function get_video_functionality($video_type , $item)
    {

        if($video_type != 'hosted'){ 
			if(empty($item[$video_type.'_link'])){
				return;
			}
		}else{      
			if($item['insert_link'] == 'yes'){
				if(empty($item['external_link']['url'])){
					return;
				}	
			}else{
				if(empty($item['hosted_link']['url'])){
					return;
				}
			}
		}	

        if($video_type != 'hosted'){
			$video_id  = $this->get_video_id($video_type,$item);
		}
     
        switch ($video_type) {
            case 'youtube':
                $embed_param =  $this->youtube_embed_params($item);
              break;
            case 'vimeo':
                $embed_param =  $this->vimeo_embed_params($item);
              break;
            case 'wistia':
                $embed_param =  $this->wistia_embed_param($item);
              break;
        }

        

        if ( 'hosted' !== $video_type ) {
			$src = $this->eae_get_url( $embed_param, $video_id,$video_type , $item );
		} else {
			$src = $this->eae_get_hosted_video_url($item);
		}
        if(empty($src)){
			return;
		}

        if ( 'hosted' === $video_type ) {
			$video_url = $this->eae_get_hosted_video_url($item);
			ob_start();

			$this->render_hosted_video($item);

			$video_html = ob_get_clean();
			$video_html = wp_json_encode( $video_html );

			$video_html = htmlspecialchars( $video_html, ENT_QUOTES );

			$this->add_render_attribute(
				'eae-video-outer',
				array(
					'data-hosted-html' => $video_html,
				)
			);
		}

        if ( 'hosted' !==  $video_type  ) {
            // echo $video_id;
			$this->add_render_attribute( 'eae-video-thumb', 'src', $this->eae_get_video_thumb( $video_id , $item , $video_type ) );
		}else{
			$thumb = $this->eae_get_hosted_video_url($item);
			$this->add_render_attribute( 'eae-video-thumb', 'src', $thumb );
		}
    
		// self hosted
		if ( 'hosted' === $video_type) {
			$custom_tag = 'video';
		} else {
			$custom_tag = 'img';
		}	

        
        $autoplay = '';
			switch ( $video_type ) {

				case 'youtube':
					$autoplay = ( 'yes' === $item['yt_autoplay'] ) ? '1' : '0';
					break;
	
				case 'vimeo':
					$autoplay = ( 'yes' === $item['vm_autoplay'] ) ? '1' : '0';
					break;
	
				case 'wistia':
					$autoplay = ( 'yes' === $item['wistia_autoplay'] ) ? '1' : '0';
					break;
				case 'hosted':
					$autoplay = ( 'yes' === $item['autoplay'] ) ? '1' : '0';
					break;
	
					
				default:
					break;
		}        
        $this->add_render_attribute('eae-video-outer' , 'class', 'eae-device-video-outer-wrapper ');
		$this->add_render_attribute( 'eae-video-outer', 'class', 'eae-video-type-' . $video_type );
		$this->add_render_attribute( 'eae-video-outer', 'data-video-type',  $video_type );

        if($autoplay){
			$this->add_render_attribute( 'eae-video-outer', 'data-autoplay', $autoplay );
		}

        if($video_type != 'hosted'){
            $this->add_render_attribute( 'eae-video-wrapper', 'data-src', $src );
        }
        $this->add_render_attribute( 'eae-video-wrapper', 'class', ['eae-device-video-wrapper', 'eae-device-video-play'] );
        

        if(empty($src)){ ?>
			<div class= "message">
				<p class="elementor-alert elementor-alert-warning">No video added. Please add video.</p>
			</div>
		<?php }
		else{
        ?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'eae-video-outer' ) ); ?>>
       
			<div <?php echo $this->get_render_attribute_string( 'eae-video-wrapper' ); ?> >
            <<?php echo esc_attr( $custom_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'eae-video-thumb' ) ); ?>></<?php echo esc_attr( $custom_tag ); ?>>
                
			</div>
        </div>
        <?php
        } 
    }

    public function getDailyMotionId($url)
	{

		if (preg_match('!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!', $url, $m)) {
			if (isset($m[6])) {
				return $m[6];
			}
			if (isset($m[4])) {
				return $m[4];
			}
			return $m[2];
		}
		return false;
	}

	protected function getStringBetween( $url, $from, $to ) {
		$sub = substr( $url, strpos( $url, $from ) + strlen( $from ), strlen( $url ) );
		$id  = substr( $sub, 0, strpos( $sub, $to ) );

		return $id;
	}
    
    public function youtube_embed_params($item){
        
		$settings = $this->get_settings_for_display();
		$youtube_options = array( 'autoplay', 'controls', 'mute', 'modestbranding' );
		foreach ( $youtube_options as $option ) {
			if ( 'autoplay' === $option ) {
				if ( 'yes' === $item['yt_autoplay'] ) {
					$params[ $option ] = 1;
				}
				continue;
			}
			$value             = ( 'yes' === $settings[ 'yt_' . $option ] ) ? 1 : 0;
			$params[ $option ] = $value;
			$params['start']   = $item['start'];
			$params['end']     = $item['end'];
		}	
		$params = apply_filters( 'eae_youtube_params', $params );
		return $params;
	}

	public function vimeo_embed_params($item){

		$vimeo_options = array( 'autoplay', 'loop', 'title', 'portrait', 'byline', 'muted' );
			foreach ( $vimeo_options as $option ) {
				if ( 'autoplay' === $option ) {
					if ( 'yes' === $item['vm_autoplay'] ) {
						$params[ $option ] = 1;
					}
					continue;
				}
				$value             = ( 'yes' === $item[ 'vm_' . $option ] ) ? 1 : 0;
				$params[ $option ] = $value;
			}
			// $params['color']     = str_replace( '#', '', $item['vm_color'] );
			$params['autopause'] = '0';
			$params = apply_filters( 'eae_vimeo_params', $params );
			return $params;
	}

    public function wistia_embed_param($settings){
        $wistia_options = array( 'muted', 'playbar', 'loop' );
            foreach ( $wistia_options as $option ) {
                if ( 'loop' === $option ) {
                    if ( 'yes' === $settings['wistia_loop'] ) {
                        $params['endVideoBehavior'] = 'loop';
                    }
                    continue;
                }

                $value             = ( 'yes' === $settings[ 'wistia_' . $option ] ) ? 'true' : 'false';
                $params[ $option ] = $value;
            }
        $params['videoFoam'] = 'true';
        $params = apply_filters( 'eae_wistia_params', $params );
        return $params;

    }

    public function get_video_id($video_type, $item){
		$id       = '';
		$url      = $item[$video_type.'_link' ];

		if ( 'youtube' === $video_type ) {
            // echo strpos($url,'shorts');
            if(strpos($url,'shorts') !== false){
                $pattern = '/\/shorts\/([A-Za-z0-9_-]+)/';
                preg_match($pattern, $url, $matches);
                if (count($matches) > 1) {
                    $id = $matches[1];
                }
            }else{
                if ( preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches ) ) {
                    $id = $matches[1];	
                }
            }
            
		} elseif ( 'vimeo' === $video_type ) {
			if ( preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs ) ) {
				$id = $regs[3];
			}
		} elseif ( 'wistia' === $video_type ) {
			$id = $this->getStringBetween( $url, 'wvideo=', '"' );
		}elseif('dailymotion' === $video_type) {
            echo "dailymotion id done";
			$id = $this->getDailyMotionId($url);
		}
       
		return $id;
	}
    
    private function eae_get_hosted_video_url($item) {
		if ( $item['insert_link'] == 'yes') {
			$video_url = $item['external_link']['url'];
		} else {
			$video_url = isset( $item['hosted_link']['url'] ) ? $item['hosted_link']['url'] : '';
		}
		
		if ( empty( $video_url ) ) {
			return '';
		}
		if ( $item['start'] || $item['end'] ) {
			$video_url .= '#t=';
		}

		if ( $item['start'] ) {
			$video_url .= $item['start'];
		}

		if ( $item['end'] ) {
			$video_url .= ',' . $item['end'];
		}
		return $video_url;
	}

    protected function eae_get_url( $params, $id, $video_type , $item ) {
		$url      = '';

		if ( 'vimeo' === $video_type ) {
			$url = 'https://player.vimeo.com/video/';

		} elseif ( 'youtube' === $video_type ) {
			$url = 'https://www.youtube.com/embed/' ;

		} elseif ( 'wistia' ===  $video_type ) {
			$url = 'https://fast.wistia.net/embed/iframe/';
		}		
		$url = add_query_arg( $params, $url . $id );	
		$url .= ( empty( $params ) ) ? '?' : '&';
	
		$url .= 'autoplay=1';
		if ( 'vimeo' ===  $video_type && '' !==  $item['start'] ) {
			$time = gmdate( 'H\hi\ms\s',  $item['start'] );
			$url .= '#t=' . $time;
		} elseif ( 'vimeo' ===  $video_type ) {
			$url .= '#t=';
		}
		$url = apply_filters( 'eae_video_url_filter', $url, $id );
		return $url;
	}

    public function get_video_link( $video_type , $item ) {	
		$video_link = '';
		switch ( $video_type ) {
			case 'youtube': $video_link = $item['youtube_link'];
							break;
			case 'vimeo':   $video_link = $item['vimeo_link'];
							break;
			case 'dailymotion':   $video_link = $item['dailymotion_link'];
							break;
			case 'wistia':  $video_link = ( preg_match( '/https?\:\/\/[^\",]+/i', $item['wistia_link'], $url ) ) ? $url[0] : '';
							break;
			case 'hosted':  if ( 'hosted' === $video_type && 'yes' !== $item['insert_link'] ) {
								$video_link = $item['hosted_link']['url'];
							} elseif ( 'hosted' === $video_type && 'yes' === $item['insert_link'] ) {
								$video_link = $item['external_link']['url'];
							}
							break;
			default:
		}
		return $video_link;
	}

    public function render_hosted_video($item)
	{
		$video_url = $this->eae_get_hosted_video_url($item);  
		$video_params = $this->get_hosted_parameter($item);
        $video_html = '';

        if ( ! empty( $video_url ) ) {
			?>
			<video class="eae-hosted-video" src="<?php echo esc_url( $video_url ); ?>" <?php  echo esc_attr( Utils::render_html_attributes( $video_params ) ); ?>></video>
			<?php	
		}	
	}
    private function get_hosted_parameter($item) {
        $parmas = [
            'loop',
            'autoplay',
        ];
      
		if($item['autoplay']=='yes'){
			$video_params[ 'autoplay' ] = 'yes';
		}else{
			$video_params[ 'autoplay' ] = 'no';
		}
		if($item['loop']=='yes'){
			$video_params[ 'loop' ] = 'yes';
		}else{
			$video_params[ 'loop' ] = 'no';
		}
		if($item['controls']=='yes'){
			$video_params[ 'controls' ] = 'yes';
		}else{
			$video_params[ 'loop' ] = 'no';
		}
		return $video_params;
	}

    protected function eae_get_video_thumb( $id , $item , $video_type ) {

       

		if ( '' === $id ) {
			return '';
		}
		$thumb    = '';
		
			if ( 'youtube' === $video_type ) {
				$thumb = 'https://i.ytimg.com/vi/' . $id . '/' . apply_filters( 'eae_video_youtube_image_quality', 'hqdefault.jpg');
			} 
            elseif ( 'vimeo' === $video_type ) {
				$response = wp_remote_get( "https://vimeo.com/api/v2/video/$id.php" );
				if ( is_wp_error( $response ) || 404 === $response['response']['code'] ) {
					return;
				}
				$vimeo = maybe_unserialize( $response['body'] );
				// privacy enabled videos don't return thumbnail data.
				$thumb = ( isset( $vimeo[0]['thumbnail_large'] ) && ! empty( $vimeo[0]['thumbnail_large'] ) ) ? str_replace( '_640', '_840', $vimeo[0]['thumbnail_large'] ) : '';

			} 
            elseif ( 'wistia' === $video_type ) {
				$url   = $item['wistia_link'];
				$thumb = 'https://embedwistia-a.akamaihd.net/deliveries/' . $this->getStringBetween( $url, 'deliveries/', '?' );
			}
		return $thumb;
	}
}


