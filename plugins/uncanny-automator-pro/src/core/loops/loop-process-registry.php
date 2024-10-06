<?php
namespace Uncanny_Automator_Pro;

use Exception;
use Uncanny_Automator_Pro\Loops\Loop\Background_Process\Entity_Actions;
use Uncanny_Automator_Pro\Loops\Loop\Model\Loop_Entry_Model;
use Uncanny_Automator_Pro\Loops\Loop\Model\Query\Loop_Entry_Item_Query;
use Uncanny_Automator_Pro\Loops\Loop\Model\Query\Loop_Entry_Query;
use Uncanny_Automator_Pro\Loops\Loop_MQ;
use WP_Error;
use WP_REST_Request;

/**
 * Singleton instance of Loop Process Registry
 *
 * @since 5.0
 */
final class Loops_Process_Registry {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
	 * @var Entity_Actions[]
	 */
	private static $transport_objects = array();

	/**
	 * Fills the object with process on initilize
	 *
	 * @return void
	 */
	protected function __construct() {

		$processes = ( new Loop_Entry_Query() )->find_all_in_progress_process();

		if ( false === $processes ) {
			return;
		}

		// Primes each job's wp_ajax async request.
		$this->register_job_primer_endpoint();

		foreach ( (array) $processes as $process_id ) {
			if ( is_string( $process_id ) && ! $this->has_object( $process_id ) ) {
				// Registers the callback for loops as defined by WP Background Processing.
				self::$transport_objects[ $process_id ] = new Entity_Actions( $process_id );
			}
		}

	}

