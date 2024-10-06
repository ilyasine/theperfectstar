<?php

/**
 * Archive Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums">

	<?php do_action( 'bbp_template_before_forums_index' ); 
	
	// hide if not admin
	if ( current_user_can('administrator') ) :?>

	<?php if ( bbp_has_forums() ): ?>

		<?php bbp_get_template_part( 'loop', 'forums' ); ?>

		<?php bbp_get_template_part( 'pagination', 'forums' ); ?>

	<?php else : ?>

		<?php bbp_get_template_part( 'feedback', 'no-forums' ); ?>

	<?php endif; ?>

	<?php endif; // hide if not admin ?>

	<?php do_action( 'bbp_template_after_forums_index' ); ?>

	<!-- Topics List -->
	<?php if ( bbp_has_topics('paged=1') ) : ?>

		<?php bbp_get_template_part( 'loop', 'topics' ); ?>

		<?php bbp_get_template_part( 'pagination', 'topics' ); ?>

	<?php else : ?>

		<?php bbp_get_template_part( 'feedback', 'no-topics' ); ?>

	<?php endif; ?>

</div>