import {
	useBlockProps,
	store as BlockStore,
	InspectorControls,
} from '@wordpress/block-editor';
import { BlockEditProps } from '@wordpress/blocks';
import {
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import getParentBlockClientId from '../hooks/get-parent-block-client-id';
import { useDispatch } from '@wordpress/data';

type ChildDescProps = Record< string, unknown >;
type ParentDescProps = BlockEditProps< ChildDescProps > & {
	context: {
		'avcl/price'?: number;
		'avcl/currency'?: string;
	};
};
export default function Edit( { context, clientId }: ParentDescProps ) {
	const parentBlockClientId = getParentBlockClientId(
		clientId,
		'avcl/product-card'
	);
	const { updateBlockAttributes } = useDispatch( BlockStore );
	const price: number = context[ 'avcl/price' ] ?? 0;
	const curreny: string = context[ 'avcl/currency' ] ?? '';
	const currencies = window.avclProductPriceCurrencies;
	const currencyOptions = Object.entries( currencies ).map(
		( [ code, currency ] ) => ( {
			label: currency.label,
			value: code,
		} )
	);

	return (
		<>
			<InspectorControls>
				<Panel header="Select Currency">
					<PanelBody>
						<PanelRow>
							<SelectControl
								options={ currencyOptions }
								value={ curreny }
								onChange={ ( value ) => {
									if ( parentBlockClientId ) {
										updateBlockAttributes(
											parentBlockClientId,
											{ currency: value }
										);
									}
								} }
							></SelectControl>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<TextControl
					type="number"
					value={ price }
					onChange={ ( value ) => {
						if ( parentBlockClientId ) {
							updateBlockAttributes( parentBlockClientId, {
								price: value,
							} );
						}
					} }
				></TextControl>
			</div>
		</>
	);
}
