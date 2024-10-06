<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'buddypress/bp-loader.php' ) && !function_exists( 'bp_is_active' ) ){
	return;
}
use Shuchkin\SimpleXLSXGen;

class TPRM_import_groups{

    var $plugin_path;

	function __construct(){   
        $this->plugin_path = is_plugin_active( 'buddyboss-platform/bp-loader.php' ) ? WP_PLUGIN_DIR . "/buddyboss-platform/" : WP_PLUGIN_DIR . "/buddypress/";

        $this->fields = $this->get_fields();


	}

    function hooks(){
        add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 10, 1 );
		add_filter( 'TPRM_importer_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_filter( 'TPRM_importer_export_columns', array( $this, 'export_columns' ), 10, 1 );
	}

    function load_scripts( $hook ){ // toplevel_page_TPRM_importer
        if( $hook != 'toplevel_page_TPRM_importer' || ( isset( $_GET['tab'] ) && $_GET['tab'] != 'import-groups' ) )
            return;

        wp_enqueue_style( 'select2-css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' );
    }


    function restricted_fields( $TPRM_importer_restricted_fields ){
		return array_merge( $TPRM_importer_restricted_fields, array( 'group_name', 'group_type', 'group_parent' ), $this->fields );
	}

    public function get_fields(){
		$buddypress_fields = array();
		
		if ( !empty( $this->profile_groups ) ) {
			 foreach ( $this->profile_groups as $profile_group ) {
				if ( !empty( $profile_group->fields ) ) {				
					foreach ( $profile_group->fields as $field ) {
						$buddypress_fields[] = $field->name;
					}
				}
			}
		}

		return $buddypress_fields;
	}

    function export_columns( $row ){
		foreach ( $this->fields as $key ) {
			$row[ $key ] = $key;
		}

		$row['group_name'] = 'group_name';
		$row['group_type'] = 'group_type';
		$row['group_parent'] = 'group_parent';

		return $row;
	}

