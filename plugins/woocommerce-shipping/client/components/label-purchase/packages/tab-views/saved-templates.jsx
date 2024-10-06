import {
	__experimentalSpacer as Spacer,
	Button,
	Dropdown,
	Flex,
	MenuItemsChoice,
} from '@wordpress/components';
import { chevronDown } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';
import { memo, useCallback } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { labelPurchaseStore } from 'data/label-purchase';
import { Conditional } from 'components/HOC';
import { TemplateRow } from './saved-templates/template-row';
import { NoSavedTemplates } from './saved-templates/no-saved-templates';
import { useLabelPurchaseContext } from '../../context';
import { FetchNotice } from './fetch-notice';
import { TotalWeight } from '../../total-weight';
import { GetRatesButton } from '../../get-rates-button';
import { recordEvent } from 'utils/tracks';
import { withBoundary } from 'components/HOC/error-boundary';

export const SavedTemplates = withBoundary(
	Conditional(
		( forwardedProps ) => {
			const savedPackages = useSelect( ( select ) =>
				select( labelPurchaseStore ).getSavedPackages()
			);
			return {
				render: savedPackages.length === 0,
				props: { ...forwardedProps, savedPackages },
			};
		},
		NoSavedTemplates,
		memo( ( { savedPackages, selectedPackage, setSelectedPackage } ) => {
			const options = savedPackages.map( ( props ) => ( {
				label: <TemplateRow { ...props } key={ props.name } />,
				value: props.id,
			} ) );
			const {
				rates: { isFetching, fetchRates, errors },
				customs: { hasErrors: hasCustomsErrors },
				hazmat: { isHazmatSpecified },
				packages: { isSelectedASavedPackage },
				weight: { getShipmentTotalWeight },
			} = useLabelPurchaseContext();

			const getOptionById = useCallback(
				( current ) =>
					savedPackages.find( ( option ) => option.id === current ),
				[ savedPackages ]
			);

			const select = useCallback(
				( id ) => {
					setSelectedPackage( getOptionById( id ) );
				},
				[ getOptionById, setSelectedPackage ]
			);

			const getRates = useCallback( () => {
				const tracksProperties = {
					package_id: selectedPackage?.id,
					is_letter: selectedPackage?.isLetter,
					width: selectedPackage?.width,
					height: selectedPackage?.height,
					length: selectedPackage?.length,
					template_name: selectedPackage?.name,
					is_saved_template: true,
				};
				recordEvent(
					'label_purchase_get_rates_clicked',
					tracksProperties
				);
				fetchRates( selectedPackage );
			}, [ selectedPackage, fetchRates ] );

			return (
				<>
					<label htmlFor="custom-packages">
						{ __( 'Package template', 'woocommerce-shipping' ) }
					</label>
					<Dropdown
						className="custom-packages"
						contentClassName="custom-package-options"
						popoverProps={ {
							placement: 'bottom-start',
							noArrow: false,
							resize: true,
							shift: true,
							inline: true,
						} }
						renderToggle={ ( { isOpen, onToggle } ) => (
							<Button
								onClick={ onToggle }
								aria-expanded={ isOpen }
								isSecondary
								icon={ chevronDown }
								className="custom-package__toggle"
								disabled={ isFetching }
							>
								{ ! selectedPackage ||
								! isSelectedASavedPackage() ? (
									__(
										'Please select',
										'woocommerce-shipping'
									)
								) : (
									<section>
										<TemplateRow { ...selectedPackage } />
									</section>
								) }
							</Button>
						) }
						renderContent={ ( { onToggle } ) => (
							<MenuItemsChoice
								choices={ options }
								onSelect={ ( value ) => {
									select( value );
									onToggle();
								} }
								value={ selectedPackage?.name }
							/>
						) }
					/>
					<Spacer marginTop={ 4 } marginBottom={ 0 } />
					<Flex align="flex-end" gap={ 6 }>
						<TotalWeight
							packageWeight={ selectedPackage?.boxWeight ?? 0 }
						/>

						<GetRatesButton
							onClick={ getRates }
							isBusy={ isFetching }
							disabled={
								! selectedPackage ||
								isFetching ||
								errors.totalWeight ||
								hasCustomsErrors() ||
								! isHazmatSpecified() ||
								! getShipmentTotalWeight()
							}
						/>
					</Flex>
					<FetchNotice />
				</>
			);
		} )
	)
)( 'SavedTemplates' );
