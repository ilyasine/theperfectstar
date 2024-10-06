<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_WP_Importer_GUI{
	function __construct(){
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'export_filters', array( $this, 'exporter' ) );
	}

	function register(){
		register_importer( 'TPRM_importer_importer', __( 'Import users or customers (CSV)', 'kwf-importer' ), __( 'Import <strong>users or customers</strong> to your site via a csv file.', 'kwf-importer' ), array( $this, 'importer' ) );
	}

	function importer(){
		echo "<script>document.location.href='" . admin_url( 'admin.php?page=TPRM_importer' ) . "'</script>";
	}

	function exporter(){		
		$title = function_exists( 'is_woocommerce' ) ? __( 'Users and customers (in CSV format)', 'kwf-importer' ) : __( 'Users (in CSV format)', 'kwf-importer' );?>
		<p><label><input type="radio" name="content" value="users" aria-describedby="Users"> <?php echo $title; ?></label></p>
		<script>
		jQuery( document ).ready( function( $ ){
			$( '#export-filters' ).submit( function( e ){
				if( $('input[type="radio"][name="content"][value="users"').is(':checked') ){
					document.location.href='<?php echo admin_url( 'admin.php?page=TPRM_importer&tab=export' ); ?>';
					return false;
				}

				return true;
			} );

			$( 'input[type="radio"][name="content"]' ).change( function(){
				if( $( this ).val() == 'users' ){
					$( '#submit' ).val( '<?php _e( 'Choose options...', 'kwf-importer' ); ?>' );
				}
				else{
					$( '#submit' ).val( '<?php _e( 'Download Export File' ) ?>' );
				}
			});
		} )	
		</script>
		<?php
	}
}
new TPRM_importer_WP_Importer_GUI();