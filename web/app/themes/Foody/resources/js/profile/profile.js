/**
 * Created by moveosoftware on 9/29/18.
 */

let toggleFollowed = require('../common/follow');
let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');
require('cropperjs');
require('cropperjs/dist/cropper.min.css');
require('jquery-cropper');
let FoodyLoader = require('../common/foody-loader');
let readUrl = require('../common/image-reader');
jQuery(document).ready(($) => {


    // Followed topics list
    $('.managed-list li .close').click(function () {

        let $parent = $(this).parent('li');
        let id = $parent.data('id');
        let type = $parent.data('type');


        let eventName = null;

        if (type == 'followed_channels') {
            eventName = 'remove channel';
        } else if (type == 'followed_authors') {
            eventName = 'remove creator';
        }

        if (eventName) {
            analytics.event(eventName, {
                id: id,
                title: $('a', $parent).text()
            });
        }


        toggleFollowed(id, type, function (error) {

            if (error) {
                // TODO handle
            } else {


                $parent.fadeOut({
                    duration: 300,
                    complete: function () {
                        $parent.detach();
                    }
                });
            }
        })
    });


    // Foody search and filter
    let profile_filter = new FoodySearchFilter({
        selector: '.page-template-profile #accordion-foody-filter',
        grid: '#my-channels-grid',
        cols: 1,
        page: '.page-template-profile',
        context: 'profile',
        contextArgs: ['channels']
    });

    new FoodySearchFilter({
        selector: '.page-template-profile #accordion-foody-filter',
        grid: '#my-recipes-grid',
        cols: 1,
        page: '.page-template-profile',
        context: 'profile',
        contextArgs: ['favorites']
    });

    new FoodyContentPaging({
        context: 'profile',
        filter: profile_filter,
        contextArgs: ['channels']
    });

    // Tab switch analytics
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

        let tab = $(e.target).attr('href');

        tab = tab.replace('#', '');

        let eventName = null;
        if (tab == 'my-channels-recipes') {
            eventName = 'my channel recipes';
        } else if (tab == 'my-recipes') {
            eventName = 'my recipes button';
        }

        if (eventName != null) {

            analytics.event(eventName);
        }

    });

    // Nested tabs fix
    let tabs = [
        '#edit-user-details',
        '#change-password',
        '#user-content',
    ];

    let tabsSelector = tabs.map((selector) => {
        return `a[href="${selector}"]`;
    }).join(',');

    $(tabsSelector).on('shown.bs.tab', function (e) {

        let $this = $(this);

        let target = $this.attr('href');

        let others = tabs.filter((tab) => {
            return tab != target;
        });

        others.forEach((selector) => {
            $(selector).removeClass('active');
            $(selector).removeClass('show');

            let link = 'a[href="' + selector + '"]';

            $(link).removeClass('active');
            $(link).removeClass('show');

        });

        if (foodyGlobals.isMobile) {
            let $mobileChannels = $('.profile-top .my-channels');
            if (target == '#user-content') {
                $mobileChannels.removeClass('d-none').addClass('d-block');
            } else {
                $mobileChannels.addClass('d-none').removeClass('d-block');
            }
        }
    });


    // Change password form validation
    if ($("#password-reset").length) {
        $("#password-reset").validate({
            rules: {
                current_password: {
                    required: true,
                    password: true,
                    minlength: 8,
                },
                password: {
                    required: true,
                    minlength: 8,
                    password: true,
                },
                password_confirmation: {
                    required: true,
                    equalTo: '#password[name="password"]',
                }
            },
            messages: {
                current_password: {
                    required: 'סיסמא נוכחית הינו שדה חובה',
                    password: 'סיסמא אינה תקינה',
                    minlength: 'יש להזין לפחות 8 תווים'
                },
                password: {
                    required: 'סיסמא חדשה הינו שדה חובה',
                    password: 'סיסמא אינה תקינה',
                    minlength: 'יש להזין לפחות 8 תווים'
                },
                password_confirmation: {
                    required: 'ווידוא סיסמא הינו שדה חובה',
                    equalTo: 'סיסמאות אינן תואמות'
                }
            }, submitHandler: function (form) {
                form.submit();
            }
        });
    }


    formSubmit({
        form: 'form#edit-user-details',
        ajaxUrl: '/wp/wp-admin/admin-ajax.php',
        action: '&action=foody_edit_user',
        ajaxSettings: {
            beforeSend: function (xhr) {

            },
            error: function (request, status, error) {
                console.log(error, request.responseJSON);
            },
            success: function (data) {
                console.log('success', data);
            },
            complete: function () {

            }
        }
    });

    if ($("form#edit-user-details").length) {
        $("form#edit-user-details").validate({
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                phone_number: {
                    regex: /^((\+972|972)|0)( |-)?([1-468-9]( |-)?\d{7}|(5|7)[0-9]( |-)?\d{7})/
                }
            },
            messages: {
                first_name: {
                    required: 'שם פרטי הינו שדה חובה'
                },
                last_name: {
                    required: 'שם משפחה הינו שדה חובה'
                },
                phone_number: {
                    regex: 'מספר טלפון אינו תקין'
                }
            },
            submitHandler: (form) => {
                console.log('submitHandler');
                form.submit();
            }
        });
    }


    let $uploadModal = $('#profile-pic-upload-modal');

    $('#upload-photo-input').on('change', function () {

        readUrl(this, $('img', $uploadModal), function () {
            let options = {
                aspectRatio: 1,
                checkCrossOrigin: false,
                minContainerHeight: 300,
                minContainerWidth: 414,
                minCanvasWidth: 100
            };
            $uploadModal.modal('show');

            $('#cropped-image').on('load', function () {
                $(this).cropper(options);
            });

        });
    });

    $uploadModal.on('hide.bs.modal', function () {

    });

    $('.btn-approve', $uploadModal).on('click', function () {

        let cropper = $('#cropped-image').data('cropper');
        let data = {
            image: cropper.getCroppedCanvas().toDataURL()
        };

        console.log(data);

        upload(data);
    });

    function upload(data) {

        let image = data.image;
        // Split the base64 string in data and contentType
        let block = image.split(";");
        // Get the content type
        let contentType = block[0].split(":")[1];// In this case "image/gif"

        let loader = new FoodyLoader({container:$('.modal-body',$uploadModal)});
        loader.attach();

        $uploadModal.block({message:''});
        srcToFile(
            image,
            'photo.' + contentType.split('/')[1],
            contentType
        ).then(function (file) {

            // Create a FormData and append the file
            let fd = new FormData();
            fd.append("photo", file);
            $.ajax({
                url: "/wp/wp-admin/admin-ajax.php?action=foody_edit_profile_picture",
                data: fd,
                type: "POST",
                contentType: false,
                processData: false,
                cache: false,
                dataType: "json",
                error: function (err) {
                    console.error(err);
                },
                success: function (data) {
                    console.log(data);

                    let url = data.data.url;
                    $('.profile-picture-container img').attr('src', url);
                    $('.profile-top .user-details .image-container img.avatar').attr('src', url);
                    $uploadModal.modal('hide');
                },
                complete: function () {
                    loader.detach();
                    $uploadModal.unblock();
                }
            });
        });


    }

    function srcToFile(src, fileName, mimeType) {
        return (fetch(src)
                .then(function (res) {
                    return res.arrayBuffer();
                })
                .then(function (buf) {
                    return new File([buf], fileName, {type: mimeType});
                })
        );
    }

});