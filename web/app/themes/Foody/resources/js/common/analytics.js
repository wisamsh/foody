/**
 * Created by moveosoftware on 10/14/18.
 */

module.exports = (function () {

    let FoodyAnalytics = function (settings) {
        this.mixpanel = import('mixpanel-browser');
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

