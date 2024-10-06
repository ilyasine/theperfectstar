<?php 
    namespace WTS_EAE\Pro\Modules\VideoGallery\Widgets;

use Elementor\Conditions;
use WTS_EAE\Base\EAE_Widget_Base;
    use Elementor\Repeater;
    use Elementor\Controls_Manager;
    use Elementor\Group_Control_Background;
    use Elementor\Group_Control_Border;
    use Elementor\Group_Control_Box_Shadow;
    use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
    use Elementor\Utils;
    use Elementor\Group_Control_Image_Size;
    use Elementor\Group_Control_Text_Shadow;
    use Elementor\Group_Control_Typography;
    use Elementor\Modules\DynamicTags\Module as TagsModule;
    use Elementor\Plugin;
    use Elementor\Icons_Manager;
    use WTS_EAE\Pro\Pro;
    use WTS_EAE\Classes\Swiper_helper;
    use WTS_EAE\Classes\Helper;
    use WTS_EAE\Classes\Lightgallery_helper;
    use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

    if(! defined('ABSPATH')){
        exit;
    }

    class VideoGallery extends EAE_Widget_Base{
        public function get_name(){
            return 'eae-video-gallery';
        }

        public function get_title()
        {
            return __('EAE - Video Gallery','wts-eae');
        }

        public function get_categories()
        {
            return [ 'wts-eae'];
        }

        public function get_icon() {
            return 'eae-icon eae-video-gallery';
        }

        public function get_script_depends() {

            // load all scripts in editor and preview mode
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
                return [ 'lightgallery-js','lg-thumbnail-js', 'lg-video-js', 'lg-fullscreen-js', 'lg-share-js', 'lg-hash-js', 'lg-zoom-js', 'lg-rotate-js' ];
    
            }
            $scripts  = [];
            $settings = $this->get_settings();
            $scripts[] = 'eae-lottie';
            if( $settings['vg_lightbox_enable'] === 'yes'){
                $scripts[] = 'lightgallery-js';
                $scripts[] = 'lg-video-js';
                $scripts[] = 'lg-fullscreen-js';
                $scripts[] = 'lg-share-js';
                $scripts[] = 'lg-hash-js';
                $scripts[] = 'lgAutoplay-js';
                $scripts[] = 'lg-thumbnail-js';
                foreach($settings['vg_video_Gallery'] as $index => $item ){
                    if($item['video_type'] === 'vimeo'){
                        $scripts[] = 'eae-player-js';
                    }
                    if($item['video_type'] === 'hosted'){
                        $scripts[] = 'eae-video-js';
                    }
                }
            }
            return $scripts;
        }

        public function get_style_depends()
        {
            return ['lightgallery-css','eae-video-css'];
        }

        protected function register_controls()
        {
            $this->start_controls_section(
                'vg_general_section',
                [
                    'label' => esc_html__('General','wts-eae'),
                ]
            );

            $repeater = new Repeater();

            $repeater->add_control(
                'video_type',
                [
                    'label' => esc_html__('Video Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'youtube',
                    'options' =>[
                        'youtube' => esc_html__('YouTube','wts-eae'),
                        'vimeo' => esc_html__('Vimeo','wts-eae'),
                        'wistia' => esc_html__('Wistia','wts-eae'),
                        //'dailymotion' => esc_html__('Dailymotion','wts-eae'),
                        'hosted' => esc_html__('Self Hosted','wts-eae'),
                    ]
                ]
            );

            $repeater->add_control(
                'insert_link',
                [
                    'label' => esc_html__('External URL','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'video_type' => 'hosted',
                    ]
                ]
            );

            $repeater->add_control(
                'hosted_link',
                [
                    'label' => esc_html__('Choose File','wts-eae'),
                    'type' => Controls_Manager::MEDIA,
                    'dynamic' => [
                        'active' => true,
                        'categories' => [
                            TagsModule:: MEDIA_CATEGORY,
                        ]
                    ],
                    'media_type' => 'video',
                    'condition' => [
                        'video_type' => 'hosted',
                        'insert_link' => '',
                    ]
                ]
            );

            $repeater->add_control(
                'external_link',
                [
                    'label' => esc_html__('Url','wts-eae'),
                    'type'         => Controls_Manager::URL,
					'autocomplete' => false,
					'options'      => false,
					'label_block'  => true,
					'show_label'   => false,
                    'dynamic' => [
                        'active'     => true,
						'categories' => [
                            TagsModule::POST_META_CATEGORY,
                            TagsModule::URL_CATEGORY,
                        ],
                    ],
                    'media_type'   => 'video',
					'placeholder'  => __( 'Enter your URL', 'wts-eae' ),
					'condition'    => array(
						'video_type'  => 'hosted',
						'insert_link' => 'yes',
					),
                ]
            );

            $default_youtube = apply_filters( 'eae_video_default_youtube_link', 'https://www.youtube.com/watch?v=XoIWJDPsLBk' );

			$default_vimeo = apply_filters( 'eae_video_default_vimeo_link', 'https://vimeo.com/838671376' );

			$default_wistia = apply_filters( 'eae_video_default_wistia_link', '<p><a href="https://wpvwebmaster.wistia.com/medias/82tscaz1gr?wvideo=82tscaz1gr"><img src="https://embed-ssl.wistia.com/deliveries/778f315db911722d46d7ae50d4d567f23ce009c5.jpg?image_play_button_size=2x&amp;image_crop_resized=960x540&amp;image_play_button=1&amp;image_play_button_color=174bd2e0" width="400" height="225" style="width: 400px; height: 225px;"></a></p><p><a href="https://wpvwebmaster.wistia.com/medias/82tscaz1gr?wvideo=82tscaz1gr">Time Lapse of the Milky Way over the Beach</a></p>' );
																			   
			$default_dailymotion = apply_filters( 'eae_video_default_dailymotion_link', 'https://www.dailymotion.com/video/k1T8t2sBqRaROEzevL8' );

            $repeater->add_control(
                'youtube_link',
                [
                    'label' => esc_html__('Link'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'dynamic' => [
                        'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
                    ],
                    'default' => $default_youtube,
                    'condition'   => array(
						'video_type' => 'youtube',
					),
                ]
            );

            $repeater->add_control(
				'youtube_link_helper',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( '<div class="eae-notice "><b>Valid Format:</b>&nbsp;https://www.youtube.com/watch?v=XHOmBV4js_E</br><b>Invalid Format:</b>&nbsp;https://youtu.be/XHOmBV4js_E</div>', 'wts-eae' ) ),
					'condition'       => [
						'video_type' => 'youtube',
					],	
					'separator'       => 'none',
                ]
			);

            $repeater->add_control(
                'vimeo_link',
                [
                    'label' => esc_html__('Link','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                        'categories' => [
                            TagsModule::POST_META_CATEGORY,
                            TagsModule::URL_CATEGORY,
                        ]
                    ],
                    'label_block' => true,
                    'default' => $default_vimeo,
                    'condition' => [
                        'video_type' => 'vimeo',
                    ]
                ]
            );

            $repeater->add_control(
                'vimeo_link_helper',
                [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf( __( '<div class="eae-notice"><b>Valid Format:</b>&nbsp;https://vimeo.com/235215203</br><b>Invalid Format:</b>&nbsp; https://vimeo.com/channels/{channel_id}/{video_id}</div>', 'wts-eae' ) ),
                    'condition' => [
                        'video_type' => 'vimeo',
                    ],
                    'separator' => 'none',
                ]
            );

            $repeater->add_control(
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
                    'default' => $default_wistia,
                    'label_block' => true,
                    'condition' => [
                        'video_type' => 'wistia', 
                    ] 
                ]
            );

            $repeater->add_control(
                'wistia_link_helper',
                [
                    'type' => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( '<div class="eae-notice">Go to your Wistia video, right click, "Copy Link & Thumbnail" and paste here. </div>', 'wts-eae' ) ),
                    'condition' => [
                        'video_type' => 'wistia',
                    ],
                    'separator' => 'none'
                ]
            );

            $repeater->add_control(
                'dailymotion_link',
                [
                    'label' => esc_html__('Link','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                        'categories' => [
                            TagsModule::POST_META_CATEGORY,
                            TagsModule::URL_CATEGORY,
                        ]
                    ],
                    'placeholder' => esc_html__('Enter your URL','wts-eae') . ' (Dailymotion)',
                    'label_block' => true,
                    'default' => $default_dailymotion,
                    'condition' => [
                        'video_type' => 'dailymotion',
                    ],
                ]
            );

            $repeater->add_control(
                'dailymotion_link_helper',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf( __( '<div class="eae-notice"><b>Valid Format:</b>&nbsp;https://www.dailymotion.com/video/x6tqhqb<b>Invalid Format:</b>&nbsp;https://dailymotion.com/video={video_id}</div>', 'wts-eae' ) ),
                    'condition'       => [
                        'video_type' => 'dailymotion',
                    ],
                    'separator'       => 'none',
                ]
            );

            $repeater->add_control(
                'vg_details',
                [
                    'label' => esc_html__('Video Details','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'separater' => 'before',
                ]
            );

            $repeater->add_control(
                'vg_title',
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'vg_details' => 'yes'
                    ]
                ]
            );

            $repeater->add_control(
                'vg_description',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'vg_details' => 'yes'
                    ]
                ]
            );

            $repeater->add_control(
                'vg_video_category',
                [
                    'label' => esc_html__('Filter Category','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
            );

            $repeater->add_control(
				'yt_thumbnail_size',
				[
					'label'     => __( 'Thumbnail Size', 'wts-eae' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'maxresdefault' => esc_html__( 'Maximum Resolution', 'wts-eae' ),
						'hqdefault'     => esc_html__( 'High Quality', 'wts-eae' ),
						'mqdefault'     => esc_html__( 'Medium Quality', 'wts-eae' ),
						'sddefault'     => esc_html__( 'Standard Quality', 'wts-eae' ),
					],
					'default'   => 'maxresdefault',
					'condition' => [
						'video_type' => 'youtube',
					],
                ]
			);

            $repeater->add_control(
                'vg_video_thumbnail',
                [
                    'label' => esc_html__('Custom Thumbnail'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );

            $repeater->add_control(
                'vg_video_thumbnail_image',
                [
                    'label' => esc_html__('Select Image','wts-eae'),
                    'type' => Controls_Manager::MEDIA,
                    'default'   => [
						'url' => Utils::get_placeholder_image_src(),
					],
                    'dynamic'   => [
						'active' => true,
					],
                    'condition' => [
                        'vg_video_thumbnail' => 'yes'
                    ]
                ]
            );

            $repeater->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name'      => 'vg_video_thumbnail_image',
					'default'   => 'full',
					'separator' => 'none',
					'condition' => [
						'vg_video_thumbnail' => 'yes',
					],
                ]
			);

            $repeater->add_control(
                'start',
                [
                    'label' => esc_html__('Start Time','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'description' => esc_html__( 'Specify a start time (in seconds)', 'wts-eae' ),
                    'condition' => [
                        'video_type' => ['youtube','vimeo','hosted','dailymotion'],
                    ]
                ]
            );

            $repeater->add_control(
                'end',
                [
                    'label' => esc_html__('End Time','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'description' => esc_html__( 'Specify an end time (in seconds)', 'wts-eae' ),
                    'condition' => [
                        'video_type' => ['youtube','hosted'],
                    ]
                ]
            );

            $repeater->add_control(
                'yt_rel',
                [
                    'label' => esc_html__('Related Video From','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'on',
                    'options' => [
                        'no' => esc_html__('Current Video Channel','wts-eae'),
                        'yes' => esc_html__('Any Random Video','wts-eae'),
                    ],
                    'condition' => [
                        'video_type' => 'youtube',
                    ]
                ]
            );

            $repeater->add_control(
                'yt_controls',
                [
                    'label' => esc_html__('Play Control','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => esc_html__('Hide','wts-eae'),
                    'label_on' => esc_html__('Show','wts-eae'),
                    'default' => 'yes',
                    'condition' => [
                        'video_type' => 'youtube',
                    ]
                ]
            );

            $repeater->add_control(
                'yt_mute',
                [
                    'label' => esc_html__('Mute','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'video_type' => 'youtube',
                    ],
                    'return_value' => 'yes',
                ]
            );

            $repeater->add_control(
				'yt_modestbranding',
				[
					'label'       => esc_html__( 'Modest Branding', 'wts-eae' ),
					'description' => esc_html__( 'This option lets you use a YouTube player that does not show a YouTube logo.', 'wts-eae' ),
					'type'        => Controls_Manager::SWITCHER,
					'condition'   => [
						'video_type'  => 'youtube',
						'yt_controls' => 'yes',
					],
                ]
			);

            $repeater->add_control(
				'yt_privacy',
				[
					'label'       => esc_html__( 'Privacy Mode', 'wts-eae' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => esc_html__( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'wts-eae' ),
					'condition'   => [
						'video_type' => 'youtube',
					],
                ]
			);

            $repeater->add_control(
				'dailymotion_mute',
				[
					'label'     => __( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'video_type' => 'dailymotion',
					],
                ]
			);

            $repeater->add_control(
				'dailymotion_controls',
				[
					'label'     => esc_html__( 'Player Control', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => esc_html__( 'Hide', 'wts-eae' ),
					'label_on'  => esc_html__( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => [
						'video_type' => 'dailymotion',
					],
                ]
			);

            $repeater->add_control(
				'dailymotion_sharing-enable',
				[
					'label'     => esc_html__( 'Enable Sharing', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'video_type' => 'dailymotion',
					],
                ]
			);

            $repeater->add_control(
				'vimeo_loop',
				[
					'label'     => esc_html__( 'Loop', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'video_type' => 'vimeo',
					],
                ]
			);

            $repeater->add_control(
				'vimeo_muted',
				[
					'label'     => esc_html__( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'video_type' => 'vimeo',
					],
                ]
			);

            $repeater->add_control(
				'vimeo_title',
				[
					'label'     => esc_html__( 'Intro Title', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => esc_html__( 'Hide', 'wts-eae' ),
					'label_on'  => esc_html__( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => [
						'video_type' => 'vimeo',
					],
                ]
			);

            $repeater->add_control(
				'vimeo_portrait',
				[
					'label'     => esc_html__( 'Intro Portrait', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => esc_html__( 'Hide', 'wts-eae' ),
					'label_on'  => esc_html__( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => [
						'video_type' => 'vimeo',
					],
                ]
			);

            $repeater->add_control(
				'vimeo_byline',
				[
					'label'     => esc_html__( 'Intro Byline', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => esc_html__( 'Hide', 'wts-eae' ),
					'label_on'  => esc_html__( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => [
						'video_type' => 'vimeo',
					],
                ]
			);

            $repeater->add_control(
                'vimeo_color',
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
					'condition' => array(
						'video_type' => 'vimeo',
					),
                ]
            );

            $repeater->add_control(
				'wistia_loop',
				[
					'label'     => esc_html__( 'Loop', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'video_type' => 'wistia',
					],
                ]
			);

            $repeater->add_control(
				'wistia_muted',
				[
					'label'     => esc_html__( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'video_type' => 'wistia',
					],
                ]
			);

            $repeater->add_control(
				'wistia_playbar',
				[
					'label'     => esc_html__( 'Show Playbar', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => [
						'video_type' => 'wistia',
					],
					'default'   => 'yes',
                ]
			);

            $repeater->add_control(
                'loop',
                [
                    'label' => esc_html__('Loop','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'condition' => [
                        'video_type' => 'hosted',
                    ]
                ]
            );

            $repeater->add_control(
                'controls',
                [
                    'label' => esc_html__('Player Control','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => esc_html__('Hide','wts-eae'),
                    'label_on' => esc_html__('Show','wts-eae'),
                    'default' => 'yes',
                    'return_value' => 'yes',
                    'condition' => [
                        'video_type' => 'hosted',
                    ]
                ]
            );

            $repeater->add_control(
                'muted',
                [
                    'label' => esc_html__('Mute','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'video_type' => 'hosted',
                    ],
                    'return_value' => 'yes',
                ]
            );

            $repeater->add_control(
                'download_button',
                [
                    'label' => esc_html__('Download Button','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => esc_html__('Hide','wts-eae'),
                    'label_on' => esc_html__('Show','wts-eae'),
                    'condition' => [
                        'video_type' => 'hosted',
                    ]
                ]
            );

            $repeater->add_control(
                'schema_support',
                [
                    'label' => esc_html__('Video Schema','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'separator' => 'before',
                ]
            );

            $repeater->add_control(
                'schema_title',
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default'     => esc_html__( 'Title of the video.', 'wts-eae' ),
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'schema_support' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'schema_description',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::TEXT,
				    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'schema_support' => 'yes',
                    ]
                ]
            );

            $repeater->add_control(
                'schema_thumbnail',
                [
                    'label'     => esc_html__( 'Video Thumbnail', 'wts-eae' ),
                    'type'      => Controls_Manager::MEDIA,
                    'default'   => array(
                        'url' => Utils::get_placeholder_image_src(),
                    ),
                    'condition' => array(
                        'schema_support'      => 'yes',
                        'vg_video_thumbnail!' => 'yes',
                    ),
                ]
            );

            $repeater->add_control(
                'schema_upload_date',
                [
                    'label' => esc_html__('Upload Date & Time'),
                    'type' => Controls_Manager::DATE_TIME,
                    'placeholder' => __( 'yyyy-mm-dd', 'wts-eae' ),
				    'default'     => gmdate( 'Y-m-d H:i' ),
                    'condition' => [
                        'schema_support' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'vg_layout',
                [
                    'label' => esc_html__('Layout','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'grid',
                    'options' => [
                        'grid' => esc_html__('Grid','wts-eae'),
                        'carousel' => esc_html__('Carousel','wts-eae'),
                    ]
                ]
            );

            $this->add_control(
                'vg_layout_filter',
                [
                    'label' => esc_html__('Enable Filter','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'vg_layout',
                                'operator' => '==',
                                'value' => 'grid',
                            ],
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value'=>'custom_list'
                            ],
                        ]
                    ]
                ]
            );

            $this->add_control(
                'vg_lightbox_enable',
                [
                    'label' =>esc_html__('Lightbox Enable','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                ]
            );
            
            $this->add_control(
                'vg_video_list_type',
                [
                    'label' => esc_html__('Source','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'youtube_channel_videos' => esc_html__('Youtube Channel','wts-eae'),
                        'youtube_playlist' => esc_html__('Youtube Playlist','wts-eae'),
                        'custom_list' => esc_html__('Custom List','wts-eae'),
                    ],
                    'default' => 'custom_list',
                ]
            );

            $youtube_api_key = get_option('wts_eae_youtube_api_key');
            
            $message = '';
            if(empty($youtube_api_key)){
                $message = sprintf(esc_html__('Add your youtube api key in %s','wts-eae'),'<a href="'.admin_url('admin.php?page=eae-settings#eae-config').'">'.esc_html__('Settings','wts-eae').'</a>');
            
            $this->add_control(
                'youtube_api_key_notice',
                array(
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             =>  __('<div class="eae-notice">'.$message.'</div>', 'wts-eae' ),
                    'condition'       => array(
                        'vg_video_list_type' => ['youtube_playlist', 'youtube_channel_videos'],
                    ),
                    'separator'       => 'none',
                    
                )
            );
        }

            $this->add_control(
                'vg_youtube_playlist_id',
                [
                    'label' => esc_html__('Playlist Id','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'vg_video_list_type' => 'youtube_playlist',
                    ]
                ]
            );

            $this->add_control(
                'vg_youtube_channel_id',
                [
                    'label' => esc_html__('Channel ID','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    // 'label_block' => true,
                    'label_block' => true,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'vg_video_list_type' => 'youtube_channel_videos',
                    ]
                ]
            );

            $this->add_control(
                'vg_number_of_videos',
                [
                    'label' => esc_html__('Number Of Videos','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => 10,
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value' => 'youtube_channel_videos',
                            ],
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value'=>'youtube_playlist'
                            ],
                        ]
                    ]
                ]
            );

            $this->add_control(
                'vg_videos_order',
                [
                    'label' => esc_html__('Video Order','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'condition' => [
                        'vg_video_list_type' => 'youtube_channel_videos',
                    ]
                ]
            );

            $this->add_control(
                'vg_videos_title_enable',
                [
                    'label' => esc_html__('Video Title Enable','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value' => 'youtube_channel_videos',
                            ],
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value'=>'youtube_playlist'
                            ],
                        ]
                    ]
                ]
            );

            $this->add_control(
                'vg_reload_video',
                [
                    'label' => esc_html__('Reload Videos (Minutes)','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => '120',
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value' => 'youtube_channel_videos',
                            ],
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value'=>'youtube_playlist'
                            ],
                        ]
                    ]
                ]
            );

            $this->add_control(
                'vg_video_Gallery',
                [
                    'label' => esc_html__('Videos','wts-eae'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'default' =>[
                        [
                            'video_type' => 'youtube',

                        ],
                        [
                            'video_type' => 'wistia',
                        ],
                        [
                            'video_type' => 'vimeo',
                        ],
                    ],
                    'condition' => [
                        'vg_video_list_type' => 'custom_list'
                    ]
                ]
            );

            Helper::eae_media_controls(
                $this,[
                    'name' => 'vg_icon',
                    'label' => 'Icon',
                    'icon'			=> true,
                    'image'			=> true,
                    'lottie'		=> true,
                    'defaults'      => [
                        'graphic_type_default' => 'icon',
                        'graphic_icon_default' => [
                            'value' => 'fas fa-play-circle',
                            'library' => 'fa-solid'
                        ],
                    ]
                ]
            );

            $this->end_controls_section();

            $this->get_lightbox_section();

            $this->get_filter_setting_section();

            $this->get_settings_section();

            $this->get_carousel_setting_section();

            $this->get_video_details_style_section();

            $this->get_video_style_section();

            $this->get_filter_style_section();

            $this->get_carousel_style_section();

            $this->get_icon_style_section();

        }

        public function get_icon_style_section(){
            $this->start_controls_section(
                'vg_icon_style_section',
                [
                    'label' => esc_html__('Icon'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            Helper::global_icon_style_controls(
                $this,
                [
                    'name'          => 'vg_icon',
                    'selector'      => '.eae-vg-icon',
                    'is_repeater'   => 'false', 
                ]
            );

            $this->end_controls_section();
        }

        public function get_lightbox_section(){
            $this->start_controls_section(
                'vg_lightbox_section',
                [
                    'label' => esc_html__('LightBox','wts-eae'),
                    'condition' => [
                        'vg_lightbox_enable' => 'yes',
                    ]
                ]
            );

            Lightgallery_helper::add_controls($this, ['video']);

            $this->start_injection([
                'of' => 'lightgallery_autoplayVideoOnSlide',
            ]);
            $this->add_control(
                'lightbox_video_mute',
                [
                    'label' => esc_html__( 'Mute', 'wts-eae' ),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'description' => esc_html__('To enable autoplay, you must enable the mute option.','wts-eae'),
                    'condition' => [
                        'vg_video_list_type!' => 'custom_list',
                    ]
                ]
            );
            $this->end_injection();

            $this->end_controls_section();
        }

        public function get_filter_setting_section(){
            $this->start_controls_section(
                'vg_filter_settings_section',
                [
                    'label' => esc_html__('Filter','wts-eae'),
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'vg_layout_filter',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value'=>'custom_list'
                            ],
                            [
                                'name' => 'vg_layout',
                                'operator' => '==',
                                'value' => 'grid',
                            ]
                        ]
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_title',
                [
                    'label' => esc_html__('Heading','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'vg_layout_filter' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_title_html_tag',
                [
                    'label' => esc_html__('Filter Title HTML Tag','wts-eae'),
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
                    'condition' => [
                        'vg_layout_filter' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'vg_filterable_all_label_text',
                [
                    'label' => esc_html__("'ALL' Category Text",'wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('All','wts-eae'),
                    'condition' => [
                        'vg_layout_filter' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_collapse',
                [
                    'label' => esc_html__('Collapse Filter','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'condition' => [
                        'vg_layout_filter' => 'yes',
                    ]
                ]
            );

            $this->add_responsive_control(
                'vg_filter_collapse_after',
                [
                    'label' => esc_html__('Collapse After','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => '4',
                    'condition' => [
                        'vg_filter_collapse' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_collapse_text',
                [
                    'label' => esc_html__('Dropdown Button Text','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'default' => esc_html__('Others','wts-eae'),
                    'dynamic' => [
                        'active' => true,
                    ],
                    'condition' => [
                        'vg_filter_collapse' => 'yes',
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_collapse_icon',
                [
                    'label' => esc_html__('Icon','wts-eae'),
                    'type' => Controls_Manager::ICONS,
                    'default' => [
                        'value'   => 'fas fa-chevron-down',
                        'library' => 'fa-solid',
                    ],
                    'condition' => [
                        'vg_layout_filter' => 'yes',
                    ]
                ]
            );

            $this->end_controls_section();
        }

        public function get_video_style_section(){
            $this->start_controls_section(
                'vg_video_style_section',
                [
                    'label' => esc_html__('Video' ,'wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'vg_video_background',
                    'selector' => '{{WRAPPER}} .eae-vg-element-wrapper',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'vg_video_container_shadow',
                    'selector' => '{{WRAPPER}} .eae-vg-element-wrapper',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'vg_video_container_hover_shadow',
                    'label' => esc_html__('Hover Box Shadow','wts-eae'),
                    'selector' => '{{WRAPPER}} .eae-vg-element-wrapper:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'vg_video_container_border',
                    'selector' => '{{WRAPPER}} .eae-vg-element-wrapper',
                ]
            );

            $this->add_responsive_control(
                'vg_video_container_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%','em','rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-element-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'vg_thumbnail_overlay_color_heading',
                [
                    'label' => esc_html__('Overlay'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'vg_overlay_color',
                    'selector' => '{{WRAPPER}} .eae-vg-image-overlay:before' 
                ]
            );

            $this->end_controls_section();
        }

        public function get_video_details_style_section(){
            $this->start_controls_section(
                'vg_video_detail_style_section',
                [
                    'label' => esc_html__('Video Detail','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            	[
                               	 	'name' => 'vg_video_list_type',
                                	'operator' => '==',
                                	'value' => 'custom_list',
                            	],
                            	[
                                	'name' => 'vg_videos_title_enable',
                                	'operator' => '==',
                                	'value'=>'yes'
                            	],
                        ]
                    ]
                ]
            );

            $this->add_control(
                'vg_title_style_heading',
                [
                    'label' => esc_html__('Title','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'vg_title_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-title' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'vg_title_typography',
                    'selector' => '{{WRAPPER}} .eae-vg-title',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'vg_title_shadow',
                    'selector' => '{{WRAPPER}} .eae-vg-title',
                ]
            );

            $this->add_control(
                'vg_description_style_heading',
                [
                    'label' => esc_html__('Description','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'vg_video_list_type' => 'custom_list',
                    ],
                ]
            );

            $this->add_control(
                'vg_description_color',
                [
                    'label' => esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
                    'condition' => [
                        'vg_video_list_type' => 'custom_list',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-description' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'vg_description_typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'condition' => [
                        'vg_video_list_type' => 'custom_list',
                    ],
                    'selector' => '{{WRAPPER}} .eae-vg-description',
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'vg_description_text_shadow',
                    'selector' => '{{WRAPPER}} .eae-vg-description',
                    'condition' => [
                        'vg_video_list_type' => 'custom_list',
                    ],
                ]
            );

            $this->add_responsive_control(
                'vg_video_details_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%'],
                    'separator' => 'before',
                    'condition' => [
                        'vg_video_list_type' => 'custom_list',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-video-details-always-visible' => 'gap:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-vg-video-details-on-hover' => 'gap:{{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .eae-vg-video-details-below-video' => 'gap:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_control(
                'vg_video_text_alignment',
                [
                    'label' => esc_html__('Alignment','wts-eae'),
                    'type' =>Controls_Manager::CHOOSE,
                    'default' => 'center',
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
                    ],
                    'toggle' => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-title' => 'text-align:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-description' => 'text-align:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'vg_video_detail_container_overlay_background_color',
                    'selector' => '{{WRAPPER}} .eae-vg-video-details-on-hover , {{WRAPPER}} .eae-vg-video-details-always-visible',
                    'condition' => [
                        'vg_video_details_layout!' => 'below-video',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'vg_video_detail_container_background',
                    'selector' => '{{WRAPPER}} .eae-vg-video-details-below-video',
                    'condition' => [
                        'vg_video_details_layout' => 'below-video',
                    ],
                ]
            );

            $this->add_responsive_control(
                'vg_video_details_container_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager:: DIMENSIONS,
                    'size_units' => ['px','%','em','rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-video-details-below-video' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-vg-video-details-always-visible' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .eae-vg-video-details-on-hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_section();
        }

        public function get_carousel_setting_section(){
            $this->start_controls_section(
                'vg_carousel_setting_section',
                [
                    'label' => esc_html__('Carousel','wts-eae'),
                    'condition' => [
                        'vg_layout' => 'carousel',
                    ]
                ]
            );

            Swiper_helper::carousel_controls($this);

            $this->end_controls_section();
        }

        public function get_carousel_style_section(){
            $this->start_controls_section(
                'vg_carousel_style_section',
                [
                    'label' => esc_html__('Carousel','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'vg_layout' => 'carousel',
                    ]
                ]
            );

            Swiper_helper::carousel_style_section($this);
            Swiper_helper::inject_carousel_controls($this);
            $this->end_controls_section();
        }

        public function get_filter_style_section(){
            $this->start_controls_section(
                'vg_filter_style_section',
                [
                    'label' => esc_html__('Filter','wts-eae'),
                    'tab' => Controls_Manager::TAB_STYLE,
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'vg_layout_filter',
                                'operator' => '==',
                                'value' => 'yes',
                            ],
                            [
                                'name' => 'vg_video_list_type',
                                'operator' => '==',
                                'value'=>'custom_list'
                            ],
                            [
                                'name' => 'vg_layout',
                                'operator' => '==',
                                'value' => 'grid',
                            ]
                        ]
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_heading_style_section',
                [
                    'label' => esc_html__('Heading','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                ]
            );

            $this->add_control(
                'vg_filter_heading_color',
                [
                    'label' => esc_html__('Color','wts-eaes'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filteres-heading' => 'color:{{VALUE}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'vg_filter_heading_typography',
                    'selector' => '{{WRAPPER}} .eae-vg-filteres-heading',
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'vg_filter_heading_text_shadow',
                    'selector' => '{{WRAPPER}} .eae-vg-filteres-heading'
                ]
            );

            $this->add_control(
                'vg_button_style_heading',
                [
                    'label' => esc_html__('Filter','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before', 
                ]
            );

            $this->add_responsive_control(
                'vg_filter_button_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%','em','rem'],
                    'default' => [
                        'size' => '20',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-tab' => 'column-gap:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'vg_filter_typography',
                    'selector' => '{{WRAPPER}} .eae-filter-button, {{WRAPPER}} .eae-vg-filter-dropdown-button',
                ]
            );

            $this->start_controls_tabs('vg_filter_button_tab');

            $this->start_controls_tab(
                'vg_filter_button_normal_tab',
                [
                    'label' => esc_html__('Normal','wts-eae')
                ]
            );

            $this->add_control(
                'vg_button_color',
                [
                    'label' =>esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-filter-button' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-filter-dropdown-button' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-filter-dropdown-button i' => 'color:{{VALUE}};',
                    ] 
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'vg_filter_button_background',
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
                    'selector' => '{{WRAPPER}} .eae-filter-button, {{WRAPPER}} .eae-vg-filter-dropdown',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'vg_filter_button_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-filter-button, {{WRAPPER}} .eae-vg-filter-dropdown',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'vg_filter_button_border',
                    'selector' => '{{WRAPPER}} .eae-filter-button, {{WRAPPER}} .eae-vg-filter-dropdown',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'vg_filter_button_active',
                [
                    'label' => esc_html__('Active','wts-eae'),
                ]
            );

            $this->add_control(
                'vg_button_active_color',
                [
                    'label' =>esc_html__('Color','wts-eae'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#FFFFFF',
                    'selectors' => [
                        '{{WRAPPER}} .eae-filter-button.eae-vg-active-button' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-filter-button:hover' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-filter-dropdown.eae-vg-active-button .eae-vg-dropdown-filter-text' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-filter-dropdown:hover .eae-vg-dropdown-filter-text' => 'color:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-filter-dropdown:hover i' => 'color:{{VALUE}};'
                    ] 
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'vg_filter_active_button_background',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                        'color' => [
                            'default' => '#000000',
                        ]
                    ],
                    'selector' => '{{WRAPPER}} .eae-filter-button.eae-vg-active-button , {{WRAPPER}} .eae-filter-button:hover, {{WRAPPER}} .eae-vg-filter-dropdown:hover, {{WRAPPER}} .eae-vg-filter-dropdown.eae-vg-active-button',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'vg_filter_active_button_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-filter-button.eae-vg-active-button , {{WRAPPER}} .eae-filter-button:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'vg_filter_active_button_border',
                    'selector' => '{{WRAPPER}} .eae-filter-button.eae-vg-active-button , {{WRAPPER}} .eae-filter-button:hover',
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control(
                'vg_filter_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%','em','rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-filter-button , {{WRAPPER}} .eae-vg-filter-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'vg_filter_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%','em','rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-filter-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ] 
                ]
            );

            $this->add_control(
                'vg_dropdown_style_heading',
                [
                    'label' => esc_html__('Dropdown','wts-eae'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
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
                'vg_filter_container_element_gap',
                [
                    'label' => esc_html__('Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%','em','rem'],
                    'separator' => 'before',
                    'default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-button-container' => 'gap:{{SIZE}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'vg_filter_container_vertical_alignment',
                [
                    'label' => esc_html__('Vertical Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'default' => 'row',
                    'tablet_default' => 'column',
                    'mobile_default' => 'column',
                    'options' => [
                        'row' => [
                            'title' => esc_html__('Row'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                        'column' => [
                            'title' => esc_html__('Column'),
                            'icon' => 'eicon-justify-end-v',
                        ],
                    ],
                    'toggle' => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-button-container' => 'flex-direction: {{VALUE}};'
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_container_horizontal_alignment_row',
                [
                    'label' => esc_html__('Horizontal Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'label_block' => true,
                    'default' => 'center',
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
                    'condition' => [
                        'vg_filter_container_vertical_alignment' => 'row'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-button-container' => 'justify-content:{{VALUE}};'
                    ]
                ]
            );

            $this->add_control(
                'vg_filter_container_horizontal_alignment',
                [
                    'label' => esc_html__('Horizontal Alignment','wts-eae'),
                    'type' => Controls_Manager::CHOOSE,
                    'default' => 'center',
                    'options' => [
                        'start' => [
                            'title' => esc_html__('Left'),
                            'icon' => 'eicon-justify-start-h',
                        ],
                        'center' => [
                            'title' => esc_html__('center'),
                            'icon' => 'eicon-justify-center-h',
                        ],
                        'end' => [
                            'title' => esc_html__('Right','wts-eae'),
                            'icon' => 'eicon-justify-end-h',
                        ]
                    ],
                    'condition' => [
                        'vg_filter_container_vertical_alignment' => 'column',
                    ],
                    'toggle' => false,
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-button-container' => 'align-items:{{VALUE}};'
                    ]
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'vg_filter_container_background',
                    'selector' => '{{WRAPPER}} .eae-vg-filter-button-container',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'vg_filter_container_border',
                    'selector' => '{{WRAPPER}} .eae-vg-filter-button-container',
                ]
            );

            $this->add_responsive_control(
                'vg_filter_container_border_radius',
                [
                    'label' => esc_html__('Border Radius','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%','em','rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-button-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'vg_filter_container_padding',
                [
                    'label' => esc_html__('Padding','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%','em','rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-button-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->add_responsive_control(
                'vg_filter_container_margin',
                [
                    'label' => esc_html__('Margin','wts-eae'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px','%','em','rem'],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-filter-button-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ]
                ]
            );

            $this->end_controls_section();
        }

        public function get_settings_section(){

            $this->start_controls_section(
                'vg_settings_section',
                [
                    'label' => esc_html__('Settings','wts-eae'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
            );
            $this->add_responsive_control(
                'vg_columns',
                [
                    'label' => esc_html__('Columns','wts-eae'),
                    'type' => Controls_Manager::NUMBER,
                    'default' => '3',
                    'tablet_default' => '2',
                    'mobile_default' => '1',
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-element-wrapper' => 'width: calc((100% -  ( ({{VALUE}} - 1)  * {{vg_horizontal_gap.size}}px)) / {{VALUE}} );',
                    ],
                    'condition' => [
                        'vg_layout!' => 'carousel',
                    ],
                    'render_type' => 'template',
                ]
            );

            $this->add_responsive_control(
                'vg_horizontal_gap',
                [
                    'label' => esc_html__('Horizontal Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%','em','rem'],
                    'default' => [
                        'size' => '10',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '10',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '5',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-video-container' => 'column-gap:{{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'vg_layout!' => 'carousel',
                    ]
                ]
            );

            $this->add_responsive_control(
                'vg_vertical_gap',
                [
                    'label' => esc_html__('Vertical Gap','wts-eae'),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => ['px','%','em','rem'],
                    'default' => [
                        'size' => '10',
                        'unit' => 'px'
                    ],
                    'tablet_default' => [
                        'size' => '10',
                        'unit' => 'px'
                    ],
                    'mobile_default' => [
                        'size' => '5',
                        'unit' => 'px'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-vg-video-container' => 'row-gap:{{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'vg_layout!' => 'carousel',
                    ]
                ]
            );

            $this->add_control(
                'vg_aspect_ratio',
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
                        '{{WRAPPER}} .eae-vg-element img' => 'aspect-ratio:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-element iframe' => 'aspect-ratio:{{VALUE}};',
                        '{{WRAPPER}} .eae-vg-element video' => 'aspect-ratio:{{VALUE}};',
                    ]
                ]
            );

            $this->add_control(
                'vg_video_details_layout',
                [
                    'label' => esc_html__('Video Details Layout','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'on-hover',
                    'options' => [
                        'on-hover' => esc_html__('On Hover Visible','wts-eae'),
                        'always-visible' => esc_html__('Always Visible','wts-eae'),
                        'below-video' => esc_html__('Below Video','wts-eae'),
                    ],
                    'conditions' => [
                        'relation' => 'or',
                        'terms'    => [
                            	[
                               	 	'name' => 'vg_video_list_type',
                                	'operator' => '==',
                                	'value' => 'custom_list',
                            	],
                            	[
                                	'name' => 'vg_videos_title_enable',
                                	'operator' => '==',
                                	'value'=>'yes'
                            	],
                        ]
                    ]
                ]
            );

            $this->end_controls_section();

        }

        protected function eae_get_video_thumb( $id , $item ) {

            if ( '' === $id ) {
                return '';
            }
            $thumb    = '';
            
            if ( 'yes' === $item['vg_video_thumbnail'] ) {
                $thumb = Group_Control_Image_Size::get_attachment_image_src( $item['vg_video_thumbnail_image']['id'], 'vg_video_thumbnail_image', $item );
                if(empty($thumb)){
                    $thumb = $item['vg_video_thumbnail_image']['url'];
                }
            } else {
                if ( 'youtube' === $item['video_type'] ) {
                    $thumb = 'https://i.ytimg.com/vi/' . $id . '/' . apply_filters( 'eae_video_youtube_image_quality', $item['yt_thumbnail_size'] ) . '.jpg';
                } elseif ( 'vimeo' === $item['video_type'] ) {
                    $response = wp_remote_get( "https://vimeo.com/api/v2/video/$id.php" );
                    if ( is_wp_error( $response ) || 404 === $response['response']['code'] ) {
                        return;
                    }
                    $vimeo = maybe_unserialize( $response['body'] );
                    $thumb = ( isset( $vimeo[0]['thumbnail_large'] ) && ! empty( $vimeo[0]['thumbnail_large'] ) ) ? str_replace( '_640', '_840', $vimeo[0]['thumbnail_large'] ) : '';
    
                } elseif ( 'wistia' === $item['video_type'] ) {
                    $url   = $item['wistia_link'];
                    $thumb = 'https://embedwistia-a.akamaihd.net/deliveries/' . $this->getStringBetween( $url, 'deliveries/', '?' );
                }
                elseif ( 'dailymotion' === $item['video_type']  ) {
                    $video_data = wp_remote_get( 'https://api.dailymotion.com/video/' . $id . '?fields=thumbnail_url' );
                    
                    if ( isset( $video_data['response']['code'] )  && $item['dailymotion_link'] !== '') {
                        if ( 404 === $video_data['response']['code'] ) {
                            return $thumb;
                        }else{
                             $thumb_data = json_decode($video_data['body']);
                             $thumb = $thumb_data->thumbnail_url;
                        }
                    }
                }
            }
            return $thumb;
        }

        public function get_video_details($item){
            $settings = $this->get_settings_for_display();
            $this->set_render_attribute('video-details','class','eae-vg-video-details-'. $settings['vg_video_details_layout']);
            if($settings['vg_video_details_layout'] !== 'below-video'){
                if($item['vg_details'] !== '' && $item['vg_title'] != '' || $item['vg_description'] != '' ){ ?>
                    <div <?php echo $this->get_render_attribute_string('video-details') ?>>
                        <?php if($item['vg_title'] !== ''){ ?>
                            <span class="eae-vg-title"><?php echo Helper::eae_wp_kses($item['vg_title']); ?></span>
                        <?php } ?>
                        <?php if($item['vg_description'] !== ''){ ?>
                            <span class="eae-vg-description"><?php echo Helper::eae_wp_kses($item['vg_description']); ?></span>
                        <?php } ?>
                    </div>
                <?php }
            }else{
                if($item['vg_details'] !== '' && $item['vg_title'] != '' || $item['vg_description'] != '' ){ ?>
                    <div class="eae-vg-video-details-below-video">
                        <?php if($item['vg_title'] !== ''){ ?>
                            <span class="eae-vg-title"><?php echo Helper::eae_wp_kses($item['vg_title']); ?></span>
                        <?php } ?>
                        <?php if($item['vg_description'] !== ''){ ?>
                            <span class="eae-vg-description"><?php echo Helper::eae_wp_kses($item['vg_description']); ?></span>
                        <?php } ?>
                    </div>
                <?php }
            }
        }

        public function get_youtube_video_details($item){
            $settings = $this->get_settings_for_display();
            
            $this->set_render_attribute('video-details','class','eae-vg-video-details-'. $settings['vg_video_details_layout']);
            if($settings['vg_video_details_layout'] !== 'below-video'){
                if( $item->snippet->title != '' || $item->snippet->description != '' ){ ?>
                    <div <?php echo $this->get_render_attribute_string('video-details') ?>>
                        <?php if($item->snippet->title != '' || $item->snippet->description != '' ){ ?>
                            <span class="eae-vg-title"><?php echo $item->snippet->title; ?></span>
                        <?php } ?>
                        <!-- <?php // if($item->snippet->description !== ''){ ?>
                            <span class="eae-vg-description"><?php //echo $item->snippet->description; ?></span>
                        <?php //} ?> -->
                    </div>
                <?php }
            }else{
                if( $item->snippet->title != '' || $item->snippet->description != '' ){ ?>
                    <div class="eae-vg-video-details-below-video">
                        <?php if($item->snippet->title != '' || $item->snippet->description != ''){ ?>
                            <span class="eae-vg-title"><?php echo $item->snippet->title; ?></span>
                        <?php } ?>
                        <!-- <?php //if($item->snippet->description !== ''){ ?> -->
                            <!-- <span class="eae-vg-description"><?php //echo $item->snippet->description; ?></span> -->
                        <!-- <?php //} ?> -->
                    </div>
                <?php }
            }
        }

        public function get_video_link($item){
            $video_type = $item['video_type'];
            $video_link = '';
            switch ( $video_type ) {
                case 'youtube': 
                    $video_link = $item['youtube_link'];
                    break;
                case 'vimeo':   
                    $video_link = $item['vimeo_link'];
                    break;
                case 'dailymotion':   
                    $video_link = $item['dailymotion_link'];
                    break;
                case 'wistia': 
                    $video_link = ( preg_match( '/https?\:\/\/[^\",]+/i', $item['wistia_link'], $url ) ) ? $url[0] : '';
                    break;
                case 'hosted': 
                    if ( 'hosted' === $video_type && 'yes' !== $item['insert_link'] ) {
                        $video_link = $item['hosted_link']['url'];
                    } elseif ( 'hosted' === $video_type && 'yes' === $item['insert_link'] ) {
                        $video_link = $item['external_link']['url'];
                    }
                    break;
                default:
            }
            return $video_link;
        }

        public function get_get_filter_categorie_key($item){
            $data_arr = [];
            $data_key = strtolower($item);
            $data_key = str_replace(' ','',$data_key);
            $data = explode( ',', $item);
            $data = array_map( 'trim', $data );
            foreach($data as $index => $value){
                $data_arr[$data_key] = $value;
            }
            return $data_arr;
        }

        // Update on 4 June 1.3
        public function get_filter_categories(){
            $settings = $this->get_settings_for_display();
            $categories = [];
            $categories_array = [];
            foreach($settings['vg_video_Gallery'] as $index => $item){
                if($item['vg_video_category'] !== ''){
                    $data = $this->get_get_filter_categorie_key($item['vg_video_category']);
                    foreach($data as $key => $value){
                        $categories[$key] = $value;
                    }
                }
            }
            $categories = array_unique($categories);
            return $categories;
        }

        public function get_filter_tabs(){
            $settings = $this->get_settings_for_display();
            $category = [];
            $active_tab = '';
            $collapseAfter = '';
            $i = 0;
            ?><div class="eae-vg-filter-tab"><?php
            if($settings['vg_filterable_all_label_text'] !== ''){
                ?><a class="eae-filter-button eae-vg-active-button" data-filter="all"><?php echo Helper::eae_wp_kses($settings['vg_filterable_all_label_text']) ?></a><?php
            }else{
                $active_tab = 1;
            }

            if($settings['vg_filter_collapse'] == 'yes'){
                $collapseAfter = $settings['vg_filter_collapse_after'];                
            }
            
            $filters_item = $this->get_filter_categories();
            // echo '<pre>';  print_r($filters_item); echo '</pre>';
            // die('dfdf');
            foreach($filters_item as $index => $value){
                if(!empty($value)){
                    $i++;
                    if($settings['vg_filter_collapse'] == 'yes'){
                        if($i > $collapseAfter && $collapseAfter !== '' ){
                            continue;
                        }
                    }
                    if($active_tab == 1){
                        $this->set_render_attribute('vg-filter-button','class',['eae-filter-button','eae-vg-active-button']);
                        $active_tab++;
                    }else{
                        $this->set_render_attribute('vg-filter-button','class','eae-filter-button');
                    }
                    $this->set_render_attribute('vg-filter-button','data-filter', $index);

                    echo sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string('vg-filter-button'), Helper::eae_wp_kses($value));

                }
            }
            if($settings['vg_filter_collapse'] == 'yes'){
                if($collapseAfter !== ''){
                    $remaining_filter_terms = array_slice($filters_item, $collapseAfter);
                }
                if($settings['vg_filter_collapse_icon']['value'] !== '' || $settings['vg_filter_collapse_text'] !== ''){
                    $this->add_render_attribute('filter_collapse_button','data-button-text',$settings['vg_filter_collapse_text']);
                    $this->add_render_attribute('filter_collapse_button','class','eae-vg-filter-dropdown eae-vg-collapse');
                    ?>      
                        <!-- to fix the Anchor Tag Security bug we have to fix that data-button-text attribute on this div -->
                        <div <?php echo $this->get_render_attribute_string('filter_collapse_button'); ?>>
                            <a  class="eae-vg-filter-dropdown-button eae-vg-splite-data" data-term-id="other">
                                <?php if($settings['vg_filter_collapse_text'] !== ''){ ?>
                                    <span class="eae-vg-dropdown-filter-text "><?php echo Helper::eae_wp_kses($settings['vg_filter_collapse_text']); ?></span>    
                                <?php }
                                $icon     =  $settings['vg_filter_collapse_icon'] ?? false;
                                if($icon){
                                    Icons_Manager::render_icon( $icon );
                                } ?>
                            </a>
                            <ul class="eae-vg-collaps-item-list">
                                <?php
                                    
                                    if($collapseAfter !== ''){
                                        foreach($remaining_filter_terms as $index => $value){
                                            $this->set_render_attribute('filter_drop_down_element', 'class', 'eae-vg-filters-item');
                                            $this->add_render_attribute('vg-filter-button-' . $index,'class', 'eae-filter-button');
                                            $this->add_render_attribute('vg-filter-button-' . $index,'data-filter',$index);
                                            ?>
                                                <li <?php echo $this->get_render_attribute_string('filter_drop_down_element'); ?>>
                                                    <?php echo sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string('vg-filter-button-' . $index), Helper::eae_wp_kses($value)); ?>
                                                </li> 
                                            <?php 
                                        }
                                    } 
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                }
            }else{
                ?></div><?
            }
        
            if($settings['vg_filter_collapse_icon']['value'] !== '' || $settings['vg_filter_collapse_text'] !== ''){
                ?>
                    <div class="eae-vg-filter-tab eae-vg-dropdown-tab"> 
                        <div class="eae-vg-filter-dropdown" data-button-text = "<?php echo esc_attr($settings['vg_filter_collapse_text']); ?>" >
                            <a class="eae-vg-filter-dropdown-button " data-term-id="other">
                                <?php if($settings['vg_filter_collapse_text'] !== ''){ ?>
                                    <span class="eae-vg-dropdown-filter-text "><?php echo Helper::eae_wp_kses($settings['vg_filter_collapse_text']); ?></span>    
                                <?php }
                                $icon     =  $settings['vg_filter_collapse_icon'] ?? false;
                                if($icon){
                                    Icons_Manager::render_icon( $icon );
                                } ?>
                            </a>
                            <ul class="eae-vg-collaps-item-list">
                                    <?php if($settings['vg_filterable_all_label_text'] !== '' ){ ?>
                                        <li class="eae-vg-filters-item">
                                            <a class="eae-filter-button" data-filter="all"><?php echo Helper::eae_wp_kses($settings['vg_filterable_all_label_text']); ?></a> 
                                        </li>
                                    <?php }

                                    foreach($filters_item as $index => $value){
                                        $this->set_render_attribute('filter_drop_down_element', 'class', 'eae-vg-filters-item');
                                        $this->set_render_attribute('vg-filter-button','class', 'eae-filter-button');
                                        if($value !== ''){ ?>
                                            <li <?php echo $this->get_render_attribute_string('filter_drop_down_element'); ?>>
                                                <a <?php echo $this->get_render_attribute_string('vg-filter-button') ?> data-filter="<?php echo esc_attr($index); ?>"><?php echo Helper::eae_wp_kses($value); ?></a> 
                                            </li> 
                                        <?php }
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>   
                <?php
            }
        }

        public function get_video_id($item){
            $id       = '';
            $url      = $item[ $item['video_type'] . '_link' ];
            if ( 'youtube' === $item['video_type'] ) {
                if ( preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches ) ) {
                    $id = $matches[1];	
                }
            } elseif ( 'vimeo' === $item['video_type'] ) {
                if ( preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs ) ) {
                    $id = $regs[3];
                }
            } elseif ( 'wistia' === $item['video_type'] ) {
                $id = $this->getStringBetween( $url, 'wvideo=', '"' );
            }elseif('dailymotion' === $item['video_type']) {
                $id = $this->getDailyMotionId($url);
            }
            return $id;
        }

        public function getDailyMotionId($url){

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

        protected function eae_get_url( $params, $id , $item) {
            $url      = '';
    
            if ( 'vimeo' === $item['video_type'] ) {
    
                $url = 'https://player.vimeo.com/video/';
    
            } elseif ( 'youtube' === $item['video_type'] ) {
    
                $cookie = '';
                if ( 'yes' === $item['yt_privacy'] ) {
                    $cookie = '-nocookie';
                }
                $url = 'https://www.youtube' . $cookie . '.com/embed/';
    
            } elseif ( 'wistia' === $item['video_type'] ) {
                $url = 'https://fast.wistia.net/embed/iframe/';
            } elseif('dailymotion' === $item['video_type']){
                $url = 'https://dailymotion.com/embed/video/';
            }
            
            $url = add_query_arg( $params, $url . $id );
            
            $url .= ( empty( $params ) ) ? '?' : '&';
            
            $url .= 'autoplay=1';
    
            if ( 'vimeo' === $item['video_type'] && '' !== $item['start'] ) {
                $time = gmdate( 'H\hi\ms\s', $item['start'] );
                $url .= '#t=' . $time;
            } elseif ( 'vimeo' === $item['video_type'] ) {
                $url .= '#t=';
            }
            $url = apply_filters( 'eae_video_url_filter', $url, $id );
            return $url;
        }

        public function get_schema($item){
            $video_link = $this->get_video_link( $item );
		
            $enable_schema = $item['schema_support'];
            if($item['schema_support'] == 'yes' ){
                $no_schema = false;
                $is_custom_thumbnail    = 'yes' === $item['vg_video_thumbnail'] ? true : false;
                $custom_thumbnail_url   = isset( $item['vg_video_thumbnail_image']['url'] ) ? $item['vg_video_thumbnail_image']['url'] : '';
                if ( ('yes' === $enable_schema ) && 
                    ( ( 
                        '' === $item['schema_title'] || 
                        '' === $item['schema_description'] || 
                        ( ! $is_custom_thumbnail && '' === $item['schema_thumbnail']['url'] ) || 
                        '' === $item['schema_upload_date'] 
                    ) 
                        || ( $is_custom_thumbnail && '' === $custom_thumbnail_url ) || ( '' === $video_link)
                    ) ) {
                        $no_schema = true;
                }
                
                
                if($no_schema == false){
                    $video_schema_data = array(
                        '@context'     => 'https://schema.org',
                        '@type'        => 'VideoObject',
                        'name'         => $item['schema_title'],
                        'description'  => $item['schema_description'],
                        'thumbnailUrl' => ( $is_custom_thumbnail ) ? $custom_thumbnail_url : $item['schema_thumbnail']['url'],
                        'uploadDate'   => $item['schema_upload_date'],
                        'contentUrl'   => $video_link,
                        'embedUrl'     => $video_link,
                    );
                    Pro::$schemas[] = $video_schema_data; 
                }
            }
        }

        public function youtube_embed_params($item){
            $settings = $this->get_settings_for_display();
            $youtube_options = array('rel', 'controls', 'mute', 'modestbranding' );
            foreach ( $youtube_options as $option ) {
                $value             = ( 'yes' === $item[ 'yt_' . $option ] ) ? 1 : 0;
                $params[ $option ] = $value;
                $params['start']   = $item['start'];
                $params['end']     = $item['end'];
            }	
            $params = apply_filters( 'eae_youtube_params', $params );
            return $params;
        }
    
        public function vimeo_embed_params($item){
            $vimeo_options = array('loop', 'title', 'portrait', 'byline', 'muted' );
                foreach ( $vimeo_options as $option ) {
                    $value             = ( 'yes' === $item[ 'vimeo_' . $option ] ) ? 1 : 0;
                    $params[ $option ] = $value;
                }
                $params['color']     = str_replace( '#', '', $item['vimeo_color'] );
                $params['autopause'] = '0';
                $params = apply_filters( 'eae_vimeo_params', $params );
                return $params;
        }
    
    
        public function dailymotion_embed_params($item){
            
            $dailymotion_options = array( 'mute', 'controls', 'sharing-enable' );
            foreach ( $dailymotion_options as $option ) {
                if($item['start'] != ''){
                    $params[ 'start' ] = $item['start'];
                }
    
                $value             = ( 'yes' === $item[ 'dailymotion_' . $option ] ) ? 'true' : 'false';
                $params[ $option ] = $value;
            }
            $params = apply_filters( 'eae_dailymotion_params', $params );
            return $params;		
        }
    
        public function wistia_embed_params($settings){
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

        public function render_hosted_video($item){
            $video_url = $this->eae_get_hosted_video_url($item);
            $video_params = $this->get_hosted_parameter($item);
            // echo '<pre>';  print_r($video_params); echo '</pre>';
            // die('dffa');
            $video_html = '';
            if ( ! empty( $video_url ) ) {
                ?>
                <video class="eae-hosted-video" src="<?php echo esc_url( $video_url ); ?>" <?php echo Utils::render_html_attributes( $video_params ) ; ?>></video>
                <?php	
            }
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
    
        private function get_hosted_parameter($item) {
            $video_params = [];
            $settings = $this->get_settings_for_display();
            $parmas = [
                'loop',
                'controls',
            ];
            
            foreach ($parmas as $option_name ) {
                if ( $item[ $option_name ] ) {
                    $video_params[ $option_name ] = '';
                }
            }
            if ( $item['muted']) {
                $video_params['muted'] = 'muted';
            }
            if ( $item['download_button'] != 'yes') {
                $video_params['controlslist'] = "nodownload";
            }
            
            return $video_params;
        }

        public function render(){
            $settings = $this->get_settings_for_display();
            if($settings['vg_video_list_type'] == 'custom_list' ){
                
                $this->add_render_attribute('eae-vg-wrapper','class','eae-vg-wrapper');
                $this->add_render_attribute('eae-vg-wrapper', 'data-stacked', $settings['stacked_below']);
                $this->add_render_attribute('eae-vg-video-container','class','eae-vg-video-container');
                $this->add_render_attribute( 'eae-vg-video-container', 'id', 'video-gallery-grid-' . $this->get_id() );

                if($settings['vg_layout'] == 'carousel'){
                    $this->add_render_attribute('eae-vg-wrapper','class',['eae-swiper-outer-wrapper','eae-swiper-container','eae-swiper','swiper','eae-vg-swiper']);
                if ( $settings['arrows_layout'] === 'inside' ) {
                    $this->add_render_attribute('eae-vg-wrapper','class','eae-hpos-' . $settings['arrow_horizontal_position']);
                    $this->add_render_attribute('eae-vg-wrapper','class','eae-vpos-' . $settings['arrow_vertical_position']);
                }
                $swiper_data = Swiper_helper::get_swiper_data($settings);
                $this->add_render_attribute('eae-vg-wrapper','data-swiper-settings', wp_json_encode( $swiper_data ) );

                $this->add_render_attribute('eae-vg-video-container','class',['eae-swiper-wrapper','eae-post-widget-wrapper','swiper-wrapper','eae-vg-swiper-container']);

                }elseif($settings['vg_layout_filter'] == 'yes'){
                    $this->add_render_attribute('eae-vg-wrapper','class','eae-vg-filter');
                }
                if($settings['vg_layout'] == 'grid'){
                    $this->add_render_attribute('eae-vg-video-container','class','eae-vg-grid');
                }

                if($settings['vg_lightbox_enable'] == 'yes'){
                    $this->add_render_attribute( 'eae-vg-video-container', 'data-lg-settings', json_encode(Lightgallery_helper::get_lightgallery_data($settings)) );
                    $this->add_render_attribute( 'eae-vg-video-container', 'class', 'lightbox');
                }
                if($settings['vg_layout'] == 'carousel'){
                    ?>
                    <!-- Carsousel Start -->
                    <div class="eae-video-gallery">
                    <?php
                }
                ?>
                    <!-- Video Wrapper Start -->
                    <div <?php echo $this->get_render_attribute_string('eae-vg-wrapper'); ?>>
                        <!-- Filter Start -->
                        <?php if($settings['vg_layout_filter'] == 'yes'){ ?>
                            <div class="eae-vg-filter-button-container"> <!--container start -->
                                <?php if($settings['vg_filter_title'] !== '') {?>
                                    <?php
                                        $this->add_render_attribute('vg-filter-title','class','eae-vg-filteres-heading');
                                        $title = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['vg_filter_title_html_tag'] ), $this->get_render_attribute_string( 'vg-filter-title' ), $settings['vg_filter_title']); 
                                        echo $title;
                                    ?>
                                <?php } ?>
                                <?php $this->get_filter_tabs(); ?>
                            </div>
                        <?php } ?>  
                        <!--Filter End-->
                        <!-- Video Container Start -->
                        <div <?php echo $this->get_render_attribute_string('eae-vg-video-container') ?>> <!--container start -->
                            <?php foreach( $settings['vg_video_Gallery'] as $index => $item ){ 
                                $category = strtolower($item['vg_video_category']);
                                $category = str_replace(' ','',$category);
                                $video_type = $item['video_type']; 
                                $this->get_schema($item);

                                if($item['video_type'] != 'hosted'){
                                    $video_id  = $this->get_video_id($item);
                                }	
                                if(method_exists($this,$video_type.'_embed_params')){
                                    $embed_param = call_user_func( array( $this, $video_type.'_embed_params',),$item);
                                }
                                $src = '';
                                if ( 'hosted' !== $item['video_type'] ) {
                                    $src = $this->eae_get_url( $embed_param, $video_id, $item );
                                    $this->set_render_attribute('vg-element-'.$index,'data-video-url',$src);
                                } else {
                                    $src = $this->eae_get_hosted_video_url($item);
                                    $this->set_render_attribute('vg-element-'.$index,'data-video-url',$src);
                                }

                                if ( 'hosted' === $item['video_type'] ) {
                                    $video_url = $this->eae_get_hosted_video_url($item);
                                    ob_start();
                        
                                    $this->render_hosted_video($item);
                        
                                    $video_html = ob_get_clean();
                        
                                    $video_html = wp_json_encode( $video_html );
                        
                                    $video_html = htmlspecialchars( $video_html, ENT_QUOTES );
                        
                                    $this->add_render_attribute(
                                        'vg-element-'.$index,
                                        array(
                                            'data-hosted-html' => $video_html,
                                        )
                                    );
                                }
                                $swiper_class = '';
                                if($settings['vg_layout'] == 'carousel'){
                                    $swiper_class = 'eae-swiper-slide swiper-slide';
                                }        
                                $this->set_render_attribute('element-wrapper','class',['eae-vg-element-wrapper',$category,$swiper_class]);
                                $this->set_render_attribute('vg-element-'.$index,'class',['eae-vg-element','eae-vg-image-overlay']);
                                $this->set_render_attribute('vg-element-'.$index,'id',$item['_id']);
                                $this->set_render_attribute('vg-element-'.$index,'data-video-type',$video_type);

                                // if($autoplay != ''){
                                //     $this->set_render_attribute( 'vg-element-'.$index, 'data-autoplay', $autoplay );
                                // }

                                if($settings['vg_lightbox_enable'] == 'yes'){
                                    
                                    if($item['video_type'] !== 'hosted'){
                                        $video_link = $this->get_video_link( $item );
                                        $this->set_render_attribute('vg-element-'.$index,'data-src', $video_link);
                                    }else{
                                        $this->set_render_attribute('vg-element-'.$index,'data-video','{"source": [{"src":"' . $src .'", "type":"video/mp4"}], "attributes": {"preload": false, "controls": true}}');
                                    }
                                    
                                    
                                    if($item['vg_video_thumbnail'] == 'yes'){
                                        $this->set_render_attribute('vg-element-'.$index,'data-poster',$item['vg_video_thumbnail_image']['url']);
                                    }
                                    $this->set_render_attribute('vg-element-'.$index,'data-sub-html','<h4>'. $item['vg_title'] .'</h4><p>'. $item['vg_description'] .'</p>');
                                }
                                
                                ?>
                                <?php if(!empty($src)){ ?>
                                    <div <?php echo $this->get_render_attribute_string('element-wrapper') ; ?>> <!--element wrapper start -->
                                        <div <?php echo $this->get_render_attribute_string('vg-element-'.$index); ?> ><!-- vg elemet start -->
                                            <?php if($settings['vg_video_details_layout'] !== 'below-video'){ 
                                                $this->get_video_details($item); 
                                            } 

                                            if ( 'hosted' !== $item['video_type'] ) {
                                                $video_id = $this->get_video_id($item);
                                                $this->set_render_attribute( 'eae-video-thumb', 'src', $this->eae_get_video_thumb( $video_id , $item) );
                                            }else{
                                                if ( 'yes' === $item['vg_video_thumbnail'] ) {
                                                    $thumb = Group_Control_Image_Size::get_attachment_image_src( $item['vg_video_thumbnail_image']['id'] , 'vg_video_thumbnail_image', $item );
                                                } else {
                                                    $thumb = $this->eae_get_hosted_video_url($item);
                                                }
                                                $this->set_render_attribute( 'eae-video-thumb', 'src', $thumb );
                                            }
                                            if ( 'hosted' === $item['video_type'] && 'yes' !== $item['vg_video_thumbnail'] ) {
                                                $custom_tag = 'video';
                                            } else {
                                                $custom_tag = 'img';
                                            }
                                            ?>
                                            
                                                <<?php echo esc_attr( $custom_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'eae-video-thumb' ) ); ?>></<?php echo esc_attr( $custom_tag ); ?>>
                                                <?php
                                                    Helper::render_icon_html($settings, $this, 'vg_icon','eae-vg-icon');
                                                ?>
                                            
                                            
                                            
                                        </div> <!-- vg elemet end -->
                                        <?php if($settings['vg_video_details_layout'] == 'below-video'){ 
                                            $this->get_video_details($item); 
                                        } ?>
                                    </div>    
                                <?php } ?>     
                            <?php } ?>
                        </div>
                        <!-- Video Container End -->
                        <?php if($settings['vg_layout'] === 'carousel'){                                  
                            Swiper_helper::get_swiper_pagination($settings);
                            /** Arrows Inside **/
                            if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
                                Swiper_helper::get_swiper_arrows($settings);
                            }
                            Swiper_helper::get_swiper_scrolbar($settings);
                        } ?>
                    </div>
                    <!-- Video Wrapper End -->
                <?php
                    if($settings['vg_layout'] == 'carousel'){ 
                        if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
                            /** Arrows Outside **/
                            Swiper_Helper::get_swiper_arrows($settings);
                        }
                    } 
                    if($settings['vg_layout'] == 'carousel'){ ?>
                        </div>
                            <!-- Carouse End -->
                            <?php 
                        } ?>
                <?php
            }else{
                $this->render_youtube_video();
            }
        }

        public function render_youtube_video(){
            $settings = $this->get_settings_for_display();
            $channelId = $settings['vg_youtube_channel_id'];
            $playListId = $settings['vg_youtube_playlist_id']; 
            $maxResults = $settings['vg_number_of_videos'] !== '' ? $settings['vg_number_of_videos'] : '5';
            $expiration =  $settings['vg_reload_video'] !== '' ? $settings['vg_reload_video'] : '120';
            $youtube_api_key = get_option('wts_eae_youtube_api_key');
            if($settings['vg_video_list_type'] != 'custom_list'){
                //check elementor is editor mode or not
                if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                    if(empty($youtube_api_key)){
                        echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-warning'>Please add youtube api key in Dashboard > Elementor Addons Elements > Configuration</p></div>";
                        return;
                    }else{
                        if($settings['vg_video_list_type'] == 'youtube_channel_videos' && empty($channelId)){
                            echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-warning'>Please add youtube Channel ID</p></div>";
                            return;
                        }
                        if($settings['vg_video_list_type'] == 'youtube_playlist' && empty($playListId)){
                            echo "<div class='eae-vg-error message'><p class='elementor-alert elementor-alert-warning'>Please add youtube Playlist ID</p></div>";
                            return;
                        }
                    }
                }else{
                    if(empty($youtube_api_key)){
                       return;
                    }else{
                        if($settings['vg_video_list_type'] == 'youtube_channel_videos' && empty($channelId)){
                            return;
                        }
                        if($settings['vg_video_list_type'] == 'youtube_playlist' && empty($playListId)){
                            return;
                        }
                    }
                }
            }  
            $ApiKey = $youtube_api_key;

            $apiUrl = '';
            $order = $settings['vg_videos_order'] == 'yes' ? '&order=date' : '' ;  
            if(!empty($channelId) || !empty($playListId)){
                if($settings['vg_video_list_type'] == 'youtube_channel_videos'){
                    $apiUrl = 'https://www.googleapis.com/youtube/v3/search?key=' . $ApiKey . '&channelId=' . $channelId . '&part=snippet,id&maxResults=' . $maxResults . $order;
                }else{
                    $apiUrl = 'https://www.googleapis.com/youtube/v3/playlistItems?key=' . $ApiKey . '&playlistId=' . $playListId . '&part=snippet,id&maxResults=' . $maxResults;
                    
                }
    
                $transient_name = md5($apiUrl);
                if( false === get_transient($transient_name)){
                    $videos = json_decode(file_get_contents($apiUrl));
                    set_transient($transient_name,$videos,60 * $expiration);
                }else{
                    $videos = get_transient($transient_name);
                }
                
                if(empty($videos)){
                    return;
                }
                
                $this->add_render_attribute('eae-vg-wrapper','class','eae-vg-wrapper');
                $this->add_render_attribute('eae-vg-wrapper', 'data-stacked', $settings['stacked_below']);
                $this->add_render_attribute('eae-vg-video-container','class','eae-vg-video-container');
                $this->add_render_attribute( 'eae-vg-video-container', 'id', 'video-gallery-grid-' . $this->get_id() );
    
                if($settings['vg_layout'] == 'carousel'){
                    $this->add_render_attribute('eae-vg-wrapper','class',['eae-swiper-outer-wrapper','eae-swiper-container','eae-swiper','swiper','eae-vg-swiper']);
                    if ( $settings['arrows_layout'] === 'inside' ) {
                        $this->add_render_attribute('eae-vg-wrapper','class','eae-hpos-' . $settings['arrow_horizontal_position']);
                        $this->add_render_attribute('eae-vg-wrapper','class','eae-vpos-' . $settings['arrow_vertical_position']);
                    }
                    $swiper_data = Swiper_helper::get_swiper_data($settings);
                    $this->add_render_attribute('eae-vg-wrapper','data-swiper-settings', wp_json_encode( $swiper_data ) );
    
                    $this->add_render_attribute('eae-vg-video-container','class',['eae-swiper-wrapper','eae-post-widget-wrapper','swiper-wrapper','eae-vg-swiper-container']);
    
                }
                if($settings['vg_layout'] == 'grid'){
                    $this->add_render_attribute('eae-vg-video-container','class','eae-vg-grid');
                }
    
                if($settings['vg_lightbox_enable'] == 'yes'){
                    $this->add_render_attribute( 'eae-vg-video-container', 'data-lg-settings', json_encode(Lightgallery_helper::get_lightgallery_data($settings)) );
                    $this->add_render_attribute( 'eae-vg-video-container', 'class', 'lightbox');
                }
                if($videos !== ''){
                    if($settings['vg_layout'] == 'carousel'){
                        ?><div class="eae-video-gallery"><?php
                    }
                    ?>
                        <!-- VG Wrappe Start -->
                        <div <?php echo $this->get_render_attribute_string('eae-vg-wrapper'); ?>>
                            <!-- VG Video Container Start -->
                                <div <?php echo $this->get_render_attribute_string('eae-vg-video-container') ?>> 
                                    <?php foreach( $videos->items as $index => $item ){
                                        $swiper_class = '';
                                        if($settings['vg_layout'] == 'carousel'){
                                            $swiper_class = 'eae-swiper-slide swiper-slide';
                                        }
                                        $url = '';   
                                        if($settings['vg_video_list_type'] == 'youtube_channel_videos'){
                                            $url = $item->id->videoId;
                                        }else{
                                            $url = $item->snippet->resourceId->videoId;
                                        }
                                        if(isset($item->id->videoId) || isset($item->snippet->resourceId->videoId) ){
                                            $mute = 0;
                                            if($settings['lightbox_video_mute'] == 'yes'){
                                                $mute = 1;
                                            }
                                            $src = 'https://www.youtube.com/embed/'. $url .'?autoplay=1&rel=1&mute='. $mute .'&loop=0';  
                                            $this->set_render_attribute('element-wrapper','class',['eae-vg-element-wrapper',$swiper_class]);
                                            $this->set_render_attribute('vg-element-'.$index,'class',['eae-vg-element','eae-vg-image-overlay']);
                                            $this->set_render_attribute('vg-element-'.$index,'data-video-type','youtube');
                                            $this->set_render_attribute('vg-element-'.$index,'data-video-url',$src);
                                            
                                            if($settings['vg_lightbox_enable'] == 'yes'){
                                                $this->set_render_attribute('vg-element-'.$index,'data-src', $src);
                                                $this->set_render_attribute('vg-element-'.$index,'data-poster', $item->snippet->thumbnails->high->url);                                                
                                                $this->set_render_attribute('vg-element-'.$index,'data-sub-html','<h4>'. $item->snippet->title .'</h4>');
                                            } 
                                            ?>
                                            <!-- Element Wrapper Start -->
                                            <div <?php echo $this->get_render_attribute_string('element-wrapper') ; ?>>
                                                <!-- VG Element Star -->
                                                <div <?php echo $this->get_render_attribute_string('vg-element-'.$index); ?> >
                                                    <?php
                                                        Helper::render_icon_html($settings, $this, 'vg_icon','eae-vg-icon');
                                                    ?>
                                                    <?php if($settings['vg_videos_title_enable'] == 'yes'){
                                                        if($settings['vg_video_details_layout'] !== 'below-video'){ 
                                                            $this->get_youtube_video_details($item); 
                                                        }
                                                    }
                                                    $this->set_render_attribute( 'eae-video-thumb', 'src', $item->snippet->thumbnails->high->url );
                                                    ?>
                                                    <img <?php echo $this->get_render_attribute_string( 'eae-video-thumb' ); ?>></img>
                                                </div>
                                                <?php if($settings['vg_videos_title_enable'] == 'yes'){
                                                    if($settings['vg_video_details_layout'] == 'below-video'){ 
                                                        $this->get_youtube_video_details($item); 
                                                    }
                                                } ?>
                                            </div><?php
                                        }
                                    }?>
                                </div>
                                <?php if($settings['vg_layout'] === 'carousel'){  
                                    
                                    Swiper_helper::get_swiper_pagination($settings);
    
                                    /** Arrows Inside **/
                                    if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
                                        Swiper_helper::get_swiper_arrows($settings);
                                    }
    
                                    Swiper_helper::get_swiper_scrolbar($settings);
                                    ?>
    
                                <?php } ?>
                        </div><?php
                                if($settings['vg_layout'] == 'carousel'){  
                                if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
                                    /** Arrows Outside **/
                                    Swiper_Helper::get_swiper_arrows($settings);
                                }
                            } ?> 
                    <?php if($settings['vg_layout'] == 'carousel'){?>
                        </div>
                    <?php 
                    }
                }
            }
        }
    }
?>