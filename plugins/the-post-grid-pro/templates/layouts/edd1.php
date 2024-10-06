<?php
/**
 * Template: EDD Layout - 1
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;

$html  = $htmlDetail = $htmlTitle = null;
$price = null;

if ( edd_has_variable_prices( get_the_ID() ) ) {
	$price = "<span class='price'>" . edd_price_range( get_the_ID(), false ) . '</span>';
} else {
	$price = "<span class='price'>" . edd_price( get_the_ID(), false ) . '</span>';
}

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', [ $grid, $class ] ) ), $pID );
$html .= '<div class="rt-holder">';
$html .= '<div class="rt-img-holder">';
$html .= '<div class="overlay">';
$html .= "<div class='product-more'>
			<ul>
				<li>" . edd_get_purchase_link() . "</li>
				<li><a class='{$anchorClass}' data-id='{$pID}' href='{$pLink}'{$link_target}><i class='fa fa-search'></i></a></li>
			</ul>
		</div> ";

$html .= '</div>';

if ( $imgSrc ) {
	$html .= "<a href='{$pLink}' class='{$anchorClass}' data-id='{$pID}'{$link_target}>$imgSrc</a>";
}

$html .= '</div> ';

if ( in_array( 'title', $items ) ) {
	$title = sprintf( '<%1$s class="product-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

$html .= "<div class='rt-woo-info rt-edd-info'>{$title}{$price}</div>";
$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
