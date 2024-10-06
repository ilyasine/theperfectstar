<?php
/*
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 * phpcs:disable WordPress.Security.NonceVerification.Missing
 * phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized:
 */

use RT\ThePostGrid\Helpers\Fns;

$current_post_id = '';
if ( ! empty( $_GET['pid'] ) ) {
	$current_post_id = absint( $_GET['pid'] );
}
$current_post  = get_post( $current_post_id );
$category_list = get_the_category_list( ' ', ' ', $current_post_id );
$tag_list      = get_the_tag_list( ' ', ' ', ' ', $current_post_id );

?>
<div id="tpg-postbox" class="tpg-postbox post-view-box">

	<div class="featured-image-container">
		<?php echo get_the_post_thumbnail( $current_post_id, 'full' ); ?>
	</div>
	<h2 class="post-title"><?php echo esc_attr( $current_post->post_title ); ?></h2>

		<div class="cat-list post-meta">
			<span class="label"><?php esc_html_e( 'Categories:', 'the-post-grid' ); ?></span>
			<?php echo wp_kses_post( $category_list ); ?>
		</div>


	<?php if ( $tag_list && ! is_wp_error( $tag_list ) ) : ?>
		<div class="tag-list post-meta">
			<span class="label"><?php esc_html_e( 'Tags:', 'the-post-grid' ); ?></span>
			<?php echo wp_kses_post( $tag_list ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $current_post->post_content ) ) : ?>
	<div class="post-content">
		<h3 class="label-title"><?php esc_html_e( 'Post Content:', 'the-post-grid' ); ?></h3>
		<?php echo wp_kses_post( $current_post->post_content ); ?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $current_post->post_excerpt ) ) : ?>
	<div class="post-excerpt">
		<h3 class="label-title"><?php esc_html_e( 'Post Excerpt:', 'the-post-grid' ); ?></h3>
		<?php echo wp_kses_post( $current_post->post_excerpt ); ?>
	</div>
	<?php endif; ?>

	<div class="post-btn-action">
		<a class="btn edit-btn"
		   href="<?php echo esc_url( Fns::get_account_endpoint_url( 'edit-post' ) ); ?>?pid=<?php echo esc_attr( $current_post_id ); ?>">
			<?php Fns::dashboard_icon( 'edit' ); ?>
			<?php echo esc_html__( 'Edit Post', 'the-post-grid' ); ?>
		</a>
		<a class="btn delete-btn tpg-delete-post" href="" data-id="<?php echo esc_attr( $current_post_id ); ?>">
			<?php Fns::dashboard_icon( 'delete' ); ?>
			<?php echo esc_html__( 'Delete Post', 'the-post-grid' ); ?>
		</a>
	</div>

</div>