<?php
/**
 * Template: EDD Layout - 2
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;

$price = null;

if ( edd_has_variable_prices( get_the_ID() ) ) {
	$price = "<span class='price'>" . edd_price_range( get_the_ID(), false ) . '</span>';
} else {
	$price = "<span class='price'>" . edd_price( get_the_ID(), false ) . '</span>';
}

$html = $htmlDetail = $htmlTitle = $pType = null;

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
	$html .= sprintf( '<%1$s class="product-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a> %7$s</%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title, $price );
}

if ( $excerpt ) {
	$html .= "<p>{$excerpt}</p>";
}

$html .= sprintf( "<div class='product-meta'>%s</div>", edd_get_purchase_link() );
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
