<?php

namespace Uncanny_Automator_Pro;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Actions_Conditions
 *
 * @package Uncanny_Automator_Pro
 */
class Actions_Conditions {


	/**
	 * @var int
	 */
	const SKIPPED_STATUS = 8;

	/**
	 * @var int|null
	 */
	public $recipe_id = null;
	/**
	 * @var int|null
	 */
	public $recipe_log_id = null;
	/**
	 * @var int|null
	 */
	public $user_id = null;

	/**
	 * @var array
	 */
	private $evaluated_conditions = array();

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {

		// Include the Action_Condition abtract class.
		if ( ! class_exists( '\Uncanny_Automator_Pro\Action_Condition', false ) ) {
			include_once UAPro_ABSPATH . 'src/core/classes/action-condition.php';
		}

		/**
		 * Prevent the actions from executing if the conditions are not met
		 * The priority is set to 5 to make sure the conditions are applied before any scheduled actions are postponed
		 */
		add_filter( 'automator_before_action_executed', array( $this, 'maybe_skip_action' ), 5, 2 );

		// Keeping this filter for backward compatibility for existing scheduled actions
		add_filter( 'automator_pro_before_async_action_executed', array( $this, 'maybe_skip_action' ), 10, 1 );

		// Add all the available conditions to the object that is sent to the UI
		add_filter( 'automator_api_setup', array( $this, 'send_to_ui' ) );

		// Add the conditions meta to the recipe objects that are sent to the UI
		add_filter( 'automator_get_recipe_data_by_recipe_id', array( $this, 'add_to_recipes_object' ), 10, 2 );
		add_filter( 'automator_get_recipes_data', array( $this, 'add_to_recipes_object' ), 10, 2 );

		// Register the API endpoint
		add_action( 'rest_api_init', array( $this, 'register_rest_api_endpoint' ) );

		// Change the status of actions that failed conditions
		add_filter( 'automator_get_action_completed_status', array( $this, 'change_action_completed_status' ), 10, 7 );

		// Adjust how the new status is displayed in the log
		add_filter( 'automator_action_log_status', array( $this, 'action_log_status_display' ), 10, 2 );

	}

	/**
	 * Method should_process_further
	 *
	 * @param mixed $action
	 *
	 * @return void
	 */
	public function should_process_further( $action ) {
		if ( isset( $action['process_further'] ) && false === $action['process_further'] ) {
			throw new \Exception( 'Action was cancelled or postponed earlier', 1 );
		}
	}

	/**
	 * Method maybe_skip_action
	 *
	 * @param array $action
	 *
	 * @return array $action
	 */
	public function maybe_skip_action( $action, $args = array() ) {

		try {

			$this->should_process_further( $action );

			$this->recipe_id     = $this->get_recipe_id( $action );
			$this->recipe_log_id = $this->get_recipe_log_id( $action );
			$this->user_id       = $this->get_user_id( $action );
			$conditions          = $this->get_recipe_conditions();

			$action = $this->maybe_process_further( $action, $conditions );

			if ( isset( $action['process_further'] ) && false === $action['process_further'] ) {
				$this->log_action( $action );
			}
		} catch ( \Exception $e ) {
			// If some data was missing, or something went wrong, skip this action and do nothing
			automator_log( $e->getMessage() );
		}

		return $action;
	}

	/**
	 * Method get_recipe_id
	 *
	 * @param array $action
	 *
	 * @return int $recipe_id
	 * @throws \Exception
	 */
	public function get_recipe_id( $action ) {

		if ( empty( $action['recipe_id'] ) ) {
			throw new \Exception( 'Missing recipe ID' );
		}

		return (int) $action['recipe_id'];
	}

	/**
	 * @param mixed[] $action
	 * @param mixed[] $action
	 * @return int
	 * @throws \Exception
	 */
	public function get_recipe_log_id( $action ) {

		if ( empty( $action['action_data']['recipe_log_id'] ) ) {
			throw new \Exception( 'Missing recipe log ID' );
		}

		return (int) $action['action_data']['recipe_log_id'];
	}

