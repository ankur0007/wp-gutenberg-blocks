/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import type { Post } from '@wordpress/core-data';
import { Spinner } from '@wordpress/components';
import Settings from './settings';
import placeholderImage from '../../assets/images/placeholder.jpg';

interface RelatedPostAttributes extends Record< string, unknown > {
	postsToShow: number;
	postType: string;
	orderby: string;
	order: string;
}

type RelatedPost = Post< 'edit' > & {
	_embedded?: {
		'wp:featuredmedia'?: Array< {
			source_url?: string;
			alt_text?: string;
		} >;
	};
};

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object} props Block properties.
 * @return {Element} Element to render.
 */
export default function Edit( props: BlockEditProps< RelatedPostAttributes > ) {
	const { attributes, setAttributes } = props;

	const Posts = useSelect(
		( select ) => {
			return select( coreDataStore ).getEntityRecords< RelatedPost >(
				'postType',
				attributes.postType,
				{
					per_page: attributes.postsToShow,
					orderby: attributes.orderby,
					order: attributes.order,
					categories: [],
					_embed: true,
				}
			);
		},
		[
			attributes.postType,
			attributes.postsToShow,
			attributes.orderby,
			attributes.order,
		]
	);
	return (
		<>
			<Settings
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<div
				{ ...useBlockProps( {
					className: 'avcl-related-posts',
				} ) }
			>
				<div
					className="avcl-related-posts__grid"
					style={ {
						gridTemplateColumns: `repeat(${ attributes.postsToShow }, 1fr)`,
					} }
				>
					{ Posts === null ? (
						<Spinner />
					) : (
						Posts?.length > 0 &&
						Posts.map( ( post ) => {
							return (
								<article
									key={ post.id }
									className="avcl-related-posts__card"
								>
									<a
										className="avcl-related-posts__image-link"
										href={ post.link }
									>
										<img
											className="avcl-related-posts__image"
											src={
												post._embedded?.[
													'wp:featuredmedia'
												]?.[ 0 ]?.source_url ||
												placeholderImage
											}
											alt={
												post._embedded?.[
													'wp:featuredmedia'
												]?.[ 0 ]?.alt_text ||
												post.title.rendered
											}
										/>
									</a>

									<div className="avcl-related-posts__content">
										<h3 className="avcl-related-posts__title">
											<a href={ post.link }>
												{ post.title.rendered }
											</a>
										</h3>

										<p className="avcl-related-posts__excerpt">
											{ post.excerpt.rendered.replace(
												/<[^>]+>/g,
												''
											) }
										</p>

										<a
											className="avcl-related-posts__link"
											href={ post.link }
										>
											Read article
											<span aria-hidden="true">→</span>
										</a>
									</div>
								</article>
							);
						} )
					) }
				</div>
			</div>
		</>
	);
}
