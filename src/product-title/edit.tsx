import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';

interface ProductTitleAttributes extends Record< string, unknown > {}

type ProductTitleEditProps = BlockEditProps< ProductTitleAttributes > & {
	context: { 'avcl/productName'?: string };
};

export default function Edit( { context, clientId }: ProductTitleEditProps ) {
	const parentBlockClientId = useSelect(
		( select ) => {
			return select( blockEditorStore ).getBlockRootClientId( clientId );
		},
		[ clientId ]
	);
	const { updateBlockAttributes } = useDispatch( blockEditorStore );
	return (
		<RichText
			{ ...useBlockProps( { className: 'product-title' } ) }
			tagName="h3"
			value={ context[ 'avcl/productName' ] || '' }
			onChange={ ( value ) => {
				if ( parentBlockClientId ) {
					updateBlockAttributes( parentBlockClientId, {
						productName: value,
					} );
				}
			} }
			placeholder="Enter product title..."
		/>
	);
}
