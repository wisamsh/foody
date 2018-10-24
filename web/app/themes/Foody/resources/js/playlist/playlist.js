/**
 * Created by moveosoftware on 8/29/18.
 */

let ytPlayer = require('../common/youtubePlayer');

jQuery(document).ready(function ($) {

    if ($('.single-foody_playlist').length) {
        $currentPlaying = $('.playlist-recipe-item.current');

        let videoId = $currentPlaying.data('video');

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
                    $next = $currentPlaying.next('li');

                    // not in the end of the playlist
                    if ($next.length) {
                        window.location.href = $('a', $next).attr('href');
                    }
                    break;
                // video playing
                case 1:
                    analytics.timeEvent('playlist video');
                    break;
                // video paused
                case 2:
                    analytics.event('playlist video', {
                        id: foodyGlobals.objectID,
                        title: foodyGlobals.title,
                        recipe_id: foodyGlobals.currentRecipe.ID,
                        recipe_title: foodyGlobals.currentRecipe.title,
                    });
                    break;
            }
        });
    }
});



