<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default settings
 *
 * @return array
 */
function bunec_get_default_settings() {
	$settings = array(
		'notification_activity_new_mention'         => array(
			'label' => __( 'A member mentions the user in an update using @username', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_activity_new_reply'           => array(
			'label' => __( 'A member replies to an update or comment the user posted', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_messages_new_message'         => array(
			'label' => __( 'A member sends the user a new private message', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_friends_friendship_request'   => array(
			'label' => __( 'A member sends the user friendship request', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_friends_friendship_accepted'  => array(
			'label' => __( "A member accepts the user's friendship request", 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		), // should we hide group related settings if group is not active?//NO.
		'notification_groups_invite'                => array(
			'label' => __( 'A member invites the user to join a group', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_groups_group_updated'         => array(
			'label' => __( 'Group information is updated', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_groups_admin_promotion'       => array(
			'label' => __( 'When user is promoted to a group administrator or moderator', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_groups_membership_request'    => array(
			'label' => __( 'When a member requests to join a private group for which the users ia an admin', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
		'notification_membership_request_completed' => array(
			'label' => __( 'When a member request to join a group has been approved or denied', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		),
	);

	if ( defined( 'BP_PLATFORM_VERSION' ) ) {
		$settings['notification_group_messages_new_message'] = array(
			'label' => __( 'When a group member sends new message to the user(using group message)', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		);
		$settings['notification_forums_following_reply']     = array(
			'label' => __( 'When a new reply is created for the forum topic(discussion thread) the user follows', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		);

		$settings['notification_forums_following_topic'] = array(
			'label' => __( 'When a new topic is created for the forums the user follows', 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		);
	}

	if ( class_exists( 'BB_Platform_Pro' ) ) {
		$settings['notification_zoom_meeting_scheduled']     = array(
			'label' => __( "When a Zoom meeting has been scheduled in one of the user's groups", 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		);

		$settings['notification_zoom_webinar_scheduled'] = array(
			'label' => __( "When a Zoom webinar has been scheduled in one of user's groups", 'bp-default-email-notification-settings-control' ),
			'val'   => 'no',
		);
	}

	// BuddyPress Group documents plugin.
	if ( defined( 'BP_GROUP_DOCUMENTS_IS_INSTALLED' ) && function_exists( 'bp_group_documents_init' ) ) {
		$settings = array_merge(
			$settings,
			array(
				'notification_group_documents_upload_member' => array(
					'label' => __( 'When a member uploads a document to a group the user belongs to', 'bp-default-email-notification-settings-control' ),
					'val'   => 'no',
				),
				'notification_group_documents_upload_mod'    => array(
					'label' => __( 'When a member uploads a document to a group for which the user is moderator/admin', 'bp-default-email-notification-settings-control' ),
					'val'   => 'no',
				),
			)
		);
	}

	return apply_filters( 'bunec_default_settings', $settings );
}
