/**
 * Created by bencohen on 2/4/19.
 */


jQuery(document).ready(($) => {
    if (foodyGlobals.post && (foodyGlobals.post.type == 'foody_recipe')) {

        // Add to recipes visited in session count
        set_recipe_order_location(foodyGlobals.ID);

        var publishers = ['אין'];
        if ($('.sponsors-container .sponsored-by.company div').length) {
            publishers = [];
            $('.sponsors-container .sponsored-by.company div').each((index, elem) => {
                publishers.push(elem.innerHTML);
            });
        }
        /**
         * Page Load
         */
        if (foodyGlobals['post']['categories']) {
            categoriesHits(publishers);
        }
        /**
         * Breadcrumbs click
         */
        let breadcrumbs = jQuery('.details-container .breadcrumb');
        breadcrumbs.delegate('li', 'click', function (event) {
            let breadcrumb = jQuery(this).find('a').text().trim();
            eventCallback(event, 'מתכון', 'מעבר לקטגוריה', breadcrumb, 'מיקום', 'פירורי לחם', get_recipe_order_location());
        });

        /**
         * Add/Remove favorite recipe
         */
        if (jQuery('.recipe-details .favorite-container')) {
            let favButton = jQuery('.recipe-details .favorite-container').find('.favorite .icon-heart');
            if (favButton) {
                favButton.on("click", null, function (event) {
                    let isFav = jQuery(this).hasClass('icon-favorite-pressed');
                    if (isFav) {
                        eventCallback(event, 'מתכון', 'הסרה ממועדפים', '', '', '', get_recipe_order_location());
                    } else {
                        eventCallback(event, 'מתכון', 'הוספה למועדפים', '', '', '', get_recipe_order_location());
                    }
                });
            }
        }

        /**
         * Rating
         */
        let ratings = jQuery('.post-ratings');
        ratings.delegate('img', 'click', function (event) {
            let ratingValue = this.id.charAt(this.id.length - 1);
            eventCallback(event, 'מתכון', 'דירוג מתכון', '', 'ציון', ratingValue, get_recipe_order_location());
        });

        /**
         * Social shares
         */
        let socialShareList = jQuery('.details-container .social .essb_links').find('ul');
        socialShareList.delegate('li', 'click', function (event) {
            let sharingPlatform = this.className.substring(this.className.lastIndexOf('_') + 1, this.className.lastIndexOf(' '));
            eventCallback(event, 'מתכון', 'שיתוף', sharingPlatform, get_recipe_order_location());
        });

        /**
         * On num of dishes number change
         */
        if (jQuery('#number-of-dishes').length) {
            jQuery('#number-of-dishes').on("change", null, function (event) {
                eventCallback(event, 'מתכון', 'שינוי מספר מנות', this.defaultValue, 'מספר מנות', this.value, get_recipe_order_location());
            });
        }


        /**
         * Related recipes chosen by name
         */
        let relatedRecipes = jQuery('#main .related-content-container .related-recipes .related-item');
        relatedRecipes.each((index, relatedRecipe) => {
            jQuery(relatedRecipe).find('.post-title a').click((event) => {
                let recipeName = jQuery(event.target).text().trim();
                let position = $('.details .post-title a').index(event.target);
                eventCallback(event, 'מתכון', 'בחירת מתכון נוסף', recipeName, 'מיקום', position, get_recipe_order_location());
            });

            jQuery(relatedRecipe).find('a .image-container').click((event) => {
                let recipeName = jQuery(event.target).parent().parent().find('.details .post-title a').text().trim();
                // let position = jQuery(this).parent().parent().index() + 1;
                let position = $('a .image-container').index(event.target);
                eventCallback(event, 'מתכון', 'בחירת מתכון נוסף', recipeName, 'מיקום', position, get_recipe_order_location());
            });
        });

        /**
         * Clicked categories widget
         */
        let categoriesHeader = jQuery('#main .sidebar-section');
        categoriesHeader.on('click', null, function (event) {
            if (jQuery('#categoriesHeader-widget-accordion').is(":hidden")) {
                eventCallback(event, 'מתכון', 'פתיחת תפריט קטגוריות', get_recipe_order_location());
            }
        });

        /**
         * Side bar category click
         */
        let sideCategoriesList = jQuery(document.getElementsByClassName("category-accordion-item"));
        sideCategoriesList.on('click', null, function (event) {
            let catName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'מתכון', 'מעבר לקטגוריה', catName, 'מיקום', 'תפריט ימין', get_recipe_order_location());
        });

        /**
         * Bottom category click
         */
        let bottomCategories = jQuery('.recipe-categories .post-categories');
        bottomCategories.delegate('li', 'click', function (event) {
            let catName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'מתכון', 'מעבר לקטגוריה', catName, 'מיקום', 'פוטר', get_recipe_order_location());
        });

        /**
         * Bottom tags click
         */
        let bottomTags = jQuery('.recipe-tags .post-tags');
        bottomTags.delegate('li', 'click', function (event) {
            let tagName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'מתכון', 'לחיצה על תגיות', tagName, 'מיקום', 'פוטר', get_recipe_order_location());
        });

        /**
         * Newsletter registration
         */
        let newsletterSubmitBtn = jQuery('.content .wpcf7 > form')
        newsletterSubmitBtn.submit((event) => {
            eventCallback(event, 'מתכון', 'לחיצה על רישום לדיוור', foodyGlobals['title'], 'מיקום', 'פוטר', get_recipe_order_location());
            if (foodyGlobals['post']['categories']) {
                categoriesHits(publishers);
            }
        });

        /**
         * Add photo button
         */
        let addImage = jQuery('#image-upload-hidden');
        addImage.click((event) => {
            if (event.target.id !== 'attachment') {
                eventCallback(event, 'מתכון', 'לחיצה על מצלמה', '', 'מיקום', 'פוטר', get_recipe_order_location());
            }
        });

        /**
         * Add photo approve button
         */
        let apprvoeAddingimage = jQuery('#image-upload-form > button.btn.btn-primary.btn-approve');
        apprvoeAddingimage.click((event) => {
            eventCallback(event, 'מתכון', 'העלאת תמונה', '', 'מיקום', 'פוטר', get_recipe_order_location());
        });

        /**
         * Add comment
         */
        let addCommentBtn = jQuery('#submit');
        addCommentBtn.click((event) => {
            eventCallback(event, 'מתכון', 'הוספת תגובה', '', 'מיקום', 'פוטר', get_recipe_order_location());
        });

        /**
         * Purchase buttons
         */
        let purchaseBtn = jQuery(document.getElementsByClassName('purchase-button-container'));
        purchaseBtn.delegate('a', 'click', function (event) {
            let analyticsLabel = $(this).parent().attr('data-analytics');
            if (!analyticsLabel) {
                analyticsLabel = this.innerText;
            }
            eventCallback(event, 'מתכון', 'לחיצה לרכישה', analyticsLabel, 'מיקום', 'עליון', get_recipe_order_location());
        });

        /**
         * Scroll listener
         */
        $(window).scroll(function (e) {
            const scrollTop = $(window).scrollTop();
            const docHeight = $(document).height();
            const winHeight = $(window).height();
            const scrollPercent = (scrollTop) / (docHeight - winHeight);
            const scrollPercentRounded = Math.round(scrollPercent * 100);
            let toLog = false;
            if (scrollPercentRounded === 0 || scrollPercentRounded === 25 ||
                scrollPercentRounded === 50 || scrollPercentRounded === 75 || scrollPercentRounded === 100) {
                toLog = true;
            }
            if (toLog) {
                eventCallback(event, 'מתכון', 'גלילה', scrollPercentRounded + '%', '', '', get_recipe_order_location());

            }
        });

        /**
         * Register to newsletter footer
         */
        let newsletterRegisterBtn = $('footer .newsletter .wpcf7');
        newsletterRegisterBtn.submit((event) => {
            eventCallback(event, 'מתכון', 'לחיצה על רישום לדיוור', '', 'מיקום', 'פוטר');
        });

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
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', recipe_order_location, itemCategory = '') {

    /**
     * Recipe name
     */
    let recipe_name = foodyGlobals['title'];

    /**
     * Item category
     */
    let item_category = itemCategory;

    /**
     * Chef Name
     */
    let chef = foodyGlobals['author_name'];

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    /**
     * Difficulty Level
     */
    let difficulty_level = '';
    if (jQuery('.recipe-overview .difficulty_level').length) {
        difficulty_level = jQuery('.recipe-overview .difficulty_level').text().trim();
    }

    /**
     * Preparation Time
     */
    let preparation_time = 0;
    if (jQuery('.recipe-overview .preparation_time').length) {
        preparation_time = jQuery('.recipe-overview .preparation_time').text().trim();
    }

    /**
     * Ingredients Count
     */
    let ingredients_amount = 0;
    if (jQuery('.recipe-overview .ingredients_count').length) {
        ingredients_amount = jQuery('.recipe-overview .ingredients_count').text().trim();
    }

    /**
     * Index of recipe in current Session
     */
    let order_location = recipe_order_location;

    /**
     * Recipe view count
     */
    let amount = foodyGlobals['view_count'];

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
        hasRichContent,
        cdDesc,
        cdValue,
        ''
    );
}

function get_recipe_order_location() {
    let recipes_visited = JSON.parse(sessionStorage.getItem('recipes_visited'));

    if (!recipes_visited) {
        return 0;
    }

    return recipes_visited.length;
}

function set_recipe_order_location(recipe_id) {
    let recipes_visited = JSON.parse(sessionStorage.getItem('recipes_visited'));

    if (!recipes_visited) {
        recipes_visited = [];
    }

    if (!recipes_visited.includes(recipe_id)) {
        recipes_visited.push(recipe_id);
    }

    sessionStorage.setItem('recipes_visited', JSON.stringify(recipes_visited));
}

function categoriesHits(publishers) {
    let primaryCategory = $('.breadcrumb > li').last()[0].innerText;
    eventCallback(null, 'מתכון', 'טעינה', 'קטגוריה ראשית', 'מפרסם', publishers.join(', '), get_recipe_order_location(), primaryCategory);
    foodyGlobals['post']['categories'].forEach((category) => {
        if (category.name != primaryCategory) {
            eventCallback(null, 'מתכון', 'טעינה', 'קטגוריות נוספות', 'מפרסם', publishers.join(', '), get_recipe_order_location(), category.name);
        }
    });
}