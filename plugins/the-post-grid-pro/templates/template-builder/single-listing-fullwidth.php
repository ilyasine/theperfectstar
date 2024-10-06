<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

wp_enqueue_script('rttpg-block-pro');
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-full-width' );
}

get_header( 'listing' );
?>

	<div class="builder-content content-invisible"> <!-- Removed jumping issue after loded -->
		<?php
		/**
		 * Before Header-Footer page template content.
		 *
		 * Fires before the content of Elementor Header-Footer page template.
		 *
		 * @since 2.0.0
		 */
		do_action( 'elementor/page_templates/header-footer/before_content' );

			do_action( 'el_builder_template_content' );

		do_action( 'elementor/page_templates/header-footer/after_content' );
		?>
	</div>
<?php
get_footer( 'listing' );
