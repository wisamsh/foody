/**
 * Created by moveosoftware on 8/12/18.
 */


window.loadMore = function (context,page) {

    $('a[data-context="' + context + '"]').click(function () {
        let button = $(this);
        let $context = $('.' + button.data('context'));
        // decrease the current comment page value
        page--;
        let submitText = button.text();
        $.ajax({
            url: ajaxurl, // AJAX handler, declared before
            data: {
                'action': 'hidloadmore', // wp_ajax_cloadmore
                'post_id': parent_post_id, // the current post
                'chpage': chpage, // current comment page
            },
            type: 'POST',
            beforeSend: function (xhr) {
                button.text('טוען...'); // preloader here
            },
            success: function (data) {
                if (data) {
                    $context.append(data);
                    button.text(submitText);
                    // if the last page, remove the button
                    if (page == 0)
                        button.remove();
                } else {
                    button.remove();
                }
            }
        });
        return false;
    });
};