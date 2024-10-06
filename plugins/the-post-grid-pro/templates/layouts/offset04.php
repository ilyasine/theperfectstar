<?php
/**
 * Template: Offset Layout - 4
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;

$html = $metaHtml = $titleHtml = null;

if ( in_array( 'title', $items ) ) {
	$titleHtml .= sprintf( '<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

if ( ! empty( $offset ) && $offset == 'big' ) {
	$class .= ( $class ? ' ' : '' ) . 'rt-col-xs-12 offset-big';
	$html  .= "<div class='{$class}' data-id='{$pID}'>";
	$html  .= '<div class="rt-holder">';
	$html  .= "<div class='overlay'>";
	$html  .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>$imgSrc</a>";

	$html .= '</div>';
	$html .= "<div class='post-info'>{$titleHtml}</div>";
	$html .= '</div>';
	$html .= '</div>';

} elseif ( ! empty( $offset ) && $offset == 'small' ) {
	$dCol   = $tCol = $mCol = 6;
	$grid   = "rt-col-md-{$dCol} rt-col-sm-{$tCol} rt-col-xs-{$mCol}";
	$class  .= ( $class ? ' ' : '' ) . $grid . ' offset-small';
	$imgSrc = Fns::getFeatureImageSrc( $pID, 'medium' );
	$html   .= "<div class='{$class}' data-id='{$pID}'>";
	$html   .= '<div class="rt-holder">';
	$html   .= "<div class='overlay'>";
	$html   .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>$imgSrc</a>";
	$html   .= '</div>';
	$html   .= "<div class='post-info'>{$titleHtml}</div>";
	$html   .= '</div>';
	$html   .= '</div>';
}

Fns::print_html( $html );
