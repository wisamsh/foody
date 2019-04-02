/**
 * Created by omerfishman on 3/26/19.
 */

jQuery(document).ready(($) => {
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

    //TODO:: Put this to work after login
    // eventCallback(event, 'הזדהות', 'לחיצה להזדהות', 'אתר');
    // eventCallback(event, 'הזדהות', 'הזדהות נכשלה', 'אתר');
    // let loginErrorMessage = jQuery('#login-form').find('span').text();
    // eventCallback(event, 'הזדהות', 'הזדהות הצליחה', 'אתר', 'הודעה', loginErrorMessage);


    /**
     * Logged User
     */
    let userRecipeAmount = foodyGlobals.userRecipesCount;
    //TODO:: Change Selectors
    let userChannels = jQuery('#user-content > section > section > ul');
    userChannels.delegate('li', 'click', function () {
        let channelName = jQuery(this).find("a").text().trim();
        eventCallback(event, 'אזור אישי', 'בחירת ערוץ', channelName, "מתכונים", userRecipeAmount);
    });

    //TODO:: Change Selectors
    let userRecipes = jQuery('#my-recipes-grid');
    userRecipes.delegate('div > div', 'click', function () {
        let recipeName = jQuery(this).find(".grid-item-title > a").text().trim();
        eventCallback(event, 'אזור אישי', 'בחירת מתכון', recipeName, "מתכונים", userRecipeAmount);
    });

    /**
     * Is Login or Profile ?
     */
    let isLoggedIn = foodyGlobals.loggedIn;
    // if (isLoggedIn) {
    //     eventCallback(event, 'אזור אישי', 'טעינה', 'מזוהה', "מתכונים", userRecipeAmount);
    // } else {
    //     eventCallback(event, 'אזור אישי', 'טעינה', 'לא מזוהה', "מתכונים", "0");  //TODO:: Check this ?
    // }

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

    tagManager.pushDataLayer(
        category,
        action,
        label,
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
        '',
        cdDesc,
        cdValue,
        ''
    );
}