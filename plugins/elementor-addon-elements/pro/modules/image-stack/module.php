<?php

namespace WTS_EAE\Pro\Modules\ImageStack;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use WTS_EAE\Base\Module_Base;
use WTS_EAE\Classes\Helper;

class Module extends Module_Base {
    
	public function __construct(){
        //add_filter('eae_stack_source_options', [$this, 'stack_source_options']);
        add_filter('eae_image_stack_add_pro_controls', [$this, 'image_stack_get_repeater_controls']);
        add_filter('eae_image_stack_data', [$this, 'image_stack_data'], 10, 2);
        add_action('eae_image_stack_render_pro', [$this, 'image_stack_render_pro'], 10, 3);
    }

    public function get_name()
    {
        return 'eae-image-stack-pro';
    }

    public function image_stack_render_pro($widget, $settings, $data)
    {
        foreach( $data as $key => $item){   
            $stackClasses =[];
            $stackClasses[]= 'img-stack-item';
            $stackClasses[]= 'eae-is-ct-'.$item['type'];
            if($settings['stack_source'] == 'repeater'){
                $key = $item['_id'];
            }else{
                $key = $key;
            }
    
            if($settings['stack_source'] !== 'repeater'){
                $widget->set_render_attribute('stackClasses-'.$key,'class',$stackClasses);
                if(isset($settings['tooltip']) && $settings['tooltip'] !=  'none'){
                    $toolText='';
                    $toolText = $widget->get_caption( $item);
                    $widget->add_render_attribute('stackClasses-'.$key,'tooltip', $toolText);
                    $widget->set_render_attribute('stackClasses-'.$key,'dir', $item['tooltip_position']);
                } 
            }

            if($settings['stack_source'] == 'repeater'){
                $stackClasses[] = 'elementor-repeater-item-'.$item['_id'];
                $widget->set_render_attribute('stackClasses-'.$key,'class',$stackClasses);
                if(isset($item['tooltip_text']) && $item['tooltip_text'] != ''){
                    $toolText= $item['tooltip_text'];
                    if(!empty($toolText)){
                        $widget->add_render_attribute('stackClasses-'.$key,'tooltip', $toolText);
                        $widget->set_render_attribute('stackClasses-'.$key,'dir', $item['tooltip_position']);
                    }
                } 
    
                if ( ! empty( $item['link']['url']) ) {
                    $widget->add_link_attributes( 'linkAll'.$key, $item['link'] );
                }
                // below code for repeater
            }else{
                $widget->set_render_attribute('stackClasses-'.$key,'class',$stackClasses);
                if(isset($settings['tooltip_sin']) && $settings['tooltip_sin'] !=  'none'){
                    $toolText='';
                    $toolText = $widget->get_caption($settings,$item);
                    if(!empty($toolText)){
                        $widget->add_render_attribute('stackClasses-'.$key,'tooltip', $toolText);
                        $widget->set_render_attribute('stackClasses-'.$key,'dir', $settings['tooltip_position_sin']);
                    }
                    
                } 
                
            }
    
            switch ( $item['type'] ) {
            
                case 'lottie-animation':
                    if(empty($item['lottie-data']['url'])){
                        break;
                    }
                    ?>   
                        <span <?php echo $widget->get_render_attribute_string( 'stackClasses-'.$key ); ?> >
                            <?php
                            $icon_class[] = 'eae-lottie-animation';
                            $icon_class[] = 'eae-lottie';
                                        
                            $widget->set_render_attribute('panel-icon', 'data-lottie-settings', wp_json_encode( $item['lottie-data'] ));
                            $widget->set_render_attribute('panel-icon', 'class', $icon_class);
                            if(! empty( $item['link']['url'])){?>
                                <a <?php echo $widget->get_render_attribute_string( 'linkAll'.$key ); ?>>
                            <?php }
                                ?> <div <?php echo $widget->get_render_attribute_string('panel-icon');?>></div><?php
                            if(! empty( $item['link']['url'])){?>
                                </a>
                            <?php }
                        ?>
                        </span>
                    <?php
                    break;
    
                case 'icon':
                    if($item['icon']['library'] != ''){?>   
                        <span <?php echo  $widget->get_render_attribute_string( 'stackClasses-'.$key ); ?> >
                            <?php
                        
                            if(! empty( $item['link']['url'])){?>
                                <a <?php echo $widget->get_render_attribute_string( 'linkAll'.$key ); ?>>
                            <?php }
                            Icons_Manager::render_icon( $item['icon']);
                            if(! empty( $item['link']['url'])){?>
                                </a>
                            <?php }
                        ?>
                        </span>
                      <?php
                    }
                    break;
                case 'image':
                    if(empty($item['imgId']) && $settings['placeholder'] != 'yes'){
                        break;
                    }
                    $attr = [
                        'class' => 'eae-img-stack'
                    ];
                    // $imgUrl = Utils::get_placeholder_image_src();
                    if( isset($item['link_control']) && $item['link_control'] != ''){
                        $link = $widget->get_link( $item); 
                        if ( $link ) {                   
                            $widget->add_link_attributes( 'link_'.$key, $link );
                            $widget->add_render_attribute( 'link_'.$key,[
                                'class' => 'elementor-clickable'.$key,
                            ]);
                            if ( $item['link_control'] != 'custom' ) {
                                $widget->add_lightbox_data_attributes( "link_".$key, $item['imgId'] );
                            }
                        } 
                    } 
                    ?>   
                    <span <?php echo $widget->get_render_attribute_string( 'stackClasses-'.$key ); ?> >
                        <?php
                            if(isset($item['link_control']) && $item['link_control'] != ''){?> 
                                <a <?php $widget->print_render_attribute_string( "link_".$key ); ?>> <?php
                            }
                            if(!empty($item['imgId'])){
                                echo  wp_get_attachment_image($item['imgId'], $settings['img_size'],false,$attr);
                            }else{
                                $imgUrl = Utils::get_placeholder_image_src();
                                echo "<img src='".esc_url($imgUrl)."' class='eae-img-stack' />";
                            }
                            if(isset($item['link_control']) && $item['link_control'] != ''){?> 
                                </a> <?php
                            }?>
                    </span>
                    <?php                
                    break;
                case 'text':
                    if(empty($item['text'])){
                        break;
                    }
                    ?>   
                    <span <?php echo $widget->get_render_attribute_string( 'stackClasses-'.$key ); ?> >
                        <?php
    
                        if(! empty( $item['link']['url'])){?>
                            <a <?php echo $widget->get_render_attribute_string( 'linkAll'.$key ); ?>>
                        <?php }
                            echo "<span class='img-stack-text'>";
                                echo Helper::eae_wp_kses($item['text']);
                            echo "</span>";
                        if(! empty( $item['link']['url'])){?>
                            </a>
                        <?php }
                     ?>
                     </span>
                     <?php
                    break;
            }
        }
    }

