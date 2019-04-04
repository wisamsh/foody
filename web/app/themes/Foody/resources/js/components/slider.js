/**
 * Created by moveosoftware on 5/15/18.
 */

let prevArrow = '<span class="foody-arrow foody-slider-prev"><img src="' + foodyGlobals.imagesUri + 'icons/icon-prev.png" ></span>';
let nextArrow = '<span class="foody-arrow foody-slider-next"><img src="' + foodyGlobals.imagesUri + 'icons/icon-prev.png" ></span>';

window.slider = function (selector, options) {
    options = options || {};
    options.rtl = true;
    options.prevArrow = prevArrow;
    options.nextArrow = nextArrow;
    options.infinite = false;
    options.lazyLoad = 'ondemand';

    let $selector = $(selector);

    if (options.slideSpacing) {

        $selector.on('init', () => {

            let $list = $('.slick-list', $selector);
            let $slides = $('.slick-slide', $selector);


            $list.css({
                'margin-right': -options.slideSpacing + 'px',
                'padding-left': `${options.slideSpacing + 'px'}`
            });


            $slides.css({
                'margin-right': options.slideSpacing + 'px'
            });
        });

    }

    $selector.not('.slick-initialized').slick(options);
};