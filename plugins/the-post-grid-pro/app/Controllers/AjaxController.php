<?php
/**
 * Ajax Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers;

use RT\ThePostGridPro\Traits\ELTempleateBuilderTraits;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;
use RT\ThePostGridPro\Helpers\Functions;
use WP_Query;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended

/**
 * Ajax Controller Class.
 */
class AjaxController {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * Layout4 toggle
	 *
	 * @var string
	 */
	private $l4toggleLoadMore;

	/**
	 * Order
	 *
	 * @var string
	 */
	private $order = 'DESC';

	/**
	 * Class constructor
	 */
	public function __construct() {
		// Ajax Callback for Shortcode.
		add_action( 'wp_ajax_tpgLayoutAjaxAction', [ $this, 'tpgLayoutAjaxAction' ] );
		add_action( 'wp_ajax_nopriv_tpgLayoutAjaxAction', [ $this, 'tpgLayoutAjaxAction' ] );

		// Ajax Callback for Gutenberg and Elementor.
		add_action( 'wp_ajax_tpgElLayoutAjaxAction', [ $this, 'tpgElLayoutAjaxAction' ] );
		add_action( 'wp_ajax_nopriv_tpgElLayoutAjaxAction', [ $this, 'tpgElLayoutAjaxAction' ] );

		// Ajax callback for Elementor archive page builder.
		add_action( 'wp_ajax_tpgp_el_templeate_builder', [ $this, 'tpgp_el_templeate_builder' ] );
		add_action( 'wp_ajax_tpgp_el_create_templeate', [ $this, 'tpgp_el_create_templeate' ] );
		add_action( 'wp_ajax_tpgp_el_default_template', [ $this, 'tpgp_el_default_template' ] );
	}

