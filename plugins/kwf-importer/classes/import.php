<?php

use Shuchkin\SimpleXLSXGen;

require_once 'SimpleXLSXGen.php';

class TPRM_importer_Import{
    function __construct(){ 
    }
    
    function show(){
        if ( !current_user_can( apply_filters( 'TPRM_importer_capability', 'create_users' ) ) ) {
            wp_die( __( 'You are not allowed to see this content.', 'kwf-importer' ));
        }
    
        $tab = ( isset ( $_GET['tab'] ) ) ? $_GET['tab'] : 'import-kwf-data';
        $sections = $this->get_sections_from_tab( $tab );
	    $section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'main';
    
        if( isset( $_POST ) && !empty( $_POST ) ):
            if ( !wp_verify_nonce( $_POST['security'], 'kwf-security' ) ) {
                wp_die( __( 'Nonce check failed', 'kwf-importer' ) ); 
            }
    
            switch ( $tab ){

                  case 'import-users':
                    TPRM_importerSettings()->save_multiple( 'import_backend', $_POST );

                    if( isset( $_POST['uploadfile'] ) && !empty( $_POST['uploadfile'] ) ){
                        $this->fileupload_process( $_POST, false );
                        return;
                    }
                  break;

                  case 'import-groups':
                    //TPRM_importerSettings()->save_multiple( 'import_backend', $_POST );

                    if( isset( $_POST['uploadfile'] ) && !empty( $_POST['uploadfile'] ) ){
                        $this->group_fileupload_process( $_POST, false );
                        return;
                    }
                  break;
    
                  case 'frontend':
                      do_action( 'TPRM_importer_frontend_save_settings', $_POST );
                  break;
    
                case 'columns':
                    do_action( 'TPRM_importer_columns_save_settings', $_POST );
                  break;
    
                case 'mail-options':
                    do_action( 'TPRM_importer_mail_options_save_settings', $_POST );
                  break;
    
                  case 'cron':
                      do_action( 'TPRM_importer_cron_save_settings', $_POST );
                  break;
              }
        endif;
        
        $this->admin_tabs( $tab );
        $this->secondary_admin_tabs( $tab, $section, $sections );
        $this->show_notices();
       
          switch ( $tab ){

            case 'import-groups':
                TPRM_import_groups::admin_gui();	
            break;

            case 'import-users' :
                TPRM_import_users::admin_gui();	
            break;

            case 'export' :
                TPRM_importer_Exporter::admin_gui();	
            break;
    
            case 'frontend':
                TPRM_importer_Frontend::admin_gui();	
            break;
   
            case 'columns':
                TPRM_importer_Columns::admin_gui();
            break;
    
            case 'meta-keys':
                TPRM_importer_MetaKeys::admin_gui();
            break;
    
            case 'doc':
                TPRM_importer_Doc::message();
            break;
    
            case 'mail-options':
                TPRM_importer_Email_Options::admin_gui();
            break;
    
            case 'cron':
                TPRM_importer_Cron::admin_gui();
            break;
    
            case 'help':
                TPRM_importer_Help::message();
            break;
    
            default:
                do_action( 'TPRM_importer_tab_action_' . $tab, $section );
            break;
        }
    }