    public function image_stack_data($data, $settings){
        $type = 'image';
        $field = 'images';
        if($settings['stack_source'] == 'repeater'){
            $field = 'content_images';
        }else{
            return $data;
        }
        $data = [];
        foreach( $settings[$field] as $key => $item){
            if($settings['stack_source'] == 'repeater'){
                $type = $item['content_type'];
            }
            $data[$key]['type'] =  $type;
            $data[$key]['_id'] =   $item['_id'];
            switch ($type) {
                case 'image':
                    $size = $settings['img_size'];
                    if($settings['stack_source'] == 'repeater'){
                        $data[$key]['imgId']  = $item['content_image']['id'] ?? '';
                        $data[$key]['imgUrl'] = $item['content_image']['url'];  
                        if($item['content_link_control'] != 'none'){
                            $data[$key]['custom_link'] = $item['content_custom_link']; 
                            $data[$key]['link_control'] = $item['content_link_control']; 
                        }
                    }       
                    break;

                case 'icon':
                        $data[$key]['icon'] = $item['content_type_icon'];
                        $data[$key]['link'] = $item['content_custom_link_icon'];

                    break;

                case 'lottie-animation':
                    $lottie_data = [
                        'loop'    => ( $item['content_type_lottie_loop'] === 'yes' ) ? true : false,
                        'reverse' => ( $item['content_type_lottie_reverse'] === 'yes' ) ? true : false,
                    ];
                    if($item['content_type_lottie_option'] == 'media_file' && !empty($item['content_type_lottie_upload_json_file']['url'])){
                        $lottie_data['url'] = $item['content_type_lottie_upload_json_file']['url'];
                    }else{
                        $lottie_data['url'] = $item['content_type_lottie_animation_json'];
                    }   
                    $data[$key]['lottie-data']=$lottie_data;       
                    $data[$key]['link'] = $item['content_custom_link_lottie-animation'];     
                    break;
                case 'text':
                    $data[$key]['text']=$item['content_type_text'];
                    $data[$key]['link'] = $item['content_custom_link_text'];

                    break;       
            }
            if($settings['stack_source'] =='repeater'){
                if($item['tooltip']!=''){
                    $data[$key]['tooltip_text']= $item['tooltip'];
                    $data[$key]['tooltip_position']= $item['tooltip_position']; 
                }
            }else{
                if($settings['tooltip_sin']!='none'){
                    $data[$key]['tooltip']=$settings['tooltip_sin'];
                    $data[$key]['tooltip_text']=$settings['tooltip_cus'];
                    $data[$key]['tooltip_position']= $settings['tooltip_position_sin'];    
                } else{
                    $data[$key]['tooltip']='none';
                }
            }
        }
        return $data;
    }

