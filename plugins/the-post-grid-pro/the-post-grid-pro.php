<?php
/**
 * Plugin Name: The Post Grid Pro
 * Plugin URI: https://www.radiustheme.com/downloads/the-post-grid-pro-for-wordpress/
 * Description: This is the Add-on plugin for The Post Grid, using this Addon you will get all pro features.
 * Author: RadiusTheme
 * Version: 7.7.5
 * Text Domain: the-post-grid-pro
 * Domain Path: /languages
 * Author URI: https://radiustheme.com/
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
define( 'RT_TPG_PRO_VERSION', '7.7.5' );
define( 'RT_TPG_PRO_AUTHOR', 'RadiusTheme' );
define( 'EDD_RT_TPG_ITEM_ITEM_NAME', 'The Post Grid Pro' );
define( 'EDD_RT_TPG_STORE_URL', 'https://www.radiustheme.com' );
define( 'EDD_RT_TPG_ITEM_ID', 3265 );
define( 'RT_THE_POST_GRID_PRO_PLUGIN_PATH', __DIR__ );
define( 'RT_THE_POST_GRID_PRO_PLUGIN_ACTIVE_FILE_NAME', __FILE__ );
define( 'RT_THE_POST_PRO_GRID_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'RT_THE_POST_GRID_PRO_PLUGIN_SLUG', basename( __DIR__ ) );
define( 'RT_THE_POST_GRID_PRO_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

if ( ! class_exists( 'RtTpgPro' ) ) {
	require_once 'app/RtTpgPro.php';
}
