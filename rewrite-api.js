jQuery(document).ready(function ($) {
    /**
     * onClick Function
     */
    $(document).on("click", "[data-function]", function (e) {
        e.preventDefault();
        window.rewrite_api_method[jQuery(this).attr('data-function')]($(this));
    });

    /**
     * onChange Function
     */
    $(document).on("change", "[data-function-change]", function (e) {
        e.preventDefault();
        window.rewrite_api_method[jQuery(this).attr('data-function-change')]($(this));
    });

    /**
     * onSubmit Form
     */
    $(document).on("submit", "[data-form-submit]", function (e) {
        e.preventDefault();
        window.rewrite_api_method[jQuery(this).attr('data-form-submit')]($(this));
    });

    /**
     * onKeyUp Function
     */
    $(document).on('keyup', '[data-function-key]', function (e) {
        e.preventDefault();
        window.rewrite_api_method[jQuery(this).attr('data-function-key')]($(this));
    });

    /**
     * Rewrite App Method
     */
    window.rewrite_api_method = {
        /**
         * Check Exist Params
         *
         * @param {*} obj
         */
        isset: function (obj) {
            let args = Array.prototype.slice.call(arguments, 1);

            for (let i = 0; i < args.length; i++) {
                if (!obj || !obj.hasOwnProperty(args[i])) {
                    return false;
                }
                obj = obj[args[i]];
            }
            return true;
        },

        /**
         * Check isObject
         * @param {*} a
         */
        isObject: function (a) {
            return (!!a) && (a.constructor === Object);
        },

        /**
         * Search Text in Variable
         *
         * @param {*} haystack
         * @param {*} needle
         * @param {*} before_needle
         */
        strstr: function (haystack, needle, before_needle = false) {
            if (haystack.indexOf(needle) >= 0)
                return before_needle ? haystack.substr(0, haystack.indexOf(needle))
                    : haystack.substr(haystack.indexOf(needle));
            return false;
        },

        /**
         * In Array
         *
         * @param {*} needle
         * @param {*} haystack
         */
        in_array: function (needle, haystack) {
            for (let i in haystack) {
                if (haystack[i] == needle) return true;
            }
            return false;
        },

        /**
         * Ajax function
         *
         * @param {*} method
         * @param {*} type
         * @param {*} arg
         */
        request: function (method, type = 'GET', arg = {}, params = {}, callback = false) {

            // Default Params
            let ajax_params = {
                url: rewrite_api.url + '/' + rewrite_api.prefix + '/' + method,
                type: type,
                data: arg,
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                cache: false,
                beforeSend: function () {
                    if (callback !== false) {
                        callback({before_send: true});
                    }
                    jQuery(document).trigger('add_action_' + method.replace("/", "_") + '_before', {});
                },
                success: function (data, textStatus, xhr) {
                    if (callback !== false) {
                        callback(data);
                    }
                    jQuery(document).trigger('add_action_' + method.replace("/", "_"), data);
                },
                error: function (xhr, status, error) {
                    let error_response_connection = {'success': false, 'code': 'connection'};
                    let error_response = xhr.responseJSON;
                    if (xhr.readyState == 0) {
                        if (callback !== false) {
                            callback(error_response_connection);
                        }
                        jQuery(document).trigger('add_action_' + method.replace("/", "_") + '_error', error_response_connection);
                    } else {
                        if (callback !== false) {
                            callback(error_response);
                        }
                        jQuery(document).trigger('add_action_' + method.replace("/", "_") + '_error', error_response);
                    }
                }
            };

            // Check Params For POST
            if (type.toUpperCase() === "POST") {
                ajax_params['processData'] = false;
                ajax_params['contentType'] = false;
                ajax_params['mimeType'] = "multipart/form-data";
                ajax_params['dataType'] = "json";
            }

            // Send Request
            $.ajax($.extend({}, ajax_params, params));
        }
    };
});