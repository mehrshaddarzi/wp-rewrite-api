<?php

/**
 * Plugin Name: Rewrite API For WordPress
 * Description: A Plugin For Developing Rewrite API as Admin-ajax in WordPress
 * Plugin URI:  https://realwp.net
 * Version:     1.0.0
 * Author:      Mehrshad Darzi
 * Author URI:  https://realwp.net
 * License:     MIT
 * Text Domain: wp-rewrite-api-request
 * Domain Path: /languages
 */

namespace WordPress_Rewrite_API_Request;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Class WordPress_Rewrite_API_Request
 * @package WordPress_Rewrite_API_Request
 * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
class WordPress_Rewrite_API_Request
{
    /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '5.4.0';

    /**
     * URL to this plugin's directory.
     *
     * @type string
     * @status Core
     */
    public static $plugin_url;

    /**
     * Path to this plugin's directory.
     *
     * @type string
     * @status Core
     */
    public static $plugin_path;

    /**
     * Path to this plugin's directory.
     *
     * @type string
     * @status Core
     */
    public static $plugin_version;

    /**
     * Plugin instance.
     *
     * @see get_instance()
     * @status Core
     */
    protected static $_instance = null;

    /**
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @return  object of this class
     * @since   2012.09.13
     */
    public static function instance()
    {
        null === self::$_instance and self::$_instance = new self;
        return self::$_instance;
    }

    /**
     * WP_MVC constructor.
     */
    public function __construct()
    {
        /*
         * Check Require Php Version
         */
        if (version_compare(PHP_VERSION, $this->min_php, '<=')) {
            add_action('admin_notices', array($this, 'php_version_notice'));
            return;
        }

        /*
         * Define Variable
         */
        $this->define_constants();

        /*
         * include files
         */
        $this->includes();

        /*
         * init Wordpress hook
         */
        $this->init_hooks();

        /*
         * Plugin Loaded Action
         */
        do_action('wp_rewrite_api_loaded');
    }

    /**
     * Define Constant
     */
    public function define_constants()
    {
        /*
         * Get Plugin Data
         */
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $plugin_data = get_plugin_data(__FILE__);

        /*
         * Set Plugin Version
         */
        self::$plugin_version = $plugin_data['Version'];

        /*
         * Set Plugin Url
         */
        self::$plugin_url = plugins_url('', __FILE__);

        /*
         * Set Plugin Path
         */
        self::$plugin_path = plugin_dir_path(__FILE__);
    }

    /**
     * Used for regular plugin work.
     *
     * @wp-hook init Hook
     * @return  void
     */
    public function init_hooks()
    {
        register_activation_hook(__FILE__, array($this, 'run_install'));
        register_deactivation_hook(__FILE__, array($this, 'run_uninstall'));
    }

    /**
     * Add Rewrite Rule
     */
    public function run_install()
    {
        // Trigger our function that registers the custom post type plugin.
        self::prefix_add_api_endpoints();
        // Clear the permalinks after the post type has been registered.
        flush_rewrite_rules();
    }

    /**
     * Run Deactivate Plugin
     */
    public function run_uninstall()
    {
        flush_rewrite_rules();
    }

