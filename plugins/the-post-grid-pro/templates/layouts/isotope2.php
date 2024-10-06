<?php
/**
 * Template: Isotope Layout - 2
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$html         = $metaHtml = $titleHtml = $contentHtml = $catHtml = null;
$num_comments = get_comments_number(); // get_comments_number returns only a numeric value

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

if ( in_array( 'comment_count', $items ) && $comment ) {
	$metaHtml .= '<span class="comment-count"><a href="' . get_comments_link() . '">';

	if ( $metaIcon ) {
		$metaHtml .= "<i class='" . Fns::change_icon( 'fas fa-comments', 'chat' ) . "'></i>";
	}

	$metaHtml .= $num_comments . '</a></span>';
}

if ( in_array( 'post_count', $items ) ) {
	$metaHtml .= '<span class="post-count">';

	if ( $metaIcon ) {
		$metaHtml .= "<i class='" . Fns::change_icon( 'fa fa-eye', 'visible' ) . "'></i>";
	}

	$metaHtml .= $post_count . '</span>';
}

if ( ! empty( $metaHtml ) && $metaPosition == 'above_title' ) {
	$titleHtml .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
}

if ( $category_position == 'above_title' ) {
	$titleHtml .= sprintf( '<div class="cat-above-title %s">%s</div>', $category_style, $catHtml );
}

if ( in_array( 'title', $items ) ) {
	$titleHtml .= sprintf(
		'<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>',
		$title_tag,
		$pID,
		$anchorClass,
		$pLink,
		$link_target,
		$title
	);
}

if ( in_array( 'excerpt', $items ) ) {
	$contentHtml .= "<div class='tpg-excerpt'>{$excerpt}</div>";
}

if ( ! empty( $metaHtml ) && $metaPosition == 'below_excerpt' ) {
	$contentHtml .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
}

if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
	$contentHtml .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
}

$postMetaBottom = null;

if ( in_array( 'social_share', $items ) ) {
	$postMetaBottom .= Functions::rtShare( $pID );
}

if ( in_array( 'read_more', $items ) ) {
	$postMetaBottom .= "<span class='read-more'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>" . esc_html__( 'Read More', 'the-post-grid-pro' ) . '</a></span>';
}

if ( ! empty( $postMetaBottom ) ) {
	$contentHtml .= "<div class='post-meta {$btn_alignment_class}'>$postMetaBottom</div>";
}

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', array_filter( [ $grid, $class, $isoFilter ] ) ) ), $pID );
$html .= '<div class="rt-holder">';
$html .= '<div class="overlay">';
$html .= "{$titleHtml}";

if ( ! empty( $metaHtml ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
	$html .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
}

if ( ! empty( $contentHtml ) ) {
	$html .= "<div class='rt-detail'>{$contentHtml}</div>";
}

$html .= '</div> ';

if ( $imgSrc ) {
	$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$imgSrc}</a>";
	if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
		$html .= sprintf( '<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml );
	}
}

$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
