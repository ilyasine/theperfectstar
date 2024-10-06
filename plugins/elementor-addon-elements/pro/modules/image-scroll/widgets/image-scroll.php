<?php

namespace WTS_EAE\Pro\Modules\ImageScroll\Widgets;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ImageScroll extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-image-scroll';
	}

	public function get_title() {
		return __( 'Image Scroll', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-image-scroll';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'Image Scroll'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie', 'imagesloaded' ];
	}

    protected function register_controls(){

        $this->start_controls_section(
			'eae_content_section',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);


        $this->add_control(
			'eae_image_scroll',
			[
				'label' => esc_html__( 'Choose Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'eae_image_scroll_size', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'separator' => 'none',
			]
		);

        $this->add_control(
			'link_type',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __('None', 'wts-eae'),
                    'media' => __('Media File', 'wts-eae'),
					'custom' => __('Custom URL', 'wts-eae'),
				],
				'default' => 'none',
			]
		);

        $this->add_control(
			'custom_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
				'condition' => [
					'link_type' => 'custom',
				],
				'show_label' => false,
			]
		);

        $this->add_control(
			'enable_lightbox',
			[
				'label' => esc_html__( 'Lightbox', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'wts-eae' ),
					'yes' => esc_html__( 'Yes', 'wts-eae' ),
					'no' => esc_html__( 'No', 'wts-eae' ),
				],
				'condition' => [
					'link_type' => 'media',
				],
			]
		);


        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'eae_image_scroll_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> false,
                'lottie'		=> true,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'eae_settings_section',
			[
				'label' => __( 'Settings', 'wts-eae' ),
			]
		);

        $this->add_responsive_control(
            'eae_image_scroll_img_height',
            [
                'label' => esc_html__( 'Image Height', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'default' => [
                    'size' => 320,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}}  .wts-eae-image-scroll' => 'height: {{SIZE}}{{UNIT}};',
                ],
                
            ]
        );
        $this->add_control(
			'eae_image_scroll_direction',
			[
				'label' => esc_html__( 'Direction', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
				],
				'default' => 'vertical',
			]
		);

        $this->add_control(
            'eae_image_scroll_speed',
            [
                'label' => esc_html__( 'Speed' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default'     => 3,
				'min'         => 1,
				'max'         => 10,
                'selectors'=>[
                    '{{WRAPPER}} .wts-eae-image-scroll img'   => 'transition-duration: {{value}}s',
                ],
            ]
        );
      

        $this->add_control(
			'eae_image_scroll_trigger',
			[
				'label' => esc_html__( 'Trigger', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'hover' => 'Hover',
					'scroll' => 'Scroll',
				],
				'default' => 'hover',
			]
		);

		$this->add_control(
			'eae_image_reverse',
			[
				'label' => esc_html__( 'Reverse', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'eae_image_scroll_trigger' => 'hover',
				],
			]
		);

        $this->add_control(
			'eae_image_enable_overlay',
			[
				'label' => esc_html__( 'Enable Overlay', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'eae_icon_style_section',
            [
                'label' => esc_html__( 'Icon', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		

        Helper::global_icon_style_controls($this,[
			'name' => 'eae_image_icon',
			'selector' => '.eae_image_scroll_icon',
            'show_hover_controls' => false
	    ]);

        $this->add_control(
            'eae_image_heading_overlay',
            [
                'label'     => __( 'Overlay', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'eae_image_enable_overlay'=>'yes',
                ]
            ]
        );
        

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'eae_image_overlay_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude'=>['image'],
                'selector' => '{{WRAPPER}}  .eae-icon-wrapper[data-overlay="enable"]',
                'condition'=>[
                    'eae_image_enable_overlay'=>'yes',
                ]
            ]
        );
		$this->add_control(
			'eae_image_mask_control_heading',
			[
				'label' => esc_html__( 'Mask', 'wts-eae' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'	=> [
					'eae_image_mask_video_shape' => 'yes'
				]
			]
		);

	
		$this->add_control(
			'eae_image_mask_background_position',
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
					'{{WRAPPER}} .wts-eae-image-scroll' => '-webkit-mask-position: {{VALUE}};',
				],
				'condition'	=> [
					'eae_image_mask_video_shape' => 'yes'
				]
			]
		);

	
		$this->add_control(
			'eae_image_mask_background_size',
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
					'{{WRAPPER}} .wts-eae-image-scroll' => '-webkit-mask-size: {{VALUE}};',
				],
				'condition'	=> [
					'eae_image_mask_video_shape' => 'yes'
				]
			]
		);

		
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
					'{{WRAPPER}} .wts-eae-image-scroll' => '-webkit-mask-repeat : {{VALUE}};',
				],
				'condition'	=> [
					'eae_image_mask_video_shape' => 'yes'
				]
			]
		);
        $this->end_controls_section();

        $this->start_controls_section(
            'eae_image_style_general',
            [
                'label' => esc_html__( 'General', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .wts-eae-image-scroll',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .wts-eae-image-scroll' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .wts-eae-image-scroll, {{WRAPPER}} .eae-image-scroll-overlay ',
			]
		);
        $this->end_controls_section();



    }

    public function render(){
        $settings = $this->get_settings_for_display();


        $data = [];
		$data['direction'] = $settings['eae_image_scroll_direction'];
		$data['trigger'] = $settings['eae_image_scroll_trigger'];
		$data['reverse'] = $settings['eae_image_reverse'];

    	$direction = $settings['eae_image_scroll_direction'];
    
		$overlay ='';
		if($settings['eae_image_enable_overlay']=='yes'){
		$overlay ='enable';
		}
       
    	$link = $this->get_link_url($settings);
	   
		if($link){
			$this->add_link_attributes( 'link', $link);

			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$this->add_render_attribute( 'link', [
					'class' => 'elementor-clickable',
				] );
			}

			if ( 'custom' !== $settings['link_type'] ) {
				$this->add_lightbox_data_attributes( 'link', $settings['eae_image_scroll']['id'], $settings['enable_lightbox'] );
			}
		}

		
		
		$this->add_render_attribute('wrapper', [    
            'class' => 'wts-eae-image-scroll',
            'data-settings' => json_encode($data),
        ]);

		$this->add_render_attribute('icon-wrapper', [
			'class' => 'eae-floating-icon-wrapper',
			'data-overlay' => $overlay
		]);
		
        $imgUrl = $settings['eae_image_scroll']['url'];    
		
		$image_html = wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'eae_image_scroll_size', 'eae_image_scroll' ) );
		
		
        ?>
		
        <div <?php echo  $this->get_render_attribute_string( 'wrapper' ) ?>>
            
			<?php 
			if($link){
				?>
				<a <?php echo $this->get_render_attribute_string('link'); ?>>
				<?php
			}
			?>

			<?php 

			if($image_html){
				?>
				<div class="image-scroll-wrapper <?php echo esc_attr($direction) ?>">
					<?php 
						echo $image_html;
					?>
				</div>
				<?php
			}
            ?>

            <?php 
				if($link){
					?>
					</a>
					<?php
				}
			?>
        </div>

		<?php
			if($settings['eae_image_enable_overlay']=='yes' || $settings['eae_image_scroll_icon_graphic_type']!='none'){
				?>
				<div <?php echo $this->get_render_attribute_string('icon-wrapper'); ?>>
					<?php Helper::render_icon_html($settings,$this,'eae_image_scroll_icon','eae_image_scroll_icon'); ?>
				</div>
				<?php
			}        
    }

    protected function get_link_url( $settings ) {
		if ( 'none' === $settings['link_type'] ) {
			return false;
		}

		if ( 'custom' === $settings['link_type'] ) {
			if ( empty( $settings['custom_link']['url'] ) ) {
				return false;
			}

			return $settings['link'];
		}

		return [
			'url' => $settings['eae_image_scroll']['url'],
		];
	}
}
