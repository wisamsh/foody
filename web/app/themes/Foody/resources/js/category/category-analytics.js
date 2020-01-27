/**
 * Created by danielkissos on 24/11/19.
 */

let pageCounter = 1;

jQuery(document).ready(($) => {
    if (foodyGlobals.type && (foodyGlobals.type == 'category' || foodyGlobals.type == 'categories')) {

        /** page load **/
        if(typeof(foodyGlobals['channel_name']) != "undefined" && foodyGlobals['channel_name'].length){
            // feed area category
            let categoryName = foodyGlobals['title'].length ? foodyGlobals['title'] : '';
            eventCallback('', 'מתחם פידים', 'טעינת קטגוריה', categoryName, '', '', '', foodyGlobals['channel_name']);

        }
        else {
            // regular category
            eventCallback('', 'עמוד קטגוריה', 'טעינה', '', '', '', '', foodyGlobals['title']);
        }

        /** selecting a category from header **/
        if ($('.slick-track .slick-slide').length) {
            $('.slick-track .slick-slide a').on('click', function () {
                let $categoryName = this.innerText;
                eventCallback('', 'עמוד קטגוריה', 'בחירת קטגוריה', $categoryName, 'מיקום', '', 'תמונה', foodyGlobals['title']);
            });
        }

        /** result sorting **/
        $('.grid-sort').on('click', 'span.text', function (event) {
            let $sorting_method = this.innerText;
            if ($sorting_method != 'סדר על פי') {
                eventCallback('', 'עמוד קטגוריה', 'מיון רשימה', foodyGlobals['title'], ' מיון', $sorting_method, '', foodyGlobals['title']);
            }
        });

        /** redirect to recipe through image **/
        $('#category-feed').on('click', '.image-container.main-image-container img', function (event) {
            let order_in_Grid = getRecipeLocation(this.parentElement);
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement);
            if(typeof foodyGlobals['channel_name'] !== 'undefined' && foodyGlobals['channel_name'] != ''){
                eventCallback('', 'מתחם פידים', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, '', foodyGlobals['channel_name'], recipeDetails);
            }
            else {
                eventCallback('', 'עמוד קטגוריה', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, 'תמונה', foodyGlobals['title'], recipeDetails);
            }
        });

        /** redirect to recipe through title **/
        $('#category-feed').on('click', '.grid-item-title a', function (event) {
            let order_in_Grid = getRecipeLocation(this.parentElement);
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement.parentElement);
            if(typeof foodyGlobals['channel_name'] !== 'undefined' && foodyGlobals['channel_name'] != ''){
                eventCallback('', 'מתחם פידים', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, '', foodyGlobals['channel_name'], recipeDetails);
            }
            else {
                eventCallback('', 'עמוד קטגוריה', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, 'כותרת', foodyGlobals['title'], recipeDetails);
            }
        });

        /** redirect to recipe through video duration **/
        $('#category-feed').on('click', '.duration', function (event) {
            let order_in_Grid = getRecipeLocation(this.parentElement);
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement);
            if(typeof foodyGlobals['channel_name'] !== 'undefined' && foodyGlobals['channel_name'] != ''){
                eventCallback('', 'מתחם פידים', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, '', foodyGlobals['channel_name'], recipeDetails);
            }
            else {
                eventCallback('', 'עמוד קטגוריה', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, 'ווידאו', foodyGlobals['title'], recipeDetails);
            }
        });

        /** add recipe to favorites **/
        $('#category-feed').on('click', '.favorite-container .icon-heart', function (event) {
            let order_in_Grid = getRecipeLocation(this.parentElement);
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement);
            eventCallback('', 'עמוד קטגוריה', 'הוספה למועדפים', foodyGlobals['title'], ' מיקום', order_in_Grid, '', foodyGlobals['title'], recipeDetails);
        });

        /** remove recipe from favorites **/
        $('#category-feed').on('click', '.favorite-container .icon-favorite-pressed', function (event) {
            let order_in_Grid = getRecipeLocation(this.parentElement);
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement);
            eventCallback('', 'עמוד קטגוריה', 'הסרה ממועדפים', foodyGlobals['title'], ' מיקום', order_in_Grid, '', foodyGlobals['title'], recipeDetails);
        });

        /** redirect to author through name **/
        $('#category-feed').on('click', '.image-container > ul > li:first-child', function (event) {
            let order_in_Grid = getRecipeLocation(this.parentElement);
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement.parentElement);
            eventCallback('', 'עמוד קטגוריה', 'בחירה בשף', foodyGlobals['title'], ' מיקום', order_in_Grid, 'שם', foodyGlobals['title'], recipeDetails);
        });

        /** redirect to author through image **/
        $('#category-feed').on('click', '.image-container > a > img', function (event) {
            let order_in_Grid = getRecipeLocation(this.parentElement);
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement.parentElement);
            eventCallback('', 'עמוד קטגוריה', 'בחירה בשף', foodyGlobals['title'], ' מיקום', order_in_Grid, 'תמונה', foodyGlobals['title'], recipeDetails);
        });

        /** add/remove filters **/
        $('.sidebar-section').on('click', '.md-checkbox', function () {
            if (this.children[0].checked) {
                eventCallback('', 'עמוד קטגוריה', 'הסרת סינון', foodyGlobals['title'], ' סינון', this.innerText, '', foodyGlobals['title']);
            } else {
                eventCallback('', 'עמוד קטגוריה', 'הוספת סינון', foodyGlobals['title'], ' סינון', this.innerText, '', foodyGlobals['title']);
            }
        });

        /** click load more recipes **/
        $('.foody-grid').on('click', '.show-more', function () {
            pageCounter++;
            eventCallback('', 'עמוד קטגוריה', 'עוד מתכונים', foodyGlobals['title'], 'מיקום', pageCounter, '', foodyGlobals['title']);
        });
    }
});

function getRecipeLocationFromParent(parent) {
    let dataset = parent;
    var flag = true;
    while ((flag)) {
        if (jQuery.inArray('grid-item', dataset.className.split(' ')) == -1) {
            dataset = dataset.parentNode;
        } else {
            flag = false;
        }
    }
    return dataset;
}

function getRecipeLocation(recipeParent) {
    let dataset = getRecipeLocationFromParent(recipeParent);
    let childrenList = $('#category-feed').children();
    let location = -1;
    childrenList.each(function (index) {
        if (childrenList[index].dataset.id == dataset.dataset.id) {
            location = index;
            return false;
        }
    });

    return location + 1;
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
 * @param recipe_order_location
 */
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', recipe_order_location, itemCategory = '', recipeDetails = '') {

    /**
     * Recipe name
     */
    let recipe_name = recipeDetails != '' ? recipeDetails['title'] : '';

    /**
     * Item category
     */
    let item_category = itemCategory;

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
    let order_location = recipe_order_location;

    /**
     * Recipe view count
     */
    let amount = '';

    /**
     * Has rich content - does contains video or product buy option
     */
    let hasRichContent = '';

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
        ''
    );
}