    /**
     * Show notice about PHP version
     *
     * @return void
     */
    function php_version_notice()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $error = __('Your installed PHP Version is:', 'woocommerce-dev') . PHP_VERSION . '. ';
        $error .= __('The <strong>Plugin</strong> plugin requires PHP version <strong>', 'woocommerce-dev') . $this->min_php . __('</strong> or greater.', 'woocommerce-dev');
        ?>
        <div class="error">
            <p><?php printf($error); ?></p>
        </div>
        <?php
    }

    /**
     * include Plugin Require File
     */
    public function includes()
    {
        add_action('plugins_loaded', array($this, 'load_php_files'), 10);
        add_action('init', array(__CLASS__, 'prefix_add_api_endpoints'));
        add_action('template_redirect', array($this, 'prefix_do_api'));
        add_filter('posts_request', array($this, 'disable_main_query_wordpress'), 10, 2);
        add_action('wp_enqueue_scripts', array($this, 'register_js_script'), 5);
    }

    /**
     * Load PHP File List
     */
    public function load_php_files()
    {
        // Load Template function
        require_once self::$plugin_path . '/template-function.php';

        /**
         * Load Custom UI Ajax Loading Component
         *
         * 1) Overhang.js | https://paulkr.github.io/overhang.js/
         * 2) PlaceHolder Loading | https://github.com/zalog/placeholder-loading
         * 3) Jquery Confirm | https://craftpip.github.io/jquery-confirm/
         * 4) Box Cover Modal
         */
        if (apply_filters('rewrite_api_request_ui_component', true) === true) {
            require_once self::$plugin_path . '/ui-component.php';
        }
    }

    /**
     * Get Rewrite API Prefix
     */
    public static function getRewriteAPIPrefix()
    {
        return apply_filters('rewrite_api_request_prefix_url', 'api');
    }

    /**
     * Get Rewrite Ajax Url
     *
     * @param string $class
     * @param string $method
     * @return string
     */
    public static function getRewriteAjaxUrl($class = '', $method = '')
    {
        $basic = trailingslashit(home_url()) . self::getRewriteAPIPrefix() . '/' . $class;
        if (!empty($method)) {
            $basic = $basic . '/' . $method;
        }

        return $basic;
    }

    /**
     * Add Prefix API Endpoint
     */
    public static function prefix_add_api_endpoints()
    {
        add_rewrite_tag('%rewrite_class%', '([^&/]+)'); //([0-9]+)
        add_rewrite_tag('%rewrite_method%', '([^&/]+)'); //([0-9]+)
        add_rewrite_rule(self::getRewriteAPIPrefix() . '/?([^/]*)/([^/]*)/?', 'index.php?rewrite_class=$matches[1]&rewrite_method=$matches[2]', 'top');
    }

    /**
     * Disable WordPress Query From Rewrite AJAx
     *
     * @param $request
     * @param $query
     * @return bool
     */
    public function disable_main_query_wordpress($request, $query)
    {
        global $wp_query;
        $Rewrite_Class = $wp_query->get('rewrite_class');
        $Rewrite_Method = $wp_query->get('rewrite_method');
        if ($query->is_main_query() && !empty($Rewrite_Class) && !empty($Rewrite_Method)) {
            return false;
        } else {
            return $request;
        }
    }

    /**
     * Get Basic View Render Directory
     *
     * @return mixed|void
     */
    public static function getBasicViewDir()
    {
        $path = 'php/view/ajax/';
        return apply_filters('rewrite_api_request_basic_view_dir', $path);
    }

    /**
     * Run Rewrite API Ajax
     */
    function prefix_do_api()
    {
        global $wp_query;

        // Check Params Class and Method
        $class = $wp_query->get('rewrite_class');
        $method = $wp_query->get('rewrite_method');
        if (empty($method) || empty($class)) {
            return;
        }

        // Check Show View Html Template
        if ($class == "view") {

            // Check Exist File
            $file_path = rtrim(get_template_directory()) . '/' . rtrim(self::getBasicViewDir(), "/") . "/" . $method . '.php';
            if (!file_exists($file_path)) {
                wp_send_json(
                    array(
                        'message' => __('The File not exist.', 'wp-rewrite-api-request'),
                        'file' => $file_path
                    ),
                    403
                );
            }

            // Load Template
            ob_start();
            get_template_part(self::getBasicViewDir() . $method);
            $html = ob_get_clean();

            // Response
            wp_send_json(array(
                'html' => $html
            ), 200);
        }

        // Check Exist Class
        $class_name = '\\' . __NAMESPACE__ . '\\' . $class;
        if (!class_exists($class_name)) {
            wp_send_json(
                array(
                    'message' => __('This class not exist.', 'wp-rewrite-api-request')
                ),
                403
            );
        }

        // Check Exist Method
        if (substr($method, 0, 1) == "_" || !method_exists($class_name, $method)) {
            wp_send_json(
                array(
                    'message' => __('This method not exist.', 'wp-rewrite-api-request')
                ),
                403
            );
        }

        // Run Method
        $class_name::{$method}();
    }

    /**
     * Register JS Script
     */
    public function register_js_script()
    {
        $show_js = apply_filters('rewrite_api_request_show_js_variable', true);
        if ($show_js) {
            wp_enqueue_script('wp-rewrite-api', self::$plugin_url . '/rewrite-api.js', array('jquery'), self::$plugin_version, true);
            $rewrite_api_localize = array(
                'url' => rtrim(get_site_url(), "/"),
                'prefix' => self::getRewriteAPIPrefix(),
                'page' => self::get_wordpress_page_type(),
                'object_id' => get_queried_object_id(),
                'auth' => (is_user_logged_in() === true ? 1 : 0),
                'token' => wp_create_nonce('rewrite-ajax-nonce')
            );
            wp_localize_script('wp-rewrite-api', 'rewrite_api', apply_filters('rewrite_api_request_localize', $rewrite_api_localize));
        }
    }

    /**
     * Get WordPress Page Type
     */
    public static function get_wordpress_page_type()
    {
        global $wp_query;
        $loop = 'not-found';

        if ($wp_query->is_page) {
            $loop = is_front_page() ? 'front' : 'page';
        } elseif ($wp_query->is_home) {
            $loop = 'home';
        } elseif ($wp_query->is_single) {
            $loop = ($wp_query->is_attachment) ? 'attachment' : 'single';
        } elseif ($wp_query->is_category) {
            $loop = 'category';
        } elseif ($wp_query->is_tag) {
            $loop = 'tag';
        } elseif ($wp_query->is_tax) {
            $loop = 'tax';
        } elseif ($wp_query->is_archive) {
            if ($wp_query->is_day) {
                $loop = 'day';
            } elseif ($wp_query->is_month) {
                $loop = 'month';
            } elseif ($wp_query->is_year) {
                $loop = 'year';
            } elseif ($wp_query->is_author) {
                $loop = 'author';
            } else {
                $loop = 'archive';
            }
        } elseif ($wp_query->is_search) {
            $loop = 'search';
        } elseif ($wp_query->is_404) {
            $loop = 'not-found';
        } elseif ($wp_query->is_author) {
            $loop = 'author';
        }

        return $loop;
    }

    /**
     * List Of Default Error in API
     */
    public static function auth_error()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'code' => 'auth_user',
                'message' => apply_filter('rewrite_api_request_auth_error_message', __('Please login your account.', 'wp-rewrite-api-request'))
            ), 400);
        }
    }

    public static function missing_params()
    {
        wp_send_json_error(array(
            'code' => 'require_params',
            'message' => __('Missing parameter(s).', 'wp-rewrite-api-request')
        ), 400);
    }

    public static function invalid_param($parameter)
    {
        wp_send_json_error(array(
            'code' => 'require_params',
            'message' => sprintf(
                __('Invalid parameter(s): %s', 'wp-rewrite-api-request'),
                $parameter
            )
        ), 400);
    }

    public static function empty_param($parameter)
    {
        wp_send_json_error(array(
            'code' => 'empty_params',
            'message' => sprintf(
                __('Please fill %s', 'wp-rewrite-api-request'),
                $parameter
            )
        ), 400);
    }

    public static function not_permission()
    {
        wp_send_json_error(array(
            'code' => 'not_permission',
            'message' => __('No access to this Request.', 'wp-rewrite-api-request')
        ), 400);
    }

    public static function not_success_action()
    {
        wp_send_json_error(array(
            'code' => 'not_success_request',
            'message' => __('The operation was not successful, please try again.', 'wp-rewrite-api-request')
        ), 400);
    }
}

/**
 * Main instance of WP_Plugin.
 *
 * @since  1.1.0
 */
function wp_rewrite_api_request_dev()
{
    return WordPress_Rewrite_API_Request::instance();
}

// Global for backwards compatibility.
$GLOBALS['wp-rewrite-api-request'] = wp_rewrite_api_request_dev();