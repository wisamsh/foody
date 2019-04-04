/**
 * Created by moveosoftware on 10/29/18.
 */
jQuery(document).ready(($) => {
    let newsletterForm = jQuery(".site-footer .newsletter .wpcf7-form");

    newsletterForm.on('wpcf7:submit', null, (event) => {
        eventCallback(event, foodyGlobals['title'], 'לחיצה לרישום לדיוור', '', 'מיקום', 'פוטר');
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