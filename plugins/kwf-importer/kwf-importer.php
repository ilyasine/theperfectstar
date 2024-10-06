<?php
/*
Plugin Name:	KWF Importer
Plugin URI:		https://tepunareomaori.com/
Description:	A plugin created by KWF that import and export tepunareomaori Data
Version:		1.3.0
Author:			KWF
Author URI: 	https://tepunareomaori.com/
License:     	GPL3
License URI: 	https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: kwf-importer
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) 
	exit;

define( 'TPRM_importer_VERSION', '1.3.0' );


class TPRM_importer{
	var $file;

	function __construct(){ 
	}

	function on_init(){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if( is_plugin_active( 'buddypress/bp-loader.php' ) || function_exists( 'bp_is_active' ) ){
			if ( defined( 'BP_VERSION' ) )
				$this->loader();
			else
				add_action( 'bp_init', array( $this, 'loader' ) );
		}
		else{
			$this->loader();
		}

		load_plugin_textdomain( 'kwf-importer', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	function loader(){
		add_action( 'admin_menu', array( $this, 'TPRM_importer_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_filter( 'plugin_action_links', array( $this, 'action_links' ), 10, 2 );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'wp_check_filetype_and_ext' ), PHP_INT_MAX, 4 );
	
		if( is_plugin_active( 'buddypress/bp-loader.php' ) && file_exists( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' ) ){
			require_once( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' );	
		}
	
		// classes
		foreach ( glob( plugin_dir_path( __FILE__ ) . "classes/*.php" ) as $file ) {
			include_once( $file );
		}
	
		// includes
		foreach ( glob( plugin_dir_path( __FILE__ ) . "include/*.php" ) as $file ) {
			include_once( $file );
		}
	
		// addons
		foreach ( glob( plugin_dir_path( __FILE__ ) . "addons/*.php" ) as $file ) {
			include_once( $file );
		}
	}
	
	static function activate(){
		include_once( 'classes/options.php' );
		$TPRM_importer_default_options_list = TPRM_importer_Options::get_default_list();
			
		foreach ( $TPRM_importer_default_options_list as $key => $value) {
			add_option( $key, $value, '', false );		
		}
	}

	static function deactivate(){
		wp_clear_scheduled_hook( 'TPRM_importer_cron' );
	}

	function TPRM_importer_menu() {
		$TPRM_importer_import = new TPRM_importer_Import();
		
		if( ! current_user_can('administrator') ) return;

		global $submenu;
	
			add_menu_page(
				'KWF Importer Settings',
				'KWF Importer',
				'manage_options',
				'TPRM_importer',
				array( $TPRM_importer_import, 'show' ),
				'dashicons-database-import',
				2
			);

			$TPRM_menus = array(
				'import-groups' => __( 'Import Groups', 'kwf-importer' ),
				'import-users' => __( 'Import Users ', 'kwf-importer' ),				
				'export' => __( 'Export', 'kwf-importer' ),
                'frontend' => __( 'Frontend', 'kwf-importer' ), 
                'mail-options' => __( 'Mail options', 'kwf-importer' ), 
                'doc' => __( 'Documentation', 'kwf-importer' ), 
			);

			foreach( $TPRM_menus as $menu_key => $menu_label ) {
				add_submenu_page( 
					'TPRM_importer',
					$menu_label , 
					$menu_label , 
					'manage_options', 
					'TPRM_importer&tab=' . $menu_key , 
					array( $TPRM_importer_import, 'show' ),
				);
			}
			
			$submenu['TPRM_importer'][0] = array( __( 'All settings', 'kwf-importer' ), 'manage_options', 'TPRM_importer&tab=import-groups', 'Import and export tepunareomaori Data' );
	}
	
	function admin_enqueue_scripts( $hook ) {
		if( 'toplevel_page_TPRM_importer' != $hook )
			return;
		
		wp_enqueue_style( 'TPRM_importer_css', plugins_url( 'assets/style.css', __FILE__ ), false, TPRM_importer_VERSION );
		wp_enqueue_style( 'datatable', '//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css' );
		wp_enqueue_script( 'datatable', '//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js' );
		wp_enqueue_script( 'TPRM_importer_script_js', plugin_dir_url( __FILE__ ) . 'assets/script.js', false, TPRM_importer_VERSION, true );
		wp_enqueue_script( 'TPRM_jspdf', plugin_dir_url( __FILE__ ) . 'assets/jspdf.umd.min.js' );
		wp_enqueue_script( 'TPRM_jspdf_autotable', plugin_dir_url( __FILE__ ) . 'assets/jspdf.plugin.autotable.js' );
		wp_enqueue_script( 'TPRM_jspdf_nunito_font', plugin_dir_url( __FILE__ ) . 'assets/Nunito-Regular-normal.js' );

        if( isset( $_GET['tab'] ) && $_GET['tab'] == 'export' ){
            TPRM_importer_Exporter::enqueue();
        }
	}

	function action_links( $links, $file ) {
		if ($file == 'kwf-importer/kwf-importer.php') {
			
			$links[] = sprintf( __( '<a href="%s">Documentation</a>', 'kwf-importer' ), get_admin_url( null, 'admin.php?page=TPRM_importer&tab=doc' ) );
			$links[] = sprintf( __( '<a href="%s">Settings</a>', 'kwf-importer' ), get_admin_url( null, 'admin.php?page=TPRM_importer' ) );
			
			return array_reverse( $links );		
		}
		
		return $links; 
	}

	function wp_check_filetype_and_ext( $values, $file, $filename, $mimes ) {
		if ( extension_loaded( 'fileinfo' ) ) {
			// with the php-extension, a CSV file is issues type text/plain so we fix that back to 
			// text/csv by trusting the file extension.
			$finfo     = finfo_open( FILEINFO_MIME_TYPE );
			$real_mime = finfo_file( $finfo, $file );
			finfo_close( $finfo );
			if ( $real_mime === 'text/plain' && preg_match( '/\.(csv)$/i', $filename ) ) {
				$values['ext']  = 'csv';
				$values['type'] = 'text/csv';
			}
		} else {
			// without the php-extension, we probably don't have the issue at all, but just to be sure...
			if ( preg_match( '/\.(csv)$/i', $filename ) ) {
				$values['ext']  = 'csv';
				$values['type'] = 'text/csv';
			}
		}
		return $values;
	}	
}

function TPRM_importer_start(){
	$import_TPRM_data = new TPRM_importer();
	add_action( 'init', array( $import_TPRM_data, 'on_init' ) );
}
add_action( 'plugins_loaded', 'TPRM_importer_start', 8);

register_activation_hook( __FILE__, array( 'TPRM_importer', 'activate' ) ); 
register_deactivation_hook( __FILE__, array( 'TPRM_importer', 'deactivate' ) );