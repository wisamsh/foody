/**
 * Created by moveosoftware on 8/22/18.
 */

let YouTubePlayer = import('youtube-player');

let videoSizes = {
    desktop: {
        width: '100%',
        height: 'auto'
    },
    mobile: {
        width: '100%',
        height: '200'
    }
};

let defaultOptions = {

    playerVars: {
        rel: 0
    }
};

let debug = false;

/*
 * @see https://developers.google.com/youtube/iframe_api_reference#Events
 * -1 (unstarted)
 * 0 (ended)
 * 1 (playing)
 * 2 (paused)
 * 3 (buffering)
 * 5 (video cued).
 * */

let playerMessages = [
    'unstarted',
    'ended',
    'playing',
    'paused',
    'buffering',
    '',
    'video cued',
];

module.exports = function (selector, youtubeId) {

    let player;
    let options = foodyGlobals.isMobile ? videoSizes.mobile : videoSizes.desktop;

    options = _.extend(defaultOptions, options);
    player = YouTubePlayer(jQuery(selector)[0], JSON.parse(JSON.stringify(options)));

    // 'cueVideoById' is queued until the player is ready to receive API calls.
    player.cueVideoById(youtubeId);

    if (debug) {
        player.on('stateChange', (event) => {

            let state = event.data + 1;
            console.log(playerMessages[state]);
        });
    }

    return player;
};


