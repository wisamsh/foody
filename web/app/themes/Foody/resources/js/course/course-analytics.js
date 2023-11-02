const analyticsCategory = 'קורסים';
const timeInPageDelta = 30;

const Vimeo = require('@vimeo/player');

jQuery(document).ready(($) => {
    if(foodyGlobals['page_template_name'] == "foody-course-efrat") {

        let secondsInPage = 0;

        eventCallback('', analyticsCategory, 'טעינה', foodyGlobals.post.title, '', '', foodyGlobals.post['hostName']);

        let registrationLinks = [
            {
                selector: '.sticky-registration a',
                label: 'צף'
            },
            {
                selector: '.course-information-bottom-section .information-registration-link a',
                label: 'מידע על הקורס'
            },
            {
                selector: '.how-it-works-registration-link a',
                label: 'איך תהפכו לצלמי אוכל'
            },
            {
                selector: '.course-plan-container .classes-registration-link a',
                label: 'תכנית השיעורים'
            },
            {
                selector: '.legal-registration-link a',
                label: 'מידע ליגאלי'
            },
            {
                selector: '.about a',
                label: 'עלות הקורס'
            },
            {
                selector: '.course-is-for a',
                label: 'מה מקבלים'
            },
        ];

        registrationLinks.forEach((link) => {
            $(link.selector).on('click', () => {
                eventCallback('', analyticsCategory, 'לחיצה להרשמה', foodyGlobals.post.title, 'מיקום', link.label, foodyGlobals.post['hostName']);
            });
        });

        $('.banner-text-container > p > .syllabus').on('click', () => {
            eventCallback('', analyticsCategory, 'הורדת סילבוס', foodyGlobals.post.title, 'מיקום', 'קאבר עמוד', foodyGlobals.post['hostName']);
        });

        $('.banner-text-container > p > .purchase').on('click', () => {
            eventCallback('', analyticsCategory, 'לחיצה להרשמה', foodyGlobals.post.title, 'מיקום', 'קאבר עמוד', foodyGlobals.post['hostName']);
        });

        let shareMediums = [
            'facebook',
            'gmail',
            'pinterest',
            'whatsapp'
        ];

        shareMediums.forEach((medium) => {
            $(`.essb_link_${medium}`).on('click', {medium}, (e) => {
                let clickedMedium = e.data.medium;
                eventCallback('', analyticsCategory, 'שיתוף', clickedMedium, '', '', foodyGlobals.post['hostName']);
            });
        });


        $('#main-content .newsletter form').on('submit', () => {
            eventCallback('', analyticsCategory, 'לחיצה על רישום לדיוור', foodyGlobals.post.title, 'מיקום', 'פוטר', foodyGlobals.post['hostName']);
        });

        $('.recommendations .recommendations-container').on('afterChange', () => {
            eventCallback('', analyticsCategory, 'שיתוף', foodyGlobals.post.title, '', '', foodyGlobals.post['hostName']);
        });

        let frame = $('.cover-video iframe');
        if (!frame[0]['src'].includes('youtube')) {
            let player = new Vimeo.default(frame);

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
                eventCallback(e, analyticsCategory, 'גלילה', scrollPercentRounded + '%', '', '', foodyGlobals.post['hostName']);

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