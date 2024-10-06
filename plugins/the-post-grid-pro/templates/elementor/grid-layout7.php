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
 * Extract post link markup
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
$column_classes   = [];

$column_classes[] .= $data['hover_animation'];
$column_classes[] .= 'rt-grid-item';
if ( 'masonry' == $data['layout_style'] ) {
	$column_classes[] .= 'masonry-grid-item';
}
?>

<div class="<?php echo esc_attr( $col_class . ' ' . implode( ' ', $column_classes ) ); ?>" data-id="<?php echo esc_attr( $pID ); ?>">
    <div class="rt-holder tpg-post-holder">
        <div class="rt-detail rt-el-content-wrapper">
			<?php if ( 'show' == $data['show_thumb'] ) :
				$has_thumbnail = has_post_thumbnail() ? 'has-thumbnail' : 'has-no-thumbnail'; ?>
                <div class="rt-img-holder tpg-el-image-wrap <?php echo esc_attr( $has_thumbnail ); ?>">

					<?php Fns::get_post_thumbnail( $pID, $data, $link_start, $link_end ); ?>

                </div>
			<?php endif; ?>


        </div>
    </div>
</div>
