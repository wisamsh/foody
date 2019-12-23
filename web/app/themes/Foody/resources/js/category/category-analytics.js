/**
 * Created by danielkissos on 24/11/19.
 */

jQuery(document).ready(($) => {
    if (foodyGlobals.type && (foodyGlobals.type == 'category' || foodyGlobals.type == 'categories')) {
        /** page load **/
        eventCallback('', 'עמוד קטגוריה', 'טעינה', '', '', '', '',foodyGlobals['title']);


        /** selecting a category from header **/
        if ($('.slick-track .slick-slide').length) {
            $('.slick-track .slick-slide a').on('click', function () {
                let $categoryName = this.innerText;
                eventCallback('', 'עמוד קטגוריה', 'בחירת קטגוריה', $categoryName, 'מיקום', '', '', foodyGlobals['title']);
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
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'עמוד קטגוריה', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, 'תמונה', foodyGlobals['title']);
        });

        /** redirect to recipe through title **/
        $('#category-feed').on('click', '.grid-item-title a', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'עמוד קטגוריה', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, 'כותרת', foodyGlobals['title']);
        });

        /** redirect to recipe through video duration **/
        $('#category-feed').on('click', '.duration', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'עמוד קטגוריה', 'בחירת מתכון', foodyGlobals['title'], ' מיקום', order_in_Grid, 'ווידאו', foodyGlobals['title']);
        });

        /** add recipe to favorites **/
        $('#category-feed').on('click', '.favorite-container .icon-heart', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'עמוד קטגוריה', 'הוספה למועדפים', foodyGlobals['title'], ' מיקום', order_in_Grid, '', foodyGlobals['title']);
        });

        /** remove recipe from favorites **/
        $('#category-feed').on('click', '.favorite-container .icon-favorite-pressed', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'עמוד קטגוריה', 'הסרה ממועדפים', foodyGlobals['title'], ' מיקום', order_in_Grid, '', foodyGlobals['title']);
        });

        /** redirect to author through name **/
        $('#category-feed').on('click', '.image-container > ul > li:first-child', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'עמוד קטגוריה', 'בחירה בשף', foodyGlobals['title'], ' מיקום', order_in_Grid, 'שם', foodyGlobals['title']);
        });

        /** redirect to author through image **/
        $('#category-feed').on('click', '.image-container > a > img', function (event) {
            let dataset = getRecipeLocationFromParent(this.parentElement);
            let order_in_Grid = dataset.dataset.order;

            eventCallback('', 'עמוד קטגוריה', 'בחירה בשף', foodyGlobals['title'], ' מיקום', order_in_Grid, 'תמונה', foodyGlobals['title']);
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
            eventCallback('', 'עמוד קטגוריה', 'עוד מתכונים', foodyGlobals['title'], '', '', '', foodyGlobals['title']);
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
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', recipe_order_location, itemCategory = '') {

    /**
     * Recipe name
     */
    let recipe_name = '';

    /**
     * Item category
     */
    let item_category = itemCategory;

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
