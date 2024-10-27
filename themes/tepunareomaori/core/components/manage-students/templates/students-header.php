
<div class="students_header">				
    
    <div class="manage_students_header">
        <!-- search -->
        <form action="" method="get" class="students-search-form">
            <input type="text" name="search" placeholder="<?php esc_attr_e( 'Search Students...', 'tprm-theme' ); ?>" value="<?php echo esc_attr( isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '' ); ?>" />
            <button type="submit" class="student_search"><?php esc_html_e( 'Search', 'tprm-theme' ); ?></button>
        </form>

        <?php if(is_tprm_manager() && 0) : //TODO After prod ?>
        <div class="bulk-actions">      
            <!-- Bulk Actions Dropdown -->
            <div class="select-wrap">
                <select id="bulk-action-selector" name="action">
                    <option value="-1"><?php _e('Bulk Actions', 'tprm-theme') ?></option>
                    <option value="delete"><?php _e('Delete', 'tprm-theme') ?></option>
                    <option value="lock"><?php _e('Lock', 'tprm-theme') ?></option>
                    <option value="lock"><?php _e('Unlock', 'tprm-theme') ?></option>
                </select>
            </div>
            <button id="apply-bulk-action"><?php _e('Apply', 'tprm-theme') ?></button>
        </div>

        <div class="bulk-assign">
            <?php $classrooms = get_school_classrooms_for_year($school_id); ?>  
            <div id="change-button" class="last filter">
                <button id="change-btn" data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>" ><?php _e('Assign to', 'tprm-theme') ?></button>
            </div>     
            <div id="groups-dropdown" class="last filter">
                <label class="bp-screen-reader-text" for="group-dropdown">
                    <span><?php _e('Select a classroom', 'tprm-theme') ?></span>
                </label>
                <div class="select-wrap">
                    <select id="classroom-dropdown" class="select2" data-current-classroom="<?php echo esc_attr(bp_get_group_id()); ?>">
                        <option value=""><?php _e('Select a classroom', 'tprm-theme') ?></option>
                        <?php
                        if (!empty($classrooms)) {                                    
                            foreach ($classrooms as $classroom_id) {
                                $classroom = groups_get_group($classroom_id); ?>
                                <option value="<?php echo esc_attr($classroom_id) ?>"><?php echo esc_html($classroom->name) ?></option>
                        <?php }
                        } else { ?>
                            <option value=""><?php _e('No Classroom found for this year', 'tprm-theme') ?></option>
                        <?php  }
                        ?>
                    </select>
                </div>
            </div>            
        </div>
        <?php endif; ?>
    </div>

</div>