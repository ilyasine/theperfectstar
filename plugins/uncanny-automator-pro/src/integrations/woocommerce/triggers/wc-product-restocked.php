<?php

namespace Uncanny_Automator_Pro;

use Uncanny_Automator\Recipe\Trigger;

class WC_PRODUCT_RESTOCKED extends Trigger {

	/**
	 * @return mixed
	 */
	protected function setup_trigger() {
		$this->set_integration( 'WC' );
		$this->set_trigger_code( 'WC_PRODUCT_RESTOCKED' );
		$this->set_trigger_meta( 'WC_PRODUCT' );
		$this->set_is_pro( true );
		$this->set_trigger_type( 'anonymous' );
		$this->set_helper( new Woocommerce_Pro_Helpers() );
		$this->set_sentence( sprintf( esc_attr_x( '{{A product:%1$s}} is restocked', 'WooCommerce', 'uncanny-automator-pro' ), $this->get_trigger_meta() ) );
		$this->set_readable_sentence( esc_attr_x( '{{A product}} is restocked', 'WooCommerce', 'uncanny-automator-pro' ) );
		$this->add_action( 'woocommerce_product_set_stock_status', 10, 3 );
	}

	public function options() {
		$variable_products = Automator()->helpers->recipe->woocommerce->options->pro->all_wc_products();
		$options           = array();
		foreach ( $variable_products['options'] as $k => $product ) {
			$options[] = array(
				'text'  => $product,
				'value' => $k,
			);
		}

		return array(
			array(
				'input_type'      => 'select',
				'option_code'     => $this->get_trigger_meta(),
				'label'           => _x( 'Product', 'WooCommerce', 'uncanny-automator-pro' ),
				'required'        => true,
				'options'         => $options,
				'relevant_tokens' => array(),
			),
		);
	}

	/**
	 * @return bool
	 */
	public function validate( $trigger, $hook_args ) {
		list( $product_id, $stock_status, $product ) = $hook_args;
		if ( $stock_status !== 'instock' ) {
			return false;
		}

		if ( ! isset( $trigger['meta'][ $this->get_trigger_meta() ] ) ) {
			return false;
		}

		$selected_product_id = $trigger['meta'][ $this->get_trigger_meta() ];

		return ( intval( '-1' ) === intval( $selected_product_id ) ) || ( absint( $selected_product_id ) === absint( $product_id ) );
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
		$common_tokens = $this->get_helper()->wc_common_product_tokens();

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
		list( $product_id, $stock_status, $product ) = $hook_args;

		return $this->get_helper()->wc_parse_common_product_tokens( $product, 'simple' );
	}

}