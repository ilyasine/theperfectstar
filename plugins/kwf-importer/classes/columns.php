<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_Columns{
	function __construct(){
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		add_action( 'TPRM_importer_columns_save_settings', array( $this, 'save_settings' ), 10, 1 );

		if( get_option( 'TPRM_importer_show_profile_fields' ) == true ){
			add_action( "user_new_form", array( $this, "extra_user_profile_fields" ) );
			add_action( "show_user_profile", array( $this, "extra_user_profile_fields" ) );
			add_action( "edit_user_profile", array( $this, "extra_user_profile_fields" ) );
			add_action( "user_register", array( $this, "save_extra_user_profile_fields" ), 10, 1 );
			add_action( "personal_options_update", array( $this, "save_extra_user_profile_fields" ), 10, 1 );
			add_action( "edit_user_profile_update", array( $this, "save_extra_user_profile_fields" ), 10, 1 );
		}
	}

	function enqueue( $hook ) {
		if( $hook != 'toplevel_page_TPRM_importer' || !isset( $_GET['tab'] ) || $_GET['tab'] != 'columns' )
			return;

		wp_enqueue_script( 'TPRM_importer-datatables', '//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', array( 'jquery' ), '1.10.20' );
		wp_enqueue_style( 'TPRM_importer-datatables', '//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css', array(), '1.10.20' );
	}

	public static function admin_gui(){
		$show_profile_fields = get_option( "TPRM_importer_show_profile_fields");
		$headers = get_option("TPRM_importer_columns");
		//$headers_extended = self::get_extended();
	?>
	<h3><?php _e( 'Extra profile fields', 'kwf-importer' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e( 'Show fields in profile?', 'kwf-importer' ); ?></th>
				<td>
					<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
						<input type="checkbox" name="show-profile-fields" value="yes" <?php if( $show_profile_fields == true ) echo "checked='checked'"; ?>>
						<input type="hidden" name="show-profile-fields-action" value="update"/>
						<?php wp_nonce_field( 'kwf-security', 'security' ); ?>
						<input class="button-primary" type="submit" value="<?php _e( 'Save option', 'kwf-importer'); ?>"/>
					</form>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Reset fields in profile?', 'kwf-importer' ); ?></th>
				<td>
					<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" id="reset-profile-fields">
						<input type="hidden" name="reset-profile-fields-action" value="reset"/>
						<?php wp_nonce_field( 'kwf-security', 'security' ); ?>
						<input class="button-primary reset_fields_profile" type="submit" value="<?php _e( 'Reset fields', 'kwf-importer'); ?>"/>
					</form>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Extra profile fields loadad in previous files', 'kwf-importer' ); ?></th>
				<td><small><em><?php _e( '(if you load another CSV with different columns, the new ones will replace this list)', 'kwf-importer' ); ?></em></small>
					<ol>
						<?php 
						if( is_array( $headers ) && count( $headers ) > 0 ):
							foreach ($headers as $column): ?>
							<li><?php echo esc_html( $column ); ?></li>
						<?php endforeach;  ?>
						
						<?php else: ?>
							<li><?php _e( 'There is no columns loaded yet', 'kwf-importer' ); ?></li>
						<?php endif;
						?>
					</ol>
				</td>
			</tr>
		</tbody>
	</table>

	<?php /*
	<h2><?php _e( 'Profile fields', 'kwf-importer' ); ?></h2>
	<form id="form_table_headers_extended" action="" method="POST">
		<table id="headers_extended">
			<thead>
				<tr>
					<td>Key</td>
					<td>Label</td>
					<td>Show</td>
					<td>Type</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td>Key</td>
					<td>Label</td>
					<td>Show</td>
					<td>Type</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ( $headers_extended as $key => $header_extended): ?>
				<tr>
					<td><?php echo $key; ?></td>
					<td><input type="text" name="<?php echo $key; ?>[label]" value="<?php echo $header_extended['label']; ?>"></td>
					<td><input type="checkbox" name="<?php echo $key; ?>[show]" <?php checked( $header_extended['show'] ); ?>></td>
					<td><?php echo $header_extended['type']; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php wp_nonce_field( 'kwf-security', 'security' ); ?>
		<input type="submit" class="button button-primary" value="Save extended fields">
	</form>
	*/ ?>

	<script type="text/javascript">
	jQuery( document ).ready( function( $ ){
		$( '.reset_fields_profile' ).click( function( e ){
			e.preventDefault();

			var r = confirm( "<?php _e( 'Are you sure to reset all fields, it will delete current fields and they will restored in next import', 'kwf-importer' ); ?>" );
			
			if( !r )
				return;

			$( '#reset-profile-fields' ).submit();
		} );

		/*var table_headers_extended = $( '#headers_extended' ).DataTable();

		$( '#form_table_headers_extended' ).on( 'submit', function (e) {
			table_headers_extended.rows().nodes().page.len(-1).draw(false);

			if( $( this ).valid() ) {
				return true;
			}

			e.preventDefault();
		});*/
	} )
	</script>
		<?php
	}

	public static function get_extended(){
		$headers_extended = get_option( "TPRM_importer_columns_extended" );

		return ( empty( $headers_extended ) ) ? self::init_extended() : $headers_extended;
	}

	public static function init_extended(){
		$headers = get_option( "TPRM_importer_columns" );
		$headers_extended = array();

		foreach ( $headers as $header ) {
			$headers_extended[ $header ] = array( 
				'label' => $header,  
				'show' => true,
				'type' => 'text'
			);
		}

		update_option( "TPRM_importer_columns_extended", $headers_extended );

		return $headers_extended;
	}

	function extra_user_profile_fields( $user ) {
		$TPRM_importer_helper = new TPRM_importer_Helper();
		$TPRM_importer_restricted_fields = $TPRM_importer_helper->get_restricted_fields();
		$headers = get_option( "TPRM_importer_columns" );
	
		if( is_array( $headers ) && !empty( array_diff( $headers, $TPRM_importer_restricted_fields ) ) ):
	?>
		<h3>Extra profile information</h3>
		
		<table class="form-table"><?php
		foreach ( $headers as $column ):
			if( in_array( $column, $TPRM_importer_restricted_fields ) )
				continue;

			$column = esc_html( $column );
			$value = is_a( $user, 'WP_User' ) ? esc_attr( TPRM_importer_Helper::show_meta( $user->ID, $column ) ) : '';
		?>
			<tr>
				<th><label for="<?php echo $column; ?>"><?php echo $column; ?></label></th>
				<td><input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" value="<?php echo $value; ?>" class="regular-text" <?php echo apply_filters( 'TPRM_importer_columns_field_extra_attributes', '', $column ); ?>/></td>
			</tr>
			<?php
		endforeach;
		?>
		</table><?php
		endif;
	}

	function save_extra_user_profile_fields( $user_id ){
		$post_filtered = filter_input_array( INPUT_POST );
		if( empty( $post_filtered ) || count( $post_filtered ) == 0 )
			return;
		
		$TPRM_importer_helper = new TPRM_importer_Helper();
		$headers = get_option("TPRM_importer_columns");
		$TPRM_importer_restricted_fields = $TPRM_importer_helper->get_restricted_fields();
		$values_changed = array();
		
		if( is_array( $headers ) && count( $headers ) > 0 ):
            $values = array();

			foreach ( $headers as $column ){
				if( in_array( $column, $TPRM_importer_restricted_fields ) )
					continue;
	
				$column_sanitized = str_replace(" ", "_", $column );

				if( isset( $post_filtered[ $column_sanitized ] ) ){
                    $old_value = get_user_meta( $user_id, $column, true );

                    if( $old_value != $post_filtered[ $column_sanitized ] )
                        $values_changed[ $column ] = $post_filtered[ $column_sanitized ];

                    update_user_meta( $user_id, $column, $post_filtered[ $column_sanitized ] );
                    $values[ $column ] = $post_filtered[ $column_sanitized ];
                }
			}

            do_action( 'TPRM_importer_columns_fields_saved', $values, $values_changed );
		endif;
	}

	public static function save_settings( $form_data ){
		if ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'kwf-security' ) ) {
			wp_die( __( 'Nonce check failed', 'kwf-importer' ) ); 
		}
	
		if( isset( $form_data['show-profile-fields-action'] ) && $form_data['show-profile-fields-action'] == 'update' )
			update_option( "TPRM_importer_show_profile_fields", isset( $form_data["show-profile-fields"] ) && $form_data["show-profile-fields"] == "yes" );
	
		if( isset( $form_data['reset-profile-fields-action'] ) && $form_data['reset-profile-fields-action'] == 'reset' )
			update_option( "TPRM_importer_columns", array() );
	}
}

new TPRM_importer_Columns();