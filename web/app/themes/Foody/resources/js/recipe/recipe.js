/**
 * Created by moveosoftware on 6/27/18.
 */

// noinspection ES6ModulesDependencies
//TODO:: Change category from מתכון to כתבה if necessary !
if (foodyGlobals.post && (foodyGlobals.post.type == 'foody_recipe' || foodyGlobals.post.type == 'post')) {

    window.scroller();

    // let $video = $('.featured-content-container #video');
    let $video = !foodyGlobals.isMobile ? $('.featured-content-container #video') : $('.featured-content-container .slider-for #video');

    let videoStopped = false;

    if ($video && $video.length) {

        if ($('.single-foody_recipe').length || $('.single-post').length) {

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
                let firstPlay = true;
                let timeUpdater;
                let videoTime = 0;
                let sentPercentage = {};
                player.on('stateChange', (event) => {

                    switch (event.data) {
                        // video ended
                        case 0:
                            clearInterval(timeUpdater);
                            sentPercentage = {};
                            break;

                        // video playing
                        case 1:
                            timeUpdater = setInterval(updateTime, 1000);
                            if (firstPlay) {
                                firstPlay = false;
                                eventCallback(event, 'מתכון', 'צפייה בווידאו', 'הפעלה', 'מיקום', '0%');
                            } else {
                                let durationPromise = player.getDuration();
                                let currPromise = player.getCurrentTime();
                                Promise.all([durationPromise, currPromise]).then(function (values) {
                                    let passPercentage = Math.round((values[1] / values[0]) * 100);
                                    let reminder = passPercentage % 10;
                                    if (reminder <= 5) {
                                        passPercentage = passPercentage - reminder;
                                    } else {
                                        let addToRoundUp = 10 - reminder;
                                        passPercentage = passPercentage + addToRoundUp;
                                    }
                                    if (videoStopped) {
                                        eventCallback(event, 'מתכון', 'צפייה בווידאו', 'הפעלה מחדש לאחר הפסקה', 'מיקום', passPercentage + '%');
                                        videoStopped = false;
                                    }
                                    eventCallback(event, 'מתכון', 'צפייה בווידאו', 'התקדמות', 'מיקום', passPercentage + '%');
                                });
                            }
                            analytics.timeEvent('recipe video');
                            break;
                        // video paused
                        case 2:
                            clearInterval(timeUpdater);
                            let pausedDurationPromise = player.getDuration();
                            let pausedCurrPromise = player.getCurrentTime();
                            Promise.all([pausedDurationPromise, pausedCurrPromise]).then(function (values) {
                                let passPercentage = Math.round((values[1] / values[0]) * 100);
                                if (passPercentage % 10 != 0) {
                                    let reminder = passPercentage % 10;
                                    if (reminder <= 5) {
                                        passPercentage = passPercentage - reminder;
                                    } else {
                                        let addToRoundUp = 10 - reminder;
                                        passPercentage = passPercentage + addToRoundUp;
                                    }
                                }
                                eventCallback(event, 'מתכון', 'עצירת ווידאו', 'עצירה', 'מיקום', passPercentage + '%');
                                videoStopped = true;
                            });


                            analytics.event('recipe video', {
                                id: foodyGlobals.objectID,
                                title: foodyGlobals.title
                            });
                            break;
                    }
                });

                function updateTime() {
                    let oldTime = videoTime;
                    if (player && player.getCurrentTime) {
                        videoTime = player.getCurrentTime();
                    }
                    if (videoTime !== oldTime) {
                        let durationPromise = player.getDuration();
                        onProgress([durationPromise, videoTime]);
                    }
                }

                function onProgress(event) {
                    Promise.all(event).then(function (values) {
                        let passPercentage = Math.round((values[1] / values[0]) * 100);
                        //Send event only for 0, 10, 20, 30, etc...
                        if (passPercentage % 10 == 0 && !sentPercentage[passPercentage]) {
                            sentPercentage[passPercentage] = true;
                            eventCallback('', 'מתכון', 'צפייה בווידאו', 'התקדמות', 'מיקום', passPercentage + '%');
                        }
                    });
                }
            });
        }


    }


    $('.must-log-in a, .comment-reply-login').on('click', function (e) {
        e.preventDefault();
        showLoginModal();
    });

    if ($('.content > .details-container .post-ratings > img').length) {
        $.each($('.content > .details-container .post-ratings > img'), function (indexArr) {
            $(this).before('<span class="ratings-index">' + (indexArr + 1) + '</span>');
        });
    }

}

jQuery(document).ready(($) => {
    if ($('.rating-digits').length) {
        if ($('.post-ratings')[0].innerText != "") {
            $('.rating-digits').remove();
        }

        $('.post-ratings img').on('keypress', function () {
            if ($('.rating-digits').length) {
                $('.rating-digits').remove();
            }
        });

        $('.post-ratings img').on('click', function () {
            if ($('.rating-digits').length) {
                $('.rating-digits').remove();
            }
        });
    }

    if ($('.slider-nav').length) {
        let slideToShow = $('.slider-for .item').length > 4 ? 4 : $('.slider-for .item').length;
        let sliderMainData = {
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<div class="arrow arrow-prev"></div>',
            nextArrow: '<div class="arrow arrow-next"></div>',
            fade: true,
            asNavFor: '.slider-nav',
            rtl: true
        };

        let sliderNavData = {
            slidesToShow: slideToShow,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            dots: false,
            focusOnSelect: true,
            rtl: true
        };
        if ($('.slider-for .item').length > 4) {
            sliderNavData.nextArrow = '<div class="arrow arrow-next"></div>';
            sliderNavData.prevArrow = '<div class="arrow arrow-prev"></div>';
        } else {
            sliderNavData.arrows = false;
        }

        $('.slider-for').slick(sliderMainData);
        $('.slider-nav').slick(sliderNavData);
    }

    $(".show-read-more").each(function(){
        let myStr = $.trim($(this).text());
        let maxLength = 85;
        if(myStr.length > maxLength){
            let newStr = myStr.substring(0, maxLength);
            let indexToStartSubstring =  Math.min(newStr.length, newStr.lastIndexOf(" "));
            newStr = newStr.substr(0,indexToStartSubstring);
            let removedStr = myStr.substring(indexToStartSubstring, myStr.length);
            $(this).empty().html(newStr);
            $(this).append(' <a href="javascript:void(0);" class="read-more">עוד...</a>');
            $(this).append('<span class="more-text">' + removedStr + '</span>');
        }
    });
    $(".read-more").click(function(){
        $(this).siblings(".more-text").contents().unwrap();
        $(this).remove();
    });
});

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






