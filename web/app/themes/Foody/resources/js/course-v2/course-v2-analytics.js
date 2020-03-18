const analyticsCategory = 'קורסים';
const timeInPageDelta = 30;

const Vimeo = require('@vimeo/player');

jQuery(document).ready(($) => {
    if(foodyGlobals['page_template_name'] == "foody-course-new") {
        let scrollsArr = {'0': false, '25': false, '50': false, '75': false, '100': false};
        let secondsInPage = 0;

        eventCallback('', analyticsCategory, 'טעינה', foodyGlobals.post.title, '', '', foodyGlobals.post['hostName']);

        let registrationLinks = [
            {
                selector: '.sticky-registration .button-purchase a',
                label: 'צף',
                action: 'לחיצה להרשמה'
            },
            {
                selector: '.sticky-registration .gift-purchase a',
                label: 'צף',
                action: 'קנייה לחבר'
            },
            {
                selector: '.gift-and-purchase-buttons .buttons-container .purchase-button-div',
                label: 'אזור רכישה',
                action: 'לחיצה להרשמה'
            },
            {
                selector: '.gift-and-purchase-buttons .buttons-container .gift-button-div',
                label: 'אזור רכישה',
                action: 'קנייה לחבר'
            },
            {
                selector: '.video-section .video-section-purchase',
                label: 'אזור וידאו',
                action: 'לחיצה להרשמה'
            },
            {
                selector: '.always-wanted-section .always-wanted-section-purchase',
                ancestor_selector: '.always-wanted-section',
                label: '',
                title_selector: '.always-wanted-title',
                action: 'לחיצה להרשמה'
            },
            {
                selector: '.buy-kit-container .course-v2-purchase-button',
                ancestor_selector: '.buy-kit-container',
                label: '',
                title_selector: '.buy-kit-title',
                action: 'לחיצה להרשמה'
            },
            {
                selector: '.syllabus-section .course-v2-purchase-button',
                ancestor_selector: '.syllabus-section',
                label: '',
                title_selector: '.syllabus-title',
                action: 'לחיצה להרשמה'
            },
            {
                selector: '.faq-section .course-v2-purchase-button',
                ancestor_selector: '.faq-section',
                label: '',
                title_selector: '.faq-title',
                action: 'לחיצה להרשמה'
            },
            {
                selector: '.testimonials-section .course-v2-purchase-button',
                ancestor_selector: '.testimonials-section',
                label: '',
                title_selector: '.testimonials-title',
                action: 'לחיצה להרשמה'
            }


        ];

        registrationLinks.forEach((link) => {
            $(link.selector).on('click', () => {
                if(link.label != '') {
                    eventCallback('', analyticsCategory, link.action, foodyGlobals.post.title, 'מיקום', link.label, foodyGlobals.post['hostName']);
                }
                else{
                    if(link.title_selector !='' && link.ancestor_selector !=''){
                        let locationTitle = $(link.ancestor_selector + ' ' + link.title_selector).length ? $(link.ancestor_selector + ' ' + link.title_selector)[0].innerText : '';
                        eventCallback('', analyticsCategory, link.action, foodyGlobals.post.title, 'מיקום', locationTitle, foodyGlobals.post['hostName']);
                    }
                }
            });
        });

        /** video */
        //
        // let $video = $('.featured-content-container #video');
        // let videoStopped = false;
        //
        // doVideoAnalytics()


        /**
         * Scroll listener
         */
        $(window).scroll(function (e) {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height();
            const winHeight = $(window).height();
            const scrollPercent = (scrollTop) / (docHeight - winHeight);
            const scrollPercentRounded = Math.round(scrollPercent * 100);
            let toLog = false;
            if (scrollPercentRounded === 0 || scrollPercentRounded === 25 ||
                scrollPercentRounded === 50 || scrollPercentRounded === 75 || scrollPercentRounded === 100) {
                toLog = true;
            }
            if (toLog) {
                if (!scrollsArr[scrollPercentRounded]) {
                    eventCallback(e, analyticsCategory, 'גלילה', scrollPercentRounded + '%', '', '', foodyGlobals.post['hostName']);
                    scrollsArr[scrollPercentRounded] = true;
                }
            }
        });

        let interval = setInterval(function () {
            secondsInPage += timeInPageDelta;
            if (secondsInPage == timeInPageDelta) {
                eventCallback('', analyticsCategory, 'טיימר', foodyGlobals.post.title, 'זמן', secondsInPage + 's', foodyGlobals.post['hostName']);
            } else {
                let timerString = toMinutes(secondsInPage);
                eventCallback('', analyticsCategory, 'טיימר', foodyGlobals.post.title, 'זמן', timerString, foodyGlobals.post['hostName']);
                if (timerString == '20m') {
                    clearInterval(interval);
                }
            }
        }, timeInPageDelta * 1000);

        /** recommendation slider */
        if($('.testimonials-slider .slick-arrow').length){
            $('.testimonials-slider .slick-arrow').on('click', function () {
                eventCallback('', analyticsCategory, 'לקוחות ממליצים', foodyGlobals.post.title, '', '', foodyGlobals.post['hostName']);
            });
        }
    }
});

