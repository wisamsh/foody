/**
 * Created by moveosoftware on 7/10/18.
 */

$(document).ready(() => {

    let howIDidForm = '#image-upload-form';

    $commentForm = $(howIDidForm);


    $parent = $('.commentform-element');
    $comment = $('#comment');


    let successCallback = function (addedCommentHTML) {

        let $commentlist = $('.how-i-did-list');// comment list container

        $commentlist.prepend(addedCommentHTML);

        //     respond = $('#respond'),
        //     cancelreplylink = $('#cancel-comment-reply-link');
        // // if this post already has comments
        // if (commentlist.length > 0) {
        //
        //
        //     let $repondParent = $(respond.parent()).parent();
        //     // if in reply to another comment
        //     if ($repondParent.hasClass('comment-body')) {
        //
        //         // if the other replies exist
        //         if ($repondParent.next('.children').length) {
        //             $repondParent.next('.children').append(addedCommentHTML);
        //         } else {
        //             // if no replies, add <ol class="children">
        //             addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
        //             $repondParent.append(addedCommentHTML);
        //         }
        //         // close respond form
        //         cancelreplylink.trigger("click");
        //     } else {
        //         // simple comment
        //         commentlist.prepend(addedCommentHTML);
        //         alert('main comment');
        //     }
        // } else {
        //     // if no comments yet
        //     addedCommentHTML = '<ol class="comment-list">' + addedCommentHTML + '</ol>';
        //     respond.before($(addedCommentHTML));
        // }
        // // clear textarea field
        // $('#comment').val('');
        //
        // $parent.add($commentForm).removeClass('open');
    };


    // show modal on input change
    $('#attachment').on('change', function (e) {

        let $modal = $('#upload-image-modal');
        $modal.modal('show');

        readURL(this, $('img', $modal));
    });


    function readURL(input, img) {

        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $(img).attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    let inputsToBind = [
        'comment'
    ];

    let $boundForm = $('#image-upload-hidden');

    inputsToBind.forEach((inputName) => {
        let inputSelector = 'input[name="' + inputName + '"]';
        $(inputSelector, $commentForm).on('change', function () {
            $(inputSelector, $boundForm).val($(this).val());
        })
    });

    $('button[type="submit"]', $commentForm).click((e) => {
        e.preventDefault();
        $boundForm.trigger('submit');
    });

    formSubmitWithFiles({
        form: '#image-upload-hidden',
        success: successCallback,
        ajaxUrl: '/wp/wp-admin/admin-ajax.php',
        action: 'ajaxhow_i_did'
    });


});

