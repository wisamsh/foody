/**
 * Created by omerfishman on 3/26/19.
 */
let FoodyLocationUtils = require('../common/foody-location-utils');

jQuery(document).ready(($) => {

    if (foodyGlobals['title'] == 'התחברות') {

        let userRecipeAmount = foodyGlobals['userRecipesCount'];

        /**
         * Un-Logged User social logins
         */
        let socialLinks = jQuery('.foody-content .wp-social-login-widget');
        let googleButton = socialLinks.siblings('.login').find('.btn-google');
        let facebookButton = socialLinks.siblings('.login').find('.btn-facebook');

        // Google Login
        googleButton.click((event) => {
            eventCallback(event, 'רישום לאתר', 'לחיצה לתחילת רישום', 'Google');
        });

        // Facebook Login
        facebookButton.click((event) => {
            eventCallback(event, 'רישום לאתר', 'לחיצה לתחילת רישום', 'Facebook');
        });

        /**
         * Go To Register
         */
        if (jQuery('.foody-content .go-to-register').length) {
            jQuery('.foody-content .go-to-register').click((event) => {
                eventCallback(event, 'רישום לאתר', 'לחיצה לתחילת רישום', 'אתר');
            });
        }

        /**
         * Login submit
         */
        if (jQuery('#login-form').length) {
            jQuery('#login-form').on('submit', null, (event) => {
                eventCallback(event, 'הזדהות', 'לחיצה להזדהות', 'אתר');
            });
        }

        /**
         * Login Failure
         */
        let locationUtils = new FoodyLocationUtils();
        let loginQuery = locationUtils.getQuery('login');
        if (loginQuery == 'failed' && jQuery('#login-form .foody-alert.login-failed-alert:visible').length) {
            if (jQuery('#login-form .foody-alert.login-failed-alert:visible').css('opacity') != 0) {
                let errorMessage = jQuery('#login-form .foody-alert.login-failed-alert:visible span').text().trim();
                eventCallback(event, 'הזדהות', 'הזדהות נכשלה', 'אתר', 'הודעה', errorMessage);
            }
        }

        /**
         * Login Success - TODO
         */
        // eventCallback(event, 'הזדהות', 'הזדהות הצליחה', 'אתר', 'הודעה', loginErrorMessage);


        /**
         * Is Login or Profile ?
         */
        let isLoggedIn = foodyGlobals['loggedIn'];
        if (isLoggedIn) {
            eventCallback(event, 'אזור אישי', 'טעינה', 'מזוהה', "מתכונים", userRecipeAmount);
        } else {
            eventCallback(event, 'אזור אישי', 'טעינה', 'לא מזוהה', "מתכונים", userRecipeAmount);
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