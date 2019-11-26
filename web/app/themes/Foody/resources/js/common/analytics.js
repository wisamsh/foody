/**
 * Created by moveosoftware on 10/14/18.
 */

module.exports = (function () {

    let FoodyAnalytics = function (settings) {
        this.mixpanel = require('mixpanel-browser');
        this.mixpanel.init(foodyGlobals.mixpanelToken);
    };


    FoodyAnalytics.prototype.event = function (name, properties) {

        // TODO maybe add here more analytics tools
        this.mixpanel.track(name, properties);
    };

    FoodyAnalytics.prototype.timeEvent = function (name) {

        // TODO maybe add here more analytics tools
        this.mixpanel.time_event(name);
    };

    FoodyAnalytics.prototype.view = function () {

        let event = {
            id: foodyGlobals.objectID,
            title: foodyGlobals.title,
            type: foodyGlobals.type
        };

        this.event('page_view', event);

    };


    return FoodyAnalytics;


})();

jQuery(document).ready(($) => {

    /**
     * Load pop-up
     */
    let modalPopup = $('#login-modal');
    modalPopup.on('shown.bs.modal', function (event) {
        eventCallback(event, 'רישום לאתר', 'טעינת פופאפ', 'אתר', '', '');
    });

    /**
     * Pressed register on pop-up
     */
    let registerBtnOnPopup = $('#login-modal .go-to-register');
    registerBtnOnPopup.on('click', function (event) {
        eventCallback(event, 'רישום לאתר', 'לחיצה על קישור לקוח חדש? הירשם', 'אתר', '', '');
    });

    /**
     * searching without autocomplete
     */
    $('.search-bar > .icon > img').on('click', function () {
       let searchValue = $('.foody-input')[0].value;
        set_search_order('searches_strings', searchValue);
        eventCallback('', 'רישום לאתר', 'חיפוש טקסט חופשי', searchValue, 'מספר חיפוש', get_search_order('searches_strings', searchValue));
    });

    $('.foody-input').on('keyup', function (event) {
       if(event.which == 13){
           let searchValue = $('.foody-input')[0].value;
           set_search_order('searches_strings', searchValue);
           eventCallback('', 'רישום לאתר', 'חיפוש טקסט חופשי', searchValue, 'מספר חיפוש', get_search_order('searches_strings', searchValue));
       }
    });

    /**
     * searching with autocomplete
     */
    $('.foody-dataset-1').on('click', '.foody-search-suggestions a', function () {
        let searchValue = $('.foody-input')[0].value;
        let choiceSuggestion = this.innerText;
        set_search_order('searches_strings', searchValue);
        eventCallback('', 'רישום לאתר', 'חיפוש טקסט חופשי',choiceSuggestion , 'מספר חיפוש', get_search_order('searches_strings', searchValue));
    });
});

function set_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        searches_comitted = [];
    }

    if (!searches_comitted.includes(key)) {
        searches_comitted.push(key);
    }

    sessionStorage.setItem(action, JSON.stringify(searches_comitted));
}

function get_search_order(action, key) {
    let searches_comitted = JSON.parse(sessionStorage.getItem(action));

    if (!searches_comitted) {
        return 0;
    }

    return jQuery.inArray(key, searches_comitted) +1;
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
function eventCallback(event, category, action, label, cdDesc, cdValue) {

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
        ''
    );
}
