import {
	CUSTOM_BOX_ID_PREFIX,
	PACKAGE_TYPES,
} from '../components/label-purchase/packages';

export interface CustomPackage {
	name: '';
	length: '';
	width: '';
	height: '';
	boxWeight: '0';
	id: CUSTOM_BOX_ID_PREFIX;
	type: PACKAGE_TYPES;
}
