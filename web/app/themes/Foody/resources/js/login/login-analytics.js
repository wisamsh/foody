/**
 * Created by omerfishman on 3/26/19.
 */

jQuery(document).ready(($) => {

    if (foodyGlobals['title'] == 'התחברות') {

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

        /**
         * Go To Register
         */
        if (jQuery('.foody-content .go-to-register').length) {
            jQuery('.foody-content .go-to-register').click((event) => {
                eventCallback(event, 'רישום לאתר', 'לחיצה לתחילת רישום', 'אתר');
            });
        }

        if (jQuery('#login-form').length) {
            jQuery('#login-form').on('submit', null, (event) => {
                eventCallback(event, 'הזדהות', 'לחיצה להזדהות', 'אתר');
            });
        }

        //TODO:: Put this to work after login
        // eventCallback(event, 'הזדהות', 'הזדהות נכשלה', 'אתר');
        // let loginErrorMessage = jQuery('#login-form').find('span').text();
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