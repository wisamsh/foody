/**
 * Created by danielkissos on 25/11/19.
 */

jQuery(document).ready(($) => {
    if (foodyGlobals.type && foodyGlobals.type == 'feed_channel') {

        let channelName = foodyGlobals.title ? foodyGlobals.title : '';
        let recipesLocationList = getRecipesLocationsInFeedChannel();

        /** page load **/

        eventCallback('', 'מתחם פידים', 'טעינה', channelName);

        /** loading channel category **/
        // if ($('.categort-listing-title').length) {
        //     $('.categort-listing-title').each(function () {
        //         let categoryName = $(this)[0].innerText;
        //         eventCallback('', 'מתחם פידים', 'טעינת קטגוריה', categoryName);
        //     });
        // }

        /** choose category **/
        if ($('.category-listing').length) {
            $('.category-listing').on('click', function () {
                let categoryName = $(this)[0].innerText;
                eventCallback('', 'מתחם פידים', 'בחירת קטגוריה', categoryName);
            });
        }


        /** redirect to recipe through image **/
        $('.foody-grid ').on('click', '.image-container.main-image-container img', function (event) {
            let dataSet = this.parentElement.parentElement.parentElement.parentElement;
            let recipeName = dataSet ? dataSet.dataset.title : '';
            let recipeLocation = recipesLocationList[recipeName];
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement);
            eventCallback('', 'מתחם פידים', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', recipeLocation, '', '', '', recipeDetails);
        });

        /** redirect to recipe through title **/
        $('.foody-grid ').on('click', '.grid-item-title a', function (event) {
            let dataSet = this.parentElement.parentElement.parentElement.parentElement.parentElement;
            let recipeName = dataSet ? dataSet.dataset.title : '';
            let recipeLocation = recipesLocationList[recipeName];
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement.parentElement);
            eventCallback('', 'מתחם פידים', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', recipeLocation, '', '', '', recipeDetails);
        });

        /** redirect to recipe through video duration **/
        $('.foody-grid ').on('click', '.duration', function (event) {
            let dataSet = this.parentElement.parentElement.parentElement.parentElement;
            let recipeName = dataSet ? dataSet.dataset.title : '';
            let recipeLocation = recipesLocationList[recipeName];
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement);
            eventCallback('', 'מתחם פידים', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', recipeLocation, '', '', '', recipeDetails);
        });

        /** click on banner **/
        if ($('.block-content .foody-banner a').length) {
            $('.block-content .foody-banner a').on('click', function () {
                let bannerLink = $(this).attr('href');
                let bannerName =  $(this).attr('data-banner-name');
                let publisherName = typeof(foodyGlobals['channel_publisher_name']) != "undefined" ? foodyGlobals['channel_publisher_name'] : '';
                if (bannerLink.toLowerCase().indexOf('utm') < 0 && bannerLink.toLowerCase().indexOf('foody') >= 0) {
                    eventCallback('', 'מתחם פידים', 'הקלקה על באנר (הפניה פנימה)', bannerName, ' מפרסם', publisherName);
                }
                else if(bannerLink.toLowerCase().indexOf('utm') >= 0 || bannerLink.toLowerCase().indexOf('foody') < 0){
                    eventCallback('', 'מתחם פידים', 'הקלקה על באנר (הפניה החוצה)', bannerName, ' מפרסם', publisherName);
                }
            });
        }


    }
});

function getRecipesLocationsInFeedChannel() {
    let locationsList = [];
    let location = 1;
    $('.foody-grid .recipe-item .grid-item-title').each(function () {
        locationsList[$(this)[0].innerText] = location;
        location++;
    });

    return locationsList;
}

function getCurrentRecipeDetail(recipeContainer) {
    let recipeDetails = {};

    //get recipe title
    let titleElement = $(recipeContainer).find('.grid-item-title');
    if (titleElement.length) {
        recipeDetails['title'] = titleElement[0] ? titleElement[0].innerText : '';
    }

    //get recipe author
    let authorElementContainer = $(recipeContainer).find('.recipe-item-details');
    if (authorElementContainer.length) {
        let authorElement = $(authorElementContainer).find('li > a');
        if (authorElement.length) {
            recipeDetails['author'] = authorElement[0] ? authorElement[0].innerText : '';
        }
    }

    //has video
    let videoDurationContainer = $(recipeContainer).find('.duration');
    if (videoDurationContainer.length) {
        recipeDetails['has_rich_content'] = true;
    }
    return recipeDetails;
}

/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 * @param action
 * @param label
 * @param cdDesc
 * @param cdValue
 * @param _order_location
 * @param _amount
 */
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', _amount = '', _order_location = '', _object, recipeDetails = '') {

    /**
     * Recipe name
     */
    let recipe_name = recipeDetails != '' ? recipeDetails['title'] : '';

    /**
     * Item category
     */
    let item_category = foodyGlobals.title ? foodyGlobals.title : '';

    /**
     * Chef Name
     */
    let chef = recipeDetails != '' ? recipeDetails['author'] : '';

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    /**
     * Difficulty Level
     */
    let difficulty_level = '';

    /**
     * Preparation Time
     */
    let preparation_time = 0;

    /**
     * Ingredients Count
     */
    let ingredients_amount = 0;

    /**
     * Index of recipe in current Session
     */
    let order_location = _order_location;

    /**
     * Recipe view count
     */
    let amount = _amount;

    /**
     * Has rich content - does contains video or product buy option
     */
    let hasRichContent = (recipeDetails != '' && typeof (recipeDetails['has_rich_content']) != "undefined") ? recipeDetails['has_rich_content'] : false;

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        recipe_name,
        item_category,
        chef,
        difficulty_level,
        preparation_time,
        ingredients_amount,
        order_location,
        amount,
        hasRichContent,
        cdDesc,
        cdValue,
        '',
        _object
    );
}