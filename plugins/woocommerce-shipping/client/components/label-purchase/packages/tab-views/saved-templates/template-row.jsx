import { getDimensionsUnit, getWeightUnit } from 'utils';
import { __experimentalText as Text, Spinner } from '@wordpress/components';

export const TemplateRow = ( {
	name,
	outerDimensions,
	innerDimensions,
	dimensions,
	boxWeight,
	isBusy,
} ) => {
	const dimensionsUnit = getDimensionsUnit();
	const weightUnit = getWeightUnit();
	const preparedDimensions =
		( outerDimensions || innerDimensions || dimensions )
			.replaceAll( `${ dimensionsUnit } x`, 'x' ) // Convert `unit + ' x'` to `x`. E.g. `cm x cm x cm` to `x x x`.
			.replaceAll( 'x', `${ dimensionsUnit } x` ) +
		` ${ dimensionsUnit }`;

	return (
		<>
			<Text truncate title={ name }>
				{ name }
			</Text>
			<span>{ preparedDimensions }</span>
			{ ', ' }&nbsp;
			<span>
				{ boxWeight }
				{ weightUnit }
			</span>
			{ isBusy && <Spinner /> }
		</>
	);
};
