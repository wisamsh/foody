/**
 * Created by moveosoftware on 10/14/18.
 */

module.exports = (function () {

    let FoodyAnalytics = function (settings) {

    };


    FoodyAnalytics.prototype.event = function (name, properties) {

        // TODO maybe add here more analytics tools
        mixpanel.track(name, properties);
    };

    FoodyAnalytics.prototype.view = function () {

    };


    return FoodyAnalytics;


})();