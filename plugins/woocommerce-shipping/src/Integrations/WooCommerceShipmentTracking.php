<?php
/**
 * WooCommerceShipmentTracking integration file.
 *
 * @package Automattic\WCShipping\Integrations
 */

namespace Automattic\WCShipping\Integrations;

/**
 * Class WooCommerceShipmentTracking
 *
 * @package Automattic\WCShipping\Integrations
 */
class WooCommerceShipmentTracking {

	/**
	 * Add a WooCommerce Shipment Tracking compatible tracking number to an order.
	 * We will use the WC Shipment Tracking functions if the plugin is installed
	 * otherwise we will use our own custom implementation so data is present for
	 * when the plugin is installed, but in the same format.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $tracking_number Tracking number.
	 * @param string $carrier Carrier ID.
	 *
	 * @return void
	 */
	public static function add_tracking_number_to_order( $order_id, $tracking_number, $carrier ) {

		// To avoid conflicts with the WC Shipment Tracking carriers/providers and to make life easier we will use custom defined ones.
		$tracking_url = self::get_tracking_url( $carrier ) . $tracking_number;

		if ( self::is_st_installed() ) {
			self::add_tracking_using_st_installed_functions( $order_id, $tracking_number, $carrier, $tracking_url );
			return;
		}

		// If we made it till here then the WC Shipment Tracking plugin is not installed and we will use our own custom implementation.
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$tracking_item = array(
			'tracking_provider'        => '',
			'custom_tracking_provider' => wc_clean( $carrier ),
			'tracking_number'          => wc_clean( $tracking_number ),
			'custom_tracking_link'     => wc_clean( $tracking_url ),
		);
		// Generate a unique key for the tracking item.
		$key                          = md5( "{$tracking_item['custom_tracking_provider']}-{$tracking_item['tracking_number']}" . microtime() );
		$tracking_item['tracking_id'] = $key;

		$tracking_items   = self::get_st_tracking_items( $order_id );
		$tracking_items[] = $tracking_item;

		/**
		 * Filter the tracking items before adding it into order meta.
		 * We will use the same filter used in WC Shipment Tracking to keep integrations working.
		 *
		 * @param array $tracking_items List of tracking item.
		 * @param array $tracking_item  New tracking item.
		 * @param int   $order_id       Order ID.
		 *
		 * @since 1.0.0
		 */
		$tracking_items = apply_filters( 'wcshipping_tracking_before_add_tracking_items', $tracking_items, $tracking_item, $order_id );

		self::save_st_tracking_items( $order_id, $tracking_items );
	}

	/**
	 * Save the tracking items to the order using the WC Shipment Tracking format.
	 *
	 * @param int   $order_id Order ID.
	 * @param array $tracking_items Tracking items.
	 *
	 * @return void
	 */
	public static function save_st_tracking_items( $order_id, $tracking_items ) {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		// Always re-index the array.
		$tracking_items = array_values( $tracking_items );

		$order->update_meta_data( '_wc_shipment_tracking_items', $tracking_items );
		$order->save();
	}

	/**
	 * Gets all tracking items from the post meta array for an order.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array Tracking items
	 */
	public static function get_st_tracking_items( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order instanceof \WC_Order ) {
			return array();
		}

		$tracking_items = $order->get_meta( '_wc_shipment_tracking_items' );

		if ( is_array( $tracking_items ) ) {
			return $tracking_items;
		} else {
			return array();
		}
	}

	/**
	 * Check if WooCommerce Shipment Tracking is installed.
	 *
	 * @return bool
	 */
	public static function is_st_installed() {
		return class_exists( 'WC_Shipment_Tracking' ) || function_exists( 'wc_st_add_tracking_number' );
	}

	/**
	 * Add tracking number using WC Shipment Tracking function, if the plugin is installed.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $tracking_number Tracking number.
	 * @param string $carrier Carrier ID.
	 * @param string $tracking_url Custom Tracking URL.
	 *
	 * @return void
	 */
	public static function add_tracking_using_st_installed_functions( $order_id, $tracking_number, $carrier, $tracking_url ) {
		if ( ! self::is_st_installed() ) {
			return;
		}

		wc_st_add_tracking_number( $order_id, $tracking_number, $carrier, null, $tracking_url );
	}

	/**
	 * Get the tracking URL for a carrier based on their ID.
	 *
	 * @param string $carrier Carrier ID.
	 *
	 * @return string
	 */
	public static function get_tracking_url( $carrier ) {
		$tracking_urls = array(
			'ups'        => 'https://www.ups.com/track?tracknum=',
			'usps'       => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=',
			'fedex'      => 'https://www.fedex.com/apps/fedextrack/?tracknumbers=',
			'dhlexpress' => 'https://www.dhl.com/en/express/tracking.html?AWB=',
		);

		return isset( $tracking_urls[ $carrier ] ) ? $tracking_urls[ $carrier ] : '';
	}
}