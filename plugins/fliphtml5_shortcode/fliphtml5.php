<?php
/*
Plugin Name: Flip Html5 Short Code
Plugin URI: https://fliphtml5.com/download/wordpress_plugin/fliphtml5_shortcode.zip
Description: Flip Html5 WordPress Plugin
Version: 1.0
Author: fliphtml5.com
Author URI: https://fliphtml5.com
License: Copyright 2019 fliphtml5.com, All Rights Reserved
*/

require_once 'shortcode.php';

define('FLIPHTML5_SHORTCODE_VERSION', '1.0');

add_action('init', 'regisiter_shortcode' );
register_activation_hook( __FILE__, 'regisiter_shortcode' );
register_deactivation_hook( __FILE__, 'unregister_shortcode' );


function regisiter_shortcode(){
	if( !shortcode_exists('fliph5') ){
	  add_shortcode('fliph5', 'fliphtml5_shortcode');
  }
}

function unregister_shortcode(){
	if( shortcode_exists('fliph5') ){
	  remove_shortcode('fliph5');
  }
}

?>