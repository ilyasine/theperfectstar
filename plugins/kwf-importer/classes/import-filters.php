<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class TPRM_importer_Import_Filters{
    function __construct(){
    }
    
    function hooks(){
        add_filter( 'pre_TPRM_importer_import_single_user_username', array( $this, 'pre_import_single_user_username' ) );
    }

    function pre_import_single_user_username( $username ){
        $TPRM_importer_helper = new TPRM_importer_Helper();
        return empty( $username ) ? $TPRM_importer_helper->get_random_unique_username( 'user_' ) : $username;
    }
}

$TPRM_importer_import_filters = new TPRM_importer_Import_Filters();
$TPRM_importer_import_filters->hooks();