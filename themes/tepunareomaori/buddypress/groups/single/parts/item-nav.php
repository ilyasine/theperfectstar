<?php
/**
 * BuddyPress Single Groups item Navigation
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/single/parts/item-nav.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */
$group_id 		  = bp_get_current_group_id();
?>

<nav class="<?php bp_nouveau_single_item_nav_classes(); ?>" id="object-nav" role="navigation" aria-label="<?php esc_attr_e( 'Group menu', 'buddyboss' ); ?>">

	<?php if ( bp_nouveau_has_nav( array( 'object' => 'groups' ) ) ) : ?>

		<ul>

			<?php
			while ( bp_nouveau_nav_items() ) :
				bp_nouveau_nav_item();
			?>

				<li id="<?php bp_nouveau_nav_id(); ?>" class="<?php bp_nouveau_nav_classes(); ?>">
					<a href="<?php bp_nouveau_nav_link(); ?>" id="<?php bp_nouveau_nav_link_id(); ?>">
						<div class="bb-single-nav-item-point"><?php bp_nouveau_nav_link_text(); ?></div>

						<?php if ( bp_nouveau_nav_has_count() ) : ?>
							<span class="count"><?php bp_nouveau_nav_count(); ?></span>
						<?php endif; ?>
					</a>
					
				</li>

			<?php endwhile; ?>

			<?php bp_nouveau_group_hook( '', 'options_nav' ); ?>

		</ul>

		<?php 
		if(is_school($group_id)){ //school
			$school_code = groups_get_groupmeta($group_id, 'school_code');
			?>
			<div class="school_manage_header">
				<div class="school_code">
					<span class="school_code_label"><?php _e('School Code ', 'tprm-theme') ?></span>
					<div class="school_code_inner">			
						<span class="school_code_text"><?php echo esc_html($school_code); ?></span>
						<span class="bb-icon-l bb-icon-copy"></span>					
					</div>
				</div>
			</div>
			<?php 
		}else if(is_group_courses_page()){
			
			?>
			<div class="courses-order-container">
				<button id="toggle-order" type="button"
					data-balloon-pos="up"
					data-balloon="<?php esc_attr_e('Press here to start ordering the courses', 'tprm-theme'); ?>">
					<span class="toggle-order-text">
						<?php _e('Unlock order', 'tprm-theme')?>
					</span>
					<span id="lock-icon" class="bb-icon-l bb-icon-lock-alt"></span>
				</button>
			</div>
			<?php
		}
		?>

	<?php endif; ?>

</nav>
