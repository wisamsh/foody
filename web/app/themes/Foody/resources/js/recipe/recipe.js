/**
 * Created by moveosoftware on 6/27/18.
 */

// noinspection ES6ModulesDependencies
if (foodyGlobals.post && foodyGlobals.post.type == 'foody_recipe') {

    window.scroller();

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









