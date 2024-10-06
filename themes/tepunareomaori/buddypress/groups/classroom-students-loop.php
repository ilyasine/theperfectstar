<?php

global $bp;
$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
$students_tab = $group_link . 'students';

$school_id = is_school(bp_get_current_group_id()) ? bp_get_current_group_id() : bp_get_parent_group_id(bp_get_current_group_id());

if ($context === 'previous_year') {
    $bp_tooltip = sprintf(esc_attr__('Promote this student to %s', 'tprm-theme'), $this_year);
    $bp_tooltip_all = sprintf(esc_attr__('Promote All students to %s', 'tprm-theme'), $this_year);
    $students = get_classroom_students($previous_classroom_id);
} elseif ($context === 'current_year') {
    $bp_tooltip = esc_attr__('Assign this student to the Classroom', 'tprm-theme' );
    $students = get_classroom_students($classroom_id);
}

if( !empty($students) ) { // there is at least a student
    ?>
    <div class="promote-students-dropdown-container">
        <?php $classrooms = get_school_classrooms_for_year($school_id); ?>
        <div class="select-wrap">
            <select id="promote-students-dropdown-<?php echo esc_attr(bp_get_group_id()); ?>" class="select2" data-cgid="<?php echo esc_attr(bp_get_group_id()); ?>">
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
    <ul class="students-list">
        <?php  if ($context === 'previous_year') : ?> 
        <li class="student promote-all">    
            <div class="promote-all-label">
                <?php _e( sprintf( __( 'Promote All students to <span>%s</span>', 'tprm-theme' ), $this_year ) ); ?>           
            </div>
            <div class="toggle-btn toggle-promote-all-students"
                type="button"
                data-bp-tooltip-pos="left"
                id="toggle-promote-all-students"
                data-bp-tooltip="<?php echo $bp_tooltip_all; ?>">
                <input type="checkbox" class="cb-value" />
                <span class="round-btn"></span>
            </div>
        </li>
        <?php endif; ?>
    <?php
    foreach ( $students as $student ) {
        ?>
        <li id="<?php echo esc_attr($student); ?>" class="student">
            <div class="item-avatar student-avatar">
                <a href="<?php echo esc_url( bp_core_get_user_domain( $student ) ); ?>">
                    <img src="<?php echo TPRM_IMG_PATH . 'avatar.svg'; ?>" class="avatar" alt=""/>
                </a>
            </div>
            <div class="student-name">
                <div class="list-title member-name">
                    <a target="_blank" href="<?php echo esc_url( bp_core_get_user_domain( $student ) ); ?>">
                        <?php echo esc_html(bp_core_get_user_displayname($student)); ?>
                    </a>
                </div>
            </div>
            <div class="student-username">
                <?php
                $student_object = get_userdata($student);
                $student_username =  $student_object->user_login;
                echo esc_html($student_username);
                ?>
            </div>
            <div class="student-action">             
                <div class="toggle-btn toggle-classroom-student"
                        data-student_id="<?php echo esc_attr($student); ?>"
                        data-bp-user-name="<?php echo esc_attr(bp_core_get_user_displayname($student)); ?>" 
                        type="button" 
                        data-bp-tooltip-pos="left"
                        data-bp-tooltip="<?php echo $bp_tooltip; ?>">           
                    <input type="checkbox" checked class="cb-value" />
                    <span class="round-btn"></span>
                </div>
            </div>
        </li>
        <?php
    }
    ?>
    </ul>
    <?php

}else{
    _e(sprintf('There are no students in this Classroom. You may promote students from the previous Classroom or create new student(s) from <a target="_blank" href="%s">here</a>.', $students_tab), 'tprm-theme'); 
}
