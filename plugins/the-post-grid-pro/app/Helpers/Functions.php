<?php
/**
 * Functions Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Helpers;

use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'Functions' ) ) :
	/**
	 * Functions Class.
	 */
	class Functions {

		public static function getAllTpgTaxonomyObject( $pt = 'post' ) {
			$taxonomy_objects = get_object_taxonomies( $pt, 'objects' );
			$taxonomy_list    = [];

			if ( ! empty( $taxonomy_objects ) ) {
				foreach ( $taxonomy_objects as $taxonomy ) {
					if ( ! in_array( $taxonomy->name, [ 'language', 'post_translations' ] ) ) {
						$taxonomy_list[] = $taxonomy;
					}
				}
			}

			return $taxonomy_list;
		}

		static function tpg_template( $data, $tpg_dir, $block_type ) {
			$layout        = str_replace( '-2', '', $data['layout'] );
			$template_name = '/the-post-grid/' . $block_type . '/' . $layout . '.php';
			if ( file_exists( get_stylesheet_directory() . $template_name ) ) {
				$file = get_stylesheet_directory() . $template_name;
			} elseif ( file_exists( get_template_directory() . $template_name ) ) {
				$file = get_template_directory() . $template_name;
			} else {
				$file = rtrim( $tpg_dir, '/' ) . DIRECTORY_SEPARATOR . $layout . '.php';
			}

			include $file;
		}

		public static function get_cf_formatted_fields( $groups, $format = [], $post_id = null ) {
			$html = null;
			if ( ! empty( $groups ) ) {
				foreach ( $groups as $group_id ) {
					$plugin = Fns::is_acf();
					$fields = [];
					switch ( $plugin ) {
						case 'acf':
							$fields = acf_get_fields( $group_id );
							break;
					}

					if ( ! empty( $fields ) ) {
						$titleHtml = $returnHtml = null;

						$acf_count = 0;
						foreach ( $fields as $field ) {
							$acf_count++;
							$item  = $htmlValue = $htmlLabel = null;
							$value = get_field( $field['name'], $post_id );

							if ( empty( $format['hide_group_title'] ) ) {
								$title = get_the_title( $group_id );
								if ( ! empty( $format['hide_empty'] ) ) {
									if ( $value ) {
										$titleHtml = "<h4 class='tpg-cf-group-title'>{$title}</h4>";
									}
								} else {
									$titleHtml = "<h4 class='tpg-cf-group-title'>{$title}</h4>";
								}
							}

							if ( $value ) {
								switch ( $field['type'] ) {
									case 'image':
										$value = "<img src='{$value['sizes']['thumbnail']}' />";
										break;
									case 'select':
										if ( ! empty( $field['choices'] ) ) {
											if ( is_array( $value ) ) {
												$nValue = [];
												foreach ( $value as $v ) {
													$nValue[] = $field['choices'][ $v ];
												}
												$value = implode( ', ', $nValue );
											} else {
												$value = $field['choices'][ $value ];
											}
										}
										break;
									case 'checkbox':
										if ( ! empty( $field['choices'] ) ) {
											if ( is_array( $value ) ) {
												$nValue = [];
												foreach ( $value as $v ) {
													$nValue[] = $field['choices'][ $v ];
												}
												$value = implode( ', ', $nValue );
											} else {
												$value = $field['choices'][ $value ];
											}
										}
										break;
									case 'radio':
										if ( ! empty( $field['choices'] ) ) {
											$value = $field['choices'][ $value ];
										}
										break;
									case 'true_false':
										$value = $value ? 1 : 0;
										break;
									// case 'date_picker':
									// $value = $value;
									// $value = get_post_meta( $post_id, $field['name'], true );
									// $value = date('m/d/Y', strtotime($value));
									// $date = new \DateTime($value);
									// $date_format = get_option( 'date_format' );
									// $date_format = $date_format ? $date_format : 'j M Y';
									// $value       = $date->format( $date_format );
									// break;
									case 'color_picker':
										$value = "<div class='tpg-cf-color' style='height:25px;width:25px;background:{$value};'></div>";
										break;
									case 'url':
										$value = "<a href='{$value}'>{$value}</a>";
										break;
									case 'link':
										if ( isset( $field['return_format'] ) && $field['return_format'] == 'url' ) {
											$value = "<a class='tpg-acf-link' href='{$value}'>{$field['label']}</a>";
										} else {
											$target = ! empty( $value['target'] ) ? "target='{$value['target']}'" : '';
											$value  = "<a class='tpg-acf-link' " . $target . " href='{$value['url']}'>{$field['label']}</a>";
										}

										break;
									case 'file':
										$value = "<a href='{$value['url']}'>" . __(
											'Download',
											'the-post-grid-pro'
										) . " {$field['label']}</a>";
										break;
									default:
										break;
								}
							}

							if ( $field['type'] !== 'link' ) {
								$htmlLabel = "<span class='tgp-cf-field-label'>{$field['label']}</span>";
							}
							$htmlValue = "<div class='tgp-cf-field-value'>{$value}</div>";
							$item     .= "<div class='tpg-cf-fields tgp-cf-{$plugin}-{$field['type']}'>";
							if ( ! empty( $format['show_value'] ) ) {
								$item .= $htmlValue;
							} else {
								$item .= $htmlLabel;
								$item .= $htmlValue;
							}
							$item .= '</div>';
							if ( ! empty( $format['hide_empty'] ) ) {
								if ( $value ) {
									$returnHtml .= $item;
								}
							} else {
								$returnHtml .= $item;
							}
						}

						$html .= "<div class='tpg-cf-wrap'>{$titleHtml}{$returnHtml}</div>";
					}
				}
			}

			return $html;
		}

		public static function rtShare( $pid ) {
			if ( ! $pid ) {
				return;
			}
			$settings  = get_option( rtTPG()->options['settings'] );
			$ssList    = ! empty( $settings['social_share_items'] ) ? $settings['social_share_items'] : [];
			$permalink = get_the_permalink( $pid );
			$html      = null;

			if ( in_array( 'facebook', $ssList ) ) {
				$html .= "<a class='facebook' title='" . __(
					'Share on facebook',
					'the-post-grid-pro'
				)
						 . "' target='_blank' href='https://www.facebook.com/sharer/sharer.php?u={$permalink}'><i class='" . Fns::change_icon( 'fab fa-facebook-f', 'facebook' ) . "' aria-hidden='true'></i></a>";
			}
			if ( in_array( 'twitter', $ssList ) ) {
				$html .= "<a class='twitter' title='" . __( 'Share on twitter', 'the-post-grid-pro' )
						 . "' target='_blank' href='http://www.twitter.com/intent/tweet?url={$permalink}'><i class='" . Fns::change_icon( 'fab fa-twitter', 'twitter' ) . "' aria-hidden='true'></i></a>";
			}
			if ( in_array( 'linkedin', $ssList ) ) {
				$html .= "<a class='linkedin' title='" . __(
					'Share on linkedin',
					'the-post-grid-pro'
				)
						 . "' target='_blank' href='https://www.linkedin.com/shareArticle?mini=true&url={$permalink}'><i class='" . Fns::change_icon( 'fab fa-linkedin-in', 'linkedin' ) . "' aria-hidden='true'></i></a>";
			}
			if ( in_array( 'pinterest', $ssList ) ) {
				$html .= "<a class='pinterest' title='" . __(
					'Share on pinterest',
					'the-post-grid-pro'
				)
						 . "' target='_blank' href='https://pinterest.com/pin/create/button/?url={$permalink}'><i class='" . Fns::change_icon( 'fab fa-pinterest', 'pinterest' ) . "' aria-hidden='true'></i></a>";
			}
			if ( in_array( 'reddit', $ssList ) ) {
				$title = wp_strip_all_tags( get_the_title( $pid ) );
				$html .= "<a class='reddit' title='" . __(
					'Share on reddit',
					'the-post-grid-pro'
				)
						  . "' target='_blank' href='http://reddit.com/submit?url={$permalink}&amp;title={$title}'><i class='" . Fns::change_icon( 'fab fa-reddit-alien', 'reddit' ) . "' aria-hidden='true'></i></a>";
			}

			if ( in_array( 'email', $ssList ) ) {
				$title   = wp_strip_all_tags( get_the_title( $pid ) );
				$excerpt = wp_strip_all_tags( get_the_excerpt( $pid ) );
				$excerpt = $excerpt . "\r - " . $permalink;
				$html   .= sprintf(
					'<a class="email" title="%s" href="mailto:?subject=%s&body=%s"><i class="' . Fns::change_icon( 'fa fa-envelope', 'email' ) . '"></i></a>',
					__( 'Share on Email', 'the-post-grid-pro' ),
					$title,
					$excerpt
				);
			}

			if ( $html ) {
				$html = "<div class='rt-tpg-social-share'>{$html}</div>";
			}

			return $html;
		}

		public static function rtProductGalleryImages() {
			$gallery = null;
			global $post, $product;
			$thumb_id       = get_post_thumbnail_id( $post->ID );
			$attachment_ids = $product->get_gallery_image_ids();
			if ( $thumb_id ) {
				array_unshift( $attachment_ids, $thumb_id );
			}

			$total_attachment_ids = count( $attachment_ids );

			$swiper_container = $swiper_wrapper = $swiper_pagination = '';
			if ( $total_attachment_ids > 1 ) {
				$swiper_container  = 'swiper-container';
				$swiper_wrapper    = 'swiper-wrapper';
				$swiper_pagination = "<div class='swiper-pagination'></div>";
			}

			if ( ! empty( $attachment_ids ) ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$full_size_image = wp_get_attachment_image_src( $attachment_id, 'full' );
					$thumbnail       = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
					$thumbnail_post  = get_post( $attachment_id );
					$image_title     = $thumbnail_post->post_content;

					$attributes = [
						'title'                   => $image_title,
						'data-src'                => $full_size_image[0],
						'data-large_image'        => $full_size_image[0],
						'data-large_image_width'  => $full_size_image[1],
						'data-large_image_height' => $full_size_image[2],
					];

					$html  = '<div data-thumb="' . esc_url( $thumbnail[0] ) . '" class="rt-product-img swiper-slide"><a href="' . esc_url( $full_size_image[0] ) . '">';
					$html .= wp_get_attachment_image( $attachment_id, 'shop_single', false, $attributes );
					$html .= '</a></div>';

					$gallery .= apply_filters(
						'woocommerce_single_product_image_thumbnail_html',
						$html,
						$attachment_id
					);
				}
			}
			$galleryClass = 'hasImg';
			if ( ! $gallery ) {
				$galleryClass = 'haNoImg';
				$gallery      = '<div class="rt-product-img-placeholder">';
				$gallery     .= sprintf(
					'<img src="%s" alt="%s" class="wp-post-image" />',
					esc_url( wc_placeholder_img_src() ),
					esc_html__( 'Awaiting product image', 'the-post-grid-pro' )
				);
				$gallery     .= '</div>';
			}
			$galleryClass .= ' ' . $swiper_container;

			// $a = explode(',', $attachment_ids);
			$gallery = "<div id='rt-product-gallery' class='$galleryClass'><div class='$swiper_wrapper'>{$gallery}</div>{$swiper_pagination}</div>";

			return $gallery;
		}

		public static function woocommerce_get_product_schema() {
			global $product;

			$schema = 'Product';

			// Downloadable product schema handling.
			if ( $product->is_downloadable() ) {
				switch ( $product->download_type ) {
					case 'application':
						$schema = 'SoftwareApplication';
						break;
					case 'music':
						$schema = 'MusicAlbum';
						break;
					default:
						$schema = 'Product';
						break;
				}
			}

			return 'http://schema.org/' . $schema;
		}

		public static function getAllUserRoles() {
			global $wp_roles;
			$roles = [];

			if ( ! empty( $wp_roles->roles ) ) {
				foreach ( $wp_roles->roles as $roleID => $role ) {
					$roles[ $roleID ] = $role['name'];
				}
			}

			return $roles;
		}

		public static function array_insert( &$array, $position, $insert_array ) {
			$first_array = array_splice( $array, 0, $position + 1 );
			$array       = array_merge( $first_array, $insert_array, $array );
		}

		/**
		 * @param         $viewName
		 * @param array    $args
		 * @param bool     $return
		 *
		 * @return string|void|\WP_Error
		 */
		public static function view( $viewName, $args = [], $return = false ) {
			$file     = str_replace( '.', '/', $viewName );
			$file     = ltrim( $file, '/' );
			$viewFile = trailingslashit( RT_THE_POST_GRID_PRO_PLUGIN_PATH . '/resources' ) . $file . '.php';

			if ( ! file_exists( $viewFile ) ) {
				return new \WP_Error( 'brock', __( "$viewFile file not found" ) ); //phpcs:ignore WordPress.WP.I18n.InterpolatedVariableText
			}

			if ( $args ) {
				extract( $args );
			}

			if ( $return ) {
				ob_start();
				include $viewFile;

				return ob_get_clean();
			}

			include $viewFile;
		}

		/**
		 * Dummy comment form
		 *
		 * @return false|string
		 */
		public static function get_dummy_comment_box() {
			ob_start(); ?>
			<form id="comments" method="post" class="comment-form">
				<h4 id="reply-title" class="comment-reply-title"><?php esc_html_e( 'Leave a Reply', 'the-post-grid' ); ?></h4>
				<input type="text" style="padding: 8px 15px;border: 1px solid #e9e9e9;width:100%;margin-bottom:10px" name="author" value="" placeholder="Your Name *" required="" class="form-control">
				<input id="email" style="padding: 8px 15px;border: 1px solid #e9e9e9;width:100%;margin-bottom:10px" name="email" type="email" value="" placeholder="Your Email *" required="" class="form-control">
				<textarea style="padding: 15px;margin-bottom:15px;height:130px;border: 1px solid #e9e9e9; width: 100%;" id="comment" name="comment" required="" placeholder="<?php esc_html_e( 'This is dummy comment form. The actual form will come from your theme on the details page.', 'the-post-grid' ); ?>" class="form-control" rows="10" cols="40" spellcheck="false"></textarea>
				<input style="padding:15px 40px; border: none;background-color:#000;color:#fff" name="submit" type="submit" id="submit" class="submit btn-send ghost-on-hover-btn" value="<?php echo esc_attr__( 'Post Comment', 'the-post-grid' ); ?>">
			</form>
			<?php
			return ob_get_clean();
		}
	}

endif;
