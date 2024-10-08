<?php

namespace Uncanny_Automator;

use Uncanny_Automator_Pro\Mailpoet_Pro_Helpers;

/**
 * Class Mailpoet_Helpers
 *
 * @package Uncanny_Automator
 */
class Mailpoet_Helpers {

	/**
	 * @var Mailpoet_Helpers
	 */
	public $options;

	/**
	 * @var bool
	 */
	public $load_options = true;

	/**
	 * @var Mailpoet_Pro_Helpers
	 */
	public $pro;

	/**
	 * Uoa_Helpers constructor.
	 */
	public function __construct() {

	}

	/**
	 * @param Mailpoet_Helpers $options
	 */
	public function setOptions( Mailpoet_Helpers $options ) {
		$this->options = $options;
	}

	/**
	 * @param Mailpoet_Pro_Helpers $pro
	 */
	public function setPro( Mailpoet_Pro_Helpers $pro ) {
		$this->pro = $pro;
	}

	/**
	 * @param $label
	 * @param $option_code
	 * @param $args
	 *
	 * @return array|mixed|null
	 * @throws \Exception
	 */
	public function get_all_mailpoet_lists( $label = null, $option_code = 'MAILPOETLISTS', $args = array() ) {
		if ( ! $this->load_options ) {

			return Automator()->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		$any_option  = key_exists( 'any_option', $args ) ? $args['any_option'] : false;
		$all_include = key_exists( 'all_include', $args ) ? $args['all_include'] : false;

		if ( ! $label ) {
			$label = esc_attr__( 'List', 'uncanny-automator' );
		}

		$options = array();
		if ( $any_option === true ) {
			$options['-1'] = esc_attr__( 'Any list', 'uncanny-automator' );
		}

		if ( $all_include === true ) {
			$options['all'] = esc_attr__( 'All lists', 'uncanny-automator' );
		}

		$mailpoet  = \MailPoet\API\API::MP( 'v1' );
		$all_lists = $mailpoet->getLists();

		foreach ( $all_lists as $list ) {
			$options[ $list['id'] ] = $list['name'];
		}

		$option = array(
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => 'select',
			'required'        => true,
			'options'         => $options,
			'relevant_tokens' => array(
				$option_code         => esc_attr__( 'List', 'uncanny-automator' ),
				$option_code . '_ID' => esc_attr__( 'List ID', 'uncanny-automator' ),
			),
		);

		return apply_filters( 'uap_option_get_all_mailpoet_lists', $option );
	}

	/**
	 * @param $label
	 * @param $option_code
	 * @param $args
	 *
	 * @return array|mixed|null
	 */
	public function get_all_mailpoet_subscribers( $label = null, $option_code = 'MAILPOETSUBSCRIBERS', $args = array() ) {
		if ( ! $this->load_options ) {

			return Automator()->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		if ( ! $label ) {
			$label = esc_attr__( 'Subscriber', 'uncanny-automator' );
		}

		$options = array();

		global $wpdb;

		$subscribers = $wpdb->get_results( "SELECT id,email FROM {$wpdb->prefix}mailpoet_subscribers  ORDER BY id DESC", ARRAY_A );

		foreach ( $subscribers as $subscriber ) {
			$options[ $subscriber['id'] ] = $subscriber['email'];
		}

		$option = array(
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => 'select',
			'required'        => true,
			'options'         => $options,
			'relevant_tokens' => array(
				$option_code         => esc_attr__( 'Subscriber', 'uncanny-automator' ),
				$option_code . '_ID' => esc_attr__( 'Subscriber ID', 'uncanny-automator' ),
			),
		);

		return apply_filters( 'uap_option_get_all_mailpoet_subscribers', $option );
	}

	/**
	 * @param string $label
	 * @param string $option_code
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function list_mailpoet_forms( $label = null, $option_code = 'MAILPOETFORMS', $args = array() ) {
		global $wpdb;

		if ( ! $this->load_options ) {
			return Automator()->helpers->recipe->build_default_options_array( $label, $option_code );
		}

		if ( ! $label ) {
			$label = esc_attr__( 'Form', 'uncanny-automator' );
		}

		$token        = key_exists( 'token', $args ) ? $args['token'] : false;
		$is_ajax      = key_exists( 'is_ajax', $args ) ? $args['is_ajax'] : false;
		$target_field = key_exists( 'target_field', $args ) ? $args['target_field'] : '';
		$end_point    = key_exists( 'endpoint', $args ) ? $args['endpoint'] : '';
		$options      = array();

		if ( Automator()->helpers->recipe->load_helpers ) {
			$forms = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mailpoet_forms WHERE `deleted_at` IS NULL AND `status` = %s ORDER BY `id` LIMIT %d", 'enabled', 9999 ) );
			if ( ! empty( $forms ) ) {
				foreach ( $forms as $form ) {
					$options[ $form->id ] = esc_html( $form->name );
				}
			}
		}
		$type = 'select';

		$option = array(
			'option_code'     => $option_code,
			'label'           => $label,
			'input_type'      => $type,
			'required'        => true,
			'supports_tokens' => $token,
			'is_ajax'         => $is_ajax,
			'fill_values_in'  => $target_field,
			'endpoint'        => $end_point,
			'options'         => $options,
			'relevant_tokens' => array(
				$option_code                => esc_attr__( 'Form title', 'uncanny-automator' ),
				$option_code . '_ID'        => esc_attr__( 'Form ID', 'uncanny-automator' ),
				$option_code . '_FIRSTNAME' => esc_attr__( 'First name', 'uncanny-automator' ),
				$option_code . '_LASTNAME'  => esc_attr__( 'Last name', 'uncanny-automator' ),
				$option_code . '_EMAIL'     => esc_attr__( 'Email', 'uncanny-automator' ),
			),
		);

		return apply_filters( 'uap_option_list_mailpoet_forms', $option );
	}

}
