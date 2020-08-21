jQuery(document).ready(function ($) {
    /**
     * onClick Function
     */
    $(document).on("click", "[data-function]", function (e) {
        e.preventDefault();
        window.rewrite_api_method[$(this).attr('data-function')]($(this));
    });

    /**
     * onChange Function
     */
    $(document).on("change", "[data-function-change]", function (e) {
        e.preventDefault();
        window.rewrite_api_method[$(this).attr('data-function-change')]($(this));
    });

    /**
     * onSubmit Form
     */
    $(document).on("submit", "[data-form-submit]", function (e) {
        e.preventDefault();
        window.rewrite_api_method[$(this).attr('data-form-submit')]($(this));
    });

    /**
     * onKeyUp Function
     */
    $(document).on('keyup', '[data-function-key]', function (e) {
        e.preventDefault();
        window.rewrite_api_method[$(this).attr('data-function-key')]($(this));
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
         * Convert Object or Array to Json
         *
         * @param array
         * @returns {string}
         */
        to_json: function (array) {
            return JSON.stringify(array);
        },

        /**
         * Convert Json to Array
         *
         * @param json
         * @returns {any}
         */
        to_array: function (json) {
            return JSON.parse(json);
        },

        /**
         * Check File Size
         *
         * @param $id
         * @param $mb
         */
        check_file_size: function ($id, $mb) {
            let f = $("input[id=" + $id + "]")[0].files[0];
            let mb = $mb * 1024 * 1024;
            if (f.size > mb || f.fileSize > mb) {
                return true;
            }
            return false;
        },

        /**
         * Check File Extension
         *
         * @param $id
         * @param $ext_array
         */
        check_file_ext: function ($id, $ext_array = ['jpeg', 'jpg', 'png']) {
            let file_upload = $("input[id=" + $id + "]").val();
            if ($.inArray(file_upload.split('.').pop().toLowerCase(), $ext_array) === -1) {
                return false;
            }

            return true;
        },

        /**
         * Get List Of Object data- attribute from Tag
         *
         * @param $tag | tag is jQuery Object $("#div)
         * @returns {{}}
         */
        attr_data: function ($tag) {
            let attr = {};
            $tag.each(function () {
                $.each(this.attributes, function (i, a) {
                    if (a.name.substr(0, 4) === "data") {
                        attr[a.name.replace("data-", "")] = a.value;
                    }
                });
            });
            return attr;
        },

        /**
         * Ajax function
         *
         * @param {*} method
         * @param {*} type
         * @param {*} arg
         * @param callback
         * @param params
         */
        request: function (method, type = 'GET', arg = {}, callback = false, params = {}) {

            // Extend in arg
            if (this.isset(window.rewrite_api_method, 'input_' + method.replace("/", "_"))) {
                arg = $.extend(arg, window.rewrite_api_method['input_' + method.replace("/", "_")]());
            }

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
                    $(document).trigger('add_action_' + method.replace("/", "_") + '_before', {});
                },
                success: function (data) {
                    if (callback !== false) {
                        callback(data);
                    }
                    $(document).trigger('add_action_' + method.replace("/", "_"), data);
                },
                error: function (xhr, status, error) {
                    let error_response_connection = {'success': false, 'code': 'connection'};
                    let error_response = $.extend({'success': false}, xhr.responseJSON);
                    if (xhr.readyState === 0) {
                        if (callback !== false) {
                            callback(error_response_connection);
                        }
                        $(document).trigger('add_action_' + method.replace("/", "_") + '_error', error_response_connection);
                    } else {
                        if (callback !== false) {
                            callback(error_response);
                        }
                        $(document).trigger('add_action_' + method.replace("/", "_") + '_error', error_response);
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
        },

        /**
         * View in Ajax
         */
        view: function ($tag = false, $view = '', $params = {}) {
            // Get View
            if ($tag.attr('data-view')) {
                $view = $tag.attr('data-view');
            }

            // Get Custom $params
            let attribute = this.attr_data($tag);
            if (Object.keys(attribute).length > 1) {
                $params = attribute;
            }

            window.rewrite_api_method.request('view/' + $view, 'GET', $params);
        },

        /**
         * Add Action
         *
         * @param $trigger
         * @param $before
         * @param $success
         * @param $error
         * @see https://www.coderrr.com/wordpress-hooks-and-jquery-custom-events/
         */
        add_action: function ($trigger, $before = false, $success = false, $error = false) {
            // Sanitize Trigger
            $trigger = $trigger.replace("/", "_");

            // BeforeSend
            if ($before !== false) {
                $(document).on('add_action_' + $trigger + '_before', function (event, data) {
                    $before(data, event);
                });
            }

            // Success
            if ($success !== false) {
                $(document).on('add_action_' + $trigger, function (event, data) {
                    $success(data, event);
                });
            }

            // Error
            if ($error !== false) {
                $(document).on('add_action_' + $trigger + '_error', function (event, data) {
                    $error(data, event);
                });
            }
        }
    };
});