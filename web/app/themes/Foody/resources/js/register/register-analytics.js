/**
 * Created by omerfishman on 3/26/19.
 */
let FoodyLocationUtils = require('../common/foody-location-utils');

jQuery(document).ready(($) => {

    if (foodyGlobals['title'] == 'הרשמה') {

        let userRecipeAmount = foodyGlobals['userRecipesCount'];
        /**
         * Un-Logged User Social Registration
         */
        let socialLinks = jQuery('.foody-content .wp-social-login-widget');
        let googleButton = socialLinks.siblings('.login').find('.btn-google');
        let facebookButton = socialLinks.siblings('.login').find('.btn-facebook');

        googleButton.click((event) => {
            eventCallback(event, 'רישום לאתר', 'לחיצה לתחילת רישום', 'גוגל');
        });

        facebookButton.click((event) => {
            eventCallback(event, 'רישום לאתר', 'לחיצה לתחילת רישום', 'פייסבוק');
        });

        /**
         * Registration Submit
         */
        if (jQuery('#register-form').length) {
            $("#register-form").on('submit', null, (event) => {
                let withMarketing = jQuery(event.target).find('#check-marketing').prop('checked');
                let withEBook = jQuery(event.target).find('#check-e-book').prop('checked');
                let marketingAnalyticsText = 'לא נרשם';
                if (withMarketing) {
                    marketingAnalyticsText = 'נרשם';
                    if (withEBook) {
                        marketingAnalyticsText = 'נרשם פלוס ספר';
                    }
                } else if (withEBook) {
                    marketingAnalyticsText = 'לא נרשם פלוס ספר';
                }

                eventCallback(event, 'רישום לאתר', 'לחיצה לסיום רישום', 'אתר', 'רישום לדיוור', marketingAnalyticsText);

                /**
                 * Registration Fail
                 */
                if (jQuery('#register-form').find('label.error:visible').length) {
                    let errorLabels = jQuery('#register-form').find('label.error:visible').map(function(){
                        return jQuery(this).text() + ' ';
                    }).get().join();

                    eventCallback(event, 'רישום לאתר', 'רישום נכשל', 'אתר', 'הודעה', errorLabels);
                }
            });
        }

        /**
         * Successful Registration
         */
        let locationUtils = new FoodyLocationUtils();
        let registered = locationUtils.getQuery('registered');
        if (registered == 1 && foodyGlobals['loggedIn']) {
            let withMarketing = jQuery('.welcome .marketing-approved').val().trim();
            let withEBook = jQuery('.welcome .e-book-approved').val().trim();
            let marketingAnalyticsText = 'לא נרשם';
            if (withMarketing) {
                marketingAnalyticsText = 'נרשם';
                if (withEBook) {
                    marketingAnalyticsText = 'נרשם פלוס ספר';
                }
            } else if (withEBook) {
                marketingAnalyticsText = 'לא נרשם פלוס ספר';
            }

            eventCallback(event, 'רישום לאתר', 'רישום הצליח', 'אתר', 'רישום לדיוור', marketingAnalyticsText);
        }

        /**
         * Is Login or Profile ?
         */
        let isLoggedIn = foodyGlobals['loggedIn'];
        if (isLoggedIn) {
            eventCallback('', 'אזור אישי', 'טעינה', 'מזוהה', "מתכונים", userRecipeAmount);
        } else {
            eventCallback('', 'אזור אישי', 'טעינה', 'לא מזוהה', "מתכונים", userRecipeAmount);
        }
    }

    /**
     * On Social Registration completion
     */
    if (jQuery('#approvals').length) {
        eventCallback('', 'רישום לאתר', 'טעינת פופאפ רישום לדיוור', 'גוגל | פייסבוק');

        $('#approvals').on('submit', null, (event) => {

            let withMarketing = jQuery(event.target).find('#check-marketing').prop('checked');
            let withEBook = jQuery(event.target).find('#check-e-book').prop('checked');
            let marketingAnalyticsText = 'לא נרשם';
            if (withMarketing) {
                marketingAnalyticsText = 'נרשם';
                if (withEBook) {
                    marketingAnalyticsText = 'נרשם פלוס ספר';
                }
            } else if (withEBook) {
                marketingAnalyticsText = 'לא נרשם פלוס ספר';
            }

            eventCallback(event, 'רישום לאתר', 'לחיצה לסיום רישום', 'גוגל | פייסבוק', 'רישום לדיוור', marketingAnalyticsText);
        });
    //    TODO: Succes & Failure
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