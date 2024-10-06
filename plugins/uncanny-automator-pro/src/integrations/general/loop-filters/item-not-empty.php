<?php
namespace Uncanny_Automator_Pro\Loop_Filters;

use Uncanny_Automator_Pro\Loops\Filter\Base\Loop_Filter;
use Uncanny_Automator_Pro\Loops\Loop\Entity_Factory;
use Uncanny_Automator_Pro\Loops\Loop\Exception\Loops_Exception;

/**
 * Loop filter - The user {is/is not} in {a group}
 *
 * Class ITEM_NOT_EMPTY
 *
 * @package Uncanny_Automator_Pro
 */
class ITEM_NOT_EMPTY extends Loop_Filter {

	/**
	 * @var string
	 */
	const META = 'ITEM_NOT_EMPTY';

	/**
	 * Sets up the filter.
	 *
	 * @return void
	 */
	public function setup() {

		$static_sentence = esc_html_x(
			'The item in the loop meets {{a specific condition}}',
			'Filter sentence',
			'uncanny-automator-pro'
		);

		$dynamic_sentence = sprintf(
			esc_html_x(
				'The item in the loop meets {{a specific condition:%1$s}}',
				'Filter sentence',
				'uncanny-automator-pro'
			),
			self::META
		);

		$this->set_integration( 'GEN' );
		$this->set_meta( self::META );
		$this->set_sentence( $static_sentence );
		$this->set_sentence_readable( $dynamic_sentence );
		$this->set_fields( array( $this, 'load_options' ) );
		$this->set_entities( array( $this, 'get_items' ) );
		$this->set_loop_type( Entity_Factory::TYPE_TOKEN );

	}

	/**
	 * Load options.
	 *
	 * @return mixed[]
	 */
	public function load_options() {

		$choices = array(
			array(
				'text'  => _x( 'Current item is not empty', 'General', 'uncanny-automator-pro' ),
				'value' => 'is_not_empty',
			),
		);

		return array(
			$this->get_meta() => array(
				array(
					'option_code'           => $this->get_meta(),
					'type'                  => 'select',
					'supports_custom_value' => false,
					'label'                 => esc_html_x( 'Condition', 'General', 'uncanny-automator-pro' ),
					'options'               => $choices,
				),
			),
		);

	}

	/**
	 * @param array{ITEM_NOT_EMPTY:string} $fields
	 *
	 * @return array{mixed[]}
	 */
	public function get_items( $fields ) {

		$loopable_items_array = $this->get_loopable_items();

		// Remove empty items.
		$loopable_items_array = array_filter( $loopable_items_array );

		if ( empty( $loopable_items_array ) ) {
			return array();
		}

		return $loopable_items_array;

	}
}
