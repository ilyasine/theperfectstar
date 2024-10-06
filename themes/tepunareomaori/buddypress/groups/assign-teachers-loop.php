<?php

global $bp;
$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
$teachers_tab = $group_link . 'teachers';
$classroom_id = bp_get_group_id();
$school_id = is_school(bp_get_current_group_id()) ? bp_get_current_group_id() : bp_get_parent_group_id(bp_get_current_group_id());
$school_name = bp_get_group_name(groups_get_group($school_id));
$added_teachers = get_classroom_teachers($classroom_id);
$this_classroom_name = bp_get_group_name();
$default_checked_classrooom = '';

ob_start();
include TPRM_THEME_PATH . 'template-parts/preloader.php';
$preloader = ob_get_clean();
$current_classroom_teachers = get_classroom_teachers($classroom_id);

$bp_tooltip = esc_attr__('Assign this teacher to the Classroom', 'tprm-theme' );
$default_checked_classrooom = $classroom_id;
$teachers = get_classroom_teachers($classroom_id);
$school_teachers = get_school_teachers($school_id);
$bp_tooltip_demote = sprintf(esc_attr__('Remove this teacher from %s', 'tprm-theme'),$this_classroom_name);

if( !empty($teachers) ) { //there is at least a teacher
    ?>

    <div class="outer-teachers-container">
        <div class="kwf-preloader-assign" style="display: none;">
            <?php echo $preloader; ?>
        </div>
        <div class="teachers-container">
            <div class="teachers-list-head assign-year">
                <div class="assign-from-label">
                    <?php _e( sprintf( __( 'Assign teachers of <span>%s</span>', 'tprm-theme' ), $school_name ) ); ?>  
                </div>
            </div>          
            <ul class="teachers-list">
                <li class="kwf-preloader" style="display: none;">
                    <?php echo $preloader;  ?>
                </li>
                <?php
                foreach ( $school_teachers as $teacher ) {
                    if(in_array($teacher, $current_classroom_teachers)){
                        continue;
                    }
                ?>
                <li id="<?php echo esc_attr($teacher); ?>" class="teacher">
                    <div class="item-avatar teacher-avatar">
                        <a href="<?php echo esc_url( bp_core_get_user_domain( $teacher ) ); ?>">
                            <img src="<?php echo TPRM_IMG_PATH . 'avatar.svg'; ?>" class="avatar" alt=""/>
                        </a>
                    </div>
                    <div class="teacher-name">
                        <div class="list-title member-name">
                            <a target="_blank" href="<?php echo esc_url( bp_core_get_user_domain( $teacher ) ); ?>">
                                <?php echo esc_html(bp_core_get_user_displayname($teacher)); ?>
                            </a>
                        </div>
                    </div>
                    <div class="teacher-username">
                        <?php
                        $teacher_object = get_userdata($teacher);
                        $teacher_username =  $teacher_object->user_login;
                        echo esc_html($teacher_username);
                        ?>
                    </div>
                    <div class="teacher-action">
                        <button class="toggle-btn toggle-classroom-teacher"
                                data-teacher_id="<?php echo esc_attr($teacher); ?>"
                                data-bp-user-name="<?php echo esc_attr(bp_core_get_user_displayname($teacher)); ?>" 
                                type="button" class="button toggle-classroom-teacher"
                                data-bp-tooltip-pos="left"
                                data-bp-tooltip="<?php echo $bp_tooltip; ?>"> 
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
        <div class="added-teachers-container">
            <div class="added-teachers-list-head assign-year">    
                <?php _e( sprintf( __( 'to <span>%s</span>', 'tprm-theme' ), $this_classroom_name ) ); ?>                     
            </div>
            <ul class="added-teachers-list">
                <?php
                foreach ( $added_teachers as $teacher ) {
                ?>
                <li id="<?php echo esc_attr($teacher); ?>" class="teacher">
                    <div class="item-avatar teacher-avatar">
                        <a href="<?php echo esc_url( bp_core_get_user_domain( $teacher ) ); ?>">
                            <img src="<?php echo TPRM_IMG_PATH . 'avatar.svg'; ?>" class="avatar" alt=""/>
                        </a>
                    </div>
                    <div class="teacher-name">
                        <div class="list-title member-name">
                            <a target="_blank" href="<?php echo esc_url( bp_core_get_user_domain( $teacher ) ); ?>">
                                <?php echo esc_html(bp_core_get_user_displayname($teacher)); ?>
                            </a>
                        </div>
                    </div>
                    <div class="teacher-username">
                        <?php
                            $teacher_object = get_userdata($teacher);
                            $teacher_username =  $teacher_object->user_login;
                            echo esc_html($teacher_username);
                        ?>
                    </div>
                    <div class="teacher-action">
                        <button class="toggle-btn toggle-classroom-teacher selected"
                                data-teacher_id="<?php echo esc_attr($teacher); ?>"
                                data-bp-user-name="<?php echo esc_attr(bp_core_get_user_displayname($teacher)); ?>" 
                                type="button" class="button toggle-classroom-teacher"
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

}else{
    _e(sprintf('There are no teachers in this Classroom. You may Assign teachers from the previous Year or create new teacher(s) from <a target="_blank" href="%s">here</a>.', $teachers_tab), 'tprm-theme'); 
}
