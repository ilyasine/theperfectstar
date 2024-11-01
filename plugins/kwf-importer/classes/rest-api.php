<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_REST_API{
	function __construct(){
		add_action( 'rest_api_init', array( $this, 'init' ) );
        add_filter( 'TPRM_importer_rest_api_permission_callback', function(){ return true; } );
	}

	function init() {
		register_rest_route( 'kwf-importer/v1', '/execute-cron/', array( 
			'methods' => 'GET',  
			'callback' => array( $this, 'fire_cron' ),
			'permission_callback' => function () {
				return apply_filters( 'TPRM_importer_rest_api_permission_callback', current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) );
			}
		) );
	}

	function fire_cron(){
		do_action( 'TPRM_importer_cron_process' );
		return "OK";
	}
}

new TPRM_importer_REST_API();