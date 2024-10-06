<!-- Actions -->
<?php 

$classrooom_id = bp_get_group_id(); 
$manage_classroom_nonce = wp_create_nonce('manage_classroom_nonce');
$this_year = get_option('school_year');
$previous_year = get_previous_year();
$has_previous_classroom = has_previous_classroom($classroom_id);
$previous_classroom_id = get_previous_classroom($classroom_id);
$promote_students_nonce = wp_create_nonce('promote_students_nonce');
$assign_students_nonce = wp_create_nonce('assign_students_nonce');
$assign_teachers_nonce = wp_create_nonce('assign_teachers_nonce');
$can_promote_students = false;
$school_id = bp_get_current_group_id();
$TPRM_school_year = school_implementation_year($school_id);
$classroom_ecole_year = groups_get_groupmeta($classrooom_id,'ecole_year');
$this_previous_year = get_previous_year_from_date($classroom_ecole_year);
$classrooms_for_previous_year = get_school_classrooms_for_year($school_id, $this_previous_year);
if ( $TPRM_school_year > 1 && !empty($classrooms_for_previous_year) ) {
    $can_promote_students = true;
}
// classroom_actions for teacher 
?>
 <a href="<?php bp_group_permalink(); ?>" target="_blank">
    <span class="bb-icon-external-link"></span>