	/**
	 * @param array{user_id:int} $action
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function get_user_id( $action ) {

		return (int) $action['user_id'];
	}

	/**
	 * Method get_recipe_conditions
	 *
	 * @param mixed $recipe_id
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_recipe_conditions( $recipe_id = null ) {

		if ( null === $recipe_id ) {
			$recipe_id = $this->recipe_id;
		}

		$conditions = get_post_meta( $recipe_id, 'actions_conditions', true );

		if ( empty( $conditions ) ) {
			throw new \Exception( 'There were no conditions to evaluate' );
		}

		return $conditions;
	}

	/**
	 * TODO: Use Automator()->db when it's ready.
	 * @return mixed|null
	 */
	public function get_condition_results( $conditions_group_id ) {
		global $wpdb;

		if ( ! empty( $this->evaluated_conditions[ $conditions_group_id ] ) ) {
			return $this->evaluated_conditions[ $conditions_group_id ];
		}

		$r = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_value FROM {$wpdb->prefix}uap_recipe_log_meta WHERE recipe_log_id = %d AND meta_key = %s",
				$this->recipe_log_id,
				$conditions_group_id
			)
		);

		if ( ! empty( $r ) ) {
			return json_decode( $r, true );
		}

		return array();
	}

	/**
	 * If the action is running in a loop, we need to make sure that the conditions are evaludated for each entity in the loop.
	 * @param $action
	 *
	 * @return bool
	 */
	public function maybe_running_in_loop( $action ) {
		return array_key_exists( 'loop', $action['action_data'] );
	}

	/**
	 * Method maybe_process_further
	 *
	 * @param mixed $action
	 * @param $actions_conditions
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function maybe_process_further( $action, $actions_conditions ) {

		$actions_conditions = json_decode( $actions_conditions, true );

		if ( ! $actions_conditions ) {
			throw new \Exception( 'Something is wrong with the conditions json string' );
		}

		if ( ! isset( $action['action_data'] ) || ! isset( $action['action_data']['ID'] ) ) {
			throw new \Exception( 'Missing action ID' );
		}

		$current_action_conditions = null;

		// We need to loop through all conditions until we find the first that includes the current action
		foreach ( $actions_conditions as $condition_group ) {

			if ( ! $this->is_correct_parent_recipe( $condition_group ) ) {
				continue;
			}

			$condition_group_actions = array_map( 'absint', $condition_group['actions'] );
			if ( in_array( absint( $action['action_data']['ID'] ), $condition_group_actions, true ) ) {
				$current_action_conditions = $condition_group;
				break;
			}
		}

		if ( null === $current_action_conditions || empty( $current_action_conditions['conditions'] ) ) {
			throw new \Exception( 'There were no conditions for this action' );
		}

		if ( ! isset( $current_action_conditions['mode'] ) ) {
			throw new \Exception( 'Missing condition mode' );
		}

		$running_in_loop = $this->maybe_running_in_loop( $action );

		/**
		 * If the `$current_action_conditions` is empty, we need to run the first action to trigger the conditions check.
		 * We only need to evaluate the conditions once for the whole group, since all action in the group will have the same conditions.
		 * We don't need to evaluate the conditions again & again for each action in the group, which accidentally will cause the action to be skipped.
		 */
		if ( true === apply_filters( 'automator_pro_actions_conditions_legacy_flow', false ) || empty( $this->get_condition_results( $current_action_conditions['id'] ) ) || true === $running_in_loop ) {
			$result_to_catch = 'any' === $current_action_conditions['mode'];

			$action = $this->find_first( $action, $current_action_conditions, $result_to_catch );
		}

		// If any condition mode was selected and some of the actions failed while some didn't, need to remove the failed_actions_conditions flag to make sure the action status doesn't switch to Skipped.
		if ( true === apply_filters( 'automator_pro_actions_conditions_legacy_flow', false ) || true === $running_in_loop ) {
			if ( 'any' === $current_action_conditions['mode'] && $action['process_further'] ) {
				$action['action_data']['failed_actions_conditions'] = false;
			}
		} else {

			// Since the conditions are evaluated once for the whole group, we need to update the results.
			if ( $this->maybe_evaluate_condition_block( $current_action_conditions ) ) {
				$action['action_data']['failed_actions_conditions'] = false;
				$action['process_further']                          = true;
			} else {
				$action['action_data']['failed_actions_conditions'] = true;
				$action['process_further']                          = false;
			}
		}

		return $action;
	}

	/**
	 * @param $condition_group
	 *
	 * @return bool
	 */
	private function is_correct_parent_recipe( $condition_group ) {
		$parent_id = absint( $condition_group['parent_id'] );
		$recipe_id = absint( $this->recipe_id );

		// If parent ID is a recipe type, then the group should match the recipe ID
		if ( 'uo-recipe' === get_post_type( $parent_id ) && $recipe_id === $parent_id ) {
			return true;
		}

		// If the parent ID is a loop type, then the Loops parent ID should match the recipe ID
		if ( 'uo-loop' === get_post_type( $parent_id ) && $recipe_id === wp_get_post_parent_id( $parent_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $current_action_conditions
	 *
	 * @return bool
	 */
	public function maybe_evaluate_condition_block( $current_action_conditions ) {

		$conditions_result = $this->get_condition_results( $current_action_conditions['id'] );

		// Nothing to evaluate
		if ( empty( $conditions_result ) ) {
			return true;
		}

		return $this->evaluate_condition_criteria( $current_action_conditions['mode'], $conditions_result );
	}

	/**
	 * @param $type
	 * @param $results
	 *
	 * @return bool
	 */
	public function evaluate_condition_criteria( $type, $results ) {
		if ( 'any' === $type && in_array( 'succeeded', $results, true ) ) {

			return true;
		}

		// Check if all conditions are met
		$all_conditions_met = count(
			array_filter(
				$results,
				function ( $value ) {
					return $value === 'succeeded';
				}
			)
		) === count( $results );

		return true === $all_conditions_met;
	}

	/**
	 * Method find_first
	 *
	 * Loops through conditions unitl the first $result_to_catch is found.
	 *
	 * @param mixed $conditions
	 * @param mixed $result_to_catch
	 *
	 * @return array
	 */
	public function find_first( $action, $conditions, $result_to_catch ) {

		$evaluated_conditions = array();
		$condition_result     = array();

		foreach ( $conditions['conditions'] as $condition ) {

			// Collect all conditions that has been evaluated.
			$evaluated_conditions[] = $condition;

			$action = apply_filters( 'automator_pro_evaluate_actions_conditions', $action, $condition );

			if ( isset( $action['process_further'] ) ) {

				$condition_result[ $condition['id'] ] = 'failed';

				// If the results to catch is false and process further is true, this means the current condition succeeds.
				if ( false === $result_to_catch && true === $action['process_further'] ) {
					$condition_result[ $condition['id'] ] = 'succeeded';
				}

				// Break from the loop if one of the conditions meets the result we are searching for
				if ( $result_to_catch === $action['process_further'] ) {
					$condition_result[ $condition['id'] ] = 'succeeded';
					// The 'All' conditions has set $result_to_catch to false.
					// If process further is false consider this condition as failed.
					if ( false === $action['process_further'] ) {
						$condition_result[ $condition['id'] ] = 'failed';
					}
					// Pass all collected evaluated conditions before returning the $action.
					do_action( 'automator_pro_actions_conditions_evaluated', $evaluated_conditions, $conditions, $action );
					do_action( 'automator_pro_actions_conditions_result', $condition_result, $conditions, $action );

					$this->store_condition_results( $conditions['id'], $condition_result );
					$this->evaluated_conditions[ $conditions['id'] ] = $condition_result;

					return $action;
				}
			}
		}

		// If we were looking for a true, and haven't found one above, consider this as failed
		if ( $result_to_catch ) {
			$action['process_further'] = false;
		}

		do_action( 'automator_pro_actions_conditions_result', $condition_result, $conditions, $action );
		// Pass all collected evaluated conditions.
		do_action( 'automator_pro_actions_conditions_evaluated', $evaluated_conditions, $conditions, $action );

		$this->store_condition_results( $conditions['id'], $condition_result );

		return $action;
	}

	/**
	 * TODO: Use Automator()->db when it's ready.
	 *
	 * @param $conditions_group_id
	 * @param $condition_result
	 *
	 * @return void
	 */
	public function store_condition_results( $conditions_group_id, $condition_result ) {

		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'uap_recipe_log_meta',
			array(
				'user_id'       => $this->user_id,
				'recipe_id'     => $this->recipe_id,
				'recipe_log_id' => $this->recipe_log_id,
				'meta_key'      => $conditions_group_id,
				'meta_value'    => wp_json_encode( $condition_result ),
			),
			array(
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
			)
		);
	}

	/**
	 * Method send_to_ui
	 *
	 * @param mixed $api_setup
	 *
	 * @return array
	 */
	public function send_to_ui( $api_setup ) {

		// Get all possible conditions
		$api_setup['actionsConditions'] = apply_filters( 'automator_pro_actions_conditions_list', array() );

		return $api_setup;
	}

	/**
	 * Method send_to_ui
	 *
	 * @param mixed $api_setup
	 *
	 * @return array
	 */
	public function add_to_recipes_object( $recipes, $recipe_id ) {

		// Only add conditions to recipes objects when the recipe UI is loaded.
		if ( ! Automator()->helpers->recipe->is_edit_page() && ! Automator()->helpers->recipe->is_automator_ajax() ) {
			return $recipes;
		}

		foreach ( $recipes as $recipe_id => $recipe ) {
			try {
				$recipes[ $recipe_id ]['actions_conditions'] = $this->get_recipe_conditions( $recipe_id );
			} catch ( \Error $th ) {
				// If the recipe doesn't have valid conditions, do nothing
				continue;
			} catch ( \Exception $th ) {
				// If the recipe doesn't have valid conditions, do nothing
				continue;
			}
		}

		return $recipes;
	}

	/**
	 * Method register_rest_api_endpoint
	 *
	 * @return void
	 */
	public function register_rest_api_endpoint() {

		register_rest_route(
			AUTOMATOR_REST_API_END_POINT,
			'/actions_conditions_update/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'actions_conditions_update' ),
				'permission_callback' => array( $this, 'save_settings_permissions' ),
			)
		);

		register_rest_route(
			AUTOMATOR_REST_API_END_POINT,
			'/actions_conditions_fields/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'actions_conditions_fields' ),
				'permission_callback' => array( $this, 'save_settings_permissions' ),
			)
		);

		register_rest_route(
			AUTOMATOR_REST_API_END_POINT,
			'/actions_order_and_conditions/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'actions_order_and_conditions' ),
				'permission_callback' => array( $this, 'save_settings_permissions' ),
			)
		);
	}

	/**
	 * Checks the nonce of Rest API requests
	 *
	 * @return bool
	 */
	public function valid_nonce() {

		if ( empty( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
			return false;
		}

		return wp_verify_nonce( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ), 'wp_rest' );
	}

	/**
	 * Permission callback function that let the rest API allow or disallow access
	 *
	 * @return bool|WP_Error
	 */
	public function save_settings_permissions() {

		if ( ! $this->valid_nonce() ) {
			return false;
		}

		$capability = 'manage_options';
		$capability = apply_filters_deprecated( 'uap_roles_modify_recipe', array( $capability ), '3.0', 'automator_capability_required' );
		$capability = apply_filters( 'automator_capability_required', $capability );

		// Restrict endpoint to only users who have the edit_posts capability.
		if ( ! current_user_can( $capability ) ) {
			return new WP_Error( 'rest_forbidden', 'You do not have the capability to save settings.', array( 'status' => 403 ) );
		}

		// This is a black-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
		$setting = true;
		$setting = apply_filters_deprecated( 'uap_save_setting_permissions', array( $setting ), '3.0', 'automator_save_setting_permissions' );

		return apply_filters( 'automator_save_setting_permissions', $setting );
	}

	/**
	 * Function to update the action's conditions.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function actions_conditions_update( WP_REST_Request $request ) {

		// Make sure we have a recipe ID and the conditions
		if ( $request->has_param( 'recipe_id' ) && $request->has_param( 'actions_conditions' ) ) {

			$recipe_id  = absint( $request->get_param( 'recipe_id' ) );
			$conditions = $request->get_param( 'actions_conditions' );

			update_post_meta( $recipe_id, 'actions_conditions', $conditions );

			$return['message'] = 'Updated!';
			$return['success'] = true;
			$return['action']  = 'actions_conditions_update';

			Automator()->cache->clear_automator_recipe_part_cache( $recipe_id );

			$return['recipes_object'] = Automator()->get_recipes_data( true, $recipe_id );
			$return['_integrations']  = Automator()->get_recipe_integrations( $recipe_id );
			$return['_recipe']        = Automator()->get_recipe_object( $recipe_id );

			return new WP_REST_Response( $return, 200 );

		}

		$return['message'] = 'Failed to update';
		$return['success'] = false;
		$return['action']  = 'show_error';

		return new WP_REST_Response( $return, 200 );

	}

	/**
	 * Will return the condtion's fields
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function actions_conditions_fields( WP_REST_Request $request ) {

		// Make sure we have the integration's and condition's code
		if ( ! $request->has_param( 'integration' ) || ! $request->has_param( 'code' ) ) {

			$return['message'] = 'Integration or condition code is missing';
			$return['success'] = false;
			$return['action']  = 'show_error';

			return new WP_REST_Response( $return, 200 );
		}

		$integration = $request->get_param( 'integration' );
		$code        = $request->get_param( 'code' );

		$fields = apply_filters( 'automator_pro_actions_conditions_fields', array(), $integration, $code );

		if ( empty( $fields ) ) {

			$return['message'] = 'No fields were found';
			$return['success'] = false;
			$return['action']  = 'show_error';

			return new WP_REST_Response( $return, 200 );
		}

		$return['fields']  = $fields;
		$return['message'] = 'success';
		$return['success'] = true;
		$return['action']  = 'actions_conditions_fields';

		return new WP_REST_Response( $return, 200 );

	}

	/**
	 * Function to update the menu_order of the actions along with conditions
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function actions_order_and_conditions( WP_REST_Request $request ) {

		// Make sure we have a recipe ID, order and the conditions
		if ( $request->has_param( 'recipe_id' ) && $request->has_param( 'conditions' ) && $request->has_param( 'order' ) ) {

			$recipe_id  = absint( $request->get_param( 'recipe_id' ) );
			$conditions = $request->get_param( 'conditions' );
			$new_order  = $request->get_param( 'order' );

			// Update the actions menu_order here
			foreach ( $new_order as $index => $action_id ) {
				Automator()->db->action->update_menu_order( $action_id, ( $index + 1 ) * 10 );
			}

			update_post_meta( $recipe_id, 'actions_conditions', $conditions );

			$return['message'] = 'Updated!';
			$return['success'] = true;
			$return['action']  = 'actions_order_and_conditions';

			Automator()->cache->clear_automator_recipe_part_cache( $recipe_id );

			$return['recipes_object'] = Automator()->get_recipes_data( true, $recipe_id );

			return new WP_REST_Response( $return, 200 );

		}

		$return['message'] = 'Failed to update';
		$return['success'] = false;
		$return['action']  = 'show_error';

		return new WP_REST_Response( $return, 200 );
	}

	/**
	 * Method action_log_status_display
	 *
	 * This function will intercept the status of each action in the log table and replace it with the appropriate status if an action was scheduled or cancelled.
	 *
	 * @param string $status
	 * @param array $action
	 *
	 * @return string
	 */
	public function action_log_status_display( $status, $action ) {

		if ( self::SKIPPED_STATUS === (int) $action->action_completed ) {
			$status = esc_attr_x( 'Skipped', 'Action', 'uncanny-automator' );
		}

		return $status;
	}

	/**
	 * Method change_action_completed_status
	 *
	 * This function will intercept the action completion process at automator_get_action_completed_status filter and swap the completed status with 7 if the action was skipped.
	 *
	 * @param int $completed
	 * @param int $user_id
	 * @param array $action_data
	 * @param int $recipe_id
	 * @param string $error_message
	 * @param int $recipe_log_id
	 * @param array $args
	 *
	 * @return int
	 */
	public function change_action_completed_status( $completed, $user_id, $action_data, $recipe_id, $error_message, $recipe_log_id, $args ) {

		// If there was an error
		if ( 2 === intval( $completed ) ) {
			return $completed;
		}

		// If failed conditions
		if ( empty( $action_data['failed_actions_conditions'] ) ) {
			return $completed;
		}

		// Change the completed status to 8 (skipped)
		$completed = self::SKIPPED_STATUS;

		return $completed;
	}

	/**
	 * Method log_action
	 *
	 * This function will go through the action process to create/update a record in Automator's action log
	 * The process will be intercepted later to change the completed status
	 *
	 * @param array $action
	 *
	 * @return void
	 */
	public function log_action( $action ) {

		$action['args']['user_action_message'] = $this->extract_errors( $action );

		// If the action was scheduled, we don't need to create a log for it
		if ( $this->action_was_scheduled( $action ) ) {
			// Complete the previously created action
			$this->mark_existing_action_skipped( $action );
		} else {
			// Otherwise create an action log
			$this->create_action_log_record( $action );
		}
	}

	/**
	 * Method action_was_scheduled
	 *
	 * @param mixed $action
	 *
	 * @return bool
	 */
	public function action_was_scheduled( $action ) {

		if ( ! isset( $action['action_data']['async']['status'] ) ) {
			return false;
		}

		return 'waiting' === $action['action_data']['async']['status'];
	}

	/**
	 * Method mark_existing_action_skipped
	 *
	 * @param mixed $action
	 *
	 * @return void
	 */
	public function mark_existing_action_skipped( $action ) {

		extract( $action );

		$recipe_log_id = $action_data['recipe_log_id'];

		Automator()->db->action->mark_complete( (int) $action_data['ID'], $recipe_log_id, self::SKIPPED_STATUS, $args['user_action_message'] );

		do_action( 'uap_action_completed', $user_id, (int) $action_data['ID'], $recipe_id, $args['user_action_message'], $args );

		Automator()->complete->recipe( $recipe_id, $user_id, $recipe_log_id, $args );
	}

	/**
	 * Method create_action_log_record
	 *
	 * @param mixed $action
	 *
	 * @return void
	 */
	public function create_action_log_record( $action ) {

		extract( $action );

		$recipe_log_id = $action_data['recipe_log_id'];

		Automator()->complete->action( $user_id, $action_data, $recipe_id, '', $action_data['recipe_log_id'], $args );
	}

	/**
	 * Method extract_errors
	 *
	 * @param mixed $action
	 *
	 * @return string
	 */
	public function extract_errors( $action ) {

		$output = '';

		if ( ! empty( $action['action_data']['actions_conditions_log'] ) ) {

			foreach ( $action['action_data']['actions_conditions_log'] as $message ) {
				$output .= $message . "\n";
			}
		}

		return $output;
	}
}
