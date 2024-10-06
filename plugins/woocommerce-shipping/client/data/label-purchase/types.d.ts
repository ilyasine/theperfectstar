import { RATES_FETCH_FAILED, RATES_FETCHED } from './action-types';
import {
	Action,
	CamelCaseType,
	CustomPackageResponse,
	Destination,
	Label,
	LabelPurchaseError,
	LabelShipmentIdMap,
	OriginAddress,
	RateWithParent,
} from 'types';
import { LABEL_PURCHASE_SUCCESS, LABEL_STATUS_RESOLVED } from './label';
import { PACKAGES_UPDATE, PACKAGES_UPDATE_ERROR } from './packages';

export interface LabelPurchaseSuccessAction extends Action {
	type: LABEL_PURCHASE_SUCCESS;
	payload: {
		label: Record< string, Label[] >;
		selectedRates: Record< string, RateWithParent >;
		selectedHazmat: Record<
			string,
			{
				isHazmat: boolean;
				category: string;
			}
		>;
		selectedOrigins: Record< string, OriginAddress >;
		selectedDestinations: Record< string, Destination >;
	};
	error?: Record< string, LabelPurchaseError >;
}

export interface LabelStatusResolvedAction extends Action {
	type: LABEL_STATUS_RESOLVED;
	payload?: Label;
	error?: unknown;
}

export interface RatesFetchedAction extends Action {
	type: RATES_FETCHED;
	payload: unknown;
}

export interface RatesFetchFailedAction extends Action {
	type: RATES_FETCH_FAILED;
	payload: Record< string, string >;
}

export interface PackageUpdateAction extends Action {
	type: PACKAGES_UPDATE;
	payload: {
		custom: CamelCaseType< CustomPackageResponse >[];
		predefined: Record< string, string[] >;
	};
}

export interface PackageUpdateFailedAction< ET = Record< string, string > >
	extends Action {
	type: PACKAGES_UPDATE_ERROR;
	payload: ET;
}

export interface StageLabelsNewShipmentIdsAction extends Action {
	type: LABEL_STAGE_NEW_SHIPMENT_IDS;
	payload: LabelShipmentIdMap;
}

export type LabelPurchaseActions =
	| ReturnType< typeof resetAddressNormalizationResponse >
	| LabelPurchaseSuccessAction
	| RatesFetchFailedAction
	| PackageUpdateAction
	| PackageUpdateFailedAction
	| RatesFetchedAction
	| StageLabelsNewShipmentIdsAction;
