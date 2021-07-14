/**
 * Created by moveosoftware on 10/8/18.
 */

let FoodySearchFilter = require('../common/foody-search-filter');
let FoodyContentPaging = require('../common/page-content-paging');
let FoodyLocationUtils = require('../common/foody-location-utils');
let FoodyLoader = require('../common/foody-loader');

let locationUtils = new FoodyLocationUtils();


jQuery(document).ready(($) => {


    if(!foodyGlobals.isMobile){
        foodyAjax({action: 'load_foody_social'}, (err, data) => {
            $('aside.sidebar-desktop  .sidebar-content').append(data);
        });
    }

    let feedContainer = $('.page-template-homepage .feed-container .content-container');
    let loader = new FoodyLoader({container:feedContainer});
    loader.attach();
    foodyAjax({action: 'load_homepage_feed',data:{filter:locationUtils.getQuery('filter')}}, (err, data) => {
        loader.detach();
        $(feedContainer).append(data);
        $('#sort-homepage-feed').selectpicker({dropdownAlignRight: true, style: 'foody-select', dropupAuto: false, width: 'fit'});

        // sidebar filter
        let filter = new FoodySearchFilter({
            selector: '.homepage #accordion-foody-filter',
            grid: '#homepage-feed',
            cols: 2,
            searchButton: '.show-recipes',
            page: '.page-template-homepage',
            context: 'homepage',
            contextArgs: [],
        });

        // search and filter pager
        let pager = new FoodyContentPaging({
            context: 'homepage',
            contextArgs: [],
            filter: filter,
            sort: '#sort-homepage-feed'
        });
    });

    if($('#foody-filter').length){
        $('#foody-filter .md-checkbox input[type="checkbox"]').on('change', function () {
            let isChecked = $(this).is(':checked');
            let filterString = $(this).siblings('label').length ? $(this).siblings('label')[0].innerText : '';
            if(isChecked){
                eventCallback('', 'עמוד הבית','הוספת סינון', filterString, 'סינון', filterString);
            } else {
                eventCallback('', 'עמוד הבית','הסרת סינון', filterString, 'סינון', filterString);
            }
        })
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
 * @param recipe_order_location
 */
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', recipe_order_location, itemCategory = '', recipeDetails = '', _amount = '') {

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
