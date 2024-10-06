<?php
/**
 * TemplateBuilderFrontend Class for Elementor builder
 *
 * TemplateBuilderFrontend Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers\Builder;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Traits\ELTempleateBuilderTraits;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * TemplateBuilderFrontend Class
 */
class TemplateBuilderFrontend {

	/**
	 * Template builder related traits.
	 */
	use ELTempleateBuilderTraits;

	/**
	 * Check Single Page ID
	 *
	 * @var string
	 */
	public static $single_page_builder_id = false;


	/**
	 * Check Archive Page ID
	 *
	 * @var string
	 */
	public static $archive_builder_id = false;

	/**
	 * Check Author Archive Page ID
	 *
	 * @var bool
	 */

	public static $author_archive_builder_id = false;

	/**
	 * Check Date Archive Page ID
	 *
	 * @var bool
	 */

	public static $date_archive_builder_id = false;

	/**
	 * Check Date Archive Page ID
	 *
	 * @var bool
	 */

	public static $search_archive_builder_id = false;

	/**
	 * Check Date category Page ID
	 *
	 * @var bool
	 */

	public static $category_archive_builder_id = false;

	/**
	 * Check Date tags Page ID
	 *
	 * @var bool
	 */

	public static $tags_archive_builder_id = false;


	/**
	 * Initialize function.
	 *
	 * @return void
	 */
	public static function init() {
		self::$archive_builder_id          = self::builder_page_id( 'archive' );
		self::$single_page_builder_id      = self::builder_page_id( 'single' );
		self::$author_archive_builder_id   = self::builder_page_id( 'author-archive' );
		self::$date_archive_builder_id     = self::builder_page_id( 'date-archive' );
		self::$search_archive_builder_id   = self::builder_page_id( 'search-archive' );
		self::$category_archive_builder_id = self::builder_page_id( 'category-archive' );
		self::$tags_archive_builder_id     = self::builder_page_id( 'tags-archive' );

		add_filter( 'template_include', [ __CLASS__, 'el_template_loader_default_file' ], 90 );
		add_action( 'template_redirect', [ __CLASS__, 'frontend_init' ], 99 );

	}

	/**
	 * Template Overider
	 *
	 * @param string $default_file file name.
	 *
	 * @return string
	 */
	public static function el_template_loader_default_file( $default_file ) {
		global $wp_query;
		$builder_file = '';
		$plugin_path  = RT_THE_POST_GRID_PRO_PLUGIN_PATH . '/templates/template-builder/';

		if ( self::$single_page_builder_id && is_singular( self::$tpg_post_types ) ) {
			add_filter( 'tpg_page_id_for_block_css', function () {
				return self::$single_page_builder_id;
			} );
			$builder_file = 'single-listing-fullwidth.php';
		} elseif ( self::$archive_builder_id && isset( $wp_query ) && (bool) $wp_query->is_posts_page ) {
			add_filter( 'tpg_page_id_for_block_css', function () {
				return self::$archive_builder_id;
			} );
			$builder_file = 'archive-listing-fullwidth.php';
		} elseif ( self::$category_archive_builder_id && is_category() ) {
			add_filter( 'tpg_page_id_for_block_css', function () {
				return self::$category_archive_builder_id;
			} );
			$builder_file = 'archive-listing-fullwidth.php';
		} elseif ( self::$author_archive_builder_id && is_author() ) {
			add_filter( 'tpg_page_id_for_block_css', function () {
				return self::$author_archive_builder_id;
			} );
			$builder_file = 'archive-listing-fullwidth.php';
		} elseif ( self::$tags_archive_builder_id && is_tag() ) {
			add_filter( 'tpg_page_id_for_block_css', function () {
				return self::$tags_archive_builder_id;
			} );
			$builder_file = 'archive-listing-fullwidth.php';
		} elseif ( self::$search_archive_builder_id && is_search() ) {
			add_filter( 'tpg_page_id_for_block_css', function () {
				return self::$search_archive_builder_id;
			} );
			$builder_file = 'archive-listing-fullwidth.php';
		} elseif ( self::$date_archive_builder_id && is_date() ) {
			add_filter( 'tpg_page_id_for_block_css', function () {
				return self::$date_archive_builder_id;
			} );
			$builder_file = 'archive-listing-fullwidth.php';
		}

		if ( $builder_file ) {
			$default_file = $plugin_path . $builder_file;
		}

		return $default_file;
	}

	/**
	 * Display content.
	 *
	 * @return void
	 */
	public static function frontend_init() {
		add_action( 'el_builder_template_content', [ __CLASS__, 'display_template_content' ] );
	}

	public static function display_template_content() {
		$builder_id = false;
		global $wp_query;

		if ( self::is_builder_page_single() ) {
			$builder_id = self::$single_page_builder_id;
		} elseif ( isset( $wp_query ) && (bool) $wp_query->is_posts_page ) {
			$builder_id = self::$archive_builder_id;
		} elseif ( self::$category_archive_builder_id && is_category() ) {
			$builder_id = self::$category_archive_builder_id;
		} elseif ( self::$author_archive_builder_id && is_author() ) {
			$builder_id = self::$author_archive_builder_id;
		} elseif ( self::$search_archive_builder_id && is_search() ) {
			$builder_id = self::$search_archive_builder_id;
		} elseif ( self::$tags_archive_builder_id && is_tag() ) {
			$builder_id = self::$tags_archive_builder_id;
		} elseif ( self::$date_archive_builder_id && is_date() ) {
			$builder_id = self::$date_archive_builder_id;
		}

		if ( $builder_id ) {
			Fns::print_html( self::get_builder_content( $builder_id ), true );
		}
	}

}
