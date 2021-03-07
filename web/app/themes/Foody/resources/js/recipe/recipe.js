/**
 * Created by moveosoftware on 6/27/18.
 */

// noinspection ES6ModulesDependencies
//TODO:: Change category from מתכון to כתבה if necessary !
// import * as player from "youtube-player";
let player;
let videoThumbnail;
if (foodyGlobals.post && (foodyGlobals.post.type == 'foody_recipe' || foodyGlobals.post.type == 'post')) {

    // sliders data
    var sliderMainData  = false, sliderNavData = false;

    window.scroller();

    // let $video = $('.featured-content-container #video');
    let $video = !foodyGlobals.isMobile ? $('.featured-content-container #video') : $('.featured-content-container .slider-for #video');
    let videoStopped = false;

    if ($video && $video.length) {

        if ($('.single-foody_recipe').length || $('.single-post').length) {

            jQuery.each($video, (index, videoElem) => {

                let videoId = jQuery(videoElem).data('video-id');
                let ytPlayer = require('../common/youtubePlayer');

                videoThumbnail = '//img.youtube.com/vi/'+videoId+'/0.jpg';

                let playerContainer = jQuery(videoElem).siblings('.video-container');

                player = ytPlayer(playerContainer, videoId);


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
                            //remove overlay
                            if($('.video-overlay').length) {
                                $('.video-overlay').toggleClass('closed');
                            }

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

    if ($('.slider-nav').length) {
        let slideToShow = $('.slider-for .item').length > 3 ? 3 : $('.slider-for .item').length;
        sliderMainData = {
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.slider-nav',
            rtl: true,
            ifinite: false
        };

        sliderNavData = {
            slidesToShow: slideToShow,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            dots: false,
            variableWidth: true,
            focusOnSelect: true,
            centerPadding: '60px',
            rtl: true,
            ifinite: false
        };
        if ($('.slider-for .item').length > 3) {
            sliderNavData.prevArrow = '<div class="arrow arrow-next"></div>';
            sliderNavData.nextArrow = '<div class="arrow arrow-prev"></div>';
        } else {
            sliderNavData.arrows = false;
        }

        $(' .slider.slider-nav').on('swipe', function () {
            if (typeof player !== 'undefined') {
                player.pauseVideo();
            }
        });

        $('.slider .arrow').on('click', function () {
            if (typeof player !== 'undefined') {
                player.pauseVideo();
            }
        });

        $('.slider.slider-nav .slick-slide').on('click', function () {
          let currentNavItem = $('.slider.slider-nav .slick-current').find('.play-btn');
          if(!currentNavItem.length){
              player.pauseVideo();
          }
        })
    }

    if ($('.slider.recipe-content-steps')) {
        $('.slider.recipe-content-steps').slick({
            rtl: true,
            infinite: false,
            nextArrow: '<div class="arrow arrow-prev"></div>',
            prevArrow: '<div class="arrow arrow-next"></div>',
        });

        $('.slider.recipe-content-steps .arrow-prev').attr('style', 'color:grey');
    }
    // if ($('.slider.recipe-content-steps')) {
    //     $('.slider.recipe-content-steps').slick({
    //         rtl: true,
    //         infinite: false,
    //         prevArrow: '<div class="arrow arrow-prev">&#x27F6; לשלב הקודם</div>',
    //         nextArrow: '<div class="arrow arrow-next"> לשלב הבא &#x27F5; </div>',
    //     });
    //
    //     $('.slider.recipe-content-steps .arrow-prev').attr('style', 'color:grey');
    // }
}

jQuery(document).ready(($) => {

    if($('.slider-nav .video-image').length){
        $('.slider-nav .video-image').attr('src', videoThumbnail);
        if($('.primary-image.print').length && $('.primary-image.print').data('is-video') == '1'){
            $('.primary-image.print').attr('src', videoThumbnail);
        }
    }

    if(sliderMainData){
        $('.slider-for').slick(sliderMainData);
    }

    if(sliderNavData){
        $('.slider-nav').slick(sliderNavData);
    }

    let isSwiping = false;

    $('.video-overlay').on('mousedown', function() {
        isSwiping = false;
    });

    $('.video-overlay').on('mousemove', function() {
        isSwiping = true;
    });


    $('.video-overlay').on('mouseup', function(e) {
        if (isSwiping && e.button === 0) {
            // swiping
        } else {
            // clicked
            $(this).toggleClass('closed');
            player.playVideo();
        }
    });

    $(' .slider.slider-nav').on('swipe', function () {
        if (typeof player !== 'undefined') {
            player.pauseVideo();
        }
    });

    $('.slider .arrow').on('click', function () {
        if (typeof player !== 'undefined') {
            player.pauseVideo();
        }
    });

    $('.slider.slider-nav .slick-slide').on('click', function () {
        debugger
        let currentNavItem = $('.slider.slider-nav .slick-current').find('.play-btn');
        if(!currentNavItem.length){
            player.pauseVideo();
        }
    });

    if($('.featured-content-container')){
        $('.featured-content-container').attr('style', 'visibility: visible');
    }

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

    $(".show-read-more").each(function () {
        let str = $.trim($(this).text());
        let maxLength;
        if ($(this).hasClass('description')) {
            maxLength = foodyGlobals.isMobile ? 85 : 170;
        } else {
            maxLength = foodyGlobals.isMobile ? 125 : 310;
        }
        if (str.length > maxLength) {
            let newStr = str.substring(0, maxLength);
            let indexToStartSubstring = Math.min(newStr.length, newStr.lastIndexOf(" "));
            newStr = newStr.substr(0, indexToStartSubstring);
            let removedStr = str.substring(indexToStartSubstring, str.length);
            $(this).empty().html(newStr);
            $(this).append(' <a href="javascript:void(0);" class="read-more">עוד...</a>');
            $(this).append('<span class="more-text">' + removedStr + '</span>');
        }
    });
    $(".read-more").click(function () {
        $(this).siblings(".more-text").contents().unwrap();
        $(this).remove();
    });

    if ($('.overview-nutrients .overview-item > .value').length && $('.overview-nutrients .recipe-nutrition').length) {
        $('.overview-nutrients .overview-item > .value').on('click', function () {
            $('.overview-nutrients .value').toggleClass('open');
            $('.overview-nutrients .recipe-nutrition').toggleClass('open');
        });

        $('.overview-nutrients .close-btn').on('click', function () {
            $('.overview-nutrients .recipe-nutrition').toggleClass('open');
            $('.overview-nutrients .value').toggleClass('open');
        });
    }

    if ($('.amount-container > .plus-icon').length && $('.amount-container > .minus-icon').length && $('.amount-container #number-of-dishes').length) {
        $('.amount-container > .plus-icon').on('click', function () {
            let currentAmount = parseInt($('.amount-container #number-of-dishes').val());
            $('.amount-container #number-of-dishes').val(++currentAmount);
            if($('.recipe-overview-print .overview-table  .cell-value.dishes_amount').length){
                $('.recipe-overview-print .overview-table  .cell-value.dishes_amount')[0].innerText = currentAmount;
            }
            $('.amount-container #number-of-dishes').trigger('input');
        });

        $('.amount-container > .minus-icon').on('click', function () {
            let currentAmount = parseInt($('.amount-container #number-of-dishes').val());
            if (currentAmount > 1) {
                $('.amount-container #number-of-dishes').val(--currentAmount);
                if($('.recipe-overview-print .overview-table  .cell-value.dishes_amount').length){
                    $('.recipe-overview-print .overview-table  .cell-value.dishes_amount')[0].innerText = currentAmount;
                }
            }
            $('.amount-container #number-of-dishes').trigger('input');
        });
    }

    if ($('.recipe-content-steps .slick-current').length) {
        $('.slider.recipe-content-steps').on('swipe', changeStyleOfArrows);
        $('.slider.recipe-content-steps .arrow-prev, .slider.recipe-content-steps .arrow-next').on('click', changeStyleOfArrows);
    }



    let selectorsArr = {'.recipe-categories':'cat-read-more', '.recipe-accessories':'acc-read-more', '.recipe-techniques':'teq-read-more', '.recipe-tags': 'tag-read-more'};
    for(let key in selectorsArr){
        if ($(key + ' ul li').length) {
            let twoRowsHeight = (parseInt($(key + ' ul li').outerHeight(true)) * 2);
            let originalSize = parseInt($(key + ' ul').outerHeight(true));
            if (originalSize > twoRowsHeight) {
                $(key + ' ul').attr('style', 'height:' + twoRowsHeight + 'px; overflow: hidden');
                $(key).append(' <a data-original-size ="' + originalSize + '" class="' + selectorsArr[key] + '" href="javascript:void(0);">עוד...</a>');
            }
        }
    }


    // read more categories
    if ($('.recipe-categories .post-categories li').length && $('.cat-read-more').length) {
        $('.cat-read-more').on('click', function () {
            $('.recipe-categories .post-categories').attr('style', 'height:' + parseInt($(this).attr('data-original-size')) + 'px');
            $(this).remove();
        });
    }

    // read more accessories
    if ($('.recipe-accessories ul li').length && $('.acc-read-more').length) {
        $('.acc-read-more').on('click', function () {
            $('.recipe-accessories ul').attr('style', 'height:' + parseInt($(this).attr('data-original-size')) + 'px');
            $(this).remove();
        });
    }

    // read more accessories
    if ($('.recipe-techniques ul li').length &&  $('.teq-read-more').length) {
        $('.teq-read-more').on('click', function () {
            $('.recipe-techniques ul').attr('style', 'height:' + parseInt($(this).attr('data-original-size')) + 'px');
            $(this).remove();
        });
    }

    // read more accessories
    if ($('.recipe-tags ul li').length &&  $('.tag-read-more').length) {
        $('.tag-read-more').on('click', function () {
            $('.recipe-tags ul').attr('style', 'height:' + parseInt($(this).attr('data-original-size')) + 'px');
            $(this).remove();
        });
    }

    if ( $("#main > div > div.cover-image.no-print").length )  {
       $("#main > div > aside").css('padding-top','15px')
    }

    // Change Position of difficalty
    if ($("#main .difficulty_level").html().trim() === "קשה מאוד" && $(window).width > 760 ) {
        $("#main .difficulty-container").css('flex-direction','column')
        $("#main .difficulty-container").css('top','7%')
    }
    if ($("#main .difficulty_level").html().trim() !== "קשה מאוד" ) {
        $("#main .difficulty-container").css('top','8%')
        $("#main  .overview-lists-container-desktop  .overview.row  li:nth-child(1)  .key").css('padding-top','2px')
    }

});

function changeStyleOfArrows() {
    let stepElem = $('.recipe-content-steps .slick-current').find('.step');
    if (stepElem.length) {
        if ($(stepElem).hasClass('first-step')) {
            $('.slider.recipe-content-steps .arrow-prev').attr('style', 'color:grey');
            $('.slider.recipe-content-steps .arrow-next').attr('style', 'color:rgb(64, 64, 64)');
        } else if ($(stepElem).hasClass('last-step')) {
            $('.slider.recipe-content-steps .arrow-prev').attr('style', 'color:rgb(64, 64, 64)');
            $('.slider.recipe-content-steps .arrow-next').attr('style', 'color:grey');
        } else {
            $('.slider.recipe-content-steps .arrow-prev').attr('style', 'color:rgb(64, 64, 64)');
            $('.slider.recipe-content-steps .arrow-next').attr('style', 'color:rgb(64, 64, 64)');
        }
    }
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






