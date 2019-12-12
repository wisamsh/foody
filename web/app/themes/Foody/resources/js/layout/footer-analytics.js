/**
 * Created by moveosoftware on 10/29/18.
 */
jQuery(document).ready(($) => {
    let newsletterForm = jQuery(".site-footer .newsletter .wpcf7-form");

    newsletterForm.on('wpcf7:submit', null, (event) => {
        eventCallback(event, foodyGlobals['title'], 'לחיצה לרישום לדיוור', '', 'מיקום', 'פוטר');
    });

    let key_for_image = ($('#newsletter-modal .modal-content > .modal-body .popup-image img').length) ? $('#newsletter-modal .modal-content > .modal-body .popup-image img')[0].alt : '';

    /** newsletter popup - load popup **/
    $('#newsletter-modal').on('show.bs.modal', function () {
        eventCallback('', foodyGlobals['title'], 'טעינת פופאפ רישום לדיוור', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן')
    });

    /** closing the popup by pressing outside of it **/
    if ($('#newsletter-modal').hasClass('show')) {
        $('body').click(function (event) {
            let selector = $('#newsletter-modal,  #newsletter-modal *');
            if (!$(event.target).is(selector)) {
                eventCallback('', foodyGlobals['title'], 'סגירת פופ אפ ללא רישום', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן')
            }
        });
    }

    /** closing the popup by pressing the close button (X) **/
    $('#newsletter-modal .close').on('click', function () {
        eventCallback('', foodyGlobals['title'], 'סגירת פופ אפ ללא רישום', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן')
    });

    /** press submit on the popup **/
    $('#newsletter-modal .wpcf7-form .wpcf7-submit').on('click', function () {
        let isEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if ((typeof $('#newsletter-modal .wpcf7-form .wpcf7-email')[0] != "undefined") &&
            ($('#newsletter-modal .wpcf7-form .wpcf7-email')[0].value != '') &&
            (isEmail.test($('#newsletter-modal .wpcf7-form .wpcf7-email')[0].value))) {
            eventCallback('', foodyGlobals['title'], 'לחיצה לאישור רישום לדיוור', '', 'קריאייטיב', key_for_image, 'פופאפ - ניוזלטר מתוזמן');
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

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
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
        '',
        _object
    );
}