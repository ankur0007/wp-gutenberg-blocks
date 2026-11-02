=== AVCL Blocks ===
Tags: blocks
Requires at least: 6.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A collection of custom Gutenberg blocks, including a Product Card block with a
Shopify product importer. The importer tries multiple public storefront methods
to support product imports from a broad range of Shopify stores using a product
URL. Storefront restrictions vary, so access cannot be guaranteed for every store.

== Installation ==

1. Place the avcl-blocks directory in /wp-content/plugins/.
2. Activate AVCL Blocks from the Plugins screen.
3. Add a Product Card or another AVCL block from the block inserter.

== Included blocks ==

Hero Section provides a prebuilt hero layout with a heading, paragraph, two
buttons, and an image. Replace its placeholder content in the editor.

Related Posts displays a grid of posts with featured images, titles, excerpts,
and read-more links. Choose the post type and number of posts to show (1 to 10);
the current post is excluded from the results.

== Shopify product importer ==

Paste a Shopify product URL into the Product Card importer. It tries the
product .js endpoint, the product .json endpoint, a storefront GraphQL
cookie-domain lookup, and a derived {store-name}.myshopify.com product URL
using .js and .json. The importer retains product details, image galleries,
and variants.

These methods use public storefront endpoints. Individual stores may block or
restrict access, and custom storefront domains do not always map to their
original myshopify.com domain. The importer cannot bypass authentication or
storefront restrictions.

== Development ==

Install dependencies with npm install, then run npm run build to compile the
blocks. Run npm start to rebuild them while developing. See README.md for
block usage, contribution instructions, and automated checks.

== Changelog ==

= 0.1.0 =
* Initial collection of Gutenberg blocks: Product Card with Shopify importer,
  Hero Section, and Related Posts.
