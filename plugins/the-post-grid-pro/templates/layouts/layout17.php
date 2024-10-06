<?php
/**
 * Template: Layout - 17
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;

$html = null;

if ( $imgSrc ) {
	$fullSrc = Fns::getFeatureImageUrl( $pID, 'full' );
	$html    .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', [ $grid, $class ] ) ), $pID );
	$html    .= '<div class="rt-holder">';
	$html    .= '<div class="overlay">';
	$html    .= "<a class='tpg-zoom' title='{$title}' href='{$fullSrc}'><i class='fa fa-plus' aria-hidden='true'></i></a>";
	$html    .= '</div>';
	$html    .= $imgSrc;
	$html    .= '</div>';
	$html    .= '</div>';
}

Fns::print_html( $html );