    public function stack_source_options($options){
        unset($options['repeater']);
        $options['repeater'] = __('Repeater', 'wts-eae');
        return $options;
    }

    public function image_stack_get_repeater_controls($widget){
        $repeater = new Repeater();
        $repeater->add_control(
			'tab_title',
			[
				'label' => esc_html__( 'Title', 'wts-eae' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Element title', 'wts-eae' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

        $repeater->add_control(
			'content_type',
			[
				'label' => esc_html__( 'Type', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'options' => [
					'icon' => [
						'title' => esc_html__( 'Icon', 'wts-eae' ),
						'icon' => 'eicon-star',
					],
                    'lottie-animation' => [
						'title' => esc_html__( 'Lottie Animation', 'wts-eae' ),
						'icon' => 'eicon-lottie',
					],
					
					'image' => [
						'title' => esc_html__( 'Image', 'wts-eae' ),
						'icon' => 'eicon-image-bold',
					],
                    'text' => [
						'title' => esc_html__( 'Text', 'wts-eae' ),
						'icon' => 'eicon-animation-text',
					],
					
				],
                'default' => 'image',
			]
		);


        $repeater->add_control(
            'content_image',
            [
                'label' => esc_html__( 'Choose Image', 'wts-eae' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'  => ''
                ],
                'condition' => [
                    'content_type' =>'image'
                ]
            ]
		);
        $repeater->add_control(
			'content_type_lottie_option',
			[
				'label' => esc_html__( 'Source', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media_file',
				'options' => [
					'media_file' => esc_html__( 'Media File', 'wts-eae' ),
					'external_url' => esc_html__( 'External URL', 'wts-eae' ),
				],
                'condition' => [
                    'content_type' =>'lottie-animation'
                ]
			]
		);

       
        $repeater->add_control(
			'content_type_lottie_animation_json',
			[
				'label'       => __( 'Animation JSON URL', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
                'condition' => [
                    'content_type' =>'lottie-animation',
                    'content_type_lottie_option' =>'external_url',
                ]
			]
		);

        $repeater->add_control(
			'content_type_lottie_upload_json_file',
			[
				'label' => esc_html__( 'Upload JSON File', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'frontend_available' => true,
                'condition' => [
                    'content_type' =>'lottie-animation',
                    'content_type_lottie_option' =>'media_file',
                ]
				
			]
		);
        $repeater->add_control(
			'content_type_icon',
			[
				'label' => esc_html__( 'Icon', 'wta-eae' ),
				'type' => Controls_Manager::ICONS,				
				'skin' => 'inline',
				'label_block' => false,
                'condition' => [
                    'content_type' =>'icon'
                ],
                'default' => [
                    'value' => 'fas fa-bolt',
                    'library' => 'fa-solid',
                ],
			]
		);

        $repeater->add_control(
			'content_link_control',
			[
				'label' => esc_html__( 'Link', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => 'None',
                    'media' => 'Media File',
					'custom' => 'Custom URL',
				],
				'default' => 'none',
                'condition' => [
                    'content_type' =>'image'
                ]
			]
		);

        $repeater->add_control(
			'content_custom_link',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
				'condition' => [
					'content_link_control' => 'custom',
                    'content_type' =>'image'
				],
				'show_label' => false,
			]
		);

        $repeater->add_control(
			'content_lightbox_control',
			[
				'label' => esc_html__( 'Lightbox', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'condition' => [
                    'content_link_control' =>'media'          
                ]
			]
		);

       

        $repeater->add_control(
			'content_custom_link_icon',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
                'condition' => [
                    'content_type' =>'icon'
                ],
				
				
			]
		);

        $repeater->add_control(
			'content_type_text',
			[
				'label'			 => esc_html__( 'Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'label_block'	 => false,
                'condition' => [
                    'content_type' =>'text'
                ],
                'default'=>'Text',
			]
		);
        $repeater->add_control(
			'content_custom_link_text',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
                'condition' => [
                    'content_type' =>'text'
                ],
				
				
			]
		);
       
        $repeater->add_control(
			'content_custom_link_lottie-animation',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
                'condition' => [
                    'content_type' =>'lottie-animation'
                ],
				
				
			]
		);

        $repeater->add_control(
			'content_type_lottie_loop',
			[
				'label' => esc_html__( 'Loop', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'condition' => [
                    'content_type' =>'lottie-animation',                    
                ]
			]
		);

        $repeater->add_control(
			'content_type_lottie_reverse',
			[
				'label' => esc_html__( 'Reverse', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
                'condition' => [
                    'content_type' =>'lottie-animation',
                ]
			]
		);


        $repeater->add_control(
			'tooltip',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __( 'Tooltip', 'wta-eae' ),
				'placeholder' => __( 'Type title here', 'wta-eae' ),
                'separator'=>'before',
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'tooltip_position',
			[
				'label' => __( 'Tooltip Position', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => __( 'Left', 'wta-eae' ),
						'icon' => 'eicon-h-align-left'
					],
					'up'  => [
						'title' => __( 'Up', 'wta-eae' ),
						'icon' => 'eicon-v-align-top'
					],
					'down'  => [
						'title' => __( 'Down', 'wta-eae' ),
						'icon' => 'eicon-v-align-bottom'
					],
					'right'  => [
						'title' => __( 'Right', 'wta-eae' ),
						'icon' => 'eicon-h-align-right'
					],
				],
                'default'=>'up',
				'toggle' => true,
			]
		); 

        

        $repeater->add_control(
            'color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'separator'=>'before',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-text' => 'color: {{VALUE}};',  
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'types' => [ 'classic', 'gradient' ,'image'],
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-icon,{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-text,{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-lottie-animation,{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-image .eae-img-stack',
            ]
        );

        $repeater->add_control(
            'border_color',
            [
                'label' => esc_html__( 'Border Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-icon' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-text' => 'border-color: {{VALUE}};',  
                    '{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item.eae-is-ct-lottie-animation' => 'border-color: {{VALUE}};',  
                    '{{WRAPPER}} {{CURRENT_ITEM}}.img-stack-item .eae-img-stack' => 'border-color: {{VALUE}};',  
                ],
            ]
        );

        $widget->start_injection( [
			'of' => 'images',
		] );
        
        $widget->add_control(
            'content_images',
            [
                'label' => esc_html__( 'Items', 'wts-eae' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls() ,
                'default' => [
					[
                        'content_image'=>'',
						'tab_title'=>esc_html__( 'Item #1', 'wts-eae' ),


					],
                    [
                        'content_image'=>'',
						'tab_title'=>esc_html__( 'Item #2', 'wts-eae' ),
                     
                     
					],
                    [
                        'content_image'=>'',
						'tab_title'=>esc_html__( 'Item #3', 'wts-eae' ),
                       
					],
                    [
                        'content_image'=>'',
						'tab_title'=>esc_html__( 'Item #4', 'wts-eae' ),
                       
					],
				],
				'title_field' => '{{{ tab_title }}}',
                'condition'=>[
                    'stack_source'=>'repeater',
                ]

            ]
        );

        $widget->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Show Placeholder', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'condition' => [
                    'stack_source' =>'repeater',                    
                ]
			]
		);

        $widget->end_injection();
       
        $widget->start_injection( [
			'of' => 'item_size',
		] );

        $widget->add_responsive_control(
            'icon_size',
            [
                'label' => __( 'Icon Size', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 25,
                ],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-lottie-animation .eae-lottie-animation svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                ],
                'condition'=>[
                    'stack_source'=>'repeater',
                ]
            ]
        );

        $widget->end_injection();

    }
}
