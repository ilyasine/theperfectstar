<?php

namespace WPFusion\Integrations;

use SureCart\Integrations\Contracts\IntegrationInterface;
use SureCart\Integrations\Contracts\PurchaseSyncInterface;
use SureCart\Integrations\IntegrationService;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Apply_Tags extends IntegrationService implements IntegrationInterface, PurchaseSyncInterface {
	/**
	 * Get the slug for the integration.
	 *
	 * @return string
	 */
	public function getName() {
		return 'wp-fusion/apply-tags';
	}

	/**
	 * Get the SureCart model used for the integration.
	 * Only 'product' is supported at this time.
	 *
	 * @return string
	 */
	public function getModel() {
		return 'product';
	}

	/**
	 * Get the integration logo url.
	 * This can be to a png, jpg, or svg for example.
	 *
	 * @return string
	 */
	public function getLogo() {
		return esc_url( WPF_DIR_URL . '/assets/img/logo-sm-trans.png' );
	}

	/**
	 * The display name for the integration in the dropdown.
	 *
	 * @return string
	 */
	public function getLabel() {
		return sprintf( __( 'Apply %1$s in %2$s', 'wp-fusion' ), ucwords( wpf_get_option( 'crm_tag_type', 'Tag' ) ), wp_fusion()->crm->name );
	}

	/**
	 * The label for the integration item that will be chosen.
	 *
	 * @return string
	 */
	public function getItemLabel() {
		return sprintf( __( 'Apply %1$s', 'wp-fusion' ), ucwords( wpf_get_option( 'crm_tag_type', 'Tag' ) ) );
	}

	/**
	 * Help text for the integration item chooser.
	 *
	 * @return string
	 */
	public function getItemHelp() {
		return sprintf( __( 'Apply %1$s to users', 'wp-fusion' ), wpf_get_option( 'crm_tag_type', 'Tag' ) );
	}

	/**
	 * Get item listing for the integration.
	 * These are a list of item the merchant can choose from when adding an integration.
	 *
	 * @param array  $items The integration items.
	 * @param string $search The search term.
	 *
	 * @return array The items for the integration.
	 */
	public function getItems( $items = array(), $search = '' ) {

		$final_tags = array();
		$tags       = wp_fusion()->settings->get_available_tags_flat( false, false );
		foreach ( $tags as $key => $value ) {

			if ( ! empty( $search ) ) {
				if ( false === strpos( strtolower( $value ), strtolower( $search ) ) ) {
					continue;
				}
			}

			$final_tags[] = array(
				'id'    => $key,
				'label' => $value,
			);
		}
		return $final_tags;
	}

	/**
	 * Get the individual item.
	 *
	 * @param string $role The item role.
	 *
	 * @return array The item for the integration.
	 */
	public function getItem( $tag ) {

		$tag = urldecode( $tag );

		$tags = wp_fusion()->settings->get_available_tags_flat( false, false );

		if ( ! isset( $tags[ $tag ] ) ) {
			return false;
		}

		return array(
			'id'    => $tag,
			'label' => isset( $tags[ $tag ] ) ? $tags[ $tag ] : sprintf( __( 'Unknown tag: %s', 'wp-fusion' ), $tag ),
		);
	}

	/**
	 * Apply tag when the purchase is created.
	 *
	 * @param \SureCart\Models\Integration $integration The integrations.
	 * @param \WP_User                     $wp_user The user.
	 *
	 * @return boolean|void Returns true if the tag is applied.
	 */
	public function onPurchaseCreated( $integration, $wp_user ) {
		$atts       = $integration->getAttributes();
		$apply_tags = array( $atts['integration_id'] );
		if ( empty( $apply_tags ) ) {
			return;
		}

		if ( intval( $wp_user->ID ) !== 0 ) {
			wp_fusion()->user->apply_tags( $apply_tags, $wp_user->ID );
		}

	}

	/**
	 * Apply tag when the purchase is invoked
	 *
	 * @param \SureCart\Models\Integration $integration The integrations.
	 * @param \WP_User                     $wp_user The user.
	 *
	 * @return boolean|void Returns true if the tag is applied.
	 */
	public function onPurchaseInvoked( $integration, $wp_user ) {
		$this->onPurchaseCreated( $integration, $wp_user );
	}

	/**
	 * Remove tag when the purchase is revoked.
	 *
	 * @param \SureCart\Models\Integration $integration The integrations.
	 * @param \WP_User                     $wp_user The user.
	 *
	 * @return boolean|void Returns true if the tag is removed.
	 */
	public function onPurchaseRevoked( $integration, $wp_user ) {
		$atts        = $integration->getAttributes();
		$remove_tags = array( $atts['integration_id'] );
		if ( empty( $remove_tags ) ) {
			return;
		}

		if ( intval( $wp_user->ID ) !== 0 ) {
			wp_fusion()->user->remove_tags( $remove_tags, $wp_user->ID );
		}
	}

}