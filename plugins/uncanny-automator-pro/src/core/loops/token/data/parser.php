<?php
namespace Uncanny_Automator_Pro\Loops\Token\Data;

use Uncanny_Automator_Pro\Loops\Loop\Model\Query\Loop_Entry_Query;
use Uncanny_Automator_Pro\Loops\Token\Text_Parseable;

/**
 * Posts tokens
 *
 * @since 6.0
 *
 * @package Uncanny_Automator_Pro\Loops\Token
 */
final class Parser extends Text_Parseable {

	/**
	 * The regexp pattern.
	 *
	 * @var string $pattern
	 */
	protected $pattern = '/{{TOKEN_EXTENDED:LOOP_TOKEN:\d+:DATA:[^}]+}}/';

	/**
	 * @param $index
	 * @param $item
	 *
	 * @return string
	 */
	public function parse( $index, $item ) {

		$process_args = $this->get_text_parser_args();

		// @phpstan-ignore-next-line Handled via null coalescing operator.
		$process_id = isset( $process_args['loop']['loop_item']['filter_id'] ) ?? null;

		$loop = ( new Loop_Entry_Query() )->find_entry_by_process_id( (string) $process_id );

		if ( false === $loop ) {
			return '';
		}

		// @phpstan-ignore-next-line Handled via null coalescing operator.
		$entities = (array) json_decode( $loop->get_user_ids(), true );

		// @phpstan-ignore-next-line Handled via null coalescing operator.
		$value = $entities[ $index ][ $item ] ?? '';

		if ( is_iterable( $value ) ) {

			$encoded = wp_json_encode( $value );

			if ( false === $encoded ) {
				return '';
			}

			return $encoded;
		}

		if ( ! is_string( $value ) ) {
			return '';
		}

		return $value;

	}

}
