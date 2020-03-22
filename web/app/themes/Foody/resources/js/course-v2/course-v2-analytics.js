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

        let frameMain = $('.video-section .main-video iframe');
        if (frameMain.length && !frameMain[0]['src'].includes('youtube')) {
            let player = new Vimeo.default(frameMain);

            let playerEvents = [
                {
                    event: 'play',
                    label: 'הפעלה',
                },
                {
                    event: 'timeupdate',
                    label: 'התקדמות',
                },
                {
                    event: 'pause',
                    label: 'עצירה',
                }
            ];

            let sentEvents = {
                play: {},
                timeupdate: {},
                pause: {}
            };

            playerEvents.forEach((event) => {
                player.on(event.event, (e) => {
                    let percent = parseFloat(e.percent);
                    percent = parseFloat(percent.toFixed(1)) * 100;
                    // send event only if video time
                    if (event.event == 'timeupdate' && percent % 10 === 0 && !(sentEvents[event.event][String(percent)])) {
                        sentEvents[event.event][String(percent)] = true;
                        eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%', foodyGlobals.post['hostName']);
                    } else if (event.event != 'timeupdate') {
                        eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%', foodyGlobals.post['hostName']);
                    }
                });
            });

        }

        let frameSecond = $('.testimonials-section .video-container iframe');
        if (frameMain.length && !frameSecond[0]['src'].includes('youtube')) {
            let player = new Vimeo.default(frameSecond);

            let playerEvents = [
                {
                    event: 'play',
                    label: 'הפעלה',
                },
                {
                    event: 'timeupdate',
                    label: 'התקדמות',
                },
                {
                    event: 'pause',
                    label: 'עצירה',
                }
            ];

            let sentEvents = {
                play: {},
                timeupdate: {},
                pause: {}
            };

            playerEvents.forEach((event) => {
                player.on(event.event, (e) => {
                    let percent = parseFloat(e.percent);
                    percent = parseFloat(percent.toFixed(1)) * 100;
                    // send event only if video time
                    if (event.event == 'timeupdate' && percent % 10 === 0 && !(sentEvents[event.event][String(percent)])) {
                        sentEvents[event.event][String(percent)] = true;
                        eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%', foodyGlobals.post['hostName']);
                    } else if (event.event != 'timeupdate') {
                        eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%', foodyGlobals.post['hostName']);
                    }
                });
            });

        }

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