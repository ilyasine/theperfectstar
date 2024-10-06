<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_Exporter{
	private $path_csv;

	function __construct(){
		$upload_dir = wp_upload_dir();
		$this->path_csv = $upload_dir['basedir'] . "/export-users.csv";

        add_action( 'init', array( $this, 'download_export_file' ) );
        add_action( 'admin_init', array( $this, 'download_export_file' ) );
		add_action( 'wp_ajax_TPRM_importer_export_users_csv', array( $this, 'export_users_csv' ) );
	}

    static function enqueue(){
        wp_enqueue_script( 'TPRM_importer_export_js', plugins_url( 'assets/export.js', dirname( __FILE__ ) ), false, TPRM_importer_VERSION, true );
        wp_localize_script( 'TPRM_importer_export_js', 'TPRM_importer_export_js_object', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'starting_process' => __( 'Starting process', 'kwf-importer' ),
            'step' => __( 'Step', 'kwf-importer' ),
            'of_approximately' => __( 'of approximately', 'kwf-importer' ),
            'steps' => __( 'steps', 'kwf-importer' ),
            'error_thrown' => __( 'Error thrown in the server, we cannot continue. Please check console to see full details about the error.', 'kwf-importer' ),
        ) );
    }

    static function styles(){
        ?>
        <style>
#TPRM_importer_export_results{
	display: none;
	background-color: #dcdcde;
	padding: 20px;
}

#TPRM_importer_exporter .user-exporter-progress-wrapper{
    padding: 5px;
    background-color: white;
    width: 80%;
    margin: 0 auto;
    text-align: center;
}

#TPRM_importer_exporter .user-exporter-progress{
    width: 100%;
    height: 42px;
	border: 0;
	border-radius: 9px;
}
.user-exporter-progress::-webkit-progress-bar {
	background-color: #f3f3f3;
	border-radius: 9px;
}

.user-exporter-progress::-webkit-progress-value {
	background: #2271b1;
	border-radius: 9px;
}

.user-exporter-progress::-moz-progress-bar {
	background: #2271b1;
	border-radius: 9px;
}

.user-exporter-progress .progress-value {
	padding: 0px 5px;
	line-height: 20px;
	margin-left: 5px;
	font-size: .8em;
	color: #555;
	height: 18px;
	float: right;
}

#TPRM_importer_exporter.user-exporter__exporting table,
#TPRM_importer_exporter .user-exporter-progress-wrapper{
    display: none;
}

