/**
 * Created by moveosoftware on 6/27/18.
 */

// noinspection ES6ModulesDependencies
if (foodyGlobals.post && (foodyGlobals.post.type == 'foody_recipe' || foodyGlobals.post.type == 'post')) {

    window.scroller();

    let $video = $('.featured-content-container #video');

    if ($video && $video.length) {

        if ($('.single-foody_recipe').length || $('.single-post').length) {

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
            let firstPlay = true;
            player.on('stateChange', (event) => {

                switch (event.data) {
                    // video ended
                    case 0:
                        break;

                    // video playing
                    case 1:
                        debugger;
                        if (firstPlay) {
                            firstPlay = false;
                            eventCallback(event, 'מתכון', 'צפייה בווידאו', 'הפעלה', 'מיקום', '0%');
                        } else {
                            let durationPromise = player.getDuration();
                            let currPromise = player.getCurrentTime();
                            Promise.all([durationPromise, currPromise]).then(function (values) {
                                debugger;
                                let passPercentage = Math.round((values[1] / values[0]) * 100);
                                eventCallback(event, 'מתכון', 'צפייה בווידאו', 'התקדמות', 'מיקום', passPercentage + '%');
                            });
                        }
                        analytics.timeEvent('recipe video');
                        break;
                    // video paused
                    case 2:
                        debugger;
                        let pausedDurationPromise = player.getDuration();
                        let pausedCurrPromise = player.getCurrentTime();
                        Promise.all([pausedDurationPromise, pausedCurrPromise]).then(function (values) {
                            debugger;
                            let passPercentage = Math.round((values[1] / values[0]) * 100);
                            eventCallback(event, 'מתכון', 'עצירת ווידאו', 'עצירה', 'מיקום', passPercentage + '%');
                        });


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


/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 * @param action
 * @param label
 * @param cdDesc
 * @param cdValue
 */
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '') {

    /**
     * Recipe name
     */
    let recipe_name = foodyGlobals['title'];

    /**
     * Item category
     */
    let item_category = '';

    /**
     * Chef Name
     */
    let chef = foodyGlobals['author_name'];

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    /**
     * Difficulty Level
     */
    let difficulty_level = '';
    if (jQuery('.recipe-overview .difficulty_level').length) {
        difficulty_level = jQuery('.recipe-overview .difficulty_level').text().trim();
    }

    /**
     * Preparation Time
     */
    let preparation_time = 0;
    if (jQuery('.recipe-overview .preparation_time').length) {
        preparation_time = jQuery('.recipe-overview .preparation_time').text().trim();
    }

    /**
     * Ingredients Count
     */
    let ingredients_amount = 0;
    if (jQuery('.recipe-overview .ingredients_count').length) {
        ingredients_amount = jQuery('.recipe-overview .ingredients_count').text().trim();
    }

    /**
     * TODO: I Don't know!
     */
    let order_location = 0;//TODO: Don't know

    /**
     * Recipe view count
     */
    let amount = foodyGlobals['view_count'];

    /**
     * Has rich content - does contains video or product buy option
     */
    let hasRichContent = foodyGlobals['has_video'] ? foodyGlobals['has_video'] : false;

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        recipe_name,
        item_category,
        chef,
        difficulty_level,
        preparation_time,
        ingredients_amount,
        order_location,
        amount,
        hasRichContent,
        cdDesc,
        cdValue,
        ''
    );
}






