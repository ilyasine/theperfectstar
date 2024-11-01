<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_SETPOSTMETA
 *
 * @package Uncanny_Automator_Pro
 */
class WP_SETPOSTMETA {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WP';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'SETPOSTMETA';
		$this->action_meta = 'WPPOSTMETAID';
		if ( Automator()->helpers->recipe->is_edit_page() ) {
			add_action(
				'wp_loaded',
				function () {
					$this->define_action();
				},
				99
			);

			return;
		}
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		$action = array(
			'author'             => Automator()->get_author_name( $this->action_code ),
			'support_link'       => Automator()->get_author_support_link( $this->action_code, 'integration/wordpress-core/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			'requires_user'      => false,
			/* translators: Action - WordPress Core */
			'sentence'           => sprintf( __( 'Set {{post meta:%1$s}}', 'uncanny-automator-pro' ), $this->action_code ),
			/* translators: Action - WordPress Core */
			'select_option_name' => __( 'Set {{post meta}}', 'uncanny-automator-pro' ),
			'priority'           => 11,
			'accepted_args'      => 3,
			'execution_function' => array( $this, 'set_post_meta' ),
			'options_callback'   => array( $this, 'load_options' ),
		);

		Automator()->register->action( $action );
	}

	/**
	 * load_options
	 *
	 * @return void
	 */
	public function load_options() {

		$custom_post_types = Automator()->helpers->recipe->wp->options->all_post_types(
			__( 'Post type', 'uncanny-automator-pro' ),
			'WPSPOSTTYPES',
			array(
				'token'        => false,
				'is_ajax'      => true,
				'target_field' => $this->action_meta,
				'is_any'       => false,
				'endpoint'     => 'select_all_post_of_selected_post_type',
			)
		);
		// now get regular post types.
		$args = array(
			'public'   => true,
			'_builtin' => true,
		);

		$output     = 'object';
		$operator   = 'and';
		$options    = array();
		$post_types = get_post_types( $args, $output, $operator );
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				$options[ $post_type->name ] = esc_html( $post_type->label );
			}
		}
		$options                      = array_merge( $options, $custom_post_types['options'] );
		$custom_post_types['options'] = $options;

		$options = Automator()->utilities->keep_order_of_options(
			array(
				'options_group' => array(
					$this->action_code => array(
						$custom_post_types,

						Automator()->helpers->recipe->field->select_field( $this->action_meta, __( 'Post', 'uncanny-automator-pro' ) ),

						array(
							'input_type'        => 'repeater',
							'relevant_tokens'   => array(),
							'option_code'       => 'SPMETA_PAIRS',
							'label'             => __( 'Meta', 'uncanny-automator-pro' ),
							'required'          => true,
							'fields'            => array(
								array(
									'input_type'      => 'text',
									'option_code'     => 'KEY',
									'label'           => __( 'Key', 'uncanny-automator-pro' ),
									'supports_tokens' => true,
									'required'        => true,
								),
								array(
									'input_type'      => 'text',
									'option_code'     => 'VALUE',
									'label'           => __( 'Value', 'uncanny-automator-pro' ),
									'supports_tokens' => true,
									'required'        => true,
								),
							),
							'add_row_button'    => __( 'Add pair', 'uncanny-automator-pro' ),
							'remove_row_button' => __( 'Remove pair', 'uncanny-automator-pro' ),
						),
					),
				),
			)
		);

		return $options;
	}

	/**
	 * Validation function when the action is hit
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 */
	public function set_post_meta( $user_id, $action_data, $recipe_id, $args ) {

		$post_id = $action_data['meta'][ $this->action_meta ];

		$meta_pairs = json_decode( $action_data['meta']['SPMETA_PAIRS'], true );

		if ( ! empty( $meta_pairs ) ) {

			foreach ( $meta_pairs as $pair ) {

				$meta_key   = $this->sanitize_text_field( Automator()->parse->text( $pair['KEY'], $recipe_id, $user_id, $args ) );
				$meta_value = $this->sanitize_text_field( Automator()->parse->text( $pair['VALUE'], $recipe_id, $user_id, $args ) );

				// If data is already serialized, unserialize it.
				if ( is_serialized( $meta_value, true ) ) {
					// Once we have the array back, assign it as meta value so it will be stored as a serialize string.
					// This will fix double serialization by WordPress.
					$meta_value = maybe_unserialize( $meta_value );
				}

				update_post_meta( $post_id, $meta_key, $meta_value );

			}
		}

		Automator()->complete_action( $user_id, $action_data, $recipe_id );
	}

	/**
	 * Wrapper method for WordPress' built-in function called sanitize_text_field.
	 *
	 * Added a filter to disable sanitation.
	 *
	 * @param mixed $value
	 *
	 * @return mixed The sanitized value.
	 */
	private function sanitize_text_field( $value = '' ) {

		// Only sanitize if its a string.
		if ( ! is_string( $value ) ) {
			return $value;
		}

		if ( apply_filters( 'automator_wp_set_post_meta_should_sanitize_fields', true, $value, $this ) ) {
			return sanitize_text_field( $value );
		}

		return $value;

	}

}
