import React from 'react';
import { __ } from '@wordpress/i18n';
import { DropdownMenu, Flex, MenuGroup, Modal } from '@wordpress/components';
import {
	createInterpolateElement,
	useCallback,
	useEffect,
	useRef,
	useState,
} from '@wordpress/element';
import { Link } from '@woocommerce/components';
import { chevronDown, Icon } from '@wordpress/icons';
import { useSelect } from '@wordpress/data';
import {
	addressToString,
	camelCaseKeys,
	formatAddressFields,
	getConfig,
	snakeCaseKeys,
} from 'utils';
import { OriginAddress } from 'types';
import { settingsPageUrl } from '../constants';
import { ShipFromOption } from './ship-from-option';
import { useLabelPurchaseContext } from '../context';
import { AddressStep } from 'components/address-step';
import { AddressVerifiedIcon } from 'components/address-verified-icon';
import { addressStore } from 'data/address';

interface ShipFromSelectProps {
	disabled: boolean;
}

export const ShipFromSelect = ( { disabled }: ShipFromSelectProps ) => {
	const origins = useSelect(
		( select ) => select( addressStore ).getOriginAddresses(),
		[]
	);
	const {
		shipment: { getOrigin, setOrigin },
		rates: { updateRates },
	} = useLabelPurchaseContext();
	const prevOrigin = useRef( getOrigin() );

	useEffect( () => {
		if ( prevOrigin.current?.id !== getOrigin()?.id ) {
			updateRates();
		}
		prevOrigin.current = getOrigin();
	}, [ getOrigin, updateRates, prevOrigin ] );

	const [ addressForEdit, openAddressForEdit ] = useState<
		OriginAddress | false
	>( false );

	const onUpdateCallback = useCallback(
		( address: OriginAddress ) => {
			setOrigin( address.id );
			updateRates();
		},
		[ setOrigin, updateRates ]
	);

	if ( origins.length < 1 ) {
		return createInterpolateElement(
			__(
				'You have no verified origin address, <a>visit settings</a> to add one',
				'woocommerce-shipping'
			),
			{
				a: (
					<Link href={ settingsPageUrl } type="internal">
						{ __( 'visit settings', 'woocommerce-shipping' ) }
					</Link>
				),
			}
		);
	}

	const noValidAddress = origins.every( ( address ) => ! address.isVerified );
	return (
		<>
			<Flex
				direction="column"
				gap={ 0 }
				justify="flex-start"
				align="flex-start"
			>
				<DropdownMenu
					label={ __(
						'Choose a ship from address',
						'woocommerce-shipping'
					) }
					text={ '' }
					icon={
						<>
							<span>{ addressToString( getOrigin() ) }</span>
							<Icon icon={ chevronDown } />
						</>
					}
					className="origin-address-dropdown"
					variant="ship-from"
					disabled={ disabled }
				>
					{ ( { onClose } ) => (
						<MenuGroup className="origin-address-options">
							{ origins.map( ( address ) => (
								<ShipFromOption
									key={ address.id }
									close={ onClose }
									address={ address }
									isSelected={
										address.id === getOrigin()?.id
									}
									editAddress={ openAddressForEdit }
								/>
							) ) }
						</MenuGroup>
					) }
				</DropdownMenu>
				{ noValidAddress && (
					<AddressVerifiedIcon
						isVerified={ false }
						onClick={ () => {
							const org = getOrigin();
							if ( org ) {
								openAddressForEdit( org );
							}
						} }
						errorMessage={ __(
							'Validate your address',
							'woocommerce-shipping'
						) }
					/>
				) }
			</Flex>

			{ addressForEdit && (
				<Modal
					className="edit-address-modal"
					onRequestClose={ () => openAddressForEdit( false ) }
					focusOnMount
					shouldCloseOnClickOutside={ false }
					title={ __(
						'Edit origin address',
						'woocommerce-shipping'
					) }
				>
					<AddressStep
						type={ 'origin' }
						address={ camelCaseKeys(
							formatAddressFields(
								snakeCaseKeys( addressForEdit )
							)
						) }
						onCompleteCallback={ () => openAddressForEdit( false ) }
						onUpdateCallback={ onUpdateCallback }
						orderId={ `${ getConfig().order.id }` }
						isAdd={ false }
					/>
				</Modal>
			) }
		</>
	);
};
