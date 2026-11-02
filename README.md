# AVCL Blocks

AVCL Blocks is a collection of custom Gutenberg blocks for WordPress, including
a Product Card block with a Shopify product importer.

## Shopify product importer

Paste a Shopify storefront product URL into the Product Card importer. It uses
multiple retrieval methods to support products from a broad range of Shopify
stores:

1. Shopify's product `.js` endpoint.
2. Shopify's product `.json` endpoint.
3. A storefront GraphQL request to discover the store's cookie domain, followed
   by a product `.js` request.
4. A derived `{store-name}.myshopify.com` product URL, tried with `.js` and
   `.json`.

The importer normalizes product details, including the title, description,
price, vendor, category, image gallery, and variants. Imported product images
and variant data are retained in the response for use by the block.

These methods use public storefront endpoints, whose availability is controlled
by each store. Stores may block or restrict product data, and a custom domain
does not always reveal the store's original `myshopify.com` domain. As a result,
the importer supports many Shopify stores but cannot guarantee access to every
store or bypass authentication and storefront restrictions.

## Requirements

- WordPress 6.8 or later
- PHP 7.4 or later

## Installation

1. Copy the `avcl-blocks` directory into `/wp-content/plugins/`.
2. Activate **AVCL Blocks** from the WordPress Plugins screen.
3. Add a Product Card or another AVCL block from the block inserter.

## Development

Install the JavaScript dependencies and build the blocks:

```sh
npm install
npm run build
```

Use the development watcher while editing block source:

```sh
npm start
```

## License

GPL-2.0-or-later, as declared in `avcl-blocks.php`.
