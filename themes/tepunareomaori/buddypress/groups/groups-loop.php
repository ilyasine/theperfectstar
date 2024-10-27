<?php
/**
 * BuddyBoss - Groups Loop
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/groups-loop.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 *
 * @package BuddyBoss\Core
 */

add_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 );

bp_nouveau_before_loop(); ?>


<?php if ( bp_get_current_group_directory_type() ) : ?>
	<div class="bp-feedback info">
		<span class="bp-icon" aria-hidden="true"></span>
		<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
	</div>
<?php endif; 

global $bp;
$cover_class        = ! bb_platform_group_element_enable( 'cover-images' ) ? 'bb-cover-disabled' : 'bb-cover-enabled';
$meta_privacy       = ! bb_platform_group_element_enable( 'group-privacy' ) ? 'meta-privacy-hidden' : '';
$meta_group_type    = ! bb_platform_group_element_enable( 'group-type' ) ? 'meta-group-type-hidden' : '';
$group_members      = ! bb_platform_group_element_enable( 'members' ) ? 'group-members-hidden' : '';
$join_button        = ! bb_platform_group_element_enable( 'join-buttons' ) ? 'group-join-button-hidden' : '';
$group_alignment    = bb_platform_group_grid_style( 'left' );
$group_cover_height = function_exists( 'bb_get_group_cover_image_height' ) ? bb_get_group_cover_image_height() : 'small';
$delete_classroom_nonce = wp_create_nonce('delete_classroom_nonce');
$duplicate_structure_nonce = wp_create_nonce('duplicate_structure_nonce');
$group_link = '';
if(bp_is_group_subgroups()){
	$group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
	$teachers_tab = $group_link . 'teachers';
	$students_tab = $group_link . 'students';
}

ob_start();
include TPRM_THEME_PATH . 'template-parts/preloader.php';
$preloader = ob_get_clean();

// Custom query arguments
$args = array(
    'meta_key' => 'classroom_level', // The custom field you want to sort by
    'orderby' => 'meta_value_num',  // Sort by numeric value of the custom field
    'order' => 'ASC',               // Ascending order
);

?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) . '&' . http_build_query( $args ) ) ) : ?>

	<?php bp_get_template_part( 'common/classrooms-list-header' ); ?>	
	
	<?php bp_nouveau_pagination( 'top' ); ?>

	<ul id="groups-list" class="
	<?php
	bp_nouveau_loop_classes();
	echo esc_attr( ' ' . $cover_class . ' ' . $group_alignment );
	?>
	 groups-dir-list">
		<?php
		while ( bp_groups() ) :
			bp_the_group();
			$group_id = bp_get_group_id();
			$classroom_id = bp_get_group_id();
            $parent_group_id = bp_get_parent_group_id( $group_id );
			?>

			<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups">
				<div class="list-wrap">

					<div class="item-block">

						<div class="item-avatar classroom_avatar">
                            <a href="<?php bp_group_permalink(); ?>" class="group-avatar-wrap">

                            <?php
                            if ( $parent_group_id ) {
								echo bp_core_fetch_avatar( array( 'item_id' => $parent_group_id, 'object' => 'group' ) );
                            } else {
                                bp_group_avatar( bp_nouveau_avatar_args() );
                            }
                            ?>
                            </a>
                        </div>

						<div class="classroom_name">
							<?php bp_group_link(); ?>
						</div>

						<div class="classroom_type">
							<?php echo bp_get_group_type(); ?>
						</div>

						<div class="classroom_level">
							<?php echo groups_get_groupmeta( bp_get_group_id(), 'classroom_level' ); ?>
						</div>

						<div class="classroom_students_count">
							<div class="classroom_students_count_inner">
								<?php 
								// Get all members of the group
								$students = groups_get_group_members(array(
									'group_id' => bp_get_group_id(),
									'exclude_admins_mods' => true, // Exclude admins and mods
									'per_page' => false, // Get all members
								));
								// Get the students count
								$student_count = ! empty( $students['count'] ) ? $students['count'] : 0;							
								echo esc_html($student_count);							
								?>
							</div>
						</div>

						<div class="classroom_teachers">
							<?php classroom_loop_teachers(); ?>
						</div>

						<div class="classroom_code">
							<div class="classroom_code_inner">
								<span class="classroom_code_text"><?php echo groups_get_groupmeta( bp_get_group_id(), 'classroom_code' ); ?></span>					
								<span class="bb-icon-l bb-icon-copy"></span>					
							</div>
						</div>

						<div class="classroom_actions">	
							<?php include 'classroom-actions.php'; ?>                   																					          		
						</div>					

					</div>

				</div>
			</li>

		<?php endwhile; ?>

	</ul>

	<!-- Leave Group confirmation popup -->
	<div class="bb-leave-group-popup bb-action-popup" style="display: none">
		<transition name="modal">
			<div class="modal-mask bb-white bbm-model-wrap">
				<div class="modal-wrapper">
					<div class="modal-container">
						<header class="bb-model-header">
							<h4><span class="target_name"><?php esc_html_e( 'Leave Group', 'buddyboss' ); ?></span></h4>
							<a class="bb-close-leave-group bb-model-close-button" href="#">
								<span class="bb-icon-l bb-icon-times"></span>
							</a>
						</header>
						<div class="bb-leave-group-content bb-action-popup-content">
							<p><?php esc_html_e( 'Are you sure you want to leave ', 'buddyboss' ); ?><span class="bb-group-name"></span>?</p>
						</div>
						<footer class="bb-model-footer flex align-items-center">
							<a class="bb-close-leave-group bb-close-action-popup" href="#"><?php esc_html_e( 'Cancel', 'buddyboss' ); ?></a>
							<a class="button push-right bb-confirm-leave-group" href="#"><?php esc_html_e( 'Confirm', 'buddyboss' ); ?></a>
						</footer>

					</div>
				</div>
			</div>
		</transition>
	</div> <!-- .bb-leave-group-popup -->

	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php 

