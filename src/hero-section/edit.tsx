import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import './editor.scss';

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'avcl-hero',
	} );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: [
			[ 'core/heading', { placeholder: 'Enter heading...' } ],
			[ 'core/paragraph', { placeholder: 'Enter paragraph...' } ],
			[
				'core/buttons',
				{},
				[
					[
						'core/button',
						{
							placeholder: 'Enter button text...',
							text: 'Enter Link 1 Text',
							url: 'Enter URL',
						},
					],
					[
						'core/button',
						{
							placeholder: 'Enter button text...',
							text: 'Enter Link 2 Text',
							url: 'Enter URL',
						},
					],
				],
			],
			[ 'core/image', { placeholder: 'Upload an image...' } ],
		],
		allowedBlocks: [
			'core/heading',
			'core/paragraph',
			'core/buttons',
			'core/button',
			'core/image',
		],
		templateLock: 'all',
	} );

	return (
		<>
			<div { ...innerBlocksProps } />
		</>
	);
}
