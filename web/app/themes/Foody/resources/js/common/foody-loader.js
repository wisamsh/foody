/**
 * Created by moveosoftware on 10/8/18.
 */


let json = '';
let lottie = '';

setTimeout(() => {
    json = import('../../lottie/loader.js');
    lottie = import('lottie-web/build/player/lottie.min');
});

module.exports = (function () {


    function FoodyLoader(settings) {
        this.$container = $(settings.container);
        this.$loaderElement = $('<div class="foody-loader"></div>');
    }


    FoodyLoader.prototype.attach = function () {
        if(this.$container.is(':hidden')){
            return;
        }
        this.$container.append(this.$loaderElement);

        lottie.loadAnimation({
            container: this.$loaderElement[0], // Required
            animationData:json,
            // path: 'dist/loader.json', // Required
            renderer: 'svg', // Required
            loop: false, // Optional
            autoplay: true, // Optional
            // name: "foody-loader", // Name for future reference. Optional.
        });
    };


    FoodyLoader.prototype.detach = function () {
        if(this.$container.is(':hidden')){
            return;
        }
        lottie.destroy();
        this.$loaderElement.detach();
    };


    return FoodyLoader;

})();