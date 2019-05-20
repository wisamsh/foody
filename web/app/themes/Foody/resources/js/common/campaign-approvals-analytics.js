jQuery(document).ready(($) => {
    let $campaignApprovalsContainer = $('.campaign-approvals-container');
    if ($campaignApprovalsContainer.length) {

        if (foodyGlobals['loggedIn']) {

            /**
             * On Registration completion
             */
            eventCallback('', 'תחרות מתכונים', 'טעינת מסך השלמת רישום', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'עמוד נחיתה ללא שם');
        }

        let form = $("form#campaign-approvals", $campaignApprovalsContainer);
        if (form) {
            form.on('submit', null, (event) => {
                eventCallback(event, 'תחרות מתכונים', 'לחיצה להשלמת רישום', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'עמוד נחיתה ללא שם');
            });
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