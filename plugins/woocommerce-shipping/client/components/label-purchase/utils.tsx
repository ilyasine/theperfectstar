import { __, _n, sprintf } from '@wordpress/i18n';
import React from 'react';

export const getShipmentTitle = (
	index: string | number,
	totalCount: number
) =>
	sprintf(
		// translators: %1$d is the shipment number, %2$d is the total number of shipments
		__( 'Shipment %1$d/%2$d' ),
		parseInt( `${ index }`, 10 ) + 1,
		totalCount
	);

export const getShipmentSummaryText = (
	orderFulfilled: boolean,
	purchasedLabelProductCount: number,
	totalProductCount: number
) => {
	let labelContent: JSX.Element;
	if ( orderFulfilled ) {
		labelContent = (
			<em>
				{ sprintf(
					// translators: %1$d: number of items
					_n(
						'%1$d item was fulfilled.',
						'%1$d items were fulfilled.',
						totalProductCount,
						'woocommerce-shipping'
					),
					totalProductCount
				) }
			</em>
		);
	} else if (
		purchasedLabelProductCount < totalProductCount &&
		purchasedLabelProductCount > 0
	) {
		labelContent = (
			<em>
				{ sprintf(
					// translators: %1$d: number of items fulfilled
					_n(
						'%1$d item was fulfilled, ',
						'%1$d items were fulfilled, ',
						purchasedLabelProductCount,
						'woocommerce-shipping'
					),
					purchasedLabelProductCount
				) }
				{ sprintf(
					// translators: %1$d: number of items to be fulfilled
					_n(
						'%d item still requires fulfillment.',
						'%d items still require fulfillment.',
						totalProductCount - purchasedLabelProductCount,
						'woocommerce-shipping'
					),
					totalProductCount - purchasedLabelProductCount
				) }
			</em>
		);
	} else {
		labelContent = (
			<em>
				{ sprintf(
					// translators: %d: number of items
					_n(
						'%d item is ready to be fulfilled',
						'%d items are ready to be fulfilled',
						totalProductCount,
						'woocommerce-shipping'
					),
					totalProductCount
				) }
			</em>
		);
	}
	return labelContent;
};
