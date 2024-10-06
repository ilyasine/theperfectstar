<?php
/**
 * Template: Offset Layout - 1
 *
 * @package RT_TPG_PRO
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

$html = $htmlDetail = null;

if ( ! empty( $offset ) && $offset == 'big' ) {
	$class       .= ( $class ? ' ' : '' ) . 'rt-col-xs-12 offset-big';
	$postMetaTop = $catHtml = null;

	if ( in_array( 'categories', $items ) && $categories ) {
		$catHtml .= "<span class='categories-links'>";

		if ( $catIcon ) {
			$catHtml .= "<i class='" . Fns::change_icon( 'fas fa-folder-open', 'folder' ) . "'></i>";
		}

		$catHtml .= "{$categories}</span>";
	}

	if ( in_array( 'author', $items ) ) {
		$postMetaTop .= "<span class='author'>";

		if ( $metaIcon ) {
			$postMetaTop .= "<i class='" . Fns::change_icon( 'fa fa-user', 'user' ) . "'></i>";
		}

		$postMetaTop .= "{$author}</span>";
	}

	if ( in_array( 'post_date', $items ) && $date ) {
		$postMetaTop .= "<span class='date'>";

		if ( $metaIcon ) {
			$postMetaTop .= "<i class='" . Fns::change_icon( 'far fa-calendar-alt', 'calendar' ) . "'></i>";
		}

		$postMetaTop .= "{$date}</span>";
	}

	if ( empty( $category_position ) ) {
		$postMetaTop .= $catHtml;
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

	$html .= "<div class='{$class}' data-id='{$pID}'>";
	$html .= '<div class="rt-holder">';
	$html .= '<div class="rt-img-holder">';

	if ( $imgSrc ) {
		$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>$imgSrc</a>";

		if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
			$html .= sprintf( '<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml );
		}
	}

	$html .= '</div> ';

	if ( $category_position == 'above_title' ) {
		$htmlDetail .= sprintf( '<div class="cat-above-title %s">%s</div>', $category_style, $catHtml );
	}

	if ( ! empty( $postMetaTop ) && $metaPosition == 'above_title' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
	}

	if ( in_array( 'title', $items ) ) {
		$htmlDetail .= "<h3 class='entry-title'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$title}</a></h3>";
	}

	$postMetaMid = null;

	if ( ! empty( $postMetaTop ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
		$htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
	}

	if ( ! empty( $postMetaMid ) ) {
		$htmlDetail .= "<div class='post-meta-tags'>{$postMetaMid}</div>";
	}

	if ( in_array( 'excerpt', $items ) ) {
		$htmlDetail .= "<div class='tpg-excerpt'>{$excerpt}</div>";
	}

	if ( ! empty( $postMetaTop ) && $metaPosition == 'below_excerpt' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
	}

	if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
		$htmlDetail .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
	}

	$postMetaBottom = null;

	if ( in_array( 'social_share', $items ) ) {
		$postMetaBottom .= Functions::rtShare( $pID );
	}

	if ( in_array( 'read_more', $items ) ) {
		$postMetaBottom .= "<span class='read-more'><a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>{$read_more_text}</a></span>";
	}

	if ( ! empty( $postMetaBottom ) ) {
		$htmlDetail .= "<div class='post-meta'>$postMetaBottom</div>";
	}
	if ( ! empty( $htmlDetail ) ) {
		$html .= "<div class='rt-detail'>$htmlDetail</div>";
	}

	$html .= '</div>';
	$html .= '</div>';

} elseif ( ! empty( $offset ) && $offset == 'small' ) {
	$dCol = $tCol = $mCol = 12;

	if ( ! empty( $offsetCol[0] ) && $offsetCol[0] == 4 ) {
		$dCol = 6;
	}

	if ( ! empty( $offsetCol[1] ) && $offsetCol[1] == 4 ) {
		$tCol = 6;
	}

	if ( ! empty( $offsetCol[2] ) && $offsetCol[2] == 4 ) {
		$mCol = 6;
	}

	$grid  = "rt-col-md-{$dCol} rt-col-sm-{$tCol} rt-col-xs-{$mCol}";
	$class .= ( $class ? ' ' : '' ) . $grid . ' offset-small';

	$postMetaTop = $catHtml = null;

	if ( in_array( 'categories', $items ) && $categories ) {
		$catHtml .= "<span class='categories-links'>";

		if ( $catIcon ) {
			$catHtml .= "<i class='" . Fns::change_icon( 'fas fa-folder-open', 'folder' ) . "'></i>";
		}

		$catHtml .= "{$categories}</span>";
	}
	if ( in_array( 'author', $items ) ) {
		$postMetaTop .= "<span class='author'>";

		if ( $metaIcon ) {
			$postMetaTop .= "<i class='" . Fns::change_icon( 'fa fa-user', 'user' ) . "'></i>";
		}

		$postMetaTop .= "{$author}</span>";
	}

	if ( in_array( 'post_date', $items ) && $date ) {
		$postMetaTop .= "<span class='date'>";

		if ( $metaIcon ) {
			$postMetaTop .= "<i class='" . Fns::change_icon( 'far fa-calendar-alt', 'calendar' ) . "'></i>";
		}

		$postMetaTop .= "{$date}</span>";
	}

	if ( empty( $category_position ) ) {
		$postMetaTop .= $catHtml;
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

	$html .= "<div class='{$class}' data-id='{$pID}'>";
	$html .= '<div class="rt-holder">';
	$html .= '<div class="rt-img-holder">';

	if ( $imgSrc ) {
		$html .= "<a data-id='{$pID}' class='{$anchorClass}' href='{$pLink}'{$link_target}>$imgSrc</a>";

		if ( ! empty( $category_position ) && $category_position != 'above_title' ) {
			$html .= sprintf( '<div class="cat-over-image %s %s">%s</div>', $category_position, $category_style, $catHtml );
		}
	}
	$html .= '</div> ';

	if ( $category_position == 'above_title' ) {
		$htmlDetail .= sprintf( '<div class="cat-above-title %s">%s</div>', $category_style, $catHtml );
	}

	if ( ! empty( $postMetaTop ) && $metaPosition == 'above_title' ) {
		$htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
	}

	if ( in_array( 'title', $items ) ) {
		$htmlDetail .= sprintf( '<%1$s class="entry-title"><a data-id="%2$s" class="%3$s" href="%4$s"%5$s>%6$s</a></%1$s>', $title_tag, $pID, $anchorClass, $pLink, $link_target, $title );
	}

	$postMetaMid = null;

	if ( ! empty( $postMetaTop ) && ( empty( $metaPosition ) || $metaPosition == 'above_excerpt' ) ) {
		$htmlDetail .= "<div class='post-meta-user {$metaPosition} {$metaSeparator}'>{$postMetaTop}</div>";
	}

	if ( ! empty( $postMetaMid ) ) {
		$htmlDetail .= "<div class='post-meta-tags'>{$postMetaMid}</div>";
	}

	if ( in_array( 'cf', $items ) && ! empty( $cf_group ) ) {
		$htmlDetail .= Functions::get_cf_formatted_fields( $cf_group, $format, $pID );
	}

	if ( ! empty( $htmlDetail ) ) {
		$html .= "<div class='rt-detail'>$htmlDetail</div>";
	}

	$html .= '</div>';
	$html .= '</div>';
}

Fns::print_html( $html );
