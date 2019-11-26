/**
 * Created by danielkissos on 25/11/19.
 */

jQuery(document).ready(($) => {
    if (foodyGlobals.type && foodyGlobals.type == 'search' ) {

        let searchString = ($('.search-results-title').length) ? $('.search-results-title')[0].innerText : '';
        let searchResultsCount = ($('.search-results-count').length) ? $('.search-results-count')[0].innerText : '';
        let separators = separators = ['\\\(', '\\\)']
        searchResultsCount = searchResultsCount.split(new RegExp(separators.join('|'), 'g'))[1];

        /** page load **/
        set_search_order('results-page-load', searchString);
        eventCallback('', 'חיפוש', 'טעינת עמוד תוצאות', searchString, 'מספר חיפוש', get_search_order('results-page-load', searchString), searchResultsCount, get_search_order('results-page-load', searchString));

        /** result sorting **/
        $('.grid-sort').on('click', 'span.text', function (event) {
            let $sorting_method = this.innerText;
            if ($sorting_method != 'סדר על פי') {
                eventCallback('', 'חיפוש', 'מיון רשימה', searchString, ' מיון', $sorting_method, searchResultsCount, get_search_order('results-page-load', searchString));
            }
        });

        /** redirect to recipe through image **/
        $('.foody-grid').on('click', '.image-container.main-image-container img', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'חיפוש', 'בחירת מתכון', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString));
        });

        /** redirect to recipe through title **/
        $('.foody-grid').on('click', '.grid-item-title a', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'חיפוש', 'בחירת מתכון', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString));
        });

        /** redirect to recipe through video duration **/
        $('.foody-grid').on('click', '.duration', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'חיפוש', 'בחירת מתכון', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString));
        });

        /** add recipe to favorites **/
        $('.foody-grid').on('click', '.favorite-container .icon-heart', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'חיפוש', 'הוספה למועדפים', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString));
        });

        /** remove recipe from favorites **/
        $('.foody-grid').on('click', '.favorite-container .icon-favorite-pressed', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'חיפוש','הסרה ממועדפים', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString));
        });

        /** redirect to author through name **/
        $('.foody-grid').on('click', '.image-container > ul > li:first-child', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'חיפוש','בחירה בשף', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString));
        });

        /** redirect to author through image **/
        $('.foody-grid').on('click', '.image-container > a > img', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'חיפוש','בחירה בשף', searchString, ' מיקום', order_in_Grid,  searchResultsCount, get_search_order('results-page-load', searchString));
        });

        /** add/remove filters **/
        $('.sidebar-section').on('click', '.md-checkbox', function () {
            if (this.children[0].checked) {
                eventCallback('', 'חיפוש','הסרת סינון', searchString, ' סינון', this.innerText,  searchResultsCount, get_search_order('results-page-load', searchString));
            } else {
                eventCallback('', 'חיפוש','הוספת סינון', searchString, ' סינון', this.innerText,  searchResultsCount, get_search_order('results-page-load', searchString));

            }
        });

        $('.foody-grid').on('click', '.show-more', function () {
            eventCallback('', 'חיפוש', 'עוד מתכונים', searchString, 'מספר חיפוש', get_search_order('results-page-load', searchString), searchResultsCount, get_search_order('results-page-load', searchString));
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
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '',  _amount = '', _order_location = '') {

    /**
     * Recipe name
     */
    let recipe_name = '';

    /**
     * Item category
     */
    let item_category = '';

    /**
     * Chef Name
     */
    let chef = '';

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
        ''
    );
}