</a>
<?php
// classroom_actions
if(is_TPRM_manager()) :
    // Start Promote students 
    if($can_promote_students) :
    ?>
    <a href="#promote-students-<?php bp_group_id() ?>" 
        id="#promote-students-from-previous-year" class="promote-students-btn" 
        target="_blank"
        data-balloon-pos="up"
        data-classroom_id="<?php bp_group_id() ?>"
        data-balloon="<?php esc_attr_e('Promote Students from previous year', 'tprm-theme'); ?>">
        <span class="bb-icon-user-arrow-up"></span>
    </a>
    <div id="promote-students-<?php bp_group_id() ?>" class="promote-students-content mfp-with-anim mfp-hide white-popup">
        <div class="promote-students-content-title">
            <span class="bb-icon-user-arrow-up"></span>
            <span class="promote-students-content-title_text">
                <?php _e(sprintf(__('Promote Students to %s classroom of %s', 'tprm-theme'), bp_get_group_name(), $this_year)); ?>
            </span>
        </div>
        <div class="promote-students-content-body">
            <div class="students-loop">
                <?php 
                include 'promote-students-loop.php';
                ?>
            </div>
        </div>
        <div class="promote-students-content-footer">
            <button 							
                data-balloon-pos="up"
                data-balloon="<?php echo sprintf(esc_attr__('I confirm Updating the %s classroom Teachers and Students', 'tprm-theme'), bp_get_group_name()); ?>"
                data-security="<?php esc_attr_e($promote_students_nonce); ?>"
                data-group="<?php bp_group_id() ?>"
                type="button"
                id="confirm_promote_students"
                class="confirm_promote_students">
                <?php _e('Confirm', 'tprm-theme'); ?>
            </button>
            <button 
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Cancel and close this popup', 'tprm-theme'); ?>"
                class="button"
                type="button"
                id="cancel_promote_students">
                <?php _e('Cancel', 'tprm-theme'); ?>
            </button>
            <button 
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Finish Promoting Students to this Classroom !', 'tprm-theme'); ?>"
                class="button"
                type="button"
                style="display: none;"
                id="close_promote_students">
                <?php _e('Finish & Close', 'tprm-theme'); ?>
            </button>				
        </div>
    </div>
    <?php
    // End Promote students
    endif;
    ?>

    <!-- Start Manage Classroom # Assign Students & Teachers -->
    <a href="#manage-classroom-<?php bp_group_id() ?>" 
        id="#manage-classroom" class="manage-classroom-btn" 
        target="_blank"
        data-balloon-pos="up"
        data-classroom_id="<?php bp_group_id() ?>"
        data-balloon="<?php echo sprintf(esc_attr__('Manage %s Students and Teachers', 'tprm-theme'), bp_get_group_name()); ?>">
        <span class="bb-icon-cogs"></span>
    </a>
    <div id="manage-classroom-<?php bp_group_id() ?>" class="classroom-manage-content mfp-with-anim mfp-hide white-popup">
        <div class="classroom-manage-content-title">
            <span class="bb-icon-cogs"></span>
            <span class="classroom-manage-content-title_text">
                <?php _e(sprintf(__('Manage %s Students and Teachers', 'tprm-theme'), bp_get_group_name())); ?>
            </span>    
        </div>
        <div class="classroom-manage-content-body">					
            <ul>           
                <li><a href="#assign-students-<?php echo esc_attr( $classrooom_id ); ?>"><?php _e('Manage Students', 'tprm-theme') ?></a></li>
                <li><a href="#assign-teachers-<?php echo esc_attr( $classrooom_id ); ?>"><?php _e('Manage Teachers', 'tprm-theme') ?></a></li>         
            </ul>
            <div id="assign-students-<?php echo esc_attr( $classrooom_id ); ?>" class="students-loop">
                <?php include 'assign-students-loop.php'; ?>  
                <div class="assign-students-footer">
                    <button 							
                        data-balloon-pos="up"
                        data-balloon="<?php echo sprintf(esc_attr__('I confirm Updating the %s Students', 'tprm-theme'), bp_get_group_name()); ?>"
                        data-security="<?php esc_attr_e($assign_students_nonce); ?>"
                        data-group="<?php bp_group_id() ?>"
                        type="button"
                        id="confirm_assign_students"
                        class="confirm_assign_students">
                        <?php _e('Confirm Update Students ', 'tprm-theme'); ?>
                    </button>
                    <button 							
                        data-balloon-pos="up"
                        data-balloon="<?php echo sprintf(esc_attr__('Revert Changes', 'tprm-theme'), bp_get_group_name()); ?>"
                        data-group="<?php bp_group_id() ?>"
                        type="button"
                        id="revert_assign_students"
                        class="revert_assign_students">
                        <?php _e('Revert Changes', 'tprm-theme'); ?>
                    </button>
                    <button 
                        data-balloon-pos="up"
                        data-balloon="<?php esc_attr_e('Finish Assigning Students to this Classroom !', 'tprm-theme'); ?>"
                        class="button"
                        type="button"
                        style="display: none;"
                        id="close_assign_students">
                        <?php _e('Finish & Close', 'tprm-theme'); ?>
                    </button>
                </div>
            </div>
            <div id="assign-teachers-<?php echo esc_attr( $classrooom_id ); ?>" class="teachers-loop">
                <?php include 'assign-teachers-loop.php'; ?>
                <div class="assign-teachers-footer">
                    <button 							
                        data-balloon-pos="up"
                        data-balloon="<?php echo sprintf(esc_attr__('I confirm Updating the %s Teachers', 'tprm-theme'), bp_get_group_name()); ?>"
                        data-security="<?php esc_attr_e($assign_teachers_nonce); ?>"
                        data-group="<?php bp_group_id() ?>"
                        type="button"
                        id="confirm_assign_teachers"
                        class="confirm_assign_teachers">
                        <?php _e('Confirm Update Teachers', 'tprm-theme'); ?>
                    </button>
                    <button 							
                        data-balloon-pos="up"
                        data-balloon="<?php echo sprintf(esc_attr__('Revert Changes', 'tprm-theme'), bp_get_group_name()); ?>"
                        data-group="<?php bp_group_id() ?>"
                        type="button"
                        id="revert_assign_teachers"
                        class="revert_assign_teachers">
                        <?php _e('Revert Changes', 'tprm-theme'); ?>
                    </button>
                    <button 
                        data-balloon-pos="up"
                        data-balloon="<?php esc_attr_e('Finish Assigning Teachers to this Classroom !', 'tprm-theme'); ?>"
                        class="button"
                        type="button"
                        style="display: none;"
                        id="close_assign_teachers">
                        <?php _e('Finish & Close', 'tprm-theme'); ?>
                    </button>
                </div>
            </div>    
        </div>
        <div class="classroom-manage-content-footer">

            <button 							
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Cancel and close this popup', 'tprm-theme'); ?>"
                class="button"
                type="button"
                id="cancel_manage_classroom">
                <?php _e('Cancel', 'tprm-theme'); ?>
            </button>			
        </div>
    </div>
    <!-- End Manage Classroom -->

    <!-- Start Delete Classroom -->
    <a href="#classroom-delete-content-<?php bp_group_id() ?>" 
        id="delete-classroom" class="delete-classroom" 
        target="_blank"
        data-balloon-pos="up"
        data-balloon="<?php esc_attr_e('Delete classroom', 'tprm-theme'); ?>">
        <span class="bb-icon-trash"></span>
    </a>
    <div id="classroom-delete-content-<?php bp_group_id() ?>" class="classroom-delete-content mfp-with-anim mfp-hide white-popup">
        
        <div class="classroom-delete-content-title">
            <span class="bb-icon-l bb-icon-exclamation-triangle"></span>
            <?php _e(sprintf(__('WARNING: Confirm Deleting the %s classroom', 'tprm-theme'), bp_get_group_name())); ?>
        </div>
            <!-- Popup content here -->
        <div class="classroom-delete-content-body">					
            <p><?php _e('Deleting this classroom will completely remove ALL content associated with it. There is no way back. Please be careful with this option.', 'tprm-theme') ?></p> 
            <p><?php _e(sprintf('You can create new classroom anytime from the <a target="_blank" href="%s">Classrooms page</a>.', $group_link), 'tprm-theme'); ?></p>
        </div>
        <div class="classroom-delete-content-footer">
            <button 							
                data-balloon-pos="up"
                data-balloon="<?php echo sprintf(esc_attr__('I confirm Deleting the %s classroom', 'tprm-theme'), bp_get_group_name()); ?>"
                data-security="<?php esc_attr_e($delete_classroom_nonce); ?>"
                data-group="<?php bp_group_id() ?>"
                type="button"
                id="confirm_delete_classroom"
                class="confirm_delete_classroom">
                <?php _e('Confirm', 'tprm-theme'); ?>
            </button>
            <button 							
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Cancel and close this popup', 'tprm-theme'); ?>"
                class="button"
                type="button"
                id="cancel_delete_classroom">
                <?php _e('Cancel', 'tprm-theme')?>
            </button>			
        </div>
    </div>
    <!-- End Delete Classroom -->  
<?php endif; ?>