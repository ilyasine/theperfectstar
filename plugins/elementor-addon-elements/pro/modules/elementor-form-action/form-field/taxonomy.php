<?php 
    namespace WTS_EAE\PRO\Modules\ElementorFormAction;

use Aepro\Classes\SwiperMaster;
use ElementorPro\Modules\Forms\Fields\Field_Base;
    use Elementor\Controls_Manager;
    use ElementorPro\Plugin;
    use Elementor\Icons_Manager;

    if ( ! defined( 'ABSPATH' ) ) {
        exit; 
    }

    class Taxonomy_Field extends Field_Base{

		public $_term_list = [];
        
        public function get_type() {
            return 'taxonomy';
        }

        public function get_name() {
            return esc_html__( 'Taxonomy', 'wts-eae' );
        }

        public function update_controls($widget){

            $elementor = Plugin::elementor();

            $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');

            if( is_wp_error($control_data) ){
                return;
            }

            $group = []; 
            $post_types = get_post_types([],'objects');
            
            foreach($post_types as $key => $post_type){
                $taxonomy = [];
                $taxonomies = get_object_taxonomies($post_type->name, 'objects');
                foreach($taxonomies as $key => $taxonomy_data){
                    if($taxonomy_data->public){
                        $taxonomy[$taxonomy_data->name] = $taxonomy_data->label;
                    }
                }
                if(count($taxonomy) > 0){
                    $group[] = [
                        'label' =>  $post_type->label,
                        'options' => $taxonomy
                    ];
                }
            }
            
            
            // $group[] = [
            //     'label' => ' ',
            //     'options' => [
            //         ' ' => 'Select'
            //     ]
            // ];
            // echo "<pre>"; print_r($group); echo "</pre>";
            // die('fasdf');
            
            $field_controls = [
                'taxonomy_name' => [
                    'name' => 'taxonomy_name',
                    'label' => esc_html__( 'Select Taxonomy', 'wts-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'groups' => $group,
                    'condition' => [
                        'field_type' => $this->get_type(),
                    ],
                    'tab'          => 'content',
                    'inner_tab'    => 'form_fields_content_tab',
                    'tabs_wrapper' => 'form_fields_tabs',
                    'render_type' => 'template',
                ],

                'taxonomy_field_type' => [
                    'name' => 'taxonomy_field_type',
                    'label' => esc_html__('Select Field Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'select' => esc_html__('Select','wts-eae'),
                        'checkbox' => esc_html__('Checkbox','wts-eae'),
                        'radio' => esc_html__('Radio','wts-eae'),
                    ],
                    'default' => 'select',
                    // 'render_type' => 'template',
                    'condition' => [
                        'field_type' => $this->get_type(),
                    ],
                    'tab'          => 'content',
                    'inner_tab'    => 'form_fields_content_tab',
                    'tabs_wrapper' => 'form_fields_tabs',
                ],

                'checkbox_inline_list' => [
                    'name' => 'checkbox_inline_list',
                    'label' => esc_html__('Inline List','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'return_value' => 'elementor-subgroup-inline',
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'field_type',
                                'operator' => '===',
                                'value' => $this->get_type(),
                            ],
                            [
                                'name' => 'taxonomy_field_type',
                                'operator' => '!==',
                                'value' => 'select',
                            ],
                        ],
                    ],
                    'tab'          => 'content',
                    'inner_tab'    => 'form_fields_content_tab',
                    'tabs_wrapper' => 'form_fields_tabs',
                ],
                
                'term_multi_select' => [
                    'name' => 'term_multi_select',
                    'label' => esc_html__('Multiple Selection','wts-eae'),
                    'type' => Controls_Manager::SWITCHER,
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'field_type',
                                'operator' => '===',
                                'value' => $this->get_type(),
                            ],
                            [
                                'name' => 'taxonomy_field_type',
                                'operator' => '==',
                                'value' => 'select',
                            ],
                        ],
                    ],
                    'tab'          => 'content',
                    'inner_tab'    => 'form_fields_content_tab',
                    'tabs_wrapper' => 'form_fields_tabs',
                ],

                'multi_select_size' => [
                    'name' => 'multi_select_size',
                    'label' => esc_html__( 'Rows', 'elementor-pro' ),
                    'type' => Controls_Manager::NUMBER,
                    'min' => 2,
                    'step' => 1,
                    'conditions' => [
                        'relation' => 'and',
                        'terms' => [
                            [
                                'name' => 'field_type',
                                'operator' => '===',
                                'value' => $this->get_type(),
                            ],
                            [
                                'name' => 'taxonomy_field_type',
                                'operator' => '==',
                                'value' => 'select',
                            ],
                            [
                                'name' => 'term_multi_select',
                                'operator' => '!=',
                                'value' => '',
                            ],
                        ],
                    ],
                    'tab'          => 'content',
                    'inner_tab'    => 'form_fields_content_tab',
                    'tabs_wrapper' => 'form_fields_tabs',
                ]

            ];
            $control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		    $widget->update_control( 'form_fields', $control_data );
            
        }

        public function render( $item, $item_index, $form ) {

            $field_type = $item['taxonomy_field_type'];

			$taxonomy_terms = $this->get_terms_hierarchy($item['taxonomy_name']);

            if($item['taxonomy_name'] != ''){
                switch ($field_type) {
                    case 'select':
                        $this->render_field_type_select($taxonomy_terms, $item, $item_index, $form, $field_type);
                        break;
                    case 'checkbox':
                        $this->render_field_type_checkbox_radio($taxonomy_terms, $item, $item_index, $form, $field_type);
                        break;
                    case 'radio':
                        $this->render_field_type_checkbox_radio($taxonomy_terms, $item, $item_index, $form, $field_type);
                        break;
                }
            }
        }

		public function get_terms_hierarchy( $taxonomy, $parent = 0 ) {
			$args = array(
				'parent' => $parent,
				'hide_empty' => false,
				'taxonomy' => $taxonomy,
			);
			$terms = get_terms( $args );
			$term_list = array();
			foreach ( $terms as $term ) {
				$term_list[ $term->term_id ] = $term;
				$term_list[ $term->term_id ]->children = $this->get_terms_hierarchy( $taxonomy, $term->term_id );
			}
			return $term_list;
		}

		public function get_terms_hierarchy_list($terms, $depth = 0){
			foreach ( $terms as $term ) {
				
				$this->_term_list[ $term->taxonomy ][ $term->slug ] = str_repeat( '&nbsp;', $depth * 3 ) . $term->name;
				if ( ! empty( $term->children ) ) {
					$this->get_terms_hierarchy_list( $term->children, $depth + 1 );
				}
			}

			return $this->_term_list;
		}

		public function render_select_box( $terms, $form, $item, $field_type, $depth = 0) {
			foreach ( $terms as $term ) {
                            
				$form->set_render_attribute( 'term-' . $term->term_id , 'value', esc_attr( $term->slug ) );
				$form->set_render_attribute( 'term-' . $term->term_id , 'id', $item['custom_id'] . '-' . $field_type . '-' . $term->term_id );

				switch ($field_type) {
					case 'select':
						if ( ! empty( $item['field_value'] ) && ( $term->term_id == $item['field_value'] )) {
							$form->set_render_attribute( 'term-' . $term->term_id , 'selected', 'selected' );
						}
						$this->render_select_option($form, $term, $depth);
						break;
					case 'checkbox':
					case 'radio':
						$form->set_render_attribute( 'term-' . $term->term_id , 'type', $field_type );
						$form->set_render_attribute( 'term-' . $term->term_id , 'name', $form->get_attribute_name( $item ) . ( ( 'checkbox' === $field_type ) ? '[]' : '' ) );
						if ( ! empty( $item['field_value'] ) && ( $term->term_id == $item['field_value'] )) {
							$form->set_render_attribute( 'term-' . $term->term_id , 'checked', 'checked' );
						}

						if ( $item['required'] && 'radio' === $field_type ) {
							$form->add_render_attribute( 'term-' . $term->term_id, 'required', 'required' );
						}
						$this->render_checkbox_option($form, $term, $item, $field_type, $depth);
						break;
				}
				
				if ( ! empty( $term->children ) ) {
					$this->render_select_box( $term->children, $form, $item, $field_type, $depth + 1 );
				}
			}
		}

		public function render_select_option($form, $term, $depth ) {
			?>
			<option <?php $form->print_render_attribute_string( 'term-' . $term->term_id ); ?>>
				<?php echo str_repeat( '&nbsp;', $depth * 3 ) . esc_html( $term->name ); ?>
			</option>
			<?php
		}

		public function render_checkbox_option($form, $term, $item, $field_type, $depth ) {
			?>
			<span class="elementor-field-option">
				<?php echo str_repeat( '&nbsp;', $depth * 3 ); ?>
				<input <?php $form->print_render_attribute_string( 'term-' . $term->term_id ); ?> />
				<label for="<?php echo esc_attr( $item['custom_id'] . '-' . $field_type . '-' . $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></label>
			</span>
			<?php
		}

        public function render_field_type_checkbox_radio($terms, $item, $item_index, $form, $type){
            $options = $terms;
            $html = '';
            if ( $options ) {
				ob_start();
				?>
                <div class="eae-form-field elementor-field-subgroup <?php echo esc_attr( $item['css_classes'] ) . ' ' . esc_attr($item['checkbox_inline_list']);?>">
					<?php
					$this->render_select_box($terms, $form, $item, $type);
					?>
                </div>
				<?php
				$html = ob_get_clean();
				echo $html;
            }
        }

        public function render_field_type_select($terms, $item, $item_index, $form, $type){

			$select = '';
            if(count($terms) > 0 ){
                $form->add_render_attribute(
                    [
                        'select-wrapper' . $item_index => [
                            'class' => [
								'eae-form-field',
                                'elementor-field',
                                'elementor-select-wrapper',
                                'remove-before',
                                esc_attr( $item['css_classes'] ),
                            ],
                        ],
                        'select' . $item_index => [
                            'name' => $form->get_attribute_name( $item ) . ( ! empty( $item['term_multi_select'] ) ? '[]' : '' ),
                            'id' => $form->get_attribute_id( $item ),
                            'class' => [
                                'elementor-field-textual',
                                'elementor-size-' . $item['input_size'],
                            ],
                        ],
                    ]
                );
        
                if ( $item['required'] ) {
                    $form->add_render_attribute( 'select' . $item_index, 'required', 'required' );
                    // $form->add_required_attribute( 'select' . $item_index );
                }
        
                if ( $item['term_multi_select'] ) {
                    $form->add_render_attribute( 'select' . $item_index, 'multiple' );
                    if ( ! empty( $item['multi_select_size'] ) ) {
                        $form->add_render_attribute( 'select' . $item_index, 'size', $item['multi_select_size'] );
                    }
                }

                ob_start();
                ?>
                <div <?php $form->print_render_attribute_string( 'select-wrapper' . $item_index ); ?>>
					<?php
					if( ! $item['term_multi_select'] ){ ?>
                    <div class="select-caret-down-wrapper">
                        <?php
                        if ( ! $item['term_multi_select'] ) {
                            $icon = [
                                'library' => 'eicons',
                                'value' => 'eicon-caret-down',
                                'position' => 'right',
                            ];
                            Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
                        }
                        ?>
                    </div>
					<?php } ?>
                    <select <?php $form->print_render_attribute_string( 'select' . $item_index ); ?>>
                        <?php
						$this->render_select_box($terms, $form, $item, $type);
                    	 ?>
                    </select>
                </div>
                <?php

                $select = ob_get_clean();
                echo $select;
            }
        }

        public function __construct() {
            parent::__construct();
            add_action( 'elementor/preview/init', [ $this, 'editor_preview_footer' ] );
        }
    
        public function editor_preview_footer() {
            add_action( 'wp_footer', [ $this, 'content_template_script' ] );
        }    

        public function content_template_script(){
            $group = []; 
            $post_types = get_post_types([],'objects');
            
            foreach($post_types as $key => $post_type){
                $taxonomy = [];
                $taxonomies = get_object_taxonomies($post_type->name, 'objects');
                foreach($taxonomies as $key => $taxonomy_data){
                    if($taxonomy_data->public){
                        $taxonomy[$taxonomy_data->name] = $taxonomy_data->label;
                    }
                }

                if(count($taxonomy) > 0){
                    $group[] = [
                        'label' =>  $post_type->label,
                        'options' => $taxonomy
                    ];
                }
            }

            $taxonomy_terms = [];
			$taxonomy_terms1 = [];
			
            foreach($group as $key => $post_type){
                foreach($post_type['options'] as $taxonomy_name => $taxonomy_label){
                    $temp = [];
					$temp_obj = [];
                   
                    $temp_obj = $this->get_terms_hierarchy($taxonomy_name);
					
					$taxonomy_terms = $this->get_terms_hierarchy_list($temp_obj);

					foreach($temp_obj as $term){
						$temp[$term->slug] = $term->name;
					}
                }
            }

            ?>
                <script>
                    jQuery( document ).ready( () => {
                        elementor.hooks.addFilter('elementor_pro/forms/content_template/field/<?php echo $this->get_type(); ?>',
                            function (inputField, item, i){
                                const itemClasses =  item.css_classes;
                                const field_type = item.taxonomy_field_type;
                                const taxonomy_name = item.taxonomy_name;
                                var fieldGroupClasses = '';
                                // var fieldGroupClasses = 'elementor-field-group elementor-column elementor-field-type-' + item.field_type;
                                let terms = [];
                                taxonomy_terms = Object.entries(<?php echo json_encode($taxonomy_terms); ?>);
                                taxonomy_terms.forEach(function(term){
                                    if(term[0] == taxonomy_name){
                                        terms = term[1];
                                    }
                                });

                                required = '';
                                if ( item.required ) {
                                    required = 'required';
                                    fieldGroupClasses += ' elementor-field-required';

                                    if ( item.mark_required ) {
                                        fieldGroupClasses += ' elementor-mark-required';
                                    }
                                }

                                switch (field_type) {
                                    case 'select':
                                        multiple = '';
                                        if ( item.term_multi_select ) {
                                            multiple = ' multiple';
                                            fieldGroupClasses += ' elementor-field-type-' + item.field_type + '-multiple';
                                        }

                                        if ( terms ) {
                                            var size = '';
                                            if ( item.term_multi_select && item.multi_select_size ) {
                                                size = ' size="' + item.multi_select_size + '"';
                                            }
                                            inputField = '<div class="elementor-field elementor-select-wrapper ' + itemClasses + '">';
                                            inputField += '<select class="elementor-field-textual elementor-size-' + item.multi_select_size + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + multiple + size + ' >';
                                            for (const [value, label] of Object.entries(terms)) {
                                                var option_value = value;
                                                var option_label = label;
                                                var option_id = 'form_field_option' + i + value;

                                                inputField += '<option value =' + option_value + '>' + option_label + '</option>';
                                            }
                                            return inputField += '</select></div>';
                                        }
                                        break;
                                    case 'radio':
                                    case 'checkbox':
                                        
                                        if ( terms ) {
                                            
                                            var multiple = '';

                                            if ( 'checkbox' === field_type && terms.length > 1 ) {
                                                multiple = '[]';
                                            }

                                            inputField = '<div class="elementor-field-subgroup ' + itemClasses + ' ' + item.checkbox_inline_list + '">';

                                            for ( const [value, label] of Object.entries(terms) ) {
                                                var option_value = value;
												var space = '&nbsp;';
												var space_count = label.split(space).length - 1;
                                                var option_label = label.replaceAll(space, '')
                                                var option_id = 'form_field_' + field_type + i + value;

                                                field_data = [];

                                                field_data['value'] = option_value;
                                                field_data['type'] = field_type;
                                                field_data['id'] = 'form_field_' + i + '-' + value;
                                                field_data['name'] = 'form_field_' + i + multiple;

                                                inputField += '<span class="elementor-field-option">' + space.repeat(space_count) + '<input  value='+ option_value +' type='+field_type+'  id=form_field_' + i + '-' + value +' name=form_field_' + i + multiple+'' + required + '> ';
                                                inputField += '<label for="form_field_' + i + '-' + value + '">' + option_label + '</label></span>';
                                            }
                                            inputField += '</div>';

                                            return inputField;
                                        }
                                    break;
                                }
                            }, 10, 3
                        );
                    });
                </script>
            <?php
        }
    
    }

?>