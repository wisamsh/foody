const analyticsCategory = 'קורסים';

const VimeoPlayer = require('@vimeo/player');

jQuery(document).ready(($) => {

    eventCallback('', analyticsCategory, 'טעינה', foodyGlobals.post.title);

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
            label: 'איך זה עובד?'
        },
        {
            selector: '.course-plan-container .classes-registration-link a',
            label: 'תכנית השיעורים'
        },
        {
            selector: '.legal-registration-link a',
            label: 'מידע ליגאלי'
        },
    ];

    registrationLinks.forEach((link) => {
        $(link.selector).on('click', () => {
            eventCallback('', analyticsCategory, 'לחיצה להרשמה', foodyGlobals.post.title, 'מיקום', link.label);
        });
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
            eventCallback('', analyticsCategory, 'שיתוף', clickedMedium);
        });
    });


    $('#main-content .newsletter form').on('submit', () => {
        eventCallback('', analyticsCategory, 'לחיצה על רישום לדיוור', foodyGlobals.post.title, 'מיקום', 'פוטר');
    });

    $('.recommendations .recommendations-container').on('afterChange', () => {
        eventCallback('', analyticsCategory, 'שיתוף', foodyGlobals.post.title);
    });


    let player = new VimeoPlayer('.cover-video iframe');

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
        player.on(event.event, event, (e) => {
            let percent = parseFloat(e.data.percent);
            percent = parseFloat(percent.toFixed(1)) * 100;
            // send event only if video time
            if (percent % 10 === 0 && !(sentEvents[e.data.event][String(percent)])) {
                sentEvents[e.data.event][String(percent)] = true;
                eventCallback('', analyticsCategory, 'צפייה בווידאו', e.data.label, 'מיקום', percent);
            }
        });
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
function eventCallback(event, category, action, label, cdDesc, cdValue) {

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
        '',
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