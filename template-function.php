<?php

function get_rewrite_api_url($class = '', $method = '')
{
    return \WordPress_Rewrite_API_Request\WordPress_Rewrite_API_Request::getRewriteAjaxUrl($class, $method);
}

function get_rewrite_api_prefix_url()
{
    return \WordPress_Rewrite_API_Request\WordPress_Rewrite_API_Request::getRewriteAPIPrefix();
}