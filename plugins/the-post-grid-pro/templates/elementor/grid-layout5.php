<?php
/**
 * Grid Layout Template - 1
 *
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$pID     = get_the_ID();
$excerpt = Fns::get_the_excerpt( $pID, $data );
$title   = Fns::get_the_title( $pID, $data );

/**
 * Get post link markup
 * $link_start, $link_end, $readmore_link_start, $readmore_link_end
 */

$post_link = Fns::get_post_link( $pID, $data );
extract( $post_link );


//Grid Column:
$grid_column_desktop = '0' !== $data['grid_column'] ? $data['grid_column'] : '4';
$grid_column_tab     = '0' !== $data['grid_column_tablet'] ? $data['grid_column_tablet'] : '6';
$grid_column_mobile  = '0' !== $data['grid_column_mobile'] ? $data['grid_column_mobile'] : '12';
$col_class           = "rt-col-md-{$grid_column_desktop} rt-col-sm-{$grid_column_tab} rt-col-xs-{$grid_column_mobile}";

//Column Dynamic Class
$column_classes = [];

$column_classes[] .= $data['hover_animation'];

//Offset settings
$tpg_post_count  = get_query_var( 'tpg_post_count' );
$tpg_total_posts = get_query_var( 'tpg_total_posts' );
$offset_size     = false;
if ( $tpg_post_count != 1 ) {
	$offset_size = true;
}

?>

<?php if ( $tpg_post_count == 1 || $tpg_post_count == 2 ) : ?>
<div class="rt-col-sm-6 rt-col-xs-12 <?php echo esc_attr( 1 == $tpg_post_count ? ' offset-left-wrap offset-left' : 'offset-right' ); ?>">
	<?php endif; ?>

    <div class="offset-item <?php echo esc_attr( implode( ' ', $column_classes ) ); ?>" data-id="<?php echo esc_attr( $pID ); ?>">
        <div class="rt-holder tpg-post-holder">
            <div class="rt-detail rt-el-content-wrapper">
				<?php if ( 'show' == $data['show_thumb'] ) :
					$has_thumbnail = has_post_thumbnail() ? 'has-thumbnail' : 'has-no-thumbnail'; ?>
                    <div class="rt-img-holder tpg-el-image-wrap <?php echo esc_attr( $has_thumbnail ); ?>">
						<?php Fns::get_post_thumbnail( $pID, $data, $link_start, $link_end, $offset_size ); ?>
                    </div>
				<?php endif; ?>

                <div class="offset-content">

					<?php
					if ( 'show' == $data['show_title'] ) {
						Fns::get_el_post_title( $data['title_tag'], $title, $link_start, $link_end, $data );
					}
					?>

					<?php if ( 'show' == $data['show_meta'] ) : ?>
                        <div class="post-meta-tags rt-el-post-meta">
							<?php Fns::get_post_meta_html( $pID, $data ); ?>
                        </div>
					<?php endif; ?>

					<?php if ( ( 'show' == $data['show_excerpt'] || 'show' == $data['show_acf'] ) && $tpg_post_count == 1 ) : ?>
                        <div class="tpg-excerpt tpg-el-excerpt">
							<?php if ( $excerpt && 'show' == $data['show_excerpt'] ) : ?>
                                <div class="tpg-excerpt-inner">
									<?php echo wp_kses_post( $excerpt ); ?>
                                </div>
							<?php endif; ?>
							<?php Fns::tpg_get_acf_data_elementor( $data, $pID ); ?>
                        </div>
					<?php endif; ?>

					<?php
					if ( 'show' === $data['show_social_share'] && $tpg_post_count == 1 ) {
						Fns::print_html( Functions::rtShare( $pID ), true );
					}

					if ( 'show' === $data['show_read_more'] && $data['read_more_label'] && $tpg_post_count == 1 ) {
						Fns::get_read_more_button( $data, $readmore_link_start, $readmore_link_end );
					}
					?>
                </div>
            </div>
        </div>
    </div>

	<?php if ( $tpg_post_count == 1 || $tpg_post_count == $tpg_total_posts ) : ?>
</div>
<!--End Loop Div-->
<?php endif; ?>
