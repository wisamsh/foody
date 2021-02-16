/**
 * Created by moveosoftware on 7/2/18.
 */

jQuery(document).ready(($) => {

   let $commentForm = $('#commentform');


    if ($commentForm.length) {

        let $parent = $('.commentform-element');
        let $comment = $('#comment', $parent);


        $comment.on('focusin', function (e) {
            $parent.add($commentForm).addClass('open');
        });

        // $comment.on('focusout',function () {
        //     $parent.add($commentForm).removeClass('open');
        // });


        let successCallback = function (addedCommentHTML) {

            let commentlist = $('.comment-list'),// comment list container
                respond = $('#respond');

            let $comment = $(addedCommentHTML);
            let approved = $('.waiting-approval', $comment).length == 0;
            if (approved) {
                incrementCommentsCount('.recipe-comments .title');
            }
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
                    let cancelReplyLink = $('#cancel-comment-reply-link');
                    cancelReplyLink.trigger("click");
                } else {
                    // simple comment
                    commentlist.prepend(addedCommentHTML);
                }
            } else {
                // if no comments yet
                addedCommentHTML = '<ol class="comment-list">' + addedCommentHTML + '</ol>';
                respond.before($(addedCommentHTML));
            }

            $commentForm[0].reset();

            $parent.add($commentForm).removeClass('open');

            if ($('.comments-rating-prep-container .comments-link-container .num-of-comments').length) {
                let currentNumOfPreps = parseInt($('.comments-rating-prep-container .comments-link-container .num-of-comments')[0].innerText) + 1;
                $('.comments-rating-prep-container .comments-link-container .num-of-comments')[0].innerText = currentNumOfPreps;
            } else {
                if ($('.comments-rating-prep-container .comments-link-container').length && typeof $('.comments-rating-prep-container .comments-link-container').attr('data-numofcomments') !== 'undefined') {
                    let currentNumOfPreps = parseInt($('.comments-rating-prep-container .comments-link-container').attr('data-numofcomments')) + 1;
                    let newElem = '<a href="#comments" class="comments-link-container"><div class="comments-title">כבר הגיבו</div><div class="num-of-comments">' + currentNumOfPreps + '</div></a>';

                    $('.comments-rating-prep-container .comments-link-container').replaceWith(newElem);
                    if($('#comments > .title').length){
                        if(currentNumOfPreps == 1){
                            $('#comments > .title')[0].innerText = 'תגובה (1)';
                        }
                        else{
                            if(currentNumOfPreps != 0){
                                $('#comments > .title')[0].innerText = 'תגובות ('+ currentNumOfPreps +')';
                            }
                        }
                    }
                }
            }
        };

        let form = '#commentform';
        let button = $(form + ' input[type="submit"]');
        if (button.length == 0) {
            button = $(form + ' button[type="submit"]');
        }

        let validator;
        formSubmit({
            form: form,
            ajaxUrl: '/wp/wp-admin/admin-ajax.php',
            action: '&action=ajaxcomments',
            unbind: false,
            ajaxSettings: {
                beforeSend: function (xhr) {


                    // TODO change to loader
                    // what to do just after the form has been submitted
                    button.addClass('loadingform').val('שולח...');
                },
                error: function (request, status, error) {
                    let response = request.responseJSON;
                    let errors = {comment: response.data.message};
                    // Show errors on the form
                    validator.showErrors(errors);
                },
                success: successCallback,
                complete: function () {
                    // TODO handle loader
                    // what to do after a comment has been added
                    button.removeClass('loadingform').val('שלח');
                }
            }
        });

        validator = $commentForm.validate({
            rules: {
                comment: {
                    required: true
                }
            },
            messages: {
                comment: {
                    required: 'אנא הזנ/י תגובה'
                }
            }
        });


        attachCancelButton();


        function attachCancelButton() {

            let $submitContainer = $('input[type="submit"]', $commentForm).parent();
            let $cancelButton = $('.cancel', $submitContainer);

            if (!$cancelButton.length) {
                $cancelButton = $('<input class="cancel" type="button" value="ביטול"/>');

                $submitContainer.prepend($cancelButton);

                $cancelButton.on('click', () => {
                    $parent.add($commentForm).removeClass('open');
                    validator.resetForm();
                    $commentForm[0].reset();
                    let cancelReplyLink = $('#cancel-comment-reply-link');
                    cancelReplyLink.trigger("click");
                });
            }
        }

        // TODO remove duplication (how-i-did.js)
        function incrementCommentsCount(titleSelector) {
            let title = $(titleSelector).text();
            let matches = title.match(/\(([0-9]+)\)/);
            if (matches && matches.length > 0) {
                let count = parseInt(matches[1]);
                if (!isNaN(count)) {
                    count += 1;

                    if(count > 1 && title.indexOf('תגובה') > -1){
                        title = title.replace('תגובה', 'תגובות');
                    }

                    title = title.replace(/[0-9]+/, count);
                    $(titleSelector).text(title);
                }
            }
        }


        // load more button click event
        $('a[data-context="comments-list"]').click(function () {
            let button = $(this);


            // decrease the current comment page value
            cpage--;

            analytics.event('show more comments', {
                id: foodyGlobals.objectID,
                type: foodyGlobals.post.type.replace('foody_', ''),
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

