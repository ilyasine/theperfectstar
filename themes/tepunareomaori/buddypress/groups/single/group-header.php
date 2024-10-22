<?php
/**
 * tepunareomaori School and Classroom Header
 *
 * This template is used to render the group header.
 *
 *
 * @since   tepunareomaori 3.0.0
 * @version 3.0.0
 */

$group_link       = bp_get_group_permalink();
$admin_link       = trailingslashit( $group_link . 'admin' );
$group_avatar     = trailingslashit( $admin_link . 'group-avatar' );
$group_cover_link = trailingslashit( $admin_link . 'group-cover-image' );
$tooltip_position = bp_disable_group_cover_image_uploads() ? 'down' : 'up';
$TPRM_school_year  = '';
$school_code      = '';
$classroom_code   = '';
$group_id 		  = bp_get_current_group_id();
$disconnect_classroom_nonce = wp_create_nonce('disconnect_classroom_nonce');

if(!is_school($group_id)){  // classroom
	$classroom_code = groups_get_groupmeta($group_id, 'classroom_code') ;
	?>
	<div class="classroom_manage_header">
		<div class="classroom_manage_header_left">
			<div class="classroom_avatar">
				<?php 			
				$parent_id = bp_get_parent_group_id();			
				if ( 0 !== $parent_id ) {
					$parent_group = groups_get_group( $parent_id );
					$school_permalink = bp_get_group_permalink($parent_group); ?>			
						<a href="<?php echo esc_url($school_permalink); ?>" rel="noopener noreferrer">
							<?php echo bp_core_fetch_avatar( array( 'item_id' => $parent_id, 'object' => 'group' ) ); ?>
						</a>
					<?php 
				}
				?>				
			</div>
			<div class="classroom_name">
				<?php echo bp_get_current_group_name(); ?>
			</div>
			<div class="classroom_level">
				<span class="classroom_level_label"><?php _e('Level  ', 'tprm-theme') ?></span>
				<span class="classroom_level_value"><?php echo groups_get_groupmeta($group_id, 'classroom_level') ?></span>			
			</div>
			<div class="classroom_type">
				<?php _e('Type  ', 'tprm-theme') ?>
				<?php echo wp_kses( bp_nouveau_group_meta()->status, array( 'span' => array( 'class' => array() ) ) ); ?>			
			</div>
		</div>
		<div class="classroom_manage_header_right">
			<div class="classroom_code">
				<span class="classroom_code_label"><?php _e('Classroom Code ') ?></span>
				<div class="classroom_code_inner">			
					<span class="classroom_code_text"><?php echo esc_html($classroom_code); ?></span>
					<span class="bb-icon-l bb-icon-copy"></span>					
				</div>
			</div>
			<div class="disconnect-classroom" >
				<div class="disconnect-classroom-button-parent">
					<a href="#" class="button disconnect-classroom-button"
						data-security="<?php echo esc_attr( $disconnect_classroom_nonce ); ?>"
						data-balloon-pos="up"
						data-balloon="<?php echo esc_html__( 'Click here to disconnect your students', 'tprm-theme' ); ?>">
						<?php echo esc_html__( 'Disconnect Classroom', 'tprm-theme' ); ?>
						<i class="bb-icon-l bb-icon-sign-in"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php
}

 
 