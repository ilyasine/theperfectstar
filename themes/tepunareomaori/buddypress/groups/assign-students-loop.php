<?php

global $bp;
$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
$students_tab = $group_link . 'students';

$school_id = is_school(bp_get_current_group_id()) ? bp_get_current_group_id() : bp_get_parent_group_id(bp_get_current_group_id());
$classroom_id = bp_get_group_id();
$school_name = bp_get_group_name(groups_get_group($school_id));
$added_students = get_classroom_students($classroom_id);
$this_classroom_name = bp_get_group_name();
ob_start();
include TPRM_THEME_PATH . 'template-parts/preloader.php';
$preloader = ob_get_clean();
$current_classroom_students = get_classroom_students($classroom_id);
$bp_tooltip = sprintf(esc_attr__('Assign this student to %s', 'tprm-theme'), $this_classroom_name);
$students = get_classroom_students($classroom_id);
$bp_tooltip_demote = sprintf(esc_attr__('Remove this student from %s', 'tprm-theme'), $this_classroom_name);
$this_classroom_year = groups_get_groupmeta($classrooom_id,'ecole_year');

//if( !empty($students) ) { //there is at least a student
    ?>
    <div class="outer-students-container">
        <div class="tprm-preloader-assign" style="display: none;">
            <?php echo $preloader; ?>
        </div>
        <div class="students-container">
            <div class="students-list-head assign-year">
                <div class="assign-from-label"><?php _e('Assign students from ', 'tprm-theme') ?></div>
                <div class="assign-students-dropdown-container">
                    <?php $classrooms = get_school_classrooms_for_year($school_id, $this_classroom_year); ?>                
                    <div class="select-wrap">
                        <select id="assign-students-dropdown-<?php echo esc_attr(bp_get_group_id()); ?>" data-school_id="<?php echo esc_attr($school_id); ?>" data-this_classroom_year="<?php echo esc_attr($this_classroom_year); ?>" data-current-classroom-name="<?php echo esc_attr($this_classroom_name); ?>">
                            <option value=""><?php _e('Select a classroom', 'tprm-theme') ?></option>
                            <?php
                            // Existing classrooms
                            if (!empty($classrooms)) {
                                foreach ($classrooms as $classroom_id) {
                                    $classroom = groups_get_group($classroom_id);
                                    if($classroom_id == bp_get_group_id()) continue;
                                    ?>
                                    <option value="<?php echo esc_attr($classroom_id); ?>">
                                        <?php echo esc_html($classroom->name); ?>
                                    </option>
                                    <?php
                                }
                            } else {
                                ?>
                                <option value=""><?php _e('No Classroom were found for this year', 'tprm-theme'); ?></option>
                                <?php
                            }

                            // Option for students without classroom
                            $students_without_classroom = get_students_without_classroom_for_year($school_id, $this_classroom_year);
                            if (!empty($students_without_classroom)) {
                                ?>
                                <option value="students_without_classroom"><?php _e('Students without Classroom', 'tprm-theme'); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
               <!--  <div class="subnav-search members-search">
                    <?php //bp_nouveau_search_form(); ?>
                </div> -->
            </div>
            
            <ul class="students-list">
                <li class="tprm-preloader" style="display: none;">
                    <?php echo $preloader; ?>
                </li>
                <li class="nostudent"><?php _e(sprintf('Please select a classroom to assign students from or create new student(s) from <a target="_blank" href="%s">here</a>.', $students_tab), 'tprm-theme'); ?></li>
                <?php
                if( !empty($students) ) {
                    foreach ( $students as $student ) :
                        if(in_array($student, $current_classroom_students)){
                            continue;                        
                        }
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
                            <button class="toggle-btn toggle-classroom-student"
                                    data-student_id="<?php echo esc_attr($student); ?>"
                                    data-bp-user-name="<?php echo esc_attr(bp_core_get_user_displayname($student)); ?>" 
                                    type="button" class="button toggle-classroom-student"
                                    data-bp-tooltip-pos="left"
                                    data-bp-tooltip="<?php echo $bp_tooltip; ?>"> 
                                    <span class="icons" aria-hidden="true"></span> 
                                    <span class="bp-screen-reader-text"></span>                     
                            </button>
                        </div>
                    </li>
                    <?php
                    endforeach;
                }/* else{ ?>
                    <li class="nostudent"><?php _e(sprintf('Please select a classroom to assign students from or create new student(s) from <a target="_blank" href="%s">here</a>.', $students_tab), 'tprm-theme'); ?></li>
                <?php } */
                ?>
            </ul>
        </div>
        <div class="added-students-container">
            <div class="added-students-list-head assign-year">
                <?php
                    $Assign_to_label = __('to ', 'tprm-theme');
                    $Assign_to_this_classroom = esc_html($this_classroom_name);
                    $Assign_this_year = sprintf(__('of <span>%s</span>', 'tprm-theme'), $this_year);
                    echo sprintf(
                        '<div class="assign-to-label">%s</div><div class="assign-to-this-classroom">%s</div>',
                        $Assign_to_label,
                        $Assign_to_this_classroom
                    );
                ?>
            </div>
            <ul class="added-students-list">
                <?php
                foreach ( $added_students as $student ) {
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
                        <button class="toggle-btn toggle-classroom-student selected"
                                data-student_id="<?php echo esc_attr($student); ?>"
                                data-bp-user-name="<?php echo esc_attr(bp_core_get_user_displayname($student)); ?>" 
                                type="button" class="button toggle-classroom-student"
                                data-bp-tooltip-pos="left"
                                data-bp-tooltip="<?php echo $bp_tooltip_demote; ?>"> 
                            <span class="icons" aria-hidden="true"></span> <span class="bp-screen-reader-text">
                        </span>
                        </button>
                    </div>
                </li>
                <?php
                }
                ?>
            </ul>
        </div>  
    </div>
    <?php

/* }else{
    _e(sprintf('There are no students in this Classroom. You may Assign students from the previous Year or create new student(s) from <a target="_blank" href="%s">here</a>.', $students_tab), 'tprm-theme'); 
} */
