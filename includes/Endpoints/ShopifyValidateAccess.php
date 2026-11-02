<?php

namespace AVCL\Blocks\Endpoints;
use AVCL\Blocks\Init;
use WP_Error;
use WP_REST_Request;

class ShopifyValidateAccess extends Init
{

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'rest_init']);
    }

    public function rest_init()
    {
        register_rest_route('avcl-blocks/v1/', 'settings/save-access', [
            'methods'=> 'POST',
            'callback' => [$this, 'save'],
            'permission_callback' => [$this, 'validate_params'],
            'args' => [
                'clientId' => [
                    'required' => true,
                    'validate_callback' => function (string $param) {
                        if (empty($param)) {
                            return new WP_Error('empty_client_id', __('Client ID should not be empty'), ['status' => 400]);
                        }
                        return true;
                    }
                ],
                'clientSecret' => [
                    'required' => true,
                    'validate_callback' => function (string $param) {
                        if (empty($param)) {
                            return new WP_Error('empty_client_secret', __('Client secret should not be empty'), ['status' => 400]);
                        }
                        return true;
                    }

                ],
                'shopDomain' => [
                    'required' => true,
                    'validate_callback' => function (string $param) {
                        if (empty($param)) {
                            return new WP_Error('empty_shop_url', __('shop URL should not be empty'), ['status' => 400]);
                        }
                        if ( !preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/i', $param ) ) {

                            return new WP_Error('invalid_shop_url', __('shop domain is invalid'), ['status' => 400]);
                        }
                        return true;
                    }
                ]
            ]
        ]);
    }

    public function validate_params()
    {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return new WP_Error('unauthorized_call', __('Unauthorized access'), ['status' => 401]);
        }
        return true;
    }

   
    public function remove_url_scheme( string $url ): string {
        return preg_replace( '~^https?://~i', '', trim( $url ) ) ?? '';
    }

    public function save(WP_REST_Request $request)
    {
        $shop_domain = sanitize_text_field($request->get_param('shopDomain'));
        $ID = sanitize_text_field($request->get_param('clientId'));
        $secret = sanitize_text_field($request->get_param('clientSecret'));

        $shop_url = $this->remove_url_scheme($shop_domain);
        $shop_url = "https://$shop_url.myshopify.com/admin/oauth/access_token";
        
        $response = wp_remote_post(
            $shop_url,
            [
                'body' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $ID,
                    'client_secret' => $secret,
                ],
            ]
        );

        
        if (is_wp_error($response)) {
            return new WP_Error(
                'shopify_connection_failed',
                __('Could not reach Shopify. Check your connection and try again.', 'avcl-blocks'),
                ['status' => 502]
            );
        }
        
        $body = wp_remote_retrieve_body($response);
        $status = wp_remote_retrieve_response_code($response);
        if ($status < 200 || $status >= 300) {
            if (in_array($status, [400, 401, 403], true)) {
                return new WP_Error(
                    'shopify_credentials_rejected',
                    __('Shopify rejected these credentials. Check the Client ID, Client Secret, and shop name.', 'avcl-blocks'),
                    ['status' => 400]
                );
            }

            if ($status === 404) {
                return new WP_Error(
                    'shopify_shop_not_found',
                    __('Shopify could not find this shop. Check the shop name and try again.', 'avcl-blocks'),
                    ['status' => 404]
                );
            }

            if ($status === 429) {
                return new WP_Error(
                    'shopify_rate_limited',
                    __('Shopify is receiving too many requests. Please wait a moment and try again.', 'avcl-blocks'),
                    ['status' => 429]
                );
            }

            return new WP_Error(
                'shopify_request_failed',
                __('Shopify could not complete the request. Please try again later.', 'avcl-blocks'),
                ['status' => 502]
            );
        }

        $data = json_decode($body, true);
        $access_token = is_array($data) && is_string($data['access_token'] ?? null)
            ? $data['access_token']
            : '';
        $expires_in = is_array($data) && is_numeric($data['expires_in'] ?? null)
            ? (int) $data['expires_in']
            : 0;

        if ($access_token === '' || $expires_in <= 0) {
            return new WP_Error(
                'shopify_invalid_response',
                __('Shopify returned an invalid response. Please try again later.', 'avcl-blocks'),
                ['status' => 502]
            );
        }

        $transient_ttl = max(1, $expires_in - HOUR_IN_SECONDS);

        update_option(
            'avcl_shopify_settings',
            [
                'shop_domain'   => $shop_domain,
                'client_id'     => $ID,
                'client_secret' => $secret,
            ],
            false
        );
        set_transient('avcl_shopify_access_token', $access_token, $transient_ttl);

        return rest_ensure_response(['success' => true]);
        
    }
}
