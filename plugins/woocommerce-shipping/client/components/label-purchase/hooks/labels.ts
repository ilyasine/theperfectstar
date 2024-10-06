import { useCallback, useEffect, useState } from '@wordpress/element';
import { dispatch, select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { mapValues } from 'lodash';
import { Label, LabelPurchaseError, PDFJson } from 'types';
import { LABEL_PURCHASE_STATUS } from 'data/constants';
import {
	getCurrentOrder,
	getPaymentSettings,
	getPDFFileName,
	getPrintURL,
	printDocument,
} from 'utils';
import { labelPurchaseStore } from 'data/label-purchase';
import { addressStore } from 'data/address';
import { usePackageState } from './packages';
import { useShipmentState } from './shipment';
import { useRatesState } from './rates';
import { TIME_TO_WAIT_TO_CHECK_PURCHASED_LABEL_STATUS_MS } from '../constants';
import { getPaperSizes } from '../label';
import { useHazmatState } from './hazmat';
import { useCustomsState } from './customs';
import { CUSTOM_BOX_ID_PREFIX } from '../packages';

interface UseLabelsStateProps {
	currentShipmentId: string;
	getPackageForRequest: ReturnType<
		typeof usePackageState
	>[ 'getPackageForRequest' ];
	totalWeight: number;
	getCurrentShipment: ReturnType<
		typeof useShipmentState
	>[ 'getCurrentShipment' ];
	getSelectedRate: ReturnType< typeof useRatesState >[ 'getSelectedRate' ];
	getShipmentHazmat: ReturnType<
		typeof useHazmatState
	>[ 'getShipmentHazmat' ];
	updateRates: ReturnType< typeof useRatesState >[ 'updateRates' ];
	getOrigin: ReturnType< typeof useShipmentState >[ 'getOrigin' ];
	customs: ReturnType< typeof useCustomsState >;
	shipments: ReturnType< typeof useShipmentState >[ 'shipments' ];
}

const handlePurchaseException = ( e: LabelPurchaseError ) =>
	Promise.reject( {
		cause: 'purchase_error',
		message: [
			...( e.cause !== 'status_error'
				? [ __( 'Error purchasing label.', 'woocommerce-shipping' ) ]
				: [] ),
			...( Array.isArray( e.message )
				? e.message
				: [ e?.message ?? '' ] ),
		],
		actions: [ ...( e.actions ?? [] ) ],
	} );

export function useLabelsState( {
	currentShipmentId,
	getPackageForRequest,
	getCurrentShipment,
	getSelectedRate,
	totalWeight,
	getShipmentHazmat,
	updateRates,
	getOrigin,
	customs: { maybeApplyCustomsToPackage, getCustomsState },
	shipments,
}: UseLabelsStateProps ) {
	const order = getCurrentOrder();
	const getShipmentLabel = useCallback(
		( shipmentId = currentShipmentId ) =>
			select( labelPurchaseStore ).getPurchasedLabel( shipmentId ),
		[ currentShipmentId ]
	);

	const getSelectedOrigin = useCallback(
		( shipmentId = currentShipmentId ) =>
			select( labelPurchaseStore ).getLabelOrigins( shipmentId ),
		[ currentShipmentId ]
	);

	const getSelectedDestination = useCallback(
		( shipmentId = currentShipmentId ) =>
			select( labelPurchaseStore ).getLabelDestinations( shipmentId ),
		[ currentShipmentId ]
	);

	const currentShipmentLabel = getShipmentLabel();

	const purchasedLabels = select( labelPurchaseStore ).getPurchasedLabels();
	const country = select( addressStore ).getStoreOrigin()?.country;

	const paperSizes = getPaperSizes( country );
	const [ labels, setLabels ] = useState<
		Record< string, Label | undefined >
	>(
		purchasedLabels ?? {
			0: currentShipmentLabel,
		}
	);

	useEffect( () => {
		if ( ! currentShipmentLabel ) {
			return;
		}
		setLabels( ( prevState ) => ( {
			...prevState,
			[ currentShipmentId ]: currentShipmentLabel,
		} ) );
	}, [ currentShipmentLabel, currentShipmentId ] );

	const [ selectedLabelSize, setLabelSize ] = useState(
		paperSizes.find(
			( { key } ) => key === getPaymentSettings().paperSize
		) ??
			/**
			 * We've slightly changed the available paper sizes in WCS vs WCS&T, that's why there is a chance the paper size
			 * selected in settings is not available anymore, so we'll default to the first available paper size
			 */
			paperSizes[ 0 ]
	);

	const getCurrentShipmentLabel = useCallback(
		( shipmentId = currentShipmentId ) => labels[ shipmentId ],
		[ labels, currentShipmentId ]
	);

	const [ isPurchasing, setIsPurchasing ] = useState( false );
	const [ isUpdatingStatus, setIsUpdatingStatus ] = useState( false );
	const [ isPrinting, setIsPrinting ] = useState( false );
	const [ isRefunding, setIsRefunding ] = useState( false );

	const maybeUpdateRates = useCallback( () => {
		if (
			! currentShipmentLabel ||
			currentShipmentLabel.status === LABEL_PURCHASE_STATUS.PURCHASE_ERROR
		) {
			// The purchase might not be successful yet.
			updateRates(); // Update rates so that the same shipment id is not used again
		}
	}, [ currentShipmentLabel, updateRates ] );
	const fetchLabelStatus = useCallback(
		async (
			labelId: number,
			resolvers?: {
				resolve?: () => void;
				reject?: ( error?: LabelPurchaseError ) => void;
			}
		): Promise< void | LabelPurchaseError > => {
			const { resolve, reject } = resolvers ?? {};
			setIsUpdatingStatus( true );
			try {
				await dispatch( labelPurchaseStore ).fetchLabelStatus(
					order.id,
					labelId
				);
			} catch ( e ) {
				setIsUpdatingStatus( false );
				maybeUpdateRates();
				return ( reject ?? Promise.reject< LabelPurchaseError > )?.( {
					cause: 'status_error',
					message: [
						__(
							'Error fetching label status. Please check the purchase status later.',
							'woocommerce-shipping'
						),
					],
				} );
			}

			const label =
				select( labelPurchaseStore ).getPurchasedLabel(
					currentShipmentId
				);

			if ( ! label ) {
				setIsUpdatingStatus( false );
				return ( resolve ?? Promise.resolve )();
			}

			if ( label.status === LABEL_PURCHASE_STATUS.PURCHASE_ERROR ) {
				setIsUpdatingStatus( false );
				maybeUpdateRates();
				return ( reject ?? Promise.reject< LabelPurchaseError > )?.( {
					cause: 'status_error',
					message: [
						label.error
							? label.error
							: __(
									'Error fetching label status. Please check the purchase status later.',
									'woocommerce-shipping'
							  ),
					],
				} );
			}

			if ( label.status === LABEL_PURCHASE_STATUS.PURCHASE_IN_PROGRESS ) {
				setTimeout( () => {
					try {
						fetchLabelStatus( labelId, resolvers );
						// @ts-ignore
					} catch ( e: LabelPurchaseError ) {
						setIsUpdatingStatus( false );
						return (
							reject ?? Promise.reject< LabelPurchaseError >
						)?.( e );
					}
				}, TIME_TO_WAIT_TO_CHECK_PURCHASED_LABEL_STATUS_MS );
			} else {
				setLabels( ( prevLabels ) => ( {
					...prevLabels,
					[ currentShipmentId ]: label,
				} ) );
				setIsUpdatingStatus( false );
				return ( resolve ?? Promise.resolve )();
			}
		},
		[ order.id, currentShipmentId, setLabels, maybeUpdateRates ]
	);

	const requestLabelPurchase = useCallback(
		async ( orderId: number ): Promise< void | LabelPurchaseError > => {
			const pkg = getPackageForRequest();
			const selectedRate = getSelectedRate();
			if ( ! pkg || ! selectedRate ) {
				return;
			}

			setIsPurchasing( true );
			const {
				isLetter,
				id = CUSTOM_BOX_ID_PREFIX,
				length,
				width,
				height,
			} = pkg;

			const {
				serviceId,
				carrierId,
				shipmentId,
				title: serviceName,
			} = selectedRate.rate;
			const dimensions = mapValues<
				{
					length: string;
					width: string;
					height: string;
				},
				number
			>( { length, width, height }, parseFloat );

			const requestPackage = [
				maybeApplyCustomsToPackage( {
					id: currentShipmentId,
					box_id: id,
					...dimensions,
					is_letter: isLetter,
					shipment_id: shipmentId,
					service_id: serviceId,
					carrier_id: carrierId,
					service_name: serviceName,
					products: getCurrentShipment().map(
						( { product_id } ) => product_id
					),
					weight: totalWeight,
					rate_id: selectedRate.rate.rateId,
				} ),
			];
			try {
				await dispatch( labelPurchaseStore ).purchaseLabel(
					orderId,
					requestPackage,
					currentShipmentId,
					{
						[ `shipment_${ currentShipmentId }` ]: selectedRate,
					},
					{
						[ `shipment_${ currentShipmentId }` ]:
							getShipmentHazmat(),
					},
					getOrigin(),
					{
						[ `shipment_${ currentShipmentId }` ]:
							getCustomsState(),
					}
				);
				// @ts-ignore
			} catch ( e: LabelPurchaseError ) {
				setIsPurchasing( false );
				maybeUpdateRates();
				return handlePurchaseException( e );
			}

			select( labelPurchaseStore ).getPurchasedLabel( currentShipmentId );

			setIsPurchasing( false );
		},
		[
			getPackageForRequest,
			currentShipmentId,
			getCurrentShipment,
			getSelectedRate,
			totalWeight,
			setIsPurchasing,
			getShipmentHazmat,
			maybeUpdateRates,
			getOrigin,
			maybeApplyCustomsToPackage,
			getCustomsState,
		]
	);

	const printLabel = useCallback(
		async ( isReprint = false ): Promise< void | LabelPurchaseError > => {
			setIsPrinting( true );
			const label =
				select( labelPurchaseStore ).getPurchasedLabel(
					currentShipmentId
				);

			if ( ! label ) {
				return Promise.reject( {
					cause: 'print_error',
					message: [
						__( 'No label to print.', 'woocommerce-shipping' ),
					],
				} );
			}
			const path = getPrintURL( selectedLabelSize.key, label.labelId );
			try {
				const pdfJson = await apiFetch< PDFJson >( {
					path,
					method: 'GET',
				} );
				await printDocument(
					pdfJson,
					getPDFFileName( order.id, isReprint )
				);
				// @ts-ignore // can't properly type the error message
			} catch ( e: Error ) {
				setIsPrinting( false );
				return Promise.reject( {
					cause: 'print_error',
					message: [
						__(
							'Error printing label, try to print later.',
							'woocommerce-shipping'
						),
						...( e.message ? [ e.message ] : [] ),
					],
				} );
			}

			setIsPrinting( false );
			return Promise.resolve();
		},
		[ order, selectedLabelSize, setIsPrinting, currentShipmentId ]
	);

	const hasPurchasedLabel = useCallback(
		(
			checkStatus = true,
			excludeRefunded = false,
			shipmentId: string = currentShipmentId
		): boolean => {
			const label = getShipmentLabel( shipmentId );
			if ( excludeRefunded && label?.refund ) {
				return false;
			}

			if ( checkStatus ) {
				return label?.status === LABEL_PURCHASE_STATUS.PURCHASED;
			}

			return (
				// label is purchased if it's not errored
				( label &&
					label.status !== LABEL_PURCHASE_STATUS.PURCHASE_ERROR ) ??
				false
			);
		},
		[ currentShipmentId, getShipmentLabel ]
	);

	const getLabelProductIds = useCallback(
		( shipmentId: string = currentShipmentId ) => {
			const label = getShipmentLabel( shipmentId );
			return label?.productIds ?? [];
		},
		[ currentShipmentId, getShipmentLabel ]
	);

	const updatePurchaseStatus = useCallback(
		async ( labelId: number ) => {
			setIsUpdatingStatus( true );
			await new Promise< Error | void >( ( resolve, reject ) =>
				fetchLabelStatus( labelId, {
					resolve,
					reject,
				} )
			);
			setIsUpdatingStatus( false );
		},
		[ fetchLabelStatus ]
	);
	// If the label is in progress, try to fetch the status and update the state
	useEffect( () => {
		if (
			currentShipmentLabel &&
			currentShipmentLabel.status ===
				LABEL_PURCHASE_STATUS.PURCHASE_IN_PROGRESS &&
			! isUpdatingStatus
		) {
			updatePurchaseStatus( currentShipmentLabel.labelId );
		}
	}, [ currentShipmentLabel, updatePurchaseStatus, isUpdatingStatus ] );

	const refundLabel = useCallback( async () => {
		setIsRefunding( true );
		const label =
			select( labelPurchaseStore ).getPurchasedLabel( currentShipmentId );
		if ( ! label ) {
			setIsRefunding( false );
			return Promise.reject( {
				cause: 'refund_error',
				message: [
					__( 'No label to refund.', 'woocommerce-shipping' ),
				],
			} );
		}
		try {
			const result = await dispatch( labelPurchaseStore ).refundLabel(
				order.id,
				label.labelId
			);
			setIsRefunding( false );

			return Promise.resolve( result );
		} catch ( e ) {
			setIsRefunding( false );
			return Promise.reject( e );
		}
	}, [ currentShipmentId, setIsRefunding, order ] );

	const hasRequestedRefund = useCallback(
		( shipmentId: string = currentShipmentId ) => {
			const label = select( labelPurchaseStore ).getRefundedLabel(
				shipmentId ?? currentShipmentId
			);

			return Boolean( label?.refund );
		},
		[ currentShipmentId ]
	);

	const getShipmentsWithoutLabel = useCallback(
		() =>
			Object.keys( shipments ).filter(
				( shipmentId ) => ! hasPurchasedLabel( true, true, shipmentId )
			),
		[ hasPurchasedLabel, shipments ]
	);

	return {
		getCurrentShipmentLabel,
		requestLabelPurchase,
		hasPurchasedLabel,
		selectedLabelSize,
		setLabelSize,
		printLabel,
		isPurchasing,
		isUpdatingStatus,
		isPrinting,
		isRefunding,
		paperSizes,
		updatePurchaseStatus,
		refundLabel,
		hasRequestedRefund,
		getLabelProductIds,
		getSelectedOrigin,
		getSelectedDestination,
		getShipmentsWithoutLabel,
	};
}