	/**
	 * Ajax Callback for Gutenberg and Elementor.
	 *
	 * @return void
	 */
	public function tpgElLayoutAjaxAction() {
		$error       = true;
		$msg         = $data = null;
		$paged       = 2;
		$total_pages = 1;
		$args        = [];

		ob_start();

		$el_is_click = $_POST['el_is_click'];

		$el_settings = $_POST['el_settings'];
		$el_query    = $_POST['el_query'];
		$el_path     = str_replace( '\\\\', '\\', $_POST['el_path'] );
		$paged       = $args['paged'] = ! empty( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 2;

		if ( $el_settings['search_by'] === 'title' ) {
			$el_query['search_prod_title'] = $el_query['s'];
		}

		if ( Fns::verifyNonce() ) {
			if ( $el_settings['search_by'] === 'title' ) {
				add_filter( 'posts_where', [ __CLASS__, 'rttpg_title_keyword_filter' ], 10, 2 );
			}
			$query = new WP_Query( apply_filters( 'tpg_sc_query_args', $el_query ) );
			if ( $el_settings['search_by'] === 'title' ) {
				remove_filter( 'posts_where', [ __CLASS__, 'rttpg_title_keyword_filter' ], 10, 2 );
			}
			// Start layout.
			if ( $query->have_posts() ) {
				$pCount = 1;
				$error  = false;

				while ( $query->have_posts() ) {
					$query->the_post();
					set_query_var( 'tpg_post_count', $pCount );
					set_query_var( 'tpg_total_posts', $query->post_count );
					$block_type = isset( $el_settings['is_gutenberg'] ) && $el_settings['is_gutenberg'] === 'yes' ? 'gutenberg' : 'elementor';
					Functions::tpg_template( $el_settings, $el_path, $block_type );
					$pCount ++;
				}
			} else {
				if ( $paged == 1 ) {
					$error = false;
				}

				if ( $el_settings['no_posts_found_text'] ) {
					printf( "<div class='no_posts_found_text rt-col-sm-12'>%s</div>", esc_html( $el_settings['no_posts_found_text'] ) );
				} else {
					printf( "<div class='no_posts_found_text rt-col-sm-12'>%s</div>", esc_html__( 'No post found', 'the-post-grid-pro' ) );
				}
			}

			$total_pages = $query->max_num_pages;

			wp_reset_postdata();
		} else {
			$msg = apply_filters( 'tpg_session_error_text', esc_html__( 'Session error', 'the-post-grid-pro' ) );
		}

		if ( 'load_more' == $el_is_click || 'pagination_ajax' == $el_is_click || 'load_on_scroll' == $el_is_click ) {
			$el_query['offset'] = intval( $el_query['offset'] ) + intval( $el_query['posts_per_page'] );
		} else {
			$el_query['offset'] = intval( $el_query['offset'] );
		}

		$data = ob_get_contents();
		ob_clean();

		wp_send_json(
			apply_filters(
				'tpg_load_more_response',
				[
					'error'       => $error,
					'msg'         => $msg,
					'data'        => $data,
					'paged'       => $paged,
					'total_pages' => $total_pages,
					'l4toggle'    => ( $this->l4toggleLoadMore ? 1 : null ),
					'args'        => $args,
					'el_query'    => $el_query,
				]
			)
		);
	}

	/**
	 * Ajax Callback for Shortcode
	 *
	 * @return void
	 */
	public function tpgLayoutAjaxAction() {
		$error       = true;
		$msg         = $data = null;
		$paged       = 2;
		$total_pages = 1;
		$args        = [];
		if ( Fns::verifyNonce() ) {
			$scID = intval( $_REQUEST['scID'] );

			if ( $scID && ! is_null( get_post( $scID ) ) ) {
				$scMeta = get_post_meta( $scID );
				$layout = ( isset( $scMeta['layout'][0] ) ? $scMeta['layout'][0] : 'layout1' );
				if ( ! in_array( $layout, array_keys( Options::rtTPGLayouts() ) ) ) {
					$layout = 'layout1';
				}
				if ( $layout == 'layout4' ) {
					$this->l4toggleLoadMore = empty( $_REQUEST['l4toggle'] );
				}
				$isIsotope  = preg_match( '/isotope/', $layout );
				$isCarousel = preg_match( '/carousel/', $layout );
				$isOffset   = preg_match( '/offset/', $layout );
				$colStore   = $dCol = ( isset( $scMeta['column'][0] ) ? absint( $scMeta['column'][0] ) : 3 );
				$tCol       = ( isset( $scMeta['tpg_tab_column'][0] ) ? absint( $scMeta['tpg_tab_column'][0] ) : 2 );
				$mCol       = ( isset( $scMeta['tpg_mobile_column'][0] ) ? absint( $scMeta['tpg_mobile_column'][0] ) : 1 );
				if ( ! in_array( $dCol, array_keys( Options::scColumns() ) ) ) {
					$dCol = 3;
				}
				if ( ! in_array( $tCol, array_keys( Options::scColumns() ) ) ) {
					$tCol = 2;
				}
				if ( ! in_array( $dCol, array_keys( Options::scColumns() ) ) ) {
					$mCol = 1;
				}
				if ( $isOffset ) {
					$dCol = ( $dCol < 3 ? 2 : $dCol );
					$tCol = ( $tCol < 3 ? 2 : $tCol );
					$mCol = ( $mCol < 3 ? 1 : $mCol );
				}
				$arg                        = [];
				$fImg                       = ( ! empty( $scMeta['feature_image'][0] ) ? true : false );
				$fImgSize                   = ( isset( $scMeta['featured_image_size'][0] ) ? $scMeta['featured_image_size'][0] : 'medium' );
				$mediaSource                = ( isset( $scMeta['media_source'][0] ) ? $scMeta['media_source'][0] : 'feature_image' );
				$arg['excerpt_type']        = ( isset( $scMeta['tgp_excerpt_type'][0] ) ? $scMeta['tgp_excerpt_type'][0] : 'character' );
				$arg['title_limit_type']    = ( isset( $scMeta['tpg_title_limit_type'][0] ) ? $scMeta['tpg_title_limit_type'][0] : 'character' );
				$arg['excerpt_limit']       = ( isset( $scMeta['excerpt_limit'][0] ) ? absint( $scMeta['excerpt_limit'][0] ) : 0 );
				$arg['title_limit']         = ( isset( $scMeta['tpg_title_limit'][0] ) ? absint( $scMeta['tpg_title_limit'][0] ) : 0 );
				$arg['excerpt_more_text']   = ( isset( $scMeta['tgp_excerpt_more_text'][0] ) ? $scMeta['tgp_excerpt_more_text'][0] : null );
				$arg['read_more_text']      = ( ! empty( $scMeta['tgp_read_more_text'][0] )
					? $scMeta['tgp_read_more_text'][0]
					: esc_html__(
						'Read More',
						'the-post-grid-pro'
					) );
				$arg['show_all_text']       = ( ! empty( $scMeta['tpg_show_all_text'][0] )
					? $scMeta['tpg_show_all_text'][0]
					: esc_html__(
						'Show all',
						'the-post-grid-pro'
					) );
				$arg['tpg_title_position']  = isset( $scMeta['tpg_title_position'][0] ) && ! empty( $scMeta['tpg_title_position'][0] ) ? $scMeta['tpg_title_position'][0]
					: null;
				$arg['btn_alignment_class'] = isset( $scMeta['tpg_read_more_button_alignment'][0] ) && ! empty( $scMeta['tpg_read_more_button_alignment'][0] )
					? $scMeta['tpg_read_more_button_alignment'][0] : '';
				// Category Settings
				$arg['category_position'] = isset( $scMeta['tpg_category_position'][0] ) ? $scMeta['tpg_category_position'][0] : null;
				$arg['category_style']    = ! empty( $scMeta['tpg_category_style'][0] ) ? $scMeta['tpg_category_style'][0] : '';
				$arg['catIcon']           = isset( $scMeta['tpg_category_icon'][0] ) ? $scMeta['tpg_category_icon'][0] : true;
				// Meta Settings
				$arg['metaPosition']  = isset( $scMeta['tpg_meta_position'][0] ) ? $scMeta['tpg_meta_position'][0] : null;
				$arg['metaIcon']      = isset( $scMeta['tpg_meta_icon'][0] ) ? $scMeta['tpg_meta_icon'][0] : true;
				$arg['metaSeparator'] = ! empty( $scMeta['tpg_meta_separator'][0] ) ? $scMeta['tpg_meta_separator'][0] : '';
				/* Argument create */
				$args     = [];
				$postType = ( isset( $scMeta['tpg_post_type'][0] ) ? $scMeta['tpg_post_type'][0] : null );
				if ( $postType ) {
					$args['post_type'] = $postType;
				}

				// Common filter
				/* post__in */
				$post__in = ( isset( $scMeta['post__in'][0] ) ? $scMeta['post__in'][0] : null );
				if ( $post__in ) {
					$post__in         = explode( ',', $post__in );
					$args['post__in'] = $post__in;
				}
				/* post__not_in */
				$post__not_in = ( isset( $scMeta['post__not_in'][0] ) ? $scMeta['post__not_in'][0] : null );
				if ( $post__not_in ) {
					$post__not_in         = explode( ',', $post__not_in );
					$args['post__not_in'] = $post__not_in; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
				}

				/* LIMIT */
				$limit                  = ( ( empty( $scMeta['limit'][0] ) || $scMeta['limit'][0] === '-1' ) ? - 1 : absint( $scMeta['limit'][0] ) );
				$queryOffset            = empty( $scMeta['offset'][0] ) ? 0 : absint( $scMeta['offset'][0] );
				$args['posts_per_page'] = $limit;
				$pagination             = ! empty( $scMeta['pagination'][0] );

				$posts_per_page         = ( isset( $scMeta['posts_per_page'][0] ) ? intval( $scMeta['posts_per_page'][0] ) : $limit );
				$args['posts_per_page'] = $posts_per_page;

				if ( ( $posts_per_page == '-1' && $limit ) || empty( $posts_per_page ) ) {
					$args['posts_per_page'] = $limit;
				}

				if ( $pagination ) {
					$paged = $args['paged'] = ! empty( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 2;
				}
    
				// Advance Filter
				/**
				 * $adv_filter - Advanced filter field (ex. category, tags, users, order etc)
				 * $tpg_taxonomy - return all taxonomy = category, post_tag
				 * $action_taxonomy: Request for taxonomy - default is category. it can be tags or others taxonomies
				 * $action_term: Request for terms by its id like 5, 14, 11 etc
				 */
				$adv_filter      = ( isset( $scMeta['post_filter'] ) ? $scMeta['post_filter'] : [] );
				$tpg_taxonomy    = ! empty( $scMeta['tpg_taxonomy'] ) ? $scMeta['tpg_taxonomy'] : [];
				$action_taxonomy = ! empty( $_REQUEST['taxonomy'] ) ? trim( $_REQUEST['taxonomy'] ) : null;
				$action_term     = ! empty( $_REQUEST['term'] ) ? ( $_REQUEST['term'] === 'all' ? $_REQUEST['term'] : absint( $_REQUEST['term'] ) ) : 0;
				// Taxonomy
				$taxQ = [];
				if ( in_array( 'tpg_taxonomy', $adv_filter ) && ! empty( $tpg_taxonomy ) ) {
					foreach ( $tpg_taxonomy as $taxonomy ) {
						$terms = ! empty( $scMeta[ 'term_' . $taxonomy ] ) ? $scMeta[ 'term_' . $taxonomy ] : [];

						if ( is_array( $terms ) && ! empty( $terms ) && ( $action_term === 'all' || ! in_array( $action_taxonomy, $tpg_taxonomy ) ) ) {
							$operator = ! empty( $scMeta[ 'term_operator_' . $taxonomy ][0] ) ? $scMeta[ 'term_operator_' . $taxonomy ][0] : 'IN';
							$taxQ[]   = [
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $terms,
								'operator' => $operator,
							];
						} elseif ( is_array( $terms ) && ! empty( $terms ) && ( $action_term === 'all' || in_array( $action_taxonomy, $tpg_taxonomy ) ) ) {
							if ( $taxonomy == $action_taxonomy ) {
								continue;
							}
							$operator = ! empty( $scMeta[ 'term_operator_' . $taxonomy ][0] ) ? $scMeta[ 'term_operator_' . $taxonomy ][0] : 'IN';
							$taxQ[]   = [
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $terms,
								'operator' => $operator,
							];

						}
					}
				}

				if ( $action_taxonomy && $action_term && $action_term !== 'all' ) {
					$taxQ[] = [
						'taxonomy' => $action_taxonomy,
						'field'    => 'term_id',
						'terms'    => [ $action_term ],
						'operator' => 'IN',
					];
				}

				if ( is_array( $taxQ ) ) {
					$taxQ['relation'] = ! empty( $scMeta['taxonomy_relation'][0] ) ? $scMeta['taxonomy_relation'][0] : 'AND';
				}

				if ( ! empty( $taxQ ) ) {
					$args['tax_query'] = $taxQ; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				}

				// Order
				if ( in_array( 'order', $adv_filter ) ) {
					$order_by = ( isset( $scMeta['order_by'][0] ) ? $scMeta['order_by'][0] : null );
					$order    = ( isset( $scMeta['order'][0] ) ? $scMeta['order'][0] : null );
					if ( $order ) {
						$args['order'] = $order;
					}
					if ( $order_by ) {
						$args['orderby'] = $order_by;
						$meta_key        = ! empty( $scMeta['tpg_meta_key'][0] ) ? trim( $scMeta['tpg_meta_key'][0] ) : null;
						if ( in_array( $order_by, array_keys( Options::rtMetaKeyType() ) ) && $meta_key ) {
							$args['orderby']  = $order_by;
							$args['meta_key'] = $meta_key; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						}
					}
				}
				// override shortcode filter
				$action_order = ! empty( $_REQUEST['order'] ) ? trim( $_REQUEST['order'] ) : null;
				if ( $action_order ) {
					$args['order'] = $action_order;
				}
				$action_order_by = ! empty( $_REQUEST['order_by'] ) ? trim( $_REQUEST['order_by'] ) : null;
				if ( $action_order_by ) {
					$args['orderby'] = $action_order_by;
					unset( $args['meta_key'] );
					$meta_key = ! empty( $scMeta['tpg_meta_key'][0] ) ? $scMeta['tpg_meta_key'][0] : null;
					if ( in_array( $action_order_by, array_keys( Options::rtMetaKeyType() ) ) && $meta_key ) {
						$args['orderby']  = $order_by;
						$args['meta_key'] = $meta_key; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					}
				}
				$this->order = ! empty( $args['order'] ) ? $args['order'] : 'DESC';
				if ( $postType == 'product' && ! empty( $args['orderby'] ) ) {
					switch ( $args['orderby'] ) {
						case 'price':
							$args['orderby']  = 'meta_value_num';
							$args['meta_key'] = '_price'; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
							break;
						case 'rating':
							// Sorting handled later though a hook
							add_filter( 'posts_clauses', [ $this, 'order_by_rating_post_clauses' ] );
							break;
					}
				}
				// Status
				if ( in_array( 'tpg_post_status', $adv_filter ) ) {
					$post_status = ( isset( $scMeta['tpg_post_status'] ) && ! empty( $scMeta['tpg_post_status'] ) ? $scMeta['tpg_post_status'] : [] );
					if ( ! empty( $post_status ) ) {
						$args['post_status'] = $post_status;
					}
				} else {
					$args['post_status'] = 'publish';
				}
				// Author
				$author = ( isset( $scMeta['author'] ) ? $scMeta['author'] : [] );
				if ( in_array( 'author', $adv_filter ) && ! empty( $author ) ) {
					$args['author__in'] = $author;
				}
				$action_author = ! empty( $_REQUEST['author'] ) ? absint( $_REQUEST['author'] ) : null;
				if ( $action_author ) {
					$args['author__in'] = [ $action_author ];
				}

				// Search
				$s = ( isset( $scMeta['s'][0] ) ? $scMeta['s'][0] : [] );
				if ( in_array( 's', $adv_filter ) && ! empty( $s ) ) {
					$args['s'] = $s;
				}
				$sAction = ( ! empty( $_REQUEST['search'] ) ? trim( $_REQUEST['search'] ) : null );
				if ( $sAction ) {
					$args['s'] = $sAction;
				}

				// Date query
				if ( in_array( 'date_range', $adv_filter ) ) {
					$startDate = ( ! empty( $scMeta['date_range_start'][0] ) ? $scMeta['date_range_start'][0] : null );
					$endDate   = ( ! empty( $scMeta['date_range_end'][0] ) ? $scMeta['date_range_end'][0] : null );
					if ( $startDate && $endDate ) {
						$args['date_query'] = [
							[
								'after'     => $startDate,
								'before'    => $endDate,
								'inclusive' => true,
							],
						];
					}
				}
				if ( ! empty( $_REQUEST['archive'] ) ) {
					$archive         = $_REQUEST['archive'];
					$archiveValue    = ! empty( $_REQUEST['archive_value'] ) ? $_REQUEST['archive_value'] : null;
					$settings        = get_option( rtTPG()->options['settings'] );
					$oLayoutTag      = ! empty( $settings['template_tag'] ) ? absint( $settings['template_tag'] ) : null;
					$oLayoutAuthor   = ! empty( $settings['template_author'] ) ? $settings['template_author'] : null;
					$oLayoutCategory = ! empty( $settings['template_category'] ) ? $settings['template_category'] : null;
					$oLayoutSearch   = ! empty( $settings['template_search'] ) ? $settings['template_search'] : null;
					$dataArchive     = null;
					if ( $archive ) {
						unset( $args['post_type'] );
						unset( $args['tax_query'] );
						unset( $args['author__in'] );
						if ( $oLayoutTag && $archive == 'tag' ) {
							if ( ! empty( $archiveValue ) ) {
								$args['tag'] = $archiveValue;
							}
						} elseif ( $oLayoutCategory && $archive == 'category' ) {
							if ( ! empty( $archiveValue ) ) {
								$args['category_name'] = $archiveValue;
							}
						} elseif ( $oLayoutAuthor && $archive == 'author' ) {
							if ( ! empty( $archiveValue ) ) {
								$args['author'] = $archiveValue;
							}
						} elseif ( $oLayoutSearch && $archive == 'search' ) {
							$args['s'] = $archiveValue;
						}
						$args['posts_per_archive_page'] = $args['posts_per_page'];
					}
				}

				// Validation
				$dCol = $dCol == 5 ? '24' : round( 12 / $dCol );
				$tCol = $dCol == 5 ? '24' : round( 12 / $tCol );
				$mCol = $dCol == 5 ? '24' : round( 12 / $mCol );
				if ( $isCarousel ) {
					$dCol = $tCol = $mCol = 12;
				}
				$arg['grid'] = "rt-col-md-{$dCol} rt-col-sm-{$tCol} rt-col-xs-{$mCol}";
				if ( $layout == 'layout2' || $layout == 'layout3' ) {
					$iCol                = ( isset( $scMeta['tgp_layout2_image_column'][0] ) ? absint( $scMeta['tgp_layout2_image_column'][0] ) : 4 );
					$iCol                = $iCol > 12 ? 4 : $iCol;
					$cCol                = 12 - $iCol;
					$arg['image_area']   = "rt-col-sm-{$iCol} rt-col-xs-12 ";
					$arg['content_area'] = "rt-col-sm-{$cCol} rt-col-xs-12 ";
				} elseif ( $layout == 'layout4' ) {
					$arg['image_area']   = 'rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ';
					$arg['content_area'] = 'rt-col-md-6 rt-col-sm-12 rt-col-xs-12 ';
				}
				$gridType = ! empty( $scMeta['grid_style'][0] ) ? $scMeta['grid_style'][0] : 'even';

				$masonryG    = null;
				$arg_class   = [];
				$arg_class[] = 'rt-grid-item';
				if ( $isOffset ) {
					$arg_class[] = 'rt-offset-item';
				} else {
					if ( $gridType == 'even' ) {
						$masonryG    = ' tpg-even';
						$arg_class[] = 'even-grid-item';
					} elseif ( $gridType == 'masonry' && ! $isIsotope && ! $isCarousel ) {
						$masonryG    = 'tpg-masonry';
						$arg_class[] = 'masonry-grid-item';
					}
				}
				// Category class
				$catHaveBg = ( isset( $scMeta['tpg_category_bg'][0] ) ? $scMeta['tpg_category_bg'][0] : '' );
				if ( ! empty( $catHaveBg ) ) {
					$arg_class[] = 'category-have-bg';
				}
				// Image animation type
				$imgAnimationType = isset( $scMeta['tpg_image_animation'][0] ) ? $scMeta['tpg_image_animation'][0] : '';
				if ( ! empty( $imgAnimationType ) ) {
					$arg_class[] = $imgAnimationType;
				}
				$preLoader = null;
				if ( $isIsotope ) {
					$arg_class[] = 'isotope-item';
					$preLoader   = 'tpg-pre-loader';
				}
				if ( $isCarousel ) {
					$arg_class[] = 'swiper-slide';
					$preLoader   = 'tpg-pre-loader';
				}

				$margin = ! empty( $scMeta['margin_option'][0] ) ? $scMeta['margin_option'][0] : 'default';
				if ( $margin == 'no' ) {
					$arg_class[] = 'no-margin';
				}
				if ( ! empty( $scMeta['tpg_image_type'][0] ) && $scMeta['tpg_image_type'][0] == 'circle' ) {
					$arg_class[] = 'tpg-img-circle';
				}
				$arg['class']       = implode( ' ', $arg_class );
				$arg['anchorClass'] = $arg['link_target'] = null;
				$link               = isset( $scMeta['link_to_detail_page'][0] ) ? $scMeta['link_to_detail_page'][0] : '1';
				$link               = ( $link == 'yes' ) ? '1' : $link;
				if ( ! $link ) {
					$arg['anchorClass'] = ' disabled';
				}
				$isSinglePopUp = false;
				$linkType      = ! empty( $scMeta['detail_page_link_type'][0] ) ? $scMeta['detail_page_link_type'][0] : 'popup';
				if ( $link == '1' ) {
					if ( $linkType == 'popup' ) {
						$popupType = ! empty( $scMeta['popup_type'][0] ) ? $scMeta['popup_type'][0] : 'single';
						if ( $popupType == 'single' ) {
							$arg['anchorClass'] .= ' tpg-single-popup';
							$isSinglePopUp      = true;
						} else {
							$arg['anchorClass'] .= ' tpg-multi-popup';
						}
					} else {
						$arg['link_target'] = ! empty( $scMeta['link_target'][0] ) ? " target='{$scMeta['link_target'][0]}'" : null;
					}
				}

				$defaultImgId  = ( ! empty( $scMeta['default_preview_image'][0] ) ? absint( $scMeta['default_preview_image'][0] ) : null );
				$customImgSize = ( ! empty( $scMeta['custom_image_size'] ) ? $scMeta['custom_image_size'] : [] );
				// Grid Hover Layout
				$fSmallImgSize      = ( isset( $scMeta['featured_small_image_size'][0] ) ? $scMeta['featured_small_image_size'][0] : 'medium' );
				$customSmallImgSize = ( ! empty( $scMeta['custom_small_image_size'] ) ? $scMeta['custom_small_image_size'] : [] );

				$arg['items'] = isset( $scMeta['item_fields'] ) ? ( $scMeta['item_fields'] ? $scMeta['item_fields'] : [] ) : [];
				if ( in_array( 'cf', $arg['items'] ) ) {
					$arg['cf_group'] = [];
					$arg['cf_group'] = get_post_meta( $scID, 'cf_group' );
					$arg['format']   = [
						'hide_empty'       => get_post_meta( $scID, 'cf_hide_empty_value', true ),
						'show_value'       => get_post_meta( $scID, 'cf_show_only_value', true ),
						'hide_group_title' => get_post_meta( $scID, 'cf_hide_group_title', true ),
					];
				}

				// Set readmore false if excerpt type = full content
				if ( isset( $arg['excerpt_type'] ) && $arg['excerpt_type'] === 'full' && ( $key = array_search( 'read_more', $arg['items'] ) ) !== false ) {
					unset( $arg['items'][ $key ] );
				}
				if ( empty( $scMeta['ignore_sticky_posts'] ) ) {
					$args['ignore_sticky_posts'] = true;
				} else {
					$args['wp_tpg_is_home'] = true;
				}

				if ( $limit != - 1 && $pagination ) {
					$tempArgs                   = $args;
					$tempArgs['posts_per_page'] = $limit;
					$tempArgs['paged']          = 1;
					$tempArgs['fields']         = 'ids';
					$tempQ                      = new WP_Query( $tempArgs );
					if ( ! empty( $tempQ->posts ) ) {
						$args['post__in'] = $tempQ->posts;
					}
				}

				if ( $pagination && $queryOffset && isset( $args['paged'] ) ) {
					$queryOffset = ( $posts_per_page * ( $args['paged'] - 1 ) ) + $queryOffset;
				}
				if ( $queryOffset ) {
					$args['offset'] = $queryOffset;
				}

				$arg['title_tag'] = ( ! empty( $scMeta['title_tag'][0] ) && in_array( $scMeta['title_tag'][0], array_keys( Options::getTitleTags() ) ) )
					? esc_attr( $scMeta['title_tag'][0] ) : 'h3';

				$query = new WP_Query( apply_filters( 'tpg_sc_query_args', $args ) );
				// Start layout
				if ( $query->have_posts() ) {
					$l                = $offLoop = 0;
					$offsetBigHtml    = $offsetSmallHtml = null;
					$gridPostCount    = 0;
					$arg['totalPost'] = $query->post_count;

					while ( $query->have_posts() ) {
						$query->the_post();

						if ( $colStore == $l ) {
							if ( $this->l4toggleLoadMore ) {
								$this->l4toggleLoadMore = false;
							} else {
								$this->l4toggleLoadMore = true;
							}
							$l = 0;
						}
						$l ++;

						$pID               = get_the_ID();
						$arg['postCount']  = $gridPostCount ++;
						$external_link     = get_post_meta( $pID, 'tpg_read_more', true );
						$arg['pID']        = $pID;
						$arg['title']      = Fns::get_the_title( $pID, $arg );
						$arg['pLink']      = $external_link['url'] ?? get_permalink();
						$arg['toggle']     = $this->l4toggleLoadMore;
						$arg['author']     = '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author() . '</a>';
						$comments_number   = get_comments_number( $pID );
						$comments_text     = sprintf( '(%s)', number_format_i18n( $comments_number ) );
						$arg['date']       = get_the_date();
						$arg['excerpt']    = Fns::get_the_excerpt( $pID, $arg );
						$arg['categories'] = get_the_term_list( $pID, 'category', null, '<span class="rt-separator">,</span>' );
						$arg['tags']       = get_the_term_list( $pID, 'post_tag', null, '<span class="rt-separator">,</span>' );
						$arg['post_count'] = get_post_meta( $pID, Fns::get_post_view_count_meta_key(), true );
						if ( $isIsotope ) {
							$isotope_filter = isset( $scMeta['isotope_filter'][0] ) ? $scMeta['isotope_filter'][0] : null;
							$termAs         = wp_get_post_terms(
								$pID,
								$isotope_filter,
								[ 'fields' => 'all' ]
							);
							$isoFilter      = [];
							if ( ! empty( $termAs ) ) {
								foreach ( $termAs as $term ) {
									$isoFilter[] = 'iso_' . $term->term_id;
									$isoFilter[] = 'rt-item-' . esc_attr( $term->slug );
								}
							}
							$arg['isoFilter'] = ! empty( $isoFilter ) ? implode( ' ', $isoFilter ) : '';
						}
						$deptClass = null;
						if ( ! empty( $deptAs ) ) {
							foreach ( $deptAs as $dept ) {
								$deptClass .= ' ' . $dept->slug;
							}
						}
						if ( comments_open() ) {
							$arg['comment'] = "<a href='" . get_comments_link( $pID ) . "'>{$comments_text} </a>";
						} else {
							$arg['comment'] = "{$comments_text}";
						}
						$imgSrc             = null;
						$arg['smallImgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
							$pID,
							$fSmallImgSize,
							$mediaSource,
							$defaultImgId,
							$customSmallImgSize
						) : null;
						if ( $isOffset ) {
							if ( $offLoop == 0 ) {
								$arg['imgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
									$pID,
									$fImgSize,
									$mediaSource,
									$defaultImgId,
									$customImgSize
								) : null;
								$arg['offset'] = 'big';
								$offsetBigHtml = Fns::get_template_html( 'layouts/' . $layout, $arg );
							} else {
								$arg['offset']    = 'small';
								$arg['offsetCol'] = [ $dCol, $tCol, $mCol ];
								$arg['imgSrc']    = ! $fImg ? Fns::getFeatureImageSrc(
									$pID,
									'thumbnail',
									$mediaSource,
									$defaultImgId,
									$customImgSize
								) : null;
								$offsetSmallHtml  .= Fns::get_template_html( 'layouts/' . $layout, $arg );
							}
						} else {
							$arg['imgSrc'] = ! $fImg ? Fns::getFeatureImageSrc(
								$pID,
								$fImgSize,
								$mediaSource,
								$defaultImgId,
								$customImgSize
							) : null;
							$data          .= Fns::get_template_html( 'layouts/' . $layout, $arg );
						}
						$offLoop ++;
					}
					if ( $isOffset ) {
						$oDCol = Fns::get_offset_col( $dCol );
						$oTCol = Fns::get_offset_col( $tCol );
						$oMCol = Fns::get_offset_col( $mCol );
						if ( $layout == 'offset03' || $layout == 'offset04' ) {
							$oDCol['big'] = $oTCol['big'] = $oDCol['small'] = $oTCol['small'] = 6;
							$oMCol['big'] = $oMCol['small'] = 12;
						} elseif ( $layout == 'offset06' ) {
							$oDCol['big']   = 7;
							$oDCol['small'] = 5;
						}
						$data .= "<div class='rt-col-md-{$oDCol['big']} rt-col-sm-{$oTCol['big']} rt-col-xs-{$oMCol['big']}'><div class='rt-row'>{$offsetBigHtml}</div></div>";
						$data .= "<div class='rt-col-md-{$oDCol['small']} rt-col-sm-{$oTCol['small']} rt-col-xs-{$oMCol['small']}'><div class='rt-row offset-small-wrap'>{$offsetSmallHtml}</div></div>";
					}
					if ( ! empty( $data ) ) {
						$error = false;
					}
				} else {
					if ( $paged == 1 ) {
						$error = false;
					}
					$not_found_text = isset( $scMeta['tgp_not_found_text'][0] ) && ! empty( $scMeta['tgp_not_found_text'][0] ) ? esc_attr( $scMeta['tgp_not_found_text'][0] )
						: esc_html__( 'No post found', 'the-post-grid-pro' );
					$data           = $msg = apply_filters( 'tpg_not_found_text', $not_found_text, $args, $scMeta );
				}
				$total_pages = $query->max_num_pages;
				wp_reset_postdata();
			} else {
				$msg = apply_filters( 'tpg_shortcode_not_found_error_text', esc_html__( 'Shortcode Id not defined', 'the-post-grid-pro' ) );
			}
		} else {
			$msg = apply_filters( 'tpg_session_error_text', esc_html__( 'Session error', 'the-post-grid-pro' ) );
		}

		wp_send_json(
			apply_filters(
				'tpg_load_more_response',
				[
					'error'       => $error,
					'msg'         => $msg,
					'data'        => $data,
					'paged'       => $paged,
					'total_pages' => $total_pages,
					'l4toggle'    => ( $this->l4toggleLoadMore ? 1 : null ),
					'args'        => $args,
				]
			)
		);
	}

	public function order_by_rating_post_clauses( $args ) {
		global $wpdb;
		$args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";
		$args['where']  .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";
		$args['join']   .= "
			LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";

		$args['orderby'] = "average_rating {$this->order}, $wpdb->posts.post_date {$this->order}";

		$args['groupby'] = "$wpdb->posts.ID";

		return $args;
	}


	/**
	 * Elementor template builder
	 *
	 * @return void
	 */
	public static function tpgp_el_templeate_builder() {
		$title = '<h2>' . esc_html__( 'Template Settings', 'the-post-grid-pro' ) . '</h2>';
		if ( ! Fns::verifyNonce() ) {
			$return = [
				'success' => false,
				'title'   => $title,
				'content' => esc_html__( 'Session Expired...', 'the-post-grid-pro' ),
			];
			wp_send_json( $return );
		}
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : null;

		$template_type    = null;
		$template_default = null;
		$url              = null;
		$tmp_title        = '';
		$edit_with        = did_action( 'elementor/loaded' ) ? 'elementor' : 'gutenberg';
		$editor_btn_text  = '';

		if ( $post_id ) {
			$tmp_title        = get_the_title( $post_id );
			$type             = get_post_meta( $post_id, self::template_type_meta_key() );
			$template_type    = isset( $type[0] ) ? $type[0] : '';
			$template_default = absint( self::builder_page_id( $template_type ) );
			$edit_with        = self::page_edit_with( $post_id );
			$editor_btn_text  = self::page_edit_btn_text( $edit_with );
			$url              = add_query_arg(
				[
					'post'   => $post_id,
					'action' => $edit_with === 'elementor' ? 'elementor' : 'edit',
				],
				admin_url( 'post.php' )
			);
		}

		$builder_type_list = Fns::get_builder_type_list();
		ob_start();

		?>
        <form action="<?php echo esc_url( admin_url( 'edit.php?post_type=tpgp_builder' ) ); ?>" autocomplete="off">
            <div class="tpgp-tb-modal-wrapper ">
                <div class="tpgp-template-name tpgp-tb-field-wraper">
                    <label for="tpgp_tb_template_name"> <?php esc_html_e( 'Template name', 'the-post-grid-pro' ); ?></label>
                    <input required class="tpgp-field" type="text" id="tpgp_tb_template_name"
                           name="tpgp_tb_template_name"
                           placeholder="<?php esc_attr_e( 'Template name', 'the-post-grid-pro' ); ?>"
                           value="<?php echo esc_attr( $tmp_title ); ?>" autocomplete="off">
                    <span class="message"
                          style="display: none; color:red"><?php esc_html_e( 'This field is required', 'the-post-grid-pro' ); ?></span>
                </div>
                <div class="tpgp-template-type tpgp-tb-field-wraper">
                    <label for="tpgp_tb_template_type"><?php esc_html_e( 'Template Type', 'the-post-grid-pro' ); ?></label>
                    <select class="tpgp-field" id="tpgp_tb_template_type" name="tpgp_tb_template_type">

						<?php foreach ( $builder_type_list as $builder_id => $builder_title ) : ?>
                            <option <?php echo $builder_id === $template_type ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr( $builder_id ); ?>">
								<?php echo esc_html( $builder_title ); ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>

                <div class="tpgp-template-edit-with tpgp-tb-field-wraper">
                    <label for="tpgp_tb_template_edit_with"><?php esc_html_e( 'Editor Type', 'tpgp-elementor-builder' ); ?></label>
                    <select class="tpgp-field" id="tpgp_tb_template_edit_with" name="tpgp_tb_template_edit_with" required>
						<?php if ( did_action( 'elementor/loaded' ) ) : ?>
                            <option value="elementor" <?php echo 'elementor' === $edit_with ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Elementor', 'tpgp-elementor-builder' ); ?></option>
						<?php endif; ?>
                        <option value="gutenberg" <?php echo 'gutenberg' === $edit_with ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Gutenberg', 'tpgp-elementor-builder' ); ?></option>
                    </select>
                </div>

                <div class="tpgp-template-setdefaults">
                    <input type="checkbox" id="default_template" class="tpgp-field" name="default_template"
                           value="default_template"
						<?php
						echo ( $post_id && absint( $post_id ) === absint( $template_default ) ) ? 'checked' : '';
						?>
                    >
                    <label for="default_template"> <?php esc_html_e( 'Set Default Template', 'the-post-grid-pro' ); ?></label><br>
                </div>
                <input type="hidden" id="page_id" name="page_id" value="<?php echo esc_attr( $post_id ); ?>">

                <div class="tpgp-template-footer">
                    <div class="tpgp-tb-button-wrapper save-button">
                        <button <?php echo $post_id ? esc_attr( 'disabled' ) : ''; ?> type="submit" id="tpgp_tb_button"><?php esc_html_e( 'Save', 'the-post-grid-pro' ); ?></button>
                    </div>
                    <div class="tpgp-tb-button-wrapper tpgp-tb-edit-button-wrapper">
                        <a href="<?php echo esc_url( $url ); ?>" class="btn">
							<?php esc_html( $editor_btn_text ); // Edit with elementor or gutenberg. ?>
                        </a>
                    </div>
                </div>
            </div>
        </form>
		<?php
		$content = ob_get_clean();
		$return  = [
			'success' => true,
			'title'   => $title,
			'content' => $content,
		];
		wp_send_json( $return );
		wp_die();
	}

	/**
	 * Elementor Create Templeate
	 *
	 * @return void
	 */
	public static function tpgp_el_create_templeate() {
		$page_type        = isset( $_POST['page_type'] ) ? sanitize_text_field( wp_unslash( $_POST['page_type'] ) ) : null;
		$page_id          = isset( $_POST['page_id'] ) ? absint( wp_unslash( $_POST['page_id'] ) ) : null;
		$page_name        = isset( $_POST['page_name'] ) ? sanitize_text_field( wp_unslash( $_POST['page_name'] ) ) : null;
		$default_template = isset( $_POST['default_template'] ) ? sanitize_text_field( wp_unslash( $_POST['default_template'] ) ) : null;
		$edit_with        = isset( $_POST['template_edit_with'] ) ? sanitize_text_field( wp_unslash( $_POST['template_edit_with'] ) ) : null;
		$url              = '#';
		$editor_btn_text  = self::page_edit_btn_text( $edit_with );

		if ( ! Fns::verifyNonce() || ! $page_type ) {
			$return = [
				'success' => false,
				'post_id' => $page_id,
			];
			wp_send_json( $return );
		}

		$option_name = self::option_name( $page_type );
		$post_data   = [
			'ID'         => $page_id,
			'post_title' => $page_name,
			'meta_input' => [
				self::template_type_meta_key() => $page_type,
			],
		];
		// for gutenberg
		if ( 'elementor' == $edit_with ) {
			$post_data['meta_input']['_elementor_edit_mode'] = 'builder';
		} elseif ( 'gutenberg' == $edit_with ) {
			$post_data['meta_input']['_elementor_edit_mode'] = '';
		}

		if ( $page_id ) {
			$page_id  = wp_update_post( $post_data );
			$new_page = false;
		} else {
			unset( $post_data['ID'] );
			$post_data['post_type']   = self::$post_type_tb;
			$post_data['post_status'] = 'publish';
			$page_id                  = wp_insert_post( $post_data );
			$new_page                 = true;
			if ( 'elementor' == $edit_with ) {
				update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
			}
		}

		if ( $page_id ) {
			if ( 'default_template' === $default_template ) {
				update_option( $option_name, $page_id );
			}
			// else {
			// update_option( $option_name, '' );
			// }
			$url = add_query_arg(
				[
					'post'   => $page_id,
					'action' => $edit_with == 'elementor' ? 'elementor' : 'edit',
				],
				admin_url( 'post.php' )
			);
		}

		$return = [
			'success'         => true,
			'post_id'         => $page_id,
			'post_edit_url'   => $url,
			'editor_btn_text' => $editor_btn_text,
			'new_page'        => $new_page,
		];
		wp_send_json( $return );
		wp_die();
	}

	/**
	 * Elementor Create Templeate
	 *
	 * @return void
	 */
	public static function tpgp_el_default_template() {
		$page_type = isset( $_POST['template_type'] ) ? sanitize_text_field( wp_unslash( $_POST['template_type'] ) ) : null;
		$page_id   = isset( $_POST['page_id'] ) ? absint( wp_unslash( $_POST['page_id'] ) ) : null;

		if ( ! Fns::verifyNonce() || ! $page_type ) {
			$return = [
				'success'   => false,
				'post_id'   => $page_id,
				'page_type' => $page_type,
			];
			wp_send_json( $return );
		}
		$option_name = self::option_name( $page_type );
		update_option( $option_name, $page_id );

		$return = [
			'success'   => true,
			'post_id'   => $page_id,
			'page_type' => $page_type,
		];
		wp_send_json( $return );
		wp_die();
	}

	public static function rttpg_title_keyword_filter( $where, $wp_query ) {
		global $wpdb;
		$search_term = $wp_query->get( 'search_prod_title' );
		if ( $search_term ) {
			if ( strpos( trim( $search_term ), ' ' ) !== false ) {
				$search_term2 = explode( ' ', $search_term );
				foreach ( $search_term2 as $title ) {
					$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $title ) ) . '%\'';
				}
			} else {
				$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
			}
		}

		return $where;
	}
}
