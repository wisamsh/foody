/**
 * Created by omerfishman on 3/26/19.
 */

jQuery(document).ready(($) => {

    if (foodyGlobals['type'] == 'profile') {
        /**
         * Logged User
         */
        let userRecipeAmount = foodyGlobals['userRecipesCount'];
        //TODO:: Change Selectors
        let userChannels = jQuery('#user-content > .my-channels > .channels > ul.managed-list');
        userChannels.delegate('li.managed-list-item', 'click', function (event) {
            let channelName = jQuery(this).find("a").text().trim();
            eventCallback(event, 'אזור אישי', 'בחירת ערוץ', channelName, "מתכונים", userRecipeAmount);
        });

        /**
         * My recipes click
         */
        let userRecipes = jQuery('#my-recipes-grid');
        userRecipes.delegate('.recipe-item-container .recipe-item', 'click', function (event) {
            let recipeName = jQuery(this).find(".grid-item-title > a").text().trim();
            eventCallback(event, 'אזור אישי', 'בחירת מתכון', recipeName, "מתכונים", userRecipeAmount);
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