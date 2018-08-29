/**
 * Created by moveosoftware on 8/29/18.
 */

$currentPlaying = $('.playlist-recipe-item.current');

let videoId = $currentPlaying.data('video');

let player = ytPlayer('.video-container', videoId);


player.on('stateChange', (event) => {
    // video ended. see https://developers.google.com/youtube/iframe_api_reference#Events
    // for YouTubePlayer events
    if (event.data === 0) {

        $next = $currentPlaying.next('li');

        // not in the end of the playlist
        if ($next.length) {
            window.location.href = $('a', $next).attr('href');
        }

    }
});