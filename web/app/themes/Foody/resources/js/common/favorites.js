/**
 * Created by moveosoftware on 7/22/18.
 */


$(document).ready(() => {


    $('body').on('click', '.favorite i', function () {
        if (foodyGlobals.loggedIn == false) {
            return showLoginModal();
        }
        let $this = $(this);

        let $parent = $this.parent();
        let postId = $parent.data('id');
        let $item = $parent.closest('.grid-item');

        let itemTitle = $item.data('title');

        if (postId) {

            let $icon = $this;

            let isAlreadyFavorite = $icon.hasClass('icon-favorite-pressed');

            toggleAllFavorites(postId, isAlreadyFavorite);

            analytics.event('add to favorites', {
                id: postId,
                title: itemTitle,
                favorite: !isAlreadyFavorite
            });


            $.ajax({
                type: 'POST',
                url: '/wp/wp-admin/admin-ajax.php', // admin-ajax.php URL
                data: {
                    action: 'toggle_favorite',
                    post_id: postId
                },
                error: function (request, status, error) {

                    // TODO handle errors

                    if (status == 500) {
                        console.log('Error while adding comment');
                    } else if (status == 'timeout') {
                        console.log('Error: Server doesn\'t respond.');
                    } else {
                        showLoginModal();
                    }
                    // revert animations and favorite indication
                    toggleAllFavorites(postId, !isAlreadyFavorite);
                },
                success: () => {
                },
                complete: function () {
                }
            })

        }
    });


    function toggleAllFavorites(postId, isAlreadyFavorite) {
        $('.favorite[data-id="' + postId + '"]').each(function () {

            let classToRemove, classToAdd, text;

            let $this = $(this);
            let $icon = $('i', $this);
            let $text = $('span', $this);

            if (isAlreadyFavorite) {
                classToRemove = 'icon-favorite-pressed';
                classToAdd = 'icon-heart';
                text = (foodyGlobals.isMobile ? 'שמרו' : 'שמרו');
            } else {
                classToRemove = 'icon-heart';
                classToAdd = 'icon-favorite-pressed';
                text = (foodyGlobals.isMobile ? 'נשמר' : 'נשמר');
            }

            $icon.removeClass(classToRemove);
            $icon.addClass(classToAdd);
            $text.text(text);

            let scale = 1.4;
            let size = parseInt($icon.css('font-size'));

            $icon.animate({
                fontSize: size * scale
            }, 350, () => {
                $icon.animate({
                    fontSize: size
                })
            });
        });
    }

    //todo: find a better place

    let $form = $('#login-form');
    if ($.validator) {
        $.validator.addMethod(
            "regex",
            function (value, element, regexpr) {
                return this.optional(element) || regexpr.test(value);
            }
        );
    }

    if ($.validator) {
        $.validator.addMethod(
            "password",
            function (value) {

                let hasNumbers = /[0-9]+/.test(value);
                let nonEn = /[^a-z0-9]/i.test(value);

                return hasNumbers && nonEn === false;
            }
        );
    }

    if ($.validator) {
        $.validator.addMethod(
            "emailOrUsername",
            function (value, element) {
                return this.email(element) || /^[^a-z0-9\s_.\-@]$/i.test(value);
            }
        );
    }

    if ($form && $form.validate) {
        $form.validate({
            rules: {
                log: {
                    required: true,
                    emailOrUsername: true
                },
                pwd: {
                    required: true
                }
            },
            messages: {
                log: 'כתובת המייל/שם המשתמש אינה תקינה',
                pwd: 'סיסמא אינה תקינה',
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    }

    $('.login .btn-google').on('click', () => {
        $('a[data-provider="Google"]')[0].click();
    });

    $('.login .btn-facebook').on('click', () => {
        $('a[data-provider="Facebook"]')[0].click();
    });
});