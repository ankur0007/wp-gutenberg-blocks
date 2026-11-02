import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import type { BlockSaveProps } from '@wordpress/blocks';

interface ProductCardSaveAttributes extends Record< string, unknown > {
	position: string;
	cardWidth: number;
}

export default function save( {
	attributes,
}: BlockSaveProps< ProductCardSaveAttributes > ) {
	return (
		<div
			{ ...useBlockProps.save( {
				className: `avcl-product-card layout-image-${ attributes.position }`,
				style: { width: `${ attributes.cardWidth }px` },
			} ) }
		>
			<InnerBlocks.Content />
		</div>
	);
}
