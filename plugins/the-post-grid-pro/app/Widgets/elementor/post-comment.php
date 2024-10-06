<?php
/**
 * Elementor: Post Comment Widget.
 *
 * @package RT_TPG_PRO
 */

use Elementor\Controls_Manager;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor: Post Comment Widget.
 */
class TPGPostComment extends Custom_Widget_Base {

	/**
	 * GridLayout constructor.
	 *
	 * @param  array $data
	 * @param  null  $args
	 *
	 * @throws \Exception
	 */


	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->tpg_name     = esc_html__( 'TPG - Comment Box', 'the-post-grid-pro' );
		$this->tpg_base     = 'tpg-single-comment-box';
		$this->tpg_icon     = 'eicon-comments tpg-grid-icon'; // .tpg-grid-icon class for just style
		$this->tpg_category = $this->tpg_archive_category;
	}


	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );
			array_push( $style, 'rt-tpg-block' );
		}

		return $style;
	}


	protected function register_controls() {
		$this->start_controls_section(
			'tpg_comment_box',
			[
				'label' => esc_html__( 'TPG Comment Box', 'the-post-grid-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'comment_spacing',
			[
				'label'              => esc_html__( 'Comment Spacing', 'the-post-grid-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-single-comment-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() { ?>
		<div class="tpg-single-comment-box">
			<?php
			if ( is_singular( 'post' ) ) {
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			} else {
				?>
				<div id="comments" class="comments-area single-blog-bottom">
					<div>
						<?php
						$rttpg_commenter = wp_get_current_commenter();
						$rttpg_req       = get_option( 'require_name_email' );
						$rttpg_aria_req  = ( $rttpg_req ? ' required' : '' );

						$rttpg_fields = [
							'author' =>
								'<div class="row"><div class="col-sm-6"><div class="form-group comment-form-author"><input type="text" id="author" name="author" value="'
								. esc_attr( $rttpg_commenter['comment_author'] ) . '" placeholder="' . esc_attr__( 'Name', 'the-post-grid-pro' ) . ( $rttpg_req ? ' *' : '' )
								. '" class="form-control"' . $rttpg_aria_req . '></div></div>',

							'email'  =>
								'<div class="col-sm-6 comment-form-email"><div class="form-group"><input id="email" name="email" type="email" value="'
								. esc_attr( $rttpg_commenter['comment_author_email'] ) . '" class="form-control" placeholder="' . esc_attr__( 'Email', 'the-post-grid-pro' ) . ( $rttpg_req
									? ' *' : '' ) . '"' . $rttpg_aria_req . '></div></div></div>',
						];

						$rttpg_args = [
							'class_submit'       => 'submit btn-send ghost-on-hover-btn',
							'submit_field'       => '<div class="form-group form-submit">%1$s %2$s</div>',
							'comment_field'      => '<div class="form-group comment-form-comment"><textarea id="comment" name="comment" required placeholder="'
													. esc_attr__( 'Comment *', 'the-post-grid-pro' ) . '" class="textarea form-control" rows="10" cols="40"></textarea></div>',
							'title_reply_before' => '<h4 id="reply-title" class="comment-reply-title">',
							'title_reply_after'  => '</h4>',
							'fields'             => apply_filters( 'comment_form_default_fields', $rttpg_fields ),
						];

						?>
						<?php comment_form( $rttpg_args, $this->last_post_id ); ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}

}
