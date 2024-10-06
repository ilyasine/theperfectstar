<?php
/**
 * Template: WC Carousel Layout - 2
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;

if ( get_post_type() != 'product' ) {
	return;
}

$_product = wc_get_product( $pID );
$price    = "<span class='price'>" . $_product->get_price_html() . '</span>';
$_rating  = null;
$pType    = $_product->get_type();
$button   = null;

if ( $_product->is_purchasable() ) {
	if ( $_product->is_in_stock() ) {
		$button = "<div><a href='{$pLink}?add-to-cart={$pID}' class='rt-wc-add-to-cart' data-id='{$pID}' data-type='{$pType}'>" . esc_html__( 'Add To Cart', 'the-post-grid-pro' ) . '</a></div>';
	}
}

if ( in_array( 'rating', $items ) ) {
	$_rating_count = $_product->get_rating_count();
	$_rating       = wc_get_rating_html( $_rating_count ? $_rating_count : .01 );
	$_rating       = "<div class='product-rating'>" . $_rating . '</div>';
}

$html = $htmlDetail = $htmlTitle = $html_pinfo = null;

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', [ $grid, $class ] ) ), $pID );
$html .= '<div class="rt-holder">';

if ( $imgSrc ) {
	$html .= '<div class="rt-img-holder">';
	$html .= "<a href='{$pLink}' class='{$anchorClass}' data-id='{$pID}'{$link_target}>$imgSrc</a>";
	$html .= '</div> ';
}

if ( in_array( 'title', $items ) ) {
	$htmlTitle = sprintf( '<%1$s class="product-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

$html_pinfo .= "<div class='product-meta'><div class='price-area'>{$price}</div>{$button}</div>";
$htmlDetail .= $htmlTitle . $_rating . $html_pinfo;

if ( ! empty( $htmlDetail ) ) {
	$html .= "<div class='rt-detail rt-woo-info'>{$htmlDetail}</div>";
}

$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
