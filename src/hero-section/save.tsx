import './style.scss';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save() {
	return (
		<div { ...useBlockProps.save( { className: 'avcl-hero' } ) }>
			<InnerBlocks.Content />
		</div>
	);
}
