<?php
/**
 * Template: Layout - 4
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$html = $imgHtml = $contentHtml = $metaHtml = $catHtml = null;

if ( in_array( 'categories', $items ) && $categories ) {
	$catHtml .= "<span class='categories-links'>";

	if ( $catIcon ) {
		$catHtml .= "<i class='" . Fns::change_icon( 'fas fa-folder-open', 'folder' ) . "'></i>";
	}

	$catHtml .= "{$categories}</span>";
}

if ( in_array( 'post_date', $items ) && $date ) {
	$metaHtml .= "<span class='date-meta'>";

	if ( $metaIcon ) {
		$metaHtml .= "<i class='" . Fns::change_icon( 'far fa-calendar-alt', 'calendar' ) . "'></i>";
	}

	$metaHtml .= "{$date}</span>";
}

if ( in_array( 'author', $items ) ) {
	$metaHtml .= "<span class='author'>";

	if ( $metaIcon ) {
		$metaHtml .= "<i class='" . Fns::change_icon( 'fa fa-user', 'user' ) . "'></i>";
	}

	$metaHtml .= "{$author}</span>";
}

if ( empty( $category_position ) ) {
	$metaHtml .= $catHtml;
}

if ( in_array( 'tags', $items ) && $tags ) {
	$metaHtml .= "<span class='post-tags-links'>";

	if ( $metaIcon ) {
		$metaHtml .= "<i class='" . Fns::change_icon( 'fa fa-tags', 'tag' ) . "'></i>";
	}

	$metaHtml .= "{$tags}</span>";
}

if ( in_array( 'comment_count', $items ) ) {
	$metaHtml .= '<span class="comment-count">';

	if ( $metaIcon ) {
		$metaHtml .= "<i class='" . Fns::change_icon( 'fas fa-comments', 'chat' ) . "'></i>";
	}

	$metaHtml .= $comment . '</span>';
}

if ( in_array( 'post_count', $items ) ) {
	$metaHtml .= '<span class="post-count">';

	if ( $metaIcon ) {
		$metaHtml .= "<i class='" . Fns::change_icon( 'fa fa-eye', 'visible' ) . "'></i>";
	}

	$metaHtml .= $post_count . '</span>';
}

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', array_filter( [ $grid, $class, 'padding0 layout4item' ] ) ) ), $pID );
$html .= '<div class="rt-holder">';

if ( $imgSrc ) {
	$imgHtml .= "<div class='{$image_area} padding0 layoutInner layoutInner-img'>";
	$imgHtml .= '<div class="rt-img-holder">';
	$imgHtml .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$imgSrc}</a>";

	if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
		$imgHtml .= sprintf( '<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml );
	}

	$imgHtml .= '</div>';
	$imgHtml .= '</div>';
} else {
	$content_area = 'rt-col-xs-12';
}

$contentHtml .= "<div class='{$content_area} padding0 layoutInner layoutInner-content'>";
$contentHtml .= '<div class="rt-detail">';

if ( ! empty( $metaHtml ) && $metaPosition == 'above_title' ) {
	$contentHtml .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
}

if ( $category_position == 'above_title' ) {
	$contentHtml .= sprintf( '<div class="cat-above-title %s">%s</div>', $category_style, $catHtml );
}

if ( in_array( 'title', $items ) ) {
	$contentHtml .= sprintf(
		'<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>',
		$title_tag,
		$pID,
		$anchorClass,
		$pLink,
		$link_target,
		$title
	);
}

if ( ! empty( $metaHtml ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
	$contentHtml .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
}

if ( in_array( 'excerpt', $items ) ) {
	$contentHtml .= "<div class='post-content'>{$excerpt}</div>";
}

if ( ! empty( $metaHtml ) && $metaPosition == 'below_excerpt' ) {
	$contentHtml .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
}

if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
	$contentHtml .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
}

if ( in_array( 'social_share', $items ) ) {
	$contentHtml .= Functions::rtShare( $pID );
}

if ( in_array( 'read_more', $items ) ) {
	$contentHtml .= "<span class='read-more {$btn_alignment_class}'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$read_more_text}</a></span>";
}

$contentHtml .= '</div>';
$contentHtml .= '</div>';

if ( $toggle ) {
	$html .= $contentHtml . $imgHtml;
} else {
	$html .= $imgHtml . $contentHtml;
}

$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
