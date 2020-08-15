# wp-rewrite-api
Use Rewrite API Method for Ajax Request in WordPress

### How to Install and Use
Activate this plugin, then Flush Rewrite in WordPress.

### How to Create New Route

use `WordPress_Rewrite_API_Request` namespace in your class.


```php
namespace WordPress_Rewrite_API_Request;

class post
{

    /**
     * @request-url => http://site.com/rewrite-api/post/get
     */
    public static function get()
    {
        wp_send_json_success(array(
            'post_id' => 1
        ), 200);
    }
}
```
