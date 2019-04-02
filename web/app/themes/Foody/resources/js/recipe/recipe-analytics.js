/**
 * Created by bencohen on 2/4/19.
 */


jQuery(document).ready(($) => {

    /**
     * Add/Remove favorite recipe
     */
    if (jQuery('.recipe-details .favorite-container')) {
        let favButton = jQuery('.recipe-details .favorite-container').find('.favorite .icon-heart');
        if (favButton) {
            favButton.on("click", null, function () {
                let isFav = jQuery(this).hasClass('icon-favorite-pressed');
                if (isFav) {
                    eventCallback(event, 'מתכון', 'הסרה ממועדפים', '', '', '');
                } else {
                    eventCallback(event, 'מתכון', 'הוספה למועדפים', '', '', '');
                }
            });
        }
    }

    /**
     * Rating
     */
    let ratings = jQuery('.post-ratings');
    ratings.delegate('img', 'click', function () {
        let ratingValue = this.id.charAt(this.id.length - 1);
        eventCallback(event, 'מתכון', 'דירוג מתכון', '', 'ציון', ratingValue);
    });

    /**
     * Social shares
     */
    let socialShareList = jQuery('.details-container .social').find('ul');
    socialShareList.delegate('li', 'click', function () {
        let sharingPlatform = this.className.substring(this.className.lastIndexOf('_') + 1, this.className.lastIndexOf(' '));
        eventCallback(event, 'מתכון', 'שיתוף', sharingPlatform);
    });

    /**
     * On num of dishes number change
     */
    if (jQuery('#number-of-dishes').length) {
        jQuery('#number-of-dishes').on("change", null, function () {
            eventCallback(event, 'מתכון', 'שינוי מספר מנות', this.defaultValue, 'מספר מנות', this.value);
        });
    }

    /**
     * Related recipes chosen by name
     */
    let relatedRecipes = jQuery('#main .related-content-container .related-recipes .related-item');
    relatedRecipes.each((index, relatedRecipe)=> {
        jQuery(relatedRecipe).find('.post-title a').click(() => {
            let recipeName = this.innerText.trim();
            let position = $('.details .post-title a').index(this)
            eventCallback(event, 'מתכון', 'בחירת מתכון נוסף', recipeName, 'מיקום', position);
        });

        jQuery(relatedRecipe).find('a .image-container').click(() => {
            let recipeName = jQuery(this).parent().parent().find('.details .post-title a')[0].innerText;
            // let position = jQuery(this).parent().parent().index() + 1;
            let position = $('a .image-container').index(this)
            eventCallback(event, 'מתכון', 'בחירת מתכון נוסף', recipeName, 'מיקום', position);
        });
    });

    /**
     * Clicked categories widget
     */
    let categoriesHeader = jQuery('#main .sidebar-section');
    categoriesHeader.on('click', null, function () {
        if (jQuery('#categoriesHeader-widget-accordion').is(":hidden")) {
            eventCallback(event, 'מתכון', 'פתיחת תפריט קטגוריות')
        }
    });

    /**
     * Category click
     */
    let categoriesList = jQuery(document.getElementsByClassName("category-accordion-item"));
    categoriesList.on('click', null, function () {
        let catName = jQuery(this).find('a')[0].innerText;
        eventCallback(event, 'מתכון', 'מעבר לקטגוריה', catName, 'מיקום', 'תפריט ימין')
    })
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
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '') {

    /**
     * Recipe name
     */
    let recipe_name = foodyGlobals['title'];

    /**
     * Item category
     */
    let item_category = '';

    /**
     * Chef Name
     */
    let chef = foodyGlobals['author_name'];

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

q    /**
     * Difficulty Level
     */
    let difficulty_level = '';
    if (jQuery('.recipe-overview .difficulty_level').length) {
        difficulty_level = jQuery('.recipe-overview .difficulty_level')[0].innerText;
    }

    /**
     * Preparation Time
     */
    let preparation_time = 0;
    if (jQuery('.recipe-overview .preparation_time').length) {
        preparation_time = jQuery('.recipe-overview .preparation_time')[0].innerText;
    }

    /**
     * Ingredients Count
     */
    let ingredients_amount = 0;
    if (jQuery('.recipe-overview .ingredients_count').length) {
        ingredients_amount = jQuery('.recipe-overview .ingredients_count')[0].innerText;
    }

    /**
     * TODO: I Don't know!
     */
    let order_location = 0;//TODO: Don't know

    /**
     * Recipe view count
     */
    let amount = foodyGlobals['view_count'];

    /**
     * Current time - formatted MM:SS
     */
    let d = new Date();
    let date = d.getMinutes() + ':' + d.getSeconds();

    /**
     * Has rich content - does contains video or product buy option
     */
    let hasRichContent = foodyGlobals['has_video'] ? foodyGlobals['has_video'] : false;

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
        '',
        date,
        hasRichContent,
        cdDesc,
        cdValue,
        ''
    );
}