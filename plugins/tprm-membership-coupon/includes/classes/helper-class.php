<?php

defined( 'ABSPATH' ) || exit;  // Exit if accessed directly

/**
 ** Start TPRM_membership_helper Class*
 */

 if( !class_exists("TPRM_membership_helper") ) {

    class TPRM_membership_helper{ 

        public $current_lang;
        private static $instance = null;
       
        function __construct(){

            $current_lang = apply_filters( 'wpml_current_language', NULL );

            $this->current_lang =  $current_lang;

        }

        /**
		 * Get single instance of TPRM_membership_coupon
		 *
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
         * classer les produits par langue
         *
         * @since V2
         * @access public
         * @return Array
         */

        public function TPRM_multilang_product(){

            $kmc_product_lang = array(
                'en' => array(
                    'license' => 16427,
                    'access'  => 16428,               
                ),
                'fr' => array(
                    'license' => 16422,
                    'access'  => 16423,
                    
                ),
            );

            return $kmc_product_lang;
            
        }


    }

}
/**
 ** End TPRM_membership_helper Class*
 */
