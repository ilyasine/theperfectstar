<?php
/**
 * Template part for displaying header aside.
 *
 * @package BuddyBoss_Theme
 */
$show_search        = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_header_search' );
$show_messages      = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_messages' ) && is_user_logged_in();
$show_notifications = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_notifications' ) && is_user_logged_in();
$show_shopping_cart = buddyboss_theme_get_option( 'desktop_component_opt_multi_checkbox', 'desktop_shopping_cart' );
$header_style       = (int) buddyboss_theme_get_option( 'buddyboss_header' );
$profile_dropdown   = buddyboss_theme_get_option( 'profile_dropdown' );
$is_lms_inner       = (
	( class_exists( 'SFWD_LMS' ) && buddyboss_is_learndash_inner() ) ||
	( class_exists( 'LifterLMS' ) && buddypanel_is_lifterlms_inner() ) ||
	( function_exists( 'tutor' ) && buddyboss_is_tutorlms_inner() )
);
?>

<div id="header-aside" class="header-aside <?php echo esc_attr( $profile_dropdown ); ?>">
	<div class="header-aside-inner">

		<?php
		if ( $is_lms_inner ) :
			?>
			<a href="#" id="bb-toggle-theme">
				<span class="sfwd-dark-mode" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Dark Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-rl bb-icon-moon"></i></span>
				<span class="sfwd-light-mode" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Light Mode', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-sun"></i></span>
			</a>
			<a href="#" class="header-maximize-link course-toggle-view" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Maximize', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-expand"></i></a>
			<a href="#" class="header-minimize-link course-toggle-view" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Minimize', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-merge"></i></a>

			<?php
		elseif ( is_user_logged_in() ) :
			if ( $show_search && 4 !== $header_style ) :
				?>
				<a href="#" class="header-search-link" data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Search', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-search"></i></a>
				<span class="bb-separator"></span>
				<?php
			endif;

			if ( function_exists( 'bp_is_active' ) && bp_is_active( 'messages' ) ) :
				get_template_part( 'template-parts/messages-dropdown' );
			endif;

			if ( $show_notifications && function_exists( 'bp_is_active' ) && bp_is_active( 'notifications' ) ) :
				get_template_part( 'template-parts/notification-dropdown' );
			endif;

			if ( $show_shopping_cart && class_exists( 'WooCommerce' ) ) :
				get_template_part( 'template-parts/cart-dropdown' );
			endif;
		endif;

		if ( ! is_user_logged_in() ) :
			?>

			<?php if ( $show_search && 4 !== $header_style && !$is_lms_inner ) : ?>
				<a href="#" class="header-search-link" data-balloon-pos="down" data-balloon="<?php esc_attr_e( 'Search', 'buddyboss-theme' ); ?>"><i class="bb-icon-l bb-icon-search"></i></a>
				<span class="search-separator bb-separator"></span>
				<?php
			endif;

			if ( $show_shopping_cart && class_exists( 'WooCommerce' ) && !$is_lms_inner ) :
				get_template_part( 'template-parts/cart-dropdown' );
			endif;
			?>
				
			<?php

			endif;

			if (
				3 === $header_style || $is_lms_inner
			) :
			    echo buddypanel_position_right();
			endif;
		?>

	</div><!-- .header-aside-inner -->
</div><!-- #header-aside -->
