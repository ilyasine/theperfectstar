<?php

namespace Uncanny_Automator_Pro\Integrations\Wp_Discuz;

/**
 * Class WP_DISCUZ_ANON_SUBMITS_COMMENT
 *
 * @package Uncanny_Automator
 */
class WP_DISCUZ_ANON_SUBMITS_COMMENT extends \Uncanny_Automator\Recipe\Trigger {

	protected $helpers;

	/**
	 * @return mixed|void
	 */
	protected function setup_trigger() {
		$this->helpers = array_shift( $this->dependencies );
		$this->set_integration( 'WPDISCUZ' );
		$this->set_trigger_code( 'WPD_ANON_SUBMITS_COMMENT' );
		$this->set_trigger_meta( 'WPD_POST' );
		$this->set_is_pro( true );
		$this->set_trigger_type( 'anonymous' );
		$this->set_sentence( sprintf( esc_attr_x( "A guest comment is submitted on a user's {{post:%1\$s}}", 'wpDiscuz', 'uncanny-automator-pro' ), $this->get_trigger_meta() ) );
		$this->set_readable_sentence( esc_attr_x( "A guest comment is submitted on a user's {{post}}", 'wpDiscuz', 'uncanny-automator-pro' ) );
		$this->add_action( 'comment_post', 10, 3 );
	}

	/**
	 * @return array
	 */
	public function options() {
		return array(
			array(
				'input_type'      => 'select',
				'option_code'     => 'WPD_POST_TYPES',
				'label'           => _x( 'Post type', 'wpDiscuz', 'uncanny-automator-pro' ),
				'required'        => true,
				'options'         => $this->helpers->get_all_post_types_options(),
				'is_ajax'         => true,
				'fill_values_in'  => $this->get_trigger_meta(),
				'endpoint'        => 'get_all_posts_by_post_type',
				'relevant_tokens' => array(),
			),
			array(
				'input_type'      => 'select',
				'option_code'     => $this->get_trigger_meta(),
				'label'           => _x( 'Post', 'wpDiscuz', 'uncanny-automator-pro' ),
				'required'        => true,
				'options'         => array(),
				'relevant_tokens' => array(),
			),
		);
	}

	/**
	 * @return bool
	 */
	public function validate( $trigger, $hook_args ) {
		list( $comment_id, $comment_approved, $commentdata ) = $hook_args;

		if ( 0 !== $commentdata['user_id'] ) {
			return false;
		}

		if ( isset( $commentdata['posted_by_automator'] ) ) {
			return false;
		}

		if ( ! isset( $trigger['meta'][ $this->get_trigger_meta() ] ) ) {
			return false;
		}

		$selected_post_id = $trigger['meta'][ $this->get_trigger_meta() ];

		return ( intval( '-1' ) === intval( $selected_post_id ) ) || ( absint( $selected_post_id ) === absint( $commentdata['comment_post_ID'] ) );
	}

	/**
	 * define_tokens
	 *
	 * @param mixed $tokens
	 * @param mixed $trigger - options selected in the current recipe/trigger
	 *
	 * @return array
	 */
	public function define_tokens( $trigger, $tokens ) {
		$common_tokens = $this->helpers->wpDiscuz_common_tokens();

		return array_merge( $tokens, $common_tokens );
	}

	/**
	 * hydrate_tokens
	 *
	 * @param $trigger
	 * @param $hook_args
	 *
	 * @return array
	 */
	public function hydrate_tokens( $trigger, $hook_args ) {
		list( $comment_id, $comment_approved, $commentdata ) = $hook_args;
		$author_id                                           = get_post_field( 'post_author', $commentdata['comment_post_ID'] );

		return $this->helpers->parse_common_token_values( $commentdata['comment_post_ID'], $comment_id, $author_id );

	}
}
