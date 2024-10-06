<?php
/**
 * Template: WC Layout - 3
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

$_product      = wc_get_product( $pID );
$price         = "<span class='price'>" . $_product->get_price_html() . '</span>';
$_rating_count = $_product->get_rating_count();
$_rating       = wc_get_rating_html( $_rating_count ? $_rating_count : .01 );
$pType         = $_product->get_type();
$html          = $htmlDetail = $htmlTitle = null;

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', [ $grid, $class ] ) ), $pID );
$html .= '<div class="rt-holder">';

if ( $imgSrc ) {
	$html         .= "<div class='rt-col-xs-12 rt-col-sm-12 rt-col-md-5 rt-col-lg-5'>";
	$html         .= "<div class='grid-img rt-img-holder'><a href='{$pLink}' class='{$anchorClass}' data-id='{$pID}'{$link_target}>$imgSrc</a></div>";
	$html         .= '</div>';
	$content_area = 'rt-col-xs-12 rt-col-sm-12 rt-col-md-7 rt-col-lg-7';
} else {
	$content_area = 'rt-col-md-12';
}

$html .= "<div class='{$content_area}'>";
$html .= "<div class='rt-detail rt-woo-info'>";

if ( in_array( 'title', $items ) ) {
	$html .= sprintf( '<%1$s class="product-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

if ( in_array( 'rating', $items ) ) {
	$_rating = "<div class='product-rating'>" . $_rating . '</div>';
}

$html .= "<p>{$excerpt}</p>";

if ( $_product->is_purchasable() ) {
	if ( $_product->is_in_stock() ) {
		$html .= "<a href='?add-to-cart={$pID}' class='rt-wc-add-to-cart' data-id='{$pID}' data-type='{$pType}'>" . esc_html__( 'Add To Cart', 'the-post-grid-pro' ) . '</a>';
		$html .= "<span class='price-area'>" . $price . '</span>';
	} else {
		$html .= '<mark class="outofstock">' . esc_html__( 'Out of stock', 'the-post-grid-pro' ) . '</mark>';
	}
}

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
