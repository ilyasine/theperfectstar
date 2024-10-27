<?php

defined( 'ABSPATH' ) || exit;  // Exit if accessed directly

/* if(  is_plugin_active( 'woocommerce-smart-coupons/woocommerce-smart-coupons.php' ) ){
    require_once WP_PLUGIN_DIR . '/woocommerce-smart-coupons/includes/class-wc-sc-purchase-credit.php';    
}
 */

/**
 ** Start TPRM_membership_coupon Main Class *
 */



 if( !class_exists("TPRM_membership_coupon") ) {

    class TPRM_membership_coupon{ 

        public $printcoupon;
        public $printpdfcoupon;
        public $helptextprint;
        private static $instance = null;
        
        function __construct(){            
            add_action('wp_ajax_license_check', array( $this,'check_license'));
            add_action('wp_ajax_license_buy', array( $this,'license_buy'));
            add_action('TPRM_thankyou',array( $this,'success_message_after_payment') );
            add_filter('woocommerce_add_to_cart_redirect', array(  $this, 'TPRM_add_to_cart_redirect'));
            add_filter('woocommerce_checkout_fields', array($this, 'TPRM_custom_checkout_fields'), 20);
            add_action('init',array($this , 'remove_send_coupon_box'));                  
            add_action('plugins_loaded', array( $this, 'TPRM_membership_coupon_translation'));
            add_filter('TPRM_coupon_print',array( $this,'TPRM_print_coupon'), 10 , 1);
            add_filter('TPRM_pdf_coupon_print',array( $this,'TPRM_print_pdf_coupon'), 10 , 1);
            add_filter('help_text_print', array( $this, 'TPRM_help_text_print_coupon'), 10 , 1);
            add_filter('woocommerce_add_to_cart_validation', array( $this, 'empty_cart_before_add_new_item') );               
            add_action('woocommerce_checkout_update_order_meta', array($this, 'save_school_details_to_order_meta'), 10, 2);         
            //add_action('woocommerce_admin_order_data_after_order_details', array($this, 'display_order_school_details_on_admin'));
        }   

        /**
		 * Do Global checks of TPRM_membership_coupon class
		 *
         * @since V2
         * @access public
		 * @return TPRM_membership_coupon Singleton object of TPRM_membership_coupon
		 */

		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


        /**
         * load plugin text domain
         *
         * @since V2
         * @access public
         * 
         */

         public function TPRM_membership_coupon_translation() {

            load_plugin_textdomain( 'TPRM_-membership-coupon', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        }

        /**
         * Remove coupon box from checkout page
         *
         * @since V2
         * @access public
         * 
         */

        public function remove_send_coupon_box() {
            if (class_exists('WC_SC_Purchase_Credit')) {
                // Get the instance of MyClass
                $WC_SC_class = WC_SC_Purchase_Credit::get_instance();
        
                // Remove the action hook
                remove_action('woocommerce_checkout_after_customer_details', array($WC_SC_class, 'gift_certificate_receiver_detail_form'));
            }
        }

        /**
         * Empty Woocommerce Cart before adding new item
         *
         * @since V2
         * @access public
         * @return Boolean
         */
        
        function empty_cart_before_add_new_item( $cart_item_data ) {
        
            global $woocommerce;
            $woocommerce->cart->empty_cart();
        
            // Do nothing with the data and return
            return true;
        }
        
        /**
         * Remove checkout fields
         *
         * @since V2
         * @access public
         * @return Array
         */

        public function TPRM_custom_checkout_fields($fields) {
            // Remove billing fields
            unset($fields['billing']['billing_country']);
            unset($fields['billing']['billing_state']);
            unset($fields['billing']['billing_city']);
            unset($fields['billing']['billing_postcode']);
            unset($fields['billing']['billing_address_1']);
            unset($fields['billing']['billing_address_2']);
            unset($fields['billing']['billing_company']);
            //unset($fields['billing']['billing_phone']);

            // Remove shipping fields
            unset($fields['shipping']['shipping_country']);
            unset($fields['shipping']['shipping_state']);
            unset($fields['shipping']['shipping_city']);
            unset($fields['shipping']['shipping_postcode']);
            unset($fields['shipping']['shipping_address_1']);
            unset($fields['shipping']['shipping_address_2']);
           
            //Add ecole 
            $fields['billing']['ecole'] = array(
                'label' => __('School Name' , 'TPRM_-membership-coupon' ),
                'placeholder' => __('Enter your School Name' , 'TPRM_-membership-coupon' ),
                'required' => true,
                'clear' => true,
                'type' => 'text'
            );

            //Add class
            $fields['billing']['classe'] = array(
                'label' => __('Class Name' , 'TPRM_-membership-coupon' ),
                'placeholder' => __('Enter your Class Name' , 'TPRM_-membership-coupon' ),
                'required' => false,
                'clear' => true,
                'type' => 'text'
            );

            return $fields;
        }

        /**
         * Redirect to checkout page when the 'Buy Now' button is clicked
         *
         * @since V2
         * @access public
         * @return String
         */

        public function TPRM_add_to_cart_redirect() {
            return wc_get_checkout_url();
        }

        /**
         * Print Coupon after payment success in the order thank you page
         *
         * @since V2
         * @access public
         * 
         */

        public function success_message_after_payment($order_id){
          
            // Get the WC_Order Object
            $order = wc_get_order( $order_id );
            $item_sku = array();

            global $printcoupon;
        
            if ( $order->has_status('processing') || $order->has_status('completed') ){
                        
                $coupons = get_post_meta( $order_id,'sc_coupon_receiver_details',true);
                $coupons = !empty($coupons ) ? $coupons : [];
                
                $printcoupon = '';
                $printpdfcoupon = '';
                $helptextprint = '';

                if(count($coupons) > 0){

                    wp_enqueue_style('save-coupon-style', TPRM_MEM_CO_CSS .'coupon.css' );  
                    wp_enqueue_script('kmc_jspdf', TPRM_MEM_CO_JS . 'jspdf.umd.min.js' , array(), TPRM_MEM_CO_VERSION, true);
                    wp_enqueue_script('kmc_html2canvas', TPRM_MEM_CO_JS . 'html2canvas.min.js' );        
                    wp_enqueue_script('kmc_jspdf_roboto_font', TPRM_MEM_CO_JS . 'Roboto-Black-normal.js' , array('kmc_jspdf') );                  
                    wp_enqueue_script('save-coupon-script', TPRM_MEM_CO_JS .'coupon.js', array('kmc_jspdf','kmc_html2canvas','kmc_jspdf_roboto_font'), TPRM_MEM_CO_VERSION, true);             
            
                    $translation_array = array(
                        'cp_code' =>__('Copy Code' , 'TPRM_-membership-coupon' ),
                        'copied' => __('Copied' , 'TPRM_-membership-coupon' ),
                    );
        
                    // Localize the script with new data
                    wp_localize_script( 'save-coupon-script', 'kmc_translate', $translation_array );
                    
                    foreach($coupons as $index => $coupon){               
                        
                        wp_localize_script( 'save-coupon-script', 'kmc_coupon', array($coupon['code']) );
                        wp_localize_script( 'save-coupon-script', 'TPRM_icon', array(TPRM_icon) );
            
                        foreach ($order->get_items() as $item) {       
                            $product = wc_get_product($item->get_product_id());
                            $item_sku = $product->get_sku();
                            $coupon_img_path = TPRM_MEM_CO_IMG  . $item_sku .'.png';
                            $howto_img_path = TPRM_MEM_CO_IMG  . $item_sku .'-howto.png';

                            $activation_url = '';

                            if ($item_sku === 'license-en') {
                                $activation_url = site_url('/en/subscription/');
                            } elseif ($item_sku === 'license-fr') {
                                $activation_url = site_url('/fr/abonnement/');
                            }



                            $printpdfcoupon .= ' <div class="coupon-container" id="coupon-container-empty" >';
                            $printpdfcoupon .= '   <div class="coupon-container-inner" >';
                            $printpdfcoupon .= '       <h2>' . __('Congratulations on your purchase !' , 'TPRM_-membership-coupon' ) . '</h2>';
                            $printpdfcoupon .= '       <p>' . __('Here is your License code :' , 'TPRM_-membership-coupon' ) . '</p>';                
                            $printpdfcoupon .= '       <div class="code-card" style="background-image: url(' . $coupon_img_path . ')">';
                            $printpdfcoupon .= '           <div class="code" style=" width: 75%; height: 80px;"></div>';
                            $printpdfcoupon .= '           <div class="sku-code">  SKU: '. $item_sku .' </div>';
                            $printpdfcoupon .= '       </div>';
                            $printpdfcoupon .= '       <div class="code-card howto" style="background-image: url(' . $howto_img_path . ')">';     
                            $printpdfcoupon .= '       </div>';
                            $printpdfcoupon .= '   </div>';
                            $printpdfcoupon .= ' </div>';

                    
                            $printcoupon .= '<div class="coupon-container" >';
                            $printcoupon .= '   <div class="coupon-container-inner" >';
                            $printcoupon .= '       <h2>' . __('Congratulations on your purchase!' , 'TPRM_-membership-coupon' ) . '</h2>';
                            $printcoupon .= '       <p>' . __('Your Activation Code download will start soon. In case the download does not begin, please click on this button to initiate the download again.' , 'TPRM_-membership-coupon' ) . '</p>';                
                            $printcoupon .= '      <a href="#/" class="savecouponbtn">' . __('Download my Activation Code again' , 'TPRM_-membership-coupon' ) . '</a>';              
                            $printcoupon .= '       <p class="you-lic-code">' . __('Here is your License code :' , 'TPRM_-membership-coupon' ) . '</p>';                
                            $printcoupon .= '       <div class="code-card" style="background-image: url(' . TPRM_MEM_CO_IMG  . $item_sku .'.png)">';
                            $printcoupon .= '           <div class="code">' . $coupon['code'] . '</div>'; 
                            $printcoupon .= '           <div class="sku-code">  SKU: '. $item_sku .' </div>';
                            $printcoupon .= '       </div>';                    
                            $printcoupon .= '   </div>';                        
                            $printcoupon .= '    <a href="#/" class="cpnbtn">' . __('Copy Code' , 'TPRM_-membership-coupon' ) . '</a>';
                            $printcoupon .= '   <div class="coupon-explain" >';
                            $printcoupon .= '       <p>' . __('You can also copy the code and keep it safe to activate access to the platform tepunareomaori.com.' , 'TPRM_-membership-coupon' ) . '</p>'; 
                            $printcoupon .= '       <p>' . __('This code is strictly personal and confidential and should not be communicated or shared with third parties.' , 'TPRM_-membership-coupon' ) . '</p>'; 
                            $printcoupon .= '       <p>' . __('An email containing the activation code has also been sent to the email specified during the order.' , 'TPRM_-membership-coupon' ) . '</p>'; 
                            $printcoupon .= '       <p class="next-step">' . __('Next Step : ' , 'TPRM_-membership-coupon' ) . '</p>'; 
                            $printcoupon .= '       <p>' . __('To activate your account, kindly copy the license code and navigate to the following ' , 'TPRM_-membership-coupon' ) ; 
                            if (!empty($activation_url)) {
                            $printcoupon .= '       <a href="' . esc_url($activation_url) . '" target="_blank" class="sub-page">' . __('Activation Page', 'TPRM_-membership-coupon') . '</a>. <br>';
                            }
                            $printcoupon .=         __('Once there, please paste the code into the corresponding field provided and activate your account.' , 'TPRM_-membership-coupon' ) . '</p>'; 
                            $printcoupon .= '   </div>';
                            $printcoupon .= '</div>';

                        }
                    
                    }

                }     

            }

            $this->printcoupon = $printcoupon;

            $this->printpdfcoupon = $printpdfcoupon;

        }

        /**
         * Return printed Coupon on thank you page to use in a filter
         *
         * @since V2
         * @access public
         * @return String
         */

        public function TPRM_print_coupon($printcoupon){
            
            $printcoupon = $this->printcoupon;

            return $printcoupon;
        }
        
        /**
         * Return printed Coupon on pdf to use in a filter
         *
         * @since V2
         * @access public
         * @return String
         */

        public function TPRM_print_pdf_coupon($printpdfcoupon){
            
            $printpdfcoupon = $this->printpdfcoupon;

            return $printpdfcoupon;
        }

        /**
         * Purshase The Acces product when the license is valid
         *
         * @since V2
         * @access public
         * @return Void
         */

         public function license_buy() {
            $is_students_page = isset($_REQUEST['is_students_page']) && $_REQUEST['is_students_page'] === 'true';
            // Check if all required fields are set
            if (!isset($_REQUEST['code']) || empty($_REQUEST['code'])) {
                wp_send_json_error(['msg' => __('Please fill in the required field', 'TPRM_-membership-coupon')]);
            }
        
            if (!isset($_REQUEST['prid']) || empty($_REQUEST['prid'])) {
                wp_send_json_error(['msg' => __('There is a problem with this license code', 'TPRM_-membership-coupon')]);
            }
        
            if (!isset($_REQUEST['user_id']) || empty($_REQUEST['user_id'])) {
                wp_send_json_error(['msg' => __('Required student ID!', 'TPRM_-membership-coupon')]);
            }
        
            // Get coupon text and validate
            $couponText = sanitize_text_field($_REQUEST['code']);
            if (!wc_get_coupon_id_by_code($couponText)) {
                wp_send_json_error(['msg' =>__('Invalid license code','TPRM_-membership-coupon') ]);
            }
        
            $couponObj = new \WC_Coupon($couponText);
            $productId = sanitize_text_field($_REQUEST['prid']);
            $product = wc_get_product($productId);
        
            if (!$product) {
                wp_send_json_error(['msg' => __('Product ID is incorrect', 'TPRM_-membership-coupon')]);
            }
        
            if (!$couponObj->is_valid_for_product($product)) {
                wp_send_json_error(['msg' => __('This license code is not valid for the membership type associated with your account', 'TPRM_-membership-coupon')]);
            }
        
            // Validate student user
            $student_id = intval($_REQUEST['user_id']);
            $student_user = get_user_by('id', $student_id);
            $student_name = bp_core_get_user_displayname($student_id);
        
            if (!$student_user || !is_student_user($student_id)) {
                wp_send_json_error(['msg' => __('Invalid student', 'TPRM_-membership-coupon')]);
            }
        
            if (in_array($student_id, $couponObj->get_used_by()) || $couponObj->get_usage_count() >= $couponObj->get_usage_limit()) {
                wp_send_json_error(['msg' => __('License code already associated with an account', 'TPRM_-membership-coupon')]);
            }
        
            if (function_exists('is_students_page') && is_students_page()) {
                if ($couponObj->get_usage_limit() == 1) {
                    wp_send_json_error(['msg' => __("You don't have enough seats", 'TPRM_-membership-coupon')]);
                }
        
                if ($couponObj->get_usage_count() >= $couponObj->get_usage_limit()) {
                    wp_send_json_error(['msg' => __('You have used all the available seats. You can no longer activate student accounts. Please contact kiaora@tepunareomaori.co.nz to purchase more seats for your students.', 'TPRM_-membership-coupon')]);
                }
            }
        
            // Handle email restrictions
            if (!in_array($student_user->user_email, $couponObj->get_email_restrictions())) {
                $couponObj->set_email_restrictions([]);
                $couponObj->save();
            }
        
            // Create order for student
            WC()->cart->empty_cart();
            WC()->cart->add_to_cart($productId, 1);
            WC()->cart->apply_coupon($couponText);
            WC()->cart->calculate_totals();
        
            $totalPay = (float)WC()->cart->get_total();
        
            // Check if the coupon allows for full payment
            if ($totalPay > 0) {
                WC()->cart->empty_cart();
                wp_send_json_error(['msg' => __('There is a problem with this license code', 'TPRM_-membership-coupon')]);
            }
        
            // Create the order
            $checkout = WC()->checkout();
            $order_id = $checkout->create_order([]);
            $order = wc_get_order($order_id);
            update_post_meta($order_id, '_customer_user', $student_id);
            $order->calculate_totals();
            $order->payment_complete();
            $order->update_status('completed');
        
            WC()->cart->empty_cart();
        
            // Display a different message if it's a student page
            if ($is_students_page) {
                $message = sprintf(__('%s\'s account has been successfully activated !', 'TPRM_-membership-coupon'), $student_name);
            } else {
                $message = __('Your tepunareomaori account has been successfully activated !', 'TPRM_-membership-coupon');
            }

            wp_send_json_success([
                'id' => $order_id,
                'msg' => $message,
            ]);
        }       
        

        /**
         * check license validity
         *
         * @since V2
         * @access public
         * 
         */

        public function check_license(){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            //Send_json_error if code is empty
            if(!isset($_REQUEST['code']) && empty($_REQUEST['code']) ){
                wp_send_json_error(['msg' =>__( 'Please fill in the required field' ,'TPRM_-membership-coupon')]);
                exit();
            }
            //product id 
            if(!isset($_REQUEST['prid']) && empty($_REQUEST['prid']) ){
                wp_send_json_error(['msg' =>__('There is a problem with this license code','TPRM_-membership-coupon') ]);
                exit();
            } 

            //coupon checking
            $couponText= sanitize_text_field($_REQUEST['code']);
            if(!wc_get_coupon_id_by_code($couponText)){
                wp_send_json_error(['msg' =>__('Invalid license code','TPRM_-membership-coupon') ]);
                exit();
            }

            //coupon object
            $couponObj = new \WC_Coupon ($couponText);
            
            //for particular product id        
            $productId=  ($_REQUEST['prid']);

            $product = wc_get_product( $productId );
            // product existance
            if( !($product )){
                wp_send_json_error(['msg' =>__('Wrong with id','TPRM_-membership-coupon') ]);
                exit();
            }
            
            //validation for that product
            if(!$couponObj->is_valid_for_product($product  )){
                //var_dump($couponObj);
                wp_send_json_error(['msg' =>__('This license code is not valid for the membership type associated with your account','TPRM_-membership-coupon')  ,'d' => $product , $_REQUEST]);
                exit();
            }
            
            //validating user
            $id_user = get_current_user_id();
            $current_user = wp_get_current_user();
            $email= $current_user->user_email;
            if(in_array($id_user , $couponObj->get_used_by())){
                wp_send_json_error(['msg' =>__('License code already associated with an account','TPRM_-membership-coupon') ]);
                exit();
            }

            if($couponObj->get_usage_count() >= $couponObj->get_usage_limit()){
                wp_send_json_error(['msg' =>__('License code already associated with an account','TPRM_-membership-coupon')]);
                exit();
            }
            
            if(!in_array($email , $couponObj->get_email_restrictions())){ 
                // lets make it empty
                $couponObj->set_email_restrictions([]);

                $couponObj->save();//Save the coupon
            } 
            
            wp_send_json_success(['msg' =>__('Nice, it’s available !','TPRM_-membership-coupon') ]);

            wp_die();

        }


        /**
         * Save ecole and classe checkout fields to order meta
         *
         * @since V2
         * @access public
         * 
         */

         public function save_school_details_to_order_meta($order_id, $posted) {
            if( isset( $posted['ecole'] ) ) {
                update_post_meta( $order_id, '_ecole', sanitize_text_field( $posted['ecole'] ) );
            }
            if( isset( $posted['classe'] ) ) {              
                update_post_meta( $order_id, '_classe', sanitize_text_field( $posted['classe'] ) );
            }
        }

        /**
         * display order school details on admin order page
         *
         * @since V2
         * @access public
         * 
         */

        function display_order_school_details_on_admin( $order ){  

            $email_class = TPRM_membership_email::get_instance();

            $product = $email_class->access_or_license( $order );

            $order_id = $order->get_id();
            $user_id = $order->get_customer_id();
            $user_id = $order->get_customer_id();
            $user_obj = get_user_by('id', $user_id);   
            $first_name = $user_obj->first_name;
            $last_name = $user_obj->last_name;
            $full_name = $first_name . ' ' . $last_name;
            $email = $user_obj->user_email;
            $output = '';

            /* 
            * When a user who is not logged he will purshase a license 
            * In this process, we need to transmit the post metadata collected from the checkout input fields that the user has filled out.
            */
            if($product === "license"){
                $ecole = get_post_meta($order_id, '_ecole', true);
                $classe = get_post_meta($order_id, '_classe', true);
            }
            /*
            * When a user activate its account
            * In this process, we need to transmit the user metadata collected from the checkout input fields that the user has filled out.
            */
            if($product === "access"){
                $username = $user_obj->user_login;
                $ecole = get_school_details($user_id)[0]['school_name'];
                $classe = get_school_details($user_id)[0]['classe_name'];
                $year = get_school_details($user_id)[0]['school_year'];
            }
            $output .= '<div class="order_data_container" style="padding: 10px 5px;display: flex; gap: 20px; justify-content: space-between;">';
            // User details
            $output .= '<div class="order_data_column">';
            $output .= '<h3 class="user_details_title">' . __('User details', 'TPRM_-membership-coupon') . '</h3>';
               
            if ($username) {
                $output .= '<p>';
                $output .= '<span style="font-weight: 600;">' . __('Username', 'TPRM_-membership-coupon') . ':</span> ';
                $output .= '<span>' . esc_html($username) . '</span>';
                $output .= '</p>';
            }
            if ($full_name) {
                $output .= '<p>';
                $output .= '<span style="font-weight: 600;">' . __('Full Name', 'TPRM_-membership-coupon') . ':</span> ';
                $output .= '<span>' . esc_html($full_name) . '</span>';
                $output .= '</p>';
            }

            if ($email) {
                $output .= '<p>';
                $output .= '<span style="font-weight: 600;">' . __('Email address', 'TPRM_-membership-coupon') . ':</span> ';
                
                //$output .= '<span>' . esc_html($email) . '</span>';
                $output .= '<a href="mailto:' . esc_html($email) . '">' . esc_html($email) . '</a>';
                $output .= '</p>';
            }


            $output .= '</div>';


            // School details
            $output .= '<div class="order_data_column">';
            $output .= '<h3 class="school_details_title">' . __('School details', 'TPRM_-membership-coupon') . '</h3>';
               
            if ($ecole) {
                $output .= '<p>';
                $output .= '<span style="font-weight: 600;">' . __('School Name', 'TPRM_-membership-coupon') . ':</span> ';
                $output .= '<span>' . esc_html($ecole) . '</span>';
                $output .= '</p>';
            }

            if ($classe) {
                $output .= '<p>';
                $output .= '<span style="font-weight: 600;">' . __('Class Name', 'TPRM_-membership-coupon') . ':</span> ';
                $output .= '<span>' . esc_html($classe) . '</span>';
                $output .= '</p>';
            }

            if ($year) {
                $output .= '<p>';
                $output .= '<span style="font-weight: 600;">' . __('Year', 'TPRM_-membership-coupon') . ':</span> ';
                $output .= '<span>' . esc_html($year) . '</span>';
                $output .= '</p>';
            }


            $output .= '</div>';
            $output .= '</div>';

            echo $output;
            
        }


    }


}
/**
 * End TPRM_membership_coupon Main Class
 */


