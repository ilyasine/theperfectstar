import { useCallback, useEffect, useState } from '@wordpress/element';
import {
	__experimentalInputControl as InputControl,
	__experimentalSpacer as Spacer,
	Button,
	CheckboxControl,
	Flex,
	FlexBlock,
	FlexItem,
	Notice,
	SelectControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { dispatch, useSelect } from '@wordpress/data';
import { getDimensionsUnit } from 'utils';
import { FetchNotice } from './fetch-notice';
import { TAB_NAMES, PACKAGE_TYPES } from '../constants';
import { labelPurchaseStore } from 'data/label-purchase';
import { useLabelPurchaseContext } from '../../context';
import { TotalWeight } from '../../total-weight';
import { GetRatesButton } from '../../get-rates-button';
import { PACKAGE_SECTION } from 'components/label-purchase/essential-details/constants';
import { recordEvent } from 'utils/tracks';
import { withBoundary } from 'components/HOC/error-boundary';

export const CustomPackage = withBoundary(
	( { rawPackageData, setRawPackageData } ) => {
		const dimensionsUnit = getDimensionsUnit();
		const [ saveAsTemplate, setSaveAsTemplate ] = useState( false );
		const [ isSaving, setIsSaving ] = useState( false );
		const [ isSaved, setIsSaved ] = useState( false );

		const {
			rates: { errors, isFetching, setErrors, fetchRates },
			shipment: { currentShipmentId },
			customs: { hasErrors: hasCustomsErrors },
			essentialDetails: {
				focusArea: essentialDetailsFocusArea,
				resetFocusArea: resetEssentialDetailsFocusArea,
			},
			packages: { currentPackageTab },
			hazmat: { isHazmatSpecified },
			weight: { getShipmentTotalWeight },
		} = useLabelPurchaseContext();
		const setData = useCallback(
			( newData ) => {
				setIsSaved( false );
				setRawPackageData( { ...rawPackageData, ...newData } );
			},
			[ currentShipmentId ]
		);

		const updateErrors = useSelect(
			( select ) => select( labelPurchaseStore ).getPackageUpdateErrors(),
			[]
		);

		useEffect( () => {
			if ( Object.keys( updateErrors ).length > 0 ) {
				setErrors( {
					name: updateErrors,
				} );
			}
		}, [ updateErrors, setErrors ] );

		useEffect( () => {
			if (
				currentPackageTab === TAB_NAMES.CUSTOM_PACKAGE &&
				essentialDetailsFocusArea === PACKAGE_SECTION
			) {
				setErrors( {
					width: true,
					height: true,
					length: true,
				} );
			}
		}, [ currentPackageTab, essentialDetailsFocusArea ] );

		const hasFormErrors = useCallback( () => {
			// eslint-disable-next-line no-unused-vars
			const { endpoint, ...formErrors } = errors;
			return Object.values( formErrors ).some( ( v ) => !! v );
		}, [ errors ] );

		const isAnyFieldEmpty = useCallback(
			() => Object.values( rawPackageData ).some( ( val ) => val === '' ),
			[ rawPackageData ]
		);

		const saveCustomPackage = useCallback( async () => {
			Object.entries( rawPackageData ).forEach( ( [ key, val ] ) => {
				if ( val === '' ) {
					setErrors( { ...errors, [ key ]: true } );
				}
			} );

			if ( rawPackageData.name.length < 3 ) {
				setErrors( {
					...errors,
					name: {
						message: __(
							'Package name should be at least 3 characters long.',
							'woocommerce-shipping'
						),
					},
				} );
				return;
			}
			setErrors( {
				...errors,
				name: false,
			} );

			if ( hasFormErrors() || isAnyFieldEmpty() > 0 ) {
				return;
			}

			setIsSaving( true );
			const { errors: savingErrors } = await dispatch(
				labelPurchaseStore
			).saveCustomPackage( rawPackageData );
			setIsSaving( false );

			if ( ! savingErrors || Object.keys( savingErrors ).length === 0 ) {
				setSaveAsTemplate( false );
				setIsSaved( true );
			}
		}, [
			rawPackageData,
			errors,
			hasFormErrors,
			setErrors,
			isAnyFieldEmpty,
		] );

		const invalidDimensionError = __(
			'Invalid dimension value.',
			'woocommerce-shipping'
		);

		const setErrorForInvalidDimension = ( value, fieldName ) => {
			if ( ! [ 'width', 'height', 'length' ].includes( fieldName ) ) {
				return;
			}

			const parsedVal = parseFloat( value );

			if ( parsedVal <= 0 || Number.isNaN( parsedVal ) ) {
				setErrors( {
					...errors,
					[ fieldName ]: {
						message: invalidDimensionError,
					},
				} );
			}
		};
		const getControlProps = ( fieldName ) => ( {
			onChange: ( val ) => {
				const { ...newErrors } = errors;
				delete newErrors[ fieldName ];
				setErrors( newErrors );
				setData( { ...rawPackageData, [ fieldName ]: val } );
				resetEssentialDetailsFocusArea();
				setErrorForInvalidDimension( val, fieldName );
			},
			value: rawPackageData[ fieldName ],
			className: errors[ fieldName ] ? 'has-error' : '',
			onValidate: ( value ) => {
				setErrorForInvalidDimension( value, fieldName );
			},
			help: errors[ fieldName ]?.message
				? errors[ fieldName ].message
				: [],
		} );

		const getRates = useCallback( async () => {
			const tracksProperties = {
				package_id: rawPackageData?.id,
				is_letter: rawPackageData?.isLetter,
				width: rawPackageData?.width,
				height: rawPackageData?.height,
				length: rawPackageData?.length,
			};
			recordEvent( 'label_purchase_get_rates_clicked', tracksProperties );
			fetchRates( rawPackageData );
		}, [ rawPackageData, fetchRates ] );

		const disableFetchButton = useCallback( () => {
			return (
				isFetching ||
				! rawPackageData.length ||
				! rawPackageData.width ||
				! rawPackageData.height ||
				hasFormErrors() ||
				hasCustomsErrors() ||
				! isHazmatSpecified()
			);
		}, [ isFetching, rawPackageData, errors, hasCustomsErrors ] );

		return (
			<Flex direction="column" gap={ 6 }>
				<FlexItem>
					<Flex
						direction="column"
						className="custom-package__details"
						expanded
						gap={ 8 }
						justify="space-between"
					>
						<Flex justify="space-between" gap={ 8 }>
							<FlexBlock>
								<SelectControl
									options={ [
										{
											label: __(
												'Box',
												'woocommerce-shipping'
											),
											value: PACKAGE_TYPES.BOX,
										},
										{
											label: __(
												'Envelope',
												'woocommerce-shipping'
											),
											value: PACKAGE_TYPES.ENVELOPE,
										},
									] }
									label={ __(
										'Package type',
										'woocommerce-shipping'
									) }
									style={ { flex: 2 } }
									__nextHasNoMarginBottom
									onChange={ ( type ) =>
										setData( {
											...rawPackageData,
											type,
										} )
									}
								></SelectControl>
							</FlexBlock>
						</Flex>
						<Flex
							direction="row"
							justify="space-between"
							align="center"
							gap={ 0 }
						>
							<InputControl
								label={ __( 'Length', 'woocommerce-shipping' ) }
								suffix={ dimensionsUnit }
								type="number"
								min={ 0 }
								{ ...getControlProps( 'length' ) }
							/>
							<Spacer
								direction="vertical"
								marginLeft={ 3 }
								marginRight={ 3 }
								paddingTop={ 8 }
							>
								{ 'x' }
							</Spacer>
							<InputControl
								label={ __( 'Width', 'woocommerce-shipping' ) }
								type="number"
								suffix={ dimensionsUnit }
								min={ 0 }
								{ ...getControlProps( 'width' ) }
							/>
							<Spacer
								direction="vertical"
								marginLeft={ 3 }
								marginRight={ 3 }
								paddingTop={ 8 }
							>
								{ 'x' }
							</Spacer>
							<InputControl
								label={ __( 'Height', 'woocommerce-shipping' ) }
								suffix={ dimensionsUnit }
								type="number"
								min={ 0 }
								{ ...getControlProps( 'height' ) }
							/>
						</Flex>
					</Flex>
				</FlexItem>
				<FlexItem className="custom-template__save">
					<Flex
						direction="row"
						gap={ 12 }
						justify="space-between"
						align="flex-start"
					>
						<FlexItem isBlock>
							<CheckboxControl
								label={ __(
									'Save this as a new package template',
									'woocommerce-shipping'
								) }
								onChange={ () =>
									setSaveAsTemplate( ! saveAsTemplate )
								}
								checked={ saveAsTemplate }
							/>
							{ isSaved && ! saveAsTemplate && (
								<Notice
									status={ 'success' }
									politeness="polite"
									isDismissible={ false }
								>
									{ __(
										'Successfully saved to Saved templates.',
										'woocommerce-shipping'
									) }
								</Notice>
							) }
							{ saveAsTemplate && (
								<Flex
									className="save-template-form"
									align="flex-start"
									gap={ 6 }
								>
									<InputControl
										placeholder={ __(
											'Enter a unique package name',
											'woocommerce-shipping'
										) }
										{ ...getControlProps( 'name' ) }
									/>
									<Button
										isSecondary
										type="submit"
										isBusy={ isSaving }
										onClick={ () => saveCustomPackage() }
									>
										{ __( 'Save', 'woocommerce-shipping' ) }
									</Button>
								</Flex>
							) }
						</FlexItem>
					</Flex>
				</FlexItem>
				<FlexItem>
					<Flex align="flex-end" gap={ 6 }>
						<TotalWeight />
						<GetRatesButton
							onClick={ getRates }
							isBusy={ isFetching }
							disabled={
								disableFetchButton() ||
								! getShipmentTotalWeight()
							}
						/>
					</Flex>
					<FetchNotice margin="before" />
				</FlexItem>
			</Flex>
		);
	}
)( 'CustomPackage' );
