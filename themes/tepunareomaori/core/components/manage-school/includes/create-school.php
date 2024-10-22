<?php 

    //error_log(' Hello World from create school ');

    function generate_school_trigram($school_name) {
        // Convert the name to lowercase
        $school_name = strtolower($school_name);

        // Split the name into words
        $words = explode(' ', $school_name);

        // If there is only one word, return the first three letters
        if (count($words) === 1) {
            return substr($words[0], 0, 3);
        }

        // Otherwise, take two letters from the first word and one from the second
        $first_word_part = substr($words[0], 0, 2);
        $second_word_part = substr($words[1], 0, 1);

        return $first_word_part . $second_word_part;
    }

    function create_school($school_name, $school_seats){
            // Create School
           global $wpdb;

            //TODO , we need to generate school trigram
            $school_trigram = generate_school_trigram($school_name);

            $school_slug = sanitize_title_with_dashes(esc_attr($school_name));

            $school_creation_year = get_option('school_year');

            $school_search_args = array(
                'slug' => $school_slug,
            );

            $args = array(
                'name'     => $school_name,
                'slug'     => groups_check_slug($school_slug),
                'status'   => 'hidden',
            );

            //$existing_school = groups_get_groups($school_search_args);
            error_log('$school_slug ' . $school_slug);
            $existing_school_id = BP_Groups_Group::group_exists($school_slug);

            if ($existing_school_id) {
                error_log('existing_school');
                return new WP_Error('existing_school', __('School Already exists', 'tprm-theme'));
            } else {
                $new_school_id = groups_create_group($args);
            }

            # school created successfully
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
                    $ld_school_id = $bb_ecole_groupid[0]->ID;

                    // Modify the post's "guid" to match $classe_slug
                    $school_permalink = home_url() . 'groups/' . $school_slug . '/';

                    wp_update_post(array(
                        'ID' => $ld_school_id,
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
                    $base_code = $school_trigram . $ld_school_id . $school_slug;
                    $school_code = substr(md5($base_code), 0, 10);
                    
                    # set invite status
                    groups_update_groupmeta($new_school_id, 'invite_status', 'admins');
                    # generated school trigram
                    groups_update_groupmeta($new_school_id, 'school_trigram', $school_trigram);
                    # generated school code
                    groups_update_groupmeta($new_school_id, 'school_code', $school_code);
                    # generated school seats
                    groups_update_groupmeta($new_school_id, 'school_seats', $school_seats);
                    # school year ( gotten from get_option('school_year'))
                    groups_update_groupmeta($new_school_id, 'school_creation_year', $school_creation_year);


                    
                    //Now School is created
                    error_log(' school created : ' . $new_school_id);

                    //TODO : wanna create classroom

                    $classe_name = 'Demo Classroom';

                    $classe_slug = sanitize_title_with_dashes(esc_attr($classe_name . '-' . $school_name . '-' . $school_creation_year));

                    $classroom_name = $classe_name . ' ' . $school_name;
                    // create classroom
                    $group_search_args = array(
                        'slug' => $classe_slug,
                    );
                    error_log('$classe_slug : ' . $classe_slug);
                    $args = array (
                        'name'          => $classroom_name,
                        'slug'          => groups_check_slug($classe_slug),
                        'status'        => 'hidden',
                    );

                    $existing_group_id = BP_Groups_Group::group_exists($classe_slug);

                    if ($existing_group_id) {
                        return new WP_Error('existing_group', __('Group Already exists', 'tprm-theme'));
                    } else {
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

                        error_log('group created : ' . $new_group_id);

                        return array(
                            'school_id' => $new_school_id,
                            'demo_group_id' => $new_group_id,
                        );

                }
            }

        } else {
            echo sprintf('Cannot create School %s, probably a temporary mysql error', $school_name);
            exit;
        }
    }


