<?php
/**
 * Template: List Layout - 2
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$html = $htmlDetail = $metaHtml = $catHtml = null;

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
	$metaHtml .= sprintf( esc_html__( 'By %s', 'the-post-grid-pro' ), $author ); //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$metaHtml .= '</span>';
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

$html .= sprintf( '<div class="%s" data-id="%d">', esc_attr( implode( ' ', [ $grid, $class ] ) ), $pID );
$html .= '<div class="item">';
$html .= '<div class="rt-post post-sm style-1">';

if ( $imgSrc ) {
	$html .= '<div class="post-img">';
	$html .= "<a data-id='{$pID}' class='{$anchorClass} rounded' href='{$pLink}'{$link_target}>{$imgSrc}</a>";

	if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
		$html .= sprintf( '<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml );
	}

	$html .= '</div>';
} else {
	$content_area = 'rt-col-xs-12';
}

if ( empty( $category_position ) || $category_position == 'above_title' ) {
	if ( empty( $category_style ) ) {
		$htmlDetail .= sprintf( '<div class="rt-tpg-category style1">%s</div>', $categories );
	} else {
		$htmlDetail .= sprintf( '<div class="rt-tpg-category %s">%s</div>', $category_style, $categories );
	}
}

if ( in_array( 'title', $items ) ) {
	if ( ! empty( $metaHtml ) && $metaPosition == 'above_title' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
	}

	$htmlDetail .= sprintf( '<%1$s class="post-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
}

if ( ! empty( $metaHtml ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
	$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$metaHtml</div>";
}

if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
	$htmlDetail .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
}

if ( in_array( 'read_more', $items ) || in_array( 'social_share', $items ) ) {
	$htmlDetail .= "<div class='btn-wrap {$btn_alignment_class}'>";

	if ( in_array( 'social_share', $items ) ) {
		$htmlDetail .= Functions::rtShare( $pID );
	}

	if ( in_array( 'read_more', $items ) ) {
		$htmlDetail .= "<a data-id='{$pID}' class='rt-read-more {$anchorClass}' href='{$pLink}'{$link_target}>{$read_more_text}</a>";
	}

	$htmlDetail .= '</div>';
}

if ( ! empty( $htmlDetail ) ) {
	$html .= "<div class='post-content'>$htmlDetail</div>";
}

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

Fns::print_html( $html );
