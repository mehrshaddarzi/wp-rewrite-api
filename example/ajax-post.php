<?php

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
        /**
         * {
            "success": true,
            "data": {
                "post_id": 1
                }
            }
         */
    }

    public static function error()
    {
        if(!isset($_REQUEST['id'])) {
            wp_send_json_error( array(
                'code' => 'not_found',
                'message' => 'post id not found'
            ), 400 );

            /**
             * {
                "success": false,
                "data": {
                    "code": "not_found",
                    "message": "post id not found"
                  }
                }
             */
        }
    }

}