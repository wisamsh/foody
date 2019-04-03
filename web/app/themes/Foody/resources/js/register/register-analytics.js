/**
 * Created by omerfishman on 3/26/19.
 */

jQuery(document).ready(($) => {

    if (foodyGlobals['title'] == 'הרשמה') {

        let userRecipeAmount = foodyGlobals['userRecipesCount'];
        /**
         * Un-Logged User
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
        });

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

        $('#register-form').on('submit', null, (event) => {

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