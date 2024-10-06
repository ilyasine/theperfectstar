<?php

namespace WTS_EAE\Pro\Modules\VideoBox\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Utils;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use WTS_EAE\Pro\Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class VideoBox extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-video-box';
	}

	public function get_title() {
		return __( 'EAE - Video Box', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-video-box';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'image', 'slider', 'accordion', 'panel slider'];
	}


	public function get_script_depends() {

		// load all scripts in editor and preview mode
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return [ 'lightgallery-js', 'lg-video-js', 'lg-fullscreen-js', 'lg-share-js', 'lg-hash-js', 'lg-zoom-js', 'lg-rotate-js' ];

		}
		$scripts  = [];
		$settings = $this->get_settings();
		$scripts[] = 'eae-lottie';
		$scripts[] = 'elementor-waypoints';
		if( $settings['lightbox'] === 'yes'){
			$scripts[] = 'lightgallery-js';
			$scripts[] = 'lg-video-js';
			$scripts[] = 'lg-fullscreen-js';
			$scripts[] = 'lg-share-js';
			$scripts[] = 'lg-hash-js';
			$scripts[] = 'lgAutoplay-js';
			if($settings['video_type'] === 'vimeo'){
				$scripts[] = 'eae-player-js';
			}
			if($settings['video_type'] === 'hosted'){
				$scripts[] = 'eae-video-js';
			}
		}
		return $scripts;
	}

	public function get_style_depends()
	{
		return ['lightgallery-css','eae-video-css'];
	}

	protected function register_controls() {

        $this->register_video_content();
        $this->register_overlay_content();
        $this->register_video_icon();
		$this->register_video_sticky_controls();
		$this->eae_register_schema_controls();
		$this->video_general_style_controls();
		$this->play_icon_style_controls();
		$this->video_sticky_style_controls(); 
    }

    protected function register_video_content() {

		$this->start_controls_section(
			'section_video',
			array(
				'label' => __( 'Video', 'wts-eae' ),
			)
		);

			$this->add_control(
				'video_type',
				array(
					'label'   => __( 'Video Type', 'wts-eae' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'youtube',
					'options' => array(
						'youtube' => __( 'YouTube', 'wts-eae' ),
						'vimeo'   => __( 'Vimeo', 'wts-eae' ),
						'wistia'  => __( 'Wistia', 'wts-eae' ),
						'dailymotion' => esc_html__( 'Dailymotion', 'wts-eae' ),
                        'hosted'  => __( 'Self Hosted', 'wts-eae' ),
					),
				)
			);

			$this->add_control(
				'insert_link',
				array(
					'label'     => __( 'External URL', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'hosted',
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
						'video_type'  => 'hosted',
						'insert_link' => '',
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
						'video_type'  => 'hosted',
						'insert_link' => 'yes',
					),
				)
			);

			$default_youtube = apply_filters( 'eae_video_default_youtube_link', 'https://www.youtube.com/watch?v=XoIWJDPsLBk' );

			$default_vimeo = apply_filters( 'eae_video_default_vimeo_link', 'https://vimeo.com/838671376' );

			$default_wistia = apply_filters( 'eae_video_default_wistia_link', '<p><a href="https://wpvwebmaster.wistia.com/medias/82tscaz1gr?wvideo=82tscaz1gr"><img src="https://embed-ssl.wistia.com/deliveries/778f315db911722d46d7ae50d4d567f23ce009c5.jpg?image_play_button_size=2x&amp;image_crop_resized=960x540&amp;image_play_button=1&amp;image_play_button_color=174bd2e0" width="400" height="225" style="width: 400px; height: 225px;"></a></p><p><a href="https://wpvwebmaster.wistia.com/medias/82tscaz1gr?wvideo=82tscaz1gr">Time Lapse of the Milky Way over the Beach</a></p>' );
																			   
			$default_dailymotion = apply_filters( 'eae_video_default_dailymotion_link', 'https://www.dailymotion.com/video/k1T8t2sBqRaROEzevL8' );

			$this->add_control(
				'youtube_link',
				array(
					'label'       => __( 'Link', 'wts-eae' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'default'     => $default_youtube,
					'label_block' => true,
					'condition'   => array(
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'youtube_link_helper',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( '<div class="eae-notice "><b>Valid Format:</b>&nbsp;https://www.youtube.com/watch?v=XHOmBV4js_E</br><b>Invalid Format:</b>&nbsp;https://youtu.be/XHOmBV4js_E</div>', 'wts-eae' ) ),
					'condition'       => array(
						'video_type' => 'youtube',
					),
					'separator'       => 'none',
				)
			);
			

			$this->add_control(
				'vimeo_link',
				array(
					'label'       => __( 'Link', 'wts-eae' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'default'     => $default_vimeo,
					'label_block' => true,
					'condition'   => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_link_helper',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( '<div class="eae-notice"><b>Valid Format:</b>&nbsp;https://vimeo.com/235215203</br><b>Invalid Format:</b>&nbsp; https://vimeo.com/channels/{channel_id}/{video_id}</div>', 'wts-eae' ) ),
					'condition'       => array(
						'video_type' => 'vimeo',
					),
					'separator'       => 'none',
				)
			);
			

			$this->add_control(
				'wistia_link',
				array(
					'label'       => __( 'Link & Thumbnail Text', 'wts-eae' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'default'     => $default_wistia,
					'label_block' => true,
					'condition'   => array(
						'video_type' => 'wistia',
					),
				)
			);

			//Add Wistia Helper Link
			$this->add_control(
				'wistia_link_helper',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( __( '<div class="eae-notice">Go to your Wistia video, right click, "Copy Link & Thumbnail" and paste here. </div>', 'wts-eae' ) ),
					'condition'       => array(
						'video_type' => 'wistia',
					),
					'separator'       => 'none',
				)
			);

		
		

		$this->add_control(
			'dailymotion_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', 'wts-eae' ) . ' (Dailymotion)',
				'default' => $default_dailymotion,
				'label_block' => true,
				'condition' => [
					'video_type' => 'dailymotion',
				],
			]
		);

		//Add Dailymotion Helper Link
		$this->add_control(
			'dailymotion_link_helper',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				/* translators: %1$s doc link */
				'raw'             => sprintf( __( '<div class="eae-notice"><b>Valid Format:</b>&nbsp;https://www.dailymotion.com/video/x6tqhqb<b>Invalid Format:</b>&nbsp;https://dailymotion.com/video={video_id}</div>', 'wts-eae' ) ),
				'condition'       => array(
					'video_type' => 'dailymotion',
				),
				'separator'       => 'none',
			)
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
						'video_type' => array( 'youtube', 'vimeo', 'hosted' , 'dailymotion'),
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
						'video_type' => array( 'youtube', 'hosted' ),
					),
				)
			);

			// YouTube.
			$this->add_control(
				'yt_autoplay',
				array(
					'label'     => __( 'Autoplay', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'youtube',
					),
					'description' => __('To enable autoplay, you must enable the mute option.', 'wts-eae')
				)
			);

			$this->add_control(
				'yt_rel',
				array(
					'label'     => __( 'Related Videos From', 'wts-eae' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'no',
					'options'   => array(
						'no'  => __( 'Current Video Channel', 'wts-eae' ),
						'yes' => __( 'Any Random Video', 'wts-eae' ),
					),
					'condition' => array(
						'video_type' => 'youtube',
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
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'yt_mute',
				array(
					'label'     => __( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'youtube',
					),
					'return_value' => 'yes' 
				)
			);

			$this->add_control(
				'yt_modestbranding',
				array(
					'label'       => __( 'Modest Branding', 'wts-eae' ),
					'description' => __( 'This option lets you use a YouTube player that does not show a YouTube logo.', 'wts-eae' ),
					'type'        => Controls_Manager::SWITCHER,
					'condition'   => array(
						'video_type'  => 'youtube',
						'yt_controls' => 'yes',
					),
				)
			);

			$this->add_control(
				'yt_privacy',
				array(
					'label'       => __( 'Privacy Mode', 'wts-eae' ),
					'type'        => Controls_Manager::SWITCHER,
					'description' => __( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'wts-eae' ),
					'condition'   => array(
						'video_type' => 'youtube',
					),
				)
			);

			// 
			$this->add_control(
				'dailymotion_autoplay',
				array(
					'label'     => __( 'Autoplay', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'description' => __('To enable autoplay, you must enable the mute option.', 'wts-eae'),
					'condition' => array(
						'video_type' => 'dailymotion',
					),
				)
			);

			$this->add_control(
				'dailymotion_mute',
				array(
					'label'     => __( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'dailymotion',
					),
				)
			);

			$this->add_control(
				'dailymotion_controls',
				array(
					'label'     => __( 'Player Control', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'wts-eae' ),
					'label_on'  => __( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'dailymotion',
					),
				)
			);

			$this->add_control(
				'dailymotion_sharing-enable',
				array(
					'label'     => __( 'Enable Sharing', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'dailymotion',
					),
				)
			);

			// Vimeo.
			$this->add_control(
				'vimeo_autoplay',
				array(
					'label'     => __( 'Autoplay', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_loop',
				array(
					'label'     => __( 'Loop', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_muted',
				array(
					'label'     => __( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_title',
				array(
					'label'     => __( 'Intro Title', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'wts-eae' ),
					'label_on'  => __( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_portrait',
				array(
					'label'     => __( 'Intro Portrait', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'wts-eae' ),
					'label_on'  => __( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_byline',
				array(
					'label'     => __( 'Intro Byline', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'wts-eae' ),
					'label_on'  => __( 'Show', 'wts-eae' ),
					'default'   => 'yes',
					'condition' => array(
						'video_type' => 'vimeo',
					),
				)
			);

			$this->add_control(
				'vimeo_color',
				array(
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
				)
			);

			// Wistia.
			$this->add_control(
				'wistia_autoplay',
				array(
					'label'     => __( 'Autoplay', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
				)
			);

			$this->add_control(
				'wistia_loop',
				array(
					'label'     => __( 'Loop', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
				)
			);

			$this->add_control(
				'wistia_muted',
				array(
					'label'     => __( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
				)
			);

			$this->add_control(
				'wistia_playbar',
				array(
					'label'     => __( 'Show Playbar', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'wistia',
					),
					'default'   => 'yes',
				)
			);

			// Self Hosted Controls <<<.
			$this->add_control(
				'autoplay',
				array(
					'label'     => __( 'Autoplay', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'hosted',
					),
				)
			);

			$this->add_control(
				'loop',
				array(
					'label'     => __( 'Loop', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'condition' => array(
						'video_type' => 'hosted',
					),
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
					'condition' => array(
						'video_type' => 'hosted',
						
					),
				)
			);

			$this->add_control(
				'muted',
				array(
					'label'     => __( 'Mute', 'wts-eae' ),
					'type'      => Controls_Manager::SWITCHER,
					'condition' => array(
						'video_type' => 'hosted',
					),
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'download_button',
				[
					'label' => esc_html__( 'Download Button', 'wts-eae' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => esc_html__( 'Hide', 'wts-eae' ),
					'label_on' => esc_html__( 'Show', 'wts-eae' ),
					'condition' => [
						'video_type' => 'hosted',
						'lightbox!'  => 'yes'
					],
					'return_value' => 'yes',
				]
			); 			

			$this->add_control(
				'heading_youtube',
				array(
					'label'     => __( 'Video Options', 'wts-eae' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
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
                        '{{WRAPPER}} .eae-video-outer-wrapper' => 'aspect-ratio: {{VALUE}}',
						'{{WRAPPER}} .eae-video-outer-wrapper .eae-video-play iframe' => 'aspect-ratio: {{VALUE}}',
                    ],
                ]
            );

			// Lightbox.
			$this->add_control(
				'lightbox',
				array(
					'label' => __( 'Lightbox', 'wts-eae' ),
					'type'  => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'description' => __('Autoplay will not work in lightbox', 'wts-eae'),
					'condition' => [
						'video_type!' => 'dailymotion',
					]
				)
			);

	
			$this->add_control(
				'lightgallery_fullscreen',
				[
					'label'        => __( 'FullScreen', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition' 	=> [
						'lightbox' => 'yes',
					],
				]
			);
			$this->add_control(
				'lightgallery_share',
				[
					'label'        => __( 'Enable Share', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition' 	=> [
						'lightbox' => 'yes',
					],
				]
			);
	
		
			$this->add_control(
				'lightgallery_hash',
				[
					'label'     => __( 'Hash URL', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition' 	=> [
						'lightbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'lightgallery_galleryId',
				[
					'label'     => __( 'Gallery ID', 'wts-eae' ),
					'type'      => Controls_Manager::TEXT,
					'default'	=> 'gallery-'.rand(1, 1000),
					'description' => __('Add a unique ID for the gallery. This is required for the hash URL to work.', 'wts-eae'),
					'condition' 	=> [
						'lightgallery_hash' => 'yes',
						'lightbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'video_display_heading',
				array(
					'label'     => __( 'Video Details', 'wts-eae' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'video_display_details',
				array(
					'label'        => __( 'Enable Video Details', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'wts-eae' ),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'default'      => 'no',
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'video_display_title',
				[
					'label' => esc_html__( 'Title', 'wts-eae' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Video Title', 'wts-eae' ),
					'placeholder' => esc_html__( 'Type your title here', 'wts-eae' ),
					'condition' => [
						'video_display_details' => 'yes',
					],	
				]
			);

			$this->add_control(
				'video_display_desc',
				[
					'label' => esc_html__( 'Description', 'wts-eae' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( '', 'wts-eae' ),
					'placeholder' => esc_html__( 'Type your Description here', 'wts-eae' ),
					'condition' => [
						'video_display_details' => 'yes',
					],
				]
			);

			$this->add_control(
				'preivew_video_display_details',
				array(
					'label'        => __( 'Preview Video Details', 'wts-eae' ),
					'description'  => __('It is only for editor preview. I helps you to design your layout properly', 'wts-eae'),
 					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'wts-eae' ),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'default'      => 'no',
					'return_value' => 'yes',
					'condition' => [
						'video_display_details' => 'yes',
					],
				)
			);

			#Add Mask Heading
			$this->add_control(
				'mask_heading',
				array(
					'label'     => __( 'Mask', 'wts-eae' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'mask_video_shape',
				array(
					'label'        => __( 'Enable Mask', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'wts-eae' ),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'default'      => 'no',
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'video_mask_image',
				[
					'label'     => __( 'Mask Shape', 'wts-eae' ),
					'type'      => Controls_Manager::MEDIA,
					'condition' => array(
						'mask_video_shape' => 'yes',
					),
					'selectors' => [
						'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-mask-media:not(.eae-sticky-apply) .eae-video-container' => 'mask-image: url({{URL}});-webkit-mask-image: url({{URL}});',
					],
					'description' => __('Use PNG image with the shape you want to mask around feature video.', 'wts-eae')
				]
			);

		$this->end_controls_section();
	}

    protected function register_overlay_content() {

		$this->start_controls_section(
			'section_image_overlay',
			array(
				'label' => __( 'Thumbnail & Overlay', 'wts-eae' ),
			)
		);

			$this->add_control(
				'yt_thumbnail_size',
				array(
					'label'     => __( 'Thumbnail Size', 'wts-eae' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => array(
						'maxresdefault' => __( 'Maximum Resolution', 'wts-eae' ),
						'hqdefault'     => __( 'High Quality', 'wts-eae' ),
						'mqdefault'     => __( 'Medium Quality', 'wts-eae' ),
						'sddefault'     => __( 'Standard Quality', 'wts-eae' ),
					),
					'default'   => 'maxresdefault',
					'condition' => array(
						'video_type' => 'youtube',
					),
				)
			);

			$this->add_control(
				'show_image_overlay',
				array(
					'label'        => __( 'Custom Thumbnail', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'wts-eae' ),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'default'      => 'no',
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'image_overlay',
				array(
					'label'     => __( 'Select Image', 'wts-eae' ),
					'type'      => Controls_Manager::MEDIA,
					'default'   => array(
						'url' => Utils::get_placeholder_image_src(),
					),
					'dynamic'   => array(
						'active' => true,
					),
					'condition' => array(
						'show_image_overlay' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'image_overlay', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_overlay_size` and `image_overlay_custom_dimension` phpcs:ignore Squiz.PHP.CommentedOutCode.Found.
					'default'   => 'full',
					'separator' => 'none',
					'condition' => array(
						'show_image_overlay' => 'yes',
					),
				)
			);


		$this->end_controls_section();
	}

    protected function register_video_icon() {
        $this->start_controls_section(
			'section_play_icon',
			array(
				'label' => __( 'Play Button', 'wts-eae' ),
			)
		);

        Helper::eae_media_controls(
            $this,[
                'name' => 'play_icon',
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
	}

	protected function eae_register_schema_controls() {
		$this->start_controls_section(
			'section_schema',
			array(
				'label' => __( 'Video Schema', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'schema_support',
			array(
				'label'     => __( 'Schema Support', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Yes', 'wts-eae' ),
				'label_off' => __( 'No', 'wts-eae' ),
				'default'   => 'no',
			)
		);

		$this->add_control(
			'schema_title',
			array(
				'label'       => __( 'Video Title', 'wts-eae' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Title of the video.', 'wts-eae' ),
				'condition'   => array(
					'schema_support' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_description',
			array(
				'label'     => __( 'Video Description', 'wts-eae' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 10,
				'default'   => __( 'Description of the video.', 'wts-eae' ),
				'condition' => array(
					'schema_support' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_thumbnail',
			array(
				'label'     => __( 'Video Thumbnail', 'wts-eae' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'schema_support'      => 'yes',
					'show_image_overlay!' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_upload_date',
			array(
				'label'       => __( 'Video Upload Date & Time', 'wts-eae' ),
				'type'        => Controls_Manager::DATE_TIME,
				'placeholder' => __( 'yyyy-mm-dd', 'wts-eae' ),
				'default'     => gmdate( 'Y-m-d H:i' ),
				'condition'   => array(
					'schema_support' => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}


	public function video_general_style_controls(){
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'General', 'wts-eae' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		#add background controls
		$this->add_control(
			'video_background',
			[
				'label' => esc_html__( 'Background', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply)' => 'background-color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'video_spacing',
			[
				'label' => esc_html__( 'Spacing', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		#Add Border Control
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply)',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply)',
			]
		);

		$this->add_responsive_control(
			'video_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					// Without Detils
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		#Add Transform Controls
		$this->add_responsive_control(
			'video_skew',
			[
				'label' => esc_html__( 'Skew', 'wts-eae' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$this->add_responsive_control(
			'video_rotate',
			[
				'label' => esc_html__( 'Rotate', 'wts-eae' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$this->add_responsive_control(
			'video_scale',
			[
				'label' => esc_html__( 'Scale', 'wts-eae' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
				'min' => 0.1,
				'max' => 2,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply)' => 'transform: skew({{video_skew.VALUE}}deg) rotate({{video_rotate.VALUE}}deg) scale({{VALUE}});',
				]
			]
		);

		$this->add_control(
			'overlay_control_heading',
			[
				'label' => esc_html__( 'Overlay', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		#add overlay color controls	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_color',
				'types' => [ 'classic', 'gradient',],
				'selector' => '{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-wrappper::before',
			]
		);



		#add mask control heading	
		$this->add_control(
			'mask_control_heading',
			[
				'label' => esc_html__( 'Mask', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'	=> [
					'mask_video_shape' => 'yes'
				]
			]
		);

		#add mask background position controls
		$this->add_control(
			'mask_background_position',
			[
				'label' => esc_html__( 'Background Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'left top' => esc_html__( 'Left Top', 'wts-eae' ),
					'left center' => esc_html__( 'Left Center', 'wts-eae' ),
					'left bottom' => esc_html__( 'Left Bottom', 'wts-eae' ),
					'center top' => esc_html__( 'Center Top', 'wts-eae' ),
					'center center' => esc_html__( 'Center Center', 'wts-eae' ),
					'center bottom' => esc_html__( 'Center Bottom', 'wts-eae' ),
					'right top' => esc_html__( 'Right Top', 'wts-eae' ),
					'right center' => esc_html__( 'Right Center', 'wts-eae' ),
					'right bottom' => esc_html__( 'Right Bottom', 'wts-eae' ),
				],
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-mask-media .eae-video-container' => '-webkit-mask-position: {{VALUE}};',
				],
				'condition'	=> [
					'mask_video_shape' => 'yes'
				]
			]
		);

		#add background size contols
		$this->add_control(
			'mask_background_size',
			[
				'label' => esc_html__( 'Background Size', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'auto' => esc_html__( 'Auto', 'wts-eae' ),
					'cover' => esc_html__( 'Cover', 'wts-eae' ),
					'contain' => esc_html__( 'Contain', 'wts-eae' ),
				],
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-mask-media .eae-video-container' => '-webkit-mask-size: {{VALUE}};',
				],
				'condition'	=> [
					'mask_video_shape' => 'yes'
				]
			]
		);

		#add mask background repeat controls
		$this->add_control(
			'mask_background_repeat',
			[
				'label' => esc_html__( 'Background Repeat', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => [
					'no-repeat' => esc_html__( 'No Repeat', 'wts-eae' ),
					'repeat' => esc_html__( 'Repeat', 'wts-eae' ),
					'repeat-x' => esc_html__( 'Repeat-x', 'wts-eae' ),
					'repeat-y' => esc_html__( 'Repeat-y', 'wts-eae' ),
				],
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-mask-media .eae-video-container' => '-webkit-mask-repeat : {{VALUE}};',
				],
				'condition'	=> [
					'mask_video_shape' => 'yes'
				]
			]
		);

		#add info bar style control heading
		$this->add_control(
			'info_bar_style_heading',
			[
				'label' => esc_html__( 'Info Bar', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => array(
					'video_display_details' => 'yes',
				),
			]
		);


		$this->add_control(
			'info_bar_title_color',
			array(
				'label'     => __( 'Title Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-display-details .eae-video-disply-title' => 'color: {{VALUE}};',
				),
				'condition'   => array(
					'video_display_details' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'		=> __('Title Typography', 'wts-eae'),
				'name'      => 'info_bar_title_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-display-details .eae-video-disply-title',
				'condition'   => array(
					'video_display_details' => 'yes',
				),
			)
		);

		$this->add_control(
			'info_bar_desc_color',
			array(
				'label'     => __( 'Description Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-display-details .eae-video-disply-desc' => 'color: {{VALUE}};',
				),
				'condition'   => array(
					'video_display_details' => 'yes',
				),
			)
		);

		

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'		=> __('Description Typography', 'wts-eae'),
				'name'      => 'info_bar_desc_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-display-details .eae-video-disply-desc',
				'condition'   => array(
					'video_display_details' => 'yes',
				),
			)
		);

		$this->add_control(
			'info_bar_bg_color',
			array(
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-display-details' => 'background-color: {{VALUE}};',
				),
				'condition'   => array(
					'video_display_details' => 'yes',
				),
			)
		);

		
		$this->add_responsive_control(
			'info_bar_padding',
			array(
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eae-video-outer-wrapper:not(.eae-sticky-apply) .eae-video-display-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'   => array(
					'video_display_details' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	public function play_icon_style_controls(){
		$this->start_controls_section(
			'play_icon_style',
			[
				'label' => esc_html__( 'Play Icon', 'wts-eae' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        Helper::global_icon_style_controls(
            $this,
            [
                'name'          => 'play_icon',
                'selector'      => '.eae-video-play-button',
                'is_repeater'   => 'false', 
            ]
        );

        $this->end_controls_section();
	}

	#add video sticky style controls
	public function video_sticky_style_controls(){
		#add section for video sticky style controls
		$this->start_controls_section(
			'video_sticky_style',
			[
				'label' => esc_html__( 'Sticky Video', 'wts-eae' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'enable_sticky' => 'yes'
				]
			]
		);

		#add sticky video background color controls
		$this->add_control(
			'sticky_video_background_color',
			[
				'label' => esc_html__( 'Background Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-video-container' => 'background-color: {{VALUE}};',
				],
			]
		);

		#add sticky video border radius controls
		$this->add_responsive_control(
			'sticky_video_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					// Without Detils
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply:not(:has(.eae-sticky-video-display-details)) .eae-video-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply:not(:has(.eae-sticky-video-display-details)) img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply:not(:has(.eae-sticky-video-display-details)) iframe' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					// With Detils
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply:has(.eae-sticky-video-display-details) .eae-video-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply:has(.eae-sticky-video-display-details) .eae-video-container img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply:has(.eae-sticky-video-display-details) .eae-video-container iframe' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply:has(.eae-sticky-video-display-details) .eae-sticky-video-display-details' => 'border-radius: 0 0 {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		#add sticky close button color control
		$this->add_control(
			'sticky_close_button_color',
			[
				'label' => esc_html__( 'Close Button Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-video-sticky-close' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'sticky_close_button_bg_color',
			[
				'label' => esc_html__( 'Close Button BG Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-video-sticky-close' => 'background-color: {{VALUE}};',
				],
			]
		);

		#Add Heading Controls for Sticky video display details
		$this->add_control(
			'sticky_video_display_details_heading',
			[
				'label' => __( 'Info Bar', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes',
					'sticky_video_display_details' => 'yes'
				),
			]
		);

		$this->add_control(
			'sticky_info_bar_title_color',
			array(
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-sticky-video-display-details .eae-video-disply-title' => 'color: {{VALUE}};',
				),
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes',
					'sticky_video_display_details' => 'yes'
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'		=> __('Title Typography', 'wts-eae'),
				'name'      => 'sticky_info_bar_title_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-sticky-video-display-details .eae-video-disply-title',
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes',
					'sticky_video_display_details' => 'yes'
				),
			)
		);

		$this->add_control(
			'sticky_info_bar_desc_color',
			array(
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-sticky-video-display-details .eae-video-disply-desc' => 'color: {{VALUE}};',
				),
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes',
					'sticky_video_display_details' => 'yes'
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label'		=> __('Descritpion Typography', 'wts-eae'),
				'name'      => 'sticky_info_bar_desc_typography',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector'  => '{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-sticky-video-display-details .eae-video-disply-desc',
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes',
					'sticky_video_display_details' => 'yes'
				),
			)
		);

		$this->add_control(
			'sticky_info_bar_bg_color',
			array(
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-sticky-video-display-details' => 'background-color: {{VALUE}};',
				),
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes',
					'sticky_video_display_details' => 'yes'
				),
			)
		);

		
		$this->add_responsive_control(
			'sticky_sticky_info_bar_padding',
			array(
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-video-sticky.eae-sticky-apply .eae-video-display-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes',
					'sticky_video_display_details' => 'yes'
				),
			)
		);


		$this->end_controls_section();
	}

	public function get_video_link( $settings ) {
		$video_type = $settings['video_type'];
		$video_link = '';
		switch ( $video_type ) {
			case 'youtube': $video_link = $settings['youtube_link'];
							break;
			case 'vimeo':   $video_link = $settings['vimeo_link'];
							break;
			case 'dailymotion':   $video_link = $settings['dailymotion_link'];
							break;
			case 'wistia':  $video_link = ( preg_match( '/https?\:\/\/[^\",]+/i', $settings['wistia_link'], $url ) ) ? $url[0] : '';
							break;
			case 'hosted':  if ( 'hosted' === $video_type && 'yes' !== $settings['insert_link'] ) {
								$video_link = $settings['hosted_link']['url'];
							} elseif ( 'hosted' === $video_type && 'yes' === $settings['insert_link'] ) {
								$video_link = $settings['external_link']['url'];
							}
							break;
			default:
		}
		return $video_link;
	}

	
    

    public function render(){
        $settings               = $this->get_settings_for_display();
        $is_editor              = \Elementor\Plugin::instance()->editor->is_edit_mode();
        $video_type = $settings['video_type'];
        if($video_type == ''){
            return;
        }
		if($video_type != 'hosted'){
			if(empty($settings[$video_type.'_link'])){
				return;
			}
		}else{
			if($settings['insert_link'] == 'yes'){
				if(empty($settings['external_link']['url'])){
					return;
				}	
			}else{
				if(empty($settings['hosted_link']['url'])){
					return;
				}
			}
		}
		#add video scheme
		if($settings['video_type'] != 'hosted'){
			$video_id  = $this->get_video_id();
		}	
		if(method_exists($this,$video_type.'_embed_params')){
			$embed_param = call_user_func( array( $this, $video_type.'_embed_params' ));
		}

		if ( 'hosted' !== $settings['video_type'] ) {
			$src = $this->eae_get_url( $embed_param, $video_id );
		} else {
			$src = $this->eae_get_hosted_video_url();
		}
		
		if(empty($src)){
			return;
		}
		$video_link = $this->get_video_link( $settings );
		
		$enable_schema = $settings['schema_support'];
		if($settings['schema_support'] == 'yes' ){
			$no_schema = false;
			$is_custom_thumbnail    = 'yes' === $settings['show_image_overlay'] ? true : false;
			$custom_thumbnail_url   = isset( $settings['image_overlay']['url'] ) ? $settings['image_overlay']['url'] : '';
			if ( ('yes' === $enable_schema ) && 
				( ( 
					'' === $settings['schema_title'] || 
					'' === $settings['schema_description'] || 
					( ! $is_custom_thumbnail && '' === $settings['schema_thumbnail']['url'] ) || 
					'' === $settings['schema_upload_date'] 
				) 
					|| ( $is_custom_thumbnail && '' === $custom_thumbnail_url ) || ( '' === $video_link)
				) ) {
					$no_schema = true;
			}
			
			
			if($no_schema == false){
				$video_schema_data = array(
					'@context'     => 'https://schema.org',
					'@type'        => 'VideoObject',
					'name'         => $settings['schema_title'],
					'description'  => $settings['schema_description'],
					'thumbnailUrl' => ( $is_custom_thumbnail ) ? $custom_thumbnail_url : $settings['schema_thumbnail']['url'],
					'uploadDate'   => $settings['schema_upload_date'],
					'contentUrl'   => $video_link,
					'embedUrl'     => $video_link,
				);
				Pro::$schemas[] = $video_schema_data; 
			}
		}
		$autoplay = '';
		if($settings['lightbox'] != 'yes'){
			switch ( $settings['video_type'] ) {

				case 'youtube':
					$autoplay = ( 'yes' === $settings['yt_autoplay'] ) ? '1' : '0';
					break;
	
				case 'vimeo':
					$autoplay = ( 'yes' === $settings['vimeo_autoplay'] ) ? '1' : '0';
					break;
	
				case 'wistia':
					$autoplay = ( 'yes' === $settings['wistia_autoplay'] ) ? '1' : '0';
					break;
				case 'dailymotion':
					$autoplay = ( 'yes' === $settings['dailymotion_autoplay'] ) ? '1' : '0';
					break;
	
				case 'hosted':
					$autoplay = ( 'yes' === $settings['autoplay'] ) ? '1' : '0';
					break;
	
					
				default:
					break;
			}
		}

		


		$this->add_render_attribute('eae-video-outer' , 'class', 'eae-video-outer-wrapper');
		$this->add_render_attribute( 'eae-video-outer', 'class', 'eae-video-type-' . $settings['video_type'] );
		$this->add_render_attribute( 'eae-video-outer', 'data-video-type',  $settings['video_type'] );
		
		$this->add_render_attribute( 'eae-video-wrapper', 'class', ['eae-video-wrappper', 'eae-video-play'] );
		if ( $settings['video_type'] == 'hosted' && $settings['download_button'] != 'yes') {
			$this->add_render_attribute( 'eae-video-wrapper', 'data-video-downaload',"nodownload");
		}
		if($settings['lightbox'] != 'yes'){
			if($settings['video_type'] != 'hosted'){
				$this->add_render_attribute( 'eae-video-wrapper', 'data-src', $src );
			}
		}

		$this->add_render_attribute( 'eae-video-thumb', 'class', 'eae-video-thumb' );
		$this->add_render_attribute( 'eae-video-play', 'class', 'eae-video-play-icon' );

		// Sticky
		if($settings['enable_sticky'] == 'yes'){
			if('yes' == $settings['preview_sticky']){
				$this->add_render_attribute( 'eae-video-outer', 'data-preview-sticky', 'yes' );
			}
			$this->add_render_attribute( 'eae-video-outer', 'class', 'eae-video-sticky' );
			$this->add_render_attribute( 'eae-video-outer', 'class', 'eae-asp-ratio-'. $settings['aspect_ratio'] );
			$this->add_render_attribute( 'eae-video-outer', 'data-video-sticky', 'yes' );
			$this->add_render_attribute( 'eae-video-outer', 'class', 'eae-video-hpos-'.$settings['sticky_horizontal_position'] );
			$this->add_render_attribute( 'eae-video-outer', 'class', 'eae-video-vpos-'.$settings['sticky_vertical_position'] );
		}
		if($settings['preivew_video_display_details'] == 'yes' && $settings['video_display_details'] == 'yes'){
			#elementor is edit mode
			if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
				$this->add_render_attribute( 'eae-video-outer', 'class', 'preview-info' );
			}
		}

		if($settings['mask_video_shape'] == 'yes'){
			$this->add_render_attribute( 'eae-video-outer', 'class', 'eae-video-mask-media' );
		}

		if($autoplay){
			$this->add_render_attribute( 'eae-video-outer', 'data-autoplay', $autoplay );
		}

		// Hosted Video Render
		if ( 'hosted' === $settings['video_type'] ) {
			$video_url = $this->eae_get_hosted_video_url();
			ob_start();

			$this->render_hosted_video();

			

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
		if($settings['lightbox'] == 'yes'){
			
			$this->add_render_attribute( 'eae-video-wrapper', 'data-download-url',false);
			$this->add_render_attribute( 'eae-video-wrapper', 'data-tweet-text','');
			$this->add_render_attribute( 'eae-video-wrapper', 'data-pin-text','');
			
			$this->add_render_attribute( 'eae-video-outer', 'data-lightbox',  'yes');
			if($settings['lightgallery_hash'] == 'yes'){
				$this->add_render_attribute( 'eae-video-wrapper', 'data-gallery-id', isset($settings['lightgallery_galleryId']) ? $settings['lightgallery_galleryId'] : 1);
			}
			if($settings['lightgallery_fullscreen'] == 'yes'){
				$this->add_render_attribute( 'eae-video-wrapper', 'data-fullscreen', 'yes');
			}
			if($settings['lightgallery_share'] == 'yes'){
				$this->add_render_attribute( 'eae-video-wrapper', 'data-share', 'yes');
			}
			if($settings['video_type'] != 'hosted'){
				$this->add_render_attribute( 'eae-video-wrapper', 'data-src',  $video_link);
				$this->add_render_attribute( 'eae-video-wrapper', 'data-params', json_encode($embed_param) );
			}else{
				if($settings['controls']){
					$controls = 'true';
				}else{
					$controls = 'false';
				}
				$self_hosted_video_params = [
					'muted' => $settings['muted'] == 'yes' ? true : false,
					'controls' => $settings['controls'] == 'yes' ? true : false,
					'loop' => $settings['loop'] == 'yes' ? true : false,
					'autoplay' => $settings['autoplay'] == 'yes' ? true : false,
				];
				$this->add_render_attribute( 'eae-video-wrapper', 'data-params', json_encode($self_hosted_video_params) );
				$this->add_render_attribute( 'eae-video-wrapper', 'data-video-controls',$settings['controls'] == 'yes' ? true : false);
				$this->add_render_attribute( 'eae-video-wrapper', 'data-video-loop',$settings['controls'] == 'yes' ? true : false);
				$this->add_render_attribute( 'eae-video-wrapper', 'data-video', 
				'{
					"source": [{"src":"'.$video_link.'", "type":"video/mp4"}], 
					"attributes": {"preload": false, "controls":'. $controls  .'}
				}');
			}
		}

		// thumbnail
		if ( 'hosted' !== $settings['video_type'] ) {
			$this->add_render_attribute( 'eae-video-thumb', 'src', $this->eae_get_video_thumb( $video_id ) );
		}else{
			if ( 'yes' === $settings['show_image_overlay'] ) {
				$thumb = Group_Control_Image_Size::get_attachment_image_src( $settings['image_overlay']['id'], 'image_overlay', $settings );
			} else {
				$thumb = $this->eae_get_hosted_video_url();
			}
			$this->add_render_attribute( 'eae-video-thumb', 'src', $thumb );
		}
		// self hosted
		if ( 'hosted' === $settings['video_type'] && 'yes' !== $settings['show_image_overlay'] ) {
			$custom_tag = 'video';
		} else {
			$custom_tag = 'img';
		}	
		
		if(empty($src)){ ?>
			<div class= "message">
				<p class="elementor-alert elementor-alert-warning">No video added. Please add video.</p>
			</div>
		<?php }
		else{
        ?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'eae-video-outer' ) ); ?>>
			<div class="eae-video-container">
				<div <?php echo $this->get_render_attribute_string( 'eae-video-wrapper' ); ?> >
					<<?php echo esc_attr( $custom_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'eae-video-thumb' ) ); ?>></<?php echo esc_attr( $custom_tag ); ?>>
					<?php Helper::render_icon_html($settings, $this, 'play_icon','eae-video-play-button'); ?>
				</div>
				<!-- no kaam, ka -->
				<?php if ( 'yes' === $settings['enable_sticky'] && 'yes' === $settings['enable_sticky_close_button'] ) { ?>
					<div class="eae-video-sticky-close">
						<i class="fas fa-times eae-sticky-close-icon"></i>
					</div>
				<?php } ?>
				<?php if ( 'yes' === $settings['video_display_details']) { ?>
					<?php if(!empty($settings['video_display_title']) || !empty($settings['video_display_desc'])){
						$this->add_render_attribute('eae-video-display-details', 'class', 'eae-video-display-details');
						if($settings['sticky_video_display_details'] == 'yes'){
							$this->add_render_attribute('eae-video-display-details', 'class', 'eae-sticky-video-display-details');
						}
						?>
					<div <?php echo $this->get_render_attribute_string('eae-video-display-details'); ?> >
					<?php if(!empty($settings['video_display_title'])){?>
						<span class="eae-video-disply-title"><?php echo Helper::eae_wp_kses($settings['video_display_title']);?></span>	
					<?php } ?>
					<?php if(!empty($settings['video_display_desc'])){?>
						<span class="eae-video-disply-desc"><?php echo Helper::eae_wp_kses($settings['video_display_desc']);?></span>	
					<?php } ?>
					</div>
					<?php } ?>
				<?php } ?>
			</div>	
		</div>
        <?php
		}

	?>
		
	<?php			
    }

	

	public function render_hosted_video()
	{
		$settings = $this->get_settings_for_display();
		$video_url = $this->eae_get_hosted_video_url();
		$video_params = $this->get_hosted_parameter();
		$video_html = '';
		if ( ! empty( $video_url ) ) {
			?>
			<video class="eae-hosted-video" src="<?php echo esc_url( $video_url ); ?>" <?php echo esc_attr( Utils::render_html_attributes( $video_params ) ); ?>></video>
			<?php	
		}
	}

	protected function register_video_sticky_controls() {
		$this->start_controls_section(
			'section_sticky',
			array(
				'label' => __( 'Sticky Video', 'wts-eae' ),
			)
		);

		$this->add_control(
			'enable_sticky',
			array(
				'label'     => __( 'Enable Sticky Video', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on'  => __( 'Yes', 'wts-eae' ),
				'default'   => 'no',
			)
		);

		$this->add_control(
			'preview_sticky',
			array(
				'label'     => __( 'Preview Sticky Video', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on'  => __( 'Yes', 'wts-eae' ),
				'return_value' => 'yes',
				'default'   => 'no',
				'condition'      => array(
					'enable_sticky' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'sticky_video_width',
			array(
				'label'          => __( 'Video Width (px)', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
				),
				'default'        => array(
					'size' => 320,
					'unit' => 'px',
				),
				'mobile_default' => array(
					'size' => 250,
					'unit' => 'px',
				),
				'condition'      => array(
					'enable_sticky' => 'yes',
				),
				'selectors'      => array(
					'{{WRAPPER}} .eae-video-sticky.eae-sticky-apply .eae-video-container' => 'width: {{SIZE}}px;',
					'{{WRAPPER}}' => '--sticky-width : {{SIZE}}px'
				),
			)
		);

		$this->add_control(
			'sticky_horizontal_position',
			[
				'label'       => __( 'Horizontal Position', 'ae-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'ae-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'ae-pro' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'ae-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'left',
				'condition'      => array(
					'enable_sticky' => 'yes',
					
				),
			]
		);


		$this->add_control(
			'sticky_vertical_position',
			[
				'label'       => __( 'Vertical Position', 'ae-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top' => [
						'title' => __( 'Top', 'ae-pro' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'ae-pro' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'ae-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'top',
				'condition'      => array(
					'enable_sticky' => 'yes',
				),
			]
		);

		$this->add_responsive_control(
			'horizontal_arrow_offset',
			[
				'label'          => __( 'Horizontal Offset', 'ae-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-sticky-apply.eae-video-hpos-left .eae-video-container' => 'left: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-sticky-apply.eae-video-hpos-right .eae-video-container' => 'right: {{SIZE}}{{UNIT}} !imporatnt;',
				],
				'condition'      => array(
					'enable_sticky' => 'yes',
					'sticky_horizontal_position!' => 'center'
				),
			]
		);
		$this->add_responsive_control(
			'vertical_arrow_offset',
			[
				'label'          => __( 'Vertical Offset', 'ae-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-sticky-apply.eae-video-vpos-top .eae-video-container' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-sticky-apply.eae-video-vpos-bottom .eae-video-container' => 'bottom: {{SIZE}}{{UNIT}}',

				],
				'condition'      => array(
					'enable_sticky' => 'yes',
					'sticky_vertical_position!' => 'middle'
				),
			]
		);

		$this->add_responsive_control(
			'sticky_video_padding',
			array(
				'label'      => __( 'Background Space', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eae-video-outer-wrapper.eae-sticky-apply iframe, {{WRAPPER}} .eae-video-outer-wrapper.eae-sticky-apply .eae-video-thumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'enable_sticky' => 'yes',
				),
			)
		);


		$this->add_control(
			'heading_sticky_close_button',
			array(
				'label'     => __( 'Close Button', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'enable_sticky' => 'yes',
				),
			)
		);

		$this->add_control(
			'enable_sticky_close_button',
			array(
				'label'     => __( 'Enable', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on'  => __( 'Yes', 'wts-eae' ),
				'default'   => 'yes',
				'condition' => array(
					'enable_sticky' => 'yes',
				),
			)
		);

		$this->add_control(
			'stikcy_video_display_heading',
			array(
				'label'     => __( 'Video Detials', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes'
				),
			)
		);

		$this->add_control(
			'sticky_video_display_details',
			array(
				'label'       => __( 'Enable', 'wts-eae' ),
				'description' => __( 'Enable this option to display the informative text under Sticky video.', 'wts-eae' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_off'   => __( 'No', 'wts-eae' ),
				'label_on'    => __( 'Yes', 'wts-eae' ),
				'default'     => 'no',
				'return_value' => 'yes',
				'condition'   => array(
					'enable_sticky' => 'yes',
					'video_display_details' => 'yes'
				),
			)
		);

		$this->end_controls_section();
	}


	protected function eae_get_video_thumb( $id ) {

		if ( '' === $id ) {
			return '';
		}

		$settings = $this->get_settings_for_display();
		$thumb    = '';
		
		if ( 'yes' === $settings['show_image_overlay'] ) {
			$thumb = Group_Control_Image_Size::get_attachment_image_src( $settings['image_overlay']['id'], 'image_overlay', $settings );
			if(empty($thumb)){
				$thumb = $settings['image_overlay']['url'];
			}
		} else {
			if ( 'youtube' === $settings['video_type'] ) {
				$thumb = 'https://i.ytimg.com/vi/' . $id . '/' . apply_filters( 'eae_video_youtube_image_quality', $settings['yt_thumbnail_size'] ) . '.jpg';
			} elseif ( 'vimeo' === $settings['video_type'] ) {
				$response = wp_remote_get( "https://vimeo.com/api/v2/video/$id.php" );
				if ( is_wp_error( $response ) || 404 === $response['response']['code'] ) {
					return;
				}
				$vimeo = maybe_unserialize( $response['body'] );
				// privacy enabled videos don't return thumbnail data.
				$thumb = ( isset( $vimeo[0]['thumbnail_large'] ) && ! empty( $vimeo[0]['thumbnail_large'] ) ) ? str_replace( '_640', '_840', $vimeo[0]['thumbnail_large'] ) : '';

			} elseif ( 'wistia' === $settings['video_type'] ) {
				$url   = $settings['wistia_link'];
				$thumb = 'https://embedwistia-a.akamaihd.net/deliveries/' . $this->getStringBetween( $url, 'deliveries/', '?' );
			}
			elseif ( 'dailymotion' === $settings['video_type'] ) {
				$video_data = wp_remote_get( 'https://api.dailymotion.com/video/' . $id . '?fields=thumbnail_url' );
				if ( isset( $video_data['response']['code'] ) ) {
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



	private function eae_get_hosted_video_url() {
		$settings = $this->get_settings_for_display();
		
		
		if ( $settings['insert_link'] == 'yes') {
			$video_url = $settings['external_link']['url'];
		} else {
			$video_url = isset( $settings['hosted_link']['url'] ) ? $settings['hosted_link']['url'] : '';
		}
		
		if ( empty( $video_url ) ) {
			return '';
		}
		if ( $settings['start'] || $settings['end'] ) {
			$video_url .= '#t=';
		}

		if ( $settings['start'] ) {
			$video_url .= $settings['start'];
		}

		if ( $settings['end'] ) {
			$video_url .= ',' . $settings['end'];
		}
		return $video_url;
	}



	// same copy
	public function youtube_embed_params(){
		$settings = $this->get_settings_for_display();
		$youtube_options = array( 'autoplay', 'rel', 'controls', 'mute', 'modestbranding' );
		foreach ( $youtube_options as $option ) {

			if ( 'autoplay' === $option ) {
				if ( 'yes' === $settings['yt_autoplay'] && $settings['lightbox'] != 'yes') {
					$params[ $option ] = 1;
				}
				continue;
			}

			$value             = ( 'yes' === $settings[ 'yt_' . $option ] ) ? 1 : 0;
			$params[ $option ] = $value;
			$params['start']   = $settings['start'];
			$params['end']     = $settings['end'];
		}	
		// echo '<pre>';  print_r($params); echo '</pre>';
		$params = apply_filters( 'eae_youtube_params', $params );
		return $params;
	}

	// same copy
	public function vimeo_embed_params(){
		$settings = $this->get_settings_for_display();
		$vimeo_options = array( 'autoplay', 'loop', 'title', 'portrait', 'byline', 'muted' );
			foreach ( $vimeo_options as $option ) {
				if ( 'autoplay' === $option ) {
					if ( 'yes' === $settings['vimeo_autoplay'] ) {
						$params[ $option ] = 1;
					}
					continue;
				}
				$value             = ( 'yes' === $settings[ 'vimeo_' . $option ] ) ? 1 : 0;
				$params[ $option ] = $value;
			}
			$params['color']     = str_replace( '#', '', $settings['vimeo_color'] );
			$params['autopause'] = '0';
			$params = apply_filters( 'eae_vimeo_params', $params );
			return $params;
	}


	public function dailymotion_embed_params(){
		$settings = $this->get_settings_for_display();
		
		$dailymotion_options = array( 'autoplay', 'mute', 'controls', 'sharing-enable' );
		foreach ( $dailymotion_options as $option ) {

			if ( 'autoplay' === $option ) {
				if ( 'yes' === $settings['dailymotion_autoplay'] ) {
					$params[ $option ] = '1';
				}
				continue;
			}
			if($settings['start'] != ''){
				$params[ 'start' ] = $settings['start'];
			}

			$value             = ( 'yes' === $settings[ 'dailymotion_' . $option ] ) ? 'true' : 'false';
			$params[ $option ] = $value;
		}
		$params = apply_filters( 'eae_dailymotion_params', $params );
		return $params;		
	}


	public function wistia_embed_params(){
		$settings = $this->get_settings_for_display();
		$wistia_options = array( 'autoplay', 'muted', 'playbar', 'loop' );
			foreach ( $wistia_options as $option ) {

				if ( 'autoplay' === $option ) {
					if ( 'yes' === $settings['wistia_autoplay'] ) {
						$params[ $option ] = 1;
					}
					continue;
				}

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

	private function get_hosted_parameter() {
		$settings = $this->get_settings_for_display();
		$parmas = [
			'loop',
			'controls',
		];
		if($settings['lightbox'] != 'yes' ){
			$parmas[] = 'autoplay';
		}	
		foreach ($parmas as $option_name ) {
			if ( $settings[ $option_name ] ) {
				$video_params[ $option_name ] = '';
			}
		}
		if ( $settings['muted']) {
			$video_params['muted'] = 'muted';
		}
		if ( $settings['download_button'] != 'yes') {
			$video_params['controlslist'] = "nodownload";
		}

		return $video_params;
	}

	// Same Copy
	protected function eae_get_url( $params, $id ) {

		$settings = $this->get_settings_for_display();
		$url      = '';

		if ( 'vimeo' === $settings['video_type'] ) {

			$url = 'https://player.vimeo.com/video/';

		} elseif ( 'youtube' === $settings['video_type'] ) {

			$cookie = '';

			if ( 'yes' === $settings['yt_privacy'] ) {
				$cookie = '-nocookie';
			}
			$url = 'https://www.youtube' . $cookie . '.com/embed/';

		} elseif ( 'wistia' === $settings['video_type'] ) {
			$url = 'https://fast.wistia.net/embed/iframe/';
		}

		elseif('dailymotion' === $settings['video_type']){
			$url = 'https://dailymotion.com/embed/video/';
		}
		
		$url = add_query_arg( $params, $url . $id );
		
		$url .= ( empty( $params ) ) ? '?' : '&';
		
		$url .= 'autoplay=1';

		if ( 'vimeo' === $settings['video_type'] && '' !== $settings['start'] ) {
			$time = gmdate( 'H\hi\ms\s', $settings['start'] );
			$url .= '#t=' . $time;
		} elseif ( 'vimeo' === $settings['video_type'] ) {
			$url .= '#t=';
		}

		$url = apply_filters( 'eae_video_url_filter', $url, $id );

		return $url;
	}

	// same copy
	public function get_video_id(){
		$settings = $this->get_settings_for_display();
		$id       = '';
		$url      = $settings[ $settings['video_type'] . '_link' ];
		 
		
		if ( 'youtube' === $settings['video_type'] ) {
			if ( preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches ) ) {

				 
				$id = $matches[1];	
			}
		} elseif ( 'vimeo' === $settings['video_type'] ) {
			if ( preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs ) ) {
				$id = $regs[3];
			}
		} elseif ( 'wistia' === $settings['video_type'] ) {
			$id = $this->getStringBetween( $url, 'wvideo=', '"' );
		}elseif('dailymotion' === $settings['video_type']) {
			$id = $this->getDailyMotionId($url);
		}
		return $id;
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

	
    
}        