<?php
/**
 * Template: Offset Layout - 6
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$html = $htmlDetail = $catHtml = $postMetaTop = null;

if ( in_array( 'categories', $items ) && $categories ) {
	$catHtml .= sprintf( '<span class="rt-tpg-category %s">', $category_style );

	if ( $catIcon ) {
		$catHtml .= "<i class='" . Fns::change_icon( 'fas fa-folder-open', 'folder' ) . "'></i>";
	}

	$catHtml .= "{$categories}</span>";
}

if ( in_array( 'author', $items ) ) {
	$postMetaTop .= "<span class='author'>";
	$postMetaTop .= sprintf( esc_html__( 'By %s', 'the-post-grid-pro' ), $author ); //phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$postMetaTop .= '</span>';
}

if ( in_array( 'post_date', $items ) && $date ) {
	$postMetaTop .= "<span class='date-meta'>";

	if ( $metaIcon ) {
		$postMetaTop .= "<i class='" . Fns::change_icon( 'far fa-calendar-alt', 'calendar' ) . "'></i>";
	}

	$postMetaTop .= "{$date}</span>";
}

if ( in_array( 'tags', $items ) && $tags ) {
	$postMetaTop .= "<span class='post-tags-links'>";

	if ( $metaIcon ) {
		$postMetaTop .= "<i class='" . Fns::change_icon( 'fa fa-tags', 'tag' ) . "'></i>";
	}

	$postMetaTop .= "{$tags}</span>";
}

if ( in_array( 'comment_count', $items ) ) {
	$postMetaTop .= '<span class="comment-count">';

	if ( $metaIcon ) {
		$postMetaTop .= "<i class='" . Fns::change_icon( 'fas fa-comments', 'chat' ) . "'></i>";
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

$imgSrc = ( $offset == 'big' ) ? $imgSrc : $smallImgSrc;

if ( ! empty( $offset ) && $offset == 'big' ) {
	$class .= ( $class ? ' ' : '' ) . 'rt-col-xs-12 offset-big';

	$html .= "<div class='{$class}' data-id='{$pID}'>";
	$html .= '<div class="rt-post-grid post-grid-lg-style-1">';
	$html .= '<div class="post-img">';

	if ( $imgSrc ) {
		$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>$imgSrc</a>";

		if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
			$html .= sprintf( '<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml );
		}
	}

	$html .= '</div> ';

	if ( empty( $category_position ) || $category_position == 'above_title' ) {
		if ( empty( $category_style ) ) {
			$htmlDetail .= '<span class="rt-tpg-category style1">' . $categories . '</span>';
		} else {
			$htmlDetail .= sprintf( '<div class="rt-tpg-category %s">%s</div>', $category_style, $categories );
		}
	}

	if ( ! empty( $postMetaTop ) && $metaPosition == 'above_title' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
	}

	if ( in_array( 'title', $items ) ) {
		$htmlDetail .= "<h3 class='post-title'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$title}</a></h3>";
	}

	if ( ! empty( $postMetaTop ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
		$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
	}

	if ( in_array( 'excerpt', $items ) ) {
		$htmlDetail .= "<p>{$excerpt}</p>";
	}

	if ( ! empty( $postMetaTop ) && $metaPosition == 'below_excerpt' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
	}

	if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
		$htmlDetail .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
	}

	if ( in_array( 'read_more', $items ) || in_array( 'social_share', $items ) ) {
		$htmlDetail .= "<div class='btn-wrap mt--25 {$btn_alignment_class}'>";

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
} elseif ( ! empty( $offset ) && $offset == 'small' ) {
	$dCol = $tCol = $mCol = 12;

	if ( ! empty( $offsetCol[0] ) && $offsetCol[0] == 4 ) {
		$dCol = 12;
	}

	if ( ! empty( $offsetCol[1] ) && $offsetCol[1] == 4 ) {
		$tCol = 12;
	}

	if ( ! empty( $offsetCol[2] ) && $offsetCol[2] == 4 ) {
		$mCol = 12;
	}

	$grid  = "rt-col-md-{$dCol} rt-col-sm-{$tCol} rt-col-xs-{$mCol}";
	$class .= ( $class ? ' ' : '' ) . $grid . ' offset-grid-small';
	$html  .= "<div class='{$class}' data-id='{$pID}'>";
	$html  .= '<div class="rt-sidebar-left-spacer_1 b__l-xl">';
	$html  .= '<div class="post-grid-list-style-6">';
	$html  .= '<div class="item">';
	$html  .= '<div class="rt-post-grid  ">';
	$html  .= '<div class="post-img">';

	if ( $imgSrc ) {
		$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>$imgSrc</a>";

		if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
			$html .= sprintf( '<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml );
		}
	}

	$html .= '</div> ';

	if ( empty( $category_position ) || $category_position == 'above_title' ) {
		if ( empty( $category_style ) ) {
			$htmlDetail .= '<span class="rt-tpg-category style1">' . $categories . '</span>';
		} else {
			$htmlDetail .= $catHtml;
		}
	}

	if ( ! empty( $postMetaTop ) && $metaPosition == 'above_title' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
	}

	if ( in_array( 'title', $items ) ) {
		$htmlDetail .= sprintf(
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
		$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
	}

	if ( ! empty( $postMetaTop ) && $metaPosition == 'below_excerpt' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaSeparator}'>$postMetaTop</div>";
	}

	if ( ! empty( $htmlDetail ) ) {
		$html .= "<div class='post-content'>$htmlDetail</div>";
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';

	$html .= '</div>';
}

Fns::print_html( $html );
