jQuery(document).ready(function ($) {
    /**
     * Post Methods
     */
    var post_methods = {
        /**
         * Example Custom Request
         * @param tag
         */
        get_post_id: function ($tag = false, $post_id = 0) {
            // Get user Input
            if ($tag != false) {
                $post_id = $tag.attr('post-id');
            }

            // Send Request
            window.rewrite_api_method.request('post/get', 'GET', {
                'post-id': $post_id
            }, function (result) {
                if (result.before_send) {
                    alert('loading ...');
                } else {
                    if (!result.success) {
                        alert(result.data.message);
                    } else {
                        alert(result.data.message);
                    }
                }
            });
        }
    };

    // Push To global Rewrite API Js
    if (typeof window.rewrite_api_method !== 'undefined') {
        $.extend(window.rewrite_api_method, post_methods);
    }

    /**
     * Example Bind For get Post Custom Request
     */
    $(document).bind('add_action_post_get_before', function (data) {
        // Before Send
    });
    $(document).bind('add_action_post_get', function (data) {
        // success
    });
    $(document).bind('add_action_post_get_error', function (data) {
        // error
    });
});