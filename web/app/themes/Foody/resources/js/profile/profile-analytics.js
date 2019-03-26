/**
 * Created by omerfishman on 3/26/19.
 */

jQuery(document).ready(($) => {
    let socialLinks = jQuery('.foody-content .wp-social-login-widget');
    let googleButton = socialLinks.siblings('.login').find('.btn-google');
    let facebookButton = socialLinks.siblings('.login').find('.btn-facebook');

    googleButton.click((event) => {
        eventCallback(event, 'גוגל');
    });

    facebookButton.click((event) => {
        eventCallback(event, 'פייסבוק');
    });
});


/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param type
 */
function eventCallback(event, type) {

    tagManager.pushDataLayer(
        'רישום לאתר',
        'לחיצה לתחילת רישום',
        type,
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
        '',
        '',
        ''
    );
}