<?php

if ( ! defined( 'ABSPATH' ) ) 
    exit;


class TPRM_import_classes{
	function __construct(){   
	}

    # display admin page
    static function admin_gui() {

        # check user capability
        if ( !current_user_can( 'publish_pages' ) )  {
            wp_die('You do not have sufficient permissions to access this page.');
        }
 
        # pre-setup
        $notice = '';
        $errors = '';

        # start import
        if (!empty($_POST)) {
            global $bp, $wpdb, $group_names, $group_slugs, $group_types, $parent_groups;

            $group_names = array();
            $group_slugs = array();
            $group_types = array();
            $parent_groups = array();
            
            # Check whether BP is active and whether Groups component is loaded, and throw error if not
            if(!(function_exists('BuddyPress') || is_a($bp,'BuddyPress')) || !bp_is_active('groups')) {
                echo 'BuddyPress is not installed or the Groups component is not activated. Cannot continue.';
                exit;
            }
            # pre-controls
            if ($_FILES['csv_file']['size'] == 0) {
                echo "It is a blank file";
                exit;
            }
            if ($_FILES['csv_file']['error'] != 0) {
                echo "Upload error";
                exit;
            }
            $info = pathinfo($_FILES['csv_file']['name']);

            if($info['extension'] != 'csv'){
                echo "Only csv file can be uploaded";
                exit;
            }

            # extract post values
            extract( $_POST, EXTR_OVERWRITE );

            # load CSV file
            if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE) {
                # read 1000 lines per run
                $group_count = 0;
                $existing_group_count = 0;
                $i=0;
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if ($data[0]== '' || $data[1]=='' || $data[2]=='' || $data[3]=='') {
                        continue;
                    }
                    # get details from csv file
                    $csv_group_name = trim($data[0]);
                    $csv_group_invite_status = trim($data[1]);

                   /*  if(sizeof($data) != 9) {
                        echo "CSV file is not according to sample CSV file";
                        exit;
                    } */

                    $group_slug = sanitize_title_with_dashes(esc_attr($csv_group_name));

                    $group_search_args = array(
                        'slug' => $group_slug
                    );

                    $existing_group = groups_get_groups($group_search_args);
                    //  var_dump($existing_group);

                    /* 
                    - format : group_name, members, group_type, parent_group_slug, instructor_emails

                            $data[0] = 'group_name' ;
                            $data[1] = 'members' ;  $csv_group_invite_status
                            $data[2] = 'group_type' ;
                            $data[3] = 'parent_group_slug' ;
                            $data[4] = 'instructor_emails' ;
                    */

                    # create group
                    $args = array (
                        'name'          => $csv_group_name,
                        'slug'          => groups_check_slug($group_slug),
                        'status'        => 'hidden',
                    );

                    if($existing_group['total'] > 0) {
                        $existing_group_count++;
                        continue;
                    }

                    else {
                        $new_group_id = groups_create_group ($args);
                    }

                    # group created successfully
                    if (!empty($new_group_id)) {
                        $u = '<a href="'.bp_loggedin_user_domain('/').'" title="'.bp_get_loggedin_user_username().'">'. bp_get_loggedin_user_username().'</a>';
                        $g = '<a href="'.site_url().'/groups/'.$group_slug.'/">'.$csv_group_name.'</a>';
          
                        /** Changes **/
                        $args = array(
                                'meta_query' => array(
                                    array(
                                        'key' => '_sync_group_id',
                                        'value' => $new_group_id
                                    )
                                ),
                                'post_type' => 'groups',
                                'pos_status'=> 'publish',
                                'posts_per_page' => -1
                            );
                        $bbgroupid = get_posts($args);
                        if( !empty($bbgroupid) ){
                            $ldgroupid = $bbgroupid[0]->ID;
                            
                            /** insert group type **/
                            $group_type = trim($data[2]);
                            if( !empty($group_type) ){
                                $gptypeargs = array(
                                                'meta_query' => array(
                                                    array(
                                                        'key' => '_bp_group_type_label_singular_name',
                                                        'value' => $group_type
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
                            } 
                            
                            /** insert group parent **/
                            $parent_group = trim($data[3]);
                                if( !empty($parent_group) ){
                                    $getparent = $wpdb->get_results("select * from ".$wpdb->prefix."bp_groups where slug='".$parent_group."'");
                                    if( !empty($getparent) ){
                                        $gpparentid = $getparent[0]->id;
                                        $updategp = $wpdb->update( $wpdb->prefix.'bp_groups' , array('parent_id'=>$gpparentid),array('id'=>$new_group_id) );
                                    }                                    
                                }                                  

                            // get all courses that have the $group_type category
                            $args = array(
                                'post_type'   => 'sfwd-courses',
                                'post_status' => 'any', // published
                                'numberposts' => -1,
                                'tax_query' => array(
                                    array(
                                    'taxonomy' => 'ld_course_category',
                                    'field' => 'slug', 
                                    'terms' => $group_type,
                                    'include_children' => false
                                    ))
                            );
                            
                            $courses = get_posts( $args );
                            
                            /** insert course in the given group type **/

                            if( !empty($group_type) ){
                                foreach( $courses as $course ){
                                    $course_slug = $course->post_name ;                      
                                    $cid = get_page_by_path( $course_slug, OBJECT, 'sfwd-courses' )->ID;
                                    update_post_meta( $cid , 'learndash_group_enrolled_'.$ldgroupid , time() );                    
                                }
                            }
                            
                            /** insert leaders in the group **/
                            $csv_leadersemail = trim($data[4]);
                            $allldemails = explode( '##' ,$csv_leadersemail );
                            if( !empty($allldemails) ){
                                foreach( $allldemails as $ldeids ){
                                    $lduserids = get_user_by( 'email' , $ldeids )->ID;
                                    update_user_meta( $lduserids, 'learndash_group_leaders_'.$ldgroupid , $ldgroupid );
                                }
                            }
                        }
                        /** Changes END **/
                        
                        # set invite status
                        groups_update_groupmeta( $new_group_id, 'invite_status', $csv_group_invite_status );

                        $group_count++;

                        $group_names[] = $csv_group_name;

                        $group_slugs[] = $group_slug;

                        $group_types[] = $group_type;

                        $parent_groups[] = $parent_group;
  
                    }
                    else {
                        echo sprintf( 'Cannot create group %s, probably a temporary mysql error', $csv_group_name);
                        exit;
                    }// else

                } // while
                fclose($handle);
            } // if
            else {
                echo 'Cannot open uploaded CSV file, contact your hosting support.';
                exit;
            }

            if ($existing_group_count != 0  ) {
                $error = '<div class="error settings-error" id="setting-error"><p><strong>' .sprintf ( 'Total %d groups are already found with the same name.', $existing_group_count ) .'</strong></p></div>';
            }
            if ($group_count > 0 ) {
                $notice = '<div class="updated settings-error" id="setting-error"><p><strong>'.sprintf ( 'Total %d groups are imported.', $group_count ).'</strong></p></div>';
            }
           
            if ( !empty($csv_group_name) && !empty($group_slug) && !empty($group_type) && !empty($parent_group) && $group_count > 0 ){

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

                        var head = [['Group Name ( Nom de la classe )', 'Group Slug ( à copier )', 'Group type ( Type de curriculum )', 'Parent Group ( Nom de l’école )']]

                        var y = 10;

                        doc.addImage(img, 'JPEG', 130, 10, 40, 20);                      
                        doc.setLineWidth(2);
                        doc.setTextColor(0, 167, 157);
                        doc.setFont("times");
                        doc.text(100, y = y + 30, "Les Classes importées à : " + titletime);
                                             
                        var body = [
                                <?php for( $i = 0 ; $i < $group_count ; $i++ ) : ?>
                                    ['<?= $group_names[$i] ?>', '<?= $group_slugs[$i] ?>', '<?= $group_types[$i] ?>', '<?= $parent_groups[$i] ?>'],
                                <?php endfor; ?>
                        ]

                        doc.autoTable({
                        head: head,
                        body: body,
                        startY: 50,
                        headStyles :{fillColor: [0, 167, 157], fontStyle: 'bold' , cellPadding: 3},
                        columnStyles: { 1: { fillColor: [161, 206, 204] } },
                        horizontalPageBreakRepeat: 0,
                        })
                        doc.save('classes_importées_' + filetime + '.pdf');

                    </script>
                <?php
             
            }  

        } // endif

            ?>

            <!--  # display admin page content -->

            <div class="wrap">
                <h3><?php _e('Import Classes', 'kwf-import') ?></h3>
                <?php  
                if ( isset($error) && isset($notice) ) {
                    echo $error;
                    echo $notice;
                }
                ?>
                <p><?php _e('This plugin imports tepunareomaori classes with their settings from a CSV file.', 'kwf-import') ?></p>
                <p><?php _e('Prepare CSV file, and then click import. That is all, enjoy', 'kwf-import') ?></p>
                <h3><i><strong><?php _e('Notes :', 'kwf-import') ?></strong></i></h3>
                <ul style="list-style: inside;">
                    <li><?php _e('CSV file structure must match with the sample.', 'kwf-import') ?></li>
                    <li><?php _e('If you get "Request timeout" or similar timeout message while trying to import large CSV file contact your hosting support or split your files into two or more part.', 'kwf-import') ?></li>
                </ul>
               <form method="POST" id="TPRM_importer_form" enctype="multipart/form-data" action="" accept-charset="utf-8">
                <table class="form-table">
                    <tbody>
                        <tr><th><label for="csv_file"><?php _e('Choose CSV File :', 'kwf-import') ?></label></th>
                        <td><input type="file" id="csv_file" name="csv_file" size="25"></td></tr>
                    </tbody>
                </table>
                <?php wp_nonce_field( 'kwf-security', 'security' ); ?>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Start Import', 'kwf-import') ?>"></p>
                
                </form>
                <h3><i><strong><?php _e('CSV Sample', 'kwf-import') ?></strong></i></h3>
                <table class="csv-sample form-table" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="column-kwf-group-name"><code>[Group Name]</code></th>						
                                <th class="column-kwf-members"><code>[members]</code></th>
                                <th class="column-kwf-group_type"><code>[group_type]</code></th>
                                <th class="column-kwf-parent_group_slug"><code>[parent_group_slug]</code></th>
                                <th class="column-kwf-instructor_emails"><code>[instructor_emails]</code></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="kwf-csv-example" valign="middle">
                                <td class="column-kwf-student-name"><code>Test group</code></td>								
                                <td class="column-kwf-members"><code>members</code></td>
                                <td class="column-kwf-group_type"><code>410</code></td>
                                <td class="column-kwf-parent_group_slug"><code>ce4</code></td>
                                <td class="column-kwf-instructor_emails"><code>instructor1@email.com##instructor2@email.com</code></td>
                            </tr>																
                        </tbody>
                </table>

                <p>
                    <?php _e('For downloading sample.csv file for classes, click', 'kwf-import') ?>
                    <a href="<?php echo plugins_url( '../samples/classes-sample.csv', __FILE__  ); ?> "><?php _e('Here', 'kwf-import') ?></a>
                </p>
                <?php 
                    
                  ?>
            </div>
        <?php   
    
    }


}// end TPRM_import_classes class

$TPRM_import_classes = new TPRM_import_classes();

?>
