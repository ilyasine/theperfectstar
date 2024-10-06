<?php
/**
 * The template for BP Nouveau Component's directory filters template
 *
 * This template can be overridden by copying it to yourtheme/buddypress/common/filters/directory-filters.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */

?>


<?php if( function_exists('bp_is_group_subgroups') && bp_is_group_subgroups() || function_exists('bp_is_groups_directory') && bp_is_groups_directory() ): ?>

<div class="manage-classrooms-header">
	<div class="classroom_avatar">
	</div>
	<div class="classroom_name">
		<?php _e('Name', 'tprm-theme') ?>
	</div>
	<div class="classroom_curriculum">
		<?php _e('Curriculum', 'tprm-theme') ?>
	</div>
	<div class="classroom_level">
		<?php _e('Level', 'tprm-theme') ?>
	</div>

	<div class="classroom_students_count">
		<?php _e('No. Students', 'tprm-theme') ?>
	</div>

	<div class="classroom_teachers">
		<?php _e('Teacher', 'tprm-theme') ?>
	</div>

	<div class="classroom_code">
		<?php _e('Access Code', 'tprm-theme') ?>
	</div>
	<div class="classroom_actions">
		<?php _e('Actions', 'tprm-theme') ?>
	</div>
</div>
<?php endif; ?>

