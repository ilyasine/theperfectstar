<?php
$student_classroom = '';
// Retrieve student classroom for current year
if( isset($student_id) && function_exists('get_student_classroom_for_year')){
    $student_classroom = get_student_classroom_for_year($student_id);
}

?>

<table class="school-classroom-table">
    <thead>
        <tr>
            <th class="classroom_name"><?php _e('Name', 'tprm-theme'); ?></th>
            <th class="classroom_curriculum"><?php _e('Curriculum', 'tprm-theme'); ?></th>
            <th class="classroom_level"><?php _e('Level', 'tprm-theme'); ?></th>
            <th class="classroom_action"><?php _e('Action', 'tprm-theme'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php  
        foreach ($classrooms as $classroom_id) :
            $classroom = groups_get_group($classroom_id);
            $is_student_classroom = $classroom_id == $student_classroom;
            ?>
            <tr id="<?php echo esc_attr($classroom->id); ?>" data-student-classroom="<?php echo esc_attr($student_classroom); ?>" class="classroom<?php echo $is_student_classroom ? ' selected' : ''; ?>">
                <td class="classroom_name">
                    <a target="_blank" href="<?php echo esc_url(bp_get_group_permalink($classroom)); ?>">
                        <?php echo esc_html($classroom->name); ?>
                    </a>
                </td>
                <td class="classroom_curriculum">
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
                    <div class="toggle-btn toggle-classroom-student<?php echo $is_student_classroom ? ' active' : ''; ?>"
                         data-classroom-id="<?php echo esc_attr($classroom->id); ?>"
                         data-bp-classroom-name="<?php echo esc_attr($classroom->name); ?>"
                         type="button"
                         data-bp-tooltip-pos="left"
                         data-bp-tooltip="<?php esc_attr_e('Assign the student to this Classroom', 'tprm-theme'); ?>"
                    >
                        <input type="checkbox" class="cb-value" <?php echo $is_student_classroom ? 'checked' : ''; ?> />
                        <span class="round-btn"></span>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
