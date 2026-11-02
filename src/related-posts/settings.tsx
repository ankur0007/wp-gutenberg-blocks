import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';
import './editor.scss';

import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import {
	SelectControl,
	Panel,
	PanelBody,
	PanelRow,
	RangeControl,
} from '@wordpress/components';

interface RelatedPostSettingsAttributes extends Record< string, unknown > {
	postsToShow: number;
	postType: string;
}

export default function Settings( {
	attributes,
	setAttributes,
}: Pick<
	BlockEditProps< RelatedPostSettingsAttributes >,
	'attributes' | 'setAttributes'
> ) {
	const { postsToShow } = attributes;
	const postTypes = useSelect( ( select ) => {
		return select( coreDataStore ).getPostTypes() ?? [];
	} );

	const availablePostTypes = postTypes
		.filter( ( type ) => type.visibility?.show_in_nav_menus )
		.map( ( type ) => ( {
			label: type.name,
			value: type.slug,
		} ) );

	return (
		<InspectorControls>
			<Panel>
				<PanelBody title="Settings" initialOpen={ true }>
					<PanelRow>
						<SelectControl
							label={ __( 'Select Post Type', 'avcl-blocks' ) }
							value={ attributes.postType }
							options={ availablePostTypes }
							onChange={ ( value ) =>
								setAttributes( { postType: value } )
							}
						/>
					</PanelRow>
					<PanelRow>
						<RangeControl
							label="Number of Posts to Show"
							value={ postsToShow }
							onChange={ ( value ) =>
								setAttributes( { postsToShow: value } )
							}
							min={ 1 }
							max={ 10 }
						/>
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);
}
