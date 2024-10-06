<?php
namespace WTS_EAE\Pro\Modules\InstagramFeed\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use WTS_EAE\Base\EAE_Widget_Base;
use WTS_EAE\Classes\Helper;
use Elementor\Plugin as EPlugin;
use WTS_EAE\Classes\Swiper_helper;
use WTS_EAE\Classes\Lightgallery_helper;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class InstagramFeed extends EAE_Widget_Base {

	private $eae_insta_graph_api_url = 'https://graph.instagram.com/';

	private $eae_media_endpoint = '/me/media';

	public function get_name() {
		return 'eae-instagram-feed';
	}

	public function get_title() {
		return __( 'Instagram Feed', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-instagram-feed';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [];
	}

	public function get_script_depends() {

		//Light Gallery JS
		$lg_script = [
			'lightgallery-js', 
			'lg-video-js', 
			'lg-fullscreen-js', 
			'lg-share-js', 
			'lg-hash-js', 
			'lg-zoom-js', 
			'lg-rotate-js', 
			'lg-thumbnail-js'
		];
		// load all scripts in editor and preview mode
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return array_merge( ['jquery-masonry', 'swiper'], $lg_script);

		}

		$scripts  = [];
		$settings = $this->get_settings();

		if ( $settings['insta_feed_layout'] === 'masonry' ) {
			$scripts[] = 'jquery-masonry';
		}
		
		if( $settings['enable_lightbox'] === 'yes'){
			$scripts = array_merge($scripts, $lg_script);
		}

		return $scripts;
	}

	public function get_style_depends()
	{
		return ['lightgallery-css','eae-video-css'];
	}


	protected function get_eae_instagram_token($settings){
		$token = $settings['access_token'];
		return $token;
	}

	protected function get_eae_insta_fetch_data($settings){
		$insta_url = $this->eae_insta_graph_api_url.$this->eae_media_endpoint;
		$insta_url = add_query_arg(
			array(
				'fields'       => 'id,media_type,media_url,thumbnail_url,permalink,caption,likes,username,children',
				'limit'        => 50, //$settings['post_count'],
				'access_token' =>  $this->get_eae_instagram_token($settings),
			),
			$insta_url
		);
		return $this->get_remote_response($insta_url);
	}

	protected function get_eae_insta_fetch_data_by_id($settings, $id){
		$insta_url = $this->eae_insta_graph_api_url.$id;
		$insta_url = add_query_arg(
			array(
				'fields'       => 'id,media_type,media_url,permalink,thumbnail_url', // for Carousel Media
				'access_token' =>  $this->get_eae_instagram_token($settings),
			),
			$insta_url
		);

		return $this->get_remote_response($insta_url);
	}

	protected function get_remote_response($insta_url){
		$response = wp_remote_get(
			$insta_url,
			array(
				'timeout'   => 60,
				'sslverify' => false,
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		$result = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_code ) {
			$message = isset( $result['error']['message'] ) ? $result['error']['message'] : __( 'No posts found', 'wts-eae' );
			return new \WP_Error( $response_code, $message );
		}

		return $result;
	}
	
	protected function check_eae_insta_cache_data($settings){
		$transient_key = 'eae_insta_fetched_data_' . $this->get_id(). '_' . $settings['post_count'] . '_' . $settings['cache_timeout'] . '_' . $settings['insta_caption_size'];

		$data = get_transient( $transient_key );
		if(is_wp_error($data)){
			delete_transient($transient_key);
			$data = [];
		}

		if ( ! empty( $data ) && $settings['cache_timeout'] !== 'none' ) {
			return $data;
		}

		$data = $this->get_eae_insta_fetch_data($settings);

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( empty( $data ) ) {
			return [];
		}

		set_transient( $transient_key, $data, $this->get_cache_duration($settings) );

		return $data;
	}

	protected function check_eae_insta_cache_data_by_id($settings, $id){
		$transient_key = 'eae_insta_fetched_data_cm_' . $id . '_' . $this->get_id(). '_' . $settings['post_count'] . '_' . $settings['cache_timeout'] . '_' . $settings['insta_caption_size'];

		$data = get_transient( $transient_key );
		if(is_wp_error($data)){
			delete_transient($transient_key);
			$data = [];
		}

		if ( ! empty( $data ) && $settings['cache_timeout'] !== 'none' ) {
			return $data;
		}

		$data = $this->get_eae_insta_fetch_data_by_id($settings, $id);

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( empty( $data ) ) {
			return [];
		}

		set_transient( $transient_key, $data, $this->get_cache_duration($settings) );

		return $data;
	}

	public function get_cache_duration($settings) {

		$cache_timeout = $settings['cache_timeout'];
		$expiration = 0;

		switch ( $cache_timeout ) {
			case 'minute':
				$expiration = MINUTE_IN_SECONDS;
				break;
			case 'hour':
				$expiration = HOUR_IN_SECONDS;
				break;
			case 'day':
				$expiration = DAY_IN_SECONDS;
				break;
			case 'week':
				$expiration = WEEK_IN_SECONDS;
				break;
			default:
				break;
		}

		return $expiration;
	}

	protected function get_eae_insta_posts($settings){
		//check if token is expired
		$this->refresh_expired_token();

		$posts = $this->check_eae_insta_cache_data($settings);
		$is_editor = EPlugin::instance()->editor->is_edit_mode();

		if ( ( is_wp_error( $posts ) || empty( $posts )) && $is_editor ) {
			$message = is_wp_error( $posts ) ? $posts->get_error_message() : esc_html__( 'No Posts Found', 'wts-eae' );
			echo wp_kses_post( $message );

			return;
		}

		return $posts;
	}

	protected function refresh_expired_token(){
		$settings = $this->get_settings_for_display();
        $transient_key = 'eae_insta_fetched_data_refresh_' . $this->get_id();
        
        $transient_data = get_transient($transient_key);

        if(!empty($transient_data)){
            return;
        }

        $update_url = 'https://graph.instagram.com/refresh_access_token';

        $access_token = $this->get_eae_instagram_token($settings);
        
        $endpoint_url = add_query_arg(
            [
                'access_token' => $access_token,
				'grant_type'   => 'ig_refresh_token',
            ],
            $update_url
        );
        $response = wp_remote_get( $endpoint_url );

        if(!$response || 200 !== wp_remote_retrieve_response_code( $response ) || is_wp_error( $response ) ){
			return;
        }
        $body = wp_remote_retrieve_body( $response );
	
		if ( ! $body ) {
			return;
		}
        $body = json_decode( $body, true );
		if ( empty( $body['access_token'] ) || empty( $body['expires_in'] ) ) {
			return;
		}
        set_transient($transient_key, 'updated', 30 * DAY_IN_SECONDS);
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_insta_profile',
			[
				'label' => __( 'Profile', 'wts-eae' ),
			]
		);
		
		$this->add_control(
			'refresh_cache',
			[
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'eae-refresh-cache-btn',
				'raw'  => __( '<button type="button">Refresh Cache</button>', 'ae-pro' ),
				'separator'       => 'after',
			]
		);
		 
		$this->add_control(
			'post_id',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => get_the_ID(),
			]
		);

		$this->add_control(
			'access_token',
			[
				'type' => Controls_Manager::TEXTAREA,
				'label' => __( 'Token', 'wts-eae' ),
			]
		);

		$this->add_control(
			'post_count',
			[
				'type' => Controls_Manager::NUMBER,
				'label' => __( 'Post Count', 'wts-eae' ),
				'default' => 6,
			]
		);

		$this->add_control(
			'insta_image',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Image', 'wts-eae' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'insta_video',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Video', 'wts-eae' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'insta_carousel',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Album', 'wts-eae' ),
				'default' => 'yes',
			]
		);
		
		$this->add_control(
			'insta_profile_link',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Profle Link', 'wts-eae' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cache_timeout',
			array(
				'label'       => esc_html__( 'Cache Timeout', 'wts-eae' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'hour',
				'options'     => array(
					'none'   => esc_html__( 'None', 'wts-eae' ),
					'minute' => esc_html__( 'Minute', 'wts-eae' ),
					'hour'   => esc_html__( 'Hour', 'wts-eae' ),
					'day'    => esc_html__( 'Day', 'wts-eae' ),
					'week'   => esc_html__( 'Week', 'wts-eae' ),
				),
			)
		);
		$this->end_controls_section();

		$this->get_section_layout();

		$this->get_section_profile_link();

		$this->get_section_flex();

		$this->get_section_carousel();

		$this->get_section_lightbox();

		$this->get_insta_profile_link_style_controls();

		$this->get_insta_images_style_controls();

		$this->get_insta_caption_style_controls();

		$this->get_carousel_style_controls();

	}

	public function get_section_layout(){
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Layout', 'wts-eae' ),
			]
		);

		$this->add_control(
			'insta_feed_layout',
			[
				'label'			=> __( 'Layout', 'wts-eae' ),
				'type'			=> Controls_Manager::SELECT,
				'default'		=> 'masonry',
				'options' 		=> [
					'grid' 		=> __( 'Grid', 'wts-eae' ),
					'flex' 		 => __( 'Flex', 'wts-eae' ),
					'masonry'	=> __( 'Masonry', 'wts-eae' ),
					'carousel'	=> __( 'Carousel', 'wts-eae' ),
				],
				'frontend_available' => true
			]
		);

		$this->add_responsive_control(
			'insta_feed_columns',
			[
				'label'			=> __( 'Columns', 'wts-eae' ),
				'type'			=> Controls_Manager::NUMBER,
				'default' => '3',
				'min'             => 1,
				'max'             => 12,
				'selectors'		=> [
					'{{WRAPPER}} .eae-insta-layout-grid .eae-post-collection' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
					'{{WRAPPER}} .eae-insta-layout-masonry .eae-post-collection' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
				'condition' => [
					'insta_feed_layout' => ['grid', 'masonry'],
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'insta_feed_column_gap',
			[
				'label'     => __( 'Column Gap', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-insta-layout-grid .eae-post-collection' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-insta-layout-masonry .eae-post-collection' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'insta_feed_layout' => ['grid', 'masonry'],
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'insta_feed_row_gap',
			[
				'label'     => __( 'Row Gap', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-insta-layout-grid .eae-post-collection' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-instagram-feed-wrap.eae-insta-layout-masonry .grid-gap' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'insta_feed_layout' => ['grid', 'masonry'],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'enable_insta_feed_image_ratio',
			[
				'label'        => __( 'Enable Image Ratio', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_responsive_control(
			'insta_feed_image_ratio',
			[
				'label'          => __( 'Image Ratio', 'ae-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => [
					'size' => 0.66,
				],
				'tablet_default' => [
					'size' => '',
				],
				'mobile_default' => [
					'size' => 0.5,
				],
				'range'          => [
					'px' => [
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					],
				],
				'selectors'      => [
					'{{WRAPPER}} .eae_image_ratio_yes .eae-insta-post-link' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
				'condition'      => [
					'enable_insta_feed_image_ratio' => 'yes',
				],
			]
		);

		$this->add_control(
			'insta_caption',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Caption', 'wts-eae' ),
				'default' => '',
			]
		);

		$this->add_control(
			'insta_caption_size',
			array(
				'label'     => __( 'Caption Size', 'wts-eae' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 30,
				'condition' => [
					'insta_caption' => 'yes',
				],
			)
		);

		$this->add_control(
			'insta_caption_layout',
			[
				'label'			=> __( 'Caption Style', 'wts-eae' ),
				'type'			=> Controls_Manager::SELECT,
				'default'		=> 'below',
				'options' 		=> [
					'below' 	=> __( 'Below', 'wts-eae' ),
					'hover' 	=> __( 'On Hover', 'wts-eae' ),
					'always'	=> __( 'Always', 'wts-eae' ),
				],
				'condition' => [
					'insta_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'insta_caption_overlay',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Caption Overlay Full', 'wts-eae' ),
				'default' => '',
				'condition' => [
					'insta_caption' => 'yes',
				],
			]
		);

		$this->add_control(
			'enable_link',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Enable Link', 'wts-eae' ),
				'default' => '',
			]
		);

		$this->add_control(
			'enable_lightbox',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Lightbox', 'wts-eae' ),
				'default' => '',
				'condition' => [
					'enable_link' => 'yes',
				],
			]
		);

		$this->add_control(
			'enable_insta_feed_icon',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Post Icon', 'wts-eae' ),
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	public function get_section_profile_link(){
		$this->start_controls_section(
			'section_profile',
			[
				'label' => __( 'Profile Link', 'wts-eae' ),
				'condition' => [
					'insta_profile_link' => 'yes',
				]
			]
		);

		$this->add_control(
			'insta_profile_position',
			[
				'label'			=> __( 'Position', 'wts-eae' ),
				'type'			=> Controls_Manager::SELECT,
				'default'		=> 'above',
				'options' 		=> [
					'above'		=> __( 'Above', 'wts-eae' ),
					'below'		=> __( 'Below', 'wts-eae' ),
				]
			]
		);

		$this->add_control(
			'insta_profile_link_text',
			[
				'label'			=> __( 'Text', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Follow us on Instagram', 'wts-eae' ),
			]
		);

		$this->add_control(
			'insta_profile_link_url',
			[
				'label'			=> __( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'insta_profile_link_icon',
			[
				'label'            => __( 'Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'recommended'      => array(
					'fa-brands'  => array(
						'instagram',
					),
					'fa-regular' => array(
						'user',
						'user-circle',
					),
					'fa-solid'   => array(
						'user',
						'user-circle',
						'user-check',
						'user-graduate',
						'user-md',
						'user-plus',
						'user-tie',
					),
				),
				'default'          => array(
					'value'   => 'fab fa-instagram',
					'library' => 'fa-brands',
				),
			]
		);

		$this->add_control(
			'insta_profile_icon_position',
			[
				'label'     => __( 'Icon Position', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'before' => __( 'Before', 'wts-eae' ),
					'after'  => __( 'After', 'wts-eae' ),
				),
				'default'   => 'before',
			]
		);

		$this->add_control(
			'insta_profile_link_align',
			array(
				'label'     => __( 'Alignment', 'wts-eae' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .eae-insta-profile-link-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function get_section_flex(){
		$wrapper = '{{WRAPPER}} .eae-insta-layout-flex .eae-post-collection';

		$this->start_controls_section(
			'section_flex',
			[
				'label' => __( 'Flex', 'wts-eae' ),
				'condition' => [
					'insta_feed_layout' => 'flex',
				]
			]
		);

		Helper::eae_flex_controls($this, $wrapper);

		$this->end_controls_section();
	}
	
	public function get_section_carousel(){
		$this->start_controls_section(
			'section_carousel',
			[
				'label' => __( 'Carousel', 'wts-eae' ),
				'condition' => [
					'insta_feed_layout' => 'carousel',
				]
			]
		);
		
		Swiper_helper::carousel_controls($this);

		$this->end_controls_section();
	}

	public function get_section_lightbox(){
		$this->start_controls_section(
			'section_lightbox',
			[
				'label' => __( 'LightBox', 'wts-eae' ),
				'condition' => [
					'enable_lightbox' => 'yes',
					'enable_link' => 'yes'
				]
			]
		);
		
		Lightgallery_helper::add_controls($this);

		$this->end_controls_section();
	}

	public function render(){
		$settings = $this->get_settings_for_display();
		$insta_data = $this->get_eae_insta_posts($settings);
		$data_settings['insta_video'] = $settings['insta_video'];

		$insta_feed_wrap_classes[] = 'eae-instagram-feed-wrap';
		$insta_feed_wrap_classes[] = 'eae-insta-layout-' . $settings['insta_feed_layout'];
		$insta_feed_wrap_classes[] = 'eae-insta-caption-' . $settings['insta_caption_layout'];
		$insta_item_classes[] = 'eae-insta-post';
		
		if ( $settings['enable_insta_feed_image_ratio'] === 'yes' ) {
			$insta_feed_wrap_classes[] = 'eae_image_ratio_yes';
		}

		if($settings['insta_feed_layout'] === 'carousel'){
			$swiper_data = Swiper_helper::get_swiper_data($settings);

			$insta_feed_wrap_classes[] = 'eae-swiper-outer-wrapper';
			$insta_feed_wrap_classes[] = 'eae-carousel-yes';
			if ( $settings['arrows_layout'] === 'inside' ) {
				$insta_feed_wrap_classes[] = 'eae-hpos-' . $settings['arrow_horizontal_position'];
				$insta_feed_wrap_classes[] = 'eae-vpos-' . $settings['arrow_vertical_position'];
			}
			$this->add_render_attribute( 'insta_feed_wrap', 'data-swiper-settings', wp_json_encode( $swiper_data ) );

			// Collection Attributes
			$this->add_render_attribute( 'collection', 'class', 'eae-swiper-container swiper' );

			//Swiper List Wrapper Attributes
			$this->add_render_attribute( 'post-list-wrapper', 'class', 'eae-post-widget-wrapper eae-swiper-wrapper swiper-wrapper' );

			//
 			$insta_item_classes[] = 'eae-swiper-slide swiper-slide';

			if( $settings['auto_height'] !== 'yes'){
				$insta_feed_wrap_classes[] = 'eae-height-100';
			}
		}
		if( $settings['insta_feed_layout'] != 'masonry' ){
			$insta_feed_wrap_classes[] = 'eae-height-100';
		}
		$this->add_render_attribute( 'insta_feed_wrap', [
			'class' => $insta_feed_wrap_classes,
			'data-settings' => json_encode( $data_settings ),
		]);
		
		$this->add_render_attribute( 'collection', 'class', 'eae-post-collection' );
		if($settings['enable_lightbox'] === 'yes' ){
			$this->add_render_attribute( 'collection', 'class', 'lightbox' );
			$this->add_render_attribute( 'collection', 'data-lg-settings', json_encode(Lightgallery_helper::get_lightgallery_data($settings)) );
		}
		$this->add_render_attribute( 'collection', 'id', 'insta-grid-' . $this->get_id() );
		$this->add_render_attribute( 'insta_item', 'class', $insta_item_classes );
		?>
		<div <?php echo $this->get_render_attribute_string('insta_feed_wrap'); ?>>
			<?php if( $settings['insta_profile_position'] === 'above'): ?>
				<?php echo $this->get_profile_link_html($settings); ?>
			<?php endif; ?>
			<div <?php echo $this->get_render_attribute_string('collection'); ?>>
				<?php if($settings['insta_feed_layout'] === 'carousel'){ ?>
					<div <?php echo $this->get_render_attribute_string('post-list-wrapper'); ?>>
				<?php } 
				if( !is_wp_error( $insta_data ) && !empty( $insta_data) && count($insta_data) ){
					$post_count = 0;
					foreach( $insta_data['data'] as $insta_post ){
						if( $post_count == $settings['post_count'] ){
							break;
						}
						$insta_media_type = $insta_post['media_type'];
						$insta_media_url = '';
						/* IMAGE, VIDEO & CAROUSEL MEDIA */
						switch( $insta_media_type ){
							case 'IMAGE': if( $settings['insta_image'] === 'yes' ){
											$insta_media_url = $insta_post['media_url'];
										  }
										  break;
							case 'VIDEO': if( $settings['insta_video'] === 'yes' ){
											$insta_media_url = $insta_post['thumbnail_url'];
										  }
										  break;
							case 'CAROUSEL_ALBUM': if( $settings['insta_carousel'] === 'yes' ){
											$insta_media_url = $insta_post['media_url'];
										  }
										  break;
						}
						if($insta_media_url == ''){
							continue;
						}
						?>
						<div <?php echo $this->get_render_attribute_string( 'insta_item') ?>>
									<?php 
									$this->set_render_attribute( 'link', 'class', 'eae-insta-post-link');
									$link_html_tag = 'div';
									if($settings['enable_link'] === 'yes'){
										if($settings['enable_lightbox'] === 'yes' ){
											$this->get_link_attribute($insta_post);
											$link_html_tag = 'div';
										}else{
											$this->set_render_attribute( 'link', [
												'href' => $insta_post['permalink'],
												'target' => '_blank',
											]);
											$link_html_tag = 'a';
										}
									}
									?>
									<<?php echo $link_html_tag ?> <?php echo $this->get_render_attribute_string('link'); ?>>
										<?php 
										$this->set_render_attribute('insta_image', 'src', $insta_media_url);
										if($settings['insta_feed_image_grayscale'] == 'yes' ){
											$this->set_render_attribute('insta_image', 'class', 'insta_image_grayscale');
										}
										if($settings['insta_feed_image_grayscale_hover'] == 'yes' ){
											$this->set_render_attribute('insta_image', 'class', 'insta_image_grayscale_hover');
										}
										?>
										<img <?php echo $this->get_render_attribute_string('insta_image'); ?>>
										<?php //Insta Caption
											if($settings['insta_caption_overlay'] == 'yes'){
												$this->get_insta_caption_html($settings, $insta_post);
											}
										?>
										<?php if( $settings['enable_insta_feed_icon'] === 'yes' ){ 
											$insta_feed_icon = '';
										?>
											<?php if( $insta_post['media_type'] === 'CAROUSEL_ALBUM' ) {
													$insta_feed_icon = '<i class="fas fa-images"></i>';
												}else if( $insta_post['media_type'] === 'IMAGE' ){
													$insta_feed_icon = '<i class="fas fa-image"></i>';
												}else if( $insta_post['media_type'] === 'VIDEO' ){
													$insta_feed_icon = '<i class="fas fa-play"></i>';
												} 
											?>
											<?php if($insta_feed_icon != ''){ ?> 
												<span class="eae-insta-feed-icon"><?php echo $insta_feed_icon; ?></span>
											<?php } ?>	
										<?php } ?>
										<!-- CAROUSEL_ALBUM HTML -->
										<?php if($insta_post['media_type'] === 'CAROUSEL_ALBUM' && $settings['insta_carousel'] === 'yes'){ ?>
											<div class="eae-insta-carousel-children" style="display:none;" >
												<?php if(isset($insta_post['children']['data'])):
													foreach($insta_post['children']['data'] as $key => $children): ?>
													<?php if($key != 0): ?>
														<?php $insta_carousel_data = $this->check_eae_insta_cache_data_by_id($settings, $children['id']); 
														if( !is_wp_error( $insta_carousel_data ) && !empty( $insta_carousel_data) && count($insta_carousel_data) ):?>
															<?php if($insta_carousel_data['media_type'] === 'IMAGE'): ?>
																<?php
																$this->get_link_attribute($insta_post, $insta_carousel_data, true);
																$this->set_render_attribute('insta_image', 'src', $insta_carousel_data['media_url']);
																?>
																<div <?php echo $this->get_render_attribute_string('link'); ?>>
																	<img <?php echo $this->get_render_attribute_string('insta_image'); ?>>
																</div>
															<?php endif; ?>
															<?php if($insta_carousel_data['media_type'] === 'VIDEO'): ?>
																<?php
																$this->get_link_attribute($insta_post, $insta_carousel_data, true);
																$this->set_render_attribute('insta_image', 'src', $insta_carousel_data['thumbnail_url']);
																?>
																<div <?php echo $this->get_render_attribute_string('link'); ?>>
																	<img <?php echo $this->get_render_attribute_string('insta_image'); ?>>
																</div>
															<?php endif; ?>
														<?php endif; ?>
													<?php endif; ?>
												<?php endforeach;
												endif; ?>
											</div>
										<?php } ?>
										
									</<?php echo $link_html_tag ?>>
									<?php //Insta Caption
										if($settings['insta_caption_overlay'] != 'yes'){
											$this->get_insta_caption_html($settings, $insta_post);
										}
									?>
						</div>
						<?php
						$post_count = $post_count + 1;
					}
				}
				?>
				<?php if($settings['insta_feed_layout'] === 'carousel'){ ?>
					</div>
				<?php 
				Swiper_helper::get_swiper_pagination($settings);
				}
				/** Arrows Inside **/
				if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
					Swiper_helper::get_swiper_arrows($settings);
				}

				Swiper_helper::get_swiper_scrolbar($settings);
				?>
			</div>
			<?php if($settings['insta_feed_layout'] === 'masonry'){ ?>
				<div class="grid-gap"></div>
			<?php } ?>
			<?php 
			if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
				/** Arrows Outside **/
				Swiper_Helper::get_swiper_arrows($settings);
			}
			?>
			<?php if( $settings['insta_profile_position'] === 'below'): ?>
				<?php echo $this->get_profile_link_html($settings); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	public function get_insta_caption_html($settings, $insta_post){
		if(trim($insta_post['caption']) !== ""){
			if ($settings['insta_caption'] === 'yes'): 
				$this->set_render_attribute( 'insta_post_caption', 'class', 'eae-insta-post-caption' );
			?>
				<?php if( isset($settings['caption_overlay_animation']) && $settings['caption_overlay_animation'] !== 'none' && $settings['caption_overlay_animation'] != "" ){
						$this->add_render_attribute( 'insta_post_caption', 'class', 'animated ' . esc_html( $settings['caption_overlay_animation'] ) . ' animated-' . esc_html( $settings['caption_overlay_animation_time']) );
				}
				if( $settings['insta_caption_overlay'] === 'yes' ){
					$this->add_render_attribute( 'insta_post_caption', 'class', 'caption-overlay-full' );
				} ?>
				<div <?php echo $this->get_render_attribute_string('insta_post_caption'); ?>>
						<?php echo wp_html_excerpt( $insta_post['caption'], $settings['insta_caption_size'], '...' ); ?>
				</div>
			<?php endif; 
		}
	}

	public function get_link_attribute($insta_post, $insta_carousel_data = [], $is_carousel_media = false){
		$caption = $insta_post['caption'];
		if($is_carousel_media){
			$insta_post = $insta_carousel_data;
		}

		$this->set_render_attribute( 'link', [
			'title' => $caption,
			'data-facebook-share-url' => $insta_post['permalink'],
			'data-twitter-share-url' => $insta_post['permalink'],
			'data-pinterest-share-url' => $insta_post['permalink'],
			'data-tweet-text' => $this->convertAll($caption),
			'data-pinterest-text' => $this->convertAll($caption),
			'data-src' => $insta_post['media_url'],
			/* 'data-sub-html' => $caption, */
			'data-download-url' => '',
			'data-video' => '',
		] );

		if($insta_post['media_type'] === 'VIDEO'){
			$this->set_render_attribute( 'link', [
				'title' => $caption,
				'data-facebook-share-url' => $insta_post['permalink'],
				'data-twitter-share-url' => $insta_post['permalink'],
				'data-pinterest-share-url' => $insta_post['permalink'],
				'data-tweet-text' => $this->convertAll($caption),
				'data-pinterest-text' => $this->convertAll($caption),
				'data-src' => '',
				'data-video' => '{"source": [{"src":"' . $insta_post['media_url'] .'", "type":"video/mp4"}], "attributes": {"preload": false, "controls": true}}',
				'data-download-url' => 'false',
			]);
		}
	}

	public function convertAll($str) {
		$regex = "/[@#](\w+)/";
		//type and links
		$hrefs = [
			'#' => '%23',
			'@' => '%40'
		];

		$result = preg_replace_callback($regex, function($matches) use ($hrefs) {
			return sprintf(
				'%s%s',
				$hrefs[$matches[0][0]],
				$matches[1], 
				$matches[0]
			);
		}, $str);

		return($result);
	}

	public function get_profile_link_html($settings){
		if( $settings['insta_profile_link'] === 'yes'){ ?>
		<div class="eae-insta-profile-link-wrap">
			<div class="eae-insta-profile">
				<?php
				$this->add_link_attributes( 'insta_profile_link', $settings['insta_profile_link_url'] );
				?>
				<a <?php echo $this->get_render_attribute_string('insta_profile_link'); ?>>
					<span class="eae-insta-profile-link">
							<?php
							if ( $settings['insta_profile_icon_position'] === 'before' ) {
								$this->get_profile_link_icon($settings);
							}

							echo Helper::eae_wp_kses( $settings['insta_profile_link_text'] );

							if ( $settings['insta_profile_icon_position'] === 'after' ) {
								$this->get_profile_link_icon($settings);
							}
							?>
						</span>
					</a>
			</div>
		</div>
		<?php
		}
	}

	public function get_profile_link_icon($settings){
		$this->add_render_attribute( 'insta_profile_link_icon', 'class', 'eae-insta-profile-link-icon' );
		$this->add_render_attribute( 'insta_profile_link_icon', 'class', 'eae-insta-icon-' . $settings['insta_profile_icon_position'] );
		?>
		<span <?php echo $this->get_render_attribute_string('insta_profile_link_icon'); ?>>
			<?php
				Icons_Manager::render_icon( $settings['insta_profile_link_icon'], array( 'aria-hidden' => 'true' ) );
			?>
			</span>
		<?php
	}

	public function get_insta_images_style_controls() {
		$this->start_controls_section(
			'section_image_styles',
			array(
				'label' => __( 'Images', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'insta_feed_image_width',
			[
				'label'     => __( 'Width', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 250,
					],
				'range' => [
					'px' => [
					'min' => 0,
					'max' => 2000,
					]
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => array(
					'{{WRAPPER}} .eae-insta-post' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => [
					'insta_feed_layout' => 'flex'
				]
			]
		);

		$this->start_controls_tabs( 'insta_feed_image_style_tabs' );

		$this->start_controls_tab(
			'insta_feed_image_tab_normal',
			array(
				'label' => __( 'Normal', 'wts-eae' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'insta_feed_image_bg_color',
				'label'     => __( 'Background', 'ae-pro' ),
				'types'     => [ 'none', 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .eae-insta-post',
			]
		);

		$this->add_control(
			'insta_feed_image_grayscale',
			array(
				'label'        => __( 'Grayscale Image', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'insta_feed_image_border',
				'label'       => __( 'Border', 'wts-eae' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .eae-insta-post',
			)
		);

		$this->add_control(
			'insta_feed_image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eae-insta-post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'insta_feed_image_box_shadow',
				'label'     => __( 'Box Shadow', 'wts-eae' ),
				'selector'  => '{{WRAPPER}} .eae-insta-post',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'insta_feed_image_tab_hover',
			array(
				'label' => __( 'Hover', 'wts-eae' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'insta_feed_image_bg_color_hover',
				'label'     => __( 'Background', 'ae-pro' ),
				'types'     => [ 'none', 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .eae-insta-post:hover',
			]
		);

		$this->add_control(
			'insta_feed_image_grayscale_hover',
			array(
				'label'        => __( 'Grayscale Image', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'insta_feed_image_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .eae-insta-post:hover' => 'border-color: {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'insta_feed_image_box_shadow_hover',
				'label'     => __( 'Box Shadow', 'wts-eae' ),
				'selector'  => '{{WRAPPER}} .eae-insta-post:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'insta_feed_image_padding',
			array(
				'label'              => __( 'Padding', 'wts-eae' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .eae-insta-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'eae_insta_feed_icon_heading',
			[
				'label'     => __( 'Post Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' 		 => 'before',
				'condition' => [
					'insta_carousel' => 'yes',
					'enable_album_icon' => 'yes'
				]
			]
		);

		$this->add_control(
			'insta_feed_icon_color',
			array(
				'label'     => __( 'Icon Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .eae-insta-feed-icon' => 'color: {{VALUE}};',
				),
				'condition' => [
					'enable_insta_feed_icon' => 'yes'
				]
			)
		);


		$this->end_controls_section();
	}

	public function get_insta_caption_style_controls() {
		$this->start_controls_section(
			'section_caption_styles',
			array(
				'label'     => __( 'Caption', 'wts-eae' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'insta_caption' => 'yes',
				],
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'caption_overlay_color',
				'label'     => __( 'Color', 'ae-pro' ),
				'types'     => [ 'none', 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .eae-insta-post-caption',
				/* 'condition' => [
					'insta_caption' => 'yes',
					'insta_caption_layout' => ['hover', 'always']
				], */
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'insta_feed_caption_typography',
				'label'     => __( 'Typography', 'wts-eae' ),
				'selector'  => '{{WRAPPER}} .eae-insta-post-caption',
			)
		);

		$this->add_control(
			'insta_feed_caption_color',
			array(
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .eae-insta-post-caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'insta_feed_caption_vertical_align',
			array(
				'label'                => __( 'Vertical Align', 'wts-eae' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'center',
				'options'              => array(
					'flex-start'    => array(
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors'            => array(
					'{{WRAPPER}} .eae-insta-post-caption' => 'justify-content: {{VALUE}};',
				),
				'condition' => [
					'insta_caption' => 'yes',
					'insta_caption_layout' => ['hover', 'always'],
					'insta_caption_overlay' => 'yes',
				]
			)
		);

		$this->add_control(
			'insta_feed_caption_horizontal_align',
			array(
				'label'                => __( 'Horizontal Align', 'wts-eae' ),
				'type'                 => Controls_Manager::CHOOSE,
				'label_block'          => false,
				'toggle'               => false,
				'default'              => 'center',
				'options'              => array(
					'flex-start'   => array(
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'  => array(
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors'            => array(
					'{{WRAPPER}} .eae-insta-post-caption' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'insta_feed_caption_padding',
			array(
				'label'              => __( 'Padding', 'wts-eae' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} .eae-insta-post-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'          => 'after',
			)
		);

		$this->get_overlay_caption_style_controls();

		$this->end_controls_section();
	}

	public function get_overlay_caption_style_controls(){
		$this->add_control(
			'caption_overlay',
			[
				'label'     => __( 'Overlay', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'insta_caption' => 'yes',
					'insta_caption_layout' => ['hover', 'always']
				],
			]
		);

		$this->add_responsive_control(
			'caption_overlay_animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'wts-eae' ),
				'type' => Controls_Manager::ANIMATION,
				'render_type' => 'template',
				'condition' => [
					'insta_caption' => 'yes',
					'insta_caption_layout' => ['hover', 'always']
				],
			]
		);

		$this->add_control(
			'caption_overlay_animation_time',
			[
				'label' => esc_html__( 'Animation Duration', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'slow' => esc_html__( 'Slow', 'wts-eae' ),
					'' => esc_html__( 'Normal', 'wts-eae' ),
					'fast' => esc_html__( 'Fast', 'wts-eae' ),
				],
				'condition' => [
					'insta_caption' => 'yes',
					'insta_caption_layout' => ['hover', 'always'],
					'caption_overlay_animation!' => '',
				],
			]
		);
	}

	public function get_insta_profile_link_style_controls(){
		$this->start_controls_section(
			'section_profile_link_styles',
			array(
				'label'     => __( 'Profile Link', 'wts-eae' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'insta_profile_link' => 'yes',
				],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'eae_insta_profile_link_typography',
				'label'     => __( 'Typography', 'wts-eae' ),
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
				'selector'  => '{{WRAPPER}} .eae-insta-profile-link',
			]
		);

		$this->start_controls_tabs( 'eae_insta_profile_link_style_tabs' );

		$this->start_controls_tab(
			'eae_insta_profile_link_normal_tab',
			[
				'label'     => __( 'Normal', 'wts-eae' ),
			]
		);

		$this->add_control(
			'eae_insta_profile_link_color',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .eae-insta-profile a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eae-insta-profile .eae-insta-profile-link-icon svg' => 'fill: {{VALUE}};',
				),
			]
		);

		$this->add_control(
			'eae_insta_profile_link_bg_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .eae-insta-profile' => 'background: {{VALUE}};',
				),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'eae_insta_profile_link_border',
				'label'       => __( 'Border', 'wts-eae' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .eae-insta-profile',
			]
		);

		$this->add_control(
			'eae_insta_profile_link_border_radius',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .eae-insta-profile' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'eae_insta_profile_link_hover_tab',
			[
				'label'     => __( 'Hover', 'wts-eae' ),
			]
		);

		$this->add_control(
			'eae_insta_profile_link_color_hover',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-insta-profile a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eae-insta-profile a:hover .eae-insta-profile-link-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eae_insta_profile_link_bg_color_hover',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-insta-profile:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eae_insta_profile_link_border_color_hover',
			[
				'label'     => __( 'Border Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eae-insta-profile:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eae_insta_profile_link_border_radius_hover',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-insta-profile:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'eae_insta_profile_link_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-insta-profile' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eae_insta_profile_link_margin',
			[
				'label'      => __( 'Margin', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-insta-profile' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eae_insta_profile_link_icon_heading',
			[
				'label'     => __( 'Icon', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'eae_insta_profile_link_icon_spacing',
			[
				'label'              => __( 'Spacing', 'wts-eae' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => ['size' => 5],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'size_units'         => ['px'],
				'selectors'          => [
					'{{WRAPPER}} .eae-insta-profile .eae-insta-icon-before' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eae-insta-profile .eae-insta-icon-after' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'separator'          => 'after',
			]
		);

		$this->end_controls_section();
	}

	public function get_carousel_style_controls(){
		$this->start_controls_section(
			'section_carousel_styles',
			array(
				'label'     => __( 'Carousel', 'wts-eae' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'insta_feed_layout' => 'carousel',
				],
			)
		);
		Swiper_helper::carousel_style_section($this);
		$this->end_controls_section();
	}

}