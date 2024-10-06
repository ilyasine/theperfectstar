<?php 
    namespace WTS_EAE\Pro\Modules\ElementorFormAction;

    use Elementor\Controls_Manager;
    use Elementor\Repeater;
    use ElementorPro\Modules\Forms\Classes\Action_Base;

    if ( ! defined( 'ABSPATH' ) ) {
        exit; 
    }

    class Ping_Action_After_Submit extends Action_Base {

        public function get_name() {
            return 'eae-create-new-post';
        }

        public function get_label(){
            return esc_html__('Create New Post','wts-eae');
        }

        public function register_settings_section( $widget ) {
            $widget->start_controls_section(
                'post_section',
                [
                    'label' => esc_html__('Create New Post','wts-eae'),
                    'condition' => [
                        'submit_actions' => 'eae-create-new-post'
                    ]
                ]
            );

            $postType = get_post_types(['public' => true], 'objects');
            $post = [];
            foreach ($postType as $post_type) {
                $post[$post_type->name] = $post_type->label;
            }

            $widget->add_control(
                'post_type',
                [
                    'label' => esc_html__('Post Type','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $post,
                ]
            );

            $postStatus = get_post_statuses();

            $widget->add_control(
                'post_status',
                [
                    'label' => esc_html__('Post Status','wts-eae'),
                    'type' => Controls_Manager::SELECT,
                    'options' => $postStatus
                ]
            );

            $widget->add_control(
                'post_title',
                [
                    'label' => esc_html__('Post Title','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'placeholder' => esc_html__('Enter Form Field Shortcode','wts-eae'),
                ]
            );

            $widget->add_control(
                'post_content',
                [
                    'label' => esc_html__('Post Content','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'placeholder' => esc_html__('Enter Form Field Shortcode','wts-eae'),
                ]
            );

            $widget->add_control(
                'post_thumbnail',
                [
                    'label' => esc_html__('Post Thumbnail','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'placeholder' => esc_html__('Enter Form Field Shortcode','wts-eae'),
                ]
            );

            $post_category = new Repeater();

            $post_category->add_control(
                'post_category_slug',
                [
                    'label' => esc_html__('Category Slug','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Enter Form Field Shortcode','wts-eae'),
                    'label_block' => true,
                    'dynamic'     => [
                        'active' => true,
                    ],
                ]
            );

            $widget->add_control(
                'post_category',
                [
                    'label' => esc_html__('Post Categories','wts-eae'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $post_category->get_controls(),
                    'title_field' => '{{{ post_category_slug }}}',
                ]
            );

            $repeater = new Repeater();

            $repeater->add_control(
                'taxonomy_slug',
                [
                    'label' => esc_html__('Taxonomy Slug','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic'     => [
                        'active' => true,
                    ],
                    'label_block' => true
                ]
            );

            $repeater->add_control(
                'taxonomy_term',
                [
                    'label' => esc_html__('Term','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic'     => [
                        'active' => true,
                    ],
                    'label_block' => true
                ]
            );

            $widget->add_control(
                'post_taxonomy_repeater',
                [
                    'label' => esc_html__('Post Taxonomy','wts-eae'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $repeater->get_controls(),
                    'title_field' => '{{{ taxonomy_slug }}}',
                ]
            );

            $custom_fields_repeater = new Repeater();

            $custom_fields_repeater->add_control(
                'custom_field_name',
                [
                    'label' => esc_html__('Field Name','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'dynamic'     => [
                        'active' => true,
                    ],
                ]
            );

            $custom_fields_repeater->add_control(
                'custom_field_value',
                [
                    'label' => esc_html__('Field Value','wts-eae'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => esc_html__('Enter Form Field Shortcode','wts-eae'),
                    'label_block' => true,
                    'dynamic'     => [
                        'active' => true,
                    ],
                ]
            );

            $widget->add_control(
                'custom_fields_repeater',
                [
                    'label' => esc_html__('Custom Fields','wts-eae'),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $custom_fields_repeater->get_controls(),
                    'title_field' => '{{{ custom_field_name }}}',
                ]
            );

            $widget->end_controls_section();
        }

	    public function run( $record, $ajax_handler ) {
            //get data
            $settings = $record->get('form_settings');
            $field_data = $record->get_field(null);

            $post_title = ''; 
            $post_content = '';

            if($settings['post_title'] != ''){
                $post_title = $this->get_form_field_value($settings['post_title'],$record);
            }

            if($settings['post_content'] != ''){
                $post_content = $this->get_form_field_value($settings['post_content'],$record);
            }

            //Post Categories
            $post_categories = $settings['post_category'];
            $post_categories_ids = [];
			
            if($post_categories !== ''){
                foreach($post_categories as $key => $category){
                    if($category['post_category_slug'] != ''){
						$category_terms = array_map('trim', explode(',', $this->get_form_field_value($category['post_category_slug'],$record)));
                        //$post_categories_ids[$key] = get_category_by_slug($this->get_form_field_value($category['post_category_slug'],$record))->term_id;
						foreach($category_terms as $term){
							$term_data = get_term_by('slug',$term,'category');
							$post_categories_ids[] = $term_data->term_id;
						}
                    }
                }
            }

            // create new post
            $new_post_id = wp_insert_post(
                array(
                    'post_title' => $post_title,
                    'post_content' => $post_content,
                    'post_type' => $settings['post_type'],
                    //'meta_input' => $custom_field,
                    'post_category' => $post_categories_ids,
                    'post_status' => $settings['post_status'],
                )
            );

			//Custom Fields
            $custom_fields_data = $settings['custom_fields_repeater'];
            $custom_fields = [];
			$field_types = [];
			foreach($settings['form_fields'] as $field){
				$field_types['[field id="' . $field['custom_id'] . '"]']['field_type'] = $field['field_type'];
				$field_types['[field id="' . $field['custom_id'] . '"]']['allow_multiple_upload'] = $field['allow_multiple_upload'];
			}

            foreach($custom_fields_data as $data){
				if($field_types[$data['custom_field_value']]['field_type'] == 'upload'){
					$custom_fields[$data['custom_field_name']] = $this->upload_to_media($this->get_form_field_value($data['custom_field_value'],$record), $new_post_id);
					/* if($field_types[$data['custom_field_value']]['allow_multiple_upload'] == 'yes'){
						$uploaded_files = $this->get_form_field_value($data['custom_field_value'],$record);
						$file_array = explode(' , ', $uploaded_files);
						foreach($file_array as $file){
							$media_ids[] = $this->upload_to_media($file, $new_post_id);
						}
						$custom_fields[$data['custom_field_name']] = $media_ids;
					}else{
						$custom_fields[$data['custom_field_name']] = $this->upload_to_media($this->get_form_field_value($data['custom_field_value'],$record), $new_post_id);
					} */
				}else{
					$custom_fields[$data['custom_field_name']] = $this->get_form_field_value($data['custom_field_value'],$record);
				}
            }

			//Post Meta
			foreach($custom_fields as $key => $meta){
				$meta_key = $key;
				$meta_value = $meta;
				update_post_meta($new_post_id, $meta_key, $meta_value);
			}

            //Add Taxonomy data            
            $taxonomy_data = $settings['post_taxonomy_repeater'];
            $taxonomies = [];
            $taxonomy_term = [];
            foreach($taxonomy_data as $key => $data){
                $taxonomy_slug = $data['taxonomy_slug'];
                if($taxonomy_slug != ''){
                    $taxonomy_term = array_map('trim', explode(',', $this->get_form_field_value($data['taxonomy_term'],$record)));
                    foreach($taxonomy_term as $term){
                        $term_data = get_term_by('slug',$term,$taxonomy_slug);
                        $taxonomies[$taxonomy_slug][] = $term_data->term_id;
                    }
                }
            }
            
            foreach($taxonomies as $taxonomy => $taxonomy_term_id){
                wp_set_post_terms($new_post_id, $taxonomy_term_id , $taxonomy);
            }

            //Post Thumbnail
            $post_thumbnail_url = $this->get_form_field_value($settings['post_thumbnail'],$record);
            $post_thumbnail_id = $this->upload_to_media($post_thumbnail_url, $new_post_id);
			;
            

            //Post Thumbnail added
            if($new_post_id && $post_thumbnail_id){
                set_post_thumbnail($new_post_id, $post_thumbnail_id);
            }
        }

		public function upload_to_media($url, $post_id = 0){
			if(!$url){
				return '';
			}

			$file_array             = [];
			$file_array['name']     = basename( $url );
			$file_array['tmp_name'] = download_url( $url );
			$desc = '';

			// "Upload" to the media collection
			$new_attachment_id = media_handle_sideload( $file_array, $post_id, $desc );

			if ( is_wp_error( $new_attachment_id ) ) {
				@unlink( $file_array['tmp_name'] );
			}
			return $new_attachment_id;
			
		}

		public function get_saved_image_id($image_url){
			$post_thumbnail_id = '';
			if($image_url != ''){

                $file_name = basename($image_url);
                
                // remove space from name
                $file_name = str_replace(' ', '', $file_name); 

                $dir_arr = wp_upload_dir();
                $upload_folder_path = $dir_arr['basedir'];
                $file_path = $upload_folder_path . '/elementor/forms/' . $file_name;
                
                if(file_exists($file_path)){ 
                    $file_content = file_get_contents($file_path);
                    $uploaded_file_data = wp_upload_bits($file_name, null, $file_content);

                    if (!$uploaded_file_data['error']) {
                        $file_path = $uploaded_file_data['file']; 
                        $file_type = wp_check_filetype(basename($file_path), null);
                        
                        $uploaded_file_id = wp_insert_attachment( 
                            array(
                                'guid'           => $uploaded_file_data['url'],
                                'post_mime_type' => $file_type['type'],
                                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file_path)),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                            ),
                            $file_path
                        );

						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						$attach_data = wp_generate_attachment_metadata( $uploaded_file_id, $file_path );
						wp_update_attachment_metadata( $uploaded_file_id, $attach_data );
                        
                        $post_thumbnail_id = $uploaded_file_id;
                    }
                } 
            }
			return $post_thumbnail_id;
		}

        public function get_form_field_value($shortcodes,$record){
            return $record->replace_setting_shortcodes($shortcodes);
        }

	    public function on_export( $element ) {
            // unset(
            //     $element['icon']
            // );
            // return $element;
        }
    }
?>