jQuery(document).ready(($) => {
    let analyticsCategory = 'קורסים';
    let courseName = '';
    let hostName = '';

    // course register page
    if (foodyGlobals['page_template_name'] == "foody-course-register") {
        courseName = $('.credit-card-pay').length && $('.credit-card-pay').data('item-name').length ? $('.credit-card-pay').data('item-name') : '';
        hostName = $('.course-title').length && $('.course-title').data('host').length ? $('.course-title').data('host') : '';

        /** page load **/
        eventCallback('', analyticsCategory, 'טעינת טופס הרשמה', courseName, 'מיקום', 'טופס הרשמה', hostName);


        $('.credit-card-pay').on('click', function () {
            let inputFields = $('#course-register-form input');
            let isSubmited = true;
            inputFields.each(function () {
                if ($(this).hasClass('error') || ($(this).attr('id') == 'terms' && !$(this).prop('checked'))) {
                    isSubmited = false;
                    return isSubmited;
                }
            });

            if (isSubmited) {
                eventCallback('', analyticsCategory, 'השלמת רישום ומעבר לרכישה', courseName, 'מיקום', 'טופס הרשמה', hostName);
            }
        });

        $('.bit-pay').on('click', function () {
            let inputFields = $('#course-register-form input');
            let submited = true;
            inputFields.each(function () {
                if ($(this).hasClass('error') || ($(this).attr('id') == 'terms' && !$(this).prop('checked'))) {
                    submited = false;
                    return submited;
                }
            });

            if (submited) {
                eventCallback('', analyticsCategory, 'השלמת רישום ומעבר לרכישה (bit)', courseName, 'מיקום', 'טופס הרשמה', hostName);
            }
        });
    }

    // course register thank you page
    if (foodyGlobals['page_template_name'] == "foody-courses-thank-you") {
        if ($('#thank-you-container').length) {
            /** page load **/
            let urlParams = '';
            let textClass = $('.thank-you-text').length ? '.thank-you-text' : false;
            textClass = !textClass && $('.cancellation-text').length ? '.cancellation-text' : textClass;

            let courseNameThankYou = $(textClass).data('course').length ? $(textClass).data('course') : '';
            let hostNameThankYou = $(textClass).data('host').length ? $(textClass).data('host') : '';
            let couponName = $(textClass).data('coupon-used').toString().length ? $(textClass).data('coupon-used').toString() : '';

            if (!foodyGlobals.isMobile) {
                urlParams = getUrlVars();
            } else {
                if ($('.foody-payment-bit').length) {
                    let _status = $('.thank-you-text').length ? 'approved' : 'canceled';
                    urlParams = {payment_method: 'ביט', status: _status};
                }
            }
            if (typeof urlParams.payment_method != 'undefined' && typeof urlParams.status != 'undefined' && decodeURI(urlParams.payment_method) == 'ביט') {
                couponName = couponName.length ? couponName : 'ללא קופון';
                if (urlParams.status == 'approved') {
                    eventCallback('', analyticsCategory, 'רכישה בוצעה בהצלחה (bit)', courseNameThankYou, 'מיקום', 'מסך סיום תהליך רכישה', hostNameThankYou, couponName);
                } else {
                    eventCallback('', analyticsCategory, 'הודעת כישלון בביצוע תשלום (bit)', courseNameThankYou, 'מיקום', 'מסך סיום תהליך רכישה', hostNameThankYou, couponName);
                }
            } else {
                eventCallback('', analyticsCategory, 'רכישה בוצעה בהצלחה', courseNameThankYou, 'מיקום', 'מסך סיום תהליך רכישה', hostNameThankYou);
            }
        }
    }

});

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 * @param action
 * @param label
 * @param cdDesc
 * @param cdValue
 */
function eventCallback(event, category, action, label, cdDesc, cdValue, hostName = '', item_category = '') {

    /**
     * Logged in user ID
     */
    let customerID = foodyGlobals['loggedInUser'] ? foodyGlobals['loggedInUser'] : '';
    let object = label;

    tagManager.pushDataLayer(
        category,
        action,
        label,
        customerID,
        '',
        item_category,
        hostName,
        '',
        '',
        '',
        '',
        '',
        '',
        cdDesc,
        cdValue,
        '',
        object
    );
}