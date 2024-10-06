<?php
/**
 * Category Archive Template
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

get_header();
global $post;

$settings = get_option( rtTPG()->options['settings'] );
$layout   = ! empty( $settings['template_category'] ) ? absint( $settings['template_category'] ) : null;
$class    = ! empty( $settings['template_class'] ) ? ' ' . $settings['template_class'] : null;
?>
	<main id="rt-main" class="site-main<?php echo esc_attr( $class ); ?>" role="main">
		<header class="page-header">
			<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
		</header><!-- .page-header -->
		<div class="rt-single-container">
			<div class="rt-row">
				<?php
				if ( $layout ) {
					echo do_shortcode( '[the-post-grid id="' . $layout . '"]' );
				}
				?>
			</div>
		</div>
	</main>
<?php
get_footer();
