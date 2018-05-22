/**
 * Created by moveosoftware on 5/15/18.
 */


let prevArrow = '<span class="foody-arrow foody-slider-prev"><img src="' + imagesUri + 'icons/icon-prev.png" ></span>';
let nextArrow = '<span class="foody-arrow foody-slider-next"><img src="' + imagesUri + 'icons/icon-prev.png" ></span>';

window.slider = function (selector, options) {
    options = options || {};
    options.rtl = true;
    options.prevArrow = prevArrow;
    options.nextArrow = nextArrow;
    options.infinite = false;
    $(selector).slick(options);
};