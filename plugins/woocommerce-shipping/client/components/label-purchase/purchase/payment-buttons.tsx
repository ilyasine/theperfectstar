import React, { MouseEventHandler } from 'react';
import {
	__experimentalSpacer as Spacer,
	Button,
	CheckboxControl,
	Flex,
	FlexBlock,
	Notice,
} from '@wordpress/components';
import {
	createInterpolateElement,
	useCallback,
	useEffect,
	useLayoutEffect,
	useState,
} from '@wordpress/element';
import { Link } from '@woocommerce/components';
import { __, sprintf } from '@wordpress/i18n';
import {
	hasPaymentMethod,
	hasSelectedPaymentMethod,
	getAddPaymentMethodURL,
} from 'utils';
import { CreditCardButton } from './credit-card-button';
import { settingsPageUrl } from '../constants';
import { useLabelPurchaseContext } from '../context';
import { getShipmentTitle } from '../utils';
import { PaperSizeSelector } from '../paper-size';
import { Label, LabelPurchaseError, Order } from 'types';
import { uniq } from 'lodash';
import { dispatch, select, useSelect } from '@wordpress/data';
import { labelPurchaseStore } from 'data/label-purchase';
import { EssentialDetails } from '../essential-details';
import { recordEvent } from 'utils/tracks';
import { LABEL_PURCHASE_STATUS } from 'data/constants';

interface PaymentButtonsProps {
	order: Order;
}