// function doVideoAnalytics(video) {
//     if (video && video.length) {
//         jQuery.each(video, (index, videoElem) => {
//
//             let videoId = jQuery(videoElem).data('video-id');
//             let ytPlayer = require('../common/youtubePlayer');
//
//             let playerContainer = jQuery(videoElem).siblings('.video-container');
//
//             let player = ytPlayer(playerContainer, videoId);
//
//
//             /*
//              * @see https://developers.google.com/youtube/iframe_api_reference#Events
//              * -1 (unstarted)
//              * 0 (ended)
//              * 1 (playing)
//              * 2 (paused)
//              * 3 (buffering)
//              * 5 (video cued).
//              * */
//             let firstPlay = true;
//             let timeUpdater;
//             let videoTime = 0;
//             let sentPercentage = {};
//             player.on('stateChange', (event) => {
//
//                 switch (event.data) {
//                     // video ended
//                     case 0:
//                         clearInterval(timeUpdater);
//                         sentPercentage = {};
//                         break;
//
//                     // video playing
//                     case 1:
//                         timeUpdater = setInterval(updateTime, 1000);
//                         if (firstPlay) {
//                             firstPlay = false;
//                             eventCallback(event, 'מתכון', 'צפייה בווידאו', 'הפעלה', 'מיקום', '0%');
//                         } else {
//                             let durationPromise = player.getDuration();
//                             let currPromise = player.getCurrentTime();
//                             Promise.all([durationPromise, currPromise]).then(function (values) {
//                                 let passPercentage = Math.round((values[1] / values[0]) * 100);
//                                 let reminder = passPercentage % 10;
//                                 if (reminder <= 5) {
//                                     passPercentage = passPercentage - reminder;
//                                 } else {
//                                     let addToRoundUp = 10 - reminder;
//                                     passPercentage = passPercentage + addToRoundUp;
//                                 }
//                                 if(videoStopped){
//                                     eventCallback(event, 'מתכון', 'צפייה בווידאו', 'הפעלה מחדש לאחר הפסקה', 'מיקום', passPercentage + '%');
//                                     videoStopped = false;
//                                 }
//                                 eventCallback(event, 'מתכון', 'צפייה בווידאו', 'התקדמות', 'מיקום', passPercentage + '%');
//                             });
//                         }
//                         analytics.timeEvent('recipe video');
//                         break;
//                     // video paused
//                     case 2:
//                         clearInterval(timeUpdater);
//                         let pausedDurationPromise = player.getDuration();
//                         let pausedCurrPromise = player.getCurrentTime();
//                         Promise.all([pausedDurationPromise, pausedCurrPromise]).then(function (values) {
//                             let passPercentage = Math.round((values[1] / values[0]) * 100);
//                             if (passPercentage % 10 != 0) {
//                                 let reminder = passPercentage % 10;
//                                 if (reminder <= 5) {
//                                     passPercentage = passPercentage - reminder;
//                                 } else {
//                                     let addToRoundUp = 10 - reminder;
//                                     passPercentage = passPercentage + addToRoundUp;
//                                 }
//                             }
//                             eventCallback(event, 'מתכון', 'עצירת ווידאו', 'עצירה', 'מיקום', passPercentage + '%');
//                             videoStopped = true;
//                         });
//
//
//                         analytics.event('recipe video', {
//                             id: foodyGlobals.objectID,
//                             title: foodyGlobals.title
//                         });
//                         break;
//                 }
//             });
//
//
//         });
//     }
// }

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


function toMinutes(secondsInPage) {
    if (secondsInPage % 60 == 0) {
        return secondsInPage / 60 + 'm';
    } else {
        return (secondsInPage / 60) - 0.5 + 'm' + 30 + 's';
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
function eventCallback(event, category, action, label, cdDesc, cdValue, hostName = '') {

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        '',
        '',
        hostName,
        '',
        '',
        '',
        '',
        '',
        '',
        cdDesc,
        cdValue,
        ''
    );
}