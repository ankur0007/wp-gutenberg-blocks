import { useSelect, useDispatch } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
export default function getParentBlockClientId(
	clientId: string,
	parentName: string
): string | null {
	return useSelect(
		( select ) => {
			const editor = select( blockEditorStore );

			return (
				editor
					.getBlockParents( clientId )
					.find(
						( parentId ) =>
							editor.getBlockName( parentId ) === parentName
					) ?? null
			);
		},
		[ clientId ]
	);
}
