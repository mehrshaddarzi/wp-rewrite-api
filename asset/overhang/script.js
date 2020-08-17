jQuery(document).ready(function ($) {

    var overhang_js_methods = {
        loading_overhang: function (time) {
            if (typeof (time) === 'undefined') time = 20000;
            $("body").overhang({
                type: "info",
                html: true,
                message: rewrite_api.wait,
                duration: time
            });
        },
        show_overhang: function (type, text, time = '5000') {
            $("body").overhang({
                type: type, //[success, error, info, warn]
                html: true,
                message: text,
                duration: time,
                upper: true
            });
            setTimeout(function () {
                $(".overhang").remove();
            }, time);
        }
    };

    // Push To global Rewrite API Js
    if (typeof window.rewrite_api_method !== 'undefined') {
        $.extend(window.rewrite_api_method, overhang_js_methods);
    }

    // Show Overhang in View Page
    if (window.rewrite_api_method.isObject(rewrite_api.overhang_alert)) {
        window.rewrite_api_method.show_overhang(rewrite_api.overhang_alert.type, rewrite_api.overhang_alert.text);
    }
});