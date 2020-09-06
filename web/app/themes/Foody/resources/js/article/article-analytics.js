/**
 * Created by bencohen on 2/4/19.
 */


jQuery(document).ready(($) => {
    if (foodyGlobals.post && (foodyGlobals.post.type == 'post')) {
        let scrollsArr = {'0': false, '25': false, '50': false, '75': false, '100': false};
        let feedPublisher = "אין";
        let primaryCategory = $('.breadcrumb > li').last()[0].innerText;

        var publishers = ['אין'];
        if (foodyGlobals['post']['publisher'] || $('.sponsors-container').length) {
            publishers = [];
        }
        if (foodyGlobals['post']['publisher']) {
            feedPublisher = foodyGlobals['post']['publisher'];
            //publishers.push(publisher);
        }

        /**
         * Page Load
         */
        if (feedPublisher == "") {
            eventCallback(null, 'כתבה', 'טעינה', 'קטגוריה ראשית', 'מפרסם', publishers.join(', '), '', primaryCategory);
        }
        else{
            eventCallback(null, 'כתבה', 'טעינה', 'קטגוריה ראשית', 'מפרסם',feedPublisher, '', primaryCategory);
        }


        /**
         * Breadcrumbs click
         */
        let breadcrumbs = jQuery('.details-container .breadcrumb');
        breadcrumbs.delegate('li', 'click', function (event) {
            let breadcrumb = jQuery(this).find('a').text().trim();
            eventCallback(event, 'כתבה', 'מעבר למדור');//TODO: article said to no have the following: , breadcrumb, 'מיקום', 'פירורי לחם');
        });

        /**
         * Rating
         */
        let ratings = jQuery('.post-ratings');
        ratings.delegate('img', 'click', function (event) {
            let ratingValue = this.id.charAt(this.id.length - 1);
            eventCallback(event, 'כתבה', 'דירוג כתבה');//TODO: article said to no have the following: , '', 'ציון', ratingValue);
        });

        /**
         * Social shares
         */
        let socialShareList = jQuery('.details-container .social .essb_links').find('ul');
        socialShareList.delegate('li', 'click', function (event) {
            let sharingPlatform = this.className.substring(this.className.lastIndexOf('_') + 1, this.className.lastIndexOf(' '));
            eventCallback(event, 'כתבה', 'שיתוף', sharingPlatform);
        });

        /**
         * Bottom category click
         */
        let bottomCategories = jQuery('.categories .post-categories');
        bottomCategories.delegate('li', 'click', function (event) {
            let catName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'כתבה', 'מעבר לקטגוריה', catName, 'מיקום', 'פוטר');
        });

        /**
         * Bottom tags click
         */
        let bottomTags = jQuery('.tags .post-tags');
        bottomTags.delegate('li', 'click', function (event) {
            let tagName = jQuery(this).find('a').text().trim();
            eventCallback(event, 'כתבה', 'לחיצה על תגיות', tagName, 'מיקום', 'פוטר');
        });

        /**
         * Newsletter registration
         */
        let newsletterSubmitBtn = jQuery('.content .wpcf7 > form')
        newsletterSubmitBtn.submit((event) => {
            //TODO: Notice the use of foodyGlobals should probably be implemented
            eventCallback(event, 'כתבה', 'לחיצה על רישום לדיוור', foodyGlobals['title'], 'מיקום', 'פוטר');
        });

        /**
         * Add comment
         */
        let addCommentBtn = jQuery('#submit');
        addCommentBtn.click((event) => {
            eventCallback(event, 'כתבה', 'הוספת תגובה', '', 'מיקום', 'פוטר');
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
                if(!scrollsArr[scrollPercentRounded]) {
                    eventCallback(event, 'כתבה', 'גלילה', scrollPercentRounded + '%', '', '');
                    scrollsArr[scrollPercentRounded] = true;
                }
            }
        });

        /**
        * Register to newsletter footer
         */
        let newsletterRegisterBtn = $('footer .newsletter .wpcf7');
        newsletterRegisterBtn.submit((event)=>{
            eventCallback(event,'כתבה', 'לחיצה על רישום לדיוור', '', 'מיקום', 'פוטר');
        });

        /**
         * Purchase button clicked
         */
        // let purchaseBtn = $('.purchase-buttons .purchase-button-container a');
        // purchaseBtn.click((event)=>{
        //     eventCallback(event,'כתבה', 'לחיצה לרכישה', '', '', '');
        // });

        /**
         * Purchase buttons
         */
        let purchaseBtn = jQuery(document.getElementsByClassName('purchase-button-container'));
        purchaseBtn.delegate('a', 'click', function (event) {
            let analyticsLabel = $(this).parent().attr('data-analytics');
            if (!analyticsLabel) {
                analyticsLabel = this.innerText;
            }
            eventCallback(event, 'מתכון', 'לחיצה לרכישה', analyticsLabel, 'מפרסם', feedPublisher,'','');
            // nonInteraction = false;
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
                eventCallback(event, 'כתבה', 'לחיצה על לינק בתוכן', domainName, 'טקסט על הקישור', text, '', 'תמונה');
            } else {
                text = $(this)[0].innerHTML;
                eventCallback(event, 'כתבה', 'לחיצה על לינק בתוכן', domainName, 'טקסט על הקישור', text, '', 'קישור');
            }
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
 */
function eventCallback(event, category, action, label = '', cdDesc = '', cdValue = '', object = '', item_category) {

    /**
     * Recipe name
     */
    let recipe_name = foodyGlobals['title'];

    /**
     * Item category
     */
    let _item_category = item_category;

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
     * TODO: I Don't know!
     */
    let order_location = 0;//TODO: Don't know

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
        object
    );
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