<?php 
/***
Plugin Name: 	TPRM Membership and Coupons
Plugin URI:		https://tepunareomaori.co.nz/
Description:	A plugin created by tepunareomaori that create and manages e-commerce and membership features for tepunareomaori.
Version:		2.2.0
Author:			tepunareomaori
Author URI: 	https://tepunareomaori.co.nz/
License:     	GPL3
License URI: 	https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: 	TPRM-membership-coupon
Domain Path: 	/languages
***/


defined( 'ABSPATH' ) || exit; // Exit if accessed directly

//constants
define('TPRM_MEM_CO_VERSION', '1.2.0' );
define('TPRM_MEM_CO_DIR', plugin_dir_path(__FILE__));
define('TPRM_MEM_CO_CLASS',plugin_dir_path( __FILE__ ) . 'includes/classes/');
define('TPRM_MEM_CO_ASSETS', plugins_url( 'assets/',__FILE__));
define('TPRM_MEM_CO_CSS', plugins_url( 'assets/css/',__FILE__));
define('TPRM_MEM_CO_JS', plugins_url( 'assets/js/',__FILE__));
define('TPRM_MEM_CO_IMG', plugins_url( 'assets/images/',__FILE__));
define('TPRM_MEM_CO_BASE', plugin_basename(__FILE__));
define('TPRM_MEM_CO_FILE',__FILE__);


require(TPRM_MEM_CO_DIR.'includes/load.php');


if (!function_exists('TPRM_membership_coupon_init')){

	function TPRM_membership_coupon_init(){
		 $plugin= new kmc_init();
		 $plugin->run();
	}
}

// Initiate the plugin's core functionality
TPRM_membership_coupon_init();