else : ?>

	<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

	<?php if(bp_is_group_subgroups()) : 
		$school_id = bp_get_current_group_id();
		$previous_year = get_previous_year();
		$TPRM_school_year = school_implementation_year($school_id);
		$classrooms_for_previous_year = get_school_classrooms_for_year($school_id, $previous_year);
		if ( $TPRM_school_year > 1 && !empty($classrooms_for_previous_year) && is_tprm_manager() ) :	?>		
			<a data-balloon-pos="up"
				data-balloon="<?php esc_attr_e('Duplicate Structure', 'tprm-theme'); ?>"														
				href="#duplicate-structure-content" id="duplicate-structure" class="button duplicate-structure" target="_blank">
				<?php _e('Duplicate Previous Year Structure', 'tprm-theme') ?>
				<span class="bb-icon-duplicate"></span>
			</a>
			<div id="duplicate-structure-content" class="duplicate-structure-content mfp-with-anim mfp-hide white-popup">								
				<div class="duplicate-structure-content-title">
					<span class="bb-icon-l bb-icon-duplicate"></span>
					<span class="duplicate-structure-content-title_text"><?php _e('Duplicate Previous Year Structure', 'tprm-theme') ?></span>	
				</div>
				<!-- Popup content here -->				
				<div class="duplicate-structure-content-body">
					<p class="hide_after_complete"><?php _e('This feature allows you to duplicate all classrooms from the previous year.', 'tprm-theme'); ?></p>
					<p class="change_after_complete"><?php _e('It will create classrooms for this year with the same names, teachers, and corresponding courses.', 'tprm-theme'); ?></p>
					<!-- <p><?php _e(sprintf('You can change teachers for each classroom later from the <a target="_blank" href="%s">Teachers page</a>.', $teachers_tab), 'tprm-theme'); ?></p> -->
					<p><?php _e('You can Manage teachers for each classroom later from the button with the following icon <span class="bb-icon-cogs"></span>', 'tprm-theme'); ?></p>
					<p>
						<div class="classroom-promote-student-note">
							<span class="bb-icon-l bb-icon-exclamation-triangle"></span>
							<?php _e('Important Note :', 'tprm-theme'); ?>
						</div>
						<?php _e('<strong>You should Promote students later for each Classroom from the button with the following icon </strong><span class="bb-icon-user-arrow-up"></span> .', 'tprm-theme'); ?>
					</p>
				</div>
				<div class="tprm-preloader" style="display: none;">
					<?php echo $preloader;  ?>
				</div>
				<div class="duplicate-structure-content-footer">
					<button 
						data-balloon-pos="up"
						data-balloon="<?php _e('I confirm Duplicating All Classrooms of the previous year', 'tprm-theme'); ?>"
						data-security="<?php esc_attr_e($duplicate_structure_nonce); ?>"
						data-school-id="<?php echo esc_attr(bp_get_current_group_id()); ?>"
						type="button"
						id="confirm_duplicate_structure"
						class="confirm_duplicate_structure">
						<?php _e('Confirm', 'tprm-theme'); ?>
					</button>
					<button 
						data-balloon-pos="up"
						data-balloon="<?php esc_attr_e('Cancel change', 'tprm-theme'); ?>"
						class="button"
						type="button"
						id="cancel_duplicate_structure">
						<?php _e('Cancel', 'tprm-theme')?>
					</button>
				</div>
				<button 
					data-balloon-pos="up"
					data-balloon="<?php esc_attr_e('I understand', 'tprm-theme'); ?>"
					class="button"
					type="button"
					style="display: none;"
					id="close_duplicate_structure">
					<?php _e('Ok !', 'tprm-theme')?>
				</button>	
			</div>
		<?php endif; ?>
	<?php endif; ?>

<?php endif; ?>

<?php
bp_nouveau_after_loop();

remove_filter( 'bp_get_group_description_excerpt', 'bb_get_group_description_excerpt_view_more', 99, 2 );
