
<div id="all-teachers" class="teachers-management-content">

<?php 

$message_button_args = array(
	'link_text'   => '<i class="bb-icon-l bb-icon-envelope"></i>',
	'button_attr' => array(
		'data-balloon-pos' => 'down',
		'data-balloon'     => esc_html__( 'Message', 'tprm-theme' ),
	),
);

global $wpdb, $bp;
$classrooms_tab = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
$std_cred_tbl = $wpdb->prefix . "students_credentials";
$school_id = '';
$delete_teacher_nonce = wp_create_nonce('delete_teacher_nonce');
$manage_teacher_classrooms_nonce = wp_create_nonce('manage_teacher_classrooms_nonce');
$is_school = is_school(bp_get_current_group_id()) ? true : false;

if($is_school){
	$school_id = bp_get_current_group_id();
	//$group_role = 'mod';
	$group_role = array('admin', 'mod');// for teachers having role of school_leader or director
}else{
	$school_id = bp_get_parent_group_id(bp_get_current_group_id());
	$group_role = 'admin';
}

// Define the arguments array
$args = array(
    'per_page'            => 20,
    'exclude_admins_mods' => true,
    'exclude_banned'      => 1,
    'group_role'          => $group_role,
);

// Convert the arguments to a query string
$args_query = http_build_query($args);

$classrooms = get_school_classrooms_for_year($school_id);

$footer_buttons_class = ( bp_is_active( 'friends' ) && bp_is_active( 'messages' ) ) ? 'footer-buttons-on' : '';

$is_follow_active = bp_is_active( 'activity' ) && function_exists( 'bp_is_activity_follow_active' ) && bp_is_activity_follow_active();
$follow_class     = $is_follow_active ? 'follow-active' : '';
?>

	<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) . '&' . $args_query ) ) :  ?>

	<?php include 'teachers-list-header.php'; ?>

	<?php bp_nouveau_group_hook( 'before', 'members_content' ); ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<?php bp_nouveau_group_hook( 'before', 'members_list' ); ?>

	<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?>">

		<?php
		while ( bp_group_members() ) :
			bp_group_the_member();

			// Get the user's role
			$teacher_id = bp_get_group_member_id();
			$user = get_userdata( $teacher_id );
			$user_roles = $user->roles;

			// Skip users who do not have the "teacher" role
			if ( ! in_array( 'school_staff', $user_roles ) ) {
				continue;
			}

			// Get user meta
			$first_name = get_user_meta( $teacher_id, 'first_name', true );
			$last_name = get_user_meta( $teacher_id, 'last_name', true );
			$email = $user->user_email;
			$full_name = $first_name . ' ' . $last_name;

			// Get the password from the students_credentials table
			$username = $user->user_login;
			$sql = $wpdb->prepare("SELECT stdcred FROM " . esc_sql($std_cred_tbl) . " WHERE username = %s", esc_sql($username));
			$stdcred_object = $wpdb->get_results($sql, OBJECT);
			$stdcred = !empty($stdcred_object) ? $stdcred_object[0]->stdcred : '';

			// Check if members_list_item has content.
			ob_start();
			bp_nouveau_member_hook( '', 'members_list_item' );
			$members_list_item_content = ob_get_contents();
			ob_end_clean();
			$member_loop_has_content = empty( $members_list_item_content ) ? false : true;
			?>
	

			<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( bp_get_group_member_id() ); ?>" data-bp-item-component="members">
				<div class="list-wrap teachers-table<?php echo esc_attr( $footer_buttons_class ); ?> <?php echo esc_attr( $follow_class ); ?> <?php echo $member_loop_has_content ? esc_attr( ' has_hook_content' ) : esc_attr( '' ); ?>">

					<div class="list-wrap-inner">
						<div class="item-avatar teacher_avatar">
							<a href="<?php bp_group_member_domain(); ?>">
								<?php
								if ( function_exists( 'bb_user_presence_html' ) ) {
									bb_user_presence_html( bp_get_group_member_id() );
								} elseif ( function_exists( 'bb_current_user_status' ) ) {
									bb_current_user_status( bp_get_group_member_id() );
								} else {
									bb_user_status( bp_get_group_member_id() );						
								}

								bp_group_member_avatar();
								?>
							</a>
						</div>

						<div class="teacher_name">
							<div class="item-block">
								<h2 class="list-title member-name">
									<?php bp_group_member_link(); ?>
								</h2>

								<p class="joined item-meta">
									<?php echo bp_get_last_activity(bp_get_group_member_id()); ?>															
								</p>
							</div>
						</div><!-- // .item -->

						<!-- Email -->						 
						<div class="teacher_email">							
							<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</div>
						<!-- Email -->

						<!-- login -->						 
						<div class="teacher_login">							
							<?php echo esc_html( $username ); ?>
						</div>
						<!-- login -->

						<!-- password -->						 
						<div class="teacher_password">
							<div class="password-toggle">																
								<input  class="copy-target" type="password" value="<?php echo esc_html($stdcred); ?>" />
								<button id="copypass"
									data-target="password-cell" 
									class="teacher-password copy-button"
									data-feedback="<?php esc_attr_e('Password copied to clipboard !', 'tprm-theme'); ?>"
									data-balloon-pos="down"
									data-balloon="<?php esc_attr_e('Click to copy the password', 'tprm-theme'); ?>"
									data-balloon-pos="right" data-balloon="<?php esc_attr_e('Click to copy the teacher password', 'tprm-theme'); ?>">
									<i class="bb-icon-l bb-icon-copy"></i>
								</button>
								<button type="button" 
									data-balloon-pos="down"
									data-balloon="<?php esc_attr_e('Click to view the password', 'tprm-theme'); ?>"
									class="button button-secondary bb-hide-pw hide-if-no-js" aria-label="<?php esc_attr_e( 'Toggle', 'tprm-theme' ); ?>">
									<span class="bb-icon bb-icon-eye-small" aria-hidden="true"></span>
								</button>
							</div>	
						</div>

						<!-- password -->

						<!-- teacher_classrroms_count -->						 
						<div class="teacher_classrooms_count">
							<div class="teacher_classrooms_count_inner">
								<?php echo get_teacher_classrooms_count($teacher_id); ?>
							</div>
						</div>
						<!-- teacher_classrroms_count -->

						<!-- teacher_actions -->
						<div class="teacher_actions">	
						<?php 
                    		include 'teacher-actions.php';																					
                		?>
						</div>
						<!-- teacher_actions -->
						
					</div>

					<div class="bp-members-list-hook">
						<?php if ( $member_loop_has_content ) { ?>
							<a class="more-action-button" href="#"><i class="bb-icon-f bb-icon-ellipsis-h"></i></a>
						<?php } ?>
						<div class="bp-members-list-hook-inner">
							<?php bp_nouveau_member_hook( '', 'members_list_item' ); ?>
						</div>
					</div>

				</div>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php bp_nouveau_group_hook( 'after', 'members_list' ); ?>

	<?php bp_nouveau_pagination( 'bottom' ); ?>

	<?php bp_nouveau_group_hook( 'after', 'members_content' ); ?>

	<div class="bp-pagination no-ajax">
		<div class="pag-count bottom">
			<span>
				<?php bp_group_member_pagination_count(); ?>			
			</span>
		</div>

		<div class="bp-pagination-links bottom">
			<?php bp_group_member_pagination(); ?>			
		</div>
	</div>

<?php else : ?>

	<?php bp_nouveau_user_feedback( 'group-members-none' ); ?>

	<?php
endif;

	?>

</div>

</div><!-- end teachers manage tab  -->