<?php
/**
 *
 * @author      RadiusTheme
 * @package     the-post-grid/templates
 * @version     1.0.0
 */

use RT\ThePostGrid\Helpers\Fns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$current_user = $data['current_user'];
?>
<div class="my-post">
	<div class="myaccount-title-wrapper">
		<h2 class="title"><?php echo esc_html__( 'My Post', 'the-post-grid' ); ?></h2>
		<?php Fns::current_time(); ?>
	</div>
	<div class="latest-post-wrapper">
		<?php
		$args = [
			'posts_per_page' => 6,
			'post_type'      => 'post',
			'post_status'    => [ 'publish', 'draft', 'pending', 'future', 'private' ],
			'oderby'         => 'date',
			'order'          => 'DESC',
			'paged'          => Fns::paged(),
			'author'         => $current_user->ID,
		];

		$_latest_post = new \WP_Query( $args );

		$count = 0;
		if ( $_latest_post->have_posts() ) {
			while ( $_latest_post->have_posts() ) {
				$_latest_post->the_post();
				$data       = [
					'excerpt_limit' => 30,
					'excerpt_type'  => 'word',
				];
				$pid        = get_the_ID();
				$excerpt    = Fns::get_the_excerpt( $pid, $data );
				$categories = Fns::rt_get_the_term_list( $pid, 'category', null, '<span class="rt-separator">,</span>' );
				$tags       = Fns::rt_get_the_term_list( $pid, 'post_tag', null, '<span class="rt-separator">,</span>' );

				?>
				<div class="post-item tpg-post-container">
					<div class="post-image">
						<?php the_post_thumbnail( 'medium' ); ?>
						<span class="status <?php echo esc_attr( get_post_status() ); ?>"><?php echo esc_html( get_post_status() ); ?></span>
					</div>
					<div class="post-content">
						<h3 class="post-title">
							<?php
							$post_url = Fns::get_account_endpoint_url( 'view-post' ) . '?pid=' . $pid;
							the_title( sprintf( '<a href="%s">', $post_url ), '</a>' );
							?>
						</h3>
						<div class="post-excerpt">
							<?php echo wp_kses_post( $excerpt ); ?>
						</div>

						<div class="post-meta">
							<span class='date'>
								<?php Fns::dashboard_icon( 'calender' ); ?>
								<?php echo get_the_date( '', $pid ); ?>
							</span>

							<?php if ( $categories ) : ?>
								<span class="categories">
								<?php Fns::dashboard_icon( 'folder' ); ?>
								<?php echo wp_kses( $categories, Fns::allowedHtml() ); ?>
							</span>
							<?php endif; ?>

							<?php if ( $tags ) : ?>
								<span class="tags">
									<?php Fns::dashboard_icon( 'tags' ); ?>
									<?php echo wp_kses( $tags, Fns::allowedHtml() ); ?>
								</span>
							<?php endif; ?>

							<span class='comment'>
								<?php Fns::dashboard_icon( 'comment' ); ?>
								<?php echo get_comments_number( $pid ); ?>
							</span>

							<span class="count">
								<?php
								$count_key      = Fns::get_post_view_count_meta_key();
								$get_view_count = get_post_meta( $pid, $count_key, true );
								Fns::dashboard_icon( 'eye' );
								?>
								<?php echo esc_html( $get_view_count ); ?>
							</span>
						</div>
				
						<div class="post-btn-action right-align">
							<a class="btn edit-btn"
							   href="<?php echo esc_url( Fns::get_account_endpoint_url( 'edit-post' ) ); ?>?pid=<?php echo esc_attr( $pid ); ?>">
								<?php Fns::dashboard_icon( 'edit' ); ?>
								<?php echo esc_html__( 'Edit', 'the-post-grid' ); ?>
							</a>
							<a class="btn delete-btn tpg-delete-post" href="" data-id="<?php echo esc_attr( $pid ); ?>">
								<?php Fns::dashboard_icon( 'delete' ); ?>
								<?php echo esc_html__( 'Delete', 'the-post-grid' ); ?>
							</a>
						</div>

					</div>
				</div>
				<?php
			}
		}
		wp_reset_postdata();
		?>
		<div class="rt-row">
			<?php
			Fns::print_html(
				Fns::get_pagination_markup(
					$_latest_post,
					[
						'pagination_type' => 'pagination',
						'show_pagination' => 'show',
					]
				)
			);
			?>
		</div>
	</div>
</div>
