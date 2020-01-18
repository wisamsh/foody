/**
 * Created by moveosoftware on 10/29/18.
 */
jQuery(document).ready(($) => {
    let newsletterForm = jQuery(".site-footer .newsletter .wpcf7-form");
    let isBannerPressed = false;

    newsletterForm.on('wpcf7:submit', null, (event) => {
        eventCallback(event, foodyGlobals['type'], 'לחיצה לרישום לדיוור', '', 'מיקום', 'פוטר');
    });

    let key_for_image = ($('#newsletter-modal .modal-content > .modal-body .popup-image img').length) ? $('#newsletter-modal .modal-content > .modal-body .popup-image img')[0].alt : '';

    /** newsletter popup - load popup **/
    $('#newsletter-modal').on('show.bs.modal', function () {
        eventCallback('', foodyGlobals['type'], 'טעינת פופאפ רישום לדיוור', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן')
    });

    /** closing the popup by pressing outside of it **/
    if ($('#newsletter-modal').hasClass('show')) {
        $('body').click(function (event) {
            let selector = $('#newsletter-modal,  #newsletter-modal *');
            if (!$(event.target).is(selector)) {
                eventCallback('', foodyGlobals['type'], 'סגירת פופ אפ ללא רישום', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן')
            }
        });
    }

    /** closing the popup by pressing the close button (X) **/
    $('#newsletter-modal .close').on('click', function () {
        eventCallback('', foodyGlobals['type'], 'סגירת פופ אפ ללא רישום', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן')
    });

    /** press submit on the popup **/
    $('#newsletter-modal .wpcf7-form .wpcf7-submit').on('click', function () {
        let isEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if ((typeof $('#newsletter-modal .wpcf7-form .wpcf7-email')[0] != "undefined") &&
            ($('#newsletter-modal .wpcf7-form .wpcf7-email')[0].value != '') &&
            (isEmail.test($('#newsletter-modal .wpcf7-form .wpcf7-email')[0].value))) {
            eventCallback('', foodyGlobals['type'], 'לחיצה לאישור רישום לדיוור', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן');
        }
    });

    /** banner popup - load popup **/
    $('#popup-banner').on('shown.bs.modal', function () {
        let bannerName = $('#popup-banner .modal-content').attr('data-banner-name');
        let publisherName = $('#popup-banner .modal-content').attr('data-banner-publisher');
        eventCallback('', foodyGlobals['type'], 'טעינת באנר קידום', bannerName, 'מפרסם', publisherName, 'באנר קידום');
    });

    /** banner popup - closed without submitting **/
    $('footer #popup-banner .close').on('click', function () {
        if (!isBannerPressed) {
            let bannerName = $('#popup-banner .modal-content').attr('data-banner-name');
            let publisherName = $('#popup-banner .modal-content').attr('data-banner-publisher');
            eventCallback('', foodyGlobals['type'], 'סגירת באנר קידום', bannerName, 'מפרסם', publisherName, 'באנר קידום');
        }
    });

    /** banner popup - clicking on submitting **/
    $('#popup-banner .banner-button').on('click', function () {
        isBannerPressed = true;
        let banner_link = $(this).attr('href');
        let bannerName = $('#popup-banner .modal-content').attr('data-banner-name');
        let publisherName = $('#popup-banner .modal-content').attr('data-banner-publisher');
        if (banner_link.toLowerCase().indexOf('foody') >= 0) {
            eventCallback('', foodyGlobals['type'], 'הקלקה על באנר (הפניה פנימה)', bannerName, 'מפרסם', publisherName, 'באנר קידום');
        } else {
            eventCallback('', foodyGlobals['type'], 'הקלקה על באנר (הפניה החוצה)', bannerName, 'מפרסם', publisherName, 'באנר קידום');
        }
    });
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
function eventCallback(event, category, action, label, cdDesc, cdValue, _object = '') {

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';

    let itemCategory = foodyGlobals.title ? foodyGlobals.title : '';

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        '',
        itemCategory,
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        cdDesc,
        cdValue,
        '',
        _object
    );
}