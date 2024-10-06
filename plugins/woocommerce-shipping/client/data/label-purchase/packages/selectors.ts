import { uniqBy } from 'lodash';
import { camelCaseKeys, getCarrierPackages } from 'utils';
import { LabelPurchaseState } from '../../types';
import { Package } from 'types';

export const getPredefinedPackages = (
	state: LabelPurchaseState,
	carrierId?: string
) => {
	return carrierId
		? state.packages.predefined?.[ carrierId ] ?? []
		: state.packages.predefined ?? [];
};

export const getPackageUpdateErrors = (
	state: LabelPurchaseState,
	packageType = 'custom'
) => {
	return state.packages.errors?.[ packageType ] ?? {};
};

const getCustomPackages = ( state: LabelPurchaseState ) => {
	return ( state.packages.custom ?? [] )
		.map( camelCaseKeys )
		.map( ( pkg, index ) => ( {
			...pkg,
			id: `${ pkg.id }-custom_${ index }`,
		} ) );
};

export const getSavedPackages = ( state: LabelPurchaseState ): Package[] => {
	return [
		...uniqBy(
			Object.values(
				getCarrierPackages( getPredefinedPackages( state ) )
			).flat(),
			'id'
		),
		...getCustomPackages( state ),
	];
};
