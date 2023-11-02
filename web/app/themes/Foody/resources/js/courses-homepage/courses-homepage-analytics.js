const analyticsCategory = 'קורסים';
const timeInPageDelta = 30;

const Vimeo = require('@vimeo/player');

jQuery(document).ready(($) => {
        if (foodyGlobals['page_template_name'] == "foody-courses-homepage") {
            let scrollsArr = {'0': false, '25': false, '50': false, '75': false, '100': false};
            let secondsInPage = 0;

            /** page loading **/

            eventCallback('', analyticsCategory, 'טעינה', foodyGlobals.title, '', '', '');

            /** video **/
            let frameMain = $('.course-cover .cover-video-container .cover-video iframe');
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
                            eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%');
                        } else if (event.event != 'timeupdate') {
                            eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%');
                        }
                    });
                });

            }

            /** video **/
            let frameSecond = $('.advantages-section .advantages-container .cover-video iframe');
            if (frameSecond.length && !frameSecond[0]['src'].includes('youtube')) {
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
                            eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%');
                        } else if (event.event != 'timeupdate') {
                            eventCallback('', analyticsCategory, 'צפייה בווידאו', event.label, 'מיקום', percent + '%');
                        }
                    });
                });

            }

            /** Scroll listener **/
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
                        eventCallback(e, analyticsCategory, 'גלילה', scrollPercentRounded + '%', '', '');
                        scrollsArr[scrollPercentRounded] = true;
                    }
                }
            });

            /** timer **/
            let interval = setInterval(function () {
                secondsInPage += timeInPageDelta;
                if (secondsInPage == timeInPageDelta) {
                    eventCallback('', analyticsCategory, 'טיימר', foodyGlobals.title, 'זמן', secondsInPage + 's');
                } else {
                    let timerString = toMinutes(secondsInPage);
                    eventCallback('', analyticsCategory, 'טיימר', foodyGlobals.title, 'זמן', timerString);
                    if (timerString == '20m') {
                        clearInterval(interval);
                    }
                }
            }, timeInPageDelta * 1000);

            /** click on course - course lists **/
            let coursesLinks =  $('.courses-section .courses-list-container .course-link');
            coursesLinks.on('click', function () {
                if ($(this).attr('href') != '') {
                    let courseHostContainer = $(this).find('.course-name-container');
                    let sectionTitleContainer = $(this).closest('section').find('.title');

                    let courseName = courseHostContainer.attr('data-course');
                    let hostName = courseHostContainer.attr('data-host');
                    let sectionTitle = sectionTitleContainer.length ? sectionTitleContainer[0].innerText : '';

                    eventCallback('', analyticsCategory, 'בחירת קורס', courseName, 'מיקום', sectionTitle, hostName);
                }
            });

            /** click on course - course lists **/
            let teamLinks =  $('.team-section .team-list-container .course-item-button');
            teamLinks.on('click', function () {
                if ($(this).attr('href') != undefined) {
                    let infoContainer = $(this).closest('.team-item-info');
                    let hostNameContainer = infoContainer.find('.host-name');
                    let sectionTitleContainer = $(this).closest('section').find('.title');

                    let hostName = hostNameContainer.length ? hostNameContainer[0].innerText.replace('-', '') : '';
                    let sectionTitle = sectionTitleContainer.length ? sectionTitleContainer[0].innerText : '';

                    eventCallback('', analyticsCategory, 'בחירת קורס', hostName, 'מיקום', sectionTitle, hostName);
                }
            });
        }
    });

function coursesLinkClicked(link) {

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
    let object = foodyGlobals.title ? foodyGlobals.title : '';

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
        '',
        object
    );
}