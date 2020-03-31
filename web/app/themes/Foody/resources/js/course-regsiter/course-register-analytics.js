jQuery(document).ready(($) => {
let analyticsCategory = 'קורסים';
    let courseName='';
    let hostName='';

    if(foodyGlobals['page_template_name'] == "foody-course-register") {
        courseName = $('.credit-card-pay').length && $('.credit-card-pay').data('item-name').length ? $('.credit-card-pay').data('item-name') : '';
        hostName = $('.course-title').length && $('.course-title').data('host').length ? $('.course-title').data('host') : '';

        /** page load **/
        eventCallback('', analyticsCategory, 'טעינת טופס הרשמה', courseName, 'מיקום', 'טופס הרשמה', hostName);


        $('.credit-card-pay').on('click', function () {
            let inputFields = $('#course-register-form input');
            let submited = true;
            inputFields.each(function () {
                if($(this).hasClass('error') || ($(this).attr('id') == 'terms' && !$(this).prop('checked') )){
                    submited = false;
                    return submited;
                }
            });

            if(submited){
                eventCallback('', analyticsCategory, 'השלמת רישום ומעבר לרכישה', courseName, 'מיקום', 'טופס הרשמה', hostName);
            }
        });
    }

    if(foodyGlobals['page_template_name'] == "foody-courses-thank-you") {
        /** page load **/
        let courseNameThankYou = $('.thank-you-text').length && $('.thank-you-text').data('course').length ? $('.thank-you-text').data('course') : '';
        let hostNameThankYou = $('.thank-you-text').length && $('.thank-you-text').data('host').length ? $('.thank-you-text').data('host') : '';
        eventCallback('', analyticsCategory, 'רכישה בוצעה בהצלחה', courseNameThankYou, 'מיקום', 'מסך סיום תהליך רכישה', hostNameThankYou);
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
function eventCallback(event, category, action, label, cdDesc, cdValue, hostName = '') {

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
        '',
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