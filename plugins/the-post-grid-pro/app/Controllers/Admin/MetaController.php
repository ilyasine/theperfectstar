<?php
/**
 * Meta Controller Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers\Admin;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Meta Controller Class.
 */
class MetaController {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		// Category color and thumbnail:
		add_action( 'category_add_form_fields', [ $this, 'colorpicker_field_add_new_category' ] );
		add_action( 'category_edit_form_fields', [ $this, 'colorpicker_field_edit_category' ] );
		add_action( 'created_category', [ $this, 'save_termmeta' ] );
		add_action( 'edited_category', [ $this, 'save_termmeta' ], 10, 2 );
		add_filter( 'manage_edit-category_columns', [ $this, 'edit_term_columns' ], 10, 3 );
		add_filter( 'manage_category_custom_column', [ $this, 'manage_term_custom_column' ], 10, 3 );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_category_admin_script' ] );
		add_action( 'admin_footer', [ $this, 'add_script' ] );
	}


	/**
	 * Category Color Added
	 *
	 * @param $taxonomy
	 *
	 * @return void
	 */
	public function colorpicker_field_add_new_category( $taxonomy ) {
		?>
		<div class="form-field term-colorpicker-wrap">
			<label for="term-colorpicker"><?php esc_html_e( 'Category Color - The Post Grid', 'the-post-grid-pro' ); ?></label>
			<input name="<?php echo esc_attr( rtTpgPro()->category_meta_key ); ?>" value="" class="rt-color"
				   id="<?php echo esc_attr( rtTpgPro()->category_meta_key ); ?>"/>
			<p><?php esc_html_e( 'Please add category color for The Post Grid plugins\'s Layout', 'the-post-grid-pro' ); ?></p>
		</div>

		<div class="form-field term-group">
			<label for="<?php echo esc_attr( rtTpgPro()->category_thumb_meta_key ); ?>"><?php esc_html_e( 'Upload and Image', 'the-post-grid-pro' ); ?></label>
			<div class="category-image"></div>
			<input type="button" id="upload_image_btn" class="button" value="<?php esc_html_e( 'Upload an Image', 'the-post-grid-pro' ); ?>"/>
		</div>
		<?php
	}

	/**
	 * Edit category table
	 *
	 * @param $term
	 *
	 * @return void
	 */
	public function colorpicker_field_edit_category( $term ) {
		$color = get_term_meta( $term->term_id, rtTpgPro()->category_meta_key, true );
		$color = ( ! empty( $color ) ) ? "#{$color}" : '';
		$image = get_term_meta( $term->term_id, rtTpgPro()->category_thumb_meta_key, true );
		?>
		<tr class="form-field term-colorpicker-wrap">
			<th scope="row"><label
						for="term-colorpicker"><?php esc_html_e( 'Category Color - The Post Grid', 'the-post-grid-pro' ); ?></label>
			</th>
			<td>
				<input name="<?php echo esc_attr( rtTpgPro()->category_meta_key ); ?>"
					   value="<?php echo esc_attr( $color ); ?>" class="rt-color"
					   id="<?php echo esc_attr( rtTpgPro()->category_meta_key ); ?>"/>
				<p class="description"><?php esc_html_e( 'Please add category color for The Post Grid plugins\'s Layout', 'the-post-grid-pro' ); ?></p>
			</td>
		</tr>

		<tr class="form-field term-image-wrap">
			<th scope="row"><label for="<?php echo esc_attr( rtTpgPro()->category_thumb_meta_key ); ?>"><?php esc_html_e( 'Category Image', 'the-post-grid-pro' ); ?></label></th>
			<td>
				<div class="category-image">
					<?php if ( $image ) { ?>
						<div class="category-image-wrap">
							<img src='<?php echo esc_url( wp_get_attachment_image_src( $image )[0] ); ?>' width='200' alt="<?php esc_attr_e( 'Category Image', 'the-post-grid-pro' ); ?>"/>
							<input type="hidden" name="<?php echo esc_attr( rtTpgPro()->category_thumb_meta_key ); ?>" value="<?php echo esc_attr( $image ); ?>" class="category-image-id"/>
							<button><i class="dashicons dashicons-no-alt"></i></button>
						</div>
					<?php } ?>
				</div>

				<input type="button" id="upload_image_btn" class="button" value="<?php esc_html_e( 'Upload an Image', 'the-post-grid-pro' ); ?>"/>
			</td>
		</tr>

		<?php
	}

	/**
	 * Save Category
	 *
	 * @param $term_id
	 *
	 * @return void
	 */
	public function save_termmeta( $term_id ) {
		// Save term color if possible.
        //phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ rtTpgPro()->category_meta_key ] ) && ! empty( $_POST[ rtTpgPro()->category_meta_key ] ) ) {
			update_term_meta( $term_id, rtTpgPro()->category_meta_key, sanitize_hex_color_no_hash( wp_unslash( $_POST[ rtTpgPro()->category_meta_key ] ) ) );
		} else {
			delete_term_meta( $term_id, rtTpgPro()->category_meta_key );
		}

		if ( isset( $_POST[ rtTpgPro()->category_thumb_meta_key ] ) && ! empty( $_POST[ rtTpgPro()->category_thumb_meta_key ] ) ) {
			update_term_meta( $term_id, rtTpgPro()->category_thumb_meta_key, absint( $_POST[ rtTpgPro()->category_thumb_meta_key ] ) );
		} else {
			delete_term_meta( $term_id, rtTpgPro()->category_thumb_meta_key );
		}
		//phpcs:enable
	}


	/**
	 * Add Category Column
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function edit_term_columns( $columns ) {
		$columns[ rtTpgPro()->category_meta_key ]       = esc_html__( 'Color', 'the-post-grid-pro' );
		$columns[ rtTpgPro()->category_thumb_meta_key ] = esc_html__( 'Thumb', 'the-post-grid-pro' );

		return $columns;
	}

	/**
	 * @param $out
	 * @param $column
	 * @param $term_id
	 *
	 * @return mixed|string
	 */
	public function manage_term_custom_column( $out, $column, $term_id ) {
		if ( rtTpgPro()->category_meta_key === $column ) {
			$value = get_term_meta( $term_id, rtTpgPro()->category_meta_key, true );
			if ( ! $value ) {
				$value = '';
			}
			$out = sprintf( '<span title="' . esc_attr( 'The Post Grid Category Color' ) . '" class="term-meta-color-block" style="background:#%s;width:30px;height:30px;display: block;border-radius:100px;" ></span>', esc_attr( $value ) );
		}

		if ( rtTpgPro()->category_thumb_meta_key === $column ) {
			$value = get_term_meta( $term_id, rtTpgPro()->category_thumb_meta_key, true );
			if ( $value ) {
				$out = '<img style="width:50px;height:50px" src=' . wp_get_attachment_image_src( $value )[0] . ' width="200" />';
			}
		}

		return $out;
	}

	/**
	 * Enqueue scripts in admin panel
	 *
	 * @return void
	 */
	public function load_category_admin_script() {
		global $pagenow, $typenow;

		if ( ! in_array( $pagenow, [ 'term.php', 'edit-tags.php' ] ) ) {
			return;
		}

		wp_enqueue_media();

		$color_js_path = rtTpgPro()->get_assets_uri( 'js/rttpg-color-picker.js' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script(
			'rttpg-color-picker',
			$color_js_path,
			[
				'jquery',
				'wp-color-picker',
			],
			rtTPG()->options['version'],
			true
		);
		?>

		<?php
	}

	/*
	 * Add script
	 * @since 1.0.0
	 */
	public function add_script() {
		global $pagenow, $typenow;

		if ( ! in_array( $pagenow, [ 'term.php', 'edit-tags.php' ] ) ) {
			return;
		}
		?>
		<style>
			.category-image-wrap {
				position: relative;
			}
			.category-image-wrap button {
				position: absolute;
				left: 10px;
				top: 10px;
				border: none;
				background: #f00;
				color: #fff;
				z-index: 99;
				cursor: pointer;
				height: 35px;
				line-height: 16px;
				border-radius: 50%;
				width: 35px;
				display: flex;
				justify-content: center;
				align-items: center;
			}
		</style>
		<script>
			//category image upload
			var meta_image_frame;
			jQuery('#upload_image_btn').click(function(e){
				e.preventDefault();
				if ( meta_image_frame ) {
					meta_image_frame.open();
					return;
				}

				// Sets up the media library frame
				meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
					title: 'Upload Category Image',
					button: { text: 'Upload Image' },
					library: { type: 'image' }
				});

				meta_image_frame.on('select', function(){
					var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
					jQuery('.category-image').html(`<div class='category-image-wrap'><img src='${media_attachment.url}' width='200' /><input type="hidden" name="<?php echo esc_attr( rtTpgPro()->category_thumb_meta_key ); ?>" value='${media_attachment.id}' class="category-image-id"/><button>x</button></div>`);
				});

				meta_image_frame.open();
			});

			jQuery(document).on("click",".category-image-wrap button",function() {
				jQuery(this).parent().remove();
			});
		</script>
		<?php
	}

	/**
	 * Admin scripts
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		global $pagenow, $typenow;

		// validate page.
		if ( ! in_array( $pagenow, [ 'post.php', 'post-new.php' ], true ) ) {
			return;
		}

		if ( rtTPG()->post_type !== $typenow ) {
			return;
		}

		// scripts.
		wp_enqueue_script( [ 'rt-pagination', 'rt-jzoom', 'rt-scrollbar', 'swiper', 'rt-magnific-popup' ] );

		// styles.
		wp_enqueue_style( [ 'swiper', 'rt-magnific-popup', 'rt-tpg-pro-admin-preview' ] );
	}
}
