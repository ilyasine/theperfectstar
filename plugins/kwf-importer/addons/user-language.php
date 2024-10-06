<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_user_lang{
    var $meta_key;

    function __construct(){
        global $blog_id, $wpdb;
        $this->meta_key = $wpdb->get_blog_prefix( $blog_id ) . 'lang';
    }

    function hooks(){
        add_filter( 'TPRM_importer_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
        add_action( 'TPRM_importer_documentation_after_plugins_activated', array( $this, 'documentation_after_plugins_activated' ) );
        add_action( 'post_TPRM_importer_import_single_user', array( $this, 'post_import_single_user' ), 10, 3 );
        add_filter( 'TPRM_importer_export_columns', array( $this, 'export_columns' ), 10, 1 );
		add_filter( 'TPRM_importer_export_data', array( $this, 'export_data' ), 10, 2 );
    }

    function restricted_fields( $TPRM_importer_restricted_fields ){
        return array_merge( $TPRM_importer_restricted_fields, array( 'lang' ) );
    }

    function documentation_after_plugins_activated(){
        ?>
        <tr valign="top">
            <th scope="row"><?php _e( "User language", 'kwf-importer' ); ?></th>
            <td>
                <?php _e( "You must specify a language and assign it to the user", 'kwf-importer' ); ?>.
                <ul style="list-style:disc outside none; margin-left:2em;">
                    <li><?php _e( "lang", 'kwf-importer' ); ?></li>
                    <li><?php _e( "The value of each cell will be the language of the user has acces to", 'kwf-importer' ); ?></li>
                    <li><?php _e( "The values must be : <strong>en</strong> ( for english ) , <strong>fr</strong> ( for french ) , <strong>bi</strong> ( for bilingual )", 'kwf-importer' ); ?></li>
                </ul>
            </td>
        </tr>
        <?php
    }

    function post_import_single_user( $headers, $row, $user_id ){
        $pos = array_search( 'lang', $headers );
    
        if( $pos === FALSE )
            return;
    
        $user_language = $row[ $pos ];
    
        //$avatar_id = media_sideload_image( $user_language, 0, 'Avatar of user ' . $user_id, 'id' );
        update_user_meta( $user_id, $this->meta_key, $user_language );
    }

    function export_columns( $row ){
		$row['lang'] = 'lang';
		return $row;
	}

	function export_data( $row, $user ){
        $row[] =  get_user_meta( $user, $this->meta_key, true ) ;
		return $row;
	}
}

$TPRM_importer_user_lang = new TPRM_importer_user_lang();
$TPRM_importer_user_lang->hooks();