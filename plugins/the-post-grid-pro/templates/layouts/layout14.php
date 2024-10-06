<?php
/**
 * Template: Layout - 14
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$html = $htmlDetail = $postMetaBottom2 = $iTitle = $postMetaTop = null;

if ( in_array( 'categories', $items ) && $categories ) {
	$postMetaTop .= "<span class='categories-links'><i class='" . Fns::change_icon( 'fas fa-folder-open', 'folder' ) . "'></i>{$categories}</span>";
}

if ( in_array( 'tags', $items ) && $tags ) {
	$postMetaTop .= "<span class='post-tags-links'><i class='" . Fns::change_icon( 'fa fa-tags', 'tag' ) . "'></i>{$tags}</span>";
}

if ( ! empty( $category_position ) && $category_position == 'above_title' && ( empty( $metaPosition ) || $metaPosition == 'above_title' ) ) {
	$iTitle .= sprintf( '<div class="cat-above-title %s">%s</div>', $category_style, $postMetaTop );
}

if ( in_array( 'title', $items ) ) {
	$iTitle .= sprintf( '<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

if ( in_array( 'social_share', $items ) ) {
	$postMetaBottom2 .= Functions::rtShare( $pID );
}

if ( in_array( 'read_more', $items ) ) {
	$postMetaBottom2 .= "<span class='read-more'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$read_more_text}</a></span>";
}

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', [ $grid, $class ] ) ), $pID );
$html .= '<div class="rt-holder">';

if ( $tpg_title_position == 'above' ) {
	$html .= sprintf( '<div class="rt-detail rt-with-title">%s</div>', $iTitle );
}

if ( $imgSrc ) {
	$html .= '<div class="rt-img-holder">';
	$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$imgSrc}</a>";

	if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
		$html .= sprintf( '<div class="cat-above-title %s">%s</div>', $category_style, $postMetaTop );
	}

	$html .= '</div> ';
}

if ( ! empty( $postMetaTop ) && empty( $category_position ) ) {
	$htmlDetail .= "<div class='post-meta-tags {$metaSeparator}'>$postMetaTop</div>";
}

if ( $tpg_title_position != 'above' ) {
	$htmlDetail .= $iTitle;
}

$postMetaMid = null;

if ( ! empty( $postMetaMid ) ) {
	$htmlDetail .= "<div class='post-meta-tags'>{$postMetaMid}</div>";
}

if ( ! empty( $postMetaTop ) && ! empty( $metaPosition ) && $metaPosition == 'above_excerpt' ) {
	$htmlDetail .= "<div class='post-meta-tags {$metaSeparator}'>$postMetaTop</div>";
}

if ( in_array( 'excerpt', $items ) ) {
	$htmlDetail .= "<div class='tpg-excerpt'>{$excerpt}</div>";
}

if ( ! empty( $postMetaTop ) && ! empty( $metaPosition ) && $metaPosition == 'below_excerpt' ) {
	$htmlDetail .= "<div class='post-meta-tags {$metaSeparator}'>$postMetaTop</div>";
}

if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
	$htmlDetail .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
}

$postMetaBottom = null;

if ( in_array( 'author', $items ) ) {
	$postMetaBottom .= "<span class='author'><i class='" . Fns::change_icon( 'fa fa-user', 'user' ) . "'></i>{$author}</span>";
}

if ( in_array( 'post_date', $items ) && $date ) {
	$postMetaBottom .= "<span class='date'><i class='" . Fns::change_icon( 'far fa-calendar-alt', 'calendar' ) . "'></i>{$date}</span>";
}

if ( in_array( 'comment_count', $items ) && $comment ) {
	$postMetaBottom .= "<span class='comment-link'><i class='" . Fns::change_icon( 'fas fa-comments', 'chat' ) . "'></i>{$comment}</span>";
}

if ( in_array( 'post_count', $items ) ) {
	$postMetaBottom .= "<span class='post-count'><i class='" . Fns::change_icon( 'fa fa-eye', 'visible' ) . "'></i>{$post_count}</span>";
}

if ( ! empty( $htmlDetail ) ) {
	$html .= "<div class='rt-detail'>$htmlDetail</div>";
}

if ( ! empty( $postMetaBottom2 ) ) {
	$html .= "<div class='post-meta {$btn_alignment_class}'>$postMetaBottom2</div>";
}

if ( ! empty( $postMetaBottom ) ) {
	$html .= "<div class='post-meta-user'>$postMetaBottom</div>";
}

$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
