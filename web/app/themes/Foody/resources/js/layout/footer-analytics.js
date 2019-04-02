/**
 * Created by moveosoftware on 10/29/18.
 */
jQuery(document).ready(($)=>{
    let signupButton = jQuery("#wpcf7-f10340-o2 > form > p > input");

    signupButton.click((event) => {
        //TODO:: check if email was enter properly before sending the event
        eventCallback(event, 'קטגוריה'); //TODO:: CREATE THIS FUNC getCategory()
    });

});


/**
 * Handle events and fire analytics dataLayer.push
 * @param event
 * @param category
 */
function eventCallback(event, category) {
    tagManager.pushDataLayer(
        '',
        'לחיצה על רישום לדיוור',
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
        'מיקום', //TODO:: CHECK THIS ??
        'פוטר',
        ''
    );
}