    static function admin_gui() {  
        $settings = new TPRM_importer_Settings( 'import_backend' );
		$settings->maybe_migrate_old_options( 'import_backend' );
        ?>

            <div class="wrap TPRM_importer">	     

                    <div id='message' class='updated'>    
                        <h3><i><strong><?php _e('Notes :', 'kwf-importer') ?></strong></i></h3>
                        <ul style="list-style: inside;">
                            <li><?php _e('File must contain exactly<strong> 5 columns: ecole, year, lang, classes and curriculum</strong>', 'kwf-importer') ?></li>                      
                            <li><?php _e('If you get "Request timeout" or similar timeout message while trying to import large CSV file contact your hosting support or split your files into two or more part.', 'kwf-importer') ?></li>
                            <li><?php _e('Once the import process has finished, a PDF and Excel file will be downloaded automatically.','kwf-importer'); ?></li>
                            <li><?php _e('The PDF and Excel file include all groups informations required for importing users in the next step.','kwf-importer'); ?></li>
                            <li><?php _e('From this tab, you can import tepunareomaori groups ( school & classes ) along with their settings from a CSV file.', 'kwf-importer') ?></li>
                            <li><strong><?php _e('After completing the processing of your groups-import.xlsx file, please ensure that you save the file as a CSV (Comma-Separated Values) file format.', 'kwf-importer') ?></strong></li>
                            <li><?php _e('Prepare CSV file, make sure the structure match with the sample below :', 'kwf-importer') ?></li>
                        </ul>
                    </div>

                    <div>
                        <h2><?php _e( 'Import Groups','kwf-importer' ); ?></h2>
                    </div>   

                    <table  id="TPRM_importer_file_wrapper" class="form-table">
                        <tbody>               
                            <tr>
                            <th style="vertical-align: middle;"><h4><i><strong><?php _e('CSV Sample', 'kwf-importer') ?></strong></i></h4></th>
                                <td>
                                    <table class="form-table" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th class="column-kwf-group-name"><code>[ecole]</code></th>
                                                <th class="column-kwf-group-name"><code>[year]</code></th>
                                                <th class="column-kwf-group-name"><code>[lang]</code></th>
                                                <th class="column-kwf-group-type"><code>[classe]</code></th>
                                                <th class="column-kwf-group-type"><code>[curriculum]</code></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="kwf-csv-example" valign="middle">
                                                <td class="column-kwf-group-name"><code><strong>KWF School</strong></code><br><br></td>
                                                <td class="column-kwf-group-year"><code><strong>2023</strong></code><br><br></td>
                                                <td class="column-kwf-group-year"><code><strong>en</strong></code><br><br></td>
                                                <td class="column-kwf-group-name"><code>CE1</code><br><br></td>								
                                                <td class="column-kwf-group-type"><code>100</code><br><br></td> 
                                            </tr>																
                                            <tr id="kwf-csv-example" valign="middle">
                                                <td class="column-kwf-group-name"><br><br></td>
                                                <td class="column-kwf-group-year"><br><br></td>
                                                <td class="column-kwf-group-lang"><br><br></td>
                                                <td class="column-kwf-group-type"><code>CE2</code><br><br></td>
                                                <td class="column-kwf-group-type"><code>200</code><br><br></td>
                                            </tr>																
                                            <tr id="kwf-csv-example" valign="middle">
                                                <td class="column-kwf-group-name"><br><br></td>
                                                <td class="column-kwf-group-year"><br><br></td>
                                                <td class="column-kwf-group-lang"><br><br></td>
                                                <td class="column-kwf-group-type"><code>CE3</code><br><br></td>
                                                <td class="column-kwf-group-type"><code>410</code><br><br></td>
                                            </tr>																
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    

                    <div style="clear:both;"></div>

                    <p><?php _e('Once CSV file is ready, click Choose file ', 'kwf-importer') ?></p>
                    <p><?php _e('Select your file ,then click import. That is all, enjoy', 'kwf-importer') ?></p>

                    <div id="TPRM_importer_form_wrapper" class="main_bar">
                        <form method="POST" id="TPRM_importer_form" enctype="multipart/form-data" action="" accept-charset="utf-8">
                        <table  id="TPRM_importer_file_wrapper" class="form-table">
                            <tbody>               

                            <tr class="form-field form-required">
                                <th scope="row">
                                <h4><i><strong><label for="uploadfile"><?php _e('CSV file (required)', 'kwf-importer') ?></label></strong></i></h4>
                            </th>
                                <td>
                                    <div id="upload_file">
                                        <input type="file" name="uploadfile" id="uploadfile" size="35" class="uploadfile" />							
                                    </div>
                                    <p>
                                        <?php _e('For downloading groups-import.xlsx file for school & classes, click', 'kwf-importer') ?>
                                        <a href="<?php echo plugins_url( '../samples/groups-import.xlsx', __FILE__  ); ?> "><?php _e('Here', 'kwf-importer') ?></a>
                                    </p>
                                    <div id="introduce_path" style="display:none;">
                                        <input placeholder="<?php _e( 'You have to introduce the path to file, i.e.:' ,'kwf-importer' ); ?><?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/test.csv" type="text" name="path_to_file" id="path_to_file" value="<?php echo $settings->get( 'path_to_file' ); ?>" style="width:70%;" />
                                        <em><?php _e( 'or you can upload it directly from your PC', 'kwf-importer' ); ?>, <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'kwf-importer' ); ?></a>.</em>
                                    </div>
                                </td>

                            </tr>
                            
                            </tbody>
                        </table>

                        <?php do_action( 'TPRM_importer_tab_import_before_import_button' ); ?>
                            
                        <?php wp_nonce_field( 'kwf-security', 'security' ); ?>

                        <input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="<?php _e( 'Start importing', 'kwf-importer' ); ?>"/>
                        
                        </form>
                    </div>

            </div><!-- end TPRM_importer -->
            <script type="text/javascript">
                jQuery( document ).ready( function( $ ){
                    

                    $( '#uploadfile_btn' ).click( function(){
                        if( $( '#uploadfile' ).val() == "" && $( '#upload_file' ).is( ':visible' ) ) {
                            alert("<?php _e( 'Please choose a file', 'kwf-importer' ); ?>");
                            return false;
                        }

                        if( $( '#path_to_file' ).val() == "" && $( '#introduce_path' ).is( ':visible' ) ) {
                            alert("<?php _e( 'Please enter a path to the file', 'kwf-importer' ); ?>");
                            return false;
                        }
                    } );

                    $( '.toggle_upload_path' ).click( function( e ){
                        e.preventDefault();
                        $( '#upload_file,#introduce_path' ).toggle();
                    } );

                } );
            </script>

        <?php   
    
    }


}// end TPRM_import_groups class

$TPRM_import_groups = new TPRM_import_groups();
$TPRM_import_groups->hooks();

?>
