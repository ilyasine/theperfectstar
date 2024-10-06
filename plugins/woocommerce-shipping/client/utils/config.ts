import { WCShippingConfig } from 'types';

export const getConfig = (): WCShippingConfig => window.WCShipping_Config || {};

export const getWeightUnit = () => {
	return getConfig().shippingLabelData.storeOptions.weight_unit;
};

export const getCurrencySymbol = () => {
	return getConfig().shippingLabelData.storeOptions.currency_symbol;
};

export const getDimensionsUnit = () => {
	return getConfig().shippingLabelData.storeOptions.dimension_unit;
};

export const getAccountSettings = ( { accountSettings } = getConfig() ) =>
	accountSettings;

export const getSelectedRates = () =>
	getConfig().shippingLabelData.storedData.selected_rates;

export const getSelectedHazmat = () =>
	getConfig().shippingLabelData.storedData.selected_hazmat;

export const getCustomsInformation = () =>
	getConfig().shippingLabelData.storedData.customs_information;