#TPRM_importer_exporter.user-exporter__exporting .user-exporter-progress-wrapper{
    display: block;
}
        </style>
        <?php
    }

	static function admin_gui(){
	?>
	<div id="TPRM_importer_export_results"></div>

	<h3 id="TPRM_importer_export_users_header"><?php _e( 'Export users', 'kwf-importer' ); ?></h3>
	<form id="TPRM_importer_exporter">
		<table class="form-table">
			<tbody>
				<tr id="TPRM_importer_role_wrapper" valign="top">
					<th scope="row"><?php _e( 'Role', 'kwf-importer' ); ?></th>
					<td>
                        <?php TPRM_importerHTML()->select( array(
                            'options' => TPRM_importer_Helper::get_editable_roles(),
                            'name' => 'role',
                            'show_option_all' => false,
                            'show_option_none' => __( 'All roles', 'kwf-importer' ),
                        )); ?>
					</td>
				</tr>
				<tr id="TPRM_importer_columns" valign="top">
					<th scope="row"><?php _e( 'Columns', 'kwf-importer' ); ?></th>
					<td>
						<?php TPRM_importerHTML()->textarea( array( 'name' => 'columns' ) ); ?>
						<span class="description"><?php _e( 'You can use this field to set which columns must be exported and in which order.  If you leave it empty, all columns will be exported. Use a list of fields separated by commas, for example', 'kwf-importer' ); ?>: user_email,first_name,last_name</span>
					</td>
				</tr>
				<tr id="TPRM_importer_user_created_wrapper" valign="top">
					<th scope="row"><?php _e( 'User created', 'kwf-importer' ); ?></th>
					<td>
						<label for="from">from <?php TPRM_importerHTML()->text( array( 'type' => 'date', 'name' => 'from', 'class' => '' ) ); ?></label>
						<label for="to">to <?php TPRM_importerHTML()->text( array( 'type' => 'date', 'name' => 'to', 'class' => '' ) ); ?></label>
					</td>
				</tr>
				<tr id="TPRM_importer_delimiter_wrapper" valign="top">
					<th scope="row"><?php _e( 'Delimiter', 'kwf-importer' ); ?></th>
					<td>
                        <?php TPRM_importerHTML()->select( array(
                            'options' => TPRM_importer_Helper::get_csv_delimiters_titles(),
                            'name' => 'delimiter',
                            'show_option_all' => false,
                            'show_option_none' => false,
                        )); ?>
					</td>
				</tr>
				<tr id="TPRM_importer_timestamp_wrapper" valign="top">
					<th scope="row"><?php _e( 'Convert timestamp data to date format', 'kwf-importer' ); ?></th>
					<td>
                        <?php TPRM_importerHTML()->checkbox( array( 'name' => 'convert_timestamp', 'current' => 0 ) ); ?>
						<?php TPRM_importerHTML()->text( array( 'name' => 'datetime_format', 'value' => 'Y-m-d H:i:s', 'class' => '' ) ); ?>
                        <span class="description"><a href="https://www.php.net/manual/en/datetime.formats.php"><?php _e( 'accepted formats', 'kwf-importer' ); ?></a> <?php _e( 'If you have problems and you get some value exported as a date that should not be converted to date, please deactivate this option. If this option is not activated, datetime format will be ignored.', 'kwf-importer' ); ?></span>
					</td>
				</tr>
				<tr id="TPRM_importer_order_fields_alphabetically_wrapper" valign="top">
					<th scope="row"><?php _e( 'Order fields alphabetically', 'kwf-importer' ); ?></th>
					<td>
                        <?php TPRM_importerHTML()->checkbox( array( 'name' => 'order_fields_alphabetically', 'current' => 0 ) ); ?>
						<span class="description"><?php _e( "Order all columns alphabetically to check easier your data. First two columns won't be affected", 'kwf-importer' ); ?></span>
					</td>
				</tr>
                <tr id="TPRM_importer_order_fields_double_encapsulate_serialized_values" valign="top">
					<th scope="row"><?php _e( 'Double encapsulate serialized values', 'kwf-importer' ); ?></th>
					<td>
                        <?php TPRM_importerHTML()->checkbox( array( 'name' => 'double_encapsulate_serialized_values', 'current' => 0 ) ); ?>                    
						<span class="description"><?php _e( "Serialized values sometimes can have problems being displayed in Microsoft Excel or LibreOffice, we can double encapsulate this kind of data but you would not be able to import this data beucase instead of serialized data it would be managed as strings", 'kwf-importer' ); ?></span>
					</td>
				</tr>
				<tr id="TPRM_importer_download_csv_wrapper" valign="top">
					<th scope="row"><?php _e( 'Download CSV file with users', 'kwf-importer' ); ?></th>
					<td>
						<input class="button-primary" type="submit" value="<?php _e( 'Download', 'kwf-importer'); ?>"/>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="action" value="TPRM_importer_export_users_csv"/>
		<?php wp_nonce_field( 'kwf-security', 'security' ); ?>

        <div class="user-exporter-progress-wrapper">
            <progress class="user-exporter-progress" value="0" max="100"></progress>
            <span class="user-exporter-progress-value">0%</span>
        </div>
	</form>

	<script type="text/javascript">
	jQuery( document ).ready( function( $ ){
		$( "input[name='from']" ).change( function() {
			$( "input[name='to']" ).attr( 'min', $( this ).val() );
		})

		$( '#convert_timestamp' ).on( 'click', function() {
			check_convert_timestamp_checked();
		});

		function check_convert_timestamp_checked(){
			if( $('#convert_timestamp').is(':checked') ){
				$( '#datetime_format' ).prop( 'disabled', false );
			} else {
				$( '#datetime_format' ).prop( 'disabled', true );
			}
		}
	} )
	</script>
	<?php
	}

    function download_export_file() {
		if( current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) && isset( $_GET['action'], $_GET['nonce'] ) && wp_verify_nonce( wp_unslash( $_GET['nonce'] ), 'kwf-security' ) && 'download_user_csv' === wp_unslash( $_GET['action'] ) ) {
            $exporter = new TPRM_importer_Batch_Exporter();

			if ( !empty( $_GET['filename'] ) ){
				$exporter->set_filename( wp_unslash( $_GET['filename'] ) );
			}

			$exporter->export();
		}
	}

	function get_results(){
		$bad_character_formulas_values_cleaned = get_transient( 'TPRM_importer_export_bad_character_formulas_values_cleaned' );
		delete_transient( 'TPRM_importer_export_bad_character_formulas_values_cleaned' );
		
		if( empty( $bad_character_formulas_values_cleaned ) ){
			return '';
		}
		
		$results = array();

		foreach( $bad_character_formulas_values_cleaned as $info ){
			$results[] = sprintf( __( 'User with id: %s has the cell of the column: %s edited because has content that may auto-run formulas in certain spreadsheet apps, new value is: %s', 'kwf-importer' ), $info['user_id'], $info['key'], $info['value'] );
		}

		$ret = '<h3>' . __( 'Export results','kwf-importer' ) . '</h3>';
		$ret .= '<h4>' . __( 'Some values has been altered','kwf-importer' ) . '</h4>';
		$ret .= '<ul>';
		foreach( $results as $result ){
			$ret .= '<li>' . $result . '</li>';
		}

		$ret .= '</ul>';

		return $ret;
	}

    function export_users_csv(){
        check_ajax_referer( 'kwf-security', 'security' );

		if( !current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) )
			wp_die( __( 'Only users who are able to create users can export them.', 'kwf-importer' ) );

    
        $step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1;
                
        $exporter = new TPRM_importer_Batch_Exporter();

		if( $step == 1 )
			delete_transient( 'TPRM_importer_export_bad_character_formulas_values_cleaned' );
               
        $exporter->set_page( $step );
        $exporter->set_delimiter( sanitize_text_field( $_POST['delimiter'] ) );
        $exporter->set_role( sanitize_text_field( $_POST['role'] ) );
        $exporter->set_from( sanitize_text_field( $_POST['from'] ) );
        $exporter->set_to( sanitize_text_field( $_POST['to'] ) );
        $exporter->set_convert_timestamp( $_POST['convert_timestamp'] );
        $exporter->set_datetime_format( sanitize_text_field( $_POST['datetime_format'] ) );
        $exporter->set_order_fields_alphabetically( $_POST['order_fields_alphabetically'] );
        $exporter->set_double_encapsulate_serialized_values( $_POST['double_encapsulate_serialized_values'] );
        $exporter->set_filtered_columns( ( isset( $_POST['columns'] ) && !empty( $_POST['columns'] ) ) ? $_POST['columns'] : array() );
        $exporter->set_orderby( ( isset( $_POST['orderby'] ) && !empty( $_POST['orderby'] ) ) ? sanitize_text_field( $_POST['orderby'] ) : '' );
        $exporter->set_order( ( isset( $_POST['order'] ) && !empty( $_POST['order'] ) ) ? sanitize_text_field( $_POST['order'] ) : 'ASC' );
        $exporter->load_columns();
        
        $exporter->generate_file();

        if ( 100 <= $exporter->get_percent_complete() ) {
            $query_args = array(
                'nonce'    => wp_create_nonce( 'kwf-security' ),
                'action'   => 'download_user_csv',
                'filename' => $exporter->get_filename()
            );

            wp_send_json_success(
                array(
                    'step'       => 'done',
                    'percentage' => 100,
                    'url'        => add_query_arg( $query_args, admin_url( 'tools.php?page=TPRM_importer&tab=export' ) ),
					'results'	 => $this->get_results()
                )
            );
        } else {
            wp_send_json_success(
                array(
                    'step'       => ++$step,
                    'total_steps' => $exporter->get_total_steps(),
                    'percentage' => $exporter->get_percent_complete(),
                )
            );
        }
    }
}

$TPRM_importer_exporter = new TPRM_importer_Exporter();