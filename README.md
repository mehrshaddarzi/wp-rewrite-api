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
     * @request-url => http://site.com/api/post/get
     */
    public static function get()
    {
        wp_send_json_success(array(
            'post_id' => 1
        ), 200);
    }
}
```

and use rewrite_api varible in js:

```js
 $(document).on("click", "[data-function=get-post]", function (e) {
        e.preventDefault();

        $.ajax({
            url: rewrite_api.url + '/' + rewrite_api.prefix + '/post/get',
            type: 'GET',
            cache: false,
            data: {},
            dataType: "json",
            beforeSend: function () {
                jQuery(document).trigger('rewrite_do_action_get_post_before', {});
            },
            success: function (data) {
                jQuery(document).trigger('rewrite_do_action_get_post', {return: data});
            },
            error: function (xhr, textStatus, errorThrown) {
                jQuery(document).trigger('rewrite_do_action_get_post_error', {message: xhr.responseJSON.message});
            }
        });
    });
```


### How to Change Prefix `api` Url

use `add_filter('rewrite_api_request_prefix_url', 'api');` in WordPress, then flush rewrite;


### How to Get My Custom Url in Template function

```php
echo get_rewrite_api_url($class = 'user', $method = 'login'); // http://site.com/rewrite-api/user/login
```

