
    <?php // start send message
    if ( bp_is_active( 'messages' ) ) { ?>
    <div class="message_teacher">
        <?php bp_send_message_button( $message_button_args ); ?>
    </div>
    <?php }
    /* End Send Message */
    /* Start Manage Classrooms */
    $teacher_name = bp_core_get_user_displayname($teacher_id); ?>					
    <a data-balloon-pos="up"
        data-balloon="<?php esc_attr_e('Manage Classrooms for this teacher', 'tprm-theme'); ?>"														
        href="#manage-teacher-classrooms-<?php echo esc_attr( $teacher_id ); ?>" 
        id="manage-teacher-classrooms" class="manage-teacher-classrooms-btn" 
        data-teacher_id="<?php echo esc_attr($teacher_id); ?>"
        data-teacher_name="<?php echo esc_attr($teacher_name); ?>" 
        target="_blank">
        <span class="bb-icon-exchange"></span>
    </a>
    <div id="manage-teacher-classrooms-<?php echo esc_attr( $teacher_id ); ?>" class="manage-teacher-classrooms mfp-with-anim mfp-hide white-popup">
        <?php if (!empty($classrooms)) {  ?>
        <div class="manage-teacher-classrooms-content-title">
            <span class="bb-icon-exchange"></span>
            <?php _e(sprintf('Manage Classrooms for <strong>%s</strong>', $teacher_name), 'tprm-theme');  ?>
        </div>
        <!-- Popup content here -->
        <div class="manage-teacher-classrooms-content-body">
            <div class="classrooms-list">
                <?php 
                    include 'school-classrooms-list.php';																					
                ?>
            </div>		
        </div>
        <div class="manage-teacher-classrooms-content-footer">
            <button 							
                data-balloon-pos="up"
                data-balloon="<?php _e('Confirm Classrooms Selection for this teacher ', 'tprm-theme'); ?>"
                data-security="<?php esc_attr_e($manage_teacher_classrooms_nonce); ?>"
                data-teacher_id="<?php echo esc_attr($teacher_id); ?>"
                data-teacher_name="<?php echo esc_attr($teacher_name); ?>"
                type="button"
                id="confirm_teacher_classrooms"
                class="confirm_teacher_classrooms">
                <?php _e('Confirm Classrooms Selection', 'tprm-theme'); ?>
            </button>
            <button 							
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Cancel change, The Classrooms for this teacher will remain the same before you click on Manage Classrooms Button', 'tprm-theme'); ?>"
                class="button"
                type="button"
                id="cancel_teacher_classrooms">
                <?php _e('Cancel', 'tprm-theme'); ?>
            </button>			
        </div>
        <?php 
            }else{
                ?>
                <div class="no-classrooms">
                    <div class="no-classrooms-body">
                        <?php
                            _e(sprintf('There are no classrooms found for this year. Please create a new classroom from <a target="_blank" href="%s">here</a>.', $classrooms_tab), 'tprm-theme'); ?>
                    </div>
                    <button 																	
                        class="button"
                        type="button"
                        id="cancel_teacher_classrooms">
                        <?php _e('OK', 'tprm-theme'); ?>
                    </button>
                </div>
                
            <?php }
            
        ?>									
    </div>
    <!-- End Manage Classrooms -->	

    <!-- Start Edit Teacher -->
    <?php $teacher_name = bp_core_get_user_displayname($teacher_id); ?>					
    <a data-balloon-pos="up"
        data-balloon="<?php esc_attr_e('Edit Teacher Details', 'tprm-theme'); ?>"														
        href="#edit-teacher-details-<?php echo esc_attr( $teacher_id ); ?>" 
        id="edit-teacher-details" class="edit-teacher-details-btn" 
        data-teacher_id="<?php echo esc_attr($teacher_id); ?>"
        data-teacher_name="<?php echo esc_attr($teacher_name); ?>" 
        target="_blank">
        <span class="bb-icon-user-edit"></span>
    </a>
    <div id="edit-teacher-details-<?php echo esc_attr( $teacher_id ); ?>" class="edit-teacher-details mfp-with-anim mfp-hide white-popup">
        <?php if (!empty($classrooms)) {  ?>
        <div class="edit-teacher-details-content-title">
            <span class="bb-icon-user-edit"></span>
            <?php _e(sprintf('Edit Details for <strong>%s</strong>', $teacher_name), 'tprm-theme');  ?>
        </div>
        <!-- Popup content here -->
            <?php 
            global $wpdb;				
            $teacher_object = get_userdata($teacher_id);
            $username = sanitize_text_field($teacher_object->user_login);
            $email = sanitize_text_field($teacher_object->user_email);
            $first_name = get_user_meta($teacher_id, 'first_name', true);
            $last_name = get_user_meta($teacher_id, 'last_name', true);
            $std_cred_tbl = $wpdb->prefix . "students_credentials";
            $sql = $wpdb->prepare("SELECT stdcred FROM " . esc_sql($std_cred_tbl) . " WHERE username = %s", esc_sql($username));     
            $stdcred_object = $wpdb->get_results($sql, OBJECT);
            if( !empty($stdcred_object) ) {
                $password = $stdcred_object[0]->stdcred;
            }

            ?>
        <div class="edit-teacher-details-content-body">
            <ul>
                <li><a href="#teacher-name-<?php echo esc_attr( $teacher_id ); ?>"><?php _e('Change Name', 'tprm-theme') ?></a></li>
                <li><a href="#teacher-email-<?php echo esc_attr( $teacher_id ); ?>"><?php _e('Change Email', 'tprm-theme') ?></a></li>
                <li><a href="#teacher-password-<?php echo esc_attr( $teacher_id ); ?>"><?php _e('Change Password', 'tprm-theme') ?></a></li>
            </ul>
            <div id="teacher-name-<?php echo esc_attr( $teacher_id ); ?>">
            <form>
                <div class="teacher-first-name">
                    <label for="first-name"><?php _e('First Name: ', 'tprm-theme') ?></label>
                    <input type="text" id="first-name" name="first-name" value="<?php echo esc_attr( $first_name ); ?>">
                </div>
                <div class="teacher-last-name">
                    <label for="last-name"><?php _e('Last Name: ', 'tprm-theme') ?></label>
                    <input type="text" id="last-name" name="last-name" value="<?php echo esc_attr( $last_name ); ?>">
                </div>
            </form>
            </div>
            <div id="teacher-email-<?php echo esc_attr( $teacher_id ); ?>">
            <form>
                <label for="email"><?php _e('Email: ', 'tprm-theme') ?></label>
                <input type="email" id="email" name="email" value="<?php echo esc_attr( $email ); ?>">
            </form>
            </div>
            <div id="teacher-password-<?php echo esc_attr( $teacher_id ); ?>">
            <form>
                <label for="password"><?php _e('Password: ', 'tprm-theme') ?></label>
                <input type="password" id="password" name="password" value="<?php echo esc_attr( $password ); ?>">
            </form>
            </div>
        </div>


        <div class="edit-teacher-details-content-footer">
            <button 							
                data-balloon-pos="up"
                data-balloon="<?php _e('Save and Update Teacher Details for this teacher ', 'tprm-theme'); ?>"
                data-security="<?php esc_attr_e($manage_teacher_classrooms_nonce); ?>"
                data-teacher_id="<?php echo esc_attr($teacher_id); ?>"
                data-teacher_name="<?php echo esc_attr($teacher_name); ?>"
                type="button"
                id="confirm_edit_teacher"
                class="confirm_edit_teacher">
                <?php _e('Update Teacher Details', 'tprm-theme'); ?>
            </button>
            <button 							
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Cancel change, The Details for this teacher will remain the same before you click on Edit Teacher Details Button', 'tprm-theme'); ?>"
                class="button"
                type="button"
                id="cancel_edit_teacher">
                <?php _e('Cancel', 'tprm-theme'); ?>
            </button>			
        </div>
        <?php 
            }else{
                ?>
                <div class="no-classrooms">
                    <div class="no-classrooms-body">
                        <?php
                            _e(sprintf('There are no classrooms found for this year. Please create a new classroom from <a target="_blank" href="%s">here</a>.', $classrooms_tab), 'tprm-theme'); ?>
                    </div>
                    <button 																	
                        class="button"
                        type="button"
                        id="cancel_teacher_classrooms">
                        <?php _e('OK', 'tprm-theme'); ?>
                    </button>
                </div>
                
            <?php }
            
        ?>									
    </div>
    <!-- End Edit Teacher -->
    
    <!-- Start Delete teacher -->
    <a data-balloon-pos="up"
    data-balloon="<?php esc_attr_e('Delete this teacher', 'tprm-theme'); ?>"														
    href="#teacher-delete-content-<?php echo esc_attr( bp_get_group_member_id() ); ?>" id="delete-teacher" class="delete-teacher" target="_blank">
        <span class="bb-icon-trash"></span>
    </a>
    <div id="teacher-delete-content-<?php echo esc_attr(bp_get_group_member_id()); ?>" class="teacher-delete-content mfp-with-anim mfp-hide white-popup">								
        <div class="teacher-delete-content-title">
            <span class="bb-icon-l bb-icon-exclamation-triangle"></span>
            <?php _e('Confirm Delete this teacher', 'tprm-theme') ?>
        </div>
        <!-- Popup content here -->
        <div class="teacher-delete-content-body">
            <p><?php _e('Please be aware that this action will not only remove the teacher from the school classrooms but will also permanently delete him/her from the school, including all associated data.', 'tprm-theme'); ?></p>
            <p><?php _e('This should only be done when a teacher is no longer part of the school.', 'tprm-theme'); ?></p>
            <p><?php _e('Note that this action is irreversible.', 'tprm-theme'); ?></p>
            <p><?php _e('If you only wish to remove the teacher from a classroom(s), please press cancel and use the button with the following icon <span class="bb-icon-exchange"></span> .', 'tprm-theme'); ?></p>			
        </div>
        <div class="teacher-delete-content-footer">
            <button 
                data-balloon-pos="up"
                data-balloon="<?php _e('I confirm deleting this teacher', 'tprm-theme'); ?>"
                data-security="<?php esc_attr_e($delete_teacher_nonce); ?>"
                data-teacher_id="<?php echo esc_attr(bp_get_group_member_id()); ?>"
                data-teacher_name="<?php echo esc_attr(bp_core_get_user_displayname(bp_get_group_member_id())); ?>" 
                type="button"
                id="confirm_delete_teacher"
                class="confirm_delete_teacher">
                <?php _e('Confirm', 'tprm-theme')?>
            </button>
            <button 
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Cancel change', 'tprm-theme'); ?>"
                class="button"
                type="button"
                id="cancel_delete_teacher">
                <?php _e('Cancel', 'tprm-theme')?>
            </button>			
        </div>
    </div>


    <!-- End Delete teacher -->
    
