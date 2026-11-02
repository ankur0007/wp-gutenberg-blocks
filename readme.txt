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

== Adding a block ==

Create a new directory under src using the block slug, for example src/author-card/.
Each block has its own block.json, index.js, edit.js, and render.php. Add save.js,
view.js, and style/editor styles when needed. In index.js, register that block using
its block.json metadata. Set its block name to avcl-blocks/<block-slug>.

Run npm run build to compile all block directories and regenerate the metadata
collection manifest. The plugin registers every block in build through that manifest.
Run npm start during development to rebuild blocks when their source files change.

The existing Related Posts block keeps its original registered name
(create-block/avcl-related-posts) so posts already using it continue to load.

== Changelog ==

= 0.1.0 =
* Initial AVCL Blocks collection with the Related Posts block.
