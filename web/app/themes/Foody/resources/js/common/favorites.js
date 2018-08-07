/**
 * Created by moveosoftware on 7/22/18.
 */


$(document).ready(() => {

    let $favorite = $('.favorite');

    if ($favorite.length) {


        $favorite.each(function () {


            let $this = $(this);

            $this.click(() => {
                let postId = $this.data('id');

                if (postId) {

                    let $icon = $('i', $this);

                    let isAlreadyFavorite = $icon.hasClass('icon-favorite-pressed');

                    toggleAllFavorites(postId, isAlreadyFavorite);


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
                                alert('Error while adding comment');
                            } else if (status == 'timeout') {
                                alert('Error: Server doesn\'t respond.');
                            } else {
                                alert('error: ' + status);
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
        });
    }


    function toggleAllFavorites(postId, isAlreadyFavorite) {
        $('.favorite[data-id="' + postId + '"]').each(function () {

            let classToRemove, classToAdd, text;

            let $this = $(this);
            let $icon = $('i', $this);
            let $text = $('span', $this);

            if (isAlreadyFavorite) {
                classToRemove = 'icon-favorite-pressed';
                classToAdd = 'icon-heart';
                text = 'הוספה למועדפים';
            } else {
                classToRemove = 'icon-heart';
                classToAdd = 'icon-favorite-pressed';
                text = 'נשמר במועדפים';
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


});