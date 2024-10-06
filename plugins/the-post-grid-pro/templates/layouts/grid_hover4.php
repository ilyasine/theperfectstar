<?php
/**
 * Template: Grid Hover Layout - 4
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$html        = $htmlDetail = $metaHtml = $iTitle = $catHtml = null;
$postMetaTop = $postMetaMid = null;

if ( in_array( 'categories', $items ) && $categories ) {
	$catHtml = sprintf( '<span class="rt-tpg-category %s">', $category_style );

	if ( $catIcon ) {
		$catHtml .= "<i class='" . Fns::change_icon( 'fas fa-folder-open', 'folder' ) . "'></i>";
	}

	$catHtml .= "{$categories}</span>";
}

if ( in_array( 'author', $items ) ) {
	$postMetaTop .= "<span class='author'>";
	$postMetaTop .= "By {$author}</span>";
}

if ( in_array( 'post_date', $items ) && $date ) {
	$postMetaTop .= "<span class='date'>";

	if ( $metaIcon ) {
		$postMetaTop .= "<i class='" . Fns::change_icon( 'far fa-calendar-alt', 'calendar' ) . " icon'></i>";
	}

	$postMetaTop .= "{$date}</span>";
}

if ( in_array( 'tags', $items ) && $tags ) {
	$postMetaTop .= "<span class='post-tags-links'>";

	if ( $metaIcon ) {
		$postMetaTop .= "<i class='" . Fns::change_icon( 'fa fa-tags', 'tag' ) . " icon'></i>";
	}

	$postMetaTop .= "{$tags}</span>";
}

if ( in_array( 'comment_count', $items ) ) {
	$postMetaTop .= '<span class="comment-count">';

	if ( $metaIcon ) {
		$postMetaTop .= '<i class="far fa-comments icon"></i>';
	}

	$postMetaTop .= $comment . '</span>';
}

if ( in_array( 'post_count', $items ) ) {
	$postMetaTop .= '<span class="post-count">';

	if ( $metaIcon ) {
		$postMetaTop .= "<i class='" . Fns::change_icon( 'fa fa-eye', 'visible' ) . "'></i>";
	}

	$postMetaTop .= $post_count . '</span>';
}

$imgHtml = $contentHtml = null;
$imgSrc  = ( $postCount % 5 ) == 2 ? $imgSrc : $smallImgSrc;

if ( $imgSrc ) {
	$imgHtml .= '<div class="post-img">';
	$imgHtml .= sprintf( '<a data-id="%s" class="img-link %s" href="%s"%s>%s</a>', $pID, $anchorClass, $pLink, $link_target, $imgSrc );

	if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
		$imgHtml .= sprintf( '<div class="cat-over-image %s">%s</div>', $category_position, $catHtml );
	}

	$imgHtml .= '</div>';
}

if ( $postCount == 0 ) {
	$html .= '<div class="rt-grid-hover-layout4">';
}

switch ( $postCount % 5 ) {
	case 0:
	case 3:
		$gridColumn = 1 + ( 2 * floor( $postCount / 5 ) );
		break;
	case 1:
	case 4:
		$gridColumn = 2 + ( 2 * floor( $postCount / 5 ) );
		break;
	case 2:
		$gridColumn = ( 1 + ( 2 * floor( $postCount / 5 ) ) ) . '/' . ( 3 + ( 2 * floor( $postCount / 5 ) ) );
		break;
	default:
		$gridColumn = 0;
}

$html        .= "<div class='grid-hover-item {$class}' data-id='{$pID}' style='--row-w:{$gridColumn}'>";
$html        .= '<div class="rt-post-overlay">';
$contentHtml .= $imgHtml;
$contentHtml .= '<div class="post-content">';

if ( ( empty( $category_position ) || $category_position == 'above_title' ) && in_array( 'categories', $items ) && $categories ) {
	$contentHtml .= $catHtml;
}

if ( ! empty( $postMetaTop ) && $metaPosition == 'above_title' ) {
	$contentHtml .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
}

if ( in_array( 'title', $items ) ) {
	$contentHtml .= sprintf(
		'<%1$s class="post-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>',
		$title_tag,
		$pID,
		$anchorClass,
		$pLink,
		$link_target,
		$title
	);
}

if ( ! empty( $postMetaTop ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
	$contentHtml .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
}

if ( in_array( 'excerpt', $items ) ) {
	$contentHtml .= "<p class='excerpt'>{$excerpt}</p>";
}

if ( ! empty( $postMetaTop ) && $metaPosition == 'below_excerpt' ) {
	$contentHtml .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
}

if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
	$contentHtml .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
}

$contentHtml .= '</div>';
$html        .= $contentHtml;
$html        .= '</div></div>';

if ( $postCount == $totalPost ) {
	$html .= '</div>';
}

Fns::print_html( $html );
