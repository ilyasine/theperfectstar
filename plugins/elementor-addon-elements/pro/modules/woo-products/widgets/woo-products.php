<?php

namespace WTS_EAE\Pro\Modules\WooProducts\Widgets;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use WC_Product_Query;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use WTS_EAE\Classes\Swiper_helper;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Box_Shadow;
use WTS_EAE\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WooProducts extends EAE_Widget_Base {

    

	public function get_name() {
		return 'eae-woo-products';
	}

	public function get_title() {
		return __( 'Woo Products', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-woo-products';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return ['Woo Products'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie', 'zoom', 'flexslider', 'photoswipe-ui-default', 'wc-single-product' ];
	}

    public function get_style_depends()
    {
        return ['photoswipe-default-skin'];
    }

    protected function register_controls(){
        
        $this->start_controls_section(
			'eae-woo-products',
			[
				'label' => __( 'Layout', 'wts-eae' ),
			]
		);

        $this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'grid' => 'Grid',
					'slider' => 'Slider',
				],
				'default' => 'grid',
			]
		);
        
        $this->add_control(
			'product_layout',
			[
				'label' => esc_html__( 'Product Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'split' => 'Split',
					'cover' => 'Cover',
				],
				'default' => 'split',
			]
		);

        $this->add_responsive_control(
            'sp_layout',
            [
                'label' => esc_html__('Split Layout','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'up' => [
                        'title' => esc_html__('Up','wts-eae'),
                        'icon' => 'eicon-arrow-up',
                    ],
                    
                    'down' =>[
                        'title' => esc_html__('Down','wts-eae'),
                        'icon' => 'eicon-arrow-down',
                    ] , 
                    'right' =>[
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-arrow-right',
                    ]
                ],
                'default' => 'up',
                'prefix_class' => 'eae-woo-split-',
                'toggle' => false,
                'selectors_dictionary' => [
                    'left' => 'row',
                    'up' => 'column',
                    'right' => 'row-reverse',
                    'down' => 'column-reverse',
                ],
                'condition' => [
					'product_layout' => 'split',
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-product-card' => 'flex-direction: {{VALUE}}',
                    '{{WRAPPER}} .eae-woo-products .eae-product-card .eae-wp-content-wrapper' => 'flex-direction: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
			'pre_layout',
			[
				'label' => esc_html__( 'Preset Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'pre1' => 'Preset 1',
					'pre2' => 'Preset 2',
				],
				'default' => 'pre1',
			]
		);

        $this->add_control( 
			'hide_img',
			[
				'label'        => __( 'Hide Image', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'condition' => [
                    'product_layout' => 'split'
                ]
			]
		);

        $this->add_control( 
			'hide_rat',
			[
				'label'        => __( 'Hide Rating', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
			]
		);

        $this->add_control( 
			'hide_title',
			[
				'label'        => __( 'Hide Title', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
            ]
		);

        $this->add_control( 
			'hide_price',
			[
				'label'        => __( 'Hide Price', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
			]
		);

        $this->add_control( 
			'hide_des',
			[
				'label'        => __( 'Hide Description', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'default'      => 'yes',
			]
		);

        $this->add_control(
            'word_limit',
            [
                'label' => esc_html__( 'Word Limit' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default' => 15,
                'min' => 1,
                'step' => 1,
                'condition'=>[
                    'hide_des!'=>'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'eae-woo-query',
			[
				'label' => __( 'Product Query', 'wts-eae' ),
			]
		);
        $category = [
            '' => __('Select', 'wts-eae'),
        ];
        if(!empty($this->get_category_name())){
            $category = array_merge($category, $this->get_category_name());
        }

        
        $tags = [
            '' => __('Select', 'wts-eae'),
        ];
        if(!empty($this->get_tags_name())){
            $tags = array_merge($tags, $this->get_tags_name());
        }        
         
        $this->add_control(
			'filter_by',
			[
				'label' => esc_html__( 'Filter By', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
                    'recent'        => __('Recent Products', 'wts-eae'),
                    'featured'      => __('Featured Product','wts-eae'),
                    'best-selling'  => __('Best Selling Product','wts-eae'),
                    'sale'          => __('Sale Products', 'wts-eae'),
                    'top-rated'     => __('Top Rated Products', 'wts-eaee'),
                    'manual'        => __('Manual Selection','wts-eae'),
				],
                'default' => 'recent',
			]
		);

        $this->add_control(
			'inc_products',
			[
				'label'			 => esc_html__( 'Include Products', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
                'condition' => [
					'filter_by' => 'manual'
				],
			]
		);

        $this->add_control(
			'exc_products',
			[
				'label'			 => esc_html__( 'Exclude Products', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
                'description' => 'Exclude specific product by giving their product ID. To exclude multiple products, simply separate the IDs with commas, for example, 3322,4434.'
			]
		);

        $this->add_control( 
			'exclude_out_of_stock',
			[
				'label'        => __( 'Exclude Out of Stock', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
			]
		);

        $this->add_control(
			'order_by',
			[
				'label' => esc_html__( 'Order By', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => [
                    'ID'          => __('Product Id','wts-eae'),
                    'title'       => __('Product Title', 'wts-eae'),
                    'price'       => __('Price','wts-eae'),
                    'sku'         => __('SKU','wts-eae'),
                    'date'        => __('Date','wts-eae'),
                    'modified'    => __('Last Modified Date','wts-eae'),
                    'parent'      => __('Parent ID', 'wts-eae'),
                    'rand'        => __('Random','wts-eae'),
                    'menu_order'  => __('Menu Order','wts-eae'),
                ],
                'default' => 'ID',
				'condition' => [
					'filter_by!' => ['sale', 'best-selling', 'top-rated']
				],
			]
		);


        $this->add_control(
			'order',
			[
				'label' => esc_html__( 'Order', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => [
                    'ASC' => __('Ascending','wts-eae'),
                    'DESC' => __('Descending', 'wts-eae'),
                ],
                'default' => 'ASC',
                'condition' => [
					'filter_by!' => ['','recent','best-selling', 'top-rated']
				],
			]
		);

        $this->add_control(
            'pro_count',
            [
                'label' => esc_html__( 'Product Count' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'max' => 10,
                'min' => 1,
                'step' => 1,
            ]
        );

        $this->add_control(
            'offset',
            [
                'label' => esc_html__( 'Offset' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'max' => 10,
                'min' => 1,
                'step' => 1
            ]
        );

        $this->add_control(
			'pro_status',
			[
				'label' => esc_html__( 'Product Status', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => [
                    'publish' => __('Publish','wpv-bu'),
                    'draft' => __('Draft','wpv-bu'),
                    'pending' => __('Pending Review','wpv-bu'),
                    'future' => __('Schedule','wpv-bu'),
                ],
                'default' => 'publish',
			]
		);

        $this->add_control(
			'categories',
			[
				'label' => esc_html__( 'Product Categories', 'wta-eae' ),
				'type' => Controls_Manager::SELECT2,
                'options' => $category,
                'default' => '',
                'multiple' => true,
                'label_block' => true,
 			]
		);

        $this->add_control(
			'pro_tag',
			[
				'label' => esc_html__( 'Product tag', 'wta-eae' ),
				'type' => Controls_Manager::SELECT2,
                'options' => $tags,
                'default' => '',
                'multiple' => true,
                'label_block' => true,
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'eae-woo-rating',
			[
				'label' => __( 'Rating', 'wts-eae' ),
			]
		);

        $this->add_control(
			'filled_icon',
			[
				'label'            => __( 'Filled Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);

        $this->add_control(
			'half_filled_icon',
			[
				'label'            => __( 'Half Filled Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'default'          => [
					'value'   => 'fas fa-star-half-alt',
					'library' => 'solid',
				],
			]
		);

        $this->add_control(
			'rating_filled_icon',
			[
				'label'            => __( 'Unmarked Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'default'          => [
					'value'   => 'far fa-star',
					'library' => 'solid',
				],
			]
		);
        $this->end_controls_section();

        $this->start_controls_section(
			'eae-woo-button',
			[
				'label' => __( 'Button', 'wts-eae' ),
			]
		);


        $this->add_control( 
			'media_button',
			[
				'label'        => __( 'Media Button', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'default'      => "yes",
                'condition' => [
					'product_layout' => 'split',
				],
			]
		);


        $this->add_control( 
			'content_button',
			[
				'label'        => __( 'Content Button', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
				'default'      =>'yes'
			]
		);

        $this->add_control( 
			'hover_button',
			[
				'label'        => __( 'Hover', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'enable-hover',
				'default'      =>'yes',
                'condition' => [
					'product_layout' => 'split',
                    'media_button'  => 'yes',
				],
			]
		);

        $this->add_control( 
			'enable_gallery',
			[
				'label'        => __( 'Enable Gallery', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'return_value' => 'yes',
                'default' => '',
                'description' => __('Enable this option to display a product image gallery instead of a single image in Quick View.', 'wts-eae'),
			]
		);


        $repeater = new Repeater();

        $repeater->add_control(
			'media_action',
			[
				'label' => esc_html__( 'Action', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => [
                    'add_to_cart' => __('Add to Cart','wts-eae'),
                    'buy_now' => __('Buy Now','wts-eae'),
                    'link' => __('Link','wts-eae'),
                    'quick_view' => __('Quick View','wts-eae'),
                ],
			]
		);

        $repeater->add_control(
			'media_title',
			[
				'label'			 => esc_html__( 'Title', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
			]
		);

        $repeater->add_control(
			'media_icon',
			[
				'label'            => __( 'Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
			]
		);

        $this->add_control(
            'media',
            [
                'label' => esc_html__( 'Media', 'wts-eae' ),
                'type' => Controls_Manager::REPEATER,
                'separator'=>'before',
                'fields' => $repeater->get_controls(),
                'condition'=>[
                    "media_button" => 'yes',
                    'product_layout' => 'split',
                ],
                'default' => [
                    [
                        'media_action'=> 'buy_now',
                        'media_icon' => [
                            'value'   => 'fas fa-shopping-bag',
                            'library' => 'solid',
                        ],
                    ],
                    [
                        'media_action'=> 'link',
                        'media_icon' => [
                            'value'   => 'fas fa-link',
                            'library' => 'solid',
                        ],
                    ],
                    [
                        'media_action'=> 'quick_view',
                        'media_icon' => [
                            'value'   => 'fas fa-eye',
                            'library' => 'solid',
                        ],
                    ],
                ],
                'title_field' => '{{{ media_action.replace(/_/g," ").toUpperCase() }}}',
            ]
        );

        $this->add_responsive_control(
            'icon_position',
            [
                'label' => esc_html__('Icon Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'up' => [
                        'title' => esc_html__('Up','wts-eae'),
                        'icon' => 'eicon-arrow-up',
                    ],
                    
                    'down' =>[
                        'title' => esc_html__('Down','wts-eae'),
                        'icon' => 'eicon-arrow-down',
                    ] , 
                    'right' =>[
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-arrow-right',
                    ]
                ],
            
                'toggle' => false,
                'selectors_dictionary' => [
                    'left' => 'row-reverse',
                    'up' => 'column-reverse',
                    'right' => 'row',
                    'down' => 'column',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn .eae-wp-icon' => 'flex-direction: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
			'media_layout',
			[
				'label' => esc_html__( 'Button Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'column' => 'Vertical',
					'row' => 'Horizontal',
				],
				'default' => 'column',
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
                'selectors_dictionary' => [
                    'column' => 'column',
                    'row' => 'row',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn' => 'flex-direction: {{VALUE}}',
                ],
			]
		);

        $this->add_responsive_control(
            'vertical_position',
            [
                'label' => esc_html__('Vertical Position','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn' => 'top:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
            ]
        );

        $this->add_responsive_control(
            'horizontal_position',
            [
                'label' => esc_html__('Horizontal Position','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn' => 'right:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
            ]
        );

        $this->add_responsive_control(
            'button_space',
            [
                'label' => esc_html__('Button Space','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn' => 'gap:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
            ]
        );

        $this->add_responsive_control(
            'icon_space',
            [
                'label' => esc_html__('Icon Space','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn .eae-wp-icon' => 'gap:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
            ]
        );

        
        $contentRepeater = new Repeater();

        $contentRepeater->add_control(
			'content_action',
			[
				'label' => esc_html__( 'Action', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'options' => [
                    'add_to_cart' => __('Add to Cart','wts-eae'),
                    'buy_now' => __('Buy Now','wts-eae'),
                    'link' => __('Link','wts-eae'),
                    'quick_view' => __('Quick View','wts-eae'),
                ],
			]
		);

        $contentRepeater->add_control(
			'content_title',
			[
				'label'			 => esc_html__( 'Title', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
			]
		);

        $contentRepeater->add_control(
			'content_icon',
			[
				'label'            => __( 'Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
			]
		);

        
        $this->add_control(
            'content',
            [
                'label' => esc_html__( 'Content', 'wts-eae' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $contentRepeater->get_controls() ,
                'condition' => [
                    'content_button'  => 'yes',
				],
                'title_field' => '{{{ content_action.replace(/_/g," ").toUpperCase() }}}',
                'separator'=>'before',
                'default' => [
					[
                        'content_action' => 'add_to_cart',
                        'content_icon' => [
                            'value' => 'fas fa-shopping-cart',
                            'library' => 'solid'
                        ]    
					],
				],
            ]
        );

        $this->add_responsive_control(
            'content_icon_position',
            [
                'label' => esc_html__('Icon Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'up' => [
                        'title' => esc_html__('Up','wts-eae'),
                        'icon' => 'eicon-arrow-up',
                    ],
                    
                    'down' =>[
                        'title' => esc_html__('Down','wts-eae'),
                        'icon' => 'eicon-arrow-down',
                    ] , 
                    'right' =>[
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-arrow-right',
                    ]
                ],
            
                'toggle' => false,
                'selectors_dictionary' => [
                    'left' => 'row-reverse',
                    'up' => 'column-reverse',
                    'right' => 'row',
                    'down' => 'column',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-button .eae-wp-icon' => 'flex-direction: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
			'content_layout',
			[
				'label' => esc_html__( 'Button Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'vertical' => 'Vertical',
					'horizontal' => 'Horizontal',
				],
				'default' => 'horizontal',
                'condition'=>[
				    'content_button' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
            'content_button_position',
            [
                'label' => esc_html__('Button Position','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'up' => [
                        'title' => esc_html__('Up','wts-eae'),
                        'icon' => 'eicon-arrow-up',
                    ],
                    
                    'down' =>[
                        'title' => esc_html__('Down','wts-eae'),
                        'icon' => 'eicon-arrow-down',
                    ] , 
                    'right' =>[
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-arrow-right',
                    ]
                ],
                'toggle' => false,
                'prefix_class' => 'eae-content-btn-',
                'selectors_dictionary' => [
                    'left' => 'row-reverse',
                    'up' => 'column-reverse',
                    'right' => 'row',
                    'down' => 'column',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-content' => 'flex-direction : {{VALUE}}' 
                ]
            ]
        );

        $this->add_responsive_control(
			'content_button_left_right',
			[
				'label' => esc_html__( 'Button Alignment', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'top'    => [
						'title' => esc_html__( 'Top', 'wta-eae' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-v-align-middle',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'center' => 'center',
                    'end' => 'flex-end',
                ],
                'conditions'=>[
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'content_button',
                            'operator' =>'==',
                            'value' => 'yes'
                         ],
                         [
                             'relation' => 'or',
                             'terms' => [
                                [
                                    'name' => 'content_button_position',
                                    'operator' => '===',
                                    'value' => 'left',
                                ],
                                [
                                    'name' => 'content_button_position',
                                    'operator' => '===',
                                    'value' => 'right',
                                ],
                             ],
                             
                         ],
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content .eae-wp-button' => 'align-self: {{VALUE}}',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content .eae-wp-content-button' => 'align-self: {{VALUE}}',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				],
			]
		);

        $this->add_responsive_control(
			'content_button_top_bottom',
			[
				'label' => esc_html__( 'Button Alignment', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Start', 'wta-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-center-h',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
					'spaceBetween' => [
						'title' => esc_html__( 'Space between', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-h',
					],
					'spaceAround' => [
						'title' => esc_html__( 'Space around', 'wta-eae' ),
						'icon' => 'eicon-justify-space-around-h',
					],
					'spaceEvenly' => [
						'title' => esc_html__( 'Space evenly', 'wta-eae' ),
						'icon' => 'eicon-justify-space-evenly-h',
					],
				],
                'selectors_dictionary' => [
                    'start' => 'start',
                    'center' => 'center',
                    'end' => 'end',
                    'spaceBetween' => 'space-between',
                    'spaceAround' => 'space-around',
                    'spaceEvenly' => 'space-evenly',
                ],
                'conditions'=>[
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'content_button',
                            'operator' =>'==',
                            'value' => 'yes'
                         ],
                         [
                             'relation' => 'or',
                             'terms' => [
                                [
                                    'name' => 'content_button_position',
                                    'operator' => '===',
                                    'value' => 'up',
                                ],
                                [
                                    'name' => 'content_button_position',
                                    'operator' => '===',
                                    'value' => 'down',
                                ],
                             ],
                             
                         ],
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-button .eae-wp-content-button' => 'justify-content: {{VALUE}}',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-button' => 'justify-content: {{VALUE}}',
                    '{{WRAPPER}} .eae-wp-btn-layout-horizontal.eae-woo-products .eae-wp-content .eae-wp-content-button' => 'justify-content: {{VALUE}}',
                    '{{WRAPPER}} .eae-wp-btn-layout-vertical.eae-woo-products .eae-wp-content .eae-wp-content-button' => 'align-items: {{VALUE}}',
                ],
			]
		);

        $this->add_responsive_control(
            'content_icon_size',
            [
                'label' => esc_html__('Icon Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-button i' => 'font-size:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );


        

        $this->add_responsive_control(
            'content_button_space',
            [
                'label' => esc_html__('Button Space','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-button ' => 'gap:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );

        $this->add_responsive_control(
            'content_icon_space',
            [
                'label' => esc_html__('Icon Space','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-button .eae-wp-icon' => 'gap:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );


        

        $this->end_controls_section();

        $this->start_controls_section(
			'eae-woo-badge',
			[
				'label' => __( 'Product Badges', 'wts-eae' ),
			]
		);

        $this->add_control( 
			'hide_sale_badge',
			[
				'label'        => __( 'Hide Sales/Out Of Stock Badge', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'default'      => '',
			]
		);

        $this->add_control( 
			'disable_stock_badge',
			[
				'label'        => __( 'Hide Out of stock badge', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'condition'    =>[
                    'hide_sale_badge!' => 'yes',
                    'exclude_out_of_stock!' =>'yes' ,
                ]
			]
		);

        $this->add_control(
			'badge_layout',
			[
				'label' => esc_html__( 'Layout', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'options'   => [
                    'preset1' => __('Preset 1', 'wts-eae'),
                    'preset2' => __('Preset 2', 'wts-eae'),
                    'preset3' => __('Preset 3', 'wts-eae'),
                    'preset4' => __('Preset 4', 'wts-eae'),
                    'preset5' => __('Preset 5', 'wts-eae'),
                ],
                'default' => 'preset1',
                'condition'    =>[
                    'hide_sale_badge!' => 'yes',
                ]
			]
		);

        $this->add_control(
			'badge_position',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'options'   => [
                    'left' => __('Left', 'wts-eae'),
                    'right' => __('Right', 'wts-eae'),
                ],
                'default' => 'left',
                'condition'    =>[
                    'hide_sale_badge!' => 'yes',
                ]
			]
		);

        $this->add_control(
			'sale_text',
			[
				'label'			 => esc_html__( 'Sales Badge Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'description'	 => esc_html__( 'To get dynamic discount% add {{discount}}, and to get dynamic price off {{price_off}}.', 'wts-eae' ),
				'label_block'	 => true,
                'default'        => 'Sale!',
                'condition'    =>[
                    'hide_sale_badge!' => 'yes',
                ]
			]
		);
        
        $this->add_control(
			'stock_text',
			[
				'label'			 => esc_html__( 'Out Of Stock Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'default'        => 'Out Of Stock',
  				'label_block'	 => true,
                'condition'    =>[
                    'hide_sale_badge!' => 'yes',
                    'disable_stock_badge!' => 'yes',
                ]
			]
		);


        $this->end_controls_section();

        $this->start_controls_section(
            'wp_carousel_setting',
            [
                'label' => esc_html__('Slider Options','wts-eae'),
                'condition' => [
                    'layout' => 'slider',
                ]
            ]
        );

        Swiper_helper::carousel_controls($this);

        $this->end_controls_section();

        $this->start_controls_section(
            'wp_order_setting',
            [
                'label' => esc_html__('Order','wts-eae'),
            ]
        );

        $this->add_responsive_control(
			'title_order',
			[
				'label'			 => esc_html__( 'Title', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'default'		 =>'1',
				'selectors' => [
					'{{WRAPPER}} .eae-product-card .eae-wp-content .eae-wp-content-inner .eae-wp-title' => 'order:{{VALUE}}'
				],
			]
		);

		$this->add_responsive_control(
			'price_order',
			[
				'label'			 => esc_html__( 'Price', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,				
				'label_block'	 => false,
				'default'		 =>'2',
				'selectors' => [
					'{{WRAPPER}} .eae-product-card .eae-wp-content .eae-wp-content-inner .eae-wp-price' => 'order:{{VALUE}}'
				],
			]
		);

		$this->add_responsive_control(
			'rating_order',
			[
				'label'			 => esc_html__( 'Rating', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'default'		 =>'1',
				'selectors' => [
					'{{WRAPPER}} .eae-product-card .eae-wp-content .eae-wp-content-inner .eae-wp-rating' => 'order:{{VALUE}}'
				],
			]
		);

		$this->add_responsive_control(
			'des_order',
			[
				'label'			 => esc_html__( 'Description', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
				'default'		 =>'4',
				'selectors' => [
					'{{WRAPPER}} .eae-product-card .eae-wp-content .eae-wp-content-inner .eae-wp-des' => 'order:{{VALUE}}'
				],
                'condition' =>[
                    'hide_des!'=>'yes'
                ]
			]
		);


        $this->end_controls_section();

        $this->start_controls_section(
            'layout_style',
            [
                'label' => esc_html__( 'Content', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'column',
            [
                'label' => esc_html__( 'Column' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'step' => 1,
                'desktop_default' => '3',
				'tablet_default'  => '2',
				'mobile_default'  => '1',
                'selectors' => [
					'{{WRAPPER}} .eae-woo-products.eae-wp-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',						
				],

                'condition' => [
                    'layout' => 'grid'
                ],
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => esc_html__('Column Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products.eae-wp-grid' => 'column-gap:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'layout' => 'grid'
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Row Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products.eae-wp-grid' => 'row-gap:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'layout' => 'grid'
                ],
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => esc_html__('Title','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
					'hide_title!'=>'yes'
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_title',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-content-inner .eae-wp-title',
                'condition'=>[
					'hide_title!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content .eae-wp-title a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content .eae-wp-title' => 'color: {{VALUE}};',
                ],
                'condition'=>[
					'hide_title!'=>'yes'
				]
            ]
        );

        $this->add_responsive_control(
            'title_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products.eae-wp-grid .eae-wp-title' => 'padding-bottom:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'hide_title!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'price_heading',
            [
                'label' => esc_html__('Price','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
					'hide_price!'=>'yes'
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_price',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-product-card .eae-wp-price ',
                'condition'=>[
					'hide_price!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-price ' => 'color: {{VALUE}};',
                ],
                'condition'=>[
					'hide_price!'=>'yes'
				]
            ]
        );

        $this->add_responsive_control(
            'price_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products.eae-wp-grid .eae-wp-price' => 'padding-bottom:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'hide_price!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'des_price',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
					'hide_des!'=>'yes'
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_des',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-content-inner .eae-wp-des',
                'condition'=>[
					'hide_des!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'des_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-inner .eae-wp-des ' => 'color: {{VALUE}};',
                ],
                'condition'=>[
					'hide_des!'=>'yes'
				]
            ]
        );


        $this->add_responsive_control(
            'des_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products.eae-wp-grid .eae-wp-des' => 'padding-bottom:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'hide_des!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'des_text_align',
            [
                'label' => esc_html__('Text Alignment','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'separator' => 'after',
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left','wts-eae'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center','wts-eae'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                    'right' => [
                        'title' => esc_html__('Right','wts-eae'),
                        'icon' => 'eicon-text-align-right',
                    ]
                ],
                'toggle' => false,
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-inner .eae-wp-des' => 'text-align: {{VALUE}};',
                ],
                'condition'=>[
					'hide_des!'=>'yes'
				]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'rating_style',
            [
                'label' => esc_html__( 'Rating', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
					'hide_rat!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'marked_color',
            [
                'label' => esc_html__( 'Marked Icon', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-inner .eae-wp-rating .woo-coo-star.checked' => 'color: {{VALUE}};',
                ],
                'condition'=>[
					'hide_rat!'=>'yes'
				]
            ]
        );
        $this->add_control(
            'unmarked_color',
            [
                'label' => esc_html__( 'Unmarked Icon', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-inner .eae-wp-rating  .woo-coo-star' => 'color: {{VALUE}};',
                ],
                'condition'=>[
					'hide_rat!'=>'yes'
				]
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Icon Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products.eae-wp-grid .eae-wp-rating i' => 'font-size:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'hide_rat!'=>'yes'
				]
            ]
        );

        $this->add_responsive_control(
            'icon_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products.eae-wp-grid .eae-wp-rating ' => 'gap:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'hide_rat!'=>'yes'
				]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'button_style',
            [
                'label' => esc_html__( 'Button', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'media_style',
            [
                'label' => esc_html__('Media Button','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				]
            ]
        );

        $this->add_control(
            'media_button_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn .eae-wp-icon' => 'color: {{VALUE}}; fill : {{VALUE}}',
                ],
                'condition'=>[
                    'media_button' => 'yes',
                    'product_layout' => 'split'
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'media_button_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn .eae-wp-icon',
                'condition'=>[
                    'media_button' => 'yes',
                    'product_layout' => 'split'
				]
            ]
        );

        $this->add_control(
            'media_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn svg' => 'fill: {{VALUE}};',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn .eae-wp-icon',
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
            ]
        );


        $this->add_responsive_control(
            'media_icon_size',
            [
                'label' => esc_html__('Icon Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn i' => 'font-size:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-media-btn svg' => 'height:{{SIZE}}{{UNIT}}; width:auto;',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				]
            ]
        );


        

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'media_border',
				'selector' => '{{WRAPPER}}  .eae-wp-media-btn .eae-wp-icon',
				'separator' => 'before',
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
				
			]
		);

        $this->add_responsive_control(
			'media_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}   .eae-wp-media-btn .eae-wp-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
			]
		);

        $this->add_responsive_control(
			'media_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-wp-media-btn .eae-wp-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
			]
		);

        $this->add_responsive_control(
			'media_margin',
			[
				'label' => esc_html__( 'Margin', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-wp-media-btn .eae-wp-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				],
			]
		);

        $this->add_responsive_control(
            'media_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-media-btn .eae-wp-icon' => 'width:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'media_button' => 'yes',
                    'product_layout' => 'split'
				]
            ]
        );

        $this->add_control(
            'content_style',
            [
                'label' => esc_html__('Content Button','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );


        $this->add_control(
            'content_button_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-button .eae-wp-icon' => 'color: {{VALUE}}; fill : {{VALUE}};',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_button_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-content-button .eae-wp-icon',
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );

        $this->add_control(
            'content_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-button .eae-wp-icon i' => 'color: {{VALUE}}; ',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content-button .eae-wp-icon svg' => 'fill: {{VALUE}}; ',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-content-button .eae-wp-icon',
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );

       
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'selector' => '{{WRAPPER}}  .eae-wp-content-button .eae-wp-icon',
				'separator' => 'before',
                'condition'=>[
				    'content_button' => 'yes'
				]
				
			]
		);

        $this->add_responsive_control(
			'content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-wp-content-button .eae-wp-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
				    'content_button' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .eae-wp-content-button .eae-wp-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
				    'content_button' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
			'content_margin',
			[
				'label' => esc_html__( 'Margin', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-wp-content-button .eae-wp-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
				    'content_button' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
            'content_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-content-button .eae-wp-icon' => 'width:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
				    'content_button' => 'yes'
				]
            ]
        );
       
        $this->end_controls_section();

        $this->start_controls_section(
            'preset_style',
            [
                'label' => esc_html__( 'Preset', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'preset_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-product-card',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'preset_border',
				'selector' => '{{WRAPPER}} .eae-woo-products .eae-product-card',
			]
		);

        $this->add_responsive_control(
			'preset_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-products .eae-product-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'preset_box_shadow',
				'selector' => '{{WRAPPER}}  .eae-woo-products .eae-product-card',
				
			]
		);

        $this->add_responsive_control(
			'preset_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}   .eae-woo-products .eae-product-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'image_heading',
            [
                'label' => esc_html__('Image','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator'=>'before',
                'condition'=>[
					'product_layout'=>'split'
				]                
            ]
        );

        $this->add_responsive_control(
            'img_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}}' => '--eae-wp-img-width:{{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'product_layout',
                            'value' => 'split',
                        ],
                        [
                            'relation' => 'or',
                            'terms' => [
                                [
                                    'name' => 'sp_layout',
                                    'value' => 'left',
                                ],
                                [
                                    'name' => 'sp_layout',
                                    'value' => 'right',
                                ],
                            ]
                        ]
                    ]
                ],
                
            ]
        );

        $this->add_responsive_control(
            'img_height',
            [
                'label' => esc_html__('Height','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-image' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
					'product_layout'=>'split'
				]
            ]
        );

        $this->add_responsive_control(
			'img_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}}   .eae-woo-products .eae-wp-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'=>[
					'product_layout'=>'split'
				]   
			]
		);

        $this->add_control(
            'content_heading',
            [
                'label' => esc_html__('Content','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator'=>'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'con_background',
                'types' => [ 'classic', 'gradient' , 'image' ],
                'selector' => '{{WRAPPER}} .eae-product-card .eae-wp-content',
                'condition' => [
                    'product_layout' => 'split'
                ]
            ]
        );

        $this->add_control(
            'content_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-product-card .eae-wp-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control( 
			'content_hover',
			[
				'label'        => __( 'Content Hover', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
                'condition' => [
                    'product_layout' => 'cover',
                    'pre_layout' => 'pre1',
                ]
			]
		);

        $this->add_control(
			'content_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' => 'Top',
					'bottom' => 'Bottom',
					'left' => 'Left',
					'right' => 'Right',
				],	
                'default' => 'top',
                'condition' => [
                    'content_hover!' => '',
                ]
			]
		);
    

        $this->add_responsive_control(
			'split_alignment',
			[
				'label' => esc_html__( 'Alignment (Horizontal)', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Top', 'wta-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-h',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'center' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content .eae-wp-content-inner' => 'align-items: {{VALUE}}',
                ],
			]
		);

        $this->add_responsive_control(
			'content_space',
			[
				'label' => esc_html__( 'Content Space', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Start', 'wta-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-center-h',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
					'spaceBetween' => [
						'title' => esc_html__( 'Space between', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-h',
					],
					'spaceAround' => [
						'title' => esc_html__( 'Space around', 'wta-eae' ),
						'icon' => 'eicon-justify-space-around-h',
					],
					'spaceEvenly' => [
						'title' => esc_html__( 'Space evenly', 'wta-eae' ),
						'icon' => 'eicon-justify-space-evenly-h',
					],
				],
                'selectors_dictionary' => [
                    'start' => 'start',
                    'center' => 'center',
                    'end' => 'end',
                    'spaceBetween' => 'space-between',
                    'spaceAround' => 'space-around',
                    'spaceEvenly' => 'space-evenly',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'pre_layout',
                            'value' => 'pre2',
                        ],
                        [
                            'name' => 'product_layout',
                            'value'=>'split'
                        ],
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content .eae-wp-content-outer' => 'justify-content: {{VALUE}}',
                ],
			]
		);


        $this->add_responsive_control(
            'split_content_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
                    'product_layout' => 'cover'
                ],
            ]
        );

    
        $this->add_responsive_control(
			'alignment_pre2',
			[
				'label' => esc_html__( 'Alignment (Vertical)', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Start', 'wta-eae' ),
						'icon' => 'eicon-justify-start-v',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-center-v',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-justify-end-v',
					],
					'spaceBetween' => [
						'title' => esc_html__( 'Space between', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-v',
					],
					'spaceAround' => [
						'title' => esc_html__( 'Space around', 'wta-eae' ),
						'icon' => 'eicon-justify-space-around-v',
					],
					'spaceEvenly' => [
						'title' => esc_html__( 'Space evenly', 'wta-eae' ),
						'icon' => 'eicon-justify-space-evenly-v',
					],
				],
                'selectors_dictionary' => [
                    'start' => 'start',
                    'center' => 'center',
                    'end' => 'end',
                    'spaceBetween' => 'space-between',
                    'spaceAround' => 'space-around',
                    'spaceEvenly' => 'space-evenly',
                ],
                'condition'=>[
                    'product_layout' => 'cover'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content' => 'justify-content: {{VALUE}}',
                ],
			]
		);

        $this->add_responsive_control(
			'content_ver_alignment',
			[
				'label' => esc_html__( 'Alignment (Horizontal)', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Top', 'wta-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-h',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'center' => 'center',
                    'bottom' => 'flex-end',
                ],
                'condition'=>[
                    'product_layout' => 'cover'
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content' => 'align-items: {{VALUE}}',
                ],
			]
		);

        $this->add_responsive_control(
			'cover_content_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-products .eae-wp-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'content_height',
            [
                'label' => esc_html__('Content Height','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
                    'product_layout' => 'cover'
                ],
            ]
        );

        $this->add_responsive_control(
            'cover_content_width',
            [
                'label' => esc_html__('Content Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content' => 'width:{{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'pre_layout',
                            'value' => 'pre1',
                        ],
                        [
                            'name' => 'product_layout',
                            'value'=>'cover'
                        ],
                    ]
                ],
            ]
        );

        $this->add_responsive_control(
            'cover_top',
            [
                'label' => esc_html__('Top','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => 'px',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content' => 'top:{{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'pre_layout',
                            'value' => 'pre1',
                        ],
                        [
                            'name' => 'product_layout',
                            'value'=>'cover'
                        ],
                    ]
                ],
            ]
        );

        $this->add_responsive_control(
            'cover_Left',
            [
                'label' => esc_html__('Left','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => 'px',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                  
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-content' => 'left:{{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'pre_layout',
                            'value' => 'pre1',
                        ],
                        [
                            'name' => 'product_layout',
                            'value'=>'cover'
                        ],
                    ]
                ],
            ]
        );

        $this->add_responsive_control(
            'content_height_hover',
            [
                'label' => esc_html__('Hover Height','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wp-preset2.eae-woo-products .eae-product-card:hover .eae-wp-content' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'and',
                    'terms'    => [
                        [
                            'name' => 'pre_layout',
                            'value' => 'pre2',
                        ],
                        [
                            'name' => 'product_layout',
                            'value'=>'cover'
                        ],
                    ]
                ],
            ]
        );


        $this->add_control(
            'overlay_heading',
            [
                'label' => esc_html__('Overlay','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator'=>'before',
                'condition'=>[
					'hide_title!'=>'yes'
				]
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-product-card .eae-wp-image .wp-img-overlay' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .wp-cover-layout .eae-product-card .eae-wp-content' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_hover_color',
            [
                'label' => esc_html__( 'Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-product-card:hover .eae-wp-image .wp-img-overlay' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .wp-cover-layout:hover .eae-product-card .eae-wp-content' => 'background: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'badge_style',
            [
                'label' => esc_html__( 'Badge', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'hide_sale_badge!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'badge_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-product-card .eae-wp-sale-tag:has(.wp-sales)' => 'color: {{VALUE}};',
                ],
                'default' => '#FFFFFF',
                'condition' => [
                    'hide_sale_badge!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'badge_background',
            [
                'label' => esc_html__( 'Background Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default'=>'#2196f3',
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales).eae-preset1::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales).eae-preset2::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales).eae-preset3::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales).eae-preset5::after' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales).eae-preset4.eae-position-left::before' => 'border-left-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales).eae-preset4.eae-position-right::before' => 'border-right-color: {{VALUE}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'badge_dot_color',
            [
                'label' => esc_html__( 'Dot Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-sales).eae-preset4::after' => 'background:{{VALUE}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset4',   
                ]
            ]
        );

        $this->add_responsive_control(
            'badge_dot_size',
            [
                'label' => esc_html__('Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => 'px',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-sales).eae-preset4::after' => 'height:{{SIZE}}{{UNIT}}; width:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset4',   
                ]
            ]
        );

        $this->add_responsive_control(
            'badge_dot_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-sales).eae-preset4.eae-position-left .wp-sales' => 'padding-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-sales).eae-preset4.eae-position-right .wp-sales' => 'padding-left:  {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset4',   
                ]
            ]
        );

        $this->add_control(
            'pre5_border_radius',
            [
                'label' => esc_html__( 'Border Radius' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'step' => 1,
                'selectors' => [
                    '{{WRAPPER}}' => '--eae-wp-border-radius: {{VALUE}}px;',
                ],
                'condition'=>[
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset5', 
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)',
                'condition' => [
                    'hide_sale_badge!' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)',
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout1' => 'preset1',
                ]
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)',
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout1' => 'preset1',
                ]
			]
		);

        $this->add_responsive_control(
            'badge_height',
            [
                'label' => esc_html__('Height','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)' => 'height:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' => '--eae-wp-badge-height:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout!' => 'preset3',
                    
                ]
            ]
        );

        $this->add_responsive_control(
            'badge_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)' => 'width:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' => '--eae-wp-badge-width:{{SIZE}}{{UNIT}};',
                ],
                
            ]
        );

        $this->add_responsive_control(
			'badge_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



        $this->add_responsive_control(
			'badge_margin',
			[
				'label' => esc_html__( 'Margin', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-products .eae-wp-sale-tag:has(.wp-sales)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'out_of_stock',
            [
                'label' => esc_html__('Out Of Stock','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'condition'=>[
					'disable_stock_badge!'=>'yes'
				]
            ]
        );
        
        $this->add_control(
            'ofc_badge_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-product-card .eae-wp-sale-tag:has(.wp-stock-out)' => 'color: {{VALUE}};',
                ],
                'default' => '#FFFFFF',
                'condition' => [
                    'hide_sale_badge!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ofc_badge_background',
            [
                'label' => esc_html__( 'Background Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default'=>'#FF0000',
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out).eae-preset1::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out).eae-preset2::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out).eae-preset3::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out).eae-preset5::after' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out).eae-preset4.eae-position-left::before' => 'border-left-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out).eae-preset4.eae-position-right::before' => 'border-right-color: {{VALUE}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ofc_badge_dot_color',
            [
                'label' => esc_html__( 'Dot Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-stock-out).eae-preset4::after' => 'background:{{VALUE}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset4',   
                ]
            ]
        );

        $this->add_responsive_control(
            'ofc_badge_dot_size',
            [
                'label' => esc_html__('Size','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => 'px',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-stock-out).eae-preset4::after' => 'height:{{SIZE}}{{UNIT}}; width:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset4',   
                ]
            ]
        );

        $this->add_responsive_control(
            'ofc_badge_dot_gap',
            [
                'label' => esc_html__('Gap','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-stock-out).eae-preset4.eae-position-left .wp-sales' => 'padding-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eae-wp-sale-tag:has(.wp-stock-out).eae-preset4.eae-position-right .wp-sales' => 'padding-left:  {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset4',   
                ]
            ]
        );

        $this->add_control(
            'ofc_pre5_border_radius',
            [
                'label' => esc_html__( 'Border Radius' , 'wts-eae'), 
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'step' => 1,
                'selectors' => [
                    '{{WRAPPER}}' => '--eae-wp-border-radius: {{VALUE}}px;',
                ],
                'condition'=>[
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset5', 
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ofc_badge_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)',
                'condition' => [
                    'hide_sale_badge!' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ofc_badge_border',
				'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)',
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout1' => 'preset1',
                ]
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ofc_badge_box_shadow',
				'selector' => '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)',
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout' => 'preset1',
                ]
			]
		);

        $this->add_responsive_control(
            'ofc_badge_height',
            [
                'label' => esc_html__('Height','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)' => 'height:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' => '--eae-wp-badge-height:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hide_sale_badge!' => 'yes',
                    'badge_layout!' => 'preset3',
                    
                ]
            ]
        );

        $this->add_responsive_control(
            'ofc_badge_width',
            [
                'label' => esc_html__('Width','wts-eae'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)' => 'width:{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' => '--eae-wp-badge-width:{{SIZE}}{{UNIT}};',
                ],
                
            ]
        );

        $this->add_responsive_control(
			'ofc_badge_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);



        $this->add_responsive_control(
			'ofc_badge_margin',
			[
				'label' => esc_html__( 'Margin', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}  .eae-woo-products .eae-wp-sale-tag:has(.wp-stock-out)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);






        $this->end_controls_section();

        $this->start_controls_section(
            'slider_style',
            [
                'label' => esc_html__( 'Slider', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout!' => 'grid',
                ]
            ]
        );
            Swiper_helper::carousel_style_section($this);
        $this->end_controls_section();

        $this->start_controls_section(
            'quick_view_style',
            [
                'label' => esc_html__( 'Quick View', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'pop_content_position',
            [
                'label' => esc_html__('Direction','wts-eae'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row' => [
                        'title' => esc_html__( 'Row - horizontal', 'elementor' ),
                        'icon' => 'eicon-arrow-left',
                    ],
                    'column' => [
                        'title' => esc_html__( 'Column - vertical', 'elementor' ),
                        'icon' => 'eicon-arrow-up',
                    ],
                    
                    'row-reverse' =>[
                        'title' => esc_html__( 'Row - reversed', 'elementor' ),
                        'icon' => 'eicon-arrow-down',
                    ] , 
                    'column-reverse' =>[
                        'title' => esc_html__( 'Column - reversed', 'elementor' ),
                        'icon' => 'eicon-arrow-right',
                    ]
                ],
                'toggle' => false,
                'condition'=>[
				    'content_button' => 'yes'
				],
                'selectors' => [
                    '.eae-wp-{{ID}} .eae-wp-popup-container' => 'flex-direction: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
			'pop_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Top', 'wta-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wta-eae' ),
						'icon' => 'eicon-justify-space-between-h',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wta-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'center' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '.eae-wp-{{ID}} .eae-wp-popup-container .wp-popup-content' => 'align-items: {{VALUE}}',
                ],
			]
		);

        $this->add_control(
            'pop_title',
            [
                'label' => esc_html__('Title','wts-eae'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pop_title_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '.eae-wp-{{ID}} .eae-wp-popup-container .wp-popup-title', 
            ]
        );

        $this->add_control(
            'pop_title_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.eae-wp-{{ID}} .eae-wp-popup-container .wp-popup-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pop_price',
            [
                'label' => esc_html__('Price','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator'=>'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pop_price_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '.eae-wp-{{ID}} .eae-wp-popup-container .wp-popup-price', 
            ]
        );

        $this->add_control(
            'pop_price_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.eae-wp-{{ID}} .eae-wp-popup-container .wp-popup-price' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'pop_des',
            [
                'label' => esc_html__('Description','wts-eae'),
                'type' => Controls_Manager::HEADING,
                'separator'=>'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'pop_des_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '.eae-wp-{{ID}} .eae-wp-popup-container .wp-popup-desc', 
            ]
        );

        $this->add_control(
            'pop_des_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.eae-wp-{{ID}} .eae-wp-popup-container .wp-popup-desc' => 'color: {{VALUE}};',
                ],
            ]
        );



        $this->end_controls_section();
    }

    public function get_tags_name(){
        $terms = get_terms(array(
            'taxonomy' => 'product_tag',
            'hide_empty' => false,
        ) );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $tags=[];

            foreach($terms as $key => $term){
                $name = $term->name;
                $tagslug = $term->slug;
                $tags[$tagslug] = $name;           
            }

            return $tags;
        } 
    }
    public function get_category_name(){
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ) );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $categories=[];

            foreach($terms as $key => $term){
                $name = $term->name;
                $catslug = $term->slug;
                $categories[$catslug] = $name;           
            }

            return $categories;
        } 
    }

    public function get_product_query(){
        $settings = $this->get_settings_for_display();

        $post_per_page = isset($settings['pro_count']) ? $settings['pro_count'] : 10;
        $offset = isset($settings['offset']) ? $settings['offset'] : '';
        $order = isset($settings['order']) ? $settings['order'] : __('Desc', 'wts-eae');
        $product_status = isset($settings['pro_status']) ? $settings['pro_status'] : __('Publish','wts-eae');
        $product_category = isset($settings['categories']) ? $settings['categories'] : '';
        $product_tag = isset($settings['pro_tag']) ? $settings['pro_tag'] : '';
        $product_stock_status = !empty($settings['exclude_out_of_stock']) ? "instock" : '';
        $args = [
            'posts_per_page'    => $post_per_page,
			'status'            => $product_status,
			'post_type'         => 'product',
            'offset'            => $offset,
            'category'          => $product_category,
            'tag'               => $product_tag,
            'stock_status'      => $product_stock_status,

        ];

        $args = $this->get_query_values($settings,$args,$order);

        //including products by id
        if (isset($settings['filter_by'])&& $settings['filter_by'] === 'manual') {
            if(!empty($settings['inc_products'])){
                $a = $settings['inc_products'];
                $prt_id = explode("," ,$a);
                $args['include'] = $prt_id;
            }
        }
        if(!empty($settings['exc_products'])){
            $a = $settings['exc_products'];
            $prt_id = explode("," ,$a);
            $args['exclude'] = $prt_id;
        } 
     
        return $args;

    }

    public function get_query_values($settings, $args,$order){
        if(isset($settings['order_by'])){
         $args['orderby'] = $settings['order_by'];

            if($settings['order_by'] === 'price'){
                $args['orderby'] = 'meta_value_num';
                $args['meta_key']  = '_price';
            }
        }
        

        if(isset($settings['filter_by'])){
            switch ($settings['filter_by']){
                case 'recent' :
                    $args = $this->get_recent_query($settings, $args);
                    break;
                case 'featured' : 
                    $args = $this->get_featured_query($args, $order);
                    break;
                case 'top-rated' : 
                    $args = $this->get_top_rated_query($args, $order);
                    break;
                case 'best-selling' :
                    $args = $this->get_best_selling_query($args, $order);
                    break;
                case 'sale' :
                    $args = $this->get_sales_query($args, $order);
                    break; 
            }
        }
        return $args;
    }

    public function get_top_rated_query($args, $order){
        $args['meta_key'] = '_wc_average_rating';
        $args['orderby'] = 'meta_value_num'; 
        $args['order']  =  'DESC';
        return $args;
    }

    public function get_recent_query($settings, $args){
        $args['order']  =  'Desc';
        $args['orderby'] = isset($settings['order_by']) ? $settings['order_by'] : 'date';
         if(isset($settings['order_by'])){
            $args['orderby'] = $settings['order_by'];
   
               if($settings['order_by'] === 'price'){
                   $args['orderby'] = 'meta_value_num';
                   $args['meta_key']  = '_price';
               }
   
           }
        return $args;
    }

    public function get_featured_query($args,$order){
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN', // or 'NOT IN' to exclude feature products
        );
        $args['order']  =  $order;
        $args['tax_query'] = $tax_query;
        return $args;
    }

    public function get_sales_query($args,$order){
        $today = date('Y-m-d');
        $args['order']  =  $order;
        $args['orderby'] = 'meta_value_num';
        $args['meta_key']  = '_sale_price';
        $args['meta_query'] = [
            'relation' => 'AND',
            [
                'relation' => 'AND',
                [
                    'key' => '_sale_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC',
                ],
                [
                    'key' => '_price',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC',
                ],
                
            ],
            [
                'relation' => 'OR',
                [
                    'key' => '_sale_price_dates_from',
                    'value' => $today,
                    'compare' => '<=',
                    'type' => 'DATE',
                ],
                [
                    'key' => '_sale_price_dates_from',
                    'value' => '',
                    'compare' => '=',
                ],

            ],
            [
                'relation' => 'OR',
                [
                    'key' => '_sale_price_dates_to',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATE',
                ],
                [
                    'key' => '_sale_price_dates_to',
                    'value' => '',
                    'compare' => '=',
                ],
            ]
        ];
        return $args;
    }

    public function get_best_selling_query($args, $order){
        $args['order']  =  $order;
        $args['meta_key']  =  'total_sales';
        $args['orderby'] = 'meta_value_num';
        return $args;
         
    }
    
    public function render(){

        $product_args = $this->get_product_query();
        $settings = $this->get_settings_for_display();
        $query = new WC_Product_Query($product_args);
        $products = $query->get_products();
        $wid = $this->get_id();
        add_action('wp_footer', 'woocommerce_photoswipe');

        $layout = isset($settings['layout']) ? $settings['layout'] : 'grid';
        $root_class = ['eae-woo-products'];

        if($layout == 'grid') {
            $root_class[] .= 'eae-wp-grid';
        }else{
            if($layout == 'slider'){
                $swiper_data = Swiper_helper::get_swiper_data($settings);
                $this->add_render_attribute('root_class','class','eae-woopro-swiper');
                $this->add_render_attribute('root_class','class','eae-swiper-outer-wrapper');
                
                $this->add_render_attribute('swiper_container','class','eae-swiper');
                
                if($settings['arrows_layout'] == 'inside'){
                    $this->add_render_attribute('root_class','class','eae-hpos-' . $settings['arrow_horizontal_position']);
                    $this->add_render_attribute('root_class','class','eae-vpos-' . $settings['arrow_vertical_position']);
                }
                     
                $this->add_render_attribute('root_class','data-swiper-settings', wp_json_encode( $swiper_data ) );

                $this->add_render_attribute( 'swiper_container', 'class', 'eae-swiper-container ' );


                $slider_id = rand(99,9999);
                $this->add_render_attribute('swiper_container','data-eae-slider-id',$slider_id);

                $this->add_render_attribute('swiper_container','class','eae-slider-id-'. $slider_id);
                if($settings['arrows_layout'] == 'outside'){
                    $this->add_render_attribute('root_class','class','eae-slider-id-'. $slider_id);
                }
                if( $settings['auto_height'] !== 'yes'){
                    $this->add_render_attribute('root_class','class','eae-height-100');
                }

                $this->add_render_attribute('swiper_wrapper','class','eae-wp-carousel-wrapper eae-woopro-swiper');

                $this->add_render_attribute('swiper_wrapper','class', 'eae-swiper-wrapper swiper-wrapper');

            }
            $root_class[] .= 'eae-wp-slider ';
        }

        if($settings['product_layout'] == 'split'){
            $root_class[] .= 'wp-split-layout';
        }else{
            $root_class[] .= 'wp-cover-layout';
        }
        $root_class[].= "eae-wp-btn-layout-".$settings['content_layout'];
        if($settings['pre_layout'] == 'pre1'){
            $root_class[] .= 'wp-preset1';
        }else{
            $root_class[] .= 'wp-preset2';
        }

        $this->add_render_attribute('root_class', [    
            'class' => $root_class ,
        ]);

        ?>
        
        <div <?php echo  $this->get_render_attribute_string('root_class') ?>> <?php
            if($layout == 'slider'){
                ?> 
                <div <?php echo $this->get_render_attribute_string('swiper_container'); ?> >

                <div <?php echo $this->get_render_attribute_string('swiper_wrapper'); ?> >
                <?php
            }
            if(!empty($products)){
                foreach ($products as $index => $product) {
                    $flag = 0;
                    setup_postdata($product->get_id());
                    // 
                    
                    if(isset($settings['product_layout'])&& $settings['product_layout'] === 'split'){
                        if(isset($settings['pre_layout']) && $settings['pre_layout'] === 'pre1' ){
                            $this->render_preset1($settings,$product,$index,$wid);  
                                         
                        }
                        if(isset($settings['pre_layout']) && $settings['pre_layout'] === 'pre2' ){
                           $this->render_preset2($settings, $product, $index, $wid);                 
                        }
                    }
                
                    if(isset($settings['product_layout'])&& $settings['product_layout'] === 'cover'){
                        if(isset($settings['pre_layout']) && $settings['pre_layout'] === 'pre1' ){
                                $this->render_cover_preset1($settings,$product,$index, $wid);         
                        }
                        if(isset($settings['pre_layout']) && $settings['pre_layout'] === 'pre2' ){
                            $this->render_cover_preset2($settings,$product,$index, $wid); 
                        }     
                            
                    }
                }
            }

            if($layout == 'slider'){
                ?> 
                </div>
                <?php 
                    Swiper_helper::get_swiper_pagination($settings);
        
                    if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'inside' ) {
                        Swiper_helper::get_swiper_arrows($settings);
                    }
        
                    Swiper_helper::get_swiper_scrolbar($settings);
                    
                ?>
                </div>
                <?php
                if ( $settings['navigation_button'] === 'yes' && $settings['arrows_layout'] === 'outside' ) {
                    /** Arrows Outside **/
                    Swiper_Helper::get_swiper_arrows($settings);
                }
            }
        ?> 
        
        <!-- </div> -->
        </div>
        <?php

        

    }

    public function render_preset1($settings,$product,$index, $wid){
        
        $product_name = $product->get_name();
        $product_price = $product->get_price_html();
        $product_url = $product->get_permalink();
        $product_image = $product->get_image();
        $hide_badges = isset($settings['hide_sale_badge']) ? $settings['hide_sale_badge'] : false;
        $hide_image = isset($settings['hide_img']) ? $settings['hide_img'] : false;
        $hide_title = isset($settings['hide_title']) ? $settings['hide_title'] : false;
        $hide_rating = isset($settings['hide_rat']) ? $settings['hide_rat'] : false;
        $hide_price = isset($settings['hide_price']) ? $settings['hide_price'] : false;
        $hide_description = isset($settings['hide_des']) ? $settings['hide_des'] : false;
        $this->set_render_attribute('media_btn', [    
            'class' => 'eae-wp-media-btn ' .$settings['hover_button'],
        ]);
        $this->set_render_attribute('content_btn', [    
            'class' => 'eae-wp-button',
        ]);

        $this->add_render_attribute('list-item'.$index, 'class', 'eae-product-card');
        if($settings['layout'] == 'slider'){
            $this->add_render_attribute('list-item'.$index,'class','eae-swiper-slide swiper-slide');
        }
        ?>
        <div <?php echo $this->get_render_attribute_string('list-item'.$index); ?>>
                <?php if(empty($hide_image)){ ?>
                    <div class="eae-wp-image wp-media-btn">
                        <?php 
                        if(!empty($settings['media_button'])){ ?>
                            <div <?php echo $this->get_render_attribute_string('media_btn') ?> > 
                            
                            <?php 
                            $this->render_buttons_html($settings,$product,$index,$product_url,'media', $wid);
                            ?> </div><?php 
                        }?>
                        <span class="wp-img-overlay"></span>
                        <?php echo $product_image ;                       
                        if(empty($hide_badges) ){
                            $this->render_badge($settings, $index, $product);
                        }
                        ?>
                </div> <?php } ?>
                <div class="eae-wp-content"> 
                    <div class="eae-wp-content-inner"> <?php
                        if(empty($hide_title) && !empty($product_name)){ ?>   
                        <span class = 'eae-wp-title' > <a href=<?php echo esc_url($product_url) ?>><?php echo $product_name ?></a></span><?php
                        } 

                        if(empty($hide_rating)){
                            $product_rating = $product->get_average_rating($product->get_id());
                            $this->render_star_rating($product_rating, $index);
                        }
                        if(empty($hide_price) && !empty($product_price)){ ?>
                        <span class="eae-wp-price"> <?php echo $product_price ?> </span>
                        <?php }
                        $product_description = $product->get_description();
                        $product_expert = $product->get_short_description();
                        
                        if(empty($hide_description) && (!empty($product_description) || !empty($product_expert))){

                            if(empty($product_expert)){
                                $descpt = $product_description;
                            }
                            else{
                                $descpt = $product_expert;
                            }
                           
                        ?>  
                        <div class ='eae-wp-des'> 
                            <?php echo wp_trim_words( wc_format_content($descpt),$settings['word_limit'])?>
                        </div>
                        <?php } ?>
                    </div>
                    <div <?php echo $this->get_render_attribute_string('content_btn'); ?>>
                        <div class="eae-wp-content-button">
                        <?php
                        if(!empty($settings['content_button'])){
                            $this->render_buttons_html($settings,$product,$index,$product_url,'content', $wid);
                            $this->render_modal_box($product,$index,$wid); 
                        }
                        ?></div>
                    </div>
                </div>
        </div>
    
        <?php
    }

    public function render_preset2($settings,$product,$index, $wid){ 
        $product_name = $product->get_name();
        $product_price = $product->get_price_html();
        $product_url = $product->get_permalink();
        $product_image = $product->get_image();
        $hide_badges = isset($settings['hide_sale_badge']) ? $settings['hide_sale_badge'] : false;
        $hide_image = isset($settings['hide_img']) ? $settings['hide_img'] : false;
        $hide_title = isset($settings['hide_title']) ? $settings['hide_title'] : false;
        $hide_rating = isset($settings['hide_rat']) ? $settings['hide_rat'] : false;
        $hide_price = isset($settings['hide_price']) ? $settings['hide_price'] : false;
        $hide_description = isset($settings['hide_des']) ? $settings['hide_des'] : false;

        $this->set_render_attribute('media_btn', [    
            'class' => 'eae-wp-media-btn ' .$settings['hover_button'] ,
        ]);
        $this->set_render_attribute('content_btn', [    
            'class' => 'eae-wp-button eae-wp-btn-layout-' .$settings['content_layout'],
        ]);

        $this->add_render_attribute('list-item'.$index, 'class', 'eae-product-card');
        if($settings['layout'] == 'slider'){
            $this->add_render_attribute('list-item'.$index,'class','eae-swiper-slide swiper-slide');
        }
    
        ?>
       <div <?php echo $this->get_render_attribute_string('list-item'.$index); ?>>
            <div class="eae-wp-content-wrapper">
                    <?php
                    if(empty($hide_image)){
                    
                        ?>
                    <div class = 'eae-wp-image wp-media-btn'>
                        <span class="wp-img-overlay"></span>
                        <?php
                        if(!empty($settings['media_button'])){
                            ?>  <div <?php echo $this->get_render_attribute_string('media_btn') ?> > <?php 
                            $this->render_buttons_html($settings,$product,$index,$product_url,'media' , $wid);
                            ?> </div> <?php
                        }
                        echo $product_image;
                        if(empty($hide_badges)){
                            $this->render_badge($settings, $index, $product);
                        }?>
                        </div>
                        <?php } ?>
                    <div class='eae-wp-content'>
                        <div class = 'eae-wp-content-outer'>
                            <div class = 'eae-wp-content-inner'> <?php
                                if(empty( $hide_title)  && !empty($product_name)){ ?>
                                    <span class ='eae-wp-title' > <a href= <?php echo esc_url($product_url) ?>><?php echo $product_name ?></a></span>
                                <?php }
                                if(empty($hide_rating) ){
                                    $product_rating = $product->get_average_rating($product->get_id());
                                    $this->render_star_rating($product_rating, $index);
                                }
                                $product_description = $product->get_description();
                                $product_expert = $product->get_short_description();
                                
                                if(empty($hide_description) && ( !empty($product_description) || !empty($product_expert) )){
                                    if(empty($product_expert)){
                                        $descpt = $product_description;
                                    }
                                    else{
                                        $descpt = $product_expert;
                                    }
                                   ?>
                                        <div class ='eae-wp-des'> <?php  echo wp_trim_words( wc_format_content($descpt),$settings['word_limit']) ?></div><?php
                                    ?>
                                    
                                <?php } ?>
                            </div> <?php
                            if(empty($hide_price) && !empty($product_price) ){?>
                                <div class= 'eae-wp-price'>
                            <span class = 'eae-price'> <?php echo $product_price ?></span>
                                </div><?php
                            }
                            ?>  </div> <?php          
                            if(isset($settings['content_button'])){ ?>
                            <div class="eae-wp-content-button"><?php
                                    $this->render_buttons_html($settings,$product,$index,$product_url,'content', $wid);
                                    $this->render_modal_box($product,$index,$wid);
                                    ?>
                                </div>
                        <?php } ?>
                    </div>        
                </div>
            </div>
        <?php
    }

    public function render_cover_preset1($settings,$product,$index, $wid){
        
        $product_name = $product->get_name();
        $product_price = $product->get_price_html();
        $product_url = $product->get_permalink();
        $product_image = $product->get_image();
        $hide_badges = isset($settings['hide_sale_badge']) ? $settings['hide_sale_badge'] : false;
        $hide_image = isset($settings['hide_img']) ? $settings['hide_img'] : false;
        $hide_title = isset($settings['hide_title']) ? $settings['hide_title'] : false;
        $hide_rating = isset($settings['hide_rat']) ? $settings['hide_rat'] : false;
        $hide_price = isset($settings['hide_price']) ? $settings['hide_price'] : false;
        $hide_description = isset($settings['hide_des']) ? $settings['hide_des'] : false;

        $this->add_render_attribute('list-item'.$index, 'class', 'eae-product-card');

        if($settings['layout'] == 'slider'){
            $this->add_render_attribute('list-item'.$index,'class','eae-swiper-slide swiper-slide');
        }
        
        if($settings['content_hover'] == 'yes'){
            $this->add_render_attribute('list-item'.$index,'class','wp-hover-'.$settings['content_animation']);
            $this->add_render_attribute('list-item'.$index,'class','enable-hover-animation');
        }
    
        ?>
       <div <?php echo $this->get_render_attribute_string('list-item'.$index); ?>>
            <?php
            echo $product_image;
            if(empty($hide_badges) ){
                $this->render_badge($settings, $index, $product);
             }?>
                <div class = 'eae-wp-content'>
                    <div class = 'eae-wp-content-inner'>  <?php
                        if(empty($hide_title) && !empty($product_name)){ ?>
                            <span class = 'eae-wp-title' > <a href= <?php echo esc_url($product_url) ?>> <?php echo  $product_name ?></a></span> <?php
                        }
                        
                        if(empty($hide_rating)){
                            $product_rating = $product->get_average_rating($product->get_id());
                            $this->render_star_rating($product_rating, $index);  
                        }

                        if(empty( $hide_price) && !empty($product_price)){ ?>
                            <span class = 'eae-wp-price'><?php echo $product_price ?> </span> <?php
                        }

                        $product_description = $product->get_description();
                        $product_expert = $product->get_short_description();
                        
                        if(empty($hide_description) && ( !empty($product_description) || !empty($product_expert))){
                            if(empty($product_expert)){
                                $descpt = $product_description;
                            }
                            else{
                                $descpt = $product_expert;
                            }
                                ?>
                                <div class = 'eae-wp-des'><?php echo  wp_trim_words( wc_format_content($descpt),$settings['word_limit']) ?></div>
                                <?php
                        } ?>
                    </div> <?php
                    if(isset($settings['content_button']) && $settings['content_button'] != ''){ ?>
                        <div class="eae-wp-content-button"> <?php
                            $this->render_buttons_html($settings,$product,$index,$product_url,'content', $wid);
                            $this->render_modal_box($product,$index,$wid);
                        ?> </div><?php 
                    }?>
                </div> 
        </div> <?php        
    }

    public function render_cover_preset2($settings,$product,$index, $wid){
        $product_name = $product->get_name();
        $product_price = $product->get_price_html();
        $product_url = $product->get_permalink();
        $product_image = $product->get_image();

        $hide_badges = isset($settings['hide_sale_badge']) ? $settings['hide_sale_badge'] : false;
        $hide_image = isset($settings['hide_img']) ? $settings['hide_img'] : false;
        $hide_title = isset($settings['hide_title']) ? $settings['hide_title'] : false;
        $hide_rating = isset($settings['hide_rat']) ? $settings['hide_rat'] : false;
        $hide_price = isset($settings['hide_price']) ? $settings['hide_price'] : false;
        $hide_description = isset($settings['hide_des']) ? $settings['hide_des'] : false;
       
        $this->add_render_attribute('list-item'.$index, 'class', 'eae-product-card');
        if($settings['layout'] == 'slider'){
            $this->add_render_attribute('list-item'.$index,'class','eae-swiper-slide swiper-slide');
        }
    
        ?>
       <div <?php echo $this->get_render_attribute_string('list-item'.$index); ?>>
            <?php
            echo $product_image;
            if(empty($hide_badges) ){
              $this->render_badge($settings, $index, $product);
             }?>
            <div class = 'eae-wp-content'>
                <div class = 'eae-wp-content-inner'>  <?php
                    if(empty($hide_title) && !empty($product_name)){ ?>
                        <span class = 'eae-wp-title' > <a href= <?php echo esc_url($product_url) ?>> <?php echo  $product_name ?></a></span> <?php
                    }
                    
                    if(empty($hide_rating)){
                        $product_rating = $product->get_average_rating($product->get_id());
                        $this->render_star_rating($product_rating, $index);  
                    }

                    if(empty( $hide_price) && !empty($product_price)){ ?>
                        <span class = 'eae-wp-price'><?php echo $product_price ?> </span> <?php
                    }

                    $product_description = $product->get_description();
                    $product_expert = $product->get_short_description();
                    
                    if(empty($hide_description) &&( !empty($product_description) || !empty($product_expert))){
                        if(empty($product_expert)){
                            $descpt = $product_description;
                        }
                        else{
                            $descpt = $product_expert;
                        }
                            ?>
                            <div class = 'eae-wp-des'><?php echo  wp_trim_words( wc_format_content($descpt),$settings['word_limit']) ?></div>
                            <?php
                    } ?>
                    </div> <?php
                if(isset($settings['content_button']) && $settings['content_button'] != '' ){ ?>
                    <div class="eae-wp-content-button"> <?php
                        $this->render_buttons_html($settings,$product,$index,$product_url,'content', $wid);
                        $this->render_modal_box($product, $index, $wid);
                    ?> </div><?php 
                }?>
                </div> 
        </div> <?php   
    
    }
    public function render_star_rating($rating,$index){
        
        $fullStars = floor($rating);
        $halfStars = ($rating - $fullStars >= 0.5) ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStars;
        $settings = $this->get_settings_for_display();
        
        $full = '';
        $half ='';
        $empty = '';
        if(!empty($settings['filled_icon'])){
            $full = $settings['filled_icon'];
        }
        if(!empty($settings['half_filled_icon'])){
            $half =  $settings['half_filled_icon'];
        }
        if(!empty($settings['rating_filled_icon'])){
            $empty =  $settings['rating_filled_icon'];
        } ?>
        
        
            <span class='eae-wp-rating'><?php

                for($i = 0; $i < $fullStars; $i++){ ?>
                    <span class='woo-coo-star checked'> <?php  Icons_Manager::render_icon($full)  ?> </span><?php
                }
                if($halfStars === 1){ ?>
                    <span class='woo-coo-star half-checked'> <?php Icons_Manager::render_icon($half)  ?> </span><?php
                }
                for($i = 0; $i < $emptyStars; $i++){ ?>
                    <span class='woo-coo-star'> <?php  Icons_Manager::render_icon($empty)  ?> </span><?php
                } ?>
                
            </span> <?php 
    }

    public function render_badge($settings, $index, $product){
        $product_on_sale = $product->is_on_sale();
        $product_in_stock = $product->is_in_stock();
        add_shortcode('discount', [$this, 'get_sale_percentage']);
        add_shortcode('price_off',[$this, 'get_sale_off']);//add shortcode

        $hide_badges = isset($settings['hide_sale_badge']) ? $settings['hide_sale_badge'] : false;

        if(empty($hide_badges)){          

            $this->set_render_attribute("sale_tag", 'class', 'eae-wp-sale-tag eae-'.$settings['badge_layout']. ' eae-position-'.$settings['badge_position'] );
            
            if($product_on_sale === true  && $product_in_stock === true){ ?>
                <div <?php echo $this->get_render_attribute_string('sale_tag') ?> > <?php
                if(!empty($settings['sale_text'])){
                    $saleText = $settings['sale_text'];
                    $saleText = str_replace('{{', '[', $saleText);
                    $saleText =  str_replace('}}', ']', $saleText);
                    ?>
                    <div class = 'wp-sales'>  <?php echo do_shortcode($saleText) ?> </div> <?php
                } ?>
                </div> <?php
            }
            else{
                if($settings['exclude_out_of_stock'] != 'yes'){
                    if($settings['disable_stock_badge'] != 'yes'){
                        if($product_in_stock === false){ ?>
                            <div <?php echo $this->get_render_attribute_string('sale_tag') ?> > <?php
                            if(!empty($settings['stock_text'])){ ?>
                                <div class = 'wp-stock-out'> <?php echo Helper::eae_wp_kses($settings['stock_text']);  ?></div> <?php
                            } ?>
                           </div> <?php
                        }
                    }
                }   
            }    
        }
    }
    public function get_sale_percentage(){
        global $product;
            if($product->is_on_sale() && $product->is_in_stock()){
                $percentage = '';
                if($product->get_type() === 'variable'){
                    $variationPrice = $product->get_variation_prices();
                    
                    $max_percentage = 0;
                    foreach ( $variationPrice['regular_price'] as $key  => $regularPrice ){
                        $salePrice =  $variationPrice['sale_price'][$key];
                        if ( $salePrice < $regularPrice){
                            $percentage = round((($regularPrice - $salePrice) / $regularPrice) * 100);
                        }
                        if($percentage > $max_percentage){
                            $max_percentage = $percentage;
                        }
                        else{
                            $percentage = $max_percentage;
                        }
                    }
                    
                }
                elseif( $product->get_type() == 'simple' || $product->get_type() == 'external'){
                    $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                }
            }
            return $percentage;
    }
    public function get_sale_off(){
        global $product;
            if($product->is_on_sale() && $product->is_in_stock()){
                $difference = '';
                $currencySymbol = get_woocommerce_currency_symbol();
                if($product->get_type() === 'variable'){
                    $variationPrice = $product->get_variation_prices();
                    
                    $max_percentage = 0;
                    foreach ( $variationPrice['regular_price'] as $key  => $regularPrice ){
                        $salePrice =  $variationPrice['sale_price'][$key];
                        if ( $salePrice < $regularPrice){
                            $difference = round($regularPrice - $salePrice);
                        }
                        if($difference > $max_percentage){
                            $max_percentage = $difference;
                        }
                        else{
                            $difference = $max_percentage;
                        }
                    }
                    
                }
                elseif( $product->get_type() == 'simple' || $product->get_type() == 'external'){
                    $difference = round($product->get_regular_price() - $product->get_sale_price());
                }
            }
            return $currencySymbol . $difference;
    }

    public function render_modal_box($product,$index,$wid){
        $popupClass = ['eae-wp-modal-box woocommerce mfp-hide eae-wp-'.$wid.'-'.$product->get_ID() ,'mfp-hide'];


        $this->set_render_attribute('render_box', [    
            'class' =>$popupClass ,
            'id' => 'eae-wp-'.$wid.'-'.$product->get_ID(),
        ]);
        ?>
        <div <?php echo $this->get_render_attribute_string('render_box') ?> >         
         <?php echo $this->pop_up_data($product); ?>
        </div>
        <?php
        
        }
        
    public function pop_up_data($product){
        $settings = $this->get_settings_for_display();
        ?>
        <div class="eae-wp-popup-container product">
        <?php

        $columns = apply_filters('woocommerce_product_thumbnails_columns', 4);
        $post_thumbnail_id = get_post_thumbnail_id($product->get_ID());
        $full_size_image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
        $image_title = get_post_field('post_excerpt', $post_thumbnail_id);
        $placeholder = has_post_thumbnail($product->get_ID()) ? 'with-images' : 'without-images';
        $wrapper_classes = apply_filters(
            'woocommerce_single_product_image_gallery_classes',
            [
                'woocommerce-product-gallery',
                'woocommerce-product-gallery--' . $placeholder,
                'woocommerce-product-gallery--columns-' . absint($columns),
                'images',
            ]
        );

        ?>
        <div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>" data-columns=<?php echo esc_attr($columns);?>>

            <figure class="woocommerce-product-gallery__wrapper">
                <?php
                $data_src = '';
                $data_large_image = '';
                $data_large_image_width = '';
                $data_large_image_height = '';


                if (is_array($full_size_image)) {
                    $data_src = isset($full_size_image[0]) ? $full_size_image[0] : '';
                    $data_large_image = isset($full_size_image[0]) ? $full_size_image[0] : '';
                    $data_large_image_width = isset($full_size_image[1]) ? $full_size_image[1] : '';
                    $data_large_image_height = isset($full_size_image[2]) ? $full_size_image[2] : '';
                }

                $attributes = [
                    'title' => $image_title,
                    'data-src' => $data_src,
                    'data-large_image' => $data_large_image,
                    'data-large_image_width' => $data_large_image_width,
                    'data-large_image_height' => $data_large_image_height,
                ];
                
                if (has_post_thumbnail($product->get_ID())) {
                    $html  = '<div data-thumb="' . get_the_post_thumbnail_url($product->get_ID(), 'shop_thumbnail') . '" class="woocommerce-product-gallery__image"><a href="' . esc_url($data_src) . '">';
                    $html .= get_the_post_thumbnail($product->get_ID(), 'shop_single', $attributes);
                    $html .= '</a></div>';
                } else {
                    $html = '<div class="woocommerce-product-gallery__image--placeholder">';
                    $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src()), esc_html__('Awaiting product image', 'woocommerce'));
                    $html .= '</div>';
                }
                echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id($product->get_ID()));
                if($settings['enable_gallery'] == 'yes'){
                    do_action('woocommerce_product_thumbnails');
                }
                ?>
            </figure>
        </div>
        <div class="wp-popup-content">
            <h3 class="wp-popup-title"><?php echo $product->get_name(); ?></h3>
            <div class="wp-popup-sku"><?php echo $product->get_sku(); ?></div>
            <div class="wp-popup-price"><?php echo $product->get_price_html(); ?></div>
         
            <div class="wp-popup-desc"><?php echo $product->get_description(); ?></div>
            <div class="woocommerce eae-wp-popup-quantity">
                <?php
                do_action('woocommerce_' . $product->get_type() . '_add_to_cart');
                ?>
            </div>
            <div class="wp-popup-meta">

                <div class="wp-product_meta">

                    <?php do_action('woocommerce_product_meta_start'); ?>

                    <?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>

                        <span class="wp_sku_wrapper"><?php esc_html_e('SKU:', 'wpv-bu'); ?> <span class="sku"><?php echo ($sku = $product->get_sku()) ? $sku : esc_html__('N/A', 'wpv-bu'); ?></span></span>

                    <?php endif; ?>

                    <?php echo wc_get_product_category_list($product->get_id(), ', ', '<span class="wp_posted_in">' . _n('Category:', 'Categories:', count($product->get_category_ids()), 'wpv-bu') . ' ', '</span>'); ?>

                    <?php echo wc_get_product_tag_list($product->get_id(), ', ', '<span class="wp_tagged_as">' . _n('Tag:', 'Tags:', count($product->get_tag_ids()), 'wpv-bu') . ' ', '</span>'); ?>

                    <?php do_action('woocommerce_product_meta_end'); ?>

                </div>
            </div>
        </div>
        </div>   <?php
    }
    public function render_buttons_html($settings,$product,$index,$product_url,$type, $wid = null){
        // echo $wid;
        // die('dfaf');
        $product_url = $product->get_permalink();
        $settings = $this->get_settings_for_display();
        $items = $settings[$type];
        if(empty($items)){
            return;
        }
        $addToCartUrl = $product->add_to_cart_url();
        $product_url = $product->get_permalink();
        
       
        $product_id = $product->get_id();
        if ($product->is_type('variable') || $product->is_type('grouped')) {
            $addToCartUrl = $product_url;
        } elseif ($product->is_type('simple')) {
            $addToCartUrl = $product->add_to_cart_url(); 
        }

          foreach ( $items as $item ) {
            if($item[$type.'_action'] === 'add_to_cart'){
                        $this->set_render_attribute("cart-$type-$index",'class', 'eae-wp-icon eae-'.$type.'-icon');
                        $this->set_render_attribute("cart-$type-$index",'href', $addToCartUrl);
                        ?>
                       <a <?php echo $this->get_render_attribute_string("cart-$type-$index")  ?> > <?php
                        if(!empty($item[$type.'_title'])){
                           echo  Helper::eae_wp_kses($item[$type.'_title']);
                        }
                        if(!empty($item[$type.'_icon'])){
                            Icons_Manager::render_icon($item[$type.'_icon']);
                        } ?>
                       </a> <?php
            }
            if($item[$type.'_action'] === 'buy_now'){             
        
                if ($product->is_type('external') || $product->is_type('grouped') || $product->is_type('variable')) {
                    continue; // Skip rendering the "buy now" button 
                }
                $this->set_render_attribute('buy_now', [    
					'class' => 'eae-wp-buy-now eae-wp-icon' ,
					'data-product-id' => $product_id,
                    'data-quantity' => 1,
				]);
                ?>
                <a <?php echo $this->get_render_attribute_string('buy_now') ?> > <?php   
                        if(!empty($item[$type.'_title'])){
                           echo  Helper::eae_wp_kses($item[$type.'_title']);
                        }
                        if(!empty($item[$type.'_icon'])){
                            Icons_Manager::render_icon($item[$type.'_icon']);
                        } ?>
                </a>     
                <?php
            }
            if($item[$type.'_action'] === 'link'){
                        $this->set_render_attribute('link', [    
                            'class' => 'eae-wp-link eae-wp-icon' ,
                            'href' => $product_url,
                        ]); ?>

                        <a <?php echo  $this->get_render_attribute_string('link')  ?> >
                        <?php
                        if(!empty($item[$type.'_title'])){
                           echo Helper::eae_wp_kses($item[$type.'_title']);
                        }
                        if(!empty($item[$type.'_icon'])){
                            Icons_Manager::render_icon($item[$type.'_icon']);
                        }
                        ?>
                        </a>
                        <?php
            }

            if($item[$type.'_action'] === 'quick_view'){       
                $this->set_render_attribute('quickBtn-', [    
                    'class' => 'eae-wp-now eae-wp-icon open-popup-link id-'.$wid.'-'.$product->get_ID(),
                    'href' =>  '#eae-wp-'.$wid.'-'.$product->get_ID(),
                ]);             
            ?>
            <a  <?php echo $this->get_render_attribute_string('quickBtn-') ?> >
                <?php
                    if(!empty($item[$type.'_title'])){
                        echo Helper::eae_wp_kses($item[$type.'_title']);
                    }
                    if(!empty($item[$type.'_icon'])){
                        Icons_Manager::render_icon($item[$type.'_icon']);
                    }
                ?>
            </a> <?php
            }
        }
    }
}