<?php
$teacher_classroom_ids = []; // Array to store classroom IDs where the teacher is a member

// Retrieve classrooms where the teacher is a member
if( isset($teacher_id)){
    $teacher_groups = get_TPRM_user_groups($teacher_id); // Replace $teacher_id with the actual teacher ID variable
    if (!empty($teacher_groups)) {
        $teacher_classroom_ids = $teacher_groups;
    }
}

// Separate classrooms into two arrays: teacher's classrooms and other classrooms
$teacher_classrooms = [];
$other_classrooms = [];

foreach ($classrooms as $classroom_id) {
    if (in_array($classroom_id, $teacher_classroom_ids)) {
        $teacher_classrooms[] = $classroom_id;
    } else {
        $other_classrooms[] = $classroom_id;
    }
}

// Merge the arrays so that teacher's classrooms come first
$sorted_classrooms = array_merge($teacher_classrooms, $other_classrooms);
?>

<table class="school-classroom-table">
    <thead>
        <tr>
            <th class="classroom_name"><?php _e('Name', 'tprm-theme'); ?></th>
            <th class="classroom_type"><?php _e('Curriculum', 'tprm-theme'); ?></th>
            <th class="classroom_level"><?php _e('Level', 'tprm-theme'); ?></th>
            <th class="classroom_action">
                <div class="toggle-btn toggle-classroom-teacher-all"
                        type="button" 
                        data-bp-tooltip-pos="left"
                        data-bp-tooltip="<?php esc_attr_e('Set as teacher for this Classroom', 'tprm-theme'); ?>"
                    >
                    <input type="checkbox" checked class="cb-value" />
                    <span class="round-btn"></span>
                </div>        
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($sorted_classrooms as $classroom_id) {
                $classroom = groups_get_group($classroom_id);
                $is_teacher_classroom = in_array($classroom_id, $teacher_classroom_ids);
                ?>
                <tr id="<?php echo esc_attr($classroom->id); ?>" class="classroom<?php echo $is_teacher_classroom ? ' selected' : ''; ?>">
                    <td class="classroom_name">
                        <a target="_blank" href="<?php echo esc_url(bp_get_group_permalink($classroom)); ?>">
                            <?php echo esc_html($classroom->name); ?>
                        </a>
                    </td>
                    <td class="classroom_type">
                        <?php                 
                            $group_type = bp_groups_get_group_type($classroom->id);
                            $group_type_object = bp_groups_get_group_type_object($group_type);
                            if(! is_null($group_type_object)) :
                                $curriculum = $group_type_object->labels['name'];
                                echo esc_html($curriculum);
                            endif;
                        ?>
                    </td>
                    <td class="classroom_level">
                        <?php echo esc_html(groups_get_groupmeta($classroom->id, 'classroom_level')); ?>
                    </td>
                    <td class="classroom_action">
                        <div class="toggle-btn toggle-classroom-teacher<?php echo $is_teacher_classroom ? ' active' : ''; ?>"
                                data-classroom-id="<?php echo esc_attr($classroom->id); ?>"
                                data-bp-classroom-name="<?php echo esc_attr($classroom->name); ?>" 
                                type="button" 
                                data-bp-tooltip-pos="left"
                                data-bp-tooltip="<?php esc_attr_e('Set as teacher for this Classroom', 'tprm-theme'); ?>"
                            >
                            <input type="checkbox" class="cb-value" <?php echo $is_teacher_classroom ? 'checked' : ''; ?> />
                            <span class="round-btn"></span>
                        </div>
                    </td>
                </tr>
                <?php
            }     
        ?>
    </tbody>
</table>
