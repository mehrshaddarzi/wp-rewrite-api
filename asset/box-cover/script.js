jQuery(document).ready(function ($) {

    var box_cover_methods = {
        remove_box_cover: function (tag) {
            tag.parent().parent().remove();
        },
       init_box_cover: function (ID, content = '', show = false) {
            // First Remove
            $("#" + ID).remove();

            // Html Box Cover
            let html = `<div class="box-cover scroll-y" id="${ID}">
            <div class="container p-3">
                <div class="close-box-cover text-left mb-3 mt-2" data-function="remove_box_cover"><i class="fa fa-close"></i></div>
                <div class="clearfix"></div>
                <div class="wp-text-content p-2 rtl">` + content + `</div>
            </div>
        </div>
        </div>`;

            // Add To Body
            $(html).appendTo('body');

            // Check Show
            if (show === true) {
                $("#" + ID).hide().fadeIn('fast');
            }
        }
    };

    // Push To global Rewrite API Js
    if (typeof window.rewrite_api_method !== 'undefined') {
        $.extend(window.rewrite_api_method, box_cover_methods);
    }
});
