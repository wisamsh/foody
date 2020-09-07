/**
 * Created by moveosoftware on 7/10/18.
 */

let FoodyLoader = require('../common/foody-loader');
let readUrl = require('./image-reader');
require('cropperjs');
require('cropperjs/dist/cropper.min.css');
require('jquery-cropper');
$(document).ready(() => {


    let howIDidForm = 'form#image-upload-form';
    let $commentForm = $(howIDidForm);
    let $uploadModal = $('#upload-image-modal');
    let $boundForm = $('form#image-upload-hidden');

    let $formContainer = $('#upload-image-modal .modal-content');
    let submitButton = $('button[type="submit"]', $commentForm);

    let loader = new FoodyLoader({container: $formContainer});

    let addImageBtn = $('.comments-area-header .btn-container');

    addImageBtn.on('click', function () {
        if (canMarkedPrepared(foodyGlobals.ID) && $('.comments-rating-prep-container .preparations-share .num-of-preps').length == 0) {
            foodyAjax({
                action: 'foody_increment_made_recipe',
                data: {
                    ID: foodyGlobals['ID']
                }
            }, function (err, data) {
                if (err) {
                    console.log(err)
                } else {
                    setAsPrepared(foodyGlobals.ID);
                    if ($('.comments-rating-prep-container .preparations-share').length &&
                        typeof $('.comments-rating-prep-container .preparations-share').attr('data-numofpreps') !== 'undefined'
                    ) {
                        let currentNumOfPreps = parseInt($('.comments-rating-prep-container .preparations-share').attr('data-numofpreps')) + 1;
                        let newElem = '<div class="preparations-share"><div class="preparations-share-title">כבר הכינו</div><div class="num-of-preps">' + currentNumOfPreps + '</div></div>';

                        $('.comments-rating-prep-container .preparations-share').replaceWith(newElem);
                    }
                }
            });
        }
    });

    let successCallback = function (addedCommentHTML) {

        let $commentlist = $('.how-i-did-list');// comment list container

        $uploadModal.on('hidden.bs.modal', function (e) {
            $(this).unbind();
            $commentlist.prepend(addedCommentHTML);
        });

        $commentForm[0].reset();
        $boundForm[0].reset();
        $uploadModal.modal('hide');


        incrementCommentsCount('#how-i-did .comments-title');

        if ($('.comments-rating-prep-container .preparations-share .num-of-preps').length) {
            let currentNumOfPreps = parseInt($('.comments-rating-prep-container .preparations-share .num-of-preps')[0].innerText) + 1;
            $('.comments-rating-prep-container .preparations-share .num-of-preps').innerText = currentNumOfPreps;
        } else {
            if ($('.comments-rating-prep-container .preparations-share').length && typeof $('.comments-rating-prep-container .preparations-share').attr('data-numofpreps') !== 'undefined') {
                let currentNumOfPreps = parseInt($('.comments-rating-prep-container .preparations-share').attr('data-numofpreps')) + 1;
                let newElem = '<div class="preparations-share"><div class="preparations-share-title">כבר הכינו</div><div class="num-of-preps">' + currentNumOfPreps + '</div></div>';

                $('.comments-rating-prep-container .preparations-share').replaceWith(newElem);
            }
        }
    };

    // TODO remove duplication
    function incrementCommentsCount(titleSelector) {
        let title = $(titleSelector).text();
        let matches = title.match(/\(([0-9]+)\)/);
        if (matches && matches.length > 0) {
            let count = parseInt(matches[1]);
            if (!isNaN(count)) {
                count += 1;

                title = title.replace(/[0-9]+/, count);
                $(titleSelector).text(title);
            }
        }
    }


    let $attachment = $('#attachment');

    // prevent upload if not logged in
    $attachment.on('click', (e) => {
        if (foodyGlobals.loggedIn == false) {
            e.preventDefault();
            showLoginModal();
            return false;
        }
    });


    // show modal on input change
    $attachment.on('change', function (e) {

        let $modal = $('#upload-image-modal');
        $modal.modal('show');

        let $modalImage = $('img', $modal);

        readUrl(this, $modalImage);

        fixExifOrientation($modalImage);
    });


    function fixExifOrientation($img) {
        $img.on('load', function () {
            EXIF.getData($img[0], function () {
                console.log('Exif=', EXIF.getTag(this, "Orientation"));
                switch (parseInt(EXIF.getTag(this, "Orientation"))) {
                    case 2:
                        $img.addClass('flip');
                        break;
                    case 3:
                        $img.addClass('rotate-180');
                        break;
                    case 4:
                        $img.addClass('flip-and-rotate-180');
                        break;
                    case 5:
                        $img.addClass('flip-and-rotate-270');
                        break;
                    case 6:
                        $img.addClass('rotate-90');
                        break;
                    case 7:
                        $img.addClass('flip-and-rotate-90');
                        break;
                    case 8:
                        $img.addClass('rotate-270');
                        break;
                }
            });
        });
    }


    let inputsToBind = [
        'comment'
    ];


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
        action: 'ajaxhow_i_did',
        onSubmit: function () {
            $formContainer.block({message: ''});
            loader.attach.call(loader);
            submitButton.prop('disabled', true);
        },
        complete: function () {
            loader.detach.call(loader);
            submitButton.prop('disabled', false);
            $formContainer.unblock();
        }
    });

    $('.how-i-did-list').on('click', '.how-i-did-modal-open', function () {
        let image = $(this).data('image');
        let user = $(this).data('user');
        let content = $(this).data('content');

        let $modal = $('#how-i-did-modal');

        $('#image', $modal).attr('src', image);

        $('#user', $modal).text(user);
        $('#content', $modal).text(content);
    });


    // load more button click event
    $('a[data-context="how-i-did-list"]').click(function () {
        let button = $(this);
        let $context = $('.' + button.data('context'));


        console.log(hidpage);
        // decrease the current comment page value
        hidpage--;
        let submitText = button.html();
        $.ajax({
            url: ajaxurl,
            data: {
                'action': 'hidloadmore',
                'post_id': parent_post_id, // the current post
                'hidpage': hidpage, // current comment page
            },
            type: 'POST',
            beforeSend: function (xhr) {
                // TODO change to loader
                button.text('טוען...'); // preloader here
            },
            success: function (data) {
                if (data) {
                    $context.append(data);
                    button.html(submitText);
                    // if the last page, remove the button
                    if (hidpage == 1)
                        button.remove();
                } else {
                    button.remove();
                }
            }
        });
        return false;
    });


});

function canMarkedPrepared(recipeId) {
    let hasPrepared = JSON.parse(sessionStorage.getItem('has_prepared'));

    if (hasPrepared == null) {
        return true;
    }

    return jQuery.inArray(recipeId, hasPrepared) == -1;
}

function setAsPrepared(recipeId) {
    let hasPrepared = JSON.parse(sessionStorage.getItem('has_prepared'));

    if (!hasPrepared) {
        hasPrepared = [];
    }

    if (!hasPrepared.includes(recipeId)) {
        hasPrepared.push(recipeId);
    }

    sessionStorage.setItem('has_prepared', JSON.stringify(hasPrepared));
}
