/**
 * Created by danielkissos on 25/11/19.
 */

jQuery(document).ready(($) => {
    if (foodyGlobals.type && foodyGlobals.type == 'search' ) {

        let searchString = ($('.search-results-title').length) ? $('.search-results-title')[0].innerText : '';
        let searchResultsCount = ($('.search-results-count').length) ? $('.search-results-count')[0].innerText : '';
        let separators = separators = ['\\\(', '\\\)'];
        let object = 'חיפוש חופשי';
        searchResultsCount = searchResultsCount.split(new RegExp(separators.join('|'), 'g'))[1];

        var urlParams = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

        if((typeof urlParams[1] != 'undefined') && urlParams[1] == 'auto=1'){
            object = 'מנגנון תוצאות'
        }

        /** page load **/
        set_search_order('results-page-load', searchString);
        eventCallback('', 'חיפוש', 'טעינת עמוד תוצאות', searchString, 'מספר חיפוש', get_search_order('results-page-load', searchString), searchResultsCount, get_search_order('results-page-load', searchString), object);

        /** result sorting **/
        $('.grid-sort').on('click', 'span.text', function (event) {
            let $sorting_method = this.innerText;
            if ($sorting_method != 'סדר על פי') {
                eventCallback('', 'חיפוש', 'מיון רשימה', searchString, ' מיון', $sorting_method, searchResultsCount, get_search_order('results-page-load', searchString), object);
            }
        });

        /** redirect to recipe through image **/
        $('.foody-grid').on('click', '.image-container.main-image-container img', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement);

            eventCallback('', 'חיפוש', 'בחירת מתכון', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString), object, recipeDetails);
        });

        /** redirect to recipe through title **/
        $('.foody-grid').on('click', '.grid-item-title a', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement.parentElement);

            eventCallback('', 'חיפוש', 'בחירת מתכון', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString), object, recipeDetails);
        });

        /** redirect to recipe through video duration **/
        $('.foody-grid').on('click', '.duration', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;
            let recipeDetails = getCurrentRecipeDetail(this.parentElement.parentElement.parentElement);

            eventCallback('', 'חיפוש', 'בחירת מתכון', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString), object, recipeDetails);
        });

        /** add recipe to favorites **/
        $('.foody-grid').on('click', '.favorite-container .icon-heart', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;
            let recipeDetails = getCurrentRecipeDetail($(this).closest('.recipe-item.feed-item'));

            eventCallback('', 'חיפוש', 'הוספה למועדפים', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString), object, recipeDetails);
        });

        /** remove recipe from favorites **/
        $('.foody-grid').on('click', '.favorite-container .icon-favorite-pressed', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;
            let recipeDetails = getCurrentRecipeDetail($(this).closest('.recipe-item.feed-item'));

            eventCallback('', 'חיפוש','הסרה ממועדפים', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString), object, recipeDetails);
        });

        /** redirect to author through name **/
        $('.foody-grid').on('click', '.image-container > ul > li:first-child', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;
            let recipeDetails = getCurrentRecipeDetail($(this).closest('.recipe-item.feed-item'));
            eventCallback('', 'חיפוש','בחירה בשף', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString), object, recipeDetails);
        });

        /** redirect to author through image **/
        $('.foody-grid').on('click', '.image-container > a > img', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;
            let recipeDetails = getCurrentRecipeDetail($(this).closest('.recipe-item.feed-item'));
            eventCallback('', 'חיפוש','בחירה בשף', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString), object, recipeDetails);
        });

        /** add/remove filters **/
        $('.sidebar-section').on('click', '.md-checkbox', function () {
            if (this.children[0].checked) {
                eventCallback('', 'חיפוש','הסרת סינון', searchString, ' סינון', this.innerText,  searchResultsCount, get_search_order('results-page-load', searchString), object);
            } else {
                eventCallback('', 'חיפוש','הוספת סינון', searchString, ' סינון', this.innerText,  searchResultsCount, get_search_order('results-page-load', searchString), object);

            }
        });

        $('.foody-grid').on('click', '.show-more', function () {
            eventCallback('', 'חיפוש', 'עוד מתכונים', searchString, 'מספר חיפוש', get_search_order('results-page-load', searchString), searchResultsCount, get_search_order('results-page-load', searchString), object);
        });
    }
});

function getRecipeLocationFromParent($parent) {
    let dataset = $parent;
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

function set_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        searches_comitted = [];
    }

    if (!searches_comitted.includes(key)) {
        searches_comitted.push(key);
    }
    sessionStorage.setItem(action, JSON.stringify(searches_comitted));
}

function get_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        return 0;
    }
    return jQuery.inArray(key, searches_comitted) +1;
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
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '',  _amount = '', _order_location = '', _object, recipe_details='') {

    /**
     * Recipe name
     */
    let recipe_name = recipe_details != '' ? recipe_details['title'] : '';

    /**
     * Item category
     */
    let item_category = '';

    /**
     * Chef Name
     */
    let chef =  recipe_details != '' ? recipe_details['author'] : '';

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
        '',
        _object
    );
}