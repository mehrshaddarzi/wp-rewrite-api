jQuery(document).ready(function ($) {

    /**
     * Svg loading ICON
     *
     * @param width
     * @param height
     * @param style
     * @returns {string}
     */
    function svg_loading_icon(width = '40', height = '40', style = 'text-align: center;margin: 15px auto 4px auto;') {
        return `<div id="svg-loader" style="` + style + `">
      <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
         width="` + width + `px" height="` + height + `px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
      <path fill="` + rewrite_api.svg_icon_color + `" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
        <animateTransform attributeType="xml"
          attributeName="transform"
          type="rotate"
          from="0 25 25"
          to="360 25 25"
          dur="0.6s"
          repeatCount="indefinite"/>
        </path>
      </svg>
    </div>`;
    }

    /**
     * Show Loading dialog
     *
     * @type {jQuery}
     * @use window.loading_dialog.open() | window.loading_dialog.close()
     */
    window.loading_dialog = $.dialog({
        title: '',
        lazyOpen: true,
        rtl: true,
        content: '<div class="text-center">' + svg_loading_icon() + rewrite_api.wait + '</div>',
        buttons: {},
        closeIcon: false,
    });

});