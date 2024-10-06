<?php
/**
 * The template for displaying Dashboard pages
 *
 * Template Name: Dashboard
 * 
 * This is the template that displays the dashboard for differents profile types .
 *
 * @package TPRM_Theme
 */

get_header();


?>

    <div id="primary" class="content-area bb-grid-cell">
        <main id="main" class="site-main">

			<?php if ( have_posts() ) :

				do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' );

				while ( have_posts() ) :
					the_post();

					if ( is_page_template('dashboard.php') && is_user_logged_in() ) :
						
						do_action( THEME_HOOK_PREFIX . '_single_template_part_content', 'dashboard' );

					endif;
										
				endwhile; // End of the loop.
			else :
				get_template_part( 'template-parts/content', 'none' );
				?>

			<?php endif; ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
if ( is_search() ) {
	get_sidebar( 'search' );
} else {
	get_sidebar( 'page' );
}
?>

<?php
get_footer();
