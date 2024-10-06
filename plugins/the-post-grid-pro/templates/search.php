<?php
/**
 * Search Archive Template
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
$layout   = ! empty( $settings['template_search'] ) ? absint( $settings['template_search'] ) : null;
$class    = ! empty( $settings['template_class'] ) ? ' ' . $settings['template_class'] : null;
?>
	<main id="rt-main" class="site-main<?php echo esc_attr( $class ); ?>" role="main">
		<header class="page-header">
			<h1 class="page-title">
				<?php echo esc_html__( 'Search Results for: ', 'the-post-grid-pro' ); ?>
				<span><?php echo esc_html( get_search_query() ); ?></span>
			</h1>
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
