<?php
/**
 * Template part for displaying page content in dashboard
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package TPRM_Theme
 */
$dash_classe = '';

$dash_classe = is_student() ? 'block' : 'flex';



/* if( wc_memberships_is_user_active_member(get_current_user_id(), 'access-' . get_option('school_year') ) ){
	echo 'member';
}else{
	echo 'not a member';
} */

//echo bp_displayed_user_domain();


//echo bbp_get_user_profile_link( get_current_user_id() )

//echo get_last_user_school() . '<br>';

//echo bp_get_group_permalink(groups_get_group(get_last_user_school()))


?>

	<div class="entry-content <?php echo $dash_classe; ?>">
		<?php
			$current_user = wp_get_current_user();
			$display_name =  function_exists( 'bp_core_get_user_displayname' ) ? bp_core_get_user_displayname( $current_user->ID ) : $current_user->display_name;
		?>
		<div class="bb-dash">
			<div class="bb-dash__welcome">
				<div class="flex align-items-center">					
					<div class="bb-dash__avatar"><?php echo get_avatar( get_current_user_id(), 300 ); ?></div>
					<div class="bb-dash__intro">
						<h2 class="bb-dash__prior">
							<span class="bb-dash__intro"><?php _e( 'Welcome', 'tprm-theme' ) ?>, </span>
							<span class="bb-dash__name"><?php echo $display_name; ?></span>
						</h2>
						<div class="bb-dash__brief"><?php _e( 'To your digital space', 'tprm-theme' ) ?></div>
					</div>
				</div>
			</div><!-- .bb-dash-welcome -->

		<?php 

		get_template_part( 'template-parts/learndash-activity-template' );

		?>

		</div><!-- .bb-dash -->
	
		<!-- start bb-dash-grid -->
		<div class="bb-dash-grid">
		<?php

		$user = wp_get_current_user();

		$valid_roles = ['director', 'teacher', 'group_leader', 'administrator', 'kwf-admin', 'school-admin'];

		$the_roles = array_intersect($valid_roles, $user->roles);

		// The current user does not have any of the 'valid' roles.
		if (!empty($the_roles)) :

			$grid_list = [];

			// Check if the user is a School Administrator
			$is_school_admin = in_array('school-admin', $the_roles);

			// Check if the user is a 'director'
			$is_director = in_array('director', $the_roles);

			// Check if the user is a 'teacher
			$is_teacher = in_array('teacher', $the_roles);

			if (is_TPRM_admin()) {

				$schools_count = count(get_TPRM_schools());
		
				 // Add the Support div for directors
				$grid_list = [
					[
						'id'	=> 'admin-grid',
						'title' => __('Admin Dashboard', 'tprm-theme'),
						'link'  => admin_url('/'),
						'icon'  => 'admin',
						'type'  => 'png',
						'classe'  => ''
					],

					[
						'id'	=> 'year-grid',
						'title' => __('School Year', 'tprm-theme'),
						'link'  => admin_url('/options-general.php?school_year'),
						'icon'  => 'school-year',
						'type'  => 'png',
						'classe'  => ''
					],

					[
						'id'	=> 'schools-grid',
						'title' => __('Schools', 'tprm-theme'),
						'link'  => home_url('schools'),
						'icon'  => 'schools',
						'type'  => 'png',
						'classe'  => ''
					],

					[
						'id'	=> 'members-grid',
						'title' => __('Members', 'tprm-theme'),
						'link'  => home_url('members'),
						'icon'  => 'members',
						'type'  => 'png',
						'classe'  => ''
					]
				];
			} 

			if ($is_school_admin) {

				
				// Add the content of 'ecole-group-container' for directors
				$bp_group_ids = BP_Groups_Member::get_group_ids(get_current_user_id());
				$bp_groups = $bp_group_ids['groups'];

				foreach ($bp_groups as $bp_group) {

					$group_type = bp_groups_get_group_type($bp_group);

					if ($group_type === 'kwf-ecole') {

						// get all details about groups we want to display
						$ecole = groups_get_group($bp_group);
						$ecole_name = $ecole->name;
						$ecole_permalink = home_url('/groupes/' . $ecole->slug);
						$ecole_avatar_url = bp_core_fetch_avatar(
							array(
								'object'  => 'group',
								'item_id' => $ecole->id,
								'html'    => false,
							)
						);
						$ecole_cover_url = bp_attachments_get_attachment(
							'url',
							array(
								'object_dir' => 'groups',
								'item_id'    => $ecole->id,
							)
						);
						$descendant_groups = bp_get_descendent_groups($bp_group, bp_loggedin_user_id());
						$nbr_classes = count($descendant_groups);

						$students_count = 0;

						foreach ($descendant_groups as $pg_subgroup) {
							$pg_subgroup_id = $pg_subgroup->id;					
							$group_members = groups_get_group_members(array(
								'group_id'            => $pg_subgroup_id	
							));
							$count = $group_members['count'];
							$students_count += $count;
						}

						?>
						Manage <?php echo $ecole_name ?>'s school
							<div class="ecole-group-container bb-dash-grid__block">
								<!-- ecole-group-cover -->
								<div class="ecole-group-cover">
									<a href="<?php echo esc_url($ecole_permalink); ?>" rel="noopener noreferrer">
										<?php if (!empty($ecole_cover_url)) { ?>
											<img src="<?php echo esc_url($ecole_cover_url); ?>">
										<?php } ?>
									</a>
								</div>
								<!-- ecole-group-avatar -->
								<div class="ecole-group-avatar">
									<div class="ecole-group-avatar-wrap">
										<a href="<?php echo esc_url($ecole_permalink); ?>" rel="noopener noreferrer">
											<?php if (!empty($ecole_avatar_url)) { ?>
												<img src="<?php echo esc_url($ecole_avatar_url); ?>">
											<?php } ?>
										</a>
									</div>
								</div>
								<!-- ecole-group-main -->
								<div class="ecole-group-main">
									<div class="ecole-group-desc">
										<span class="bb-current-group-kwf-ecole"><?php _e('School group', 'tprm-theme') ?></span>
									</div>
									<div class="ecole-group-name">
										<a href="<?php echo esc_url($ecole_permalink); ?>" rel="noopener noreferrer">
											<span class="name"><?php echo esc_html($ecole_name); ?></span>
										</a>
									</div>
									<div class="ecole-group-classes-count">
										<span class="classes-count"><?php _e('Number of classes', 'tprm-theme'); ?></span>
										<span class="count-nbr"><?php echo esc_html($nbr_classes); ?></span>
									</div>
									<div class="ecole-group-students-count">
										<span class="students-count"><?php _e('Number of students', 'tprm-theme'); ?></span>
										<span class="count-nbr"><?php echo esc_html($students_count); ?></span>
									</div>
								</div>
							</div>
				<?php
					}
				}
				 // Add the Support div for directors
				$grid_list[] = [
					'title' => __('Support', 'tprm-theme'),
					'link'  => home_url('support-portal'),
					'icon'  => 'support',
					'type'  => 'png',
					'classe'  => '',
				];
			} 

			if ($is_director) {
				// Add the content of 'ecole-group-container' for directors
				$bp_group_ids = BP_Groups_Member::get_group_ids(get_current_user_id());
				$bp_groups = $bp_group_ids['groups'];

				foreach ($bp_groups as $bp_group) {

					$group_type = bp_groups_get_group_type($bp_group);

					if ($group_type === 'kwf-ecole') {

						// get all details about groups we want to display
						$ecole = groups_get_group($bp_group);
						$ecole_name = $ecole->name;
						$ecole_permalink = home_url('/groupes/' . $ecole->slug);
						$ecole_avatar_url = bp_core_fetch_avatar(
							array(
								'object'  => 'group',
								'item_id' => $ecole->id,
								'html'    => false,
							)
						);
						$ecole_cover_url = bp_attachments_get_attachment(
							'url',
							array(
								'object_dir' => 'groups',
								'item_id'    => $ecole->id,
							)
						);
						$descendant_groups = bp_get_descendent_groups($bp_group, bp_loggedin_user_id());
						$nbr_classes = count($descendant_groups);

						$students_count = 0;

						foreach ($descendant_groups as $pg_subgroup) {
							$pg_subgroup_id = $pg_subgroup->id;					
							$group_members = groups_get_group_members(array(
								'group_id'            => $pg_subgroup_id	
							));
							$count = $group_members['count'];
							$students_count += $count;
						}

						?>
						
							<div class="ecole-group-container bb-dash-grid__block">
								<!-- ecole-group-cover -->
								<div class="ecole-group-cover">
									<a href="<?php echo esc_url($ecole_permalink); ?>" rel="noopener noreferrer">
										<?php if (!empty($ecole_cover_url)) { ?>
											<img src="<?php echo esc_url($ecole_cover_url); ?>">
										<?php } ?>
									</a>
								</div>
								<!-- ecole-group-avatar -->
								<div class="ecole-group-avatar">
									<div class="ecole-group-avatar-wrap">
										<a href="<?php echo esc_url($ecole_permalink); ?>" rel="noopener noreferrer">
											<?php if (!empty($ecole_avatar_url)) { ?>
												<img src="<?php echo esc_url($ecole_avatar_url); ?>">
											<?php } ?>
										</a>
									</div>
								</div>
								<!-- ecole-group-main -->
								<div class="ecole-group-main">
									<div class="ecole-group-desc">
										<span class="bb-current-group-kwf-ecole"><?php _e('School group', 'tprm-theme') ?></span>
									</div>
									<div class="ecole-group-name">
										<a href="<?php echo esc_url($ecole_permalink); ?>" rel="noopener noreferrer">
											<span class="name"><?php echo esc_html($ecole_name); ?></span>
										</a>
									</div>
									<div class="ecole-group-classes-count">
										<span class="classes-count"><?php _e('Number of classes', 'tprm-theme'); ?></span>
										<span class="count-nbr"><?php echo esc_html($nbr_classes); ?></span>
									</div>
									<div class="ecole-group-students-count">
										<span class="students-count"><?php _e('Number of students', 'tprm-theme'); ?></span>
										<span class="count-nbr"><?php echo esc_html($students_count); ?></span>
									</div>
								</div>
							</div>
				<?php
					}
				}
				 // Add the Support div for directors
				$grid_list[] = [
					'title' => __('Support', 'tprm-theme'),
					'link'  => home_url('support-portal'),
					'icon'  => 'support',
					'type'  => 'png',
					'classe'  => '',
				];
			} 
			//
			if ($is_teacher) {
				
				// Add the teacher specific items to the grid list
				$grid_list = [
					[
						'id'	=> 'classes-grid',
						'title' => __('Classes', 'tprm-theme'),
						'link'  => home_url('groupes'),
						'icon'  => 'classes',
						'type'  => 'png',
						'classe'  => 'classes-container',
					],
					[
						'id'	=> 'onboarding-grid',
						'title' => __('Onboarding', 'tprm-theme'),
						'link'  => home_url('/my-course/onboarding/'),
						'icon'  => 'onboarding',
						'type'  => 'svg',
						'classe'  => 'onboarding-container',
					],
					[
						'id'	=> 'help-grid',
						'title' => __('Need Help', 'tprm-theme'),
						'link'  => home_url('need-help'),
						'icon'  => 'need-help',
						'type'  => 'svg',
						'classe'  => 'need-help-container',
					],
				];


				$en_onboarding_course = get_page_by_path('onboarding', OBJECT, 'sfwd-courses');
				$fr_onboarding_course = get_page_by_path('integration', OBJECT, 'sfwd-courses');
				
				$en_onboarding_course_id = $en_onboarding_course->ID;
				$fr_onboarding_course_id = $fr_onboarding_course->ID;

				$en_onboarding_course_progress = learndash_user_get_course_progress(get_current_user_id(), $en_onboarding_course_id );
				$fr_onboarding_course_progress = learndash_user_get_course_progress(get_current_user_id(), $fr_onboarding_course_id );

				// $status: ["not_started", "in_progress", "completed"],

				if( is_fr_user(get_current_user_id()) || is_bilingual_user(get_current_user_id())){
					$status = $fr_onboarding_course_progress['status'];
				}
				if( is_en_user(get_current_user_id()) || is_TPRM_admin() || is_bilingual_user(get_current_user_id())){
					$status = $en_onboarding_course_progress['status'];
				}

				$status_label = str_replace('_', ' ', $status);
				$status_label = ucwords($status_label);
				$status_label = __($status_label, 'tprm-theme');

				
				/* // Add forum link for teacher, group leader, or administrator
				if (in_array('teacher', $the_roles) || in_array('group_leader', $the_roles) || in_array('administrator', $the_roles)) {
					// Add the new item to the grid list.
					$grid_list[] = [
						'title' => __('Forums', 'tprm-theme'),
						'link'  => home_url('forums'),
						'icon'  => 'forum',
						'type'  => 'png',
						'classe'  => '',
					];
				} */				
			}
			?>			
				<div class="bb-dash-grid__frame bb-dash-grid__cols-1 flex flex-wrap">
					<?php foreach ($grid_list as $item) : ?>
						<?php if (!empty($item['title'])) : ?>
							<div class="bb-dash-grid__block <?php if (!empty($item['id']) && $item['id'] === 'onboarding-grid'){ echo $status . ' ';}; echo $item['icon'] ?> bb-dash-grid__sep">
								<a class="bb-dash-grid__link" href="<?php echo $item['link'] ?>" rel="">
									<div class="bb-dash-grid__inner is-sep">
										<div class="bb-dash-grid__image square <?php echo $item['classe'] ?>">
											<img decoding="async" src="<?php echo TPRM_IMG_PATH . $item['icon'] . '-icon.'. $item['type'] ?>" alt="<?php echo $item['title']; ?>" title="<?php echo $item['title']; ?>">
										</div>
										<div class="bb-dash-grid__body">
											<div class="bb-dash-grid__title">
												<h2 class="bb-dash-grid_<?php echo $item['icon'] ?>_title"><?php echo $item['title']; ?></h2>
											</div>
											<!-- <span class="bb-dash-grid__ico"><i class="bb-icon-l bb-icon-angle-right"></i></span> -->
											<!-- start if item grid is onboarding -->
											<?php if (!empty($item['id']) && $item['id'] === 'onboarding-grid') : ?>
												<div class="bb-dash-grid-onboarding-title">
													<h2 class="bb-dash-grid_<?php echo $status; ?>_title"><?php echo $status_label; ?></h2>
												</div>
											<?php endif; ?>
											<!-- end if item grid is onboarding -->
											<!-- start if item grid is schools -->
											<?php if (!empty($item['id']) && $item['id'] === 'schools-grid') : ?>
												<div class="ecole-group-desc">												
													<span class="bb-current-group-kwf-ecole"><?php echo $schools_count; ?></span>
												</div>
											<?php endif; ?>
											<!-- end if item grid is schools -->
											<!-- start if item grid is school year -->
											<?php if (!empty($item['id']) && $item['id'] === 'year-grid') : ?>
												<div class="ecole-group-desc">												
													<span class="bb-current-group-kwf-ecole"><?php echo get_option('school_year'); ?></span>
												</div>
											<?php endif; ?>
											<!-- end if item grid is school year -->
										</div>
									</div>
								</a>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>

		<?php endif; ?>

	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : 
		
		?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
			sprintf(
			wp_kses(
			/* translators: %s: Name of current post. Only visible to screen readers */
			__( 'Edit <span class="screen-reader-text">%s</span>', 'tprm-theme' ), array(
				'span' => array(
					'class' => array(),
				),
			)
			), get_the_title()
			), '<span class="edit-link">', '</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>