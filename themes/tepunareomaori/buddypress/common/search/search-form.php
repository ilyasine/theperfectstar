<?php
/**
 * BP Object search form
 *
 * @since 3.0.0
 * @version 3.1.0
 */

?>

<div class="<?php bp_nouveau_search_container_class(); ?> bp-search" data-bp-search="<?php bp_nouveau_search_object_data_attr() ;?>">
	<form action="" method="get" class="bp-dir-search-form search-form-has-reset" id="<?php bp_nouveau_search_selector_id( 'search-form' ); ?>" autocomplete="off">
		<button type="submit" id="<?php bp_nouveau_search_selector_id( 'search-submit' ); ?>" class="nouveau-search-submit search-form_submit" name="<?php bp_nouveau_search_selector_name( 'search_submit' ); ?>">
			<span class="dashicons dashicons-search" aria-hidden="true"></span>
			<span id="button-text" class="bp-screen-reader-text"><?php echo esc_html_x( 'Search', 'button', 'buddyboss-theme' ); ?></span>
		</button>
		<label for="<?php bp_nouveau_search_selector_id( 'search' ); ?>" class="bp-screen-reader-text"><?php bp_nouveau_search_default_text( '', false ); ?></label>

		<input id="<?php bp_nouveau_search_selector_id( 'search' ); ?>" name="<?php bp_nouveau_search_selector_name(); ?>" type="search"  placeholder="<?php bp_nouveau_search_default_text(); ?>" />

		<button type="reset" class="search-form_reset">
			<span class="bb-icon-rf bb-icon-times" aria-hidden="true"></span>
			<span class="bp-screen-reader-text"><?php esc_html_e( 'Reset', 'buddyboss-theme' ); ?></span>
		</button>

	</form>
</div>
