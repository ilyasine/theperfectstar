<?php
/**
 * Traits Elementor builder
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Traits;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Traits Elementor builder
 */
trait ELTempleateBuilderTraits {

	/**
	 * Elementor Templeate builder post type
	 *
	 * @var string
	 */
	public static $post_type_tb = 'tpg_builder';
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static $template_meta = 'tpg_tb_template';
	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static $tpg_post_types = 'post';

	/**
	 * Listing page id
	 *
	 * @var string
	 */
	public static function builder_page_id( $type ) {
		return get_option( self::option_name( $type ) );
	}

	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function option_name( $type ) {
		return self::$template_meta . '_default_' . $type;  // @id = tpg_tb_template_default_archive | author_archive]
	}

	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function builder_type( $post_id ) {
		return get_post_meta( $post_id, self::template_type_meta_key(), true ); // Meta key = tpg_tb_template_type
	}

	/**
	 * Elementor check archive builder is active or not
	 *
	 * @var string
	 */
	public static function is_builder_page_archive() {

		if ( in_array( self::builder_type( get_the_ID() ), [ 'archive', 'author-archive', 'search-archive', 'date-archive', 'category-archive', 'tags-archive' ] )
		     || ( self::builder_page_id( self::builder_type( get_the_ID() ) ) && ( is_post_type_archive( self::$tpg_post_types ) ) )
		     || is_tax( get_object_taxonomies( self::$tpg_post_types ) )
		     || is_tag() || is_category() || is_author() || is_date() || is_search()
		     || ( ! is_front_page() && is_home() )
		) {
			return true;
		}

		return false;
	}


	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function is_builder_page_single() {
		if ( 'single' == self::builder_type( get_the_ID() ) || ( self::builder_page_id( 'single' ) && is_singular( self::$tpg_post_types ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Elementor Templeate builder
	 *
	 * @var string
	 */
	public static function template_type_meta_key() {
		return self::$template_meta . '_type';
	}

	/**
	 * Get builder content function
	 *
	 * @param [type]  $template_id builder Template id.
	 * @param boolean $with_css with css.
	 *
	 * @return mixed
	 */
	public static function get_builder_content( $template_id, $with_css = false ) {
		$page_edit_with = self::page_edit_with( $template_id );
		if ( 'gutenberg' === $page_edit_with ) {
			return self::gutenberg_template_main_content( $template_id );
		} else {
			return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, $with_css );
		}
	}

	public static function gutenberg_template_main_content( $builder_id ) {
		if ( ! $builder_id ) {
			return;
		}
		$output  = '';
		$content = get_the_content( null, false, $builder_id );
		if ( has_blocks( $content ) ) {
			$blocks = parse_blocks( $content );
			foreach ( $blocks as $block ) {
				$output .= render_block( $block );
			}
		} else {
			$content = apply_filters( 'the_content', $content );
			$output  = str_replace( ']]>', ']]&gt;', $content );
		}

		return $output;

	}

	public static function page_edit_with( $post_id ) {
		if ( ! $post_id ) {
			return '';
		}

		$edit_with = get_post_meta( $post_id, '_elementor_edit_mode', true );

		if ( 'builder' === $edit_with ) {
			$edit_by = 'elementor';
		} else {
			$edit_by = 'gutenberg';
		}

		return $edit_by;
	}

	public static function page_edit_btn_text( $btn_text = '' ) {
		return $btn_text == 'elementor' ? 'Edit with elementor' : 'Edit with gutenberg';
	}

}
