/**
 * Created by bencohen on 2/4/19.
 */
//.sponsored-by.company div

jQuery(document).ready(($) => {
    if (foodyGlobals.post && (foodyGlobals.post.type == 'foody_recipe')) {
        let redTitlesList = [];
        // let redTitlesAppeared = [];
        let timeInPageDelta = 60;
        let secondsInPage = 0;
        let feedPublisher = "אין";
        let scrollsArr = {'0': false, '25': false, '50': false, '75': false, '100': false};
        let mainCategoriesList = {
            "קינוחים": "קינוחים",
            "עוגות": "עוגות",
            "קציצות": "קציצות",
            "אוכל שילדים אוהבים": "אוכל שילדים אוהבים",
            "עוגיות": "עוגיות",
            "מתכוני חגים": "מתכוני חגים",
            "מתכונים ב-5 דקות": "מתכונים ב-5 דקות",
            "מתכוני עוף": "מתכוני עוף",
            "מרקים": "מרקים",
            "מתכונים טבעוניים": "מתכונים טבעוניים",
            "קינוחים טבעוניים": "קינוחים טבעוניים",
            "מתכונים ללא גלוטן": "מתכונים ללא גלוטן",
            "קינוחים ללא גלוטן": "קינוחים ללא גלוטן",
            "לחם": "לחם",
            "לחמניות": "לחמניות",
            "סלטים": "סלטים",
            "צמחוני": "צמחוני",
            "קינוחי שוקולד": "קינוחי שוקולד",
            "דגים": "דגים",
            "ארוחות ערב": "ארוחות ערב",
            "ארוחות צהריים": "ארוחות צהריים",
            "בראנץ'": "בראנץ'",
        };
        let nonInteraction = true;
        let isTwoMinuets = false;


        // Add to recipes visited in session count
        set_recipe_order_location(foodyGlobals.ID);

        $('h2').each(function (index) {
            if($($('h2')[index]).css('color') == 'rgb(237, 61, 72)'){
                if($($('h2')[index]).length) {
                    redTitlesList[$($('h2')[index])[0].innerText] = $($('h2')[index]);
                    // redTitlesAppeared[$($('h2')[index])[0].innerText] = false;
                }
            }
        });

        var publishers = ['אין'];
        if (foodyGlobals['post']['publisher'] || $('.sponsors-container').length) {
            publishers = [];
        }
        if (foodyGlobals['post']['publisher']) {
            feedPublisher = foodyGlobals['post']['publisher'];
            //publishers.push(publisher);
        } else if(foodyGlobals['channel_publisher_name']){
            feedPublisher = foodyGlobals['channel_publisher_name'];
        }

        if ($('.sponsors-container').length) {
            let sponsors = $('.sponsors-container');
            for (let i = 0; i < sponsors.length; i++) {
                let topOfHierarchy = sponsors[i].children[sponsors[i].children.length - 1];
                if ($(topOfHierarchy).length && topOfHierarchy.innerText != '') {
                    if ($.inArray(topOfHierarchy.innerText, publishers) < 0) {
                        publishers.push(topOfHierarchy.innerText);
                    }
                }
            }
        }

        /**
         * Page Load
         */
        if (foodyGlobals['post']['categories']) {
            categoriesHits(publishers, feedPublisher, mainCategoriesList);
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
                        nonInteraction = false;
                    } else {
                        eventCallback(event, 'מתכון', 'הוספה למועדפים', '', '', '', get_recipe_order_location());
                        nonInteraction = false;
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
            nonInteraction = false;
        });

        /**
         * Social shares
         */
        let socialShareList = jQuery('.details-container .social .essb_links').find('ul');
        socialShareList.delegate('li', 'click', function (event) {
            let sharingPlatform = this.className.substring(this.className.lastIndexOf('_') + 1, this.className.lastIndexOf(' '));
            eventCallback(event, 'מתכון', 'שיתוף', sharingPlatform, get_recipe_order_location());
            nonInteraction = false;
        });

        /**
         * On num of dishes number change
         */
        if (jQuery('#number-of-dishes').length) {
            jQuery('#number-of-dishes').on("change", null, function (event) {
                eventCallback(event, 'מתכון', 'שינוי מספר מנות', this.defaultValue, 'מספר מנות', this.value, get_recipe_order_location());
                nonInteraction = false;
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
                nonInteraction = false;
            });

            jQuery(relatedRecipe).find('a .image-container').click((event) => {
                let recipeName = jQuery(event.target).parent().parent().find('.details .post-title a').text().trim();
                // let position = jQuery(this).parent().parent().index() + 1;
                let position = $('a .image-container').index(event.target);
                eventCallback(event, 'מתכון', 'בחירת מתכון נוסף', recipeName, 'מיקום', position, get_recipe_order_location());
                nonInteraction = false;
            });
        });

        /**
         * Clicked categories widget
         */
        let categoriesHeader = jQuery('#main .sidebar-section');
        categoriesHeader.on('click', null, function (event) {
            if (jQuery('#categoriesHeader-widget-accordion').is(":hidden")) {
                eventCallback(event, 'מתכון', 'פתיחת תפריט קטגוריות', get_recipe_order_location());
                nonInteraction = false;
            }
        });

        /**
         * Side bar category click
         */
        let sideCategoriesList = jQuery(document.getElementsByClassName("category-accordion-item"));
        sideCategoriesList.on('click', null, function (event) {
            let catName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'מתכון', 'מעבר לקטגוריה', catName, 'מיקום', 'תפריט ימין', get_recipe_order_location());
            nonInteraction = false;
        });

        /**
         * Bottom category click
         */
        let bottomCategories = jQuery('.recipe-categories .post-categories');
        bottomCategories.delegate('li', 'click', function (event) {
            let catName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'מתכון', 'מעבר לקטגוריה', catName, 'מיקום', 'פוטר', get_recipe_order_location());
            nonInteraction = false;
        });

        /**
         * Bottom tags click
         */
        let bottomTags = jQuery('.recipe-tags .post-tags');
        bottomTags.delegate('li', 'click', function (event) {
            let tagName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'מתכון', 'לחיצה על תגיות', tagName, 'מיקום', 'פוטר', get_recipe_order_location());
            nonInteraction = false;
        });

        /**
         * Newsletter registration
         */
        let newsletterSubmitBtn = jQuery('.content .wpcf7 > form')
        newsletterSubmitBtn.submit((event) => {
            eventCallback(event, 'מתכון', 'לחיצה על רישום לדיוור', foodyGlobals['title'], 'מיקום', 'פוטר', get_recipe_order_location());
            nonInteraction = false;
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
                nonInteraction = false;
            }
        });

        /**
         * Add photo approve button
         */
        let apprvoeAddingimage = jQuery('#image-upload-form > button.btn.btn-primary.btn-approve');
        apprvoeAddingimage.click((event) => {
            eventCallback(event, 'מתכון', 'העלאת תמונה', '', 'מיקום', 'פוטר', get_recipe_order_location());
            nonInteraction = false;
        });

        /**
         * Add comment
         */
        let addCommentBtn = jQuery('#submit');
        addCommentBtn.click((event) => {
            eventCallback(event, 'מתכון', 'הוספת תגובה', '', 'מיקום', 'פוטר', get_recipe_order_location());
            nonInteraction = false;
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
            eventCallback(event, 'מתכון', 'לחיצה לרכישה', analyticsLabel, 'מפרסם', feedPublisher, get_recipe_order_location(),'');
            nonInteraction = false;
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

            for(let title in redTitlesList){
                if ($(redTitlesList[title]).isInViewport()){
                    eventCallback(e, 'מתכון', 'חשיפת רכיב',  title);
                    delete redTitlesList[title];
                }
            }

            let toLog = false;
            if (scrollPercentRounded === 0 || scrollPercentRounded === 25 ||
                scrollPercentRounded === 50 || scrollPercentRounded === 75 || scrollPercentRounded === 100) {
                toLog = true;
            }
            if (toLog) {
                if (!scrollsArr[scrollPercentRounded]) {
                    eventCallback(e, 'מתכון', 'גלילה', scrollPercentRounded + '%', '', '', get_recipe_order_location(), '', '', '', '', isNonInteraction(scrollsArr,isTwoMinuets,nonInteraction));
                    scrollsArr[scrollPercentRounded] = true;
                }
            }
        });

        /**
         * Register to newsletter footer
         */
        let newsletterRegisterBtn = $('footer .newsletter .wpcf7');
        newsletterRegisterBtn.submit((event) => {
            eventCallback(event, 'מתכון', 'לחיצה על רישום לדיוור', '', 'מיקום', 'פוטר');
            nonInteraction = false;
        });

        /**
         * click on link from the content
         */
        $('.post-content-link').on('click', function () {
            let text = '';
            let linkURL = $(this).attr("href");
            let domainName = get_hostname(linkURL);
            let imageContainer = $(this).has('img');
            if (imageContainer.length) {
                text = $(imageContainer[0].children[0]).attr('alt');
                eventCallback(event, 'מתכון', 'לחיצה על לינק בתוכן', domainName, 'טקסט על הקישור', text, '', '', 'תמונה');
                nonInteraction = false;
            } else {
                text = $(this)[0].innerHTML;
                eventCallback(event, 'מתכון', 'לחיצה על לינק בתוכן', domainName, 'טקסט על הקישור', text, '', '', 'קישור');
                nonInteraction = false;
            }
        });

        /**
         * clicked on commercial ingredient
         */
        $('.sponsored-by a').on('click', function () {
            let linkName = $(this).closest('.ingredients').find('.foody-u-link')[0].innerText;
            eventCallback(event, 'מתכון', 'לחיצה על קידום מצרכים', linkName, 'מפרסם', feedPublisher, '', '', '', 'יש קידום עם קישור');
            nonInteraction = false;
        });

        /**
         * clicked on ingredient
         */
        $('.ingredient-data .foody-u-link').on('click', function () {
            let ingredientName = this.innerText;
            let ingredientLink = $(this).attr('href');
            let elementParent = $(this).closest('.ingredients');
            let ingredientsPromotion = '';

            if (elementParent.find('.sponsors-container').length) {
                let sponsoredElement = elementParent.find('.sponsored-by');
                let sponsoredElementLink = sponsoredElement.find('a');
                if (sponsoredElementLink.length) {
                    ingredientsPromotion = 'יש קידום עם קישור';
                } else {
                    if (sponsoredElement.length) {
                        ingredientsPromotion = 'יש קידום';
                    } else {
                        ingredientsPromotion = 'אין קידום';
                    }
                }
            } else {
                ingredientsPromotion = 'אין קידום';
            }

            if (ingredientLink.toLowerCase().indexOf('utm') < 0 && ingredientLink.toLowerCase().indexOf('foody') >= 0) {
                eventCallback(event, 'מתכון', 'לחיצה על מצרכים (הפניה פנימה)', ingredientName, 'מפרסם', feedPublisher, '', '', '', ingredientsPromotion);
                nonInteraction = false;
            } else if (ingredientLink.toLowerCase().indexOf('utm') >= 0 || ingredientLink.toLowerCase().indexOf('foody') < 0) {
                eventCallback(event, 'מתכון', 'לחיצה על מצרכים (הפניה החוצה)', ingredientName, 'מפרסם', feedPublisher, '', '', '', ingredientsPromotion);
                nonInteraction = false;
            }
        });

        /** substitute ingredient **/
        $('.substitute-ingredient').on('click', function () {
            let substituteIngredientName = $(this).attr('data-name');
            let parentIngredient = $(this).closest('.ingredients');
            let shownIngredientName = (parentIngredient.find('.foody-u-link'))[0].innerText;
            let substituteLinkName = $(this)[0].innerText.replace('החלפה ל', '');

            if (substituteIngredientName == shownIngredientName) {
                // substitute ingredient
                eventCallback(event, 'מתכון', 'החלפת מצרך מקורי', substituteLinkName, 'מפרסם', feedPublisher, '', '', '', '', substituteIngredientName);
                nonInteraction = false;
            } else {
                // return to original
                eventCallback(event, 'מתכון', 'החזרת מצרך מקורי', substituteIngredientName, 'מפרסם', feedPublisher, '', '', '', '',);
                nonInteraction = false;
            }
        });

        /** recipe timer **/
        let interval = setInterval(function () {
            secondsInPage += timeInPageDelta;
            let timerString = toMinutes(secondsInPage);
            eventCallback('', 'מתכון', 'טיימר',timerString , '', '', '','','','','', isNonInteraction(scrollsArr,isTwoMinuets,nonInteraction));
            if(timerString == '2m'){
                isTwoMinuets = true;
            }
            if (timerString == '20m') {
                clearInterval(interval);
            }
        }, timeInPageDelta * 1000);

        /** feed areas cover clicked **/
        if ($('.cover-image a').length) {
            $('.cover-image a').on('click', function () {
                let coverLink = $(this).attr('href');
                let coverName = $(this).find('img').attr('data-name');
                if (coverLink != '') {
                    if (coverLink.toLowerCase().indexOf('utm') < 0 && coverLink.toLowerCase().indexOf('foody') >= 0) {
                        eventCallback('', 'מתכון', 'לחיצה על קאבר (הפניה פנימה)', coverName, ' מפרסם', feedPublisher);
                    } else if (coverLink.toLowerCase().indexOf('utm') >= 0 || coverLink.toLowerCase().indexOf('foody') < 0) {
                        eventCallback('', 'מתחם פידים', 'לחיצה על קאבר (הפניה החוצה)', coverName, ' מפרסם', feedPublisher);
                    }
                }
            });
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
 * @param recipe_order_location
 */
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', recipe_order_location = '', itemCategory = '', object = '', ingredientsPromotion = '', ingredient = '', non_interaction = false) {

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
        '',
        object,
        ingredientsPromotion,
        ingredient,
        non_interaction
    );
}

