
<div id="all-students" class="students-management-content">

<?php include 'students-header.php'; 


$message_button_args = array(
	'link_text'   => '<i class="bb-icon-l bb-icon-envelope"></i>',
	'button_attr' => array(
		'data-balloon-pos' => 'left',
		'data-balloon'     => esc_html__( 'Send Message to this student', 'tprm-theme' ),
	),
);

global $wpdb, $bp;
$classrooms_tab = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
$std_cred_tbl = $wpdb->prefix . "students_credentials";
$school_id = '';
$suspend_student_nonce = wp_create_nonce('suspend_student_nonce');
$manage_student_classrooms_nonce = wp_create_nonce('manage_student_classrooms_nonce');
$activate_paid_student_nonce = wp_create_nonce('activate_paid_student_nonce');
$search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$is_students_page = function_exists('is_students_page') && is_students_page() ? true : false;

$args = array(
    'per_page'            => 20,
    'exclude_admins_mods' => true,
    'exclude_banned'      => 1,
    'group_role'          => 'member',
    'search_terms'        => $search_term, // Add this line to filter members by search term
);

$args_query = bp_ajax_querystring( 'group_members' );

$args_query = http_build_query($args);

if(is_school(bp_get_current_group_id())){
	$school_id = bp_get_current_group_id();
}else{
	$school_id = bp_get_parent_group_id(bp_get_current_group_id());
}

$classrooms = get_school_classrooms_for_year($school_id);

$footer_buttons_class = ( bp_is_active( 'friends' ) && bp_is_active( 'messages' ) ) ? 'footer-buttons-on' : '';

$is_follow_active = bp_is_active( 'activity' ) && function_exists( 'bp_is_activity_follow_active' ) && bp_is_activity_follow_active();
$follow_class     = $is_follow_active ? 'follow-active' : '';

//page_arg=bpage&type=group_role=student&per_page=100

?>

