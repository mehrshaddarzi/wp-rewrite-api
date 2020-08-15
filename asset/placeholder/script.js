jQuery(document).ready(function ($) {

    var placeholder_methods = {
        /**
         * PlaceHolder Content
         */
        ph_content: function () {
            return `
        <div class="ph-item">
    <div class="ph-col-12">
        <div class="ph-picture"></div>
        <div class="ph-row">
            <div class="ph-col-6 big"></div>
            <div class="ph-col-4 empty big"></div>
            <div class="ph-col-2 big"></div>
            <div class="ph-col-4"></div>
            <div class="ph-col-8 empty"></div>
            <div class="ph-col-6"></div>
            <div class="ph-col-6 empty"></div>
            <div class="ph-col-12"></div>
        </div>
    </div>
</div>
        `;
        },

        /**
         * PlaceHolder Line
         */
        ph_line: function () {
            return `
        <div class="ph-item">
    <div class="ph-col-12">
        <div class="ph-row">
            <div class="ph-col-6"></div>
            <div class="ph-col-4 empty big"></div>
            <div class="ph-col-2 big"></div>
            <div class="ph-col-4"></div>
            <div class="ph-col-8 empty"></div>
            <div class="ph-col-6"></div>
            <div class="ph-col-6 empty"></div>
            <div class="ph-col-12"></div>
        </div>
    </div>
</div>
        `;
        },

        /**
         * PlaceHolder Avatar
         */
        ph_avatar: function (radius = false) {
            return `
        <div class="ph-item">
        <div class="ph-col-2">
        <div class="ph-avatar"` + (radius != false ? ` style="border-radius:${radius};"` : ``) + `></div>
        </div>
    <div class="ph-col-9">
    <div class="ph-row">
            <div class="ph-col-6"></div>
            <div class="ph-col-6 empty big"></div>
            <div class="ph-col-4"></div>
            <div class="ph-col-8 empty"></div>
            <div class="ph-col-12"></div>
        </div>
    </div>

</div>
        `;
        },

        /**
         * PlaceHolder Remove
         */
        ph_remove_all: function ($ID) {
            $("#" + $ID + " .ph-item").remove();
        }
    };

    // Push To global Rewrite API Js
    if (typeof window.rewrite_api_method !== 'undefined') {
        $.extend(window.rewrite_api_method, placeholder_methods);
    }
});