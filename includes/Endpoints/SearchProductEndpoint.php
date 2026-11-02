<?php

namespace AVCL\Blocks\Endpoints;

use AVCL\Blocks\Init;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

class SearchProductEndpoint extends Init
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'rest_init']);
    }

    public function rest_init()
    {
        register_rest_route('avcl/blocks/v1', '/product-search/', [
            'methods' =>  WP_REST_Server::READABLE,
            'callback' => [$this, 'fetch'],
            'permission_callback' => [$this, 'check'],
            'args' => [
                'post_id' => [
                    'required' => true,
                    'validate_callback' => (function (int $param) {
                        if (is_numeric($param) && get_post($param)) {
                            return true;
                        } else {
                            return new WP_Error('invalid_post', 'Please provide a valid numeric post ID', ['status' => 400]);
                        }
                    })
                ],
                'platform' => [
                    'required' => true,
                    'validate_callback' => function ($value) {
                        $allowed_platforms = ['shopify'];

                        if (!is_string($value) || !in_array($value, $allowed_platforms, true)) {
                            return new WP_Error(
                                'invalid_platform',
                                'Select a supported platform.',
                                ['status' => 400]
                            );
                        }

                        return true;
                    },
                ],
                'method' => [
                    'required' => false,
                    'validate_callback' => function ($value) {
                        if (!is_string($value) || !in_array($value, ['js', 'json', 'graphql', 'domain'], true)) {
                            return new WP_Error(
                                'invalid_import_method',
                                'Select a supported Shopify import method.',
                                ['status' => 400]
                            );
                        }

                        return true;
                    },
                ],
                'product_url' => [
                    'required' => true,
                    'validate_callback' => (function (string $param, $request) {
                        if (empty($param)) {
                            return new WP_Error('empty_url', 'Product URL can not be empty', ['status' => 400]);
                        }
                        if (!wp_http_validate_url($param)) {
                            return new WP_Error('invalid_url', 'Product URL is not valid', ['status' => 400]);
                        }

                        $parts = wp_parse_url($param);
                        $path  = $parts['path'] ?? '';

                        if (
                            $request->get_param('platform') === 'shopify' && (
                                ! wp_http_validate_url($param) ||
                                ! is_array($parts) ||
                                strtolower($parts['scheme'] ?? '') !== 'https' ||
                                ! preg_match('#^/products/(?:[^/]+/)*[^/]+/?$#i', $path))
                        ) {
                            return new WP_Error(
                                'invalid_product_url',
                                'Enter a valid HTTPS Shopify product URL.',
                                ['status' => 400]
                            );
                        }
                        return true;
                    })
                ]
            ]
        ]);
    }

    public function check(WP_REST_Request $request)
    {
        $post_id = $request->get_param('post_id');
        if (is_user_logged_in() && current_user_can('edit_post', $post_id)) {
            return true;
        }
        return false;
    }

    public function get_token()
    {
        return null;
    }

    public function fetch(WP_REST_Request $request)
    {
        $product_url = sanitize_url($request->get_param('product_url'));
        $platform = sanitize_text_field($request->get_param('platform'));
        $method = $request->get_param('method');

        $url_parts = wp_parse_url($product_url);
        if (is_array($url_parts) && !empty($url_parts['host'])) {
            $product_url = 'https://' . $url_parts['host']
                . (isset($url_parts['port']) ? ':' . $url_parts['port'] : '')
                . ($url_parts['path'] ?? '');
        }

        if ($platform !== 'shopify') {
            return new WP_Error('invalid_platform', 'Select a supported platform.', ['status' => 400]);
        }

        if ($method === null || $method === '') {
            $product_data = ShopifyImporter::byJS($product_url);
            if (!is_wp_error($product_data)) {
                return $product_data;
            }

            $product_data = ShopifyImporter::byJSON($product_url);
            if (!is_wp_error($product_data)) {
                return $product_data;
            }

            $product_data = ShopifyImporter::byGraphQL($product_url);
            if (!is_wp_error($product_data)) {
                return $product_data;
            }

            $product_data = ShopifyImporter::byDomain($product_url);
            if (!is_wp_error($product_data)) {
                return $product_data;
            }

            return new WP_Error(
                'restricted_product',
                'Seems like this product is restricted to fetch details',
                ['status' => 404]
            );
        }

        switch ($method) {
            case 'js':
                return ShopifyImporter::byJS($product_url);
            case 'json':
                return ShopifyImporter::byJSON($product_url);
            case 'graphql':
                return ShopifyImporter::byGraphQL($product_url);
            case 'domain':
                return ShopifyImporter::byDomain($product_url);
            default:
                return new WP_Error(
                    'invalid_import_method',
                    'Select a supported Shopify import method.',
                    ['status' => 400]
                );
        }
    }
}