<?php if ( bp_group_has_members( bp_ajax_querystring( 'group_members' ) . '&' . $args_query ) ) :  ?>

	<?php include 'students-list-header.php'; ?>

	<?php bp_nouveau_group_hook( 'before', 'members_content' ); ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<?php bp_nouveau_group_hook( 'before', 'members_list' ); ?>

	<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?>">

		<?php
		while ( bp_group_members() ) :
			bp_group_the_member();

			// Get the user's role
			$student_id = bp_get_group_member_id();
			$user = get_userdata( $student_id );
			$user_roles = $user->roles;

			// Skip users who do not have the "student" role
			if ( ! in_array( 'school_student', $user_roles ) && ! in_array( 'tprm-student', $user_roles )  ) {
				continue;
			}

			// Get user meta
			$first_name = get_user_meta( $student_id, 'first_name', true );
			$last_name = get_user_meta( $student_id, 'last_name', true );
			$email = $user->user_email;
			$full_name = $first_name . ' ' . $last_name;
			$passwordType = get_user_meta($student_id, 'password_type', true);

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
				<div class="list-wrap students-table<?php echo esc_attr( $footer_buttons_class ); ?> <?php echo esc_attr( $follow_class ); ?> <?php echo $member_loop_has_content ? esc_attr( ' has_hook_content' ) : esc_attr( '' ); ?>">

					<div class="list-wrap-inner">
						<div class="item-avatar student_avatar <?php echo $is_school ? 'school' : ''; ?>">
							<div class="tprm-checkbox">
								<div class="round">
									<input type="checkbox" id="checkbox-<?php echo bp_get_group_member_id(); ?>" />
									<label for="checkbox-<?php echo bp_get_group_member_id(); ?>"></label>
								</div>
							</div>
						</div>

						<div class="student_name">
							<div class="item-block">
								<h2 class="list-title member-name">
									<?php bp_group_member_link(); ?>
								</h2>
								<p class="joined item-meta">
                                    <?php $member_last_activity = bp_get_last_activity( bp_get_member_user_id() );
                                    echo wp_kses_post( $member_last_activity ); ?>
									<?php //bp_group_member_joined_since(); ?>
								</p>
							</div>
						</div><!-- // .item -->

						<!-- login -->						 
						<div class="student_login">
							<span class="copy-target"><?php echo esc_html( $username ); ?></span>
							<button id="copyusername"
								data-target="username-cell" 
								class="student-username copy-button"
								data-feedback="<?php esc_attr_e('Username copied to clipboard !', 'tprm-theme'); ?>"
								data-balloon-pos="down"
								data-balloon="<?php esc_attr_e('Click to copy the username', 'tprm-theme'); ?>">
								<i class="bb-icon-l bb-icon-copy"></i>
							</button>
						</div>
						<!-- login -->

						<!-- password -->
						<div class="student_password">
							<div class="password-toggle">
								<?php if($passwordType == "picture"): 
									$image_dir = wp_upload_dir()['baseurl'] . '/picture-passwords/';
									$picturePassword = get_user_meta($student_id, 'picture_password_image', true);                                
									$imageUrl = $image_dir . $picturePassword;
								?>
									<!-- Placeholder Image -->
									<img class="picture-password" 
										src="<?php echo TPRM_PLACEHOLDER_IMG; ?>" 
										data-password-src="<?php echo esc_url($imageUrl); ?>" 
										data-placeholder-src="<?php echo TPRM_PLACEHOLDER_IMG; ?>" />
									<button type="button" 
										data-balloon-pos="down"
										id="view-picture-password"
										data-balloon="<?php esc_attr_e('Click to view the Picture password', 'tprm-theme'); ?>"
										class="button button-secondary bb-hide-pw hide-if-no-js" aria-label="<?php esc_attr_e( 'Toggle', 'tprm-theme' ); ?>">
										<span class="bb-icon bb-icon-eye-small" aria-hidden="true"></span>
									</button>
								<?php else: ?>
									<input class="copy-target" type="password" value="<?php echo esc_html($stdcred); ?>" />
									<button id="copypass"
											data-target="password-cell" 
											class="student-password copy-button"
											data-feedback="<?php esc_attr_e('Password copied to clipboard !', 'tprm-theme'); ?>"
											data-balloon-pos="down"
											data-balloon="<?php esc_attr_e('Click to copy the password', 'tprm-theme'); ?>"
											data-balloon-pos="right" data-balloon="<?php esc_attr_e('Click to copy the student credentials', 'tprm-theme'); ?>">
										<i class="bb-icon-l bb-icon-copy"></i>
									</button>
									<button type="button" 
										data-balloon-pos="down"
										data-balloon="<?php esc_attr_e('Click to view the password', 'tprm-theme'); ?>"
										class="button button-secondary bb-hide-pw hide-if-no-js" aria-label="<?php esc_attr_e( 'Toggle', 'tprm-theme' ); ?>">
										<span class="bb-icon bb-icon-eye-small" aria-hidden="true"></span>
									</button>
								<?php endif; ?>
							</div>
						</div>
						<!-- password -->

                        <?php
                        if ($is_school) {
                            $this_year = get_option('school_year');
                            $previous_year = get_previous_year();
                            ?>
                            <div class="student_classroom">
								
								<?php 
									$classroom_id = get_student_classroom_for_year($student_id, $this_year); 
								
									if($classroom_id){
										$classroom = groups_get_group($classroom_id);   ?>                                    
										<a target="_blank" href="<?php echo esc_url(bp_get_group_permalink($classroom)); ?>">
											<?php echo esc_html($classroom->name); ?>
										</a>
									<?php                                           
									}                            
								?> 
                               <!--  <div class="sub-cell-container">
                                    <div class="sub-cell previous-year <?php echo esc_attr($previous_year) ?>">
                                        <?php
                                        $classroom_id = get_student_classroom_for_year($student_id, get_previous_year()); 
                                        if($classroom_id){
                                            $classroom = groups_get_group($classroom_id);   ?>                                    
                                            <a target="_blank" href="<?php echo esc_url(bp_get_group_permalink($classroom)); ?>">
                                                <?php echo esc_html($classroom->name); ?>
                                            </a>
                                        <?php                                           
                                        }                            
                                        ?>                                 
                                    </div>
                                    <div class="sub-cell this-year <?php echo esc_attr($this_year) ?>">
                                        <?php 
                                        $classroom_id = get_student_classroom_for_year($student_id); 
                                        if($classroom_id){
                                            $classroom = groups_get_group($classroom_id);   ?>                                    
                                            <a target="_blank" href="<?php echo esc_url(bp_get_group_permalink($classroom)); ?>">
                                                <?php echo esc_html($classroom->name); ?>
                                            </a>
                                        <?php                                           
                                        }                            
                                        ?> 
                                    </div>
                                </div> -->
                            </div>
                            <?php
                            }                    
                        ?>
						<!-- student_classroom -->

                        <!-- student_account_status -->

						<div class="student_account_status">
                            <?php 
                            $account_status = '';
                            $text_status = '';
							$text_status = '';
					
                            if (is_active_student($student_id)) {
								$account_status = '<a class="activated" disabled>
														<span>																												
															<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12">
																<path id="test" fill="#13c40e" d="M4.76499011,6.7673683 L8.2641848,3.26100386 C8.61147835,2.91299871 9.15190114,2.91299871 9.49919469,3.26100386 C9.51164115,3.27347582 9.52370806,3.28637357 9.53537662,3.29967699 C9.83511755,3.64141434 9.81891834,4.17816549 9.49919469,4.49854425 L5.18121271,8.82537365 C4.94885368,9.05820878 4.58112654,9.05820878 4.34876751,8.82537365 L2.50080531,6.97362503 C2.48835885,6.96115307 2.47629194,6.94825532 2.46462338,6.93495189 C2.16488245,6.59321455 2.18108166,6.0564634 2.50080531,5.73608464 C2.84809886,5.3880795 3.38852165,5.3880795 3.7358152,5.73608464 L4.76499011,6.7673683 Z"></path>
															</svg>																													
														</span>
														<ul>
															<li>' . __( "Active", "tprm-theme" ) . '</li>
														</ul>
													</a>
												';
                                $text_status = 'tprm-active';
                                $active_count++;
                            } else {

								global $blog_id, $wpdb;
								$user_lang = $wpdb->get_blog_prefix($blog_id) . 'lang';
								$user_language = get_user_meta($student_id, $user_lang, true);
								$kmc_helper = TPRM_membership_helper::get_instance();
								if($user_language === 'en'){
									$access_product_id = $kmc_helper->TPRM_multilang_product()['en']['access'];
								}else{
									$access_product_id = $kmc_helper->TPRM_multilang_product()['fr']['access'];
								}

								$school_seats = get_school_seats($school_id);
								if(is_tprm_manager()){
									$membershipID = get_this_year_membership_id();
									$account_status = '<a class="activate" data-studentID="' . esc_attr($student_id) . '" data-schoolID="' . esc_attr($school_id) . '" data-security="' . esc_attr($activate_paid_student_nonce) . '">  
														<span>
															<svg><use xlink:href="#circle"></svg>																														
															<svg><use xlink:href="#arrow"></svg>																												
															<svg><use xlink:href="#check"></svg>																														
														</span>
														<ul>															
															<li>' . __( "Activate", "tprm-theme" ) . '</li>														
															<li>' . __( "Waiting", "tprm-theme" ) . '</li>															
															<li>' . __( "Active", "tprm-theme" ) . '</li>
														</ul>
													</a>
													<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
														<symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="circle">
															<circle cx="8" cy="8" r="7.5"></circle>
														</symbol>
														<symbol id="arrow" viewBox="0 0 122.88 122.88">
															<path fill="current_color" d="M61.44,0A61.44,61.44,0,1,1,0,61.44,61.44,61.44,0,0,1,61.44,0Z"/>
															<path fill="#fff" d="M81,44.75a7.08,7.08,0,0,1,10.71-9.27,40,40,0,1,1-60.87.39A7.07,7.07,0,0,1,41.67,45,25.85,25.85,0,1,0,81,44.75ZM68.54,47.92a7.1,7.1,0,1,1-14.2,0V26.74a7.1,7.1,0,1,1,14.2,0V47.92Z"/>
															<path fill="current_color" d="M61.44,2.23a59.15,59.15,0,0,1,59.15,59.15c0,.77,0,1.54,0,2.31a59.14,59.14,0,0,0-118.2,0c0-.77,0-1.54,0-2.31A59.15,59.15,0,0,1,61.44,2.23Z"/>
														</symbol>									
														<symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" id="check">
																<path id="test" d="M4.76499011,6.7673683 L8.2641848,3.26100386 C8.61147835,2.91299871 9.15190114,2.91299871 9.49919469,3.26100386 C9.51164115,3.27347582 9.52370806,3.28637357 9.53537662,3.29967699 C9.83511755,3.64141434 9.81891834,4.17816549 9.49919469,4.49854425 L5.18121271,8.82537365 C4.94885368,9.05820878 4.58112654,9.05820878 4.34876751,8.82537365 L2.50080531,6.97362503 C2.48835885,6.96115307 2.47629194,6.94825532 2.46462338,6.93495189 C2.16488245,6.59321455 2.18108166,6.0564634 2.50080531,5.73608464 C2.84809886,5.3880795 3.38852165,5.3880795 3.7358152,5.73608464 L4.76499011,6.7673683 Z"></path>
														</symbol>
													</svg>';
								}else{
									$account_status = '<a class="inactivated" disabled>
													<span>																												
														<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 122.88"><defs><style>.cls-1{fill:#ff4141;fill-rule:evenodd;}</style></defs><title>cross</title><path class="cls-1" d="M6,6H6a20.53,20.53,0,0,1,29,0l26.5,26.49L87.93,6a20.54,20.54,0,0,1,29,0h0a20.53,20.53,0,0,1,0,29L90.41,61.44,116.9,87.93a20.54,20.54,0,0,1,0,29h0a20.54,20.54,0,0,1-29,0L61.44,90.41,35,116.9a20.54,20.54,0,0,1-29,0H6a20.54,20.54,0,0,1,0-29L32.47,61.44,6,34.94A20.53,20.53,0,0,1,6,6Z"/></svg>	
													</span>
													<ul>
														<li>' . __( "Inactive", "tprm-theme" ) . '</li>
													</ul>
												</a>
											';
								}
								
								$text_status = 'tprm-inactive';
								$inactive_count++;
                            }
                            ?>
                            <div class="column-tprm-student-status-<?php echo esc_attr($text_status); ?>" id="<?php echo esc_attr($text_status); ?>"><?php echo $account_status; ?></div>
                        </div>


						<!-- student_actions -->
						<div class="student_actions">	
							<div class="bb_more_options member-dropdown">
								<a href="#" class="bb_more_options_action">
									<i class="bb-icon-menu-dots-h"></i>
								</a>
								<div class="bb_more_options_list">
									<?php include 'student-actions.php'; ?>
								</div>
							</div><!-- .bb_more_options -->					     
						</div>
						<!-- student_actions -->
						
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

	<div class="bp-pagination">
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

</div><!-- end students manage tab  -->
