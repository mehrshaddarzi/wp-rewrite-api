jQuery(document).ready(function($){

    /**
     * Get Post
     */
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

});