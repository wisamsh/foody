jQuery(document).ready(($) => {
    let $approvalsContainer = $('.approvals-container');
    if ($approvalsContainer.length) {

        if (foodyGlobals['loggedIn'] && foodyGlobals['user'] && foodyGlobals['user']['social_type']) {

            /**
             * On Social Registration completion
             */
            eventCallback('', 'רישום לאתר', 'טעינת מסך השלמת רישום', foodyGlobals['user']['social_type']);

        }

        let form = $("form#approvals", $approvalsContainer);
        if (form) {
            if (form.find('#check-marketing').length == 0 && form.find('#check-e-book').length == 0) {
                window.location.href = '/';
            } else {
                form.on('submit', null, (event) => {
                    let social_type = '';
                    if (foodyGlobals['user']) {
                        social_type = foodyGlobals['user']['social_type'] ? foodyGlobals['user']['social_type'] : ''
                    }
                    let withMarketing = form.find('#check-marketing').prop('checked');
                    let withEBook = form.find('#check-e-book').prop('checked');
                    let marketingAnalyticsText = 'לא נרשם';
                    if (withMarketing) {
                        marketingAnalyticsText = 'נרשם';
                        if (withEBook) {
                            marketingAnalyticsText = 'נרשם פלוס ספר';
                        }
                    } else if (withEBook) {
                        marketingAnalyticsText = 'לא נרשם פלוס ספר';
                    }
                    eventCallback(event, 'רישום לאתר', 'לחיצה לסיום רישום', social_type, 'רישום לדיוור', marketingAnalyticsText);
                });
            }
        }
    }
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