	/**
	 * Register the acknowledge endpoint.
	 *
	 * @return void
	 */
	public function register_job_primer_endpoint() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					'automator-pro/v1',
					'/loops/job/primer',
					array(
						'methods'             => 'GET',
						'callback'            => array( $this, 'prime_specific_job' ),
						'permission_callback' => '__return_true',
					)
				);
			}
		);
	}

	/**
	 * We're not actually priming the object it here. The method __construct does. We only verify.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_Error|array{primed:int}
	 */
	public function prime_specific_job( $request ) {

		$job_id = $request->get_param( 'process_id' );

		if ( ! is_string( $job_id ) ) {
			return new WP_Error( 'automator_pro_invalid_job_id', __( 'Invalid job id found.', 'uncanny-automator-pro' ), array( 'status' => 404 ) );
		}

		// Retrieve the object, at this point, the DB should have been populated already.
		// And the processes should have been primed already. See @_construct.
		$job = $this->get_object( $job_id );

		if ( empty( $job ) ) {
			return new WP_Error( 'automator_pro_invalid_job_id', __( 'Invalid Request.', 'uncanny-automator-pro' ), array( 'status' => 404 ) );
		}

		return array( 'primed' => time() );
	}

	/**
	 * Inserts newly primed process id into transport objects.
	 *
	 * @param string $process_id
	 * @param bool $is_doing_rest
	 *
	 * @return Entity_Actions
	 */
	public function spawn_process( $process_id = '', $is_doing_rest = false ) {

		// No need to prime when not doing rest.
		if ( false === $is_doing_rest ) {
			self::$transport_objects[ $process_id ] = new Entity_Actions( $process_id );
			return self::$transport_objects[ $process_id ];
		}

		$job_primed = $this->remotely_prime_job( $process_id );

		if ( false !== $job_primed ) {
			self::$transport_objects[ $process_id ] = $job_primed;
			return self::$transport_objects[ $process_id ];
		}

		throw new \Exception( 'Creating loop process failed.', 400 );

	}

	/**
	 * Sends an HTTP request to prime the jobs.
	 *
	 * @param string $process_id
	 *
	 * @return Entity_Actions|false
	 * @throws Exception
	 */
	public function remotely_prime_job( string $process_id ) {

		$args = array(
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ), // Local requests, fine to pass false.
		);

		$rest_url = rest_url( '/automator-pro/v1/loops/job/primer' ) . "?process_id={$process_id}";

		// Make sure to instantiate t
		$response = wp_remote_get( $rest_url, $args );

		$response_body   = (array) json_decode( wp_remote_retrieve_body( $response ), true );
		$response_status = (int) wp_remote_retrieve_response_code( $response );

		// Complete with notice on generic connection issue.
		if ( is_wp_error( $response ) ) {
			$message = 'Process did not start with error: ' . $response->get_error_message();
			throw new \Exception( $message, 400 );
		}

		// Complete with error on local connection issue. (e.g. rest is not found).
		if ( 200 !== $response_status ) {
			$response_message = $response_body['message'] ?? '(empty response body message)';
			$message          = 'Process did not start with error: ' . $response_message;
			throw new \Exception( $message, $response_status );
		}

		// Only proceed if wp async request has primed the object already.
		if ( is_numeric( $response_body['primed'] ) && ! empty( $response_body['primed'] ) ) {
			// At this point, the query request has already been primed.
			return new Entity_Actions( $process_id );
		}

		return false;

	}

	/**
	 * Retrieves current running processes in-progress or queued.
	 *
	 * @return Entity_Actions[]
	 */
	public function get_processes() {
		return self::$transport_objects;
	}

	/**
	 * @param string $id
	 *
	 * @return bool True if object is found. Returns false, otherwise.
	 */
	public function has_object( $id ) {
		return isset( self::$transport_objects[ $id ] );
	}

	/**
	 * Get the process object.
	 *
	 * @param string $id
	 *
	 * @return false|Entity_Actions
	 */
	public function get_object( $id ) {
		if ( ! $this->has_object( $id ) ) {
			return false;
		}
		return self::$transport_objects[ $id ];
	}

	/**
	 * @param string $process_id The process ID.
	 *
	 * @return array{loop_id:string,recipe_id:string,recipe_log_id:string,run_number:string}|false
	 */
	public static function extract_process_id( $process_id ) {

		$extracted = explode( '_', $process_id );

		if ( is_array( $extracted ) && 6 !== count( $extracted ) ) {
			return false;
		}

		return array(
			'loop_id'       => $extracted[2],
			'recipe_id'     => $extracted[3],
			'recipe_log_id' => $extracted[4],
			'run_number'    => $extracted[5],
		);

	}

	/**
	 * Generates a process ID string from entry model.
	 *
	 * @return string The process ID token we can use.
	 */
	public static function generate_process_id( Loop_Entry_Model $loop_entry ) {

		return 'loop_process' .
		'_' . $loop_entry->get_loop_id() .
		'_' . $loop_entry->get_recipe_id() .
		'_' . $loop_entry->get_recipe_log_id() .
		'_' . $loop_entry->get_run_number();

	}

	/**
	 * Generates a process ID string from given args.
	 *
	 * @param int $loop_id
	 * @param int $recipe_id
	 * @param int $recipe_log_id
	 * @param int $run_number
	 *
	 * @return string The process ID token we can use.
	 */
	public static function generate_process_id_manual( $loop_id, $recipe_id, $recipe_log_id, $run_number ) {

		return 'loop_process' .
		'_' . $loop_id .
		'_' . $recipe_id .
		'_' . $recipe_log_id .
		'_' . $run_number;

	}

	/**
	 * Deletes specific process from recipe run.
	 *
	 * @param int $recipe_id
	 * @param int $recipe_log_id
	 * @param int $run_number
	 *
	 * @return void
	 */
	public function delete_process( $recipe_id, $recipe_log_id, $run_number ) {

		// Retrieve all loops from specific process.
		$loop_entry_query = new Loop_Entry_Query();

		$loop_entries = $loop_entry_query->find_by_recipe_process( $recipe_id, $recipe_log_id, $run_number );

		foreach ( $loop_entries as $loop_entry ) {

			// Delete from entry.
			$loop_entry_query->delete( $loop_entry );

			$proc_id = $loop_entry->get_process_id();

			// Delete the object from the registry.
			$process = $this->get_object( $proc_id );

			if ( false !== $process ) {
				$process->delete_all();
			}
			// Delete orphan process here?

			// Delete the record from the queue.
			$queue = new Loop_MQ();
			$queue->remove( $proc_id );

			// Delete the transients.
			delete_transient( $proc_id . '_transaction_transient' );

			// Delete the items.
			$loop_entries_items_query = new Loop_Entry_Item_Query();
			$loop_entries_items_query->delete_by_process_id( $proc_id );

		}

	}

	/**
	 * Manually deletes all orphaned processes.
	 *
	 * @param string $process_id
	 *
	 * @return void
	 */
	public function delete_orphaned_process( $process_id ) {
		// @todo Delete orpaned processes? Not sure how the process can have orphaned records, but possible.
	}

	/**
	 * Determine if there are any active process.
	 *
	 * @return bool When an active process is found. Otherwise, false.
	 */
	public function has_active_process() {

		foreach ( $this->get_processes() as $id => $process ) {

			$proc_obj = $this->get_object( $id );

			if ( false !== $proc_obj && $proc_obj->is_active() ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Determines if there are any running processes.
	 *
	 * @return bool True if there are any running process. Otherwise, false.
	 */
	public function has_running_process() {

		foreach ( $this->get_processes() as $id => $process ) {

			$proc_obj = $this->get_object( $id );

			if ( false !== $proc_obj && $proc_obj->is_processing() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Prevent cloning of object.
	 */
	protected function __clone() { }

	/**
	 * Prevent serialization of the object.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Retrieve the instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

}
