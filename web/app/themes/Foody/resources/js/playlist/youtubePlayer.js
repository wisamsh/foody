/**
 * Created by moveosoftware on 8/22/18.
 */

let YouTubePlayer = require('youtube-player');

let videoSizes = {
    desktop: {
        width: 1036,
        height: 524
    },
    mobile: {
        width: '100%',
        height: '200'
    }
};

let defaultOptions = {

    playerVars: {
        autoplay: 0,
        rel:0
    }
};

window.ytPlayer = function (selector, youtubeId) {

    let player;
    let options = foodyGlobals.isMobile ? videoSizes.mobile : videoSizes.desktop;

    options = _.extend(defaultOptions,options);
    player = YouTubePlayer($(selector)[0], options);

    // 'loadVideoById' is queued until the player is ready to receive API calls.
    player.loadVideoById(youtubeId);

    return player;
};


