<?php

namespace WTS_EAE\Pro\Modules\ThumbGallery;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {
    
	public function __construct(){
        //add_filter('eae_stack_source_options', [$this, 'stack_source_options']);
         add_filter('eae_thumbnail_slider/add_pro_controls', [$this, 'add_pro_controls']);
        // add_filter('eae_image_stack_data', [$this, 'image_stack_data'], 10, 2);
        // add_action('eae_image_stack_render_pro', [$this, 'image_stack_render_pro'], 10, 3);
    }

    public function get_name()
    {
        return 'eae-thumbgallery-pro';
    }

    public function add_pro_controls($widget){

        $widget->start_injection(
            [
                'of' => 'thumb_container_width',
            ]
        );

        $widget->add_control(
            'thumb_inside_hpos',
            [
                'label' => __('Horizontal Position', 'eae-pro'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'eae-pro'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'eae-pro'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'eae-pro'),
                        'icon' => 'eicon-h-align-right',
                    ]
                ],
                'default' => 'center',
                'prefix_class'         => 'eae-thumb-inside-hpos-',
                'condition' => [
                    'thumb_horizontal_align' => 'inside'
                ]

            ]
        );

        $widget->add_control(
			'thumb_inside_vpos',
			[
				'label'                => __( 'Vertical Position', 'wts-eae' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'top' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
                'default' => 'top',
                'prefix_class'         => 'eae-thumb-inside-vpos-',
				'condition' => [
                    'thumb_horizontal_align' => 'inside'
                ]
			]
		);

        $widget->add_responsive_control(
			'horizontal_thumb_offset',
			[
				'label'          => __( 'Horizontal Offset', 'wts-eae' ),
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
                    // .eae-thumb-horizontal-inside.eae-thumb-container
					'{{WRAPPER}}.eae-thumb-inside-hpos-left .eae-gallery-thumbs.eae-thumb-horizontal-inside.eae-thumb-container' => 'left: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}}.eae-thumb-inside-hpos-right .eae-gallery-thumbs.eae-thumb-horizontal-inside.eae-thumb-container' => 'right: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}}.eae-thumb-inside-hpos-center .eae-gallery-thumbs.eae-thumb-horizontal-inside.eae-thumb-container' => 'left: {{SIZE}}{{UNIT}} !important',
				],
				'condition' => [
                    'thumb_horizontal_align' => 'inside',
                    'thumb_inside_hpos!' => 'center' 
                ]
			]
		);

		$widget->add_responsive_control(
			'vertical_thumb_offset',
			[
				'label'          => __( 'Vertical Offset', 'wts-eae' ),
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
					'{{WRAPPER}}.eae-thumb-inside-vpos-top .eae-gallery-thumbs.eae-thumb-horizontal-inside.eae-thumb-container' => 'top: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}}.eae-thumb-inside-vpos-bottom .eae-gallery-thumbs.eae-thumb-horizontal-inside.eae-thumb-container' => 'bottom: {{SIZE}}{{UNIT}} !important',
					'{{WRAPPER}}.eae-thumb-inside-vpos-middle .eae-gallery-thumbs.eae-thumb-horizontal-inside.eae-thumb-container' => 'top: {{SIZE}}{{UNIT}} !important', 
				],
				'condition' => [
                    'thumb_horizontal_align' => 'inside',
                    'thumb_inside_vpos!' => 'middle'
                ]
			]
		);

        $widget->end_injection();
        return $widget;
    }
}
