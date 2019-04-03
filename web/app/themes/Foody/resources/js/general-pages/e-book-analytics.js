/**
 * Created by omerfishman on 4/3/19.
 */

jQuery(document).ready(($) => {

    if (jQuery('.page-template-e-book').length) {

        /**
         * Page Load
         */
        eventCallback('', 'ebook', 'טעינת עמוד', 'מתכוני פסח');//TODO: Hard-Coded Passover PDF

        /**
         * Register button click
         */
        let registerButton = jQuery('#main-content .site-content .btn-primary');
        registerButton.on('click', null, function () {
            eventCallback(event, 'ebook', 'מעבר לרישום לאתר', 'מתכוני פסח');
        });
    }
});


/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 * @param action
 * @param label
 */
function eventCallback(event, category, action, label) {

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
        '',
        '',
        ''
    );
}