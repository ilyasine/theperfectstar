import React from 'react';
import {
	__experimentalText as Text,
	Flex,
	MenuItem,
} from '@wordpress/components';
import { __, _x, sprintf } from '@wordpress/i18n';
import { addressToString } from 'utils';
import { OriginAddress } from 'types';
import { AddressVerifiedIcon } from '../../address-verified-icon';
import { useLabelPurchaseContext } from '../context';
import { check, Icon } from '@wordpress/icons';
import { Link } from '@woocommerce/components';

interface ShipFromOptionProps {
	address: OriginAddress;
	close: () => void;
	isSelected: boolean;
	editAddress: ( address: OriginAddress ) => void;
}

export const ShipFromOption = ( {
	address,
	close,
	isSelected,
	editAddress,
}: ShipFromOptionProps ) => {
	const {
		shipment: { setOrigin },
	} = useLabelPurchaseContext();

	const onEditClick = ( e: MouseEvent ) => {
		e.stopPropagation();
		e.preventDefault();
		close();
		editAddress( address );
	};
	return (
		<MenuItem
			onClick={ () => {
				if ( address.isVerified ) {
					setOrigin( address.id );
				} else {
					editAddress( address );
				}
				close();
			} }
			aria-label={ addressToString( address ) }
			isSelected={ isSelected }
			role="menuitemradio"
			suffix={ isSelected && <Icon icon={ check } size={ 20 } /> }
		>
			<Flex gap={ 2 } direction="column">
				<sub>
					{ ! address.defaultAddress
						? address.name
						: sprintf(
								// translators: %1$s a user defined name for the address.
								_x(
									'%1$s (Default)',
									'Origin address',
									'woocommerce-shipping'
								),
								address.name
						  ) }
				</sub>
				<Text truncate={ false }>{ addressToString( address ) }</Text>
				<Link onClick={ onEditClick } type="button" href="#">
					{ __( 'Edit', 'woocommerce-shipping' ) }
				</Link>
				{ Boolean( address.isVerified ) === false && (
					<AddressVerifiedIcon
						isVerified={ false }
						errorMessage={ __(
							'Verify to use this address',
							'woocommerce-shipping'
						) }
					/>
				) }
			</Flex>
		</MenuItem>
	);
};
