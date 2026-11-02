import type { BlockEditProps } from '@wordpress/blocks';
import {
	BlockControls,
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import './editor.scss';
import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	ResizableBox,
	SelectControl,
	TextControl,
	ToolbarGroup,
	ToolbarButton,
	Dropdown,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { useState } from 'react';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { store as blockEditorStore } from '@wordpress/block-editor';
type ImagePosition = 'top' | 'left' | 'right' | 'bottom';
type Platforms = 'shopify';
interface ProductCardAttributes extends Record< string, unknown > {
	position: ImagePosition;
	cardWidth: number;
	productUrl: string;
	platform: Platforms;
}

function isImagePosition( value: string ): value is ImagePosition {
	return [ 'top', 'left', 'right', 'bottom' ].includes( value );
}

function isCorrectPlatform( value: string ): value is Platforms {
	return [ 'shopify' ].includes( value );
}

function isValidProductUrl( value: string ): boolean {
	try {
		const url = new URL( value.trim() );

		if ( url.protocol !== 'https:' || ! url.hostname.includes( '.' ) ) {
			return false;
		}

		const labels = url.hostname.split( '.' );
		return labels.every(
			( label ) =>
				label.length > 0 &&
				label.length <= 63 &&
				/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i.test( label )
		);
	} catch {
		return false;
	}
}

function getApiErrorStatus( error: unknown ): number | undefined {
	if ( typeof error !== 'object' || error === null ) {
		return undefined;
	}

	if (
		'data' in error &&
		typeof error.data === 'object' &&
		error.data !== null &&
		'status' in error.data
	) {
		const status = error.data.status;
		if ( typeof status === 'number' ) {
			return status;
		}
	}

	if ( 'status' in error && typeof error.status === 'number' ) {
		return error.status;
	}

	return undefined;
}

function getApiErrorMessage( error: unknown ): string | undefined {
	if (
		typeof error === 'object' &&
		error !== null &&
		'message' in error &&
		typeof error.message === 'string'
	) {
		return error.message;
	}

	return undefined;
}

const TEMPLATE = [
	[ 'core/image' ],
	[
		'core/group',
		{
			className: 'product-details-content',
			layout: { type: 'flex', orientation: 'vertical' },
		},
		[
			[ 'avcl/product-title' ],
			[ 'avcl/product-price' ],
			[ 'avcl/product-description' ],
			[ 'core/buttons', {}, [ [ 'core/button' ] ] ],
		],
	],
] as const;

const ALLOWED_BLOCKS = [
	'core/image',
	'core/group',
	'avcl/product-title',
	'avcl/product-price',
	'avcl/product-description',
	'core/buttons',
];

export default function Edit( {
	attributes,
	setAttributes,
	clientId,
}: BlockEditProps< ProductCardAttributes > ) {
	const { position, cardWidth, productUrl, platform } = attributes;
	const [ URLError, setURLError ] = useState( '' );

	const blockProps = useBlockProps( {
		className: `avcl-product-card layout-image-${ position }`,
	} );

	type EditorSelectors = {
		getCurrentPostId: () => number;
	};
	type ProductData = {
		image?: string;
		title?: string;
		price?: number;
		url?: string;
		category?: string;
		tags?: Array< string >;
		min_price?: number;
		max_price?: number;
		description?: string;
	};
	const postId = useSelect(
		( select ) =>
			( select( editorStore ) as EditorSelectors ).getCurrentPostId(),
		[]
	);

	const imageBlockClientId = useSelect(
		( select ) =>
			select( blockEditorStore )
				.getBlocks( clientId )
				.find( ( block ) => block.name === 'core/image' )?.clientId,
		[ clientId ]
	) as string;

	const { updateBlockAttributes } = useDispatch( blockEditorStore );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: TEMPLATE,
		allowedBlocks: ALLOWED_BLOCKS,
		templateLock: false,
	} );

	const fetchProduct = async (
		productUrl: string,
		platform: string,
		method: 'js' | 'json' | 'graphql' | 'domain'
	) => {
		return apiFetch< ProductData >( {
			path: `/avcl/blocks/v1/product-search?product_url=${ encodeURIComponent( productUrl ) }&post_id=${ postId }&platform=${ platform }&method=${ method }`,
		} );
	};

	const handleImport = async ( productUrl: string, platform: string ) => {
		if ( typeof postId !== 'number' ) {
			setURLError( 'Save the post before importing product data.' );
			return;
		}

		try {
			if ( ! isValidProductUrl( productUrl ) ) {
				setURLError( 'Enter a valid HTTPS product URL.' );
				return;
			}
			setURLError( '' );
		} catch ( e ) {
			setURLError( 'Enter a complete, valid URL, including https://' );
			return;
		}
		if ( ! isCorrectPlatform( platform ) ) {
			setURLError( 'Select a correct platform' );
			return;
		}

		const cleanProductUrl = new URL( productUrl.trim() );
		cleanProductUrl.search = '';
		cleanProductUrl.hash = '';
		const normalizedProductUrl = cleanProductUrl.toString();
		setAttributes( { productUrl: normalizedProductUrl } );

		const methods = [
			{ name: 'method 1', method: 'js' as const },
			{ name: 'method 2', method: 'json' as const },
			{ name: 'method 3', method: 'graphql' as const },
			{ name: 'method 4', method: 'domain' as const },
		];

		for ( const [ index, attempt ] of methods.entries() ) {
			setURLError( `Finding product with ${ attempt.name }...` );

			try {
				const productData = await fetchProduct(
					normalizedProductUrl,
					platform,
					attempt.method
				);

				setURLError( '' );

				if ( imageBlockClientId && productData.image ) {
					updateBlockAttributes( imageBlockClientId, {
						url: productData.image,
					} );

					setAttributes( { imageUrl: productData.image } );
				}
				if ( productData.title ) {
					setAttributes( { productName: productData.title } );
				}
				if ( productData.description ) {
					setAttributes( { description: productData.description } );
				}
				if ( productData.price ) {
					setAttributes( { price: productData.price } );
				}

				return productData;
			} catch ( error ) {
				if ( getApiErrorStatus( error ) !== 404 ) {
					setURLError(
						getApiErrorMessage( error ) ||
							__(
								'Could not fetch product details. Please try again.',
								'avcl-blocks'
							)
					);
					return;
				}

				if ( index < methods.length - 1 ) {
					setURLError(
						`Not found with ${ attempt.name }, trying ${ methods[ index + 1 ].name }...`
					);
				}
			}
		}

		setURLError(
			__(
				'Seems like this product is restricted to fetch details',
				'avcl-blocks'
			)
		);
	};
	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<Dropdown
						renderToggle={ ( { isOpen, onToggle } ) => (
							<ToolbarButton
								label={ __( 'Import product', 'avcl-blocks' ) }
								icon="download"
								isActive={ isOpen }
								aria-expanded={ isOpen }
								onClick={ onToggle }
							/>
						) }
						renderContent={ () => (
							<div className="avcl-product-card__import-popover">
								{ URLError && (
									<p
										role="alert"
										className="avcl-import-error"
									>
										{ URLError }
									</p>
								) }
								<TextControl
									type="url"
									label={ __( 'Product URL', 'avcl-blocks' ) }
									value={ productUrl }
									onChange={ ( value ) => {
										setAttributes( { productUrl: value } );
									} }
								/>
								<SelectControl
									value={ platform }
									onChange={ ( value ) => {
										if ( isCorrectPlatform( value ) ) {
											setAttributes( {
												platform: value,
											} );
										}
									} }
									options={ [
										{ label: 'Shopify', value: 'shopify' },
									] }
								></SelectControl>
								<Button
									variant="primary"
									onClick={ () =>
										handleImport( productUrl, platform )
									}
									disabled={ ! productUrl.trim() }
								>
									{ __( 'Import', 'avcl-blocks' ) }
								</Button>
							</div>
						) }
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<Panel header="Product Card Settings">
					<PanelBody>
						<PanelRow>
							<strong>Position:</strong>
							<SelectControl
								options={ [
									{
										label: 'Image on Top',
										value: 'top',
									},
									{
										label: 'Image on Left',
										value: 'left',
									},
									{
										label: 'Image on Right',
										value: 'right',
									},
									{
										label: 'Image on Bottom',
										value: 'bottom',
									},
								] }
								value={ position }
								onChange={ ( value ) => {
									if ( isImagePosition( value ) ) {
										setAttributes( { position: value } );
									}
								} }
							/>
						</PanelRow>
						<PanelRow>
							<></>
						</PanelRow>
					</PanelBody>
				</Panel>
			</InspectorControls>
			<ResizableBox
				size={ { width: cardWidth, height: 'auto' } }
				onResizeStop={ ( _event, _direction, element ) => {
					setAttributes( { cardWidth: element.offsetWidth } );
				} }
				enable={ {
					top: false,
					right: true,
					bottom: false,
					left: false,
					topRight: false,
					bottomRight: false,
					bottomLeft: false,
					topLeft: false,
				} }
			>
				<div { ...innerBlocksProps } />
			</ResizableBox>
		</>
	);
}