    function admin_tabs( $current = 'import-kwf-data' ) {
        $tabs = array( 
                'import-groups' => __( 'Import Groups', 'kwf-importer' ),
                'import-users' => __( 'Import Users ', 'kwf-importer' ),             
                'export' => __( 'Export', 'kwf-importer' ),
                'frontend' => __( 'Frontend', 'kwf-importer' ), 
                'mail-options' => __( 'Mail options', 'kwf-importer' ),
               /*  'meta-keys' => __( 'Meta Keys', 'kwf-importer' ), */
                'doc' => __( 'Documentation', 'kwf-importer' ), 
        );
    
        $tabs = apply_filters( 'TPRM_importer_tabs', $tabs );
    
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';

            $class = apply_filters( 'TPRM_importer_tab_class', $class, $tab );            
            $href = apply_filters( 'TPRM_importer_tab_href', '?page=TPRM_importer&tab=' . $tab, $tab );
            $target = apply_filters( 'TPRM_importer_tab_target', '_self', $tab );

            if( !function_exists( 'TPRM_importer_ec_check_active' ) && $tab == 'cron-export' ){
                $name = $name .= ' (PRO)';
                $href = 'https://import-wp.com/recurring-export-addon/';
                $target = '_blank';
            }
    
            echo "<a class='nav-tab$class' href='$href' target='$target'>$name</a>";    
        }
        echo '</h2>';
    }

    static function secondary_admin_tabs( $active_tab = '', $section = '', $sections = array() ){
        if( empty( $sections ) )
            return;

        $links = array();

        foreach ( $sections as $section_id => $section_name ) {
            $tab_url = add_query_arg(
                array(
                    'page'      => 'TPRM_importer',
                    'tab'       => $active_tab,
                    'section'   => $section_id,
                ),
                admin_url( 'admin.php' )
            );

            $class = ( $section === $section_id ) ? 'current' : '';
            $links[ $section_id ] = '<li class="' . esc_attr( $class ) . '"><a class="' . esc_attr( $class ) . '" href="' . esc_url( $tab_url ) . '">' . esc_html( $section_name ) . '</a><li>';
        } ?>

        <div class="wp-clearfix">
            <ul class="TPRM_importer-subsubsub">
                <?php echo implode( '', $links ); ?>
            </ul>
        </div>

        <?php
    }

    function get_sections_from_tab( $tab ){
        switch ( $tab ){
            case 'import-groups':
            case 'import-users':    
            case 'export':
            case 'frontend':
            case 'doc':
            case 'mail-options':
              return array();
          break;
  
          default:
              return apply_filters( 'TPRM_importer_tab_section_' . $tab, array() );
          break;
      }
    }

    function show_notices(){
        $notices = TPRM_importer_Helper::get_notices();
        foreach( $notices as $notice ){
            ?>
            <div class="notice notice-success"> 
                <p><strong><?php echo $notice; ?></strong></p>
            </div>
            <?php
        }
    }

    function fileupload_process( $form_data, $is_cron = false, $is_frontend  = false ) {
        if ( !defined( 'DOING_CRON' ) && ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'kwf-security' ) ) ){
            wp_die( __( 'Nonce check failed', 'kwf-importer' ) ); 
        }

        if( empty( $_FILES['uploadfile']['name'] ) || $is_frontend ){
              $path_to_file = wp_normalize_path( $form_data["path_to_file"] );
              
            if( validate_file( $path_to_file ) !== 0 ){
                echo "<p>" . __( 'Error, path to file is not well written', 'kwf-importer' ) . ": $path_to_file</p>";
                echo sprintf( __( 'Reload or try <a href="%s">a new import here</a>', 'kwf-importer' ), get_admin_url( null, 'admin.php?page=TPRM_importer' ) );
            } 
            elseif( !file_exists ( $path_to_file ) ){
                echo "<p>" . __( 'Error, we cannot find the file', 'kwf-importer' ) . ": $path_to_file</p>";
                echo sprintf( __( 'Reload or try <a href="%s">a new import here</a>', 'kwf-importer' ), get_admin_url( null, 'admin.php?page=TPRM_importer' ) );
            }
            else{
                $this->import_users( $path_to_file, $form_data, 0, $is_cron, $is_frontend );
            }            
        }else{
            $uploadfile = wp_handle_upload( $_FILES['uploadfile'], array( 'test_form' => false, 'mimes' => array('csv' => 'text/csv') ) );
    
            if ( !$uploadfile || isset( $uploadfile['error'] ) ) {
                wp_die( __( 'Problem uploading file to import. Error details: ' . var_export( $uploadfile['error'], true ), 'kwf-importer' ));
            } else {
                $this->import_users( $uploadfile['file'], $form_data, TPRM_importer_Helper::get_attachment_id_by_url( $uploadfile['url'] ), $is_cron, $is_frontend );
            }
        }
    }

    function group_fileupload_process( $form_data, $is_cron = false, $is_frontend  = false ) {
        if ( !defined( 'DOING_CRON' ) && ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'kwf-security' ) ) ){
            wp_die( __( 'Nonce check failed', 'kwf-importer' ) ); 
        }

        if( empty( $_FILES['uploadfile']['name'] ) || $is_frontend ){
              $path_to_file = wp_normalize_path( $form_data["path_to_file"] );
              
            if( validate_file( $path_to_file ) !== 0 ){
                echo "<p>" . __( 'Error, path to file is not well written', 'kwf-importer' ) . ": $path_to_file</p>";
                echo sprintf( __( 'Reload or try <a href="%s">a new import here</a>', 'kwf-importer' ), get_admin_url( null, 'admin.php?page=TPRM_importer' ) );
            } 
            elseif( !file_exists ( $path_to_file ) ){
                echo "<p>" . __( 'Error, we cannot find the file', 'kwf-importer' ) . ": $path_to_file</p>";
                echo sprintf( __( 'Reload or try <a href="%s">a new import here</a>', 'kwf-importer' ), get_admin_url( null, 'admin.php?page=TPRM_importer' ) );
            }
            else{
                $this->import_groups( $path_to_file, $form_data, 0, $is_cron, $is_frontend );
            }            
        }else{
            $uploadfile = wp_handle_upload( $_FILES['uploadfile'], array( 'test_form' => false, 'mimes' => array('csv' => 'text/csv') ) );
    
            if ( !$uploadfile || isset( $uploadfile['error'] ) ) {
                wp_die( __( 'Problem uploading file to import. Error details: ' . var_export( $uploadfile['error'], true ), 'kwf-importer' ));
            } else {
                $this->import_groups( $uploadfile['file'], $form_data, TPRM_importer_Helper::get_attachment_id_by_url( $uploadfile['url'] ), $is_cron, $is_frontend );
            }
        }
    }

    function import_users( $file, $form_data, $attach_id = 0, $is_cron = false, $is_frontend = false ){
        if ( ! function_exists( 'get_editable_roles' ) ) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }
        ?>
        <div class="wrap">
            <h2><?php echo apply_filters( 'TPRM_importer_log_main_title', __('Importing users','kwf-importer') ); ?></h2>
            <?php
                @set_time_limit( 0 );
                
                do_action( 'before_TPRM_importer_import_users' );
                global $wpdb;
                $TPRM_importer_helper = new TPRM_importer_Helper();
                $restricted_fields = $TPRM_importer_helper->get_restricted_fields();
                $all_roles = array_keys( wp_roles()->roles );
                $editable_roles = array_keys( get_editable_roles() );
    
                $users_registered = array();
                $headers = array();
                $headers_filtered = array();
                $is_backend = !$is_frontend && !$is_cron;			
                
                $update_existing_users = isset( $form_data["update_existing_users"] ) ? sanitize_text_field( $form_data["update_existing_users"] ) : '';
    
                $role_default = isset( $form_data["role"] ) ? $form_data["role"] : array( '' );
                if( !is_array( $role_default ) )
                    $role_default = array( $role_default );
                array_walk( $role_default, 'sanitize_text_field' );
               
                $update_emails_existing_users = isset( $form_data["update_emails_existing_users"] ) ? sanitize_text_field( $form_data["update_emails_existing_users"] ) : 'yes';
                $update_roles_existing_users = isset( $form_data["update_roles_existing_users"] ) ? sanitize_text_field( $form_data["update_roles_existing_users"] ) : 'no';
                $update_allow_update_passwords = isset( $form_data["update_allow_update_passwords"] ) ? sanitize_text_field( $form_data["update_allow_update_passwords"] ) : 'yes';
                $empty_cell_action = isset( $form_data["empty_cell_action"] ) ? sanitize_text_field( $form_data["empty_cell_action"] ) : '';
                $delete_users_not_present = isset( $form_data["delete_users_not_present"] ) ? sanitize_text_field( $form_data["delete_users_not_present"] ) : '';
                $delete_users_assign_posts = isset( $form_data["delete_users_assign_posts"] ) ? sanitize_text_field( $form_data["delete_users_assign_posts"] ) : '';
                $delete_users_only_specified_role = isset( $form_data["delete_users_only_specified_role"] ) ? sanitize_text_field( $form_data["delete_users_only_specified_role"] ) : false;			
    
                $change_role_not_present = isset( $form_data["change_role_not_present"] ) ? sanitize_text_field( $form_data["change_role_not_present"] ) : '';
                $change_role_not_present_role = isset( $form_data["change_role_not_present_role"] ) ? sanitize_text_field( $form_data["change_role_not_present_role"] ) : '';
                
                if( $is_cron ){
                    $allow_multiple_accounts = ( get_option( "TPRM_importer_cron_allow_multiple_accounts" ) == "allowed" ) ? "allowed" : "not_allowed";
                }
                else {
                    $allow_multiple_accounts = ( empty( $form_data["allow_multiple_accounts"] ) ) ? "not_allowed" : sanitize_text_field( $form_data["allow_multiple_accounts"] );
                }
        
                // disable WordPress default emails if this must be disabled
              /*   if( !get_option('TPRM_importer_automatic_wordpress_email') ){ */
                    add_filter( 'send_email_change_email', function() { return false; }, 999 );
                    add_filter( 'send_password_change_email', function() { return false; }, 999 );
               /*  } */
    
                // action
                echo apply_filters( "TPRM_importer_log_header", "<h3>" . __('Ready to registers','kwf-importer') . "</h3>" );
                echo apply_filters( "TPRM_importer_log_header_first_row_explanation", "<p>" . __('First row represents the form of sheet','kwf-importer') . "</p>" );
    
                $row = 0;
                $positions = array();
                $errors = array();
                $errors_totals = array( 'notices' => 0, 'warnings' => 0, 'errors' => 0 );
                $results = array( 'created' => 0, 'updated' => 0, 'deleted' => 0 );
                $users_created = array();
                $users_updated = array();
                $users_deleted = array();
                $users_ignored = array();
    
                @ini_set( 'auto_detect_line_endings', TRUE );
    
                $delimiter = $TPRM_importer_helper->detect_delimiter( $file );
    
                $manager = new SplFileObject( $file );
                while ( $data = $manager->fgetcsv( $delimiter ) ):
                    $row++;

                    if( count( $data ) == 1 )
                        $data = $data[0];
                    
                    if( $data == NULL ){
                        break;
                    }
                    elseif( !is_array( $data ) ){
                        echo apply_filters( 'TPRM_importer_message_csv_file_bad_formed', __( 'CSV file seems to be bad formed. Please use LibreOffice to create and manage CSV to be sure the format is correct', 'kwf-importer') );
                        break;
                    }
        
                    for( $i = 0; $i < count($data); $i++ ){
                        $data[$i] = $TPRM_importer_helper->string_conversion( $data[$i] );
    
                        if( is_serialized( $data[$i] ) ) // serialized
                            $data[$i] = maybe_unserialize( $data[$i] );
                        elseif( strpos( $data[$i], "::" ) !== false ) // list of items
                            $data[$i] = TPRM_importer_Helper::get_array_from_cell( $data[$i] );                                              
                    }
                    
                    if( $row == 1 ):
                        $data = apply_filters( 'pre_TPRM_importer_import_header', $data );
    
                        // check min columns username - email - password
                        if( count( $data ) < 3 ){
                            echo "<div id='message' class='error'>" . __( 'File must contain at least 3 columns: username, email and password', 'kwf-importer' ) . "</div>";
                            break;
                        }
    
                        $i = 0;
                        $password_position = false;
                        $id_position = false;
                        
                        foreach ( $restricted_fields as $TPRM_importer_restricted_field ) {
                            $positions[ $TPRM_importer_restricted_field ] = false;
                        }
    
                        foreach( $data as $element ){
                            $headers[] = $element;
    
                            if( in_array( strtolower( $element ) , $restricted_fields ) )
                                $positions[ strtolower( $element ) ] = $i;
    
                            if( !in_array( strtolower( $element ), $restricted_fields ) )
                                $headers_filtered[] = $element;
    
                            $i++;
                        }
    
                        $columns = count( $data );
    
                        update_option( "TPRM_importer_columns", $headers_filtered );
    
                        $TPRM_importer_helper->basic_css();                        
                        $TPRM_importer_helper->print_table_header_footer( $headers );
                    else:
                        $data = apply_filters( 'pre_TPRM_importer_import_single_user_data', $data, $headers );
                        
                        if( count( $data ) != $columns ): // if number of columns is not the same that columns in header
                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, __( 'Row does not have the same columns than the header, we are going to ignore this row', 'kwf-importer') );
                            continue;
                        endif;
    
                        do_action( 'pre_TPRM_importer_import_single_user', $headers, $data );

                        $data = apply_filters( 'pre_TPRM_importer_import_single_user_data', $data, $headers );
    
                        $username = apply_filters( 'pre_TPRM_importer_import_single_user_username', $data[0] );
                        $data[0] = ( $username == $data[0] ) ? $username : sprintf( __( '<em>Converted to: %s</em>', 'kwf-importer' ), $username );
                        $email = apply_filters( 'pre_TPRM_importer_import_single_user_email', $data[1] );
                        $data[1] = ( $email == $data[1] ) ? $email : sprintf( __( '<em>Converted to: %s</em>', 'kwf-importer' ), $email );

                        $user_id = 0;
                        $password_position = $positions["password"];
                        $password_changed = false;

                        $password = ( $password_position === false ) ? wp_generate_password( apply_filters( 'TPRM_importer_auto_password_length', 12 ), apply_filters( 'TPRM_importer_auto_password_special_chars', true ), apply_filters( 'TPRM_importer_auto_password_extra_special_chars', false ) ) : $data[ $password_position ];
                        $role_position = $positions["role"];
                        $role = "";
                        $id_position = $positions["id"];
                        $id = ( empty( $id_position ) ) ? '' : $data[ $id_position ];
                        $created = true;
                        
                        if( $role_position === false ){
                            $role = $role_default;
                        }
                        else{
                            $roles_cells = explode( ',', $data[ $role_position ] );
                            
                            if( !is_array( $roles_cells ) )
                                $roles_cells = array( $roles_cells );
    
                            array_walk( $roles_cells, 'trim' );
                            
                            foreach( $roles_cells as $it => $role_cell )
                                $roles_cells[ $it ] = strtolower( $role_cell );
                            
                            $role = $roles_cells;
                        }

                        $no_role = ( $role == 'no_role' ) || in_array( 'no_role', $role );


                        if( !$no_role ){
                            if( ( !empty( $role ) || is_array( $role ) && empty( $role[0] ) ) && !empty( array_diff( $role, $all_roles ) ) && $update_roles_existing_users != 'no' ){
                                if( is_array( $role ) && empty( $role[0] ) )
                                    $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, sprintf( __( 'If you are upgrading roles, you must choose at least one role', 'kwf-importer' ), implode( ', ', $role ) ) );
                                else
                                    $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, sprintf( __( 'Some of the next roles "%s" does not exists', 'kwf-importer' ), implode( ', ', $role ) ) );
                                continue;
                            }
        
                            if ( ( !empty( $role ) || is_array( $role ) && empty( $role[0] ) ) && !empty( array_diff( $role, $editable_roles ) && $update_roles_existing_users != 'no' ) ){ // users only are able to import users with a role they are allowed to edit
                                $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, sprintf( __( 'You do not have permission to assign some of the next roles "%s"', 'kwf-importer' ), implode( ', ', $role ) ) );
                                $created = false;
                                continue;
                            }
                        }

                        if( !empty( $email ) && ( ( sanitize_email( $email ) == '' ) ) ){ // if email is invalid
                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'Invalid email "%s"', 'kwf-importer' ), $email ) );
                            $data[0] = __('Invalid Email','kwf-importer')." ($email)";
                            continue;
                        }
                        elseif( empty( $email ) ) {
                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  __( 'Email not specified', 'kwf-importer' ) );
                            $data[0] = __( 'Email not specified', 'kwf-importer' );
                            continue;
                        }
                        
                       // Update student credentials when an existing user is updated
                        $std_cred_tbl = $wpdb->prefix . "students_credentials";

                        // Check if the user already exists
                        $user_exists = $wpdb->get_row("SELECT * FROM $std_cred_tbl WHERE username = '$username'");

                        if($user_exists){
                            // Update the user details if the user already exists
                            $wpdb->update(
                                $std_cred_tbl,
                                array(
                                    'username' => $username,
                                    'stdcred' => $data[2],
                                    'first_name' => $data[3],
                                    'last_name' => $data[4],
                                ),
                                array( 'username' => $username )
                            );
                        } else {
                            // Insert the user if not exists ( created )
                            $wpdb->insert( $std_cred_tbl, array(
                                'username' => $username,
                                'stdcred' => $data[2],
                                'first_name' => $data[3],
                                'last_name' => $data[4],
                            ));
                        }
    
                        if( !empty( $id ) ){ // if user have used id
                            if( $TPRM_importer_helper->user_id_exists( $id ) ){
                                if( $update_existing_users == 'no' ){
                                    $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'User with ID "%s" exists, we ignore it', 'kwf-importer' ), $id ), 'notice' );
                                    array_push( $users_ignored, $id );                                    
                                    continue;
                                }
    
                                // we check if username is the same than in row
                                $user = get_user_by( 'ID', $id );
    
                                if( $user->user_login == $username ){
                                    $user_id = $id;
                                    
                                    if( $password !== "" && $update_allow_update_passwords == 'yes' ){
                                        wp_set_password( $password, $user_id );
                                        $password_changed = true;
                                    }
    
                                    $new_user_id = $TPRM_importer_helper->maybe_update_email( $user_id, $email, $password, $update_emails_existing_users );
                                    if( empty( $new_user_id ) ){
                                        $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'User with email "%s" exists, we ignore it', 'kwf-importer' ), $email ), 'notice' );
                                        array_push( $users_ignored, $new_user_id );
                                        continue;
                                    }
                                    
                                    if( is_wp_error( $new_user_id ) ){
                                        $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  $new_user_id->get_error_message() );     
                                        $data[0] = $new_user_id->get_error_message();
                                        $created = false;
                                    }
                                    elseif( $new_user_id == $user_id)
                                        $created = false;
                                    else{
                                        $user_id = $new_user_id;
                                        $new_user = get_user_by( 'id', $new_user_id );
                                        $data[0] = sprintf( __( 'Email has changed, new user created with username %s', 'kwf-importer' ), $new_user->user_login );
                                        $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  $data[0], 'notice' );
                                        $created = true;

                                        
                                    }
                                }
                                else{
                                    $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'Problems with ID "%s" username is not the same in the CSV and in database', 'kwf-importer' ), $id ) );
                                    continue;
                                }
                            }
                            else{
                                $user_id = wp_insert_user( array(
                                    'ID'		  =>  $id,
                                    'user_login'  =>  $username, // $data[0]
                                    'user_email'  =>  $email,
                                    'user_pass'   =>  $password
                                ) );

                                $created = true;
                                $password_changed = true;

                            }
                        }
                        elseif( username_exists( $username ) ){
                            $user_object = get_user_by( "login", $username );
                            $user_id = $user_object->ID;

                            if( $update_existing_users == 'no' ){
                                $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'User with username "%s" exists, we ignore it', 'kwf-importer' ), $username ), 'notice' );
                                array_push( $users_ignored, $user_id );
                                continue;
                            }
                            
                            if( $password !== "" && $update_allow_update_passwords == 'yes' ){
                                wp_set_password( $password, $user_id );
                                $password_changed = true;
                            }

                         $new_user_id = $TPRM_importer_helper->maybe_update_email( $user_id, $email, $password, $update_emails_existing_users );
                            if( empty( $new_user_id ) ){
                                $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'User with email "%s" exists with other username, we ignore it', 'kwf-importer' ), $email ), 'notice' );     
                                array_push( $users_ignored, $new_user_id );
                                continue;
                            }
                            
                            if( is_wp_error( $new_user_id ) ){
                                $data[0] = $new_user_id->get_error_message();
                                $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  $data[0] );
                                $created = false;
                            }
                            elseif( $new_user_id == $user_id)
                                $created = false;
                            else{
                                $user_id = $new_user_id;
                                $new_user = get_user_by( 'id', $new_user_id );
                                $data[0] = sprintf( __( 'Email has changed, new user created with username %s', 'kwf-importer' ), $new_user->user_login );
                                $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  $data[0], 'warning' );     
                                $created = true;
                            }
                        }
                        elseif( email_exists( $email ) && $allow_multiple_accounts == "not_allowed" ){ // if the email is registered, we take the user from this and we don't allow repeated emails
                            if( $update_existing_users == 'no' ){
                                array_push( $users_ignored, $user_id );
                                $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, sprintf( __( 'The email %s already exists in the system but is used by a different user than the one indicated in the CSV', 'kwf-importer' ), $email ), 'warning' );
                                continue;
                            }
    
                            $user_object = get_user_by( "email", $email );
                            $user_id = $user_object->ID;
                            
                            $data[0] = sprintf( __( 'User already exists as: %s (in this CSV file is called: %s)', 'kwf-importer' ), $user_object->user_login, $username );
                            array_push( $users_ignored, $user_id );
                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, $data[0], 'warning' );
    
                            if( $password !== "" && $update_allow_update_passwords == 'yes' ){
                                wp_set_password( $password, $user_id );
                                $password_changed = true;
                            }
    
                            $created = false;
                        }
                        elseif( email_exists( $email ) && $allow_multiple_accounts == "allowed" ){ // if the email is registered and repeated emails are allowed
                            // if user is new, but the password in csv is empty, generate a password for this user
                            if( $password === "" ) {
                                $password = wp_generate_password( apply_filters( 'TPRM_importer_auto_password_length', 12 ), apply_filters( 'TPRM_importer_auto_password_special_chars', true ), apply_filters( 'TPRM_importer_auto_password_extra_special_chars', false ) );
                            }
                            
                            $hacked_email = TPRM_importer_AllowMultipleAccounts::hack_email( $email );
                            $user_id = wp_create_user( $username, $password, $hacked_email );
                            TPRM_importer_AllowMultipleAccounts::hack_restore_remapped_email_address( $user_id, $email );
                        }
                        else{
                            // if user is new, but the password in csv is empty, generate a password for this user
                            if( $password === "" ) {
                                $password = wp_generate_password( apply_filters( 'TPRM_importer_auto_password_length', 12 ), apply_filters( 'TPRM_importer_auto_password_special_chars', true ), apply_filters( 'TPRM_importer_auto_password_extra_special_chars', false ) );
                            }
                            
                            $user_id = wp_create_user( $username, $password, $email );
                            $password_changed = true;
                        }                            
                        if( is_wp_error( $user_id ) ){ // in case the user is generating errors after this checks
                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, sprintf( __( 'Problems with user: "%s" does not exists, error: %s', 'kwf-importer' ), $username, $user_id->get_error_message() ) );
                            continue;
                        }
    
                        $users_registered[] = $user_id;
                        $user_object = new WP_User( $user_id );                  
    
                        if( $created || $update_roles_existing_users != 'no' ){
                            
                            if( empty( array_intersect( apply_filters( 'TPRM_importer_protected_roles', array( 'administrator' ) ), $TPRM_importer_helper->get_roles_by_user_id( $user_id ) ) ) || is_multisite() && is_super_admin( $user_id ) ){
                                if( $update_roles_existing_users == 'yes' || $created ){
                                    $default_roles = $user_object->roles;
                                    foreach ( $default_roles as $default_role ) {
                                        $user_object->remove_role( $default_role );
                                    }
                                }

                                if( !$no_role && ( $update_roles_existing_users == 'yes' || $update_roles_existing_users == 'yes_no_override' || $created ) ){
                                    if( !empty( $role ) ){
                                        if( is_array( $role ) ){
                                            foreach ($role as $single_role) {
                                                $user_object->add_role( $single_role );                                             
                                            }
                                        }
                                        else{
                                            $user_object->add_role( $role );
                                        }
                                    }

                                    $invalid_roles = array();
                                    if( !empty( $role ) ){
                                        if( !is_array( $role ) ){
                                            $role_tmp = $role;
                                            $role = array();
                                            $role[] = $role_tmp;
                                        }
                                        
                                        foreach ($role as $single_role) {
                                            $single_role = strtolower($single_role);
                                            if( get_role( $single_role ) ){
                                                $user_object->add_role( $single_role );
                                            }
                                            else{
                                                $invalid_roles[] = trim( $single_role );
                                            }
                                        }
                                    }
    
                                    if ( !empty( $invalid_roles ) ){
                                        if( count( $invalid_roles ) == 1 )
                                            $data[0] = __('Invalid role','kwf-importer').' (' . reset( $invalid_roles ) . ')';
                                        else
                                            $data[0] = __('Invalid roles','kwf-importer').' (' . implode( ', ', $invalid_roles ) . ')';
                                
                                        $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, $data[0], 'warning' );    
                                    }
                                }
                            }
                            
                            $std_cred_tbl = $wpdb->prefix . "students_credentials";
                            // Check if the user already exists
                            $user_exists = $wpdb->get_row("SELECT * FROM $std_cred_tbl WHERE username = '$username'");

                            if($user_exists){
                                // Update the user details if the user already exists
                                $wpdb->update(
                                    $std_cred_tbl,
                                    array(
                                        'username' => $username,
                                        'stdcred' => $data[2],
                                        'first_name' => $data[3],
                                        'last_name' => $data[4],
                                    ),
                                    array( 'username' => $username )
                                );
                            } else {
                                // Insert the user if not exists
                                $wpdb->insert( $std_cred_tbl, array(
                                    'username' => $username,
                                    'stdcred' => $data[2],
                                    'first_name' => $data[3],
                                    'last_name' => $data[4],
                                ));
                            }
                        }
    
                        // Multisite add user to current blog
                        if( is_multisite() ){
                            if( $created || $update_roles_existing_users != 'no' ){
                                if( empty( $role ) )
                                    $role = 'subscriber';

                                if( !is_array( $role ) )
                                    $role = array( $role );

                                foreach( $role as $single_role )
                                    add_user_to_blog( get_current_blog_id(), $user_id, $single_role );
                            }
                            elseif( $update_roles_existing_users == 'no' && !is_user_member_of_blog( $user_id, get_current_blog_id() ) ){
                                add_user_to_blog( get_current_blog_id(), $user_id, 'subscriber' );
                            }                            
                        }
    
                        if( $columns > 2 ){
                            for( $i = 2 ; $i < $columns; $i++ ):
                                $data[$i] = apply_filters( 'pre_TPRM_importer_import_single_user_single_data', $data[$i], $headers[$i], $i );
    
                                if( !empty( $data ) ){
                                    if( strtolower( $headers[ $i ] ) == "password" ){ // passwords -> continue
                                        continue;
                                    }
                                    elseif( strtolower( $headers[ $i ] ) == "user_pass" ){ // hashed pass
                                        if( !$created && $update_allow_update_passwords == 'no' )
                                            continue;
    
                                        global $wpdb;
                                        $wpdb->update( $wpdb->users, array( 'user_pass' => wp_slash( $data[ $i ] ) ), array( 'ID' => $user_id ) );
                                        wp_cache_delete( $user_id, 'users' );
                                        continue;
                                    }
                                    elseif( in_array( $headers[ $i ], $TPRM_importer_helper->get_wp_users_fields() ) ){ // wp_user data									
                                        if( $data[ $i ] === '' && $empty_cell_action == "leave" ){
                                            continue;
                                        }
                                        else{
                                            wp_update_user( array( 'ID' => $user_id, $headers[ $i ] => $data[ $i ] ) );
                                            continue;
                                        }										
                                    }
                                    elseif( in_array( $headers[ $i ], $TPRM_importer_helper->get_not_meta_fields() ) ){
                                        continue;
                                    }
                                    else{				
                                        if( $data[ $i ] === '' ){
                                            if( $empty_cell_action == "delete" )
                                                delete_user_meta( $user_id, $headers[ $i ] );
                                            else
                                                continue;	
                                        }
                                        else{
                                            update_user_meta( $user_id, $headers[ $i ], $data[ $i ] );
                                            continue;
                                        }
                                    }
    
                                }
                            endfor;
                        }

                        $TPRM_importer_helper->print_row_imported( $row, $data, $errors );
    
                        do_action( 'post_TPRM_importer_import_single_user', $headers, $data, $user_id, $role, $positions, $form_data, $is_frontend, $is_cron, $password_changed, $created );
    
                        $mail_for_this_user = false;
                        if( $is_cron ){
                            if( get_option( "TPRM_importer_cron_send_mail" ) ){
                                if( $created || ( !$created && get_option( "TPRM_importer_cron_send_mail_updated" ) ) ){
                                    $mail_for_this_user = true;
                                }							
                            }
                        }
                        else{
                            if( isset( $form_data["sends_email"] ) && $form_data["sends_email"] ){
                                if( $created || ( !$created && ( isset( $form_data["send_email_updated"] ) && $form_data["send_email_updated"] ) ) )
                                    $mail_for_this_user = true;
                            }
                        }
    
                        // wordpress default user created and edited emails
                        if( get_option('TPRM_importer_automatic_created_edited_wordpress_email') === 'true' ){
                            ( $created ) ? do_action( 'register_new_user', $user_id ) : do_action( 'edit_user_created_user', $user_id, 'both' );
                        }
                            
                        // send mail
                        $mail_for_this_user = apply_filters( 'TPRM_importer_send_email_for_user', $mail_for_this_user, $headers, $data, $user_id, $role, $positions, $form_data, $is_frontend, $is_cron, $password_changed );

                        if( isset( $mail_for_this_user ) && $mail_for_this_user ){
                            if( !$created && $update_allow_update_passwords == 'no' )
                                $password = __( 'Password has not been changed', 'kwf-importer' );

                            TPRM_importer_Email_Options::send_email( $user_object, $positions, $headers, $data, $created, $password );
                        }

                        // results
                        ( $created ) ? $results['created']++ : $results['updated']++;
                        ( $created ) ? array_push( $users_created, $user_id ) : array_push( $users_updated, $user_id );
                    endif;
                endwhile;

                $TPRM_importer_helper->print_table_end();

                $TPRM_importer_helper->print_errors( $errors );

                // let the filter of default WordPress emails as it were before deactivating them
                if( !get_option('TPRM_importer_automatic_wordpress_email') ){
                    remove_filter( 'send_email_change_email', function() { return false; }, 999 );
                    remove_filter( 'send_password_change_email', function() { return false; }, 999 );
                }
    
                if( $attach_id != 0 )
                    wp_delete_attachment( $attach_id );
    
                // delete all users that have not been imported
                $delete_users_flag = false;
                $change_role_not_present_flag = false;
    
                if( $delete_users_not_present == 'yes' ){
                    $delete_users_flag = true;
                }
    
                if( $is_cron && get_option( "TPRM_importer_cron_delete_users" ) ){
                    $delete_users_flag = true;
                    $delete_users_assign_posts = get_option( "TPRM_importer_cron_delete_users_assign_posts");
                }
    
                if( $is_backend && $change_role_not_present == 'yes' ){
                    $change_role_not_present_flag = true;
                }
    
                if( $is_cron && !empty( get_option( "TPRM_importer_cron_change_role_not_present" ) ) ){
                    $change_role_not_present_flag = true;
                    $change_role_not_present_role = get_option( "TPRM_importer_cron_change_role_not_present_role");
                }
    
                if( $is_frontend && !empty( get_option( "TPRM_importer_frontend_change_role_not_present" ) ) ){
                    $change_role_not_present_flag = true;
                    $change_role_not_present_role = get_option( "TPRM_importer_frontend_change_role_not_present_role");
                }
    
                if( $errors_totals['errors'] > 0 || $errors_totals['warnings'] > 0 ){ // if there is some problem of some kind importing we won't proceed with delete or changing role to users not present to avoid problems
                    $delete_users_flag = false;
                    $change_role_not_present_flag = false;
                }
    
                if( $delete_users_flag ):
                    require_once( ABSPATH . 'wp-admin/includes/user.php');	
    
                    global $wp_roles; // get all roles
                    $all_roles = $wp_roles->roles;
                    $exclude_roles = array_diff( array_keys( $all_roles ), $editable_roles ); // remove editable roles
    
                    if ( !in_array( 'administrator', $exclude_roles )){ // just to be sure
                        $exclude_roles[] = 'administrator';
                    }
    
                    $args = array( 
                        'fields' => array( 'ID' ),
                        'role__not_in' => $exclude_roles,
                        'exclude' => array( get_current_user_id() ), // current user never cannot be deleted
                    );
    
                    if( $delete_users_only_specified_role ){
                        $args[ 'role__in' ] = $role_default;
                    }
    
                    $all_users = get_users( $args );
                    $all_users_ids = array_map( function( $element ){ return intval( $element->ID ); }, $all_users );
                    $users_to_remove = array_diff( $all_users_ids, $users_registered );
    
                    $delete_users_assign_posts = ( get_userdata( $delete_users_assign_posts ) === false ) ? false : $delete_users_assign_posts;
                    $results['deleted'] = count( $users_to_remove );
    
                    foreach ( $users_to_remove as $user_id ) {
                        ( empty( $delete_users_assign_posts ) ) ? wp_delete_user( $user_id ) : wp_delete_user( $user_id, $delete_users_assign_posts );
                        array_push( $users_deleted, $user_id );
                    }
                endif;
    
                if( $change_role_not_present_flag && !$delete_users_flag ):
                    require_once( ABSPATH . 'wp-admin/includes/user.php');	
    
                    $all_users = get_users( array( 
                        'fields' => array( 'ID' ),
                        'role__not_in' => array( 'administrator' )
                    ) );
                    
                    foreach ( $all_users as $user ) {
                        if( !in_array( $user->ID, $users_registered ) ){
                            $user_object = new WP_User( $user->ID );
                            $user_object->set_role( $change_role_not_present_role );
                        }
                    }
                endif;
                
                $TPRM_importer_helper->print_results( $results, $errors );
                
                if( !$is_frontend )
                    $TPRM_importer_helper->print_end_of_process();

                if( !$is_frontend && !$is_cron )
                    $TPRM_importer_helper->execute_datatable();

                @ini_set( 'auto_detect_line_endings', FALSE );
                
                do_action( 'after_TPRM_importer_import_users' ); // deprecated this hook will be changed by the next one
                do_action( 'TPRM_importer_after_import_users', $users_created, $users_updated, $users_deleted, $users_ignored );
            ?>
        </div>
    <?php
    } //end import_users

    function import_groups( $file, $form_data, $attach_id = 0, $is_cron = false, $is_frontend = false ){

        # Check whether BP is active and whether Groups component is loaded, and throw error if not
        if( !is_plugin_active( 'buddypress/bp-loader.php' ) && !function_exists( 'bp_is_active' ) || !(function_exists('BuddyPress') || is_a($bp,'BuddyPress')) || !bp_is_active('groups') ) {
            _e ('BuddyPress is not installed or the Groups component is not activated. Cannot continue.','kwf-importer');
            exit;
        }
        $TPRM_bp_path = is_plugin_active( 'buddyboss-platform/bp-loader.php' ) ? WP_PLUGIN_DIR . "/buddyboss-platform/" : WP_PLUGIN_DIR . "/buddypress/";

        if( !class_exists( 'BP_Groups_Group' ) ) require_once( $TPRM_bp_path . "bp-groups/classes/class-bp-groups-group.php" );
        ?>
        <div class="wrap">
            <h2><?php echo apply_filters( 'TPRM_importer_log_main_title', __('Importing groups','kwf-importer') ); ?></h2>
            <?php
                @set_time_limit( 0 );
                
                do_action( 'before_TPRM_importer_import_groups' );

                $TPRM_importer_helper = new TPRM_importer_Helper();
                $TPRM_bp_group = new BP_Groups_Group();
                $restricted_fields = $TPRM_importer_helper->get_restricted_fields();

                global $bp, $wpdb, $classes, $classe_slugs, $curriculums, $precurriculum, $parent_groups, $ecole, $school_lang, $school_slug, $rows;

                $classes = array();
                $classe_slugs = array();
                $curriculums = array();
                $parent_groups = array();
                $rows = array();              
                $headers = array();
                $headers_filtered = array();
                $is_backend = !$is_frontend && !$is_cron;			  
                $row = 0;
                $positions = array();
                $errors = array();
                $errors_totals = array( 'notices' => 0, 'warnings' => 0, 'errors' => 0 );
                $results = array( 'created' => 0, 'updated' => 0, 'deleted' => 0 );
                $ecole_results = array( 'created' => 0, 'updated' => 0, 'deleted' => 0 );

                $groups_created = array();
                $groups_updated = array();
                $groups_deleted = array();
                $groups_ignored = array();

                $ecole_created = array();
                $ecole_updated = array();
                $ecole_deleted = array();
                $ecole_ignored = array();

                $school_creation_year = get_option('school_year');
                $school_permalink = '';
                  
                @ini_set( 'auto_detect_line_endings', TRUE );
    
                $delimiter = $TPRM_importer_helper->detect_delimiter( $file );
    
                $manager = new SplFileObject( $file );
                while ( $data = $manager->fgetcsv( $delimiter ) ):
                    $row++;

                    if( count( $data ) == 1 )
                        $data = $data[0];
                    
                    if( $data == NULL ){
                        break;
                    }
                    elseif( !is_array( $data ) ){
                        echo apply_filters( 'TPRM_importer_message_csv_file_bad_formed', __( 'CSV file seems to be bad formed. Please use LibreOffice to create and manage CSV to be sure the format is correct', 'kwf-importer') );
                        break;
                    }
        
                    for( $i = 0; $i < count($data); $i++ ){
                        $data[$i] = $TPRM_importer_helper->string_conversion( $data[$i] );
    
                        if( is_serialized( $data[$i] ) ) // serialized
                            $data[$i] = maybe_unserialize( $data[$i] );
                        elseif( strpos( $data[$i], "::" ) !== false ) // list of items
                            $data[$i] = TPRM_importer_Helper::get_array_from_cell( $data[$i] );                                              
                    }
                  
                    
                    if( $row == 1 ):
                        // check columns classe - curriculum
                        if( count( $data ) < 1 ){
                            echo "<div id='message' class='error'>" . __( 'File must contain exactly 4 columns: ecole, classe, year and curriculum', 'kwf-importer' ) . "</div>";
                            break;
                        }
    
                        $i = 0;
                            
                        foreach ( $restricted_fields as $TPRM_importer_restricted_field ) {
                            $positions[ $TPRM_importer_restricted_field ] = false;
                        }
    
                        foreach( $data as $element ){
                            $headers[] = $element;
    
                            if( in_array( strtolower( $element ) , $restricted_fields ) )
                                $positions[ strtolower( $element ) ] = $i;
    
                            if( !in_array( strtolower( $element ), $restricted_fields ) )
                                $headers_filtered[] = $element;
    
                            $i++;
                        }
    
                        $columns = count( $data );
    
                        update_option( "TPRM_importer_columns", $headers_filtered );
    
                        $TPRM_importer_helper->basic_css();                        
                        $TPRM_importer_helper->print_table_header_footer( $headers );
                                                      

                    else:
               
                        if( count( $data ) != $columns ): // if number of columns is not the same that columns in header
                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals, __( 'Row does not have the same columns than the header, we are going to ignore this row', 'kwf-importer') );
                            continue;
                        endif;
    
                        do_action( 'pre_TPRM_importer_import_single_group', $headers, $data );

                        $gcreated = false;

                        $group_count = 0;
                        $existing_group_count = 0;
                        $i=0;                   

                        $data = apply_filters( 'pre_TPRM_importer_import_single_group_data', $data, $headers );

                        /* **** start ecole ***** */

                        if( $row == 2 && !empty($data[0]) && !empty($data[1]) && !empty($data[2]) ):
                            // Data[0] = ecole
                            // Extract data from $data
                            $school_name = apply_filters('pre_TPRM_importer_import_single_classe', $data[0]);
                            $data[0] = ($school_name == $data[0]) ? $school_name : sprintf(__('<em>Converted to: %s</em>', 'kwf-importer'), $school_name);
                   
                            $school_lang = apply_filters('pre_TPRM_importer_import_single_lang', $data[1]);
                            $data[1] = ($school_lang == $data[1]) ? $school_lang : sprintf(__('<em>Converted to: %s</em>', 'kwf-importer'), $school_lang);

                            $school_type = apply_filters('pre_TPRM_importer_import_single_lang', $data[2]);
                            $data[2] = ($school_type == $data[2]) ? $school_type : sprintf(__('<em>Converted to: %s</em>', 'kwf-importer'), $school_type);

                            $school_trigram = apply_filters('pre_TPRM_importer_import_single_lang', $data[3]);
                            $data[3] = ($school_trigram == $data[3]) ? $school_trigram : sprintf(__('<em>Converted to: %s</em>', 'kwf-importer'), $school_trigram);

                            $school_slug = sanitize_title_with_dashes(esc_attr($school_name));

                            // create ecole
                            $ecole_search_args = array(
                                'slug' => $school_slug
                            );

                            $args = array(
                                'name'     => $school_name,
                                'slug'     => groups_check_slug($school_slug),
                                'status'   => 'hidden',
                            );

                            $existing_ecole = groups_get_groups($ecole_search_args);

                            $existing_school_id = $TPRM_bp_group::group_exists($school_slug);

                            # create school group only if it doesn't already exist
                           /*  if ($existing_school_id) {
                                $errors[] = $TPRM_importer_helper->new_error($row - 1, $errors_totals, sprintf(__('The School called "%s" with ID "%s" exists, it has been updated with new classes', 'kwf-importer'), $school_name, $existing_school_id ), 'notice');
                                array_push($groups_updated, $existing_school_id);
                                continue;
                            } else {
                                $new_school_id = groups_create_group($args);
                                $gcreated = true;
                            } */

                            $new_school_id = groups_create_group($args);
                            $gcreated = true;

                            # ecole created successfully
                            if (!empty($new_school_id)) {

                                $args = array(
                                    'meta_query' => array(
                                        array(
                                            'key' => '_sync_group_id',
                                            'value' => $new_school_id
                                        )
                                    ),
                                    'post_type' => 'groups',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1
                                );

                                $bb_ecole_groupid = get_posts($args);
                                if (!empty($bb_ecole_groupid)) {
                                    $ld_ecole_id = $bb_ecole_groupid[0]->ID;

                                     // Modify the post's "guid" to match $classe_slug
                                    //$school_permalink = home_url() . 'groups/' . $school_slug . '/';
                                    $school_permalink = site_url('/' . $school_lang . '/groupes/' . $school_slug . '/');
                                    wp_update_post(array(
                                        'ID' => $ld_ecole_id,
                                        'guid' => $school_permalink,
                                        'post_name' => $school_slug,
                                    ));

                                    /** insert ecole ( group type ) **/
                                    $gptypeargs = array(
                                        'meta_query' => array(
                                            array(
                                                'key' => '_bp_group_type_key',
                                                'value' => 'kwf-ecole'
                                                /* 'key' => '_bp_group_type_label_singular_name',
                                                'value' => 'School group' */
                                            )
                                        ),
                                        'post_type' => 'bp-group-type',
                                        'post_status' => 'publish',
                                        'posts_per_page' => -1
                                    );
                               
                                    $grptypepost = get_posts($gptypeargs);

                                    if (!empty($grptypepost)) {
                                        $gptypepostid = $grptypepost[0]->ID;
                                        $gptypeslug = get_post_meta($gptypepostid, '_bp_group_type_key', true);
                                        $termobject = wp_set_post_terms($new_school_id, $gptypeslug, 'bp_group_type', false);
                                    }

                                    //school
                                    $base_code = $school_trigram . $ld_ecole_id . $school_slug;
                                    $school_code = substr(md5($base_code), 0, 10);
                                    
                                    # set invite status
                                    groups_update_groupmeta($new_school_id, 'invite_status', 'admins');
                                    # en or fr
                                    groups_update_groupmeta($new_school_id, 'ecole_lang', $school_lang);
                                    # paid or impaid
                                    groups_update_groupmeta($new_school_id, 'school_type', $school_type);
                                    # generated school trigram
                                    groups_update_groupmeta($new_school_id, 'school_trigram', $school_trigram);
                                    # generated school code
                                    groups_update_groupmeta($new_school_id, 'school_code', $school_code);
                                    # school year ( gotten from get_option('school_year'))
                                    groups_update_groupmeta($new_school_id, 'school_creation_year', $school_creation_year);

                                }

                            } else {
                                echo sprintf('Cannot create School %s, probably a temporary mysql error', $school_name);
                                exit;
                            }

                            // results
                            ($gcreated) ? $ecole_results['created']++ : $ecole_results['updated']++;
                            ($gcreated) ? array_push($ecole_created, $new_school_id) : array_push($ecole_updated, $new_school_id);
                            

                        endif;       

                        // endif end create group ecole

                        /* **** end ecole ***** */

                         /* **** start classes ***** */

                        // Data[1] = classes
                         $classe_name = apply_filters( 'pre_TPRM_importer_import_single_classe', $data[4] );
                         $data[4] = ( $classe_name == $data[4] ) ? $classe_name : sprintf( __( '<em>Converted to: %s</em>', 'kwf-importer' ), $classe_name );                   

                         // Modify the slug to include 'school_name' and 'ecole_year'
                        $classe_slug = sanitize_title_with_dashes(esc_attr($classe_name . '-' . $school_name . '-' . $school_creation_year));

                        $classroom_name = $classe_name . ' ' . $school_name;
                        // create classes
                        $group_search_args = array(
                            'slug' => $classe_slug
                        );
                        
                        $args = array (
                            'name'          => $classroom_name,
                            'slug'          => groups_check_slug($classe_slug),
                            'status'        => 'hidden',
                        );

                        $existing_group = groups_get_groups($group_search_args);

                        $existing_group_id = $TPRM_bp_group::group_exists($classe_slug);

                        # create group only if it doesn't already exist
                        /* //TODO : handle if we want to add new classes to already existed school ( at first line )
                        if($existing_group_id){
                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'Classe with ID "%s" exists, we ignore it', 'kwf-importer' ), $existing_group_id ), 'notice' );
                            array_push( $groups_ignored, $existing_group_id );
                            continue;
                        }
                        else {
                            $new_group_id = groups_create_group ($args);
                        } */

                        # create group only if it doesn't already exist
                        if ($existing_group_id) {

                            $errors[] = $TPRM_importer_helper->new_error( $row, $errors_totals,  sprintf( __( 'Classe called "%s" with ID "%s" already exists under the school "%s", we ignore it', 'kwf-importer' ), $classe_name, $existing_group_id, $school_name ), 'notice' );
                            array_push( $groups_ignored, $existing_group_id );
                           // continue;
                            // Ecole already exists, so we need to attach the class to it.
                            $new_group_id = $existing_group_id;

                            # set ecole lang
                            //groups_update_groupmeta($new_school_id, 'ecole_lang', $school_lang );

                            // Update ecole year if needed
                            //groups_update_groupmeta($new_school_id, 'ecole_year', $school_creation_year );

                        } else {
                            // Ecole doesn't exist, so create it.
                            $new_group_id = groups_create_group($args);
                        }
              
                        # group created successfully
                        if (!empty($new_group_id)) {
                                       
                            $args = array(
                                'meta_query' => array(
                                    array(
                                        'key' => '_sync_group_id',
                                        'value' => $new_group_id,
                                    )
                                ),
                                'post_type' => 'groups',
                                'post_status' => 'publish',
                                'posts_per_page' => -1,
                            );
                            $bbgroupid = get_posts($args);
                
                            if( !empty($bbgroupid) ){
                                $ldgroupid = $bbgroupid[0]->ID;

                                // Modify the post's "guid" to match $classe_slug
                                $new_guid = home_url() . '/groups/' . $classe_slug . '/';
                                wp_update_post(array(
                                    'ID' => $ldgroupid,
                                    'guid' => $new_guid,
                                    'post_name' => $classe_slug,
                                ));
                                                        
                                /** insert curriculum ( group type ) **/
                                
                               /*  if( !empty($curriculum) ){
                                    $gptypeargs = array(
                                                    'meta_query' => array(
                                                        array(
                                                            'key' => '_bp_group_type_key',
                                                            'value' => $curriculum
                                                        )
                                                    ),
                                                    'post_type' => 'bp-group-type',
                                                    'post_status' => 'publish',
                                                    'posts_per_page' => -1
                                                );
                                                
                                    $grptypepost = get_posts($gptypeargs);
                                    
                                    if( !empty($grptypepost) ){
                                        $gptypepostid = $grptypepost[0]->ID;
                                        $gptypeslug = get_post_meta( $gptypepostid , '_bp_group_type_key' , true );
                                        $termobject = wp_set_post_terms( $new_group_id, $gptypeslug , 'bp_group_type' , false );
                                    } 
                                } */
            
                                /** insert ecole ( group parent ) **/
                                                  
                                if( !empty($school_slug) ){
                                    $getparent = $wpdb->get_results("select * from ".$wpdb->prefix."bp_groups where slug='".$school_slug."'");                                       
                                    if( !empty($getparent) ){
                                        $gpparentid = $getparent[0]->id;
                                        $updategp = $wpdb->update( $wpdb->prefix.'bp_groups' , array('parent_id'=>$gpparentid),array('id'=>$new_group_id) );
                                    }
                                }
     
                            }
                            
                            # set invite status
                            groups_update_groupmeta($new_group_id, 'invite_status', 'admins' );

                        }
                        else {
                            echo sprintf( 'Cannot create classe, probably a temporary mysql error');
                            exit;
                        }// endif end create group
        
                        if ($existing_group_count != 0  ) {
                            $error = '<div class="error settings-error" id="setting-error"><p><strong>' .sprintf ( 'Total %d classes are already found with the same name.', $existing_group_count ) .'</strong></p></div>';
                        }

                        $TPRM_importer_helper->print_row_imported( $row, $data, $errors );
    
                        do_action( 'post_TPRM_importer_import_single_group', $headers, $data, $positions, $form_data, $is_frontend, $is_cron, $gcreated );

                        // results
                        
                        ( $gcreated ) ? $results['created']++ : $results['updated']++;
                        ( $gcreated ) ? array_push( $groups_created, $new_group_id ) : array_push( $groups_updated, $new_group_id );

                    endif;
                endwhile;

                //if ( !empty($school_name) && !empty($school_creation_year) && !empty($school_slug) && !empty($school_lang) && !empty($classe_slug) ){
                            
                    $TPRM_ecole = [
                        ['<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>Nom de l’école</center></middle></style>',
                        '<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>Année</center></middle></style>',
                        '<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>Slug</center></middle></style>',
                        '<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>Langue</center></middle></style>',
                        '<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>School Code</center></middle></style>',
                        '<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>Permalink</center></middle></style>',
                        '<style height="40" bgcolor="#1c5cb2" color="#ffffff"><middle><center>School Trigram</center></middle></style>',
                        ],        
                        ['<style height="30"><middle><center> ' . $school_name . '</center></middle></style>',
                        '<style height="30"><middle><center> ' . $school_creation_year . '</center></middle></style>',
                        '<style height="30" bgcolor="#a1cecc" color="#000000"><middle><center> ' . $school_slug . '</center></middle></style>',
                        '<style height="30"><middle><center> ' . $school_lang . '</center></middle></style>',
                        '<style height="30"><middle><center> ' . $school_code . '</center></middle></style>',
                        '<style height="30" bgcolor="#a1cecc" color="#000000"><middle><center> ' . $school_permalink . '</center></middle></style>',
                        '<style height="30"><middle><center> ' . $school_trigram . '</center></middle></style>',
                        ],         
                    ];                     

                    // Get the current user's ID
                    $current_user_id = get_current_user_id();

                    // Get the user data for the current user
                    $current_user = get_userdata($current_user_id);

                    // Author (current user's name and email)
                    $author = $current_user->display_name . ' <' . $current_user->user_email . '>';

                    // Last Modified By (same as author)
                    $lastModifiedBy = $author;

                    $school_name_formatted = strtolower(str_replace(' ', '_', $school_name));

                    $company = 'tepunareomaori <kiaora@tepunareomaori.co.nz>';

                    $title = sprintf( __( 'Import school : %s %s at %s', 'tprm-theme' ), $school_name, date('d-m-Y'), date('H:i:s') );

                    $description = sprintf( __( 'Excel file contains the data of the school %s', 'tprm-theme' ), $school_name );

                    $filename = 'import_ecole_' . $school_name_formatted . '_' . date('Y_m_d') . '-' . str_replace(':', '_', date('H:i:s')) . '.xlsx';

                    // Get the WordPress upload directory path
                    $upload_dir = wp_upload_dir();

                    // Specify the file path within the upload directory
                    $excel_file_path = $upload_dir['basedir'] . '/' . $filename;

                    // Save the Excel file to the upload directory
                       
                    $xlsx = new SimpleXLSXGen();
                    $xlsx->setAuthor($author)
                    ->setCompany($company)
                    ->setManager($lastModifiedBy)
                    ->setLastModifiedBy($lastModifiedBy)
                    ->setTitle($title)
                    ->setDescription($description)
                    ->setDefaultFont('Nunito')
                    ->setDefaultFontSize(14)
                    ->addSheet( $TPRM_ecole, 'École' )
                    ->saveAs($excel_file_path);

                    ?>
                        <script>
                            var doc = new jspdf.jsPDF('l');
                            var img = "<?php echo TPRM_icon ?>";

                            var currentdate = new Date(); 
                            var filetime = currentdate.getDate() + "-"
                                            + (currentdate.getMonth()+1)  + "-" 
                                            + currentdate.getFullYear() + "-"  
                                            + currentdate.getHours() + "-"  
                                            + currentdate.getMinutes() + "-" 
                                            + currentdate.getSeconds();
                                            
                            var titletime = currentdate.getDate() + "/"
                                            + (currentdate.getMonth()+1)  + "/" 
                                            + currentdate.getFullYear() + " @ "  
                                            + currentdate.getHours() + ":"  
                                            + currentdate.getMinutes() + ":" 
                                            + currentdate.getSeconds();

                            var y = 10;

                            doc.addImage(img, 'JPEG', 130, 10, 40, 20);                      
                            doc.setLineWidth(2);
                            doc.setTextColor(0, 167, 157);
                            doc.setFont('Nunito-Regular');
                            doc.text(100, y = 40, "Les groupes importées à : " + titletime);                          

                            doc.text(120, y = 55, "L’école importée");

                            var schoolhead = [['Nom de l’école', 'Année', 'Slug', 'Langue']];
                            var schoolbody = [ ['<?= $school_name ?>', '<?= $school_creation_year ?>' , '<?= $school_slug ?>' , '<?= $school_lang ?>'] ]
              
                            // Add school table
                            doc.autoTable({
                                head: schoolhead,
                                body: schoolbody,
                                startY: 60,
                                headStyles :{fillColor: [247, 148, 29], fontStyle: 'bold' , cellPadding: 2},
                                bodyStyles :{fontStyle: 'bold' , cellPadding: 2},
                                columnStyles: { 2: { fillColor: [161, 206, 204] } },
                                styles: { halign: 'center' },
                                horizontalPageBreakRepeat: 0,
                            });      

                            // Download Excel file first
                            setTimeout(function() {
                                // Create an anchor element
                                var downloadLink = document.createElement('a');

                                // Set the href attribute to the file path
                                downloadLink.href = '<?php echo esc_url($upload_dir['baseurl'] . '/' . $filename); ?>';

                                // Set the download attribute to the file name
                                downloadLink.download = '<?php echo esc_attr($filename) ?>';

                                downloadLink.click();
                            }, 1000); 

                            // and then download The PDF file
                            setTimeout(function() {

                                doc.save('groupes_importés_' + filetime + '.pdf');

                            }, 2000); 
                                           

                        </script>
                        <?php         
                    //}


                $TPRM_importer_helper->print_table_end();

                $TPRM_importer_helper->print_errors( $errors );
        
                $TPRM_importer_helper->class_print_results( $results, $ecole_results, $errors );
                
                if( !$is_frontend )
                    $TPRM_importer_helper->print_end_of_group_process();

                if( !$is_frontend && !$is_cron )
                    $TPRM_importer_helper->execute_datatable();

                @ini_set( 'auto_detect_line_endings', FALSE );
                
                do_action( 'after_TPRM_importer_import_groups' ); // deprecated this hook will be changed by the next one
                do_action( 'TPRM_importer_after_import_groups', $groups_created, $groups_updated, $groups_deleted, $groups_ignored );
            ?>
        </div>
    <?php
    } //end import_groups
}