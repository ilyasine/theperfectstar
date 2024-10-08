<?php

namespace Uncanny_Automator_Pro;

use PeepSoUser;
use PeepSoUserFollower;

/**
 * Class PeepSo_USERGAINSFOLLOWER
 *
 * @package Uncanny_Automator_Pro
 */
class PeepSo_USERGAINSFOLLOWER {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'PP';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'PPUSERGAINSFOLLOWER';
		$this->trigger_meta = 'USERGAINSFOLLOWER';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/peepso/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'meta'                => $this->trigger_meta,
			/* translators: Logged-in trigger - WordPress Core */
			'sentence'            => __( 'A user gains a new follower', 'uncanny-automator-pro' ),
			/* translators: Logged-in trigger - WordPress Core */
			'select_option_name'  => __( 'A user gains a new follower', 'uncanny-automator-pro' ),
			'action'              => 'peepso_ajax_start',
			'priority'            => 100,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'user_followed' ),
			'options'             => array(),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $user_id
	 */
	public function user_followed( $data ) {

		if ( $data === 'followerajax.set_follow_status' ) {

			if ( automator_filter_has_var( 'follow', INPUT_POST ) && automator_filter_input( 'follow', INPUT_POST ) === 0 ) {
				return;
			}

			$user_id       = ( automator_filter_input( 'uid', INPUT_POST ) ) ? absint( automator_filter_input( 'uid', INPUT_POST ) ) : false;
			$follower_id   = ( automator_filter_input( 'user_id', INPUT_POST ) ) ? absint( automator_filter_input( 'user_id', INPUT_POST ) ) : false;
			$follow_status = ( automator_filter_input( 'follow', INPUT_POST ) ) ? absint( automator_filter_input( 'follow', INPUT_POST ) ) : false;

			if ( false === $follow_status || false === $follower_id ) {
				return;
			}

			$peepso_user = PeepSoUser::get_instance( $follower_id );

			$args = array(
				'code'           => $this->trigger_code,
				'meta'           => $this->trigger_meta,
				'post_id'        => - 1,
				'ignore_post_id' => true,
				'user_id'        => $user_id,
				'is_signed_in'   => true,
			);

			$args = Automator()->maybe_add_trigger_entry( $args, false );

			// Save trigger meta
			if ( $args ) {
				foreach ( $args as $result ) {
					if ( true === $result['result'] && $result['args']['trigger_id'] && $result['args']['trigger_log_id'] ) {

						$run_number = Automator()->get->trigger_run_number( $result['args']['trigger_id'], $result['args']['trigger_log_id'], $result['args']['user_id'] );
						$save_meta  = array(
							'user_id'        => $result['args']['user_id'],
							'trigger_id'     => $result['args']['trigger_id'],
							'run_number'     => $run_number, //get run number
							'trigger_log_id' => $result['args']['trigger_log_id'],
							'ignore_user_id' => true,
						);

						$save_meta['meta_key']   = 'AVATARURL';
						$save_meta['meta_value'] = $peepso_user->get_avatar();
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_USERNAME';
						$save_meta['meta_value'] = $peepso_user->get_username();
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_FIRST_NAME';
						$save_meta['meta_value'] = Automator()->helpers->recipe->peepso->pro->get_name( $peepso_user->get_fullname(), 'first' );
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_LAST_NAME';
						$save_meta['meta_value'] = Automator()->helpers->recipe->peepso->pro->get_name( $peepso_user->get_fullname(), 'last' );
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_GENDER';
						$save_meta['meta_value'] = Automator()->helpers->recipe->peepso->pro->get_gender( $follower_id );
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_BIRTHDATE';
						$save_meta['meta_value'] = Automator()->helpers->recipe->peepso->pro->get_birthdate( $follower_id );
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_FOLLOWERS';
						$save_meta['meta_value'] = PeepSoUserFollower::count_followers( $follower_id );
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_FOLLOWING';
						$save_meta['meta_value'] = PeepSoUserFollower::count_following( $follower_id );
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_PROFILEURL';
						$save_meta['meta_value'] = $peepso_user->get_profileurl();
						Automator()->insert_trigger_meta( $save_meta );

						$save_meta['meta_key']   = 'FL_EMAIL';
						$save_meta['meta_value'] = $peepso_user->get_email();
						Automator()->insert_trigger_meta( $save_meta );

						Automator()->maybe_trigger_complete( $result['args'] );
					}
				}
			}
		}
	}

}
