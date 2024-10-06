<?php
/**
 * Grid Layout Template - 1
 *
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

use RT\ThePostGrid\Helpers\Fns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$cat        = $data['cat'];
$cat_info   = get_term( $cat );
$cat_thumb  = get_term_meta( $cat, rtTpgPro()->category_thumb_meta_key, true );
$image_size = $data['image_size'] ?? 'thumbnail';

$grid_column_desktop = '0' !== $data['grid_column'] ? $data['grid_column'] : '24';
$grid_column_tab     = '0' !== $data['grid_column_tablet'] ? $data['grid_column_tablet'] : '4';
$grid_column_mobile  = '0' !== $data['grid_column_mobile'] ? $data['grid_column_mobile'] : '6';
$col_class           = "rt-col-md-{$grid_column_desktop} rt-col-sm-{$grid_column_tab} rt-col-xs-{$grid_column_mobile}";
?>

<div class="cat-item-col <?php echo esc_attr( $col_class ) ?>">
    <div class="card-inner-wrapper">
        <div class="cat-thumb">
            <div class="cat-link" >
	            <?php if ( $data['img_visibility'] === 'yes' ) {
		            echo wp_get_attachment_image( $cat_thumb, $image_size, null, [ 'class' => 'category-image' ] );
	            } ?>
                <span class="overlay"></span>

				<?php printf("<%s class='category-name'>", esc_attr( $data['cat_tag'] ) ) ?>
                <a href="<?php echo esc_url( get_term_link( $cat_info ) ) ?>">
					<?php echo esc_html( $cat_info->name ) ?>
					<?php if ( $data['count_visibility'] === 'yes' ) : ?>
                        <span class="count count-title"><?php echo $data['show_bracket'] == 'yes' ? esc_html( '(' ) : '' ?><?php echo esc_html( $cat_info->count ) ?><?php echo $data['show_bracket'] == 'yes' ? esc_html( ')' ) : '' ?></span>
					<?php endif; ?>
                </a>
				<?php printf("</%s>", esc_attr( $data['cat_tag'] ) ) ?>
            </div>
        </div>
    </div>
</div>

