<?php

global $bp;
$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
$teachers_tab = $group_link . 'teachers';
$teachers = get_classroom_teachers($classroom_id);

if( !empty($teachers) ) { // there is at least a teacher
    ?>
    <ul class="teachers-list">
    <?php
    foreach ( $teachers as $teacher ) {
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
                <div class="toggle-btn toggle-classroom-teacher"
                        data-teacher_id="<?php echo esc_attr($teacher); ?>"
                        data-bp-user-name="<?php echo esc_attr(bp_core_get_user_displayname($teacher)); ?>" 
                        type="button" 
                        data-bp-tooltip-pos="left"
                        data-bp-tooltip="<?php esc_attr_e('Assign this teacher to the Classroom', 'tprm-theme' ) ?>">           
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
    _e(sprintf('There are no teachers available for this school. Please create a new teacher from <a target="_blank" href="%s">here</a>.', $teachers_tab), 'tprm-theme'); 
}
