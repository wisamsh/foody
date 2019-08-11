/**
 * Created by omerfishman on 4/3/19.
 */

jQuery(document).ready(($) => {

    if (jQuery('.page-template-foody-campaign').length || jQuery('.page-template-foody-campaign-extended').length) {

        /**
         * Page Load
         */
        eventCallback('', 'ebook', 'טעינת עמוד', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'עמוד נחיתה ללא שם');

        /**
         * Register button click
         */
        let registerButtons = jQuery('' +
            '#main-content .site-content .hero-container a, ' +
            '#main-content .site-content .btn-primary, ' +
            '.page-template-foody-campaign .site-content .foody-content a, ' +
            '.page-template-foody-campaign-extended .site-content .foody-content a');

        registerButtons.on('click', null, function () {
            eventCallback(event, 'ebook', 'מעבר לרישום לאתר', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'עמוד נחיתה ללא שם');
        });
    }

    if (foodyGlobals.type && (foodyGlobals.type == 'campaign')) {

        let $attachment = $('#attachment');

        // prevent upload if not logged in
        $attachment.on('click', (e) => {
            eventCallback(e, 'תחרות מתכונים', 'לחיצה על תעלו תמונה', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'עמוד נחיתה ללא שם');
        });

        var howIDidPopup = jQuery('#upload-image-modal');
        if (howIDidPopup.length) {
            howIDidPopup.find('.btn-approve').click(function (e) {
                eventCallback(e, 'תחרות מתכונים', 'לחיצה לשליחת תמונה', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'עמוד נחיתה ללא שם');
            });
            howIDidPopup.on('hidden.bs.modal', function (e) {
                eventCallback(e, 'תחרות מתכונים', 'תמונה הועלתה בהצלחה', foodyGlobals['campaign_name'] ? foodyGlobals['campaign_name'] : 'עמוד נחיתה ללא שם');
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