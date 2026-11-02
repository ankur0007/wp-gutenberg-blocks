<?php

namespace AVCL\Blocks\Endpoints;

use AVCL\Blocks\Init;
use WP_Error;

class ShopifyImporter extends Init {
    public function __construct() {}

    public static function byJS(string $product_url, ?string $display_url = null) {
        $response = wp_safe_remote_get($product_url . '.js');

        if (is_wp_error($response)) {
            return new WP_Error(
                'api_error',
                'Failed to connect to Shopify',
                array('status' => 500)
            );
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $status_code    = wp_remote_retrieve_response_code($response);
        $status_message = wp_remote_retrieve_response_message($response);

        if ($status_code !== 200) {
            $error_status = $status_code === 403 ? 404 : $status_code;
            return new WP_Error(
                'invalid_request',
                $status_message,
                ['status' => $error_status]
            );
        }

        return self::formatProductResponse($body, $display_url ?? $product_url);
    }

    public static function byJSON(string $product_url, ?string $display_url = null) {
        $response = wp_safe_remote_get($product_url . '.json');

        if (is_wp_error($response)) {
            return new WP_Error(
                'api_error',
                'Failed to connect to Shopify',
                array('status' => 500)
            );
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $status_code    = wp_remote_retrieve_response_code($response);
        $status_message = wp_remote_retrieve_response_message($response);

        if ($status_code !== 200) {
            $error_status = $status_code === 403 ? 404 : $status_code;
            return new WP_Error(
                'invalid_request',
                $status_message,
                ['status' => $error_status]
            );
        }

        $product = is_array($body)
            ? ($body['product'] ?? $body)
            : null;

        return self::formatProductResponse($product, $display_url ?? $product_url);
    }

    private static function formatProductResponse($product, string $product_url) {
        if (
            !is_array($product) ||
            !isset($product['title']) ||
            !is_string($product['title']) ||
            $product['title'] === ''
        ) {
            return new WP_Error(
                'invalid_product_response',
                'Shopify returned an invalid product response.',
                ['status' => 502]
            );
        }

        $image = $product['featured_image'] ?? '';
        if (is_array($image)) {
            $image = $image['src'] ?? '';
        }

        if (!is_string($image)) {
            $image = '';
        } elseif (strpos($image, '//') === 0) {
            $image = 'https:' . $image;
        }

        $images = is_array($product['images'] ?? null)
            ? $product['images']
            : [];
        $images = array_map(
            static function ($image) {
                if (is_string($image)) {
                    return self::sanitizeShopifyValue($image, 'src');
                }

                return self::sanitizeShopifyValue($image);
            },
            $images
        );

        $tags = $product['tags'] ?? [];
        if (is_string($tags)) {
            $tags = array_map('trim', explode(',', $tags));
        } elseif (!is_array($tags)) {
            $tags = [];
        }

        $variants = is_array($product['variants'] ?? null)
            ? self::sanitizeShopifyValue($product['variants'])
            : [];

        return rest_ensure_response(
            [
                'title'       => sanitize_text_field($product['title']),
                'slug'        => sanitize_title($product['handle'] ?? ''),
                'description' => wp_kses_post($product['description'] ?? ''),
                'image'       => sanitize_url($image),
                'images'      => $images,
                'variants'    => $variants,
                'price'       => intval($product['price'] ?? 0),
                'url'         => sanitize_url($product_url),
                'category'    => sanitize_text_field($product['type'] ?? ''),
                'tags'        => rest_sanitize_array($tags),
                'min_price'   => intval($product['price_min'] ?? $product['min_price'] ?? 0),
                'max_price'   => intval($product['price_max'] ?? $product['max_price'] ?? 0),
                'vendor'      => sanitize_text_field($product['vendor'] ?? ''),
            ]
        );
    }

    private static function sanitizeShopifyValue($value, $key = '') {
        if (is_array($value)) {
            $sanitized = [];

            foreach ($value as $item_key => $item) {
                $sanitized_key = is_int($item_key) ? $item_key : sanitize_key($item_key);
                $sanitized[$sanitized_key] = self::sanitizeShopifyValue(
                    $item,
                    (string) $item_key
                );
            }

            return $sanitized;
        }

        if (is_string($value)) {
            if (preg_match('/(?:url|image|src)$/i', $key)) {
                if (strpos($value, '//') === 0) {
                    $value = 'https:' . $value;
                }

                return sanitize_url($value);
            }

            return sanitize_text_field($value);
        }

        return is_scalar($value) || $value === null ? $value : '';
    }

    public static function byGraphQL(string $product_url) {
        $parts = wp_parse_url($product_url);
        $host  = $parts['host'] ?? '';
        $path  = $parts['path'] ?? '';

        if (!$host || !$path) {
            return new WP_Error(
                'invalid_product_url',
                'Enter a valid Shopify product URL.',
                ['status' => 400]
            );
        }

        $graphql_url = 'https://' . $host . '/api/unstable/graphql.json';
        $response = wp_safe_remote_post($graphql_url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode([
                'query' => 'query ensureCookies { consentManagement { cookies(visitorConsent:{}) { cookieDomain } } }',
            ]),
        ]);

        if (is_wp_error($response)) {
            return new WP_Error(
                'api_error',
                'Failed to connect to Shopify GraphQL.',
                ['status' => 500]
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code < 200 || $status_code >= 300) {
            return new WP_Error(
                'graphql_request_failed',
                'Seems like this product is restricted to fetch details.',
                ['status' => 404]
            );
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        $cookie_domain = $body['data']['consentManagement']['cookies']['cookieDomain'] ?? '';

        if (
            !is_string($cookie_domain) ||
            !preg_match(
                '/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\\.)+[a-z]{2,63}$/i',
                $cookie_domain
            )
        ) {
            return new WP_Error(
                'cookie_domain_unavailable',
                'Shopify did not return a valid cookie domain.',
                ['status' => 404]
            );
        }

        $fallback_url = 'https://' . $cookie_domain . untrailingslashit($path);
        $response = self::byJS($fallback_url);
        if (!is_wp_error($response)) {
            return $response;
        }

        return new WP_Error(
            'restricted_product',
            'Seems like this product is restricted to fetch details',
            ['status' => 404]
        );
    }

    public static function byDomain(string $product_url) {
        $parts = wp_parse_url($product_url);
        if (!is_array($parts)) {
            return new WP_Error(
                'invalid_product_url',
                'Could not derive a Shopify store domain from this product URL.',
                ['status' => 404]
            );
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if (strpos($host, 'www.') === 0) {
            $host = substr($host, 4);
        }

        $store_name = explode('.', $host)[0] ?? '';
        if (
            !$store_name ||
            !preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/', $store_name) ||
            substr($host, -strlen('.myshopify.com')) === '.myshopify.com'
        ) {
            return new WP_Error(
                'invalid_product_url',
                'Could not derive a Shopify store domain from this product URL.',
                ['status' => 404]
            );
        }

        $myshopify_url = 'https://' . $store_name . '.myshopify.com' . $path;
        $product_data = self::byJS($myshopify_url, $product_url);
        if (!is_wp_error($product_data)) {
            return $product_data;
        }

        $error_data = $product_data->get_error_data();
        if (!is_array($error_data) || ($error_data['status'] ?? null) !== 404) {
            return $product_data;
        }

        $product_data = self::byJSON($myshopify_url, $product_url);
        if (is_wp_error($product_data)) {
            $error_data = $product_data->get_error_data();

            if (is_array($error_data) && ($error_data['status'] ?? null) === 404) {
                return new WP_Error(
                    'restricted_product',
                    'Seems like this product is restricted to fetch details',
                    ['status' => 404]
                );
            }
        }

        return $product_data;
    }
}
