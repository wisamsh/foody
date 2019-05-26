/**
 * Created by moveosoftware on 6/27/18.
 */

// noinspection ES6ModulesDependencies
if (foodyGlobals.type && (foodyGlobals.type == 'campaign')) {

    let $attachment = $('#attachment');

    // prevent upload if not logged in
    $attachment.on('click', (e) => {
        if (foodyGlobals.loggedIn == true) {
            if (!foodyGlobals.seen_extended_approvals && foodyGlobals.extended_campaign_url) {
                e.preventDefault();
                window.location.href = foodyGlobals.extended_campaign_url;
                return false;
            }
        }
    });


    let $video = $('.hero-container #video');

    if ($video && $video.length) {

        jQuery.each($video, (index, videoElem) => {

            let videoId = jQuery(videoElem).data('video-id');
            let ytPlayer = require('../common/youtubePlayer');

            let playerContainer = jQuery(videoElem).siblings('.video-container');

            let player = ytPlayer(playerContainer, videoId);


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
        });

    }

}









