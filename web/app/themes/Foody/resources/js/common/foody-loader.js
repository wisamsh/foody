/**
 * Created by moveosoftware on 10/8/18.
 */


let json = '';

json = require('../../lottie/loader.js');


if (!window.lottie) {
    window.lottie = require('lottie-web/build/player/lottie.min');
}


module.exports = (function () {


    function FoodyLoader(settings) {
        this.$container = $(settings.container);
        if (typeof settings.id != 'undefined') {
            this.$id = '#' + settings.id;
            this.$loaderElement = $('<div id="' + settings.id + '" class="foody-loader"></div>');
        } else {
            this.$loaderElement = $('<div class="foody-loader"></div>');
        }
    }


    FoodyLoader.prototype.attach = function (setting = null) {
        if (this.$container.is(':hidden')) {
            return;
        }
        this.$container.append(this.$loaderElement);
        if (typeof this.$id != 'undefined' && setting != null && typeof setting.topPercentage != 'undefined') {
            $(this.$loaderElement).css('top', this.$container.position().top - this.$container.position().top * setting.topPercentage / 100);
        }
        window.lottie.loadAnimation({
            container: this.$loaderElement[0], // Required
            animationData: json,
            // path: 'dist/loader.json', // Required
            renderer: 'svg', // Required
            loop: false, // Optional
            autoplay: true, // Optional
            // name: "foody-loader", // Name for future reference. Optional.
        });
    };


    FoodyLoader.prototype.detach = function () {
        if (this.$container.is(':hidden')) {
            return;
        }
        lottie.destroy();
        this.$loaderElement.detach();
    };


    return FoodyLoader;

})();