/**
 * Created by moveosoftware on 6/27/18.
 */

let foodyAlert = require('../common/alerts');

if (foodyGlobals.post && foodyGlobals.post.type == 'foody_recipe') {


    let $ratingContainer = $('.recipe-rating');

    window.scroller();

    updateRating();

    $('.foody-rating').on('rating:change', function (event, value, caption) {
        if (foodyGlobals.loggedIn == 'false') {
            showLoginModal();
            $(this).rating('update', 0);
        } else {
            rating(value);
        }

    });


    function rating(value) {

        let settings = {
            action: 'foody_rating',
            data: {
                post_id: foodyGlobals.post.ID,
                post_type: foodyGlobals.post.type,
                value: value
            }
        };

        analytics.event('recipe rating', {
            id: foodyGlobals.objectID,
            title: foodyGlobals.title,
            rating: value
        });

        foodyAjax(settings, (err) => {

            let message = 'הדירוג התקבל בהצלחה. תודה!';
            let wrapperClasses = 'foody-message';

            if (err) {
                message = 'אירעה שגיאה. אנא נסה/י שנית.';
                wrapperClasses = `${wrapperClasses} error`;
            } else {
                updateRating(true);
            }

            let $messageWrapper = $(`<div class="${wrapperClasses}">${message}</div>`).hide().fadeIn(300);

            let wrapperClassesSelector = '.' + wrapperClasses.split(' ').join('.');
            if ($(wrapperClassesSelector, $ratingContainer).length == 0) {

                setTimeout(() => {
                    $messageWrapper.fadeOut(300, function () {
                        $(this).remove();
                    })
                }, 3000);
                $ratingContainer.append($messageWrapper);

            }
        });

    }


    function updateRating() {

        let settings = {
            action: 'foody_get_rating',
            data: {
                post_id: foodyGlobals.post.ID
            }
        };

        foodyAjax(settings, (err, response) => {

            if (err) {
                console.log(err);
            } else {
                console.log('frs', response);

                $('.recipe-details .rating-input').rating('update', response.data.rating);

            }
        });

    }


    let $video = $('.featured-content-container #video');

    if ($video && $video.length) {

        if ($('.single-foody_recipe').length) {

            let videoId = $video.data('video-id');
            let ytPlayer = require('../common/youtubePlayer');

            let player = ytPlayer('.video-container', videoId);


            /*
             * @see https://developers.google.com/youtube/iframe_api_reference#Events
             * -1 (unstarted)
             * 0 (ended)
             * 1 (playing)
             * 2 (paused)
             * 3 (buffering)
             * 5 (video cued).
             * */
            player.on('stateChange', (event) => {


                switch (event.data) {
                    // video ended
                    case 0:
                        console.log('video ended');
                        break;

                    // video playing
                    case 1:
                        analytics.timeEvent('recipe video');
                        break;
                    // video paused
                    case 2:
                        analytics.event('recipe video', {
                            id: foodyGlobals.objectID,
                            title: foodyGlobals.title
                        });
                        break;
                }
            });
        }


    }


    $('.must-log-in a, .comment-reply-login').on('click', function (e) {
        e.preventDefault();
        showLoginModal();
    });

}









