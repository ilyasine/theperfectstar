<?php
/**
 * Template: WC Isotope Layout - 1
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
$pType    = $_product->get_type();
$html     = $htmlDetail = $htmlTitle = $_rating = null;
$button   = null;

if ( $_product->is_purchasable() ) {
	if ( $_product->is_in_stock() ) {
		$button = "<li><a href='{$pLink}?add-to-cart={$pID}' class='rt-wc-add-to-cart' data-id='{$pID}' data-type='{$pType}'><i class='fa fa-shopping-cart'></i></a></li>";
	}
}

if ( in_array( 'rating', $items ) ) {
	$_rating_count = $_product->get_rating_count();
	$_rating       = wc_get_rating_html( $_rating_count ? $_rating_count : .01 );
	$_rating       = "<div class='product-rating'>" . $_rating . '</div>';
}

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', array_filter( [ $grid, $class, $isoFilter ] ) ) ), $pID );
$html .= '<div class="rt-holder">';

if ( $imgSrc ) {
	$html .= '<div class="rt-img-holder">';
	$html .= '<div class="overlay">';
	$html .= "<div class='product-more'>
				<ul>{$button}
					<li><a class='{$anchorClass}' data-id='{$pID}' href='{$pLink}'><i class='fa fa-search'></i></a></li>
				</ul>
			</div> ";
	$html .= '</div>';
	$html .= "<a href='{$pLink}' class='{$anchorClass}' data-id='{$pID}'{$link_target}>$imgSrc</a>";
	$html .= '</div> ';
}

if ( in_array( 'title', $items ) ) {
	$title = sprintf( '<%1$s class="product-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

$price = "<span class='price'>" . $_product->get_price_html() . '</span>';

$html .= "<div class='rt-woo-info rt-detail'>{$title}{$_rating}{$price}</div>";

$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
