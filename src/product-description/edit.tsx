import {
	RichText,
	useBlockProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { BlockEditProps } from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';
import getParentBlockClientId from '../hooks/get-parent-block-client-id';

type DescProps = Record< string, unknown >;

type ProductDescriptionEditProps = BlockEditProps< DescProps > & {
	context: {
		'avcl/description'?: string;
	};
};

export default function Edit( {
	context,
	clientId,
}: ProductDescriptionEditProps ) {
	const description: string = context[ 'avcl/description' ] || '';

	const productCardClientId = getParentBlockClientId(
		clientId,
		'avcl/product-card'
	);
	const { updateBlockAttributes } = useDispatch( blockEditorStore );
	return (
		<RichText
			{ ...useBlockProps() }
			value={ description }
			onChange={ ( value ) => {
				if ( productCardClientId ) {
					updateBlockAttributes( productCardClientId, {
						description: value,
					} );
				}
			} }
		></RichText>
	);
}
