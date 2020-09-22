<?php

class WordPress_Rewrite_API_Request_Ui_Component
{
    public function __construct()
    {
        // Load Script
        add_action('wp_enqueue_scripts', array($this, 'register_js_script'), 6);

        // Custom Localize For OverHang or jQuery Confirm
        if (apply_filters('rewrite_api_request_ui_component_overhang_js', true) === true || apply_filters('rewrite_api_request_ui_component_jquery_confirm', true) === true) {
            add_filter('rewrite_api_request_localize', function ($localize) {
                $localize['wait'] = __('Please wait...', 'wp-rewrite-api-request');
                return $localize;
            }, 9);
        }

        // Custom Localize For Jquery-Confirm
        if (apply_filters('rewrite_api_request_ui_component_jquery_confirm', true) === true) {
            add_filter('rewrite_api_request_localize', function ($localize) {
                $localize['svg_icon_color'] = '#FF324D';
                return $localize;
            }, 9);
        }

        // Show Alert in web page Overhang
        if (apply_filters('rewrite_api_request_ui_component_overhang_js', true) === true and isset($_GET['overhang_alert']) and isset($_GET['overhang_type'])) {
            add_filter('rewrite_api_request_localize', function ($localize) {
                $text = self::get_overhang_text(sanitize_text_field($_GET['overhang_alert']));
                if (!is_null($text)) {
                    $localize['overhang_alert'] = array(
                        'type' => sanitize_text_field($_GET['overhang_type']),
                        'text' => self::get_overhang_text(sanitize_text_field($_GET['overhang_alert'])),
                    );
                }
                return $localize;
            }, 9);
        }
    }

    public function register_js_script()
    {
        $plugin_url = \WordPress_Rewrite_API_Request\WordPress_Rewrite_API_Request::$plugin_url;
        $plugin_version = \WordPress_Rewrite_API_Request\WordPress_Rewrite_API_Request::$plugin_version;

        // Overhang Js | https://paulkr.github.io/overhang.js/
        if (apply_filters('rewrite_api_request_ui_component_overhang_js', true) === true) {
            wp_enqueue_script('overhang-js', $plugin_url . '/asset/overhang/overhang.min.js', array('jquery', 'wp-rewrite-api'), '1.0.3', true);
            wp_enqueue_style('overhang-js', $plugin_url . '/asset/overhang/overhang.css', array(), '1.0.3', 'all');
            wp_enqueue_script('overhang-js-rewrite', $plugin_url . '/asset/overhang/script.js', array('jquery', 'wp-rewrite-api'), '1.0.3', true);
        }

        // Placeholder loading | https://github.com/zalog/placeholder-loading
        if (apply_filters('rewrite_api_request_ui_component_placeholder', true) === true) {
            wp_enqueue_style('placeholder-loading', $plugin_url . '/asset/placeholder/placeholder.css', array(), '0.2.6', 'all');
            wp_enqueue_script('placeholder-rewrite', $plugin_url . '/asset/placeholder/script.js', array('jquery', 'wp-rewrite-api'), '0.2.6', true);
        }

        // jQuery Confirm | https://craftpip.github.io/jquery-confirm/
        if (apply_filters('rewrite_api_request_ui_component_jquery_confirm', true) === true) {
            wp_enqueue_style('jquery-confirm', $plugin_url . '/asset/jquery-confirm/jquery-confirm.min.css', array(), '3.3.2', 'all');
            wp_enqueue_script('jquery-confirm', $plugin_url . '/asset/jquery-confirm/jquery-confirm.min.js', array('jquery', 'wp-rewrite-api'), '3.3.2', true);
            wp_enqueue_script('jquery-confirm-rewrite', $plugin_url . '/asset/jquery-confirm/script.js', array('jquery', 'wp-rewrite-api'), '3.3.2', true);
        }

        // Box Cover
        if (apply_filters('rewrite_api_request_ui_component_box_cover', false) === true) {
            wp_enqueue_style('box-cover', $plugin_url . '/asset/box-cover/box-cover.css', array(), $plugin_version, 'all');
            wp_enqueue_script('box-cover-js', $plugin_url . '/asset/box-cover/script.js', array('jquery', 'wp-rewrite-api'), $plugin_version, true);
        }
    }

    public static function get_overhang_text($type)
    {
        $array = apply_filters('rewrite_api_request_overhang_alert', array(
            'test_alert' => __('Example Overhang Alert', 'wp-rewrite-api-request')
        ));

        if (isset($array[$type])) {
            return $array[$type];
        }

        return null;
    }

    public static function generate_overhang_link($url, $type = 'success', $alert)
    {
        return add_query_arg(array(
            'overhang_type' => $type,
            'overhang_alert' => $alert,
        ), $url);
    }
}

new WordPress_Rewrite_API_Request_Ui_Component;