export const PaymentButtons = ( { order }: PaymentButtonsProps ) => {
	const {
		shipment: { shipments, currentShipmentId, getOrigin },
		labels: {
			selectedLabelSize,
			requestLabelPurchase,
			isPurchasing,
			isUpdatingStatus,
			hasPurchasedLabel,
			getCurrentShipmentLabel,
		},
		rates: { getSelectedRate },
		account: { accountSettings, canPurchase },
	} = useLabelPurchaseContext();
	const [ errors, setErrors ] = useState< LabelPurchaseError | null >( null );
	const [ markOrderAsCompleted, setMarkOrderAsCompleted ] = useState( false );

	const resetErrors = () => {
		setErrors( null );
	};

	const purchaseAPIErrors = useSelect(
		( s ) =>
			s( labelPurchaseStore ).getPurchaseAPIError( currentShipmentId ),
		[ currentShipmentId ]
	);

	useEffect( () => {
		if ( purchaseAPIErrors ) {
			setErrors( purchaseAPIErrors );
		}
	}, [ purchaseAPIErrors ] );

	useLayoutEffect( () => {
		if ( isPurchasing ) {
			document
				.querySelector(
					'.label-purchase-modal .components-modal__content'
				)
				?.scrollTo( {
					left: 0,
					top: 0,
					behavior: 'smooth',
				} );
		}
	}, [ isPurchasing ] );

	const shipmentsCount = Object.keys( shipments ).length;
	const purchaseButtonLabel =
		shipmentsCount > 1
			? sprintf(
					// translators: %s is the shipment title as Shipment 1/2, Shipment 2/2, etc.
					__( 'Purchase %s', 'woocommerce-shipping' ),
					getShipmentTitle( currentShipmentId, shipmentsCount )
			  )
			: __( 'Purchase label', 'woocommerce-shipping' );

	const addCardButtonDescription = (
		onAddCard: MouseEventHandler< HTMLAnchorElement >
	) =>
		createInterpolateElement(
			__(
				'To print this shipping label, <a>add a credit card to your account</a>.',
				'woocommerce-shipping'
			),
			{
				a: (
					<Link
						onClick={ onAddCard }
						type="external"
						href="#"
						role="button"
					>
						{ ' ' }
					</Link>
				),
			}
		);
	const chooseCardButtonDescription = (
		onChooseCard: MouseEventHandler< HTMLAnchorElement >
	) =>
		createInterpolateElement(
			__(
				'To print this shipping label, <a>choose a credit card to add to your account</a>.',
				'woocommerce-shipping'
			),
			{
				a: (
					<Link
						onClick={ onChooseCard }
						type="internal"
						href="#"
						role="button"
					>
						{ ' ' }
					</Link>
				),
			}
		);

	const updateOrderStatus = useCallback(
		async ( label?: Label ) => {
			if (
				markOrderAsCompleted &&
				label?.status === LABEL_PURCHASE_STATUS.PURCHASED
			) {
				await dispatch( labelPurchaseStore ).updateOrderStatus( {
					orderId: `${ order.id }`,
					status: 'completed',
				} );
			}
		},
		[ markOrderAsCompleted, order.id ]
	);

	useEffect( () => {
		if ( ! isPurchasing && ! isUpdatingStatus && ! errors ) {
			( async () => {
				await updateOrderStatus( getCurrentShipmentLabel() );
			} )();
		}
	}, [
		isPurchasing,
		isUpdatingStatus,
		errors,
		getCurrentShipmentLabel,
		updateOrderStatus,
	] );

	const purchaseLabel = async () => {
		if ( hasPurchasedLabel( false ) ) {
			return;
		}

		const tracksProperties = {
			order_product_count: order.line_items.length,
			shipment_product_count: shipments[ currentShipmentId ].length,
			order_shipping_total: order.total_shipping,
			order_total: order.total,
			order_shipping_method: order.shipping_methods,
			mark_order_as_completed: markOrderAsCompleted,
			selected_label_size: selectedLabelSize.key,
			order_destination_country: order.shipping_address.country,
			carrier_id: getSelectedRate()?.rate.carrierId,
			rate: getSelectedRate()?.rate.rate,
			list_rate: getSelectedRate()?.rate.listRate,
			retail_rate: getSelectedRate()?.rate.retailRate,
			service_id: getSelectedRate()?.rate.serviceId,
		};
		recordEvent(
			'label_purchase_purchase_shipping_label_clicked',
			tracksProperties
		);
		resetErrors();
		try {
			await requestLabelPurchase( order.id );
			const currentShipmentLabel =
				select( labelPurchaseStore ).getPurchasedLabel(
					currentShipmentId
				);
			if ( ! errors ) {
				await updateOrderStatus( currentShipmentLabel );
			}
		} catch ( e ) {
			setErrors( e as unknown as LabelPurchaseError );
		}
	};

	const markAsCompletedCheckboxHandler = () => {
		if ( markOrderAsCompleted ) {
			setMarkOrderAsCompleted( false );
		} else {
			setMarkOrderAsCompleted( true );
		}
	};

	return (
		<>
			<Flex className="purchase-label-buttons" direction="column">
				{ canPurchase() && (
					<>
						<Flex>
							<PaperSizeSelector
								disabled={ hasPurchasedLabel( false ) }
							/>
							<Button
								variant="primary"
								disabled={
									! getSelectedRate() ||
									isPurchasing ||
									isUpdatingStatus ||
									hasPurchasedLabel( false ) ||
									! getOrigin().isVerified
								}
								onClick={ purchaseLabel }
								isBusy={ isPurchasing }
								aria-disabled={
									! getSelectedRate() ||
									isPurchasing ||
									hasPurchasedLabel( false ) ||
									! getOrigin().isVerified
								}
							>
								{ purchaseButtonLabel }
							</Button>
						</Flex>
						<FlexBlock>
							<EssentialDetails />
						</FlexBlock>
						{ ! hasPurchasedLabel( false ) && (
							<Flex>
								<CheckboxControl
									label={ __(
										'After purchasing a label, mark this order as complete and notify the customer',
										'woocommerce-shipping'
									) }
									onChange={ markAsCompletedCheckboxHandler }
									checked={ markOrderAsCompleted }
									disabled={ isPurchasing }
									aria-disabled={ isPurchasing }
									className="purchase-label-mark-order-complete"
								/>
							</Flex>
						) }
					</>
				) }
				{ ! hasPaymentMethod( { accountSettings } ) && (
					<CreditCardButton
						url={ getAddPaymentMethodURL() }
						buttonLabel={ __(
							'Add credit card',
							'woocommerce-shipping'
						) }
						buttonDescription={ addCardButtonDescription }
					/>
				) }
				{ hasPaymentMethod( { accountSettings } ) &&
					! hasSelectedPaymentMethod( { accountSettings } ) && (
						<CreditCardButton
							url={ settingsPageUrl }
							buttonLabel={ __(
								'Choose credit card',
								'woocommerce-shipping'
							) }
							buttonDescription={ chooseCardButtonDescription }
						/>
					) }
			</Flex>
			<Spacer />
			{ errors && Object.keys( errors ).length > 0 && (
				<Notice
					status="error"
					actions={ uniq( errors.actions ) }
					onDismiss={ resetErrors }
				>
					{ uniq( errors.message ).map( ( m, index ) => (
						<p key={ index }>{ m }</p>
					) ) }
				</Notice>
			) }
		</>
	);
};
