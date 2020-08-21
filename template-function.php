<?php

function get_rewrite_api_url($class = '', $method = '')
{
    return \WordPress_Rewrite_API_Request\WordPress_Rewrite_API_Request::getRewriteAjaxUrl($class, $method);
}

function get_rewrite_api_prefix_url()
{
    return \WordPress_Rewrite_API_Request\WordPress_Rewrite_API_Request::getRewriteAPIPrefix();
}

function wp_rewrite_send_json_success($data = null, $status_code = 200)
{
    //@TODO Refresh wp_create_nonce and push to window.rewrite_api.nonce if nonce is activate
    $data = apply_filters('rewrite_api_request_response_data', $data, 'success', $status_code);
    wp_send_json_success($data, $status_code);
}

function wp_rewrite_send_json_error($data = null, $status_code = 400)
{
    //@TODO Refresh wp_create_nonce and push to window.rewrite_api.nonce if nonce is activate
    $data = apply_filters('rewrite_api_request_response_data', $data, 'error', $status_code);
    wp_send_json_error($data, $status_code);
}