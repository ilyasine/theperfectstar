<?php

defined( 'ABSPATH' ) || exit;  // Exit if accessed directly

    /**
     ** Start TPRM_membership_shortcode Class*
    */

    if( !class_exists("TPRM_membership_shortcode") ) {
        class TPRM_membership_shortcode{ 

            public $TPRM_subscriptionID;
            public $TPRM_buyID;
            
            function __construct(){
                add_shortcode('TPRM_license_box',[ $this, 'TPRM_license_box' ]);                           
                add_filter( 'the_content', [ $this, 'display_shortcode_on_subscription']);              
            }

            /**
             * Generate License and activation box shortcode
             *
             * @since V2
             * @access public
             * @return String
             */
            public function TPRM_license_box($atts, $content=null){

                $productID= isset($atts['productid']) ? $atts['productid'] : 16328;
                $subscriptionID = isset($atts['subid']) ? $atts['subid'] : 16328;
                $buyId= isset($atts['buyproductid']) ? $atts['buyproductid'] : 16328;
                $subscriptionID= intval($subscriptionID);
                $this->TPRM_subscriptionID = $subscriptionID;
                $this->TPRM_buyID = $buyId;
                $link=  get_the_permalink($buyId);
                $link = empty($link) ? '#' : $link; 
                $link = home_url('/?add-to-cart='.$buyId.'&quantity=1');

                if ( is_user_logged_in() && function_exists('is_subscription') && is_subscription() ) {

                    wp_enqueue_script('kmc-script' , TPRM_MEM_CO_JS .'kmc-script.js', [ 'jquery' ], time() );
                    wp_localize_script('kmc-script', 'TPRM_Data', [ 'ajaxUrl' => admin_url('admin-ajax.php') ] );
                    wp_enqueue_script('kmc-multilang-script', TPRM_MEM_CO_JS .'kmc-multilang.js', [ 'jquery' ], time() );                
                    wp_enqueue_style('license_box_style', TPRM_MEM_CO_CSS .'license_box.css' );                 

                    $kmc_shortcode_translation = array(
                        //Your subscription has been set up successfully
                        //Your account has been activated
                        'got_membership' =>__('Your account has been successfully activated !' , 'TPRM_-membership-coupon' ),
                        'creating' => __('Creating..' , 'TPRM_-membership-coupon' ),
                        'proccessing' => __('Proccessing..' , 'TPRM_-membership-coupon' ),
                        'empty_code' => __('Please fill in the required field' , 'TPRM_-membership-coupon' ),
                    );

                    // Localize the script with the new translation strings
                    wp_localize_script( 'kmc-script', 'kmc_translate', $kmc_shortcode_translation );

                    $kmc_lang = '';
                    if(is_page('subscription')){
                        $kmc_lang = 'en';
                    }elseif(is_page('abonnement')){
                        $kmc_lang = 'fr';
                    }

                    $json_lang_folder = TPRM_MEM_CO_JS . '/languages/' ;

                    wp_add_inline_script('kmc-multilang-script', 'var kmc_lang = ' . wp_json_encode($kmc_lang) . ';', 'before');
                    wp_add_inline_script('kmc-multilang-script', 'var json_lang_folder = ' . wp_json_encode($json_lang_folder) . ';', 'before');


                    ob_start();
                    include(TPRM_MEM_CO_DIR . "views/public/license_box.php" );
                    return ob_get_clean();
                }
            
            }

        
            /**
             * Display the generated shortcode on subscription pages
             *
             * @since V2
             * @access public
             * @return String
             */
            public function display_shortcode_on_subscription( $content ) {
                
                if ( function_exists('is_subscription') &&  is_subscription() ){


                    $kmc_helper = new TPRM_membership_helper();

                    $access_product_id = $kmc_helper->TPRM_multilang_product()[$kmc_helper->current_lang]['access'];

                    $license_product_id = $kmc_helper->TPRM_multilang_product()[$kmc_helper->current_lang]['license'];                 

                    $content = do_shortcode('[TPRM_license_box productid="' . $license_product_id .' " buyproductid="' . $access_product_id . ' "]');
              
                }

                return $content;
                
            }

        }
    } // End TPRM_membership_shortcode Class


    /**
     ** Start TPRM_add_to_cart_shortcode Class*
    */
    if( !class_exists("TPRM_add_to_cart_shortcode") ) {

        class TPRM_add_to_cart_shortcode{ 

            public $button_text;

            function __construct(){
                add_filter('woocommerce_product_single_add_to_cart_text',[ $this, 'change_add_to_cart_text']);
                add_shortcode('add_to_cart_form', [ $this, 'TPRM_add_to_cart_form_shortcode' ]);
            }

            /**
             * Display a single product with single-product/add-to-cart/$product_type.php template.
             *
             * @param array $atts Attributes.
             * @return string
             */
            public function TPRM_add_to_cart_form_shortcode( $atts ) {

                if ( empty( $atts ) ) {
                    return '';
                }

                if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) {
                    return '';
                }

                //$this->change_add_to_cart_text('Activate');

                $atts = shortcode_atts(
                    array(
                        'id'                => '',
                        'sku'               => '',
                        'text'               => __( 'Buy Now', 'woocommerce' ),
                        'status'            => 'publish',
                        'show_price'        => 'false',
                        'hide_quantity'     => 'true',
                        'allow_form_action' => 'false',
                        'align' => 'center',
                        'text_color' => '#ffffff', // Default text color
                        'background_color' => '#1c5cb2', // Default background color
                        'hover_text_color' => '#ffffff', // Text color on hover
                        'hover_background_color' => '#00A79D', // Background color on hover
                    ),
                    $atts,
                    'product_add_to_cart_form'
                );

                $query_args = array(
                    'posts_per_page'      => 1,
                    'post_type'           => 'product',
                    'post_status'         => $atts['status'],
                    'ignore_sticky_posts' => 1,
                    'no_found_rows'       => 1,
                );

                if ( ! empty( $atts['sku'] ) ) {
                    $query_args['meta_query'][] = array(
                        'key'     => '_sku',
                        'value'   => sanitize_text_field( $atts['sku'] ),
                        'compare' => '=',
                    );

                    $query_args['post_type'] = array( 'product', 'product_variation' );
                }

                if ( ! empty( $atts['id'] ) ) {
                    $query_args['p'] = absint( $atts['id'] );
                }

                // Hide quantity input if desired.
                if ( 'true' === $atts['hide_quantity'] ) {
                    add_filter( 'woocommerce_quantity_input_min', [ $this, 'TPRM_add_to_cart_form_return_one' ] );
                    add_filter( 'woocommerce_quantity_input_max', [ $this, 'TPRM_add_to_cart_form_return_one' ] );
                }

                // Change form action to avoid redirect.
                if ( 'false' === $atts[ 'allow_form_action' ] ) {
                    add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
                }

                $single_product = new WP_Query( $query_args );

                $preselected_id = '0';

                // Check if sku is a variation.
                if ( ! empty( $atts['sku'] ) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type ) {

                    $variation  = new WC_Product_Variation( $single_product->post->ID );
                    $attributes = $variation->get_attributes();

                    // Set preselected id to be used by JS to provide context.
                    $preselected_id = $single_product->post->ID;

                    // Get the parent product object.
                    $query_args = array(
                        'posts_per_page'      => 1,
                        'post_type'           => 'product',
                        'post_status'         => 'publish',
                        'ignore_sticky_posts' => 1,
                        'no_found_rows'       => 1,
                        'p'                   => $single_product->post->post_parent,
                    );

                    $single_product = new WP_Query( $query_args );
                    ?>
                    <script type="text/javascript">
                        jQuery( document ).ready( function( $ ) {
                            var $variations_form = $( '[data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>"]' ).find( 'form.variations_form' );

                            <?php foreach ( $attributes as $attr => $value ) { ?>
                                $variations_form.find( 'select[name="<?php echo esc_attr( $attr ); ?>"]' ).val( '<?php echo esc_js( $value ); ?>' );
                            <?php } ?>
                        });
                    </script>
                    <?php
                }

                // For "is_single" to always make load comments_template() for reviews.
                $single_product->is_single = true;

                ob_start();

                global $wp_query;

                // Backup query object so following loops think this is a product page.
                $previous_wp_query = $wp_query;
                // @codingStandardsIgnoreStart
                $wp_query          = $single_product;
                // @codingStandardsIgnoreEnd

                wp_enqueue_script( 'wc-single-product' );

                while ( $single_product->have_posts() ) {
                    $single_product->the_post();

                    ?>
                    <div class="product single-product add_to_cart_form_shortcode" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">

                        <?php
                        if ( wc_string_to_bool( $atts['show_price'] ) ) {
                            woocommerce_template_single_price();
                        }
                        $this->button_text = $atts['text'] ? esc_html( $atts['text'] ) : ''; // Use custom text if provided, otherwise use the default text from the WooCommerce settings.
                        woocommerce_template_single_add_to_cart( array(
                            'button_text' => $this->button_text,
                        ) ); ?> 
                    </div>
                    <?php
                }

                // Restore $previous_wp_query and reset post data.
                // @codingStandardsIgnoreStart
                $wp_query = $previous_wp_query;
                // @codingStandardsIgnoreEnd
                wp_reset_postdata();
            

                // Remove filters.
                if ( 'true' === $atts['hide_quantity'] ) {
                    remove_filter( 'woocommerce_quantity_input_min', [ $this, 'TPRM_add_to_cart_form_return_one' ] );
                    remove_filter( 'woocommerce_quantity_input_max', [ $this, 'TPRM_add_to_cart_form_return_one' ] );
                    echo '<style>.woocommerce div.product form.cart div.quantity {display: none ; }</style>';
                }
                // Edit styles later
                echo '
                <style>
                .woocommerce form {
                    display: flex;
                    align-items: center;
                ';
                // Switch-case to check the value of align
                switch ( $atts['align'] ) {
                    case 'center':
                        echo 'justify-content: center;';
                        break;
                    case 'right':
                        echo 'justify-content: flex-end;';
                        break;
                    case 'left':
                        echo 'justify-content: flex-start;';
                        break;
                    default:
                        echo 'justify-content: center;';
                        break;
                }
                echo '
                } 
               
               
                // Add the new CSS properties here
                .woocommerce div.product form.cart button.button.single_add_to_cart_button {
                    color: ' . $atts['text_color'] . ';
                    background-color: ' . $atts['background_color'] . '; 
                }
                .woocommerce div.product form.cart button.button.single_add_to_cart_button:hover ,
                .woocommerce div.product form.cart button.button.single_add_to_cart_button:active ,
                .woocommerce div.product form.cart button.button.single_add_to_cart_button:focus {
                    color: ' . $atts['hover_text_color'] . '; 
                    background-color: ' . $atts['hover_background_color'] . ';
                }
                </style>    
                ';

                /* 
                selector .woocommerce form {
                text-align: center;
                }
                selector .woocommerce form button.button{
                    float: none;
                    font-size: 17px;
                }
                selector .woocommerce form button.button:hover{
                    background-color: #00A79D;
                }
                */

                // skip and autofill checkout page


                if ( 'false' === $atts[ 'allow_form_action' ] ) {
                    remove_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
                }	

                return '<div class="woocommerce">' . ob_get_clean() . '</div>';
            }

            /**
             * Change add to cart text
             *
             * @since V2
             * @access public
             * @return String
             */
            public function change_add_to_cart_text($text) {

                $text = $this->button_text;
                
                return __( $text, 'TPRM_-membership-coupon' );
            }


            /**
             * Redirect to same page
             *
             * @return string
             */
            public function TPRM_add_to_cart_form_redirect( $url ) {
                return get_permalink();
            }

            /**
             * Return integer
             *
             * @return int
             */
            function TPRM_add_to_cart_form_return_one() {
                return 1;
            }
        }
    }// End TPRM_add_to_cart_shortcode Class

    