/**
 * Created by bencohen on 2/4/19.
 */


jQuery(document).ready(($) => {


    let favButton = jQuery('.recipe-details .favorite-container').find('i');
    favButton.on("click", null, function () {
        let isFav = jQuery(this).hasClass('icon-favorite-pressed');
        if (isFav) {
            eventCallback(event, 'מתכון', 'הסרה ממועדפים', '');
        } else {
            eventCallback(event, 'מתכון', 'הוספה למועדפים', '');
        }
    });


    let ratings = jQuery('.post-ratings');
    ratings.delegate('img', 'click', function () {
        let ratingValue = this.id.charAt(this.id.length - 1);
        eventCallback(event, 'מתכון', 'דירוג מתכון', '', 'ציון', ratingValue);
    });


    let socialShareList = jQuery('.details-container .social').find('ul');
    socialShareList.delegate('li', 'click', function () {
        let sharingPlatform = this.className.substring(this.className.lastIndexOf('_') + 1, this.className.lastIndexOf(' '));
        eventCallback(event, 'מתכון', 'שיתוף', sharingPlatform);
    });

    let numOfDishes = jQuery('#number-of-dishes');
    let defaultNumOfDishes = numOfDishes && numOfDishes.length && numOfDishes[0].defaultValue;
    numOfDishes.on("change", null, function () {
        eventCallback(event, 'מתכון', 'שינוי מספר מנות', defaultNumOfDishes, 'מספר מנות', this.value);
    });


    let relatedRecipes = jQuery('#main .related-content-container .related-recipes');
    relatedRecipes.delegate('.details a', 'click', function () {
        let recipeName = this.innerText.trim();
        let position = jQuery(this).parent().parent().parent().index() + 1;
        eventCallback(event, 'מתכון', 'בחירת מתכון נוסף', recipeName, 'מיקום', position);
    });

    relatedRecipes.delegate('a .image-container', 'click', function () {
        let recipeName = jQuery(this).parent().parent().find('.details .post-title a')[0].innerText;
        let position = jQuery(this).parent().parent().index() + 1;
        eventCallback(event, 'מתכון', 'בחירת מתכון נוסף', recipeName, 'מיקום', position);
    });

    let categoriesHeader = jQuery('#main .sidebar-section');
    categoriesHeader.on('click', null, function () {
        if (jQuery('#categoriesHeader-widget-accordion').is(":hidden")) {
            eventCallback(event, 'מתכון', 'פתיחת תפריט קטגוריות')
        }
    });

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

    tagManager.pushDataLayer(
        category,
        action,
        label,
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        cdDesc,
        cdValue,
        ''
    );
}