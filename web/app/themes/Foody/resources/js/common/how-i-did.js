/**
 * Created by moveosoftware on 7/10/18.
 */

$(document).ready(() => {

    let howIDidForm = '#image-upload-form';

    $commentForm = $(howIDidForm);


    $parent = $('.commentform-element');
    $comment = $('#comment');
    $uploadModal = $('#upload-image-modal');
    $uploadDialog = $('#how-i-did-modal');
    let $boundForm = $('#image-upload-hidden');

    let successCallback = function (addedCommentHTML) {

        let $commentlist = $('.how-i-did-list');// comment list container


        $uploadModal.on('hidden.bs.modal', function (e) {
            $(this).unbind();
            $commentlist.prepend(addedCommentHTML);
        });

        $uploadModal.modal('hide');
        $commentForm[0].reset();
        $boundForm[0].reset();
    };


    let $attachment =  $('#attachment');

    // prevent upload if not logged in
    $attachment.on('click',(e) => {
        if (foodyGlobals.loggedIn == 'false') {
            e.preventDefault();
            showLoginModal();
            return false;
        }
    });


    // show modal on input change
    $attachment.on('change', function (e) {

        let $modal = $('#upload-image-modal');
        $modal.modal('show');

        readURL(this, $('img', $modal));
    });

    // convert file to image
    function readURL(input, img) {

        if (input.files && input.files[0]) {
            let reader = new FileReader();

            // reader.onload = function (e) {
            //     $(img).attr('src', e.target.result);
            // };

            reader.onloadend = function (e) {

                // Update an image tag with loaded image source
                $(img).attr('src', e.target.result);
                // Use EXIF library to handle the loaded image exif orientation
                EXIF.getData(input.files[0], function () {

                    // // Fetch image tag
                    // let img = $(img).get(0);
                    // Fetch canvas
                    let canvas = document.createElement('canvas');
                    // run orientation on img in canvas
                    orientation(img[0], canvas);
                });
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    // handle mobile image orientation
    function orientation(img, canvas) {

        // Set variables
        let ctx = canvas.getContext("2d");
        let exifOrientation = '';
        let width = img.width,
            height = img.height;

        console.log(width);
        console.log(height);

        // Check orientation in EXIF metadatas
        EXIF.getData(img, function () {
            let allMetaData = EXIF.getAllTags(this);
            exifOrientation = allMetaData.Orientation;
            console.log('Exif orientation: ' + exifOrientation);
        });

        // set proper canvas dimensions before transform & export
        if (jQuery.inArray(exifOrientation, [5, 6, 7, 8]) > -1) {
            //noinspection JSSuspiciousNameCombination
            canvas.width = height;
            //noinspection JSSuspiciousNameCombination
            canvas.height = width;
        } else {
            canvas.width = width;
            canvas.height = height;
        }

        // transform context before drawing image
        switch (exifOrientation) {
            case 2:
                ctx.transform(-1, 0, 0, 1, width, 0);
                break;
            case 3:
                ctx.transform(-1, 0, 0, -1, width, height);
                break;
            case 4:
                ctx.transform(1, 0, 0, -1, 0, height);
                break;
            case 5:
                ctx.transform(0, 1, 1, 0, 0, 0);
                break;
            case 6:
                ctx.transform(0, 1, -1, 0, height, 0);
                break;
            case 7:
                ctx.transform(0, -1, -1, 0, height, width);
                break;
            case 8:
                ctx.transform(0, -1, 1, 0, 0, width);
                break;
            default:
                ctx.transform(1, 0, 0, 1, 0, 0);
        }

        // Draw img into canvas
        ctx.drawImage(img, 0, 0, width, height);

        $(img).attr('src', canvas.toDataURL());
        img = null;
        canvas = null;
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
        action: 'ajaxhow_i_did'
    });

    $('.how-i-did-list').on('click','.how-i-did-modal-open', function () {
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
                    if (hidpage == 0)
                        button.remove();
                } else {
                    button.remove();
                }
            }
        });
        return false;
    });


});

