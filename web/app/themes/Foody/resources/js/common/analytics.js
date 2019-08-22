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
