<?php

namespace RT\ThePostGridPro\Controllers\Blocks\BlockController;

class SliderSettingsAndStyle {

	/**
	 * @return mixed|void
	 */
	public static function get_controller( $prefix = '' ) {

		$settings_attribute = [
			'slider_column' => [
				'type'    => 'object',
				'default' => [
					"lg" => 0,
					"md" => 0,
					"sm" => 0,
				],
			],

			'slider_gap' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
					'strictZero'=>true,
				],
				'style'   => [
					(object) [
						'selector' => 'body {{RTTPG}} .tpg-el-main-wrapper .rt-slider-item {padding-left: {{slider_gap}}px; padding-right: {{slider_gap}}px }
						body {{RTTPG}} .tpg-el-main-wrapper .rt-swiper-holder {margin-left: calc(-{{slider_gap}}px - 5px); margin-right: -{{slider_gap}}px }
						{{RTTPG}} .rt-tpg-container .slider-column.swiper-slide .rt-slider-item {padding-top: {{slider_gap}}px; padding-bottom: {{slider_gap}}px }'
					]
				]
			],

			'arrows' => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'arrow_position' => [
				'type'    => 'string',
				'default' => 'default',
			],

			'dots' => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'dynamic_dots' => [
				'type'    => 'string',
				'default' => '',
			],

			'slider_per_group' => [
				'type'    => 'string',
				'default' => '',
			],

			'dots_style' => [
				'type'    => 'string',
				'default' => 'default',
			],

			'infinite' => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'autoplay'      => [
				'type'    => 'string',
				'default' => '',
			],
			'autoplaySpeed' => [
				'type'    => 'integer',
				'default' => 3000,
			],
			'stopOnHover'   => [
				'type'    => 'string',
				'default' => 'yes',
			],
			'grabCursor'    => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'autoHeight' => [
				'type'    => 'string',
				'default' => '',
			],

			'lazyLoad' => [
				'type'    => 'string',
				'default' => '',
			],

			'scroll_visibility' => [
				'type'    => 'string',
				'default' => 'yes',
			],

			'speed' => [
				'type'    => 'integer',
				'default' => 500,
			],

			'carousel_overflow' => [
				'type'    => 'string',
				'default' => 'hidden',
			],
			'slider_direction'  => [
				'type'    => 'string',
				'default' => 'ltr',
			],

			//Slider Style


			'arrow_font_size' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {font-size: {{arrow_font_size}}; }'
					]
				]
			],

			'slider_content_width' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .slider-layout12 .rt-grid-hover-item .rt-holder .grid-hover-content .slider-content-inner,
						{{RTTPG}} .tpg-el-main-wrapper.slider-layout11-main .slider-content-inner
						 {max-width: {{slider_content_width}}; }'
					]
				]
			],

			'arrow_border_radius' => [
				'type'    => 'integer',
				"default" => 100,
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {border-radius: {{arrow_border_radius}}px; }'
					]
				]
			],

			'arrow_width' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {width: {{arrow_width}}; }'
					]
				]
			],

			'arrow_height' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {height: {{arrow_height}}; }'
					]
				]
			],

			'arrow_x_position' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn.swiper-button-prev {left: {{arrow_x_position}}; }
						{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn.swiper-button-next {right: {{arrow_x_position}}; }
						{{RTTPG}}.slider-arrow-position-top-right .swiper-navigation {right: {{arrow_x_position}}; }
						{{RTTPG}}.slider-arrow-position-top-left .swiper-navigation {left: {{arrow_x_position}}; }'
					]
				]
			],

			'arrow_y_position' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '
						{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn, 
						{{RTTPG}}.slider-arrow-position-top-right .swiper-navigation, 
						{{RTTPG}}.slider-arrow-position-top-left .swiper-navigation {top: {{arrow_y_position}}; }'
					]
				]
			],

			//Arrow Tab Start

			'arrow_style_tabs' => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'arrow_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {color: {{arrow_color}}; }'
					]
				]
			],

			'arrow_arrow_bg_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn {background-color: {{arrow_arrow_bg_color}}; }'
					]
				]
			],

			'arrow_box_shadow' => [
				'type'    => 'object',
				'default' => (object) [
					'openShadow' => 1,
					'width'      => (object) [
						'top'    => 0,
						'right'  => 0,
						'bottom' => 0,
						'left'   => 0
					],
					'color'      => '',
					'inset'      => false,
					'transition' => 0.5
				],
				'style'   => [
					(object) [ 'selector' => 'body {{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn' ]
				],
			],

			'slider_btn_border' => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '#D4D4D4',
					'style'         => 'solid',
					'width'         => '1px',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn'
					]
				]
			],

			//Hover

			'arrow_hover_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn:hover {color: {{arrow_hover_color}}; }'
					]
				]
			],

			'arrow_bg_hover_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn:hover {background-color: {{arrow_bg_hover_color}}; }'
					]
				]
			],

			'arrow_box_shadow_hover' => [
				'type'    => 'object',
				'default' => (object) [
					'openShadow' => 1,
					'width'      => (object) [
						'top'    => 0,
						'right'  => 0,
						'bottom' => 0,
						'left'   => 0
					],
					'color'      => '',
					'inset'      => false,
					'transition' => 0.5
				],
				'style'   => [
					(object) [ 'selector' => 'body {{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn:hover' ]
				],
			],

			'slider_btn_border_hover' => [
				'type'    => 'object',
				'default' => (object) [
					'openTpgBorder' => 1,
					'color'         => '#D4D4D4',
					'style'         => 'solid',
					'width'         => '1px',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-navigation .slider-btn:hover'
					]
				]
			],

			'dots_text_align' => [
				'type'    => 'string',
				'default' => '',
			],

			'dot_wrapper_radius' => [
				'type'    => 'integer',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}}.slider-dots-style-background .tpg-el-main-wrapper .swiper-pagination {border-radius: {{dot_wrapper_radius}}px; }'
					]
				]
			],

			'dots_border_radius' => [
				'type'    => 'integer',
				"default" => 100,
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet {border-radius: {{dots_border_radius}}px; }'
					]
				]
			],

			'dots_width' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => ' {{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet, 
						{{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active {width: {{dots_width}}; }'
					]
				]
			],

			'dots_height' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet {height: {{dots_height}}; }'
					]
				]
			],

			'dots_margin' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet {margin-left: {{dots_margin}}; }'
					]
				]
			],

			'dots_position' => [
				'type'    => 'object',
				"default" => (object) [
					'lg' => '',
					'md' => '',
					'sm' => '',
				],
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-pagination {bottom: {{dots_position}}; }'
					]
				]
			],

			//Dots Tab Start

			'dots_style_tabs' => [
				'type'    => 'string',
				'default' => 'normal',
			],

			'dots_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet {background-color: {{dots_color}}; }'
					]
				]
			],

			'dots_border_color' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active {background-color: {{dots_border_color}}; }'
					]
				]
			],

			'dots_wrap_bg' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}}.slider-dots-style-background .tpg-el-main-wrapper .swiper-pagination {background-color: {{dots_wrap_bg}}; }'
					]
				]
			],

			//Hover

			'dots_color_hover' => [
				'type'    => 'string',
				'default' => '',
				'style'   => [
					(object) [
						'selector' => '{{RTTPG}} .rt-tpg-container .swiper-pagination .swiper-pagination-bullet:hover {background-color: {{dots_color_hover}}; }'
					]
				]
			],

		];

		return apply_filters( 'rttpg_guten_settings_attribute', $settings_attribute );
	}
}