function get_recipe_order_location() {
    let recipes_visited = JSON.parse(sessionStorage.getItem('recipes_visited'));

    if (!recipes_visited) {
        return 0;
    }

    return jQuery.inArray(foodyGlobals.ID, recipes_visited);
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

function categoriesHits(publishers, feedPublisher, categoriesList) {
    let primaryCategory = $('.breadcrumb > li').last()[0].innerText;
    let secondaryCategoriesString = "";
    let hasSecondaryCategories = false;
    //let techniquesAndAccessoriesString = '';
    let techniquesAndAccessoriesString = getTechniquesAndAccessories();

    let ingredientsPromotion = $('.sponsors-container').length > 0 ? getRelevantIngredientsPromotion(publishers, feedPublisher) : 'אין קידומים';
    if (feedPublisher == "") {
        eventCallback(null, 'מתכון', 'טעינה', 'קטגוריה ראשית', 'מפרסם', publishers.join(', '), get_recipe_order_location(), primaryCategory, '', ingredientsPromotion);
    } else {
        eventCallback(null, 'מתכון', 'טעינה', 'קטגוריה ראשית', 'מפרסם', feedPublisher, get_recipe_order_location(), primaryCategory, '', ingredientsPromotion);
    }
    foodyGlobals['post']['categories'].forEach((category, index, array) => {
        if (category.name != primaryCategory) {
            if (typeof (categoriesList[category.name]) != 'undefined') {
                if (feedPublisher == "") {
                    eventCallback(null, 'מתכון', 'טעינה', 'קטגוריות נוספות', 'מפרסם', publishers.join(', '), get_recipe_order_location(), category.name, '', ingredientsPromotion);
                } else {
                    eventCallback(null, 'מתכון', 'טעינה', 'קטגוריות נוספות', 'מפרסם', feedPublisher, get_recipe_order_location(), category.name, '', ingredientsPromotion);
                }
            } else {
                if (index === (array.length - 1)) {
                    secondaryCategoriesString += category.name;
                    hasSecondaryCategories = true;
                } else {
                    secondaryCategoriesString += category.name + '/';
                }
            }
        }
    });

    if (hasSecondaryCategories) {
        if (techniquesAndAccessoriesString != '') {
            eventCallback(null, 'מתכון', 'טעינה', 'קטגוריות משניות, אביזרים, טכניקות', '', '', '', secondaryCategoriesString + '/' + techniquesAndAccessoriesString, '', ingredientsPromotion);
        } else {
            eventCallback(null, 'מתכון', 'טעינה', 'קטגוריות משניות, אביזרים, טכניקות', '', '', '', secondaryCategoriesString, '', ingredientsPromotion);
        }
    } else {
        if (techniquesAndAccessoriesString != '') {
            eventCallback(null, 'מתכון', 'טעינה', 'קטגוריות משניות, אביזרים, טכניקות', '', '', '', secondaryCategoriesString, '', ingredientsPromotion);
        }
    }

}

function isNonInteraction (scrollsArr, isTwoMinuets, _nonInteraction){
    if (_nonInteraction) {
        let scrollInteraction = false;

        for (let key in scrollsArr) {
            if (key != '0' && scrollsArr[key]) {
                scrollInteraction = true;
                break;
            }
        }

        if (isTwoMinuets && scrollInteraction) {
            return false;
        }
    }
    else{
        return false;
    }
    return true;
}

function getRelevantIngredientsPromotion(publishers, feedPublisher) {
    let hasLinks = $('.sponsored-by a').length > 0;
    let somePromotionsRelatedToPublisher = $.inArray(feedPublisher, publishers) >= 0;
    const promotionWithLink = 'יש קידום עם קישור';
    const promotionWithoutLink = 'יש קידום';
    const noPromotion = 'אין קידום';

    if (hasLinks) {
        return promotionWithLink;
    } else {
        if ($('.sponsored-by').length) {
            return promotionWithoutLink;
        } else {
            return noPromotion;
        }
    }
}

function getTechniquesAndAccessories() {
    let accessoriesList = '';
    let techniquesList = '';
    let hasAccessories = false;

    $('.recipe-accessories li a').each((index, accessory) => {
        if (index === ($('.recipe-accessories li a').length - 1)) {
            accessoriesList += accessory.innerText;
            hasAccessories = true;
        } else {
            accessoriesList += accessory.innerText + '/';
        }
    });

    $('.recipe-techniques li a').each((index, technique) => {
        if (index === ($('.recipe-techniques li a').length - 1)) {
            techniquesList += technique.innerText;
        } else {
            techniquesList += technique.innerText + '/';
        }
    });

    if (hasAccessories) {
        return accessoriesList + '/' + techniquesList;
    } else {
        return techniquesList;
    }
}

function get_hostname(url) {
    var domain = "", page = "";

    //remove "http://"
    if (url.indexOf("http://") == 0) {
        url = url.substr(7);
    }
    //remove "https://"
    if (url.indexOf("https://") == 0) {
        url = url.substr(8);
    }
    //remove "www."
    if (url.indexOf("www.") == 0) {
        url = url.substr(4);
    }
    domain = url.split('/')[0].split('.')[0];

    return domain;
}

function toMinutes(secondsInPage) {
    if (secondsInPage % 60 == 0) {
        return secondsInPage / 60 + 'm';
    } else {
        return (secondsInPage / 60) - 0.5 + 'm' + 30 + 's';
    }
}

$.fn.isInViewport = function() {
    let elementTop = $(this).offset().top;
    let elementBottom = elementTop + $(this).outerHeight();
    let viewportTop = $(window).scrollTop();
    let viewportBottom = viewportTop + $(window).height();
    return elementBottom > viewportTop && elementTop < viewportBottom;
};