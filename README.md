# WordPress Rewrite API Ajax
Use Rewrite API Method for Ajax Request in WordPress.

Rewrite API in WordPress is Very Faster than `admin-ajax.php`.

### How to Install and Use
Activate this plugin, then Flush Rewrite in WordPress.

### How to Create New Route

use `WordPress_Rewrite_API_Request` namespace in your class.


```php
namespace WordPress_Rewrite_API_Request;

class post
{

    /**
     * @request_url => http://site.com/api/post/get
     */
    public static function get()
    {
        wp_send_json_success(array(
            'data' => get_post($_REQUEST['post_id'], ARRAY_A)
        ), 200);
    }

}
```

And use rewrite_api variable in js:

```js
$.ajax({
    url: rewrite_api.url + '/' + rewrite_api.prefix + '/post/get',
    type: 'GET'
});
```

### Create Flexible Methods

We want to create a function to add to cart in WooCommerce:

##### 1) Create PHP Class
```php
namespace WordPress_Rewrite_API_Request;

class wc_cart
{
    
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, '_register_js_script'), 7);
    }

    // Add Js Script
    // Helper Function start with _
    public function _register_js_script()
    {
        wp_enqueue_script('woocommerce-cart-rewrite', '../wc-cart.js' , array('jquery', 'wp-rewrite-api'), '1.0.0', true);
    }

    /**
     * @request_url => http://site.com/api/wc_cart/add
     */
    public static function add()
    {
        // Check Isset Params
        if(!isset($_REQUEST['product_id'])) {
            WordPress_Rewrite_API_Request::empty_param('product_id');
        }
           
        // Add To Product
        $cart_hash_item = WC()->cart->add_to_cart(sanitize_text_field($_REQUEST['product_id']), 1);

        // Response
        wp_send_json_success(array(
            'cart_key' => $cart_hash_item
        ), 200);
    }
}
```

##### 2) Create Js File (wc-cart.js)

```js
jQuery(document).ready(function ($) {

    let woocommerce_cart_methods = {
        wc_add_to_cart: function ($tag = false, $product_id = 0) {

            // Sanitize Params
            if ($tag !== false) {
                $product_id = $tag.attr('data-product-id');
            }
            window.rewrite_api_method.request('wc_cart/add', 'GET', {
                'product_id': $product_id
            }, $tag);
        }
    };

    // Push To global Rewrite API Js
    if (typeof window.rewrite_api_method !== 'undefined') {
        $.extend(window.rewrite_api_method, woocommerce_cart_methods);
    }
});
```

##### 3) Use in Every Project according to FrontEnd

```html
<a href="#" data-function="wc_add_to_cart" data-product-id="123">Add to Cart</a>
```


```js
jQuery(document).ready(function ($) {
    
    // Get All Methods
    const _methods = window.rewrite_api_method;
    const add_action = window.rewrite_api_method['add_action'];

    /**
     * Add Action For Add to Cart
     */
    add_action('wc_cart/add',
        function (data, event) {
            alert('before-send');
        }, function (data, event) {
            alert('success');
        }, function (data, event) {
            alert(_methods.to_json(data));
    });
});
```


### How to Change Prefix `api` Url

use `add_filter('rewrite_api_request_prefix_url', 'api');` in WordPress, then flush rewrite;


### How to Get My Custom Url in Template function

```php
echo get_rewrite_api_url($class = 'user', $method = 'login'); // http://site.com/rewrite-api/user/login
```

### How To Disable Load Custom UI Component

```php
add_filter('rewrite_api_request_ui_component', '__return_false');
```

For Disable Custom Ui Component, see `ui-component.php` and use every filter. for example:

```php
add_filter('rewrite_api_request_ui_component_jquery_confirm', '__return_false');
```

