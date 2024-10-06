<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'wp-user-avatar/wp-user-avatar.php' ) ){
	return;
}

class TPRM_importer_WPUA{
    var $meta_key;

    function __construct(){
        global $blog_id, $wpdb;
        $this->meta_key = $wpdb->get_blog_prefix( $blog_id ) . 'user_avatar';
    }

    function hooks(){
        add_filter( 'TPRM_importer_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
        add_action( 'TPRM_importer_documentation_after_plugins_activated', array( $this, 'documentation_after_plugins_activated' ) );
        add_action( 'post_TPRM_importer_import_single_user', array( $this, 'post_import_single_user' ), 10, 3 );
        add_filter( 'TPRM_importer_export_columns', array( $this, 'export_columns' ), 10, 1 );
		add_filter( 'TPRM_importer_export_data', array( $this, 'export_data' ), 10, 2 );
    }

    function restricted_fields( $TPRM_importer_restricted_fields ){
        return array_merge( $TPRM_importer_restricted_fields, array( 'avatar_url' ) );
    }

    function documentation_after_plugins_activated(){
        ?>
        <tr valign="top">
            <th scope="row"><?php _e( "WP Users Avatar is activated", 'kwf-importer' ); ?></th>
            <td>
                <?php _e( "You can import user avatar and assign them to the users using the next format", 'kwf-importer' ); ?>.
                <ul style="list-style:disc outside none; margin-left:2em;">
                    <li><?php _e( "avatar_url as the column title", 'kwf-importer' ); ?></li>
                    <li><?php _e( "The value of each cell will be the url to the image in your system", 'kwf-importer' ); ?></li>
                </ul>
            </td>
        </tr>
        <?php
    }

    function post_import_single_user( $headers, $row, $user_id ){
        $pos = array_search( 'avatar_url', $headers );
    
        if( $pos === FALSE )
            return;
    
        $avatar_url = $row[ $pos ];
    
        $avatar_id = media_sideload_image( $avatar_url, 0, 'Avatar of user ' . $user_id, 'id' );
        update_user_meta( $user_id, $this->meta_key, $avatar_id );
    }

    function export_columns( $row ){
		$row['user_avatar'] = 'user_avatar';
		return $row;
	}

	function export_data( $row, $user ){
        $row[] = wp_get_attachment_url( get_user_meta( $user, $this->meta_key, true ) );
		return $row;
	}
}

$TPRM_importer_wpua = new TPRM_importer_WPUA();
$TPRM_importer_wpua->hooks();