<?php
global $bp;
$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
$students_tab = $group_link . 'students';
$school_id = is_school(bp_get_current_group_id()) ? bp_get_current_group_id() : bp_get_parent_group_id(bp_get_current_group_id());
$added_students = get_classroom_students($classroom_id); // Students already added to the current classroom
$this_classroom_name = bp_get_group_name(groups_get_group($classroom_id));
$default_checked_classroom = '';
$previous_year = get_previous_year();
ob_start();
include TPRM_THEME_PATH . 'template-parts/preloader.php';
$preloader = ob_get_clean();
$current_classroom_students = get_classroom_students($classroom_id); // Students currently in the selected classroom
$bp_tooltip = sprintf(esc_attr__('Promote this student to %s', 'tprm-theme'), $this_year);
$bp_tooltip_demote = sprintf(esc_attr__('Demote this student from %s of %s', 'tprm-theme'), $this_classroom_name, $this_year);
$bp_tooltip_all = sprintf(esc_attr__('Promote All students to %s', 'tprm-theme'), $this_year);
$default_checked_classroom = $previous_classroom_id;

// Fetch students from last year's classroom selected from the dropdown
$students_last_year = get_classroom_students($previous_classroom_id);

// Fetch students not currently enrolled in any classroom for this year
$students_not_enrolled_this_year = get_students_without_classroom_for_year($school_id, $this_year);

// Intersect both arrays to get the students who were in the selected classroom last year and not enrolled this year
$students = array_intersect($students_last_year, $students_not_enrolled_this_year);

?>

<div class="outer-students-container">
    <div class="tprm-preloader-promote" style="display: none;">
        <?php echo $preloader; ?>
    </div>
    <div class="students-container">
        <div class="students-list-head promote-year">
            <div class="promote-from-label"><?php _e('Promote students from ', 'tprm-theme') ?></div>   
            <div class="promote-students-dropdown-container">
                <?php $classrooms = get_school_classrooms_for_year($school_id, $previous_year); ?>
                <div class="select-wrap">
                    <select id="promote-students-dropdown-<?php echo esc_attr(bp_get_group_id()); ?>" data-cgid="<?php echo esc_attr(bp_get_group_id()); ?>">
                        <option value=""><?php _e('classroom', 'tprm-theme') ?></option>
                        <?php
                        if (!empty($classrooms)) {                                    
                            foreach ($classrooms as $classroom_id) {
                                $classroom = groups_get_group($classroom_id); 
                                ?>
                                <option value="<?php echo esc_attr($classroom_id); ?>">
                                    <?php echo esc_html($classroom->name); ?>
                                </option>
                        <?php 
                            }
                        } else { ?>
                            <option value=""><?php _e('No Classrooms were found for this year', 'tprm-theme'); ?></option>
                        <?php  
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="promote-year previous">
                <?php _e(sprintf(__('of <span>%s</span>', 'tprm-theme'), $previous_year)); ?>           
            </div> 
        </div>
        
        <ul class="students-list">
            <li class="tprm-preloader" style="display: none;">
                <?php echo $preloader;  ?>
            </li>
            <?php
            if (!empty($students)) {
                foreach ($students as $student) :
                    ?>
                    <li id="<?php echo esc_attr($student); ?>" class="student">
                        <div class="item-avatar student-avatar">
                            <a href="<?php echo esc_url(bp_core_get_user_domain($student)); ?>">
                                <img src="<?php echo TPRM_IMG_PATH . 'avatar.svg'; ?>" class="avatar" alt=""/>
                            </a>
                        </div>
                        <div class="student-name">
                            <div class="list-title member-name">
                                <a target="_blank" href="<?php echo esc_url(bp_core_get_user_domain($student)); ?>">
                                    <?php echo esc_html(bp_core_get_user_displayname($student)); ?>
                                </a>
                            </div>
                        </div>
                        <div class="student-username">
                            <?php
                            $student_object = get_userdata($student);
                            $student_username = $student_object->user_login;
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
                                <span class="icons" aria-hidden="true"></span> <span class="bp-screen-reader-text">
                            </span>
                            </button>
                        </div>
                    </li>
                    <?php
                endforeach;
            } else { ?>
                <li class="nostudent"><?php _e(sprintf('Please select a classroom to promote students from or create new student(s) from <a target="_blank" href="%s">here</a>.', $students_tab), 'tprm-theme'); ?></li>
            <?php }
            ?>
        </ul>
    </div>
        <div class="added-students-container">
            <div class="added-students-list-head promote-year">
                <?php
                    $promote_to_label = __('to ', 'tprm-theme');
                    $promote_to_this_classroom = esc_html($this_classroom_name);
                    $promote_this_year = sprintf(__('of <span>%s</span>', 'tprm-theme'), $this_year);
                    echo sprintf(
                        '<div class="promote-to-label">%s</div><div class="promote-to-this-classroom">%s</div><div class="promote-year">%s</div>',
                        $promote_to_label,
                        $promote_to_this_classroom,
                        $promote_this_year
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
    _e(sprintf('Please select a classroom to promote students from or create new student(s) from <a target="_blank" href="%s">here</a>.', $students_tab), 'tprm-theme'); 
} */
