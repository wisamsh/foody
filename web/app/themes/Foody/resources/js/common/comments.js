/**
 * Created by moveosoftware on 7/2/18.
 */

$(document).ready(() => {

    $commentForm = $('#commentform');

    if ($commentForm.length) {

        $parent = $('.commentform-element');
        $comment = $('#comment', $parent);

        $comment.click(() => {
            if (foodyGlobals.loggedIn) {
                $parent.add($commentForm).toggleClass('open');
            } else {
                showLoginModal();
            }
        });


        attachCancelButton();


        function attachCancelButton() {

            $submitContainer = $('input[type="submit"]', $commentForm).parent();
            $cancelButton = $('.cancel', $submitContainer);

            if (!$cancelButton.length) {
                $cancelButton = $('<input class="cancel" type="button" value="ביטול"/>');

                $submitContainer.prepend($cancelButton);

                $cancelButton.on('click', () => {
                    $parent.add($commentForm).removeClass('open');
                });
            }
        }


        let successCallback = function (addedCommentHTML) {

            let commentlist = $('.comment-list'),// comment list container
                respond = $('#respond'),
                cancelreplylink = $('#cancel-comment-reply-link');
            // if this post already has comments
            if (commentlist.length > 0) {


                let $repondParent = $(respond.parent()).parent();
                // if in reply to another comment
                if ($repondParent.hasClass('comment-body')) {

                    // if the other replies exist
                    if ($repondParent.next('.children').length) {
                        $repondParent.next('.children').append(addedCommentHTML);
                    } else {
                        // if no replies, add <ol class="children">
                        addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
                        $repondParent.append(addedCommentHTML);
                    }
                    // close respond form
                    cancelreplylink.trigger("click");
                } else {
                    // simple comment
                    commentlist.prepend(addedCommentHTML);
                    alert('main comment');
                }
            } else {
                // if no comments yet
                addedCommentHTML = '<ol class="comment-list">' + addedCommentHTML + '</ol>';
                respond.before($(addedCommentHTML));
            }
            // clear textarea field
            $('#comment').val('');

            $parent.add($commentForm).removeClass('open');
        };

        let form = '#commentform';

        formSubmit({
            form: form,
            success: successCallback,
            ajaxUrl: '/wp/wp-admin/admin-ajax.php',
            action: '&action=ajaxcomments'
        });


        // load more button click event
        $('a[data-context="comments-list"]').click(function () {
            let button = $(this);


            // decrease the current comment page value
            cpage--;

            analytics.event('show more comments', {
                id: foodyGlobals.objectID,
                type: foodyGlobals.post.type.replace('foody_',''),
                title: foodyGlobals.title,
                page: cpage
            });


            let submitText = button.html();
            $.ajax({
                url: ajaxurl, // AJAX handler, declared before
                data: {
                    'action': 'cloadmore', // wp_ajax_cloadmore
                    'post_id': parent_post_id, // the current post
                    'cpage': cpage, // current comment page
                },
                type: 'POST',
                beforeSend: function (xhr) {
                    button.text('טוען...'); // preloader here
                },
                success: function (data) {
                    if (data) {
                        $('ol.comment-list').append(data);
                        button.html(submitText);
                        // if the last page, remove the button
                        if (cpage == 1)
                            button.remove();
                    } else {
                        button.remove();
                    }
                }
            });
            return false;
        });

    }

});

