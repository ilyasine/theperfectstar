<?php
/**
 * The template for displaying schools page
 *
 * Template Name: Schools
 * 
 * This is the template that displays the schools page for admins .
 *
 * @package TPRM_Theme
 */

get_header();

?>

    <div id="primary" class="content-area buddypress-wrap">
        <main id="main" class="site-main">

			<?php if ( $schools ) :

				do_action( THEME_HOOK_PREFIX . '_template_parts_content_top' );
		
					if ( is_page_template('schools.php') && is_TPRM_admin() ) {
						do_action( THEME_HOOK_PREFIX . '_single_template_part_content', 'schools' );
					}else{
						bp_do_404();
						load_template( get_404_template() );
					}					
					
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
