import React, { useEffect, useRef, JSX } from 'react';
import { Link, useFormContext } from '@woocommerce/components';
import type { InputProps } from '@woocommerce/components/build-types/form';

import {
	__experimentalHeading as Heading,
	__experimentalInputControl as InputControl,
	__experimentalSpacer as Spacer,
	CheckboxControl,
	Flex,
	FlexBlock,
	Icon,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { createInterpolateElement } from '@wordpress/element';
import { contentTypes, restrictionTypes } from './constants';
import { ControlledPopover } from '../../controlled-popover';
import { getCountryNames, getWeightUnit } from '../../../utils';
import { useLabelPurchaseContext } from '../context';
import { CustomsItem, CustomsState } from 'types';
import { CUSTOMS_SECTION } from '../essential-details/constants';

export const CustomsForm = (): JSX.Element => {
	const weightUnit = getWeightUnit();
	const {
		storeCurrency: { getCurrencyConfig },
		shipment: { getOrigin },
		customs: { isHSTariffNumberRequired },
		labels: { hasPurchasedLabel },
	} = useLabelPurchaseContext();
	const { symbol: currencySymbol, symbolPosition } = getCurrencyConfig();
	const {
		values: { items, restrictionType, contentsType },
		getInputProps,
		getSelectControlProps,
		getCheckboxControlProps,
		setValue,
		errors,
	} = useFormContext< CustomsState >();
	const contentTypeRef = useRef< HTMLSelectElement >( null );
	const {
		essentialDetails: { focusArea: essentialDetailsFocusArea },
	} = useLabelPurchaseContext();

	useEffect( () => {
		if (
			essentialDetailsFocusArea === CUSTOMS_SECTION &&
			contentTypeRef.current
		) {
			contentTypeRef.current.focus();
		}
	}, [ essentialDetailsFocusArea ] );

	/**
	 * Origin is defined at this point,
	 * we're asking for destination countries as there shouldn't be any limit on the destination country, countries used for origin are limited to US for now
	 */
	const countryNames = getCountryNames( 'destination', getOrigin().country );

	const showOtherRestrictionType = restrictionType === 'other';
	const showOtherContentsType = contentsType === 'other';
	const disable = hasPurchasedLabel( false );

	const getProps = < T extends CustomsState[ keyof CustomsState ] >(
		key: keyof CustomsState | keyof CustomsItem,
		productId?: number,
		defaultValue: T = '' as T
	): Omit< InputProps< CustomsState, T >, 'onBlur' > => {
		if ( productId ) {
			const existingOrderItem =
				items.find( ( { id } ) => id === productId ) ??
				( {} as CustomsItem );
			const index = items.findIndex( ( { id } ) => id === productId );
			const shipmentItem = {
				...existingOrderItem,
				[ key as keyof CustomsItem ]: ( existingOrderItem?.[
					key as keyof CustomsItem
				] ?? defaultValue ) as T,
			};

			/**
			 * The type for errors (FormErrors< CustomsState >) doesn't accommodate for
			 * errors.items which is defined as { items: FormErrors< CustomsItem >[] }
			 * See CustomsValidationInput
			 */
			// @ts-ignore
			const help = errors?.items?.[ index ][ key as keyof CustomsItem ];
			return {
				value:
					( shipmentItem[
						key as keyof CustomsItem
					] as unknown as T ) || defaultValue,
				onChange: ( value ) => {
					const shipmentItems = items.map( ( item ) => {
						if ( item.id === productId ) {
							return {
								...item,
								[ key ]: value,
							};
						}
						return item;
					} );
					setValue( 'items', shipmentItems );
				},
				checked: Boolean( shipmentItem[ key as keyof CustomsItem ] ),
				// Refrain from showing errors on disabled form fields
				help: disable || help,
				className: help && ! disable ? 'has-error' : '',
			};
		}

		const props = getInputProps< T >( key );

		return errors[ key as keyof CustomsState ]
			? {
					...props,
					help:
						( errors[ key as keyof CustomsState ] as string ) ??
						undefined,
					className: 'has-error',
					onChange: ( value ) => {
						props.onChange( value );
					},
			  }
			: {
					...props,
					onChange: ( value ) => {
						props.onChange( value );
					},
			  };
	};

	const symbolProp =
		symbolPosition === 'left'
			? { prefix: currencySymbol }
			: { suffix: currencySymbol };

	return (
		<>
			<Flex gap={ 6 } align="flex-start">
				<FlexBlock>
					<SelectControl
						label={ __( 'Content type', 'woocommerce-shipping' ) }
						{ ...getSelectControlProps( 'contentsType' ) }
						options={ contentTypes }
						disabled={ disable }
						required={ true }
						ref={ contentTypeRef }
					/>
					{ showOtherContentsType && (
						<TextControl
							label={ __(
								'Content details',
								'woocommerce-shipping'
							) }
							required={ true }
							{ ...getProps< string >( 'contentsExplanation' ) }
							disabled={ disable }
						/>
					) }
				</FlexBlock>
				<FlexBlock>
					<SelectControl
						label={ __(
							'Restriction type',
							'woocommerce-shipping'
						) }
						options={ restrictionTypes }
						{ ...getSelectControlProps( 'restrictionType' ) }
						disabled={ disable }
						required={ true }
					/>
					{ showOtherRestrictionType && (
						<TextControl
							label={ __(
								'Restriction details',
								'woocommerce-shipping'
							) }
							required={ true }
							{ ...getProps< string >( 'restrictionComments' ) }
							disabled={ disable }
						/>
					) }
				</FlexBlock>
			</Flex>
			<Flex>
				<TextControl
					label={ createInterpolateElement(
						__(
							'International transaction number (<a>more info about ITN</a>)',
							'woocommerce-shipping'
						),
						{
							a: (
								<Link
									href="https://pe.usps.com/text/imm/immc5_010.htm"
									target="_blank"
									rel="noopener noreferrer"
									type="external"
								>
									{ ' ' }
								</Link>
							),
						}
					) }
					{ ...getProps< string >( 'itn' ) }
					disabled={ disable }
				/>
			</Flex>
			<Flex>
				<CheckboxControl
					label={ __(
						'Return package to sender if undeliverable',
						'woocommerce-shipping'
					) }
					{ ...getCheckboxControlProps< boolean >(
						'isReturnToSender'
					) }
					disabled={ disable }
				/>
			</Flex>
			<Flex>
				<Heading level={ 4 }>
					{ __( 'Product details', 'woocommerce-shipping' ) }
				</Heading>
			</Flex>
			<Spacer margin={ 10 } />
			<Flex direction="column">
				{ items.map( ( { id }, index ) => (
					<Flex gap={ 6 } direction="column" key={ id }>
						<FlexBlock>
							<Flex align="normal" className="customs-items">
								<TextControl
									label={
										<>
											{ __(
												'Description',
												'woocommerce-shipping'
											) }{ ' ' }
											<ControlledPopover icon="info-outline">
												{ createInterpolateElement(
													__(
														`When shipping to countries that follow European Union (EU) customs rules, you must provide a clear, specific description on every item. For example, if you are sending clothing, you must indicate what type of clothing (e.g. men\'s shirts, girl\'s vest, boy\'s jacket) for the description to be acceptable. Otherwise, shipments may be delayed or interrupted at customs. <a>Learn more about customs rules <i></i></a>`,
														'woocommerce-shipping'
													),
													{
														a: (
															<Link
																href="https://pe.usps.com/text/imm/immc5_010.htm"
																target="_blank"
																rel="noopener noreferrer"
																type="external"
															>
																{ ' ' }
															</Link>
														),
														i: (
															<Icon
																icon="external"
																size={ 16 }
															/>
														),
													}
												) }
											</ControlledPopover>
										</>
									}
									hideLabelFromVision={ index !== 0 }
									{ ...getProps<
										CustomsItem[ 'description' ]
									>( 'description', id ) }
									disabled={ disable }
									required={ true }
								/>
								<TextControl
									label={ createInterpolateElement(
										__(
											'HS tariff number (<a>more…</a>)',
											'woocommerce-shipping'
										),
										{
											a: (
												<Link
													href="https://woocommerce.com/document/woocommerce-shipping-and-tax/woocommerce-shipping/#section-30"
													target="_blank"
													rel="noopener noreferrer"
													type="external"
												>
													{ ' ' }
												</Link>
											),
										}
									) }
									hideLabelFromVision={ index !== 0 }
									{ ...getProps< string >(
										'hsTariffNumber',
										id
									) }
									placeholder={
										isHSTariffNumberRequired()
											? ''
											: __(
													'Optional',
													'woocommerce-shipping'
											  )
									}
									required={ isHSTariffNumberRequired() }
									disabled={ disable }
								/>
								<InputControl
									label={ __(
										'Value per unit',
										'woocommerce-shipping'
									) }
									hideLabelFromVision={ index !== 0 }
									type="number"
									min={ 0 }
									step={ 0.01 }
									{ ...symbolProp }
									{ ...getProps( 'price', id ) }
									disabled={ disable }
								/>
								<InputControl
									label={ __(
										'Weight per unit',
										'woocommerce-shipping'
									) }
									hideLabelFromVision={ index !== 0 }
									type="number"
									min={ 0 }
									step={ 0.01 }
									suffix={ weightUnit }
									{ ...getProps( 'weight', id ) }
									disabled={ disable }
									required={ true }
								/>
								<SelectControl
									label={
										<>
											{ __(
												'Origin country',
												'woocommerce-shipping'
											) }
											<ControlledPopover icon="info-outline">
												{ __(
													'Country where the product was manufactured or assembled.',
													'woocommerce-shipping'
												) }
											</ControlledPopover>{ ' ' }
										</>
									}
									hideLabelFromVision={ index !== 0 }
									options={ countryNames }
									{ ...getProps( 'originCountry', id ) }
									disabled={ disable }
									required={ true }
								/>
							</Flex>
						</FlexBlock>
					</Flex>
				) ) }
			</Flex>
		</>
